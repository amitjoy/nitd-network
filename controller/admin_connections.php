<?php

$page = "admin_connections";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = "main"; }


// SET RESULT VARIABLE
$result = 0;


// SAVE CHANGES
if($task == "dosave") {
  $setting[setting_connection_allow] = $_POST['setting_connection_allow'];
  $setting[setting_connection_framework] = $_POST['setting_connection_framework'];
  $setting[setting_connection_other] = $_POST['setting_connection_other'];
  $setting[setting_connection_explain] = $_POST['setting_connection_explain'];

  $types_new = array();
  $friendtypes = $_POST['setting_connection_types'];
  for($t=0;$t<count($friendtypes);$t++) {
    if(trim($friendtypes[$t]) != "") {
      $types_new[] = $friendtypes[$t];
    }
  }
  $setting[setting_connection_types] = implode("<!>", $types_new);

  $database->database_query("UPDATE se_settings SET 
			setting_connection_allow='$setting[setting_connection_allow]', 
			setting_connection_framework='$setting[setting_connection_framework]',
			setting_connection_types='$setting[setting_connection_types]',
			setting_connection_other='$setting[setting_connection_other]',
			setting_connection_explain='$setting[setting_connection_explain]'");

  // UPDATE ALL USER FRIENDSHIPS - CONFIRM UNVERIFIED FRIENDSHIPS IF ADMIN SETS TO UNVERIFIED
  if($setting[setting_connection_framework] == "2" || $setting[setting_connection_framework] == "3") {
    $database->database_query("UPDATE se_friends SET friend_status='1'");
  }

  $result = 1;
}






$friendtypes = explode("<!>", trim($setting[setting_connection_types]));
for($t=0;$t<count($friendtypes);$t++) {
  if(trim($friendtypes[$t]) != "") {
    $types[] = $friendtypes[$t];
  }
}



// ADD ADDITIONAL BLANK OPTION IF NO FRIENDTYPES EXIST
if(count($types) == 0) { $types = Array('0' => ''); }






// ASSIGN VARIABLES AND SHOW CONNECTIONS PAGE
$smarty->assign('result', $result);
$smarty->assign('types', $types);
include "admin_footer.php";
?>