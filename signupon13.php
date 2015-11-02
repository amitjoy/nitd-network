<?php

$page = "signup";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = "step1"; }

// SET ERROR VARS
$is_error = 0;


// IF USER IS ALREADY LOGGED IN, FORWARD TO USER HOME PAGE
if( $user->user_exists )
{
  header("Location: user_home.php");
  exit();
}



// CHECK IF USER SIGNUP COOKIES SET (STEPS 3, 4, 5)
$signup_logged_in = 0;
if($task != "step1" && $task != "step1do" && $task != "step2" && $task != "step2do")
{
  if(isset($_COOKIE['signup_id']) && isset($_COOKIE['signup_email']) && isset($_COOKIE['signup_password']))
  {
    // GET USER ROW IF AVAILABLE
    $user_id = $_COOKIE['signup_id'];
    $new_user = new se_user(Array($user_id));
    
    // VERIFY USER LOGIN COOKIE VALUES AND RESET USER LOGIN VARIABLE
    //if($_COOKIE['signup_email'] == crypt($new_user->user_info['user_email'], "$1$".$new_user->user_info['user_code']."$") && $_COOKIE['signup_password'] == $new_user->user_info['user_password'])
    $new_user->user_salt = $new_user->user_info['user_code'];
    if( $_COOKIE['signup_email'] == $new_user->user_password_crypt($new_user->user_info['user_email']) && $_COOKIE['signup_password'] == $new_user->user_info['user_password'] )
    {
      $signup_logged_in = 1;
    }
  }

  if($signup_logged_in != 1) { cheader("signupon13.php"); exit(); }
}

if($signup_logged_in != 1)
{
  setcookie("signup_id", "", 0, "/");
  setcookie("signup_email", "", 0, "/");
  setcookie("signup_password", "", 0, "/");
  $_COOKIE['signup_id'] = "";
  $_COOKIE['signup_email'] = "";
  $_COOKIE['signup_password'] = "";
  $new_user = new se_user();
  if($task == "step1")
  { 
    if(isset($_GET['signup_email'])) { $signup_email = $_GET['signup_email']; } else { $signup_email = ""; }
    if(isset($_GET['signup_invite'])) { $signup_invite = $_GET['signup_invite']; } 
    $signup_password = ""; 
    $signup_timezone = $setting['setting_timezone'];
  }
}



// PROCESS INPUT FROM FIRST STEP (OR DOUBLE CHECK VALUES), CONTINUE TO SECOND STEP (OR SECOND STEP PROCESSING)
if($task == "step1do" || $task == "step2do")
{
  $signup_email = $_POST['signup_email'];
  $signup_password = $_POST['signup_password'];
  $signup_password2 = $_POST['signup_password2'];
  $step = $_POST['step'];
  
  if($task == "step2do" && $step != "1")
  {
    $signup_password = base64_decode($signup_password);
    $signup_password2 = base64_decode($signup_password2);
  }
  
  $signup_username = $_POST['signup_username'];
  $signup_timezone = $_POST['signup_timezone'];
  $signup_invite = $_POST['signup_invite'];
  $signup_cat = $_POST['signup_cat'];

  // GET LANGUAGE PACK SELECTION
  $signup_lang = ( $setting['setting_lang_allow'] ? $_POST['signup_lang'] : 0 );

  // TEMPORARILY SET PASSWORD IF RANDOM PASSWORD ENABLED
  if($setting['setting_signup_randpass'] != 0)
  {
    $signup_password = "temporary";
    $signup_password2 = "temporary";
  }

  // CHECK USER ERRORS
  $new_user->user_password('', $signup_password, $signup_password2, 0);
  $new_user->user_account($signup_email, $signup_username);
  $is_error = $new_user->is_error;

  // CHECK INVITE CODE IF NECESSARY
  if($setting['setting_signup_invite'] != 0)
  {
    if($setting['setting_signup_invite_checkemail'] != 0)
    {
      $invite = $database->database_query("SELECT invite_id FROM se_invites WHERE invite_code='$signup_invite' AND invite_email='$signup_email'");
      $invite_error_message = 705;
    }
    else
    {
      $invite = $database->database_query("SELECT invite_id FROM se_invites WHERE invite_code='$signup_invite'");
      $invite_error_message = 706;
    }
    if($database->database_num_rows($invite) == 0) { $is_error = $invite_error_message; }
  }

  // CHECK TERMS OF SERVICE AGREEMENT IF NECESSARY
  if($setting['setting_signup_tos'] != 0)
  {
    $signup_agree = $_POST['signup_agree'];
    if($signup_agree != 1)
    {
      $is_error = 707;
    }
  }

  // RETRIEVE AND CHECK SECURITY CODE IF NECESSARY
  if($setting['setting_signup_code'] != 0)
  {
    // NOW IN HEADER
    $signup_secure = $_POST['signup_secure'];
    $code_found = false;
    if( $signup_secure && @$_SESSION['code'] == $signup_secure )
      $code_found = true;

    if( !empty($_SESSION['codes']) && is_array($_SESSION['codes']) )
    {
      foreach( $_SESSION['codes'] as $index => $code_info )
      {
        if( $code_info['code'] == $signup_secure )
        {
          $code_found = true;
          unset($_SESSION['codes'][$index]);
        }
      }
    }
    
    if( !$code_found )
      $is_error = 708;

    //session_start();
    //$code = $_SESSION['code'];
    //if($code == "") { $code = randomcode(); }
    //$signup_secure = $_POST['signup_secure'];
    //
    //if($signup_secure != $code)
    //{
    //  $is_error = 708;
    //}
  }

  // IF THERE IS NO ERROR, CONTINUE TO STEP 2 OR PROCESS STEP 2
  if($is_error == 0)
  {
    // ONLY IF ON STEP ONE, CONTINUE TO STEP 2 - ELSE GO TO PROCESSING STEP 2
    if($task == "step1do") { $task = "step2"; }
  }
  
  // IF THERE WAS AN ERROR, GO BACK TO STEP 1
  else
  {
    $task = "step1";
  }

}












