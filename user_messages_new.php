<?php

$page = "user_messages_new";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['to_user'])) { $to_user = $_POST['to_user']; } elseif(isset($_GET['to_user'])) { $to_user = $_GET['to_user']; } else { $to_user = ""; }
if(isset($_POST['to_id'])) { $to_id = $_POST['to_id']; } elseif(isset($_GET['to_id'])) { $to_id = $_GET['to_id']; } else { $to_id = ""; }

// CHECK FOR ADMIN ALLOWANCE OF MESSAGES
if( !$user->level_info['level_message_allow'] )
{
  header("Location: user_home.php");
  exit();
}


// SET ERROR VARIABLES AND EMPTY VARS
$is_error = 0;
$submitted = 0;


// TRY TO SEND MESSAGE
if($task == "send")
{
  $to = $_POST['to'];
  $subject = $_POST['subject'];
  $message = $_POST['message'];

  $user->user_message_send($to, $subject, $message);
  $is_error = $user->is_error;

  if($is_error != 0) { SE_Language::_preload($is_error); SE_Language::load(); $error_message = SE_Language::_get($is_error); }
 

  // SEND AJAX CONFIRMATION
  echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><script type='text/javascript'>";
  echo "window.parent.messageSent('$is_error', '".str_replace("'", "\'", $error_message)."');";
  echo "</script></head><body></body></html>";
  exit();
}



// GET LIST OF FRIENDS FOR SUGGEST BOX
$total_friends = $user->user_friend_total(0);
$friends = $user->user_friend_list(0, $total_friends, 0);


// ASSIGN SMARTY VARS AND INCLUDE FOOTER
$smarty->assign('is_error', $is_error);
$smarty->assign('submitted', $submitted);

$smarty->assign_by_ref('friends', $friends);

$smarty->assign('to_user', $to_user);
$smarty->assign('to_id', $to_id);
$smarty->assign('subject', $subject);
$smarty->assign('message', $message);
include "footer.php";
?>