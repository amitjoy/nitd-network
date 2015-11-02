<?
$page = "article_album";
include "header.php";

if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
if(isset($_GET['article_id'])) { $article_id = $_GET['article_id']; } else { $article_id = 0; }

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if($user->user_exists == 0 & $setting[setting_permission_article] == 0) {
  $smarty->assign('error_header', 11150803);
  $smarty->assign('error_message', 11150804);
  $smarty->assign('error_submit', 11150810);
  $smarty->display("error.tpl");
  exit();
}

// INITIALIZE ARTICLE OBJECT
$article = new rc_article($user->user_info[user_id], $article_id);
if($article->article_exists == 0) { header("Location: home.php"); exit(); }


if(!$article->is_article_active()) { 
  header("Location: article.php?article_id=".$article->article_info[article_id]); exit();
}

// GET ARTICLE ALBUM INFO
$articlealbum_info = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_articlealbums WHERE articlealbum_article_id='".$article->article_info[article_id]."' LIMIT 1"));


// GET PRIVACY LEVEL
$article->article_owner();
$owner = $article->article_owner;

// CHECK PRIVACY
$privacy_max = $owner->user_privacy_max($user);
if(!($article->article_info[article_privacy] & $privacy_max)) {
  header("Location: article.php?article_id=".$article->article_info[article_id]); exit();
}


// UPDATE ALBUM VIEWS
$articlealbum_views_new = $articlealbum_info[articlealbum_views] + 1;
$database->database_query("UPDATE se_articlealbums SET articlealbum_views='$articlealbum_views_new' WHERE articlealbum_id='$articlealbum_info[articlealbum_id]' LIMIT 1");



// GET TOTAL FILES IN ARTICLE ALBUM
$total_files = $article->article_media_total($articlealbum_info[articlealbum_id]);

// MAKE MEDIA PAGES
$files_per_page = 16;
$page_vars = make_page($total_files, $files_per_page, $p);

// GET MEDIA ARRAY
$file_array = $article->article_media_list($page_vars[0], $files_per_page, "articlemedia_id ASC", "(articlemedia_articlealbum_id='$articlealbum_info[articlealbum_id]')");


// GET CUSTOM ARTICLE STYLE IF ALLOWED
if($article->articleowner_level_info[level_article_style] != 0 & $is_article_private == 0) { 
  $articlestyle_info = $database->database_fetch_assoc($database->database_query("SELECT articlestyle_css FROM se_articlestyles WHERE articlestyle_article_id='".$article->article_info[article_id]."' LIMIT 1")); 
  $global_css = $articlestyle_info[articlestyle_css];
}




// ASSIGN VARIABLES AND DISPLAY ARTICLE ALBUM PAGE
$smarty->assign('article', $article);
$smarty->assign('files', $file_array);
$smarty->assign('total_files', $total_files);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($file_array));
include "footer.php";
?>