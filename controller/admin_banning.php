<?php
$page = "admin_banning";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = "main"; }


// SET RESULT VARIABLE
$result = 0;


// SAVE CHANGES
if($task == "dosave")
{
  // GET VALUES AND REMOVE SPACES
  $setting['setting_banned_ips'] = str_replace(", ", ",", $_POST['setting_banned_ips']);
  $setting['setting_banned_emails'] = str_replace(", ", ",", $_POST['setting_banned_emails']);
  $setting['setting_banned_usernames'] = str_replace(", ", ",", $_POST['setting_banned_usernames']);
  $setting['setting_banned_words'] = str_replace(", ", ",", $_POST['setting_banned_words']);
  $setting['setting_comment_code'] = $_POST['setting_comment_code'];
  $setting['setting_invite_code'] = $_POST['setting_invite_code'];
  $setting['setting_contact_code'] = $_POST['setting_contact_code'];
  $setting['setting_login_code'] = $_POST['setting_login_code'];
  $setting['setting_login_code_failedcount'] = $_POST['setting_login_code_failedcount'];

  $sql = "
    UPDATE
      se_settings
    SET 
			setting_banned_ips='{$setting['setting_banned_ips']}', 
			setting_banned_emails='{$setting['setting_banned_emails']}', 
			setting_banned_usernames='{$setting['setting_banned_usernames']}',
			setting_banned_words='{$setting['setting_banned_words']}',
			setting_comment_code='{$setting['setting_comment_code']}',
			setting_invite_code='{$setting['setting_invite_code']}',
			setting_contact_code='{$setting['setting_contact_code']}',
			setting_login_code='{$setting['setting_login_code']}',
			setting_login_code_failedcount='{$setting['setting_login_code_failedcount']}'
  ";
  
  $database->database_query($sql) or die($database->database_error());
  
  $result = TRUE;
}





// ASSIGN VARIABLES AND SHOW BANNING PAGE
$smarty->assign('result', $result);
include "admin_footer.php";
?>