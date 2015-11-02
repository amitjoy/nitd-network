<?php

$page = "user_friends_block";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['confirm'])) { $confirm = $_POST['confirm']; } elseif(isset($_GET['confirm'])) { $confirm = $_GET['confirm']; } else { $confirm = 0; }

// ENSURE BLOCKING IS ALLOWED FOR THIS USER
if( !$user->level_info['level_profile_block'] ) exit();

// INITIALIZE VARS
$submitted = 0;

// BLOCK USER
if($task == "block_do")
{
  // CHECK IF USER TO BE BLOCKED IS ALREADY BLOCKED
  if($user->user_blocked($owner->user_info['user_id'])) { exit(); }

  // CHECK IF USER IS TRYING TO BLOCK THEMSELVES
  if($owner->user_info['user_id'] == $user->user_info['user_id']) { exit(); }

  // DISPLAY ERROR PAGE IF NO OWNER
  if( !$owner->user_exists ) { exit(); }

  // BLOCK USER
  $new_blocklist = $user->user_info['user_blocklist'].$owner->user_info['user_id'].",";
  $database->database_query("UPDATE se_users SET user_blocklist='{$new_blocklist}' WHERE user_id='{$user->user_info['user_id']}'");
  $user->user_friend_remove($owner->user_info['user_id']);

  $submitted = 1;
  $task = "block";
}



// UNBLOCK USER
if($task == "unblock_do")
{
  // CHECK IF USER TO BE UNBLOCKED IS NOT ALREADY BLOCKED
  if( !$user->user_blocked($owner->user_info['user_id']) ) exit();

  // CHECK IF USER IS TRYING TO UNBLOCK THEMSELVES
  if( $owner->user_info['user_id'] == $user->user_info['user_id'] ) exit();

  // DISPLAY ERROR PAGE IF NO OWNER
  if( !$owner->user_exists ) exit();

  // UNBLOCK USER
  $blocklist = explode(",", $user->user_info['user_blocklist']);
  $user_key = array_search($owner->user_info['user_id'], $blocklist);
  $blocklist[$user_key] = "";
  $user->user_info['user_blocklist'] = implode(",", $blocklist);
  $database->database_query("UPDATE se_users SET user_blocklist='{$user->user_info['user_blocklist']}' WHERE user_id='{$user->user_info['user_id']}'");

  $submitted = 1;
  $task = "unblock";
}





// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('submitted', $submitted);
$smarty->assign('task', $task);
include "footer.php";
?>