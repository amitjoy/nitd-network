<?php

$page = "login";
include "header.php";

// USER IS LOGGED IN, FORWARD TO USER HOME
if( $user->user_exists )
{
  header("Location: user_home.php");
  exit();
}

$task = ( isset($_POST['task']) ? $_POST['task'] : NULL );

// CHECK FOR REDIRECTION URL
if(isset($_POST['return_url'])) { $return_url = $_POST['return_url']; } elseif(isset($_GET['return_url'])) { $return_url = $_GET['return_url']; } else { $return_url = ""; }
$return_url = urldecode($return_url);
$return_url = str_replace("&amp;", "&", $return_url);
if($return_url == "") { $return_url = "user_home.php"; }

// INITIALIZE ERROR VARS
$is_error = 0;

if( !isset($_SESSION['failed_login_count']) )
  $failed_login_count = $_SESSION['failed_login_count'] = 0;
else
  $failed_login_count = $_SESSION['failed_login_count'];


// GET EMAIL
if(isset($_POST['email'])) { $email = $_POST['email']; } elseif(isset($_GET['email'])) { $email = $_GET['email']; } else { $email = ""; }


// TRY TO LOGIN
if($task == "dologin")
{
  $user->is_error = FALSE;
  
  if( !empty($setting['setting_login_code']) || (!empty($setting['setting_login_code_failedcount']) && $_SESSION['failed_login_count']>=$setting['setting_login_code_failedcount']) )
  {
    $code = $_SESSION['code'];
    if($code == "") { $code = randomcode(); }
    $login_secure = $_POST['login_secure'];
    
    if($login_secure != $code)
      $user->is_error = 708;
  }
  
  if( !$user->is_error )
    $user->user_login($email, $_POST['password'], $_POST['javascript_disabled'], $_POST['persistent']);

  // IF USER IS LOGGED IN SUCCESSFULLY, FORWARD THEM TO SPECIFIED URL
  if( !$user->is_error )
  {
    $failed_login_count = $_SESSION['failed_login_count'] = 0;
    
    // INSERT ACTION 
    $actions->actions_add($user, "login", Array($user->user_info['user_username'], $user->user_displayname), Array(), 0, false, "user", $user->user_info['user_id'], $user->user_info['user_privacy']);

    // CALL LOGIN HOOK
    ($hook = SE_Hook::exists('se_login_success')) ? SE_Hook::call($hook, array()) : NULL;

    cheader("$return_url");
    exit();
  }
  
  // IF THERE WAS AN ERROR, SET ERROR MESSAGE
  else
  {
    $failed_login_count = ++$_SESSION['failed_login_count'];
    
    $is_error = $user->is_error;
    $user = new se_user();
  }
}


// SET GLOBAL PAGE TITLE
$global_page_title[0] = 658;
$global_page_description[0] = 673;


// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('email', $email);
$smarty->assign('is_error', $is_error);
$smarty->assign('return_url', $return_url);
$smarty->assign('failed_login_count', $failed_login_count);
include "footer.php";
?>