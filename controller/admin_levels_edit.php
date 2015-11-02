<?php

$page = "admin_levels_edit";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['level_id'])) { $level_id = $_POST['level_id']; } elseif(isset($_GET['level_id'])) { $level_id = $_GET['level_id']; } else { $level_id = 0; }

// VALIDATE LEVEL ID
$level = $database->database_query("SELECT * FROM se_levels WHERE level_id='$level_id'");
if($database->database_num_rows($level) != 1) { header("Location: admin_levels.php"); exit(); }
$level_info = $database->database_fetch_assoc($level);

// SET ERROR VARIABLES
$result = 0;


// CREATE USER LEVEL
if($task == "editlevel") {
  $level_info[level_name] = $_POST['level_name'];
  $level_info[level_desc] = $_POST['level_desc'];

  // MAKE SURE USER LEVEL HAS A NAME
  if(trim($level_info[level_name]) != "") {
    $database->database_query("UPDATE se_levels SET level_name='$level_info[level_name]', level_desc='$level_info[level_desc]' WHERE level_id='$level_id'");
    $result = 1;
  }

}



// ASSIGN VARIABLES AND SHOW ADMIN ADD USER LEVEL PAGE
$smarty->assign('result', $result);
$smarty->assign('level_info', $level_info);
include "admin_footer.php";
?>