<?php

$page = "signup_verify";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['verify'])) { $verify = $_POST['verify']; } elseif(isset($_GET['verify'])) { $verify = $_GET['verify']; } else { $verify = ""; }
if(isset($_POST['u'])) { $u = $_POST['u']; } elseif(isset($_GET['u'])) { $u = $_GET['u']; } else { $u = 0; }

// SET ERROR VARS
$is_error = 0;
$result = 0;

// SET RESEND FOR APPROPRIATE TASK
if($task == "resend") { $resend = 1; } else { $resend = 0; }

// IF VERIFICATIONS ARE TURNED OFF, RETURN TO HOME
if($setting['setting_signup_verify'] == 0) { header("Location: home.php"); exit(); }


// RESEND EMAIL
if($task == "resend_do")
{
  $resend = 1;
  $resend_email = $_POST['resend_email'];
  $user_query = $database->database_query("SELECT user_id, user_email, user_code, user_username, user_fname, user_lname, user_verified, user_newemail FROM se_users WHERE user_newemail='$resend_email'");

  // VERIFY USER EXISTS
  if($database->database_num_rows($user_query) != 1)
  {
    $is_error = 1038;
    $user_info['user_code'] = "";
    $user_info['user_email'] = "";
    $user_info['user_newemail'] = "";
    $user_info['user_verified'] = "";
  }
  else
  {
    $user_info = $database->database_fetch_assoc($user_query);
    $thisuser = new se_user();
    $thisuser->user_exists = 1;
    $thisuser->user_info['user_id'] = $user_info['user_id'];
    $thisuser->user_info['user_username'] = $user_info['user_username'];
    $thisuser->user_info['user_fname'] = $user_info['user_fname'];
    $thisuser->user_info['user_lname'] = $user_info['user_lname'];
    $thisuser->user_info['user_email'] = $user_info['user_email'];
    $thisuser->user_info['user_newemail'] = $user_info['user_newemail'];
    $thisuser->user_info['user_code'] = $user_info['user_code'];
    $thisuser->user_displayname();
  }

  // VERIFY USER IS NOT ALREADY VERIFIED  
  if($user_info['user_verified'] == 1 && $user_info['user_email'] == $user_info['user_newemail']) { $is_error = 1044; }

  // NO ERROR, RESEND EMAIL
  if($is_error == 0)
  {
    $verify_code = md5($thisuser->user_info['user_code']);
    $time = time();
    $verify_link = $url->url_base."signup_verify.php?u={$thisuser->user_info['user_id']}&verify={$verify_code}&d={$time}";
    send_systememail('verification', $thisuser->user_info['user_newemail'], Array($thisuser->user_displayname, $thisuser->user_info['user_newemail'], "<a href=\"$verify_link\">$verify_link</a>")); 
    $result = 1042; 
  }
}

// CHECK VERIFICATION
elseif($resend != 1)
{
  // VALIDATE USER ID
  $new_user = new se_user(Array($u));
  if($new_user->user_exists == 0) { $is_error = 1039; }

  // ENSURE NEW EMAIL NOT ALREADY TAKEN
  if($database->database_num_rows($database->database_query("SELECT user_id FROM se_users WHERE user_email='{$new_user->user_info['user_newemail']}' AND user_id<>'{$new_user->user_info['user_id']}'")) != 0)
  {
    $is_error = 1037;
  }

  // CHECK VERIFICATION URL
  if(md5($new_user->user_info['user_code']) !== $verify) { $is_error = 1039; }

  // VERIFY EMAIL ADDRESS IF NO ERROR
  if($is_error == 0)
  {  
    // SET SUBNETWORK
    $subnet = $new_user->user_subnet_select($new_user->user_info['user_newemail'], $new_user->user_info['user_profilecat_id'], $new_user->profile_info); 
    if($subnet[0] != $new_user->user_info['user_subnet_id'])
    {
      $new_subnet_id = $subnet[0];
      $result = 1041;
    }
    else
    {
      $new_subnet_id = $new_user->user_info['user_subnet_id'];
      $result = 1028;
    }
    
    $database->database_query("UPDATE se_users SET user_subnet_id='{$new_subnet_id}', user_verified='1', user_email='{$new_user->user_info['user_newemail']}' WHERE user_id='{$new_user->user_info['user_id']}'");
   
    // IF USER JUST SIGNED UP
    if( !$new_user->user_info['user_verified'] )
    {
      // SEND WELCOME EMAIL
      send_systememail('welcome', $new_user->user_info['user_newemail'], Array($new_user->user_displayname, $new_user->user_info['user_newemail'], '', "<a href=\"".$url->url_base."login.php\">".$url->url_base."login.php</a>"));
      
      // INSERT ACTION (IF VERIFICATION REQUIRED)
      $actions->actions_add($new_user, "signup", Array($new_user->user_info['user_username'], $new_user->user_displayname), Array(), 0, false, "user", $new_user->user_info['user_id'], $new_user->user_info['user_privacy']);
    }
  }
}




// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('is_error', $is_error);
$smarty->assign('resend', $resend);
$smarty->assign('result', $result);
$smarty->assign('old_subnet_name', $subnet[2]);
$smarty->assign('new_subnet_name', $subnet[1]);
include "footer.php";
?>