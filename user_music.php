<?php
$page = "user_music";
include "header.php";
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['music_id'])) { $music_id = $_POST['music_id']; } elseif(isset($_GET['music_id'])) { $music_id = $_GET['music_id']; }


// ENSURE MUSIC IS ENABLED FOR THIS USER
if( !$user->level_info['level_music_allow'] ) { header("Location: user_home.php"); exit(); }


// CREATE MUSIC OBJECT
$music = new se_music($user->user_info['user_id']);
$musiclist = $music->music_list();


// DELETE MULTIPLE SONGS
if( $task=="dodelete" )
{
  for( $i=0;$i<count($musiclist);$i++ )
  {
    $var = "delete_music_".$musiclist[$i]['music_id'];
    if( !empty($_POST[$var]) )
    {
      $music->music_delete($musiclist[$i]['music_id']);
    }
  }
}


// GET PLAYLIST
$musiclist = $music->music_list();


// GET TOTAL SPACE USED
$space_used = $music->music_space();
if( $user->level_info['level_music_storage'] ) {
  $space_left = $user->level_info['level_music_storage'] - $space_used;
} else {
  $space_left = ( $dfs=disk_free_space("/") ? $dfs : pow(2, 32) );
}
$space_left_mb = ($space_left / 1024) / 1024;
$space_left_mb = round($space_left_mb, 2);


// ASSIGN VARIABLES
$smarty->assign('task', $task);
$smarty->assign('musiclist', $musiclist);
$smarty->assign('music_total', count($musiclist));
$smarty->assign('space_left', $space_left_mb);

include "footer.php";
?>
