<?
$page = "user_article_edit";
include "header.php";
if($user->level_info[level_article_allow] == 0) { header("Location: user_home.php"); exit(); }
if(isset($_POST['article_id'])) { $article_id = $_POST['article_id']; } elseif(isset($_GET['article_id'])) { $article_id = $_GET['article_id']; } else { $article_id = 0; }
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['justadded'])) { $justadded = $_POST['justadded']; } elseif(isset($_GET['justadded'])) { $justadded = $_GET['justadded']; } else { $justadded = ""; }


// INITIALIZE ARTICLE OBJECT
$article = new rc_article($user->user_info[user_id], $article_id);
$rc_tag = new rc_articletag();

if($article->article_exists == 0) { header("Location: user_article.php"); exit(); }
if($article->article_info[article_user_id] != $user->user_info[user_id]) { header("Location: user_article.php"); exit(); }


// SET ERROR VARIABLES
$is_error = 0;
$result = 0;
$error_message = "";

// GET PRIVACY SETTINGS
$level_article_privacy = unserialize($user->level_info[level_article_privacy]);
rsort($level_article_privacy);
$level_article_comments = unserialize($user->level_info[level_article_comments]);
rsort($level_article_comments);

// CHECK FOR ADMIN ALLOWANCE OF PHOTO
if($article->articleowner_level_info[level_article_photo] == 0 & ($task == "remove" | $task == "upload")) { $task = "main"; }


// DELETE PHOTO
if($task == "remove") { $article->article_photo_delete(); $article->article_lastupdate(); }

// UPLOAD PHOTO
if($task == "upload") {
  $article->article_photo_upload("photo");
  $is_error = $article->is_error;
  $error_message = $article->error_message;
  if($is_error == 0) { $article->article_lastupdate(); }
}



if($task == "dosave") {
  // GET BASIC ARTICLE INFO
  $article_title = censor($_POST['article_title']);
  $article_body = censor($_POST['article_body']);

  $articlecat_id = $_POST['articlecat_id'];
  $subarticlecat_id = $_POST['subarticlecat_id'];
  $article_draft = $article->article_info[article_draft];
  $article_date_start = $article->article_info[article_date_start];
  $article_search = $_POST['article_search'];
  $article_privacy = $_POST['article_privacy'];
  $article_comments = $_POST['article_comments'];
  $article_tags = rc_toolkit::get_request('article_tags');

  // CHECK TO MAKE SURE TITLE HAS BEEN ENTERED
  if(str_replace(" ", "", $article_title) == "") {
    $is_error = 1;
    $error_message = 11151408;
  }

  // MAKE SURE SUBMITTED PRIVACY OPTIONS ARE ALLOWED, IF NOT, SET TO EVERYONE
  if(!in_array($article_privacy, $level_article_privacy)) { $article_privacy = $level_article_privacy[0]; }
  if(!in_array($article_comments, $level_article_comments)) { $article_comments = $level_article_comments[0]; }

  // CHECK THAT SEARCH IS NOT BLANK
  if($article->articleowner_level_info[level_article_search] == 0) { $article_search = 1; }

  //$article_approved = ($user->level_info[level_article_approved] == 0) ? 1 : 0;

  // IF NO ERROR, SAVE ARTICLE
  if($is_error == 0) {
    if($subarticlecat_id != 0) { $new_articlecat_id = $subarticlecat_id; } else { $new_articlecat_id = $articlecat_id; }

    if (isset($_POST['publish'])) {
      $article_draft = 0;
      $article_date_start = time();

      if ($article->article_info[article_approved] > 0) {
	      $article_title_short = $article_title;
	      if(strlen($article_title_short) > 100) { $article_title_short = substr($article_title_short, 0, 97); $article_title_short .= "..."; }
	      $actions->actions_add($user, "newarticle", Array('[username]', '[id]', '[title]', '[publish_date]'), Array($user->user_info[user_username], $article_id, $article_title_short, date("F j, Y, g:i a",$article_date_start)));
      }
      
    }    
    
    // UPDATE ARTICLE
    $article_slug = $article->generate_slug($article_title);
    
    $database->database_query("UPDATE se_articles SET article_slug='$article_slug', article_title='$article_title', article_articlecat_id='$new_articlecat_id', article_body='$article_body', article_date_start='$article_date_start', article_draft='$article_draft', article_search='$article_search', article_privacy='$article_privacy', article_comments='$article_comments'  WHERE article_id='".$article->article_info[article_id]."'");
    $database->database_query("UPDATE se_articlealbums SET articlealbum_privacy='$article_privacy', articlealbum_comments='$article_comments', articlealbum_search='$article_search' WHERE articlealbum_article_id='".$article->article_info[article_id]."'");
    $rc_tag->update_object_tags($article_id, $article_tags);
    
    // RESET RESULTS
    $article->article_info = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_articles WHERE article_id='".$article->article_info[article_id]."'"));


    
    // SET RESULT MESSAGE
    $result = 1;

    $article->article_lastupdate();
  }




}
else {
  $article_title = $article->article_info[article_title];
 
  $article_body = $article->article_info[article_body];
  $article_date_start = $article->article_info[article_date_start];
  $article_tags = join(', ',$rc_tag->get_object_tags($article_id));
  $article_inviteonly = $article->article_info[article_inviteonly];
  $article_search = $article->article_info[article_search];
  $article_privacy = $article->article_info[article_privacy];
  $article_comments = $article->article_info[article_comments];
  $article_draft = $article->article_info[article_draft] ? 1 : 0;
  if($article->article_info[article_articlecat_id] == 0) {
    $articlecat_id = 0;
    $subarticlecat_id = 0;
  } else {
    $articlecat = $database->database_fetch_assoc($database->database_query("SELECT articlecat_id, articlecat_dependency FROM se_articlecats WHERE articlecat_id='".$article->article_info[article_articlecat_id]."'"));
    if($articlecat[articlecat_dependency] == 0) {
      $articlecat_id = $articlecat[articlecat_id];
      $subarticlecat_id = "0";
    } else {
      $parentarticlecat = $database->database_fetch_assoc($database->database_query("SELECT articlecat_id FROM se_articlecats WHERE articlecat_id='$articlecat[articlecat_dependency]'"));
      $articlecat_id = $parentarticlecat[articlecat_id];
      $subarticlecat_id = $articlecat[articlecat_id];
    }
  }
}





