<?php

$page = "user_userconnection_setting";
include "header.php";
global $user,$database;
$id = $user->user_info['user_id'];
if (isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if ($task == "edit") {
	$usersetting_userconnection = $_POST['usersetting_userconnection'];
	$database->database_query ("UPDATE se_usersettings SET usersetting_userconnection = '$usersetting_userconnection' WHERE usersetting_user_id = '$id'");
	 $smarty->assign ('success_message',650002011);
}
$row = $database->database_query ("SELECT usersetting_userconnection FROM se_usersettings WHERE usersetting_user_id = '$id'");
$result = $database->database_fetch_assoc ($row);
$smarty->assign ('result', $result);
include "footer.php";
?>