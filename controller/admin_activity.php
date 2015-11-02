<?php

$page = "admin_activity";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }


// SET RESULT VARIABLE
$result = 0;


// SAVE RESULTS
if($task == "dosave") {
  $setting[setting_actions_showlength] = $_POST['setting_actions_showlength'];
  $setting[setting_actions_actionsperuser] = $_POST['setting_actions_actionsperuser'];
  $setting[setting_actions_selfdelete] = $_POST['setting_actions_selfdelete'];
  $setting[setting_actions_privacy] = $_POST['setting_actions_privacy'];
  $setting[setting_actions_visibility] = $_POST['setting_actions_visibility'];
  $setting[setting_actions_preference] = $_POST['setting_actions_preference'];
  $setting[setting_actions_actionsonprofile] = $_POST['setting_actions_actionsonprofile'];
  $setting[setting_actions_actionsinlist] = $_POST['setting_actions_actionsinlist'];
  $setting[setting_actions_privacy] = $_POST['setting_actions_privacy'];

  // GET ACTION TYPES
  $current_language = SE_Language::info("language_id");
  $actiontype_text = $_POST['actiontype_text'];
  $actiontype_enabled = $_POST['actiontype_enabled'];
  $actiontype_setting = $_POST['actiontype_setting'];
  $actiontypes = $database->database_query("SELECT * FROM se_actiontypes ORDER BY actiontype_id ASC");
  while($actiontype = $database->database_fetch_assoc($actiontypes)) {
    $text = htmlspecialchars_decode($actiontype_text[$actiontype[actiontype_id]], ENT_QUOTES);
    $database->database_query("UPDATE se_actiontypes SET actiontype_enabled='".$actiontype_enabled[$actiontype[actiontype_id]]."', actiontype_setting='".$actiontype_setting[$actiontype[actiontype_id]]."' WHERE actiontype_id='$actiontype[actiontype_id]'");
    $vars = explode(",", $actiontype[actiontype_vars]);
    for($i=0;$i<count($vars);$i++) { $text = str_replace($vars[$i], "%".($i+1)."\$s", $text); }
    SE_Language::edit($actiontype[actiontype_text], $text);
  }

  // SAVE SETTINGS
  $database->database_query("UPDATE se_settings SET setting_actions_showlength='$setting[setting_actions_showlength]', 
						    setting_actions_actionsperuser='$setting[setting_actions_actionsperuser]', 
						    setting_actions_selfdelete='$setting[setting_actions_selfdelete]', 
						    setting_actions_privacy='$setting[setting_actions_privacy]', 
						    setting_actions_visibility='$setting[setting_actions_visibility]', 
						    setting_actions_preference='$setting[setting_actions_preference]', 
						    setting_actions_actionsonprofile='$setting[setting_actions_actionsonprofile]',
						    setting_actions_actionsinlist='$setting[setting_actions_actionsinlist]'");
  $result = 1;
}




// GET ACTION TYPES
$actiontypes = $database->database_query("SELECT * FROM se_actiontypes ORDER BY actiontype_id ASC");
$actiontype_array = Array();
while($actiontype = $database->database_fetch_assoc($actiontypes)) {
  SE_Language::_preload($actiontype[actiontype_text]);
  $actiontype_array[] = Array('actiontype_id' => $actiontype[actiontype_id],
				'actiontype_name' => $actiontype[actiontype_name],
				'actiontype_text' => $actiontype[actiontype_text],
				'actiontype_enabled' => $actiontype[actiontype_enabled],
				'actiontype_setting' => $actiontype[actiontype_setting],
				'actiontype_media' => $actiontype[actiontype_media],
				'actiontype_vars' => implode(",", array_filter(explode(",",$actiontype[actiontype_vars]))),
				'actiontype_vars_array' => explode(",", $actiontype[actiontype_vars]));
}




// ASSIGN VARIABLES AND SHOW LOG PAGE
$smarty->assign('result', $result);
$smarty->assign('actiontypes', $actiontype_array);
$smarty->assign('actiontypes_total', $actiontype_count);
include "admin_footer.php";
?>