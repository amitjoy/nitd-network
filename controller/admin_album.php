<?php

$page = "admin_album";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = "main"; }


// SET RESULT VARIABLE
$result = 0;


// SAVE CHANGES
if($task == "dosave")
{
  $setting['setting_permission_album'] = $_POST['setting_permission_album'];

  $database->database_query("UPDATE se_settings SET 
			setting_permission_album='$setting[setting_permission_album]'");

  $result = 1;
}


// ASSIGN VARIABLES AND SHOW GENERAL SETTINGS PAGE
$smarty->assign('result', $result);
include "admin_footer.php";
?>