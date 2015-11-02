<?php
$page = "forum_view";
include "header.php";

if(isset($_GET['task'])) { $task = $_GET['task']; } elseif(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = "main"; }
if(isset($_GET['p'])) { $p = (int) $_GET['p']; } elseif(isset($_POST['p'])) { $p = (int) $_POST['p']; } else { $p = 1; }
if(isset($_GET['forum_id'])) { $forum_id = (int) $_GET['forum_id']; } elseif(isset($_POST['forum_id'])) { $forum_id = (int) $_POST['forum_id']; } else { $forum_id = 0; }

// IF FORUMS ARE TURNED OFF, FORWARD TO HOME PAGE
if($setting[setting_forum_status] == 0) { header("Location: home.php"); exit(); }

// IF FORUMS ARE IN MAINTENANCE MOD, FORWARD TO MAIN FORUM
if($setting[setting_forum_status] == 2 && (!$user->user_exists || ($user->user_exists && !$forum->forum_is_moderator($user->user_info[user_id])))) { header("Location: forum.php"); exit(); }

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if((!$user->user_exists && !$setting['setting_permission_forum'])) {
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

// VALIDATE FORUM ID AND GET INFO
$forum_query = $database->database_query("SELECT * FROM se_forums WHERE forum_id='$forum_id'");
if($database->database_num_rows($forum_query) != 1) {
  header("Location: forum.php");
  exit();
}
$forum_info = $database->database_fetch_assoc($forum_query);



// DETERMINE THE USER'S PERMISSIONS FOR THIS FORUM (VIEW, POST, MODERATE, ETC)
$forum_permission = $forum->forum_permission($forum_info[forum_id]);

// SEND USER BACK IF NOT ALLOWED TO VIEW THIS FORUM
if(!$forum_permission[allowed_to_view]) { header("Location: forum.php"); exit(); }

// IF LOGGED IN, SET COOKIE TO SIGNAL FORUM IS "READ"
if($user->user_exists) { setcookie("forum_{$user->user_info[user_id]}_{$forum_info[forum_id]}", time(), time()+99999999, "/"); }

// GET TOTAL TOPICS
$total_topics = $forum_info[forum_totaltopics];

// MAKE TOPIC PAGES
$topics_per_page = 25;
$page_vars = make_page($total_topics, $topics_per_page, $p);

// SET TOPIC ARRAY
$topic_array = $forum->forum_topic_list($forum_info[forum_id], $page_vars[0], $topics_per_page);

// GET MODERATORS
$mod_array = Array();
$mods = $database->database_query("SELECT se_users.user_id, se_users.user_username, se_users.user_fname, se_users.user_lname FROM se_forummoderators LEFT JOIN se_users ON se_forummoderators.forummoderator_user_id=se_users.user_id WHERE se_forummoderators.forummoderator_forum_id='$forum_info[forum_id]' AND se_users.user_id IS NOT NULL");
while($user_info = $database->database_fetch_assoc($mods)) {

  $mod_user = new se_user();
  $mod_user->user_info[user_id] = $user_info[user_id];
  $mod_user->user_info[user_username] = $user_info[user_username];
  $mod_user->user_info[user_fname] = $user_info[user_fname];
  $mod_user->user_info[user_lname] = $user_info[user_lname];
  $mod_user->user_displayname();

  $mod_array[] = $mod_user;
}


// ASSIGN SMARTY VARS AND INCLUDE FOOTER
$smarty->assign('forum_info', $forum_info);
$smarty->assign('topics', $topic_array);
$smarty->assign('moderators', $mod_array);
$smarty->assign('forum_permission', $forum_permission);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
include "footer.php";
?>