<?
$page = "admin_levels_educationsettings";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = "main"; }
if(isset($_POST['level_id'])) { $level_id = $_POST['level_id']; } elseif(isset($_GET['level_id'])) { $level_id = $_GET['level_id']; } else { $level_id = 0; }

// VALIDATE LEVEL ID
$level = $database->database_query("SELECT * FROM se_levels WHERE level_id='$level_id'");
if($database->database_num_rows($level) != 1) { 
  header("Location: admin_levels.php");
  exit();
}
$level_info = $database->database_fetch_assoc($level);


// SET RESULT AND ERROR VARS
$result = 0;
$is_error = 0;

// SAVE CHANGES
if($task == "dosave") {
  $level_education_allow = $_POST['level_education_allow'] ? 1 : 0;

  // IF THERE WERE NO ERRORS, SAVE CHANGES
  if($is_error == 0) {
    $database->database_query("UPDATE se_levels SET level_education_allow='$level_education_allow' WHERE level_id='$level_info[level_id]'");
    $level_info = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_levels WHERE level_id='$level_info[level_id]'"));
    $result = 1;
  }
}


// ASSIGN VARIABLES AND SHOW USER EVENTS PAGE
$smarty->assign('level_info', $level_info);

$smarty->assign('level_id', $level_info[level_id]);
$smarty->assign('level_name', $level_info[level_name]);
$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);
$smarty->assign('error_message', $error_message);
$smarty->assign('education_allow', $level_info[level_education_allow]);
include "admin_footer.php";
