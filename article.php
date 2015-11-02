<?
$page = "article";
include "header.php";

if(isset($_GET['article_id'])) { $article_id = $_GET['article_id']; } else { $article_id = 0; }

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if($user->user_exists == 0 & $setting[setting_permission_article] == 0) {
  $page = "error";
  $smarty->assign('error_header', 11150528);
  $smarty->assign('error_message', 11150530);
  $smarty->assign('error_submit', 11150539);
  include "footer.php";
}


// INITIALIZE ARTICLE OBJECT
$rc_article = new rc_article($user->user_info[user_id], $article_id);

//rc_toolkit::debug($rc_article);

if($rc_article->article_exists == 0) { 
  $page = "error";
  $smarty->assign('error_header', 11150528);
  $smarty->assign('error_message', 11150541);
  $smarty->assign('error_submit', 11150539);
  include "footer.php";
}
elseif ($rc_article->article_info[article_approved] == 0) {
  $page = "error";
  $smarty->assign('error_header', 11150528);
  $smarty->assign('error_message', 11150502);
  $smarty->assign('error_submit', 11150539);
  include "footer.php";
}
elseif ($rc_article->article_info[article_draft] == 1) {
  $page = "error";
  $smarty->assign('error_header', 11150528);
  $smarty->assign('error_message', 11150503);
  $smarty->assign('error_submit', 11150539);
  include "footer.php";
}

$rc_article->article_owner();
$owner = $rc_article->article_owner;

// CHECK PRIVACY
$privacy_max = $owner->user_privacy_max($user);

//rc_toolkit::debug($privacy_max,'$privacy_max');
//rc_toolkit::debug($rc_article->article_info[article_privacy],'$rc_article->article_info[article_privacy]');
//rc_toolkit::debug(!($rc_article->article_info[article_privacy] & $privacy_max),'$($rc_article->article_info[article_privacy] & $privacy_max)');

if(!($rc_article->article_info[article_privacy] & $privacy_max)) {
  $page = "error";
  $smarty->assign('error_header', 11150528);
  $smarty->assign('error_message', 11150501);
  $smarty->assign('error_submit', 11150539);
  include "footer.php";
}
  
  
// UPDATE ARTICLE VIEWS IF ARTICLE VISIBLE
$article_views = $rc_article->article_info[article_views]+1;
$database->database_query("UPDATE se_articles SET article_views='$article_views' WHERE article_id='".$rc_article->article_info[article_id]."'");


// GET ARTICLE LEADER INFO
$articleowner_info = $database->database_fetch_assoc($database->database_query("SELECT user_id, user_username FROM se_users WHERE user_id='".$rc_article->article_info[article_user_id]."'"));

// GET ARTICLE CATEGORY
$article_category = "";
$parent_category = "";
$article_category_query = $database->database_query("SELECT articlecat_id, articlecat_title, articlecat_dependency FROM se_articlecats WHERE articlecat_id='".$rc_article->article_info[article_articlecat_id]."' LIMIT 1");
if($database->database_num_rows($article_category_query) == 1) {
  $article_category_info = $database->database_fetch_assoc($article_category_query);
  $article_category = $article_category_info[articlecat_title];
  if($article_category_info[articlecat_dependency] != 0) {
    $parent_category = $database->database_fetch_assoc($database->database_query("SELECT articlecat_id, articlecat_title FROM se_articlecats WHERE articlecat_id='".$article_category_info[articlecat_dependency]."' LIMIT 1"));
  }
}


// GET ARTICLE COMMENTS
$comment = new se_comment('article', 'article_id', $rc_article->article_info[article_id]);
$total_comments = $comment->comment_total();
$comments = $comment->comment_list(0, 10);



// CHECK IF USER IS ALLOWED TO COMMENT
$allowed_to_comment = 1;
if(!($privacy_max & $rc_article->article_info[article_comments])) { $allowed_to_comment = 0; }

// SHOW FILES IN THIS ALBUM
$articlealbum_info = $database->database_fetch_assoc($database->database_query("SELECT articlealbum_id FROM se_articlealbums WHERE articlealbum_article_id='".$rc_article->article_info[article_id]."' LIMIT 1"));
$total_files = $rc_article->article_media_total($articlealbum_info[articlealbum_id]);
$file_array = $rc_article->article_media_list(0, 5, "RAND()", "(articlemedia_articlealbum_id='$articlealbum_info[articlealbum_id]')");

$rc_article->article_info[article_body] = str_replace("\r\n", "", html_entity_decode($rc_article->article_info[article_body]));

$rc_tag = new rc_articletag();
$article_tags = $rc_tag->get_object_tags($article_id);

// ASSIGN VARIABLES AND DISPLAY ARTICLE PAGE
$smarty->assign('article', $rc_article);
$smarty->assign('articleowner_info', $articleowner_info);
$smarty->assign('article_category', $article_category);
$smarty->assign('parent_category', $parent_category);
$smarty->assign('comments', $comments);
$smarty->assign('total_comments', $total_comments);
$smarty->assign('article_tags', $article_tags);
$smarty->assign('is_article_private', $is_article_private);
$smarty->assign('allowed_to_comment', $allowed_to_comment);
$smarty->assign('files', $file_array);
$smarty->assign('total_files', $total_files);
include "footer.php";
?>