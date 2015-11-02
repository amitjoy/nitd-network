<?php

$page = "user_account";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }

// SET RESULT VARIABLES
$result = 0;
$is_error = 0;

// GET USER SETTINGS
$user->user_settings();

// GET NOTIFICATIONS
$notifytypes = Array();
$notifytype_query = $database->database_query("SELECT notifytype_id, notifytype_title, notifytype_name FROM se_notifytypes");
while( $notifytype_info = $database->database_fetch_assoc($notifytype_query) )
{
  // Ignore notify types that are missing a corresponding usersetting column
  $usersetting_notifytype = "usersetting_notify_".$notifytype_info['notifytype_name'];
  if( !array_key_exists($usersetting_notifytype, $user->usersetting_info) ) continue;
  SE_Language::_preload($notifytype_info['notifytype_title']);
  $notifytypes[] = $notifytype_info;
}


// SAVE ACCOUNT SETTINGS
if($task == "dosave")
{
  $user_email = $_POST['user_email'];
  $user_username = $_POST['user_username'];
  $user_timezone = $_POST['user_timezone'];
  $user_profilecat_id = $_POST['user_profilecat_id'];
  $notifications = $_POST['notifications'];

  // GET NOTIFICATIONS
  $usersettings = Array();
  foreach( $notifytypes as $notifytype )
  {
    // Ignore notify types that are missing a corresponding usersetting column
    $usersetting_notifytype = "usersetting_notify_".$notifytype['notifytype_name'];
    if( !array_key_exists($usersetting_notifytype, $user->usersetting_info) ) continue;
    // Update current setting and add to lists
    $user->usersetting_info[$usersetting_notifytype] = $notifications[$notifytype['notifytype_name']] = !empty($notifications[$notifytype['notifytype_name']]);
    $usersettings[] = "{$usersetting_notifytype}='{$notifications[$notifytype['notifytype_name']]}'";
    
  }

  // CHECK IF USER CAN CHANGE USERNAME, OR IF USERNAMES NOT ALLOWED
  if( !$user->level_info['level_profile_change'] || !$setting['setting_username'] ) { $user_username = $user->user_info['user_username']; }

  // VALIDATE ACCOUNT INFO
  $user->user_account($user_email, $user_username);
  $is_error = $user->is_error;

  // ENSURE PROFILE CATEGORY EXISTS
  if( !is_numeric($user_profilecat_id) ) { $user_profilecat_id = $user->user_info['user_profilecat_id']; }

  // SAVE NEW ACCOUNT SETTINGS IF THERE WAS NO ERROR
  if( !$is_error )
  {
    // SET SUBNETWORK
    $subnet = $user->user_subnet_select($user_email, $user_profilecat_id, $user->profile_info); 
    if($subnet[0] != $user->user_info['user_subnet_id']) { $new_subnet_id = $subnet[0]; } else { $new_subnet_id = $user->user_info['user_subnet_id']; }
    
    // USER MUST VERIFY THEIR EMAIL
    if( $setting['setting_signup_verify'] != 0 && $user_email != $user->user_info['user_email'] )
    {
      $verify_code = md5($user->user_info['user_code']);
      $verify_link = $url->url_base."signup_verify.php?u=".$user->user_info['user_id']."&verify=$verify_code&d=".time();
      send_systememail('verification', $user_email, Array($user->user_displayname, $user_email, "<a href=\"$verify_link\">$verify_link</a>")); 
      $user_newemail = $user_email;
      $user_email = $user->user_info['user_email'];
      $subnet_id = $user->user_info['user_subnet_id'];
      if($new_subnet_id != $user->user_info['user_subnet_id']) {
        $result = 817;
      } else {
        $result = 816;
      }
    }
    
    // USER DOESN'T NEED TO VERIFY THEIR EMAIL
    else
    {
      $user_email = $user_email;
      $user_newemail = $user_email;
      $subnet_id = $new_subnet_id;
      if($new_subnet_id != $user->user_info['user_subnet_id'])
      {
        $result = 819;
      } else {
        $result = 191;
      }
    }
    
    // UPDATE DATABASE
    $database->database_query("UPDATE se_users SET user_subnet_id='$subnet_id', user_email='$user_email', user_newemail='$user_newemail', user_username='$user_username', user_timezone='$user_timezone', user_profilecat_id='$user_profilecat_id' WHERE user_id='{$user->user_info['user_id']}' LIMIT 1");
    if( count($usersettings)>0 )
    {
      $database->database_query("UPDATE se_usersettings SET ".implode(", ", $usersettings)." WHERE usersetting_user_id='{$user->user_info['user_id']}' LIMIT 1");
      
      // Flush cached usersettings
      $usersettings_static =& SEUser::getUserSettings($user->user_info['user_id']);
      $usersettings_static = NULL;
      
      $cache_object = SECache::getInstance();
      if( is_object($cache_object) )
      {
        $cache_object->remove('site_user_settings_'.$user->user_info['user_id']);
      }
    }
    
    // IF USERNAME HAS CHANGED, DELETE OLD RECENT ACTIVITY
    if($user->user_info['user_username'] != $user_username) { $database->database_query("DELETE FROM se_actions WHERE action_user_id='{$user->user_info['user_id']}'"); }
    
    // RESET USER INFO
    $user = new se_user(Array($user->user_info['user_id']));
    
    // UPDATE COOKIES
    $user->user_setcookies();
  }
}


// GET PROFILE CATEGORIES
$field = new se_field("profile");
$field->cat_list(0, 0, 0, "profilecat_signup='1' || profilecat_id='{$user->user_info['user_profilecat_id']}'", "profilecat_id='0'", "");

// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);
$smarty->assign('cats', $field->cats);
$smarty->assign('notifytypes', $notifytypes);
$smarty->assign('old_subnet_name', $subnet[2]);
$smarty->assign('new_subnet_name', $subnet[1]);
include "footer.php";
?>