<?php

$page = "admin_poll";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }

// SET RESULT VARIABLE
$result = 0;

if($task == "dosave")
{
  $setting_permission_poll  = ( !empty($_POST['setting_permission_poll']) ? $_POST['setting_permission_poll'] : NULL );
  $setting_poll_html        = ( !empty($_POST['setting_poll_html'])       ? $_POST['setting_poll_html']       : NULL );
  
  $setting_poll_html  = str_replace(" ", "", $setting_poll_html);
  
  $sql = "
    UPDATE
      se_settings
    SET
      setting_permission_poll='$setting_permission_poll',
      setting_poll_html='$setting_poll_html'
  ";
  
  $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  
  $setting['setting_permission_poll'] = $setting_permission_poll;
  $setting['setting_poll_html'] = $setting_poll_html;
  $result = 1;
}

// ASSIGN VARIABLES AND SHOW GENERAL SETTINGS PAGE
$smarty->assign('result', $result);
include "admin_footer.php";
?>