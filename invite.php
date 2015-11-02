<?php

$page = "invite";
include "header.php";

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if($user->user_exists == 0 && $setting[setting_permission_invite] == 0) {
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }

// SET EMPTY VARS
$is_error = 0;
$result = 0;

// CHECK IF INVITE CODES SET TO ADMINS ONLY
if($setting['setting_signup_invite'] == 1) { header("Location: user_home.php"); exit(); }


// SEND INVITATIONS
if($task == "doinvite")
{
  $invite_emails = $_POST['invite_emails'];
  $invite_message = $_POST['invite_message'];

  // CHECK FOR SECURITY CODE
  if( $setting['setting_invite_code'] )
  {
    // NOW IN HEADER
    //session_start();
    $code = $_SESSION['code'];
    if($code == "") { $code = randomcode(); }
    $invite_secure = $_POST['invite_secure'];
    if($invite_secure != $code) { $is_error = 708; }
  }

  // CHECK FOR NO INVITE EMAILS
  if(trim($invite_emails) == "") { $is_error = 1073; }

  // SEND INVITATION IF NO ERROR
  if($is_error == 0)
  {
    $invite_emails = implode(",", array_slice(explode(",", $invite_emails), 0, 10));
    
    
    // NO INVITE CODE REQUIRED
    if($setting['setting_signup_invite'] == 0)
    {
      send_systememail('invite', $invite_emails, Array($user->user_displayname, $user->user_info['user_email'], $invite_message, "<a href=\"".$url->url_base."signup.php\">".$url->url_base."signup.php</a>"), TRUE);
    }
    
    // INVITE CODE REQUIRED
    else
    {
      // LOOP OVER EMAILS
      $invites_left = $user->user_info['user_invitesleft'];
      $invite_emails_array = explode(",", $invite_emails);
      for($e=0;$e<count($invite_emails_array);$e++)
      {
        $email = trim($invite_emails_array[$e]);
        if($email != "" && $invites_left > 0)
        {
          // CREATE CODE, INSERT INTO DATABASE, AND SEND EMAIL
          $invite_code = randomcode();
          $database->database_query("INSERT INTO se_invites (invite_user_id, invite_date, invite_email, invite_code) VALUES ('{$user->user_info['user_id']}', '".time()."', '$email', '$invite_code')");
          send_systememail('invitecode', $email, Array($user->user_displayname, $user->user_info['user_email'], $invite_message, $invite_code, "<a href=\"".$url->url_base."signup.php?signup_email=$email&signup_invite=$invite_code\">".$url->url_base."signup.php?signup_email=$email&signup_invite=$invite_code</a>"));
          $invites_left--;
        }
      }
      
      $database->database_query("UPDATE se_users SET user_invitesleft='$invites_left' WHERE user_id='{$user->user_info['user_id']}'");
      $user->user_info['user_invitesleft'] = $invites_left;
    }
    
    $invite_emails = "";
    $invite_message = "";
    $result = 341;
  }
}

// SET GLOBAL PAGE TITLE
$global_page_title[0] = 1074;
$global_page_description[0] = 1075;


// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);
$smarty->assign('invite_emails', $invite_emails);
$smarty->assign('invite_message', $invite_message);
include "footer.php";
?>