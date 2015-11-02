<?php

$page = "user_album_update";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_GET['album_id'])) { $album_id = $_GET['album_id']; } elseif(isset($_POST['album_id'])) { $album_id = $_POST['album_id']; } else { $album_id = 0; }

// ENSURE ALBUMS ARE ENABLED FOR THIS USER
if($user->level_info[level_album_allow] == 0) { header("Location: user_home.php"); exit(); }

// BE SURE ALBUM BELONGS TO THIS USER
$album = $database->database_query("SELECT * FROM se_albums WHERE album_id='$album_id' AND album_user_id='".$user->user_info[user_id]."'");
if($database->database_num_rows($album) != 1) { header("Location: user_album.php"); exit(); }
$album_info = $database->database_fetch_assoc($album);


// SET VARS
$result = 0;
$album = new se_album($user->user_info[user_id]);



// ROTATE
if($task == "rotate") {
  $media_id = $_GET['media_id'];
  $dir = $_GET['dir'];

  if($dir == "cc") { $dir = 90; } else { $dir = 270; }

  // ROTATE IMAGE
  $album->album_media_rotate($media_id, $dir);

  // SET THUMBPATH
  $thumb_path = $url->url_userdir($user->user_info[user_id]).$media_id."_thumb.jpg?".rand();

  // SEND AJAX CONFIRMATION
  echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><script type='text/javascript'>";
  echo "var img = window.parent.document.getElementById('file_$media_id');";
  echo "img.src = '$thumb_path';";
  echo "</script></head><body></body></html>";
  exit();




// UPDATE FILES IN THIS ALBUM
} elseif($task == "doupdate") {

  // GET TOTAL FILES
  $total_files = $album->album_files($album_info[album_id]);

  // DELETE NECESSARY FILES
  $album->album_media_delete(0, $total_files, "media_id ASC", "(media_album_id='$album_info[album_id]')");

  // UPDATE NECESSARY FILES
  $media_array = $album->album_media_update(0, $total_files, "media_id ASC", "(media_album_id='$album_info[album_id]')");

  // SET ALBUM COVER AND UPDATE DATE
  $newdate = time();
  $album_info[album_cover] = $_POST['album_cover'];
  if(!in_array($album_info[album_cover], $media_array)) { $album_info[album_cover] = $media_array[0]; }
  $database->database_query("UPDATE se_albums SET album_cover='$album_info[album_cover]', album_dateupdated='$newdate' WHERE album_id='$album_info[album_id]'");

  // UPDATE LAST UPDATE DATE (SAY THAT 10 TIMES FAST)
  $user->user_lastupdate();

  // SHOW SUCCESS MESSAGE
  $result = 1;



// MOVE MEDIA UP
} elseif($task == "moveup") {
  $media_id = $_GET['media_id'];

  $media_query = $database->database_query("SELECT media_id, media_order, media_album_id FROM se_media LEFT JOIN se_albums ON se_media.media_album_id=se_albums.album_id WHERE media_id='$media_id' AND se_albums.album_user_id='".$user->user_info[user_id]."'");
  if($database->database_num_rows($media_query) == 1) { 

    $media_info = $database->database_fetch_assoc($media_query);

    $prev_query = $database->database_query("SELECT media_id, media_order FROM se_media LEFT JOIN se_albums ON se_media.media_album_id=se_albums.album_id WHERE se_media.media_album_id='$media_info[media_album_id]' AND se_albums.album_user_id='".$user->user_info[user_id]."' AND media_order<$media_info[media_order] ORDER BY media_order DESC LIMIT 1");
    if($database->database_num_rows($prev_query) == 1) {

      $prev_info = $database->database_fetch_assoc($prev_query);

      // SWITCH ORDER
      $database->database_query("UPDATE se_media SET media_order=$prev_info[media_order] WHERE media_id=$media_info[media_id]");
      $database->database_query("UPDATE se_media SET media_order=$media_info[media_order] WHERE media_id=$prev_info[media_id]");

      // SEND AJAX CONFIRMATION
      echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><script type='text/javascript'>";
      echo "window.parent.reorderMedia('$media_info[media_id]', '$prev_info[media_id]');";
      echo "</script></head><body></body></html>";
      exit();

    } 
  }
}




// SHOW FILES IN THIS ALBUM
$total_files = $album->album_files($album_info[album_id]);
$file_array = $album->album_media_list(0, $total_files, "media_order ASC", "(media_album_id='$album_info[album_id]')");


// GET LIST OF OTHER ALBUMS
$total_albums = $album->album_total("album_id<>'$album_info[album_id]'");
$album_array = $album->album_list(0, $total_albums, "album_order ASC", "album_id<>'$album_info[album_id]'");


// ASSIGN VARIABLES AND SHOW UDPATE ALBUMS PAGE
$smarty->assign('result', $result);
$smarty->assign('files', $file_array);
$smarty->assign('files_total', $total_files);
$smarty->assign('album_info', $album_info);
$smarty->assign('albums', $album_array);
$smarty->assign('albums_total', $total_albums);
include "footer.php";
?>