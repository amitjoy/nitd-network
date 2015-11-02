<?php

$page = "admin_group";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }


// SET RESULT VARIABLE
$result = 0;



// SAVE CHANGES
if($task == "dosave") {
  $setting[setting_permission_group] = $_POST['setting_permission_group'];
  $setting[setting_group_discussion_code] = $_POST['setting_group_discussion_code'];
  $setting[setting_group_discussion_html] = str_replace(" ", "", $_POST['setting_group_discussion_html']);

  // SAVE CHANGES
  $database->database_query("UPDATE se_settings SET 
			setting_permission_group='$setting[setting_permission_group]',
			setting_group_discussion_code = '$setting[setting_group_discussion_code]',
			setting_group_discussion_html = '$setting[setting_group_discussion_html]'");

  $result = 1;
}








// GET TABS AND FIELDS
$field = new se_field("group");
$field->cat_list();
$cat_array = $field->cats;





// ASSIGN VARIABLES AND SHOW GENERAL SETTINGS PAGE
$smarty->assign('result', $result);
$smarty->assign('cats', $cat_array);
include "admin_footer.php";
?>