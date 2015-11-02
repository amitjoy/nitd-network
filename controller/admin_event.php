<?php

$page = "admin_event";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }


// SET RESULT VARIABLE
$result = 0;



// SAVE CHANGES
if($task == "dosave")
{
  $setting['setting_permission_event'] = $_POST['setting_permission_event'];

  // SAVE CHANGES
  $sql = "UPDATE se_settings SET setting_permission_event='{$setting[setting_permission_event]}'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");

  $result = 1;
}



// GET TABS AND FIELDS
$field = new se_field("event");
$field->cat_list();
$cat_array = $field->cats;



// ASSIGN VARIABLES AND SHOW GENERAL SETTINGS PAGE
$smarty->assign('result', $result);
$smarty->assign_by_ref('cats', $cat_array);
include "admin_footer.php";
?>