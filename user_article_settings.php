<?
$page = "user_article_settings";
include "header.php";
if($user->level_info[level_article_allow] == 0) { header("Location: user_home.php"); exit(); }
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }

// SET VARS
$result = 0;

// SAVE NEW SETTINGS
if($task == "dosave") {
  $usersetting_notify_articlecomment = $_POST['usersetting_notify_articlecomment'] ? 1 : 0;
  $usersetting_notify_articlemediacomment = $_POST['usersetting_notify_articlemediacomment'] ? 1 : 0;

  // UPDATE DATABASE
  $database->database_query("UPDATE se_usersettings SET usersetting_notify_articlecomment='$usersetting_notify_articlecomment', usersetting_notify_articlemediacomment='$usersetting_notify_articlemediacomment' WHERE usersetting_user_id='".$user->user_info[user_id]."'");
  $user = new se_user(Array($user->user_info[user_id]));
  $result = 1;
}

// ASSIGN USER SETTINGS
$user->user_settings();

// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('result', $result);
include "footer.php";
?>