if($task == "step1" || $task == "step1do" || $task == "step2" || $task == "step2do")
{
  if($database->database_num_rows($database->database_query("SELECT NULL FROM se_profilecats WHERE profilecat_id='$signup_cat' AND profilecat_dependency='0'")) != 1)
  {
    $cat_info = $database->database_fetch_assoc($database->database_query("SELECT profilecat_id FROM se_profilecats WHERE profilecat_dependency='0' AND profilecat_signup = 1 ORDER BY profilecat_order LIMIT 1"));
    $signup_cat = $cat_info['profilecat_id'];
  }
  if($task == "step2do") { $validate = 1; } else { $validate = 0; }
  if($task != "step1") { $cat_where = "profilecat_signup='1' AND profilecat_id='$signup_cat'"; } else { $cat_where = "profilecat_signup='1'"; }
  $field = new se_field("profile");
  $field->cat_list($validate, 0, 0, $cat_where, "", "profilefield_signup='1'");
  $cat_array = $field->cats;
  if($task != "step1" && count($cat_array) == 0) { $task = "step1"; }
  if($validate == 1) { $is_error = $field->is_error; }
  if($task != "step1" && count($field->fields_all) == 0) { $task = "step2do"; }
}









if($task == "step2do")
{
  // PROFILE FIELD INPUTS PROCESSED AND CHECKED FOR ERRORS ABOVE
  // IF THERE IS NO ERROR, ADD USER AND USER PROFILE AND CONTINUE TO STEP 3
  if($is_error == 0)
  {
    $new_user->user_create($signup_email, $signup_username, $signup_password, $signup_timezone, $signup_lang, $signup_cat, $field->field_query);
    
    // INVITE CODE FEATURES
    if($setting['setting_signup_invite'] != 0)
    {
      if($setting['setting_signup_invite_checkemail'] != 0)
      {
        $invitation = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_invites WHERE invite_code='$signup_invite' AND invite_email='$signup_email' LIMIT 1"));
      }
      else
      {
        $invitation = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_invites WHERE invite_code='$signup_invite' LIMIT 1"));
      }

      // ADD USER TO INVITER'S FRIENDLIST
      $friend = new se_user(Array($invitation['invite_user_id']));
      if($friend->user_exists == 1)
      {
        if($setting['setting_connection_allow'] == 3 || $setting['setting_connection_allow'] == 1 || ($setting['setting_connection_allow'] == 2 && $new_user->user_info['user_subnet_id'] == $friend->user_info['user_subnet_id']))
        {
          // SET RESULT, DIRECTION, STATUS
          switch($setting['setting_connection_framework'])
          {
            case "0":
              $direction = 2;
              $friend_status = 0;
              break;
            case "1":
              $direction = 1;
              $friend_status = 0;
              break;
            case "2": 
              $direction = 2;
              $friend_status = 1;
              break;
            case "3":
              $direction = 1;
              $friend_status = 1;
              break;      
          } 
          
          // INSERT FRIENDS INTO FRIEND TABLE AND EXPLANATION INTO EXPLAIN TABLE	          
          $friend->user_friend_add($new_user->user_info['user_id'], $friend_status, '', '');
	        
          // IF TWO-WAY CONNECTION AND NON-CONFIRMED, INSERT OTHER DIRECTION
          if($direction == 2 && $friend_status == 1) { $new_user->user_friend_add($friend->user_info['user_id'], $friend_status, '', ''); }
        }
      }
      
      // DELETE INVITE CODE
      $database->database_query("DELETE FROM se_invites WHERE invite_id='$invitation[invite_id]' LIMIT 1");
    }
    
    // SET SIGNUP COOKIE
    $new_user->user_salt = $new_user->user_info['user_code'];
    $id = $new_user->user_info['user_id'];
    $em = $new_user->user_password_crypt($new_user->user_info['user_email']);
    $pass = $new_user->user_info['user_password'];
    setcookie("signup_id", "$id", 0, "/");
    setcookie("signup_email", "$em", 0, "/");
    setcookie("signup_password", "$pass", 0, "/");
    
    // SEND USER TO PHOTO UPLOAD IF SPECIFIED BY ADMIN
    // OR TO USER INVITE IF NO PHOTO UPLOAD
    if( !$setting['setting_signup_photo'] )
    { 
      if( !$setting['setting_signup_invitepage'] )
      {
        $task = "step5";
      }
      else
      {
        $task = "step4"; 
      }
    }
    else
    { 
      $task = "step3"; 
    }
  }
  
  // IF THERE WAS AN ERROR, GO BACK TO STEP 2
  else
  {
    $task = "step2";
  }
}







