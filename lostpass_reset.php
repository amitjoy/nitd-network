<?php

$page = "lostpass_reset";
include "header.php";

$task = ( isset($_POST['task']) ? $_POST['task'] : ( isset($_GET['task']) ? $_GET['task'] : NULL ) );
$r = ( isset($_POST['r']) ? $_POST['r'] : ( isset($_GET['r']) ? $_GET['r'] : NULL ) );

// DISPLAY PASSWORD REQUEST FORM
$submitted = 0;
$valid = 0;
$is_error = 0;

// ASSIGN USER SETTINGS
$owner->user_settings();

// CHECK VALIDITY OF OWNER AND CODE
if( !$owner->user_exists )
{
  $is_error = 750;
}
elseif( $owner->usersetting_info['usersetting_lostpassword_code'] != $r || !trim($owner->usersetting_info['usersetting_lostpassword_code']) )
{
  $is_error = 750;
}
elseif( $owner->usersetting_info['usersetting_lostpassword_time'] < (time()-86400) )
{
  $is_error = 750;
}
else
{
  $valid = 1;
}


// LINK IS VALID, RESET PASSWORD
if($task == "reset" && $valid == 1)
{
  $user_password = $_POST['user_password'];
  $user_password2 = $_POST['user_password2'];
  $submitted = 1;
  
  // CHECK PASSWORD
  $owner->user_password('', $user_password, $user_password2, 0);
  $is_error = $owner->is_error;
  
  // IF THERE WAS NO ERROR, SAVE CHANGES
  if($is_error == 0)
  {
    // ENCRYPT NEW PASSWORD WITH MD5
    $password_new_crypt = $owner->user_password_crypt($user_password);

    // SAVE NEW PASSWORD
    $database->database_query("UPDATE se_users SET user_password='$password_new_crypt' WHERE user_id='{$owner->user_info['user_id']}' LIMIT 1");
    $database->database_query("UPDATE se_usersettings SET usersetting_lostpassword_code='', usersetting_lostpassword_time='0' WHERE usersetting_user_id='{$owner->user_info['user_id']}' LIMIT 1");
  }
  else
  {
    $submitted = 0;
  }
}


// SET GLOBAL PAGE TITLE
$global_page_title[0] = 43;
$global_page_description[0] = 45;


// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('submitted', $submitted);
$smarty->assign('valid', $valid);
$smarty->assign('is_error', $is_error);
$smarty->assign('r', $r);
include "footer.php";
?>