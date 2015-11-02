<?
$page = "user_article_add";
include "header.php";
if($user->level_info[level_article_allow] == 0) { header("Location: user_home.php"); exit(); }
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }

// INITIALIZE VARIABLES
$is_error = 0;
$error_message = "";
$article_title = "";
$article_body = "";

// GET PRIVACY SETTINGS
$level_article_privacy = unserialize($user->level_info[level_article_privacy]);
rsort($level_article_privacy);
$level_article_comments = unserialize($user->level_info[level_article_comments]);
rsort($level_article_comments);

$article_draft = 0;
$article_privacy = $level_article_privacy[0];
$article_comments = $level_article_comments[0];
$article_search = 1;
$articlecat_id = 0;
$subarticlecat_id = 0;
$article_date_start = time();

$article_tags = rc_toolkit::get_request('article_tags');

// INITIALIZE ARTICLE OBJECT
$new_article = new rc_article($user->user_info[user_id], 0);

// ATTEMPT TO ADD ARTICLE
if($task == "doadd") {
  $article_title = censor($_POST['article_title']);
  $article_body = censor($_POST['article_body']);
 
  $articlecat_id = $_POST['articlecat_id'];
  $subarticlecat_id = $_POST['subarticlecat_id'];
  $article_draft = isset($_POST['draft']) ? 1 : 0;
  $article_search = $_POST['article_search'];
  $article_privacy = $_POST['article_privacy'];
  $article_comments = $_POST['article_comments'];
  if($_POST['article_date_start_hour'] == "12") { $_POST['article_date_start_hour'] = 0; }
  if($_POST['article_date_start_ampm'] == "PM") { $_POST['article_date_start_hour'] += 12; }
  $article_date_start = mktime($_POST['article_date_start_hour'], $_POST['article_date_start_minute'], 0, $_POST['article_date_start_month'], $_POST['article_date_start_day'], $_POST['article_date_start_year']);

  // CHECK TO MAKE SURE TITLE HAS BEEN ENTERED
  if(str_replace(" ", "", $article_title) == "") {
    $is_error = 1;
    $error_message = 11151301;
  }

  // MAKE SURE SUBMITTED PRIVACY OPTIONS ARE ALLOWED, IF NOT, SET TO EVERYONE
  if(!in_array($article_privacy, $level_article_privacy)) { $article_privacy = $level_article_privacy[0]; }
  if(!in_array($article_comments, $level_article_comments)) { $article_comments = $level_article_comments[0]; }

  // CHECK THAT SEARCH IS NOT BLANK
  if($user->level_info[level_article_search] == 0) { $article_search = 1; }

  $article_approved = ($user->level_info[level_article_approved] == 0) ? 1 : 0; 
  
  // IF NO ERROR, SAVE ARTICLE
  if($is_error == 0) {

  	$article_date_start = ($article_draft == 0) ? time() : 0;
  	
    if($subarticlecat_id != 0) { $articlecat_id = $subarticlecat_id; }
    $article_id = $new_article->article_create($article_title, $article_body, $articlecat_id, $article_date_start, $article_draft, $article_approved, $article_search, $article_privacy, $article_comments);
    
    if ($article_id > 0) {
    	$rc_tag = new rc_articletag();
    	$rc_tag->update_object_tags($article_id, $article_tags);
    }
    
    
    // INSERT ACTION    
    if ($article_approved == 1 and $article_draft == 0) {
	    $article_title_short = $article_title;
	    if(strlen($article_title_short) > 100) { $article_title_short = substr($article_title_short, 0, 97); $article_title_short .= "..."; }
      $actions->actions_add($user, "newarticle", Array($user->user_info[user_username], $user->user_displayname, $article_id, $article_title_short, date("F j, Y, g:i a",$article_date_start)), Array(), 0, FALSE, "user", $user->user_info[user_id], $article_privacy);
    }

    header("Location: user_article_edit.php?article_id=$article_id&justadded=1");
    exit();
  }
}



// GET ARTICLE CATEGORIES
$categories_array = Array();
$categories_query = $database->database_query("SELECT * FROM se_articlecats WHERE articlecat_dependency='0' ORDER BY articlecat_id");
while($category = $database->database_fetch_assoc($categories_query)) {
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


// ASSIGN VARIABLES AND SHOW ADD ARTICLES PAGE
$smarty->assign('is_error', $is_error);
$smarty->assign('error_message', $error_message);
$smarty->assign('cats', $categories_array);
$smarty->assign('article_title', $article_title);
$smarty->assign('article_body', str_replace("\r\n", "", html_entity_decode($article_body)));
$smarty->assign('article_date_start', $article_date_start);
$smarty->assign('article_draft', $article_draft);
$smarty->assign('article_search', $article_search);
$smarty->assign('article_privacy', $article_privacy);
$smarty->assign('article_comments', $article_comments);
$smarty->assign('privacy_options', $privacy_options);
$smarty->assign('comment_options', $comment_options);
$smarty->assign('articlecat_id', $articlecat_id);
$smarty->assign('subarticlecat_id', $subarticlecat_id);
$smarty->assign('article_tags', $article_tags);
include "footer.php";
?>