<?php

$page = "user_album";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }

// ENSURE ALBUMS ARE ENABLED FOR THIS USER
if($user->level_info[level_album_allow] == 0) { header("Location: user_home.php"); exit(); }


// CREATE ALBUM OBJECT
$album = new se_album($user->user_info[user_id]);


// BE SURE ALBUM BELONGS TO THIS USER, DELETE ALBUM
if($task == "delete") {
  $album_id = $_GET['album_id'];
  if($database->database_num_rows($database->database_query("SELECT album_id FROM se_albums WHERE album_id='$album_id' AND album_user_id='".$user->user_info[user_id]."'")) == 1) { 
    $album->album_delete($album_id);    
  }



// MOVE ALBUM UP
} elseif($task == "moveup") {
  $album_id = $_GET['album_id'];

  $album_query = $database->database_query("SELECT album_id, album_order FROM se_albums WHERE album_id='$album_id' AND album_user_id='".$user->user_info[user_id]."'");
  if($database->database_num_rows($album_query) == 1) { 

    $album_info = $database->database_fetch_assoc($album_query);

    $prev_query = $database->database_query("SELECT album_id, album_order FROM se_albums WHERE album_user_id='".$user->user_info[user_id]."' AND album_order<'".$album_info[album_order]."' ORDER BY album_order DESC LIMIT 1");
    if($database->database_num_rows($prev_query) == 1) {

      $prev_info = $database->database_fetch_assoc($prev_query);

      // SWITCH ORDER
      $database->database_query("UPDATE se_albums SET album_order='" . $prev_info[album_order] . "' WHERE album_id='" . $album_info[album_id] . "'" );
      $database->database_query("UPDATE se_albums SET album_order='" . $album_info[album_order] . "' WHERE album_id='" . $prev_info[album_id]. "'");

      // SEND AJAX CONFIRMATION
      echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><script type='text/javascript'>";
      echo "window.parent.reorderAlbum('$album_info[album_id]', '$prev_info[album_id]');";
      echo "</script></head><body></body></html>";
      exit();

    } 
  }
}


// GET ALBUMS
$total_albums = $album->album_total();
$album_array = $album->album_list(0, $total_albums, "album_order ASC");

$space_used = $album->album_space();
$total_files = $album->album_files();

// CALCULATE SPACE FREE, CONVERT TO MEGABYTES
if($user->level_info[level_album_storage]) {
  $space_free = $user->level_info[level_album_storage] - $space_used;
} else {
  $space_free = ( $dfs=disk_free_space("/") ? $dfs : pow(2, 32) );
} 
$space_free = ($space_free / 1024) / 1024;
$space_free = round($space_free, 2);


// ASSIGN VARIABLES AND SHOW VIEW ALBUMS PAGE
$smarty->assign('space_free', $space_free);
$smarty->assign('total_files', $total_files);
$smarty->assign('albums_total', $total_albums);
$smarty->assign('albums', $album_array);
include "footer.php";
?>