<?php

$page = "admin_emails_config";
include "admin_header.php";


if(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = "main"; }


// SET RESULT VARIABLE
$result = 0;


// SAVE CHANGES
if($task == "dosave") {
  $email_method = addslashes($_POST['email_method']);
  $smtp_host = addslashes($_POST['smtp_host']);
  $smtp_user = addslashes($_POST['smtp_user']);
  $smtp_pass = addslashes($_POST['smtp_pass']);
  $smtp_port = addslashes($_POST['smtp_port']);

  // SAVE SETTINGS
  $database->database_query("UPDATE se_settings_email SET email_method='$email_method', smtp_host='$smtp_host', smtp_user='$smtp_user', smtp_pass='$smtp_pass', smtp_port='$smtp_port'");

  $result = 1;
}



// GET EMAILS
$setting_email_query = $database->database_query("SELECT * FROM se_settings_email LIMIT 1");
$setting_email_array = $database->database_fetch_assoc($setting_email_query);



// ASSIGN VARIABLES AND SHOW GENERAL SETTINGS PAGE
$smarty->assign('result', $result);
$smarty->assign('setting_email', $setting_email_array);
include "admin_footer.php";
?>