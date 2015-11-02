<?php

$page = "help_contact";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }


// SET DEFAULTS
$result = 0;
$is_error = 0;

// SET DEFAULT EMAIL IF NOT SUBMITTED
if(!isset($_POST['contact_email'])) { $contact_email = $user->user_info['user_email']; } else { $contact_email = $_POST['contact_email']; }

// SEND HELP MESSAGE
if($task == "dosend")
{
  $contact_name = $_POST['contact_name'];
  $contact_subject = $_POST['contact_subject'];
  $contact_message = $_POST['contact_message'];

  // MAKE SURE FIELDS ARE NOT BLANK
  if(!is_email_address($contact_email)) { $is_error = 698; }
  if(trim($contact_message) == "") { $is_error = 1036; }
  if(trim($contact_name) == "") { $is_error = 1046; }
  
  // CHECK CODE
  // NOW IN HEADER:
  //session_start();
  if( !empty($setting['setting_contact_code']) )
  {
    $code = $_SESSION['code'];
    if($code == "") { $code = randomcode(); }
    $contact_secure = $_POST['contact_secure'];
    
    if($contact_secure != $code)
      $is_error = 708;
  }
  
  // SEND MESSAGE TO SUPERADMIN
  if( !$is_error )
  {
    $recepient_info = $database->database_fetch_assoc($database->database_query("SELECT admin_email, admin_name FROM se_admins ORDER BY admin_id LIMIT 1"));

    // GET SUBJECT AND MESSAGE
    SE_Language::_preload_multi(1153, 1154);
    SE_Language::load();

    // COMPOSE SUBJECT
    $subject = vsprintf(SE_Language::_get(1153), Array($contact_subject));

    // COMPOSE MESSAGE
    $message = vsprintf(SE_Language::_get(1154), Array($recepient_info['admin_name'], $contact_email, $contact_name, $contact_subject, $contact_message));

    // SEND MAIL
    send_generic($recepient_info['admin_email'], $contact_email, $subject, $message, Array(), Array());

    // SET RESULT
    $result = 1040;
    $contact_name = "";
    $contact_email = $user->user_info['user_email'];
    $contact_subject = "";
    $contact_message = "";
  }

}

// SET GLOBAL PAGE TITLE/DESCRIPTION
$global_page_title[0] = 754;
$global_page_description[0] = 1035;

// ASSIGN SMARTY VARIABLES AND INCLUDE FOOTER
$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);
$smarty->assign('contact_name', $contact_name);
$smarty->assign('contact_email', $contact_email);
$smarty->assign('contact_subject', $contact_subject);
$smarty->assign('contact_message', $contact_message);
include "footer.php";
?>