<?php
$page = "admin_forum";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }


// SET RESULT VARIABLE
$result = 0;



// CREATE CATEGORY
if($task == "addcategory") {

  $forumcat_title = $_POST['forumcat_title'];
  $max_order = $database->database_fetch_assoc($database->database_query("SELECT max(forumcat_order) AS max_order FROM se_forumcats"));
  $forumcat_order = $max_order[max_order]+1;

  $forumcat_title = SE_Language::edit(0, $forumcat_title, NULL, LANGUAGE_INDEX_CUSTOM);

  $database->database_query("INSERT INTO se_forumcats (forumcat_title, forumcat_order) VALUES ('$forumcat_title', '$forumcat_order')");






// EDIT CATEGORY
} elseif($task == "editcategory") {

  $forumcat_id = $_POST['forumcat_id'];
  $forumcat = $database->database_query("SELECT * FROM se_forumcats WHERE forumcat_id='$forumcat_id'");
  if($database->database_num_rows($forumcat) == 1) {
    $forumcat_info = $database->database_fetch_assoc($forumcat);
    $forumcat_title = $_POST['forumcat_title'];
    SE_Language::edit($forumcat_info[forumcat_title], $forumcat_title);
  }






// DELETE CATEGORY
} elseif($task == "deletecategory") {

  $forumcat_id = $_GET['forumcat_id'];

  $user_posts = $database->database_query("SELECT forumpost_authoruser_id, COUNT(forumpost_id) AS total_posts FROM se_forumposts LEFT JOIN se_forumtopics ON se_forumposts.forumpost_forumtopic_id=se_forumtopics.forumtopic_id LEFT JOIN se_forums ON se_forumtopics.forumtopic_forum_id=se_forums.forum_id WHERE se_forums.forum_forumcat_id='$forumcat_id' AND se_forumposts.forumpost_deleted=0 GROUP BY se_forumposts.forumpost_authoruser_id") or die(mysql_error());
  while($user_post_info = $database->database_fetch_assoc($user_posts)) {
    $database->database_query("UPDATE se_forumusers SET forumuser_totalposts=forumuser_totalposts-{$user_post_info[total_posts]} WHERE forumuser_user_id='{$user_post_info[forumpost_authoruser_id]}'") or die(mysql_error());
  }
  $topics = $database->database_query("SELECT forumtopic_id FROM se_forumtopics LEFT JOIN se_forums ON se_forumtopics.forumtopic_forum_id=se_forums.forum_id WHERE se_forums.forum_forumcat_id='$forumcat_id'") or die(mysql_error());
  while($topic_info = $database->database_fetch_assoc($topics)) {
    $dir = "../uploads_forum/{$topic_info[forumtopic_id]}/";
    if(is_dir($dir)) {
      if($dh = @opendir($dir)) {
        while(($file = @readdir($dh)) !== false) {
          if($file != "." & $file != "..") {
            @unlink($dir.$file);
          }
        }
        @closedir($dh);
      }
      @rmdir($dir);
    }
  }

  $database->database_query("DELETE FROM se_forumlevels USING se_forums LEFT JOIN se_forumlevels ON se_forums.forum_id=se_forumlevels.forumlevel_forum_id WHERE se_forums.forum_forumcat_id='$forumcat_id'");
  $database->database_query("DELETE FROM se_forummoderators USING se_forums LEFT JOIN se_forummoderators ON se_forums.forum_id=se_forummoderators.forummoderator_forum_id WHERE se_forums.forum_forumcat_id='$forumcat_id'");
  $database->database_query("DELETE FROM se_forumtopics, se_forumposts, se_forummedia USING se_forums LEFT JOIN se_forumtopics ON se_forums.forum_id=se_forumtopics.forumtopic_forum_id LEFT JOIN se_forumposts ON se_forumtopics.forumtopic_id=se_forumposts.forumpost_forumtopic_id LEFT JOIN se_forummedia ON se_forumtopics.forumtopic_id=se_forummedia.forummedia_forumtopic_id WHERE se_forums.forum_forumcat_id='$forumcat_id'");
  $database->database_query("DELETE FROM se_languagevars, se_forums USING se_forums JOIN se_languagevars WHERE forum_forumcat_id='$forumcat_id' AND (forum_title=languagevar_id OR forum_desc=languagevar_id)");
  $database->database_query("DELETE FROM se_languagevars, se_forumcats USING se_forumcats LEFT JOIN se_languagevars ON se_forumcats.forumcat_title=se_languagevars.languagevar_id WHERE forumcat_id='$forumcat_id'");





// MOVE CATEGORY
} elseif($task == "movecategory") {

  $forumcat_id = $_GET['forumcat_id'];
  $forumcat_info = $database->database_fetch_assoc($database->database_query("SELECT forumcat_id, forumcat_order FROM se_forumcats WHERE forumcat_id='$forumcat_id'"));
  $prev_forumcat = $database->database_query("SELECT forumcat_id, forumcat_order FROM se_forumcats WHERE forumcat_order<'$forumcat_info[forumcat_order]' ORDER BY forumcat_order DESC LIMIT 1");
  if($database->database_num_rows($prev_forumcat) == 1) {
    $prev_forumcat_info = $database->database_fetch_assoc($prev_forumcat);
    $database->database_query("UPDATE se_forumcats SET forumcat_order='$forumcat_info[forumcat_order]' WHERE forumcat_id='$prev_forumcat_info[forumcat_id]'");
    $database->database_query("UPDATE se_forumcats SET forumcat_order='$prev_forumcat_info[forumcat_order]' WHERE forumcat_id='$forumcat_info[forumcat_id]'");
  }




// CREATE FORUM
} elseif($task == "addforum") {

  $forumcat_id = $_POST['forumcat_id'];
  $forum_title = $_POST['forum_title'];
  $forum_desc = $_POST['forum_desc'];

  $max_order = $database->database_fetch_assoc($database->database_query("SELECT max(forum_order) AS max_order FROM se_forums"));
  $forum_order = $max_order[max_order]+1;

  $view_levels = $_POST['view_levels'];
  $view_levels   = ( !empty($view_levels)  ? ( is_string($view_levels)   ? explode(",", $view_levels)   : (array) $view_levels  ) : NULL );

  $post_levels = $_POST['post_levels'];
  $post_levels   = ( !empty($post_levels)  ? ( is_string($post_levels)   ? explode(",", $post_levels)   : (array) $post_levels  ) : NULL );

  $forum_title = SE_Language::edit(0, $forum_title, NULL, LANGUAGE_INDEX_CUSTOM);
  $forum_desc = SE_Language::edit(0, $forum_desc, NULL, LANGUAGE_INDEX_CUSTOM);

  $database->database_query("INSERT INTO se_forums (forum_forumcat_id, forum_order, forum_title, forum_desc) VALUES ('$forumcat_id', '$forum_order', '$forum_title', '$forum_desc')");
  $forum_id = $database->database_insert_id();

  if($view_levels != NULL) {
    for($i=0;$i<count($view_levels);$i++) {
      if(in_array($view_levels[$i], $post_levels)) { $canpost = 1; } else { $canpost = 0; }
      $database->database_query("INSERT INTO se_forumlevels (forumlevel_forum_id, forumlevel_level_id, forumlevel_post) VALUES ('$forum_id', '$view_levels[$i]', '$canpost')");
    }
  }

  // DELETE UNNECESSARY DATA IN FORUMLEVELS TABLE
  $database->database_query("DELETE FROM se_forumlevels USING se_forumlevels LEFT JOIN se_levels ON se_forumlevels.forumlevel_level_id=se_levels.level_id WHERE se_levels.level_id IS NULL AND se_forumlevels.forumlevel_level_id<>0");




// EDIT FORUM
} elseif($task == "editforum") {

  $forum_id = $_POST['forum_id'];
  $forumcat_id = $_POST['forumcat_id'];
  $forum_title = $_POST['forum_title'];
  $forum_desc = $_POST['forum_desc'];

  $view_levels = $_POST['view_levels'];
  $view_levels   = ( !empty($view_levels)  ? ( is_string($view_levels)   ? explode(",", $view_levels)   : (array) $view_levels  ) : NULL );

  $post_levels = $_POST['post_levels'];
  $post_levels   = ( !empty($post_levels)  ? ( is_string($post_levels)   ? explode(",", $post_levels)   : (array) $post_levels  ) : NULL );

  $forum = $database->database_query("SELECT * FROM se_forums WHERE forum_id='$forum_id'");
  if($database->database_num_rows($forum) == 1) {
    $forum_info = $database->database_fetch_assoc($forum);

    SE_Language::edit($forum_info[forum_title], $forum_title);
    SE_Language::edit($forum_info[forum_desc], $forum_desc);

    $database->database_query("UPDATE se_forums SET forum_forumcat_id='$forumcat_id' WHERE forum_id='$forum_id'");

    $database->database_query("DELETE FROM se_forumlevels WHERE forumlevel_forum_id='$forum_id'");
    if($view_levels != NULL) {
      for($i=0;$i<count($view_levels);$i++) {
        if(in_array($view_levels[$i], $post_levels)) { $canpost = 1; } else { $canpost = 0; }
        $database->database_query("INSERT INTO se_forumlevels (forumlevel_forum_id, forumlevel_level_id, forumlevel_post) VALUES ('$forum_id', '$view_levels[$i]', '$canpost')");
      }
    }
  }

  // DELETE UNNECESSARY DATA IN FORUMLEVELS TABLE
  $database->database_query("DELETE FROM se_forumlevels USING se_forumlevels LEFT JOIN se_levels ON se_forumlevels.forumlevel_level_id=se_levels.level_id WHERE se_levels.level_id IS NULL AND se_forumlevels.forumlevel_level_id<>0");





// DELETE FORUM
} elseif($task == "deleteforum") {

  $forum_id = $_GET['forum_id'];

  $user_posts = $database->database_query("SELECT forumpost_authoruser_id, COUNT(forumpost_id) AS total_posts FROM se_forumposts LEFT JOIN se_forumtopics ON se_forumposts.forumpost_forumtopic_id=se_forumtopics.forumtopic_id WHERE se_forumtopics.forumtopic_forum_id='$forum_id' AND se_forumposts.forumpost_deleted=0 GROUP BY se_forumposts.forumpost_authoruser_id") or die(mysql_error());
  while($user_post_info = $database->database_fetch_assoc($user_posts)) {
    $database->database_query("UPDATE se_forumusers SET forumuser_totalposts=forumuser_totalposts-{$user_post_info[total_posts]} WHERE forumuser_user_id='{$user_post_info[forumpost_authoruser_id]}'") or die(mysql_error());
  }
  $topics = $database->database_query("SELECT forumtopic_id FROM se_forumtopics WHERE se_forumtopics.forumtopic_forum_id='$forum_id'") or die(mysql_error());
  while($topic_info = $database->database_fetch_assoc($topics)) {
    $dir = "../uploads_forum/{$topic_info[forumtopic_id]}/";
    if(is_dir($dir)) {
      if($dh = @opendir($dir)) {
        while(($file = @readdir($dh)) !== false) {
          if($file != "." & $file != "..") {
            @unlink($dir.$file);
          }
        }
        @closedir($dh);
      }
      @rmdir($dir);
    }
  }

  $database->database_query("DELETE FROM se_forumlevels WHERE forumlevel_forum_id='$forum_id'");
  $database->database_query("DELETE FROM se_forummoderators WHERE forummoderator_forum_id='$forum_id'");
  $database->database_query("DELETE FROM se_forumtopics, se_forumposts, se_forummedia USING se_forumtopics LEFT JOIN se_forumposts ON se_forumtopics.forumtopic_id=se_forumposts.forumpost_forumtopic_id LEFT JOIN se_forummedia ON se_forumtopics.forumtopic_id=se_forummedia.forummedia_forumtopic_id WHERE se_forumtopics.forumtopic_forum_id='$forum_id'");
  $database->database_query("DELETE FROM se_languagevars, se_forums USING se_forums JOIN se_languagevars WHERE forum_id='$forum_id' AND (forum_title=languagevar_id OR forum_desc=languagevar_id)"); 





// MOVE FORUM
} elseif($task == "moveforum") {

  $forum_id = $_GET['forum_id'];
  $forum_info = $database->database_fetch_assoc($database->database_query("SELECT forum_id, forum_order, forum_forumcat_id FROM se_forums WHERE forum_id='$forum_id'"));
  $prev_forum = $database->database_query("SELECT forum_id, forum_order FROM se_forums WHERE forum_order<'$forum_info[forum_order]' AND forum_forumcat_id='$forum_info[forum_forumcat_id]' ORDER BY forum_order DESC LIMIT 1");
  if($database->database_num_rows($prev_forum) == 1) {
    $prev_forum_info = $database->database_fetch_assoc($prev_forum);
    $database->database_query("UPDATE se_forums SET forum_order='$forum_info[forum_order]' WHERE forum_id='$prev_forum_info[forum_id]'");
    $database->database_query("UPDATE se_forums SET forum_order='$prev_forum_info[forum_order]' WHERE forum_id='$forum_info[forum_id]'");
  }





// SEARCH USERS FOR MODERATORS
} elseif($task == "searchusers") {

  $mod_user = $_GET['mod_user'];
  if(trim($mod_user) == "") { exit(); }
  $page = (isset($_GET['page']))?(int)$_GET['page']:1;
  $start = ($page-1)*10;
	
  // RETRIEVE FITTING USERS
  $results = array();
  $sql = "SELECT user_id, user_username, user_fname, user_lname FROM se_users WHERE user_username LIKE '%$mod_user%' OR user_fname LIKE '%$mod_user%' OR user_lname LIKE '%$mod_user%'";
  $total_users = $database->database_num_rows($database->database_query($sql));

  $sql .= " LIMIT $start, 10";
  $resource = $database->database_query($sql);
  while( $user_info = $database->database_fetch_assoc($resource) )
  {
    $sugg_user = new se_user();
    $sugg_user->user_info['user_id'] = $user_info['user_id'];
    $sugg_user->user_info['user_username'] = $user_info['user_username'];
    $sugg_user->user_info['user_fname'] = $user_info['user_fname'];
    $sugg_user->user_info['user_lname'] = $user_info['user_lname'];
    $sugg_user->user_displayname();
    
    $results[] = array(
      "id"          => $user_info['user_id'],
      "username"       => $user_info['user_username'],
      "display_name"        => $sugg_user->user_displayname
    );
  }
	
  // OUTPUT JSON
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
  header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
  header("Pragma: no-cache"); // HTTP/1.0
  header("Content-Type: application/json");
  echo json_encode(array('total_results' => &$total_users, 'page' => &$page, 'results' => &$results));
  exit();


// MANAGE MODERATORS
} elseif($task == "moderators") {

  $forummoderator_forum_id = $_POST['forummoderator_forum_id'];
  $mods = $_POST['mods'];
  $mods_keep = (is_array($_POST['mods_keep']))?$_POST['mods_keep']:Array();

  for($i=0;$i<count($mods);$i++) {
    if(!array_key_exists($mods[$i], $mods_keep)) {
      $database->database_query("DELETE FROM se_forummoderators WHERE forummoderator_forum_id='$forummoderator_forum_id' AND forummoderator_user_id='$mods[$i]'");
    } elseif($database->database_num_rows($database->database_query("SELECT FROM se_forummoderators WHERE forummoderator_forum_id='$forummoderator_forum_id' AND forummoderator_user_id='$mods[$i]'")) == 0) {
      $database->database_query("INSERT INTO se_forummoderators (forummoderator_forum_id, forummoderator_user_id) VALUES ('$forummoderator_forum_id', '$mods[$i]')");
    }
  }

}




