<?php

$page = "admin_signup";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = "main"; }


// SET RESULT VARIABLE
$result = 0;


// SAVE CHANGES
if($task == "dosave") {
  $setting[setting_signup_photo] = $_POST['setting_signup_photo'];
  $setting[setting_signup_enable] = $_POST['setting_signup_enable'];
  $setting[setting_signup_welcome] = $_POST['setting_signup_welcome'];
  $setting[setting_signup_invite] = $_POST['setting_signup_invite'];
  $setting[setting_signup_invite_checkemail] = $_POST['setting_signup_invite_checkemail'];
  $setting[setting_signup_invite_numgiven] = $_POST['setting_signup_invite_numgiven'];
  $setting[setting_signup_invitepage] = $_POST['setting_signup_invitepage'];
  $setting[setting_signup_verify] = $_POST['setting_signup_verify'];
  $setting[setting_signup_code] = $_POST['setting_signup_code'];
  $setting[setting_signup_randpass] = $_POST['setting_signup_randpass'];
  $setting[setting_signup_tos] = $_POST['setting_signup_tos'];
  $setting[setting_signup_tostext] = $_POST['setting_signup_tostext'];

  $field_signup = $_POST['field_signup'];
  if(is_array($field_signup)) { 
    $database->database_query("UPDATE se_profilefields SET profilefield_signup='1' WHERE profilefield_id IN('".join("', '", $field_signup)."')");
    $database->database_query("UPDATE se_profilefields SET profilefield_signup='0' WHERE profilefield_id NOT IN('".join("', '", $field_signup)."')");
  }


  $cat_signup = $_POST['cat_signup'];
  if(is_array($cat_signup)) { 
    $database->database_query("UPDATE se_profilecats SET profilecat_signup='1' WHERE profilecat_id IN('".join("', '", $cat_signup)."')");
    $database->database_query("UPDATE se_profilecats SET profilecat_signup='0' WHERE profilecat_id NOT IN('".join("', '", $cat_signup)."')");
  }

  // UPDATE TOS TEXT
  SE_Language::edit(1210, $setting[setting_signup_tostext]);

  // UPDATE SETTINGS
  $database->database_query("UPDATE se_settings SET 
			setting_signup_photo='$setting[setting_signup_photo]',
			setting_signup_enable='$setting[setting_signup_enable]',
			setting_signup_welcome='$setting[setting_signup_welcome]',
			setting_signup_invite='$setting[setting_signup_invite]',
			setting_signup_invite_checkemail='$setting[setting_signup_invite_checkemail]',
			setting_signup_invite_numgiven='$setting[setting_signup_invite_numgiven]',
			setting_signup_invitepage='$setting[setting_signup_invitepage]',
			setting_signup_verify='$setting[setting_signup_verify]',
			setting_signup_code='$setting[setting_signup_code]',
			setting_signup_randpass='$setting[setting_signup_randpass]',
			setting_signup_tos='$setting[setting_signup_tos]'");
  $result = 1;
}








// GET TABS AND FIELDS
$field = new se_field("profile");
$field->cat_list();
$cat_array = $field->cats;


// ASSIGN VARIABLES AND SHOW ADMIN SIGNUP PAGE
$smarty->assign('result', $result);
$smarty->assign('cats', $cat_array);
include "admin_footer.php";
?>