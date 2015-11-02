<?php

$page = "admin_emails";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = "main"; }


// SET RESULT VARIABLE
$result = 0;


// SAVE CHANGES
if($task == "dosave") {
  $setting[setting_email_fromname] = $_POST['setting_email_fromname'];
  $setting[setting_email_fromemail] = $_POST['setting_email_fromemail'];
  $subject = $_POST['subject'];
  $message = $_POST['message'];

  // SAVE SETTINGS
  $database->database_query("UPDATE se_settings SET setting_email_fromname='$setting[setting_email_fromname]', setting_email_fromemail='$setting[setting_email_fromemail]'");

  // GET EMAILS
  $email_query = $database->database_query("SELECT * FROM se_systememails ORDER BY systememail_id");
  while($email = $database->database_fetch_assoc($email_query)) {
    $vars = explode(",", $email[systememail_vars]);
    $new_subject = $subject[$email[systememail_id]];
    $new_message = $message[$email[systememail_id]];
    for($i=0;$i<count($vars);$i++) { 
      $new_subject = str_replace($vars[$i], "%".($i+1)."\$s", $new_subject); 
      $new_message = str_replace($vars[$i], "%".($i+1)."\$s", $new_message); 
    }
    SE_Language::edit($email[systememail_subject], $new_subject);
    SE_Language::edit($email[systememail_body], str_replace("\r\n", "<br>", $new_message));
  }

  $result = 1;
}



// GET EMAILS
$email_query = $database->database_query("SELECT * FROM se_systememails ORDER BY systememail_id");
while($email = $database->database_fetch_assoc($email_query)) {
  SE_Language::_preload_multi($email[systememail_title], $email[systememail_desc], $email[systememail_subject], $email[systememail_body]);
  $email[systememail_vars_array] = explode(",", $email[systememail_vars]);
  $email_array[] = $email;
}


// ASSIGN VARIABLES AND SHOW GENERAL SETTINGS PAGE
$smarty->assign('result', $result);
$smarty->assign('emails', $email_array);
include "admin_footer.php";
?>