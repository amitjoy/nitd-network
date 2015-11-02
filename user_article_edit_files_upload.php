<?
$page = "user_article_edit_files_upload";
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


// GET TOTAL SPACE USED
$space_used = $article->article_media_space();
$space_left = $article->articleowner_level_info[level_article_album_storage] - $space_used;



// UPLOAD FILES
if($task == "doupload") {
  $file_result = Array();

  // RUN FILE UPLOAD FUNCTION FOR EACH SUBMITTED FILE
  $update_articlealbum = 0;
  $new_articlealbum_cover = "";
  for($f=1;$f<6;$f++) {
    $fileid = "file".$f;
    if($_FILES[$fileid]['name'] != "") {
      $file_result[$fileid] = $article->article_media_upload($fileid, $articlealbum_info[articlealbum_id], $space_left);
      if($file_result[$fileid]['is_error'] == 0) {
  	$file_result[$fileid]['message'] = stripslashes($_FILES[$fileid]['name'])." 11151923";
	$new_articlealbum_cover = $file_result[$fileid]['media_id'];
        $update_articlealbum = 1;
      }
    }
  }

  // UPDATE ARTICLE ALBUM UPDATED DATE AND ARTICLE ALBUM COVER IF FILE UPLOADED
  if($update_articlealbum == 1) {
    $newdate = time();
    if($articlealbum_info[articlealbum_cover] != 0) { $new_articlealbum_cover = $articlealbum_info[articlealbum_cover]; }
    $database->database_query("UPDATE se_articlealbums SET articlealbum_cover='$new_articlealbum_cover', articlealbum_dateupdated='$newdate' WHERE articlealbum_id='$articlealbum_info[articlealbum_id]'");

    // UPDATE LAST UPDATE DATE (SAY THAT 10 TIMES FAST)
    $article->article_lastupdate();
  }

} // END TASK



// GET MAX FILESIZE ALLOWED
$max_filesize_kb = ($article->articleowner_level_info[level_article_album_maxsize]) / 1024;
$max_filesize_kb = round($max_filesize_kb, 0);

// CONVERT UPDATED SPACE LEFT TO MB
$space_left_mb = ($space_left / 1024) / 1024;
$space_left_mb = round($space_left_mb, 2);


// ASSIGN VARIABLES AND SHOW USER EDIT ARTICLE PAGE
$smarty->assign('file1_result', $file_result[file1][message]);
$smarty->assign('file2_result', $file_result[file2][message]);
$smarty->assign('file3_result', $file_result[file3][message]);
$smarty->assign('file4_result', $file_result[file4][message]);
$smarty->assign('file5_result', $file_result[file5][message]);
$smarty->assign('article', $article);
$smarty->assign('space_left', $space_left_mb);
$smarty->assign('allowed_exts', str_replace(",", ", ", $article->articleowner_level_info[level_article_album_exts]));
$smarty->assign('max_filesize', $max_filesize_kb);
include "footer.php";
?>