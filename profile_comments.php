<?php

$page = "profile_comments";
include "header.php";

// CHECK FOR SECOND PAGE OWNER
if(isset($_POST['user2'])) { $user2_username = $_POST['user2']; } elseif(isset($_GET['user2'])) { $user2_username = $_GET['user2']; } else { $user2_username = ""; }
if(isset($_POST['user2_id'])) { $user2_id = $_POST['user2_id']; } elseif(isset($_GET['user2_id'])) { $user2_id = $_GET['user2_id']; } else { $user2_id = ""; }
$owner2 = new se_user(Array($user2_id, $user2_username));

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if($user->user_exists == 0 && $setting['setting_permission_profile'] == 0) { exit(); }

// DISPLAY ERROR PAGE IF NO OWNER
if($owner->user_exists == 0 || $owner2->user_exists == 0) { exit(); }

// GET PRIVACY LEVEL FOR OWNER 1
$privacy_max = $owner->user_privacy_max($user);
$is_profile_private = 0;
if(!($privacy_max & $owner->user_info['user_privacy'])) { exit(); }


// GET PRIVACY LEVEL FOR OWNER 2
$privacy_max = $owner2->user_privacy_max($user);
$is_profile_private = 0;
if(!($privacy_max & $owner2->user_info['user_privacy'])) { exit(); }


// GET CONVERSATION
$comment_query = $database->database_query("SELECT * FROM se_profilecomments WHERE (profilecomment_user_id='{$owner->user_info['user_id']}' AND profilecomment_authoruser_id='{$owner2->user_info['user_id']}') OR (profilecomment_user_id='{$owner2->user_info['user_id']}' AND profilecomment_authoruser_id='{$owner->user_info['user_id']}') ORDER BY profilecomment_date DESC");
while($comment_info = $database->database_fetch_assoc($comment_query))
{
  if($comment_info['profilecomment_authoruser_id'] == $owner->user_info['user_id']) { $author = $owner; } else { $author = $owner2; }

  // SET COMMENT ARRAY
  $comment_array[] = Array(
    'comment_id' => $comment_info['profilecomment_id'],
    'comment_author' => $author,
    'comment_date' => $comment_info['profilecomment_date'],
    'comment_body' => $comment_info['profilecomment_body']
  );
}



// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('owner2', $owner2);
$smarty->assign('comments', $comment_array);
include "footer.php";
?>