<?php
$page = "user_music_settings";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }

if( !$user->level_info['level_music_allow'] ) { header("Location: user_home.php"); exit(); }

if($task == "dosave")
{
	$profile_autoplay = $_POST["profile_autoplay"];
	$site_autoplay    = $_POST["site_autoplay"];
	$music_skin       = $_POST["select_music_skin"];
  
  $sql = "
    UPDATE
      se_usersettings
    SET
      usersetting_music_profile_autoplay='$profile_autoplay',
      usersetting_music_site_autoplay='$site_autoplay',
      usersetting_xspfskin_id='$music_skin'
    WHERE
      usersetting_user_id='{$user->user_info['user_id']}'
    LIMIT
      1
  ";
  
  $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
}

$usersettings = $database->database_fetch_assoc($database->database_query("SELECT usersetting_music_profile_autoplay, usersetting_xspfskin_id, usersetting_music_site_autoplay FROM se_usersettings WHERE usersetting_user_id = '{$user->user_info['user_id']}'"));
$music = new se_music($user->user_info['user_id']);


if( $user->level_info['level_music_allow_skins'] )
{
	$skins = $music->music_skin_list();
  $smarty->assign_by_ref('skins', $skins);
}

$smarty->assign('profile_autoplay', $usersettings['usersetting_music_profile_autoplay']);
$smarty->assign('site_autoplay', $usersettings['usersetting_music_site_autoplay']);
$smarty->assign('skin_id', ( !empty($usersettings['usersetting_xspfskin_id']) ? $usersettings['usersetting_xspfskin_id'] : 1 ));

include "footer.php";
?>