// UPLOAD PHOTO
if($task == "step3do")
{
  $new_user->user_photo_upload("photo");
  $is_error = $new_user->is_error;
  $task = "step3";
}




// SEND INVITE EMAILS
if($task == "step4do")
{
  $invite_emails = $_POST['invite_emails'];
  $invite_message = $_POST['invite_message'];

  if($invite_emails != "")
  {
    send_systememail('invite', $invite_emails, Array($new_user->user_displayname, $new_user->user_info['user_email'], $invite_message, "<a href=\"".$url->url_base."signupon13.php\">".$url->url_base."signupon13.php</a>"), TRUE);
  }

  // SEND USER TO THANK YOU PAGE
  $task = "step5";
}





// SIGNUP TERMINAL VELOCITY POINT HOOK
($hook = SE_Hook::exists('se_signup_decide')) ? SE_Hook::call($hook, array()) : NULL; 







// SHOW COMPLETION PAGE
if($task == "step5")
{
  // UNSET SIGNUP COOKIES
  setcookie("signup_id", "", 0, "/");
  setcookie("signup_email", "", 0, "/");
  setcookie("signup_password", "", 0, "/");

  // UPDATE SIGNUP STATS
  update_stats("signups");

  // DISPLAY THANK YOU
  $step = 5;
}




// SHOW FOURTH STEP
if($task == "step4")
{
  $step = 4;
  $next_task = "step4do";
  if($setting['setting_signup_invitepage'] == 0) { $task = "step3"; }
}





// SHOW THIRD STEP
if($task == "step3")
{
  $step = 3;
  $next_task = "step3do";
  if($setting['setting_signup_invitepage'] == 0) { $last_task = "step5"; } else { $last_task = "step4"; }
  if($setting['setting_signup_photo'] == 0) { $task = "step2"; }
}





// SHOW SECOND STEP
if($task == "step2")
{
  $step = 2;
  $next_task = "step2do";
  if(count($field->cats) == 0) { $task = "step1"; }
  $signup_password = base64_encode($signup_password);
  $signup_password2 = base64_encode($signup_password2);
}







// SHOW FIRST STEP
if($task == "step1")
{
  $step = 1;
  $next_task = "step1do";

  // GET LANGUAGE PACK LIST
  $lang_packlist = SE_Language::list_packs();
  ksort($lang_packlist);
  $lang_packlist = array_values($lang_packlist);
}






// SET GLOBAL PAGE TITLE
$global_page_title[0] = 679;
$global_page_description[0] = 680;



// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('is_error', $is_error);
$smarty->assign('new_user', $new_user);
$smarty->assign('cats', $field->cats);
$smarty->assign('signup_email', $signup_email);
$smarty->assign('signup_password', $signup_password);
$smarty->assign('signup_password2', $signup_password2);
$smarty->assign('signup_username', $signup_username);
$smarty->assign('signup_timezone', $signup_timezone);
$smarty->assign('signup_lang', $signup_lang);
$smarty->assign('signup_invite', $signup_invite);
$smarty->assign('signup_secure', $signup_secure);
$smarty->assign('signup_agree', $signup_agree);
$smarty->assign('signup_cat', $signup_cat);
$smarty->assign('lang_packlist', $lang_packlist);
$smarty->assign('next_task', $next_task);
$smarty->assign('last_task', $last_task);
$smarty->assign('step', $step);
include "footer.php";
?>
