<?php
$page = "forum_topic";
include "header.php";

if(isset($_GET['task'])) { $task = $_GET['task']; } elseif(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = "main"; }
if(isset($_GET['p'])) { $p = (int) $_GET['p']; } elseif(isset($_POST['p'])) { $p = (int) $_POST['p']; } else { $p = 1; }
if(isset($_GET['forum_id'])) { $forum_id = (int) $_GET['forum_id']; } elseif(isset($_POST['forum_id'])) { $forum_id = (int) $_POST['forum_id']; } else { $forum_id = 0; }
if(isset($_GET['topic_id'])) { $topic_id = (int) $_GET['topic_id']; } elseif(isset($_POST['topic_id'])) { $topic_id = (int) $_POST['topic_id']; } else { $topic_id = 0; }
if(isset($_GET['post_id'])) { $post_id = (int) $_GET['post_id']; } elseif(isset($_POST['post_id'])) { $post_id = (int) $_POST['post_id']; } else { $post_id = 0; }

// IF FORUMS ARE TURNED OFF, FORWARD TO HOME PAGE
if($setting[setting_forum_status] == 0) { header("Location: home.php"); exit(); }

// IF FORUMS ARE IN MAINTENANCE MOD, FORWARD TO MAIN FORUM
if($setting[setting_forum_status] == 2 && (!$user->user_exists || ($user->user_exists && !$forum->forum_is_moderator($user->user_info[user_id])))) { header("Location: forum.php"); exit(); }

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if((!$user->user_exists && !$setting['setting_permission_forum'])) {
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

// VALIDATE FORUM ID AND GET INFO
$forum_query = $database->database_query("SELECT * FROM se_forums WHERE forum_id='$forum_id'");
if($database->database_num_rows($forum_query) != 1) {
  header("Location: forum.php");
  exit();
}
$forum_info = $database->database_fetch_assoc($forum_query);


// VALIDATE TOPIC ID AND GET INFO
$topic_query = $database->database_query("SELECT * FROM se_forumtopics WHERE forumtopic_forum_id='{$forum_info[forum_id]}' AND forumtopic_id='$topic_id'");
if($database->database_num_rows($topic_query) != 1) {
  header("Location: forum_view.php?forum_id={$forum_info[forum_id]}");
  exit();
}
$topic_info = $database->database_fetch_assoc($topic_query);


// DETERMINE THE USER'S PERMISSIONS FOR THIS FORUM (VIEW, POST, MODERATE, ETC)
$forum_permission = $forum->forum_permission($forum_info[forum_id]);

// SEND USER BACK IF NOT ALLOWED TO VIEW THIS FORUM
if(!$forum_permission[allowed_to_view]) { header("Location: forum.php"); exit(); }

// IF LOGGED IN, SET COOKIE TO SIGNAL FORUM IS "READ"
if($user->user_exists) { setcookie("forum_{$user->user_info[user_id]}_{$forum_info[forum_id]}", time(), time()+99999999, "/"); }



// CLOSE TOPIC
if($task == "close" && $forum_permission[allowed_to_close]) {
  $database->database_query("UPDATE se_forumtopics SET forumtopic_closed=1 WHERE forumtopic_forum_id='{$forum_info[forum_id]}' AND forumtopic_id='{$topic_info[forumtopic_id]}'");
  $topic_info[forumtopic_closed] = 1;



// OPEN TOPIC
} elseif($task == "open" && $forum_permission[allowed_to_close]) {
  $database->database_query("UPDATE se_forumtopics SET forumtopic_closed=0 WHERE forumtopic_forum_id='{$forum_info[forum_id]}' AND forumtopic_id='{$topic_info[forumtopic_id]}'");
  $topic_info[forumtopic_closed] = 0;



// STICK TOPIC
} elseif($task == "stick" && $forum_permission[allowed_to_stick]) {
  $database->database_query("UPDATE se_forumtopics SET forumtopic_sticky=1 WHERE forumtopic_forum_id='{$forum_info[forum_id]}' AND forumtopic_id='{$topic_info[forumtopic_id]}'");
  $topic_info[forumtopic_sticky] = 1;



// UN-STICK TOPIC
} elseif($task == "unstick" && $forum_permission[allowed_to_stick]) {
  $database->database_query("UPDATE se_forumtopics SET forumtopic_sticky=0 WHERE forumtopic_forum_id='{$forum_info[forum_id]}' AND forumtopic_id='{$topic_info[forumtopic_id]}'");
  $topic_info[forumtopic_sticky] = 0;



// MOVE TOPIC
} elseif($task == "move" && $forum_permission[allowed_to_move]) {
  $new_forum_id = $_POST['new_forum_id'];

  // VALIDATE FORUM ID
  if($database->database_num_rows($database->database_query("SELECT NULL FROM se_forums WHERE forum_id='{$new_forum_id}'")) == 1) {

    // ENSURE MODERATOR CAN SEE THIS FORUM
    $new_forum_permission = $forum->forum_permission($new_forum_id);
    if($new_forum_permission[allowed_to_view]) {

      // DECREMENT TOTAL TOPICS AND REPLIES IN OLD FORUM
      $database->database_query("UPDATE se_forums SET forum_totaltopics=forum_totaltopics-1, forum_totalreplies=forum_totalreplies-{$topic_info[forumtopic_totalreplies]} WHERE forum_id='{$forum_info[forum_id]}'");

      // INCREMENT TOTAL TOPICS AND REPLIES IN NEW FORUM
      $database->database_query("UPDATE se_forums SET forum_totaltopics=forum_totaltopics+1, forum_totalreplies=forum_totalreplies+{$topic_info[forumtopic_totalreplies]} WHERE forum_id='{$new_forum_id}'");

      // MOVE TOPIC
      $database->database_query("UPDATE se_forumtopics SET forumtopic_forum_id='{$new_forum_id}' WHERE forumtopic_forum_id='{$forum_info[forum_id]}' AND forumtopic_id='{$topic_info[forumtopic_id]}'");
      header("Location: forum_topic.php?forum_id={$new_forum_id}&topic_id={$topic_info[forumtopic_id]}&p={$p}");
      exit();

    }

  }

// DELETE TOPIC
} elseif($task == "delete" && $forum_permission[allowed_to_deleteall]) {

  $user_posts = $database->database_query("SELECT forumpost_authoruser_id, COUNT(forumpost_id) AS total_posts FROM se_forumposts WHERE forumpost_forumtopic_id='{$topic_info[forumtopic_id]}' AND forumpost_deleted=0 GROUP BY forumpost_authoruser_id");
  while($user_post_info = $database->database_fetch_assoc($user_posts)) {
    $database->database_query("UPDATE se_forumusers SET forumuser_totalposts=forumuser_totalposts-{$user_post_info[total_posts]} WHERE forumuser_user_id='{$user_post_info[forumpost_authoruser_id]}'");
  }
  $database->database_query("UPDATE se_forums SET forum_totaltopics=forum_totaltopics-1, forum_totalreplies=forum_totalreplies-{$topic_info[forumtopic_totalreplies]} WHERE forum_id='{$forum_info[forum_id]}'");
  $database->database_query("DELETE FROM se_forumposts, se_forummedia USING se_forumtopics LEFT JOIN se_forumposts ON se_forumtopics.forumtopic_id=se_forumposts.forumpost_forumtopic_id LEFT JOIN se_forummedia ON se_forumtopics.forumtopic_id=se_forummedia.forummedia_forumtopic_id WHERE se_forumtopics.forumtopic_forum_id='{$forum_info[forum_id]}' AND se_forumtopics.forumtopic_id='{$topic_info[forumtopic_id]}'");
  $database->database_query("DELETE FROM se_forumtopics WHERE forumtopic_forum_id='{$forum_info[forum_id]}' AND forumtopic_id='{$topic_info[forumtopic_id]}'");
  $dir = "./uploads_forum/{$topic_info[forumtopic_id]}/";
  if($dh = @opendir($dir)) {
    while(($file = @readdir($dh)) !== false) {
      if($file != "." & $file != "..") {
        @unlink($dir.$file);
      }
    }
    @closedir($dh);
  }
  @rmdir($dir);
  header("Location: forum_view.php?forum_id={$forum_info[forum_id]}");
  exit();


// DELETE POST
} elseif($task == "deletepost") {

  // VALIDATE POST ID
  $post = $database->database_query("SELECT forumpost_id, forumpost_authoruser_id, forumpost_forummedia_id FROM se_forumposts WHERE forumpost_id='{$post_id}' AND forumpost_forumtopic_id='{$topic_info[forumtopic_id]}' AND forumpost_deleted='0'");
  if($database->database_num_rows($post) == 1) {
    $post_info = $database->database_fetch_assoc($post);

    // ENSURE USER IS ALLOWED TO DELETE THIS POST
    if($forum_permission[allowed_to_deleteall] || ($user->user_exists && $user->user_info[user_id] == $post_info[forumpost_authoruser_id] && !$topic_info[forumtopic_closed])) {
      $database->database_query("UPDATE se_forumusers SET forumuser_totalposts=forumuser_totalposts-1 WHERE forumuser_user_id='{$post_info[forumpost_authoruser_id]}'");

      // IF THIS IS THE FIRST POST, DELETE TOPIC
      $firstpost = $database->database_fetch_assoc($database->database_query("SELECT forumpost_id FROM se_forumposts WHERE forumpost_forumtopic_id='{$topic_info[forumtopic_id]}' ORDER BY forumpost_id ASC LIMIT 1"));
      if($firstpost[forumpost_id] == $post_info[forumpost_id]) {
	$database->database_query("UPDATE se_forumtopics SET forumtopic_excerpt='' WHERE forumtopic_id='{$topic_info[forumtopic_id]}'");
      }

      if($post_info[forumpost_forummedia_id] != 0) {
        $media = $database->database_query("SELECT * FROM se_forummedia WHERE forummedia_id='{$post_info[forumpost_forummedia_id]}' AND forummedia_forumtopic_id='{$topic_info[forumtopic_id]}'");
        if($database->database_num_rows($media) == 1) {
          $media_info = $database->database_fetch_assoc($media);
          $media_path = "./uploads_forum/{$topic_info[forumtopic_id]}/{$media_info[forummedia_id]}.{$media_info[forummedia_ext]}";
          if(file_exists($media_path)) { @unlink($media_path); }
	  $database->database_query("DELETE FROM se_forummedia WHERE forummedia_id='{$media_info[forummedia_id]}' AND forummedia_forumtopic_id='{$media_info[forummedia_forumtopic_id]}'");
        }
      }
      $database->database_query("UPDATE se_forumposts SET forumpost_deleted='1', forumpost_body='', forumpost_excerpt='' WHERE forumpost_id='{$post_info[forumpost_id]}' AND forumpost_forumtopic_id='{$topic_info[forumtopic_id]}'");
    }
  }
}







// GET TOTAL POSTS
$total_posts = $topic_info[forumtopic_totalreplies]+1;

// SET POSTS PER PAGE
$posts_per_page = 20;


// IF POST ID IS SPECIFIED, GO TO THAT PAGE
if($post_id != 0) {

  $post = $database->database_query("SELECT forumpost_id FROM se_forumposts WHERE forumpost_id='{$post_id}' AND forumpost_forumtopic_id='{$topic_info[forumtopic_id]}'");
  if($database->database_num_rows($post) == 1) {
    $post_info = $database->database_fetch_assoc($post);
    $posts_before = $database->database_num_rows($database->database_query("SELECT NULL FROM se_forumposts WHERE forumpost_forumtopic_id='{$topic_info[forumtopic_id]}' AND forumpost_id<'{$post_info[forumpost_id]}'"));
    $p = ceil(($posts_before+1)/$posts_per_page);
  }
}



// MAKE POST PAGES
$page_vars = make_page($total_posts, $posts_per_page, $p);

// GET MODERATORS
$mod_array_id = Array();
$mods = $database->database_query("SELECT forummoderator_user_id FROM se_forummoderators WHERE forummoderator_forum_id='$forum_info[forum_id]'");
while($user_info = $database->database_fetch_assoc($mods)) {
  $mod_array_id[] = $user_info[forummoderator_user_id];
}

// SET POST ARRAY
$post_array = Array();
$posts = $database->database_query("SELECT se_forumposts.*, se_users.user_id, se_users.user_username, se_users.user_fname, se_users.user_lname,	se_users.user_photo, se_users.user_signupdate, se_forumusers.forumuser_totalposts FROM se_forumposts LEFT JOIN se_users ON se_forumposts.forumpost_authoruser_id=se_users.user_id LEFT JOIN se_forumusers ON se_users.user_id=se_forumusers.forumuser_user_id WHERE forumpost_forumtopic_id='{$topic_info[forumtopic_id]}' ORDER BY forumpost_id ASC LIMIT {$page_vars[0]}, $posts_per_page");
while($post_info = $database->database_fetch_assoc($posts)) {

  // GET POST AUTHOR
  $author = new se_user();
  if($post_info['forumpost_authoruser_id'] != $post_info['user_id']) {
    $author->user_exists = false;
  } else {
    $author->user_exists = true;
    $author->user_info['user_id'] = $post_info['user_id'];
    $author->user_info['user_username'] = $post_info['user_username'];
    $author->user_info['user_fname'] = $post_info['user_fname'];
    $author->user_info['user_lname'] = $post_info['user_lname'];
    $author->user_info['user_photo'] = $post_info['user_photo'];
    $author->user_info['user_signupdate'] = $post_info['user_signupdate'];
    $author->totalposts = $post_info['forumuser_totalposts'];
    if(in_array($post_info['user_id'], $mod_array_id)) { $author->is_moderator = true; } else { $author->is_moderator = false; }
    $author->user_displayname();
  }
  $post_info['author'] = $author;


  // GET POST MEDIA
  if($post_info[forumpost_forummedia_id] != 0) {
    $postmedia_query = $database->database_query("SELECT * FROM se_forummedia WHERE forummedia_id='{$post_info[forumpost_forummedia_id]}'");
    if($database->database_num_rows($postmedia_query) == 1) {
      $postmedia_info = $database->database_fetch_assoc($postmedia_query);
      $post_info[forummedia_id] = $postmedia_info[forummedia_id];
      $post_info[forummedia_path] = "./uploads_forum/{$topic_info[forumtopic_id]}/{$postmedia_info[forummedia_id]}.{$postmedia_info[forummedia_ext]}";
    } else {
      $post_info[forumpost_forummedia_id] = 0;
    }
  }

  // DECODE POST BODY FOR DISPLAY
  $post_info[forumpost_body] = htmlspecialchars_decode($post_info[forumpost_body]);
  $post_info[forumpost_body] = $forum->forum_bbcode_parse_view($post_info[forumpost_body]);

  $post_array[] = $post_info;
}


// IF MODERATOR IS ALLOWED TO MOVE TOPIC, GET FORUM LOOP
if($forum_permission[allowed_to_move]) {
  $forumcat_array = $forum->forum_list();
}


// UPDATE READ DATE ON THIS TOPIC IF USER IS LOGGED IN
if($user->user_exists) { $database->database_query("INSERT INTO se_forumlogs (forumlog_forumtopic_id, forumlog_user_id, forumlog_date) VALUES ('{$topic_info[forumtopic_id]}', '{$user->user_info[user_id]}', '".time()."') ON DUPLICATE KEY UPDATE forumlog_date='".time()."'"); }

// DELETE REPLY NOTIFICATIONS IF LOGGED IN
if($user->user_exists) {
  $database->database_query("DELETE FROM se_notifys USING se_notifys LEFT JOIN se_notifytypes ON se_notifys.notify_notifytype_id=se_notifytypes.notifytype_id WHERE se_notifys.notify_user_id='{$user->user_info[user_id]}' AND se_notifytypes.notifytype_name='forumreply' AND notify_object_id='{$topic_info[forumtopic_id]}'");
}

// INCREMENT TOPIC VIEW COUNT
$database->database_query("UPDATE se_forumtopics SET forumtopic_views=forumtopic_views+1 WHERE forumtopic_id='{$topic_info[forumtopic_id]}' AND forumtopic_forum_id='{$forum_info[forum_id]}'");


// ASSIGN SMARTY VARS AND INCLUDE FOOTER
$smarty->assign('forum_info', $forum_info);
$smarty->assign('topic_info', $topic_info);
$smarty->assign('post_id', $post_id);
$smarty->assign('posts', $post_array);
$smarty->assign('forum_permission', $forum_permission);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('forumcats', $forumcat_array);
include "footer.php";
?>