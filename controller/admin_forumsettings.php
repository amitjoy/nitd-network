<?php
$page = "admin_forumsettings";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }


// SET RESULT VARIABLE
$result = 0;



// SAVE CHANGES
if($task == "dosave") {

  $setting[setting_permission_forum] = $_POST['setting_permission_forum'];
  $setting[setting_forum_code] = $_POST['setting_forum_code'];
  $modprivs = (is_array($_POST['setting_forum_modprivs']))?$_POST['setting_forum_modprivs']:Array();
  $setting[setting_forum_status] = $_POST['setting_forum_status'];

  $setting[setting_forum_modprivs] = "";
  for($i=0;$i<5;$i++) {
    if($modprivs[$i] == 1) { $setting[setting_forum_modprivs] .= '1'; } else { $setting[setting_forum_modprivs] .= '0'; }
  }

  // SAVE CHANGES
  $database->database_query("UPDATE se_settings SET 
			setting_permission_forum='$setting[setting_permission_forum]',
			setting_forum_code = '$setting[setting_forum_code]',
			setting_forum_status = '$setting[setting_forum_status]',
			setting_forum_modprivs = '$setting[setting_forum_modprivs]'");

  $result = 1;
}









// ASSIGN VARIABLES AND SHOW FORUM SETTINGS PAGE
$smarty->assign('result', $result);
include "admin_footer.php";
?>