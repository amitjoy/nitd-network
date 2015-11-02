<?
$page = "user_article_edit_files";
include "header.php";
if($user->level_info[level_article_allow] == 0) { header("Location: user_home.php"); exit(); }
if(isset($_GET['article_id'])) { $article_id = $_GET['article_id']; } elseif(isset($_POST['article_id'])) { $article_id = $_POST['article_id']; } else { $article_id = 0; }
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }

// INITIALIZE ARTICLE OBJECT
$article = new rc_article($user->user_info[user_id], $article_id);

if($article->article_exists == 0) { header("Location: user_article.php"); exit(); }
if($article->article_info[article_user_id] != $user->user_info[user_id]) { header("Location: user_article.php"); exit(); }


// GET ARTICLE ALBUM INFO
$articlealbum_info = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_articlealbums WHERE articlealbum_article_id='".$article->article_info[article_id]."' LIMIT 1"));





// UPDATE FILES IN THIS ARTICLE ALBUM
if($task == "doupdate") {

  // GET TOTAL FILES
  $total_files = $article->article_media_total($articlealbum_info[articlealbum_id]);

  // DELETE NECESSARY FILES
  $article->article_media_delete(0, $total_files, "articlemedia_id ASC", "(articlemedia_articlealbum_id='$articlealbum_info[articlealbum_id]')");

  // UPDATE NECESSARY FILES
  $media_array = $article->article_media_update(0, $total_files, "articlemedia_id ASC", "(articlemedia_articlealbum_id='$articlealbum_info[articlealbum_id]')");

  // SET ALBUM COVER AND UPDATE DATE
  $newdate = time();
  $articlealbum_info[articlealbum_cover] = $_POST['articlealbum_cover'];
  if(!in_array($articlealbum_info[articlealbum_cover], $media_array)) { $articlealbum_info[articlealbum_cover] = $media_array[0]; }
  $database->database_query("UPDATE se_articlealbums SET articlealbum_cover='$articlealbum_info[articlealbum_cover]', articlealbum_dateupdated='$new_date' WHERE articlealbum_id='$articlealbum_info[articlealbum_id]'");

  // UPDATE LAST UPDATE DATE (SAY THAT 10 TIMES FAST)
  $article->article_lastupdate();

  // SHOW SUCCESS MESSAGE
  $result = 1;

}






// SHOW FILES IN THIS ALBUM
$total_files = $article->article_media_total($articlealbum_info[articlealbum_id]);
$file_array = $article->article_media_list(0, $total_files, "articlemedia_id ASC", "(articlemedia_articlealbum_id='$articlealbum_info[articlealbum_id]')");





// ASSIGN VARIABLES AND SHOW USER EDIT ARTICLE PHOTO PAGE
$smarty->assign('article', $article);
$smarty->assign('result', $result);
$smarty->assign('files', $file_array);
$smarty->assign('files_total', $total_files);
$smarty->assign('articlealbum_id', $articlealbum_info[articlealbum_id]);
include "footer.php";
?>