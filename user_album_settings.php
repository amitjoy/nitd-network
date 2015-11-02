<?php

$page = "user_album_settings";
include "header.php";

$task = ( isset($_POST['task']) ? $_POST['task'] : NULL );

// ENSURE ALBUMS ARE ENABLED FOR THIS USER
if( !$user->level_info['level_album_allow'] )
{
  header("Location: user_home.php");
  exit();
}

// SET VARS
$result = 0;
$level_album_profile = explode(",", $user->level_info['level_album_profile']);

// SAVE NEW CSS
if($task == "dosave")
{
  $style_album = addslashes(str_replace("-moz-binding", "", strip_tags(htmlspecialchars_decode($_POST['style_album'], ENT_QUOTES))));
  $user_profile_album = $_POST['user_profile_album'];

  // ENSURE USER SELECTED APPROPRIATE OPTION
  if(!in_array($user_profile_album, $level_album_profile)) { $user_profile_album = $level_album_profile[0]; }

  $database->database_query("UPDATE se_albumstyles SET albumstyle_css='{$style_album}' WHERE albumstyle_user_id='{$user->user_info['user_id']}'");
  $database->database_query("UPDATE se_users SET user_profile_album='{$user_profile_album}' WHERE user_id='{$user->user_info['user_id']}'");
  $user->user_lastupdate();
  $user->user_info['user_profile_album'] = $user_profile_album;
  $result = 1;
}



// GET THIS USER'S ALBUM CSS
$style_query = $database->database_query("SELECT albumstyle_css FROM se_albumstyles WHERE albumstyle_user_id='{$user->user_info['user_id']}' LIMIT 1");
if($database->database_num_rows($style_query) == 1) { 
  $style_info = $database->database_fetch_assoc($style_query); 
} else {
  $database->database_query("INSERT INTO se_albumstyles (albumstyle_user_id, albumstyle_css) VALUES ('{$user->user_info['user_id']}', '')");
  $style_info = $database->database_fetch_assoc($database->database_query("SELECT albumstyle_css FROM se_albumstyles WHERE albumstyle_user_id='{$user->user_info['user_id']}' LIMIT 1")); 
}

// ENSURE PROFILE LOCATION IS ALLOWED
if(!in_array($user->user_info['user_profile_album'], $level_album_profile)) { $user->user_info['user_profile_album'] = $level_album_profile[0]; }

// ASSIGN SMARTY VARIABLES AND DISPLAY ALBUM STYLE PAGE
$smarty->assign('result', $result);
$smarty->assign('level_album_profile', $level_album_profile);
$smarty->assign('style_album', htmlspecialchars($style_info['albumstyle_css'], ENT_QUOTES, 'UTF-8'));
include "footer.php";
?>