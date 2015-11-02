<?php

$page = "admin_lostpass_reset";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['r'])) { $r = $_POST['r']; } elseif(isset($_GET['r'])) { $r = $_GET['r']; } else { $r = ""; }
if(isset($_POST['admin_id'])) { $admin_id = $_POST['admin_id']; } elseif(isset($_GET['admin_id'])) { $admin_id = $_GET['admin_id']; } else { $admin_id = ""; }

// DISPLAY PASSWORD REQUEST FORM
$submitted = 0;
$valid = 0;
$is_error = 0;

// ASSIGN USER SETTINGS
$owner = new se_admin($admin_id);

// CHECK VALIDITY OF OWNER
if( !$owner->admin_exists )
{
  $is_error = 1;
}
elseif( $owner->admin_info['admin_lostpassword_code'] != $r || !trim($owner->admin_info['admin_lostpassword_code']) )
{
  $is_error = 1;
}
elseif( $owner->admin_info['admin_lostpassword_time'] < (time()-86400) )
{
  $is_error = 1;
}
else
{
  $valid = 1;
}


if($task == "reset" & $valid == 1)
{
  $admin_password = $_POST['admin_password'];
  $admin_password2 = $_POST['admin_password2'];
  $submitted = 1;

  // CHECK FOR BLANK FIELDS
  if(trim($admin_password) == "" || trim($admin_password2) == "") {
    $is_error = 51;
  }

  // CHECK FOR INVALID PASSWORD
  if(preg_match("/[^a-zA-Z0-9]/", $admin_password)) { 
    $is_error = 52;
  }

  // CHECK FOR PASSWORD LENGTH
  if(trim($admin_password) != "" && strlen($admin_password) < 6) { 
    $is_error = 53;
  }
	
  // CHECK FOR PASSWORD MATCH
  if(trim($admin_password) != "" && $admin_password != $admin_password2) { 
    $is_error = 54;
  }

  // IF THERE WAS NO ERROR, SAVE CHANGES
  if( !$is_error )
  {
    // ENCRYPT NEW PASSWORD WITH MD5
    $password_new_crypt = $owner->admin_password_crypt($admin_password);
    
    // SAVE NEW PASSWORD
    $database->database_query("UPDATE se_admins SET admin_password='{$password_new_crypt}', admin_lostpassword_code='', admin_lostpassword_time='' WHERE admin_id='{$owner->admin_info['admin_id']}' LIMIT 1");
  }
  else
  {
    $submitted = 0;
  }
}




// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('submitted', $submitted);
$smarty->assign('valid', $valid);
$smarty->assign('is_error', $is_error);
$smarty->assign('r', $r);
$smarty->assign('admin_id', $admin_id);
include "admin_footer.php";
?>