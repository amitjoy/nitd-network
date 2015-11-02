<?php
$page = "admin_levels_chatsettings";
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

// SET RESULT VARIABLE
$result = 0;
$is_error = 0;
$error_message = "";


// SAVE CHANGES

if($task == "dosave")
{
  $level_chat_allow = ( !empty($_POST['level_chat_allow'])  && $_POST['level_chat_allow'] ? '1' : '0' );
  $level_im_allow   = ( !empty($_POST['level_im_allow'])    && $_POST['level_im_allow']   ? '1' : '0' );
  
  // Do update
  $sql = "
    UPDATE
      se_levels
    SET 
			level_chat_allow='$level_chat_allow',
			level_im_allow='$level_im_allow'
    WHERE
      level_id='$level_id'
  ";
  
  $database->database_query($sql) or die($database->database_error());
  
  // Get new data
  $level_info = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_levels WHERE level_id='$level_id'"));
  $result = 1;
}

// END DOSAVE TASK



// ASSIGN VARIABLES AND SHOW ALBUM SETTINGS PAGE
$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);
$smarty->assign('error_message', $error_message);

$smarty->assign('level_id', $level_info[level_id]);
$smarty->assign('level_name', $level_info[level_name]);
$smarty->assign_by_ref('level_info', $level_info);
include "admin_footer.php";
?>