// GET ARTICLE CATEGORIES
$categories_array = Array();
$categories_query = $database->database_query("SELECT * FROM se_articlecats WHERE articlecat_dependency='0' ORDER BY articlecat_id");
while($category = $database->database_fetch_assoc($categories_query)) {
  // GET DEPENDENT ARTICLE CATS
  $dep_categories_query = $database->database_query("SELECT * FROM se_articlecats WHERE articlecat_dependency='$category[articlecat_id]' ORDER BY articlecat_id");
  $dep_articlecat_array = Array();
  while($dep_category = $database->database_fetch_assoc($dep_categories_query)) {
    $dep_articlecat_array[] = Array('subarticlecat_id' => $dep_category[articlecat_id],
					'subarticlecat_title' => $dep_category[articlecat_title]);
  }

  $categories_array[] = Array('articlecat_id' => $category[articlecat_id],
			       'articlecat_title' => $category[articlecat_title],
			       'subcats' => $dep_articlecat_array);
}






// GET PREVIOUS PRIVACY SETTINGS
for($c=0;$c<count($level_article_privacy);$c++) {
  if(user_privacy_levels($level_article_privacy[$c]) != "") {
    SE_Language::_preload(user_privacy_levels($level_article_privacy[$c]));
    $privacy_options[$level_article_privacy[$c]] = user_privacy_levels($level_article_privacy[$c]);
  }
}

for($c=0;$c<count($level_article_comments);$c++) {
  if(user_privacy_levels($level_article_comments[$c]) != "") {
    SE_Language::_preload(user_privacy_levels($level_article_comments[$c]));
    $comment_options[$level_article_comments[$c]] = user_privacy_levels($level_article_comments[$c]);
  }
}

//rc_toolkit::debug($article);


// ASSIGN VARIABLES AND SHOW USER EDIT ARTICLE PAGE
$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);
$smarty->assign('justadded', $justadded);
$smarty->assign('error_message', $error_message);
$smarty->assign('article', $article);
$smarty->assign('privacy_options', $privacy_options);
$smarty->assign('comment_options', $comment_options);
$smarty->assign('cats', $categories_array);
$smarty->assign('article_title', $article_title);
$smarty->assign('article_body', str_replace("\r\n", "", html_entity_decode($article_body)));
$smarty->assign('article_date_start', $article_date_start);
$smarty->assign('article_draft', $article_draft);
$smarty->assign('articlecat_id', $articlecat_id);
$smarty->assign('subarticlecat_id', $subarticlecat_id);
$smarty->assign('article_tags', $article_tags);
$smarty->assign('article_search', $article_search);
$smarty->assign('article_privacy', $article_privacy);
$smarty->assign('article_comments', $article_comments);
include "footer.php";
?>