// GET FORUM CATEGORIES
$forumcats = $database->database_query("SELECT * FROM se_forumcats ORDER BY forumcat_order");
while($forumcat_info = $database->database_fetch_assoc($forumcats)) {

  // GET FORUMS
  $forum_array = Array();
  $forums = $database->database_query("SELECT * FROM se_forums WHERE forum_forumcat_id='$forumcat_info[forumcat_id]' ORDER BY forum_order");
  while($forum_info = $database->database_fetch_assoc($forums)) {

    SE_Language::_preload_multi($forum_info[forum_title], $forum_info[forum_desc]);

    // GET MODERATORS
    $mod_array = Array();
    $mod_array_id = Array();
    $mods = $database->database_query("SELECT se_users.user_id, se_users.user_username, se_users.user_fname, se_users.user_lname FROM se_forummoderators LEFT JOIN se_users ON se_forummoderators.forummoderator_user_id=se_users.user_id WHERE se_forummoderators.forummoderator_forum_id='$forum_info[forum_id]' AND se_users.user_id IS NOT NULL");
    while($user_info = $database->database_fetch_assoc($mods)) {

      $user = new se_user();
      $user->user_info[user_id] = $user_info[user_id];
      $user->user_info[user_username] = $user_info[user_username];
      $user->user_info[user_fname] = $user_info[user_fname];
      $user->user_info[user_lname] = $user_info[user_lname];
      $user->user_displayname();
      $user_info[user_displayname] = $user->user_displayname;

      $mod_array[] = $user_info;
      $mod_array_id[] = $user_info[user_id];
    }
    $forum_info[forum_mods] = $mod_array;
    $forum_info[forum_mods_js] = json_encode($mod_array);
    $forum_info[forum_mods_id_js] = json_encode($mod_array_id);

    // GET LEVELS
    $view_levels = Array();
    $post_levels = Array();
    $forumlevels = $database->database_query("SELECT * FROM se_forumlevels WHERE forumlevel_forum_id='$forum_info[forum_id]'");
    while($forumlevel_info = $database->database_fetch_assoc($forumlevels)) {
      if($forumlevel_info[forumlevel_post]) {
        $post_levels[] = $forumlevel_info[forumlevel_level_id];
      }
      $view_levels[] = $forumlevel_info[forumlevel_level_id];
    }
    $forum_info[forum_level_view] = json_encode($view_levels);
    $forum_info[forum_level_post] = json_encode($post_levels);
    
    $forum_array[] = $forum_info;
  }

  SE_Language::_preload($forumcat_info[forumcat_title]);
  $forumcat_info[forums] = $forum_array;
  $forumcat_array[] = $forumcat_info;
}



// GET USER LEVELS
$levels = $database->database_query("SELECT level_id, level_name, level_default FROM se_levels");
$level_array = Array();
while($level_info = $database->database_fetch_assoc($levels)) {
  $level_array[] = $level_info;
}


// ASSIGN VARIABLES AND SHOW FORUM SETUP PAGE
$smarty->assign('is_error', $is_error);
$smarty->assign('forumcats', $forumcat_array);
$smarty->assign("levels", $level_array);
include "admin_footer.php";
?>