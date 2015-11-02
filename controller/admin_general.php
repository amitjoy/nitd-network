<?php

$page = "admin_general";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = "main"; }


// SET RESULT VARIABLE
$result = 0;


// SAVE CHANGES
if($task == "dosave") {
  $old_setting_username = $setting[setting_username];
  $setting[setting_timezone] = $_POST['setting_timezone'];
  $setting[setting_dateformat] = $_POST['setting_dateformat'];
  $setting[setting_timeformat] = $_POST['setting_timeformat'];
  $setting[setting_username] = $_POST['setting_username'];
  $setting[setting_permission_profile] = $_POST['setting_permission_profile'];
  $setting[setting_permission_invite] = $_POST['setting_permission_invite'];
  $setting[setting_permission_search] = $_POST['setting_permission_search'];
  $setting[setting_permission_portal] = $_POST['setting_permission_portal'];
  $setting[setting_comment_html] = str_replace(" ", "", $_POST['setting_comment_html']);

  $database->database_query("UPDATE se_settings SET 
			setting_timezone='$setting[setting_timezone]', 
			setting_dateformat='$setting[setting_dateformat]', 
			setting_timeformat='$setting[setting_timeformat]',
			setting_username='$setting[setting_username]',
			setting_permission_profile='$setting[setting_permission_profile]',
			setting_permission_invite='$setting[setting_permission_invite]',
			setting_permission_search='$setting[setting_permission_search]',
			setting_permission_portal='$setting[setting_permission_portal]',
			setting_comment_html='$setting[setting_comment_html]'");
  if($setting[setting_username] == 0 && $old_setting_username == 1) { $database->database_query("UPDATE se_users SET user_username=user_id"); $database->database_query("TRUNCATE TABLE se_actions"); $database->database_query("TRUNCATE TABLE se_notifys"); }
  $result = 1;
}



// ASSIGN VARIABLES AND SHOW GENERAL SETTINGS PAGE
$smarty->assign('result', $result);
include "admin_footer.php";
?>