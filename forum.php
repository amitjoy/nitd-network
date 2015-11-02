<?php
$page = "forum";
include "header.php";

if(isset($_GET['task'])) { $task = $_GET['task']; } elseif(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = "main"; }

// IF FORUMS ARE TURNED OFF, FORWARD TO HOME PAGE
if($setting[setting_forum_status] == 0) { header("Location: home.php"); exit(); }

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if((!$user->user_exists && !$setting['setting_permission_forum'])) {
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}


// SET FORUMCAT ARRAY
$forumcat_array = $forum->forum_list();

// SET WHETHER USER IS A MODERATOR IN ANY FORUMS
$forum_is_moderator = ($user->user_exists)?$forum->forum_is_moderator($user->user_info[user_id]):false;

// CLEAN OUT OLD LOG ENTRIES (90 DAYS)
$database->database_query("DELETE FROM se_forumlogs WHERE forumlog_date<'".(time()-60*60*24*90)."'");

// ASSIGN SMARTY VARS AND INCLUDE FOOTER
$smarty->assign('forumcats', $forumcat_array);
$smarty->assign('forum_is_moderator', $forum_is_moderator);
include "footer.php";
?>