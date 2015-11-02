<?php

$page = "user_messages_view";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_GET['pmconvo_id'])) { $pmconvo_id = $_GET['pmconvo_id']; } elseif(isset($_POST['pmconvo_id'])) { $pmconvo_id = $_POST['pmconvo_id']; } else { $pmconvo_id = 0; }
if(isset($_POST['b'])) { $b = $_POST['b']; } elseif(isset($_GET['b'])) { $b = $_GET['b']; } else { $b = 0; } 
if($b != 1 && $b != 0) { $b = 0; }


// CHECK FOR ADMIN ALLOWANCE OF MESSAGES
if( !$user->level_info['level_message_allow'] )
{
  header("Location: user_home.php");
  exit();
}


$pmconvo_info = $user->user_message_validate($pmconvo_id);


// Check if user is in convo
if( !$pmconvo_info )
{
  header("Location: user_messages.php");
  exit();
}


$pm_info = $user->user_message_view($pmconvo_info['pmconvo_id']);


// GOES THROUGH THE ARRAY AND TAKE THE COLLABORATOR OUT AS A USER OBJECT
foreach( $pm_info['collaborators'] as $index => $coll )
{
  $collaborator =& $pm_info['collaborators'][$index];
}


//VARIABLE TO SHOW ERROR OR NOT
$blockerror = false;


// SEND REPLY IF NECESSARY
if($task == "reply")
{
  $reply = $_POST['reply'];
  if( trim($reply) )
  {
    if( !$collaborator->user_blocked($user->user_info['user_id']) )
    {
      $user->user_message_send('', '', $reply, $pmconvo_info['pmconvo_id']);
      header("Location: user_messages_view.php?pmconvo_id=".$pmconvo_info['pmconvo_id']."#bottom");
      exit();
    }
    else
    {
      $blockerror = true;
    }
  }
}

// DELETE CONVERSATION
elseif($task == "delete")
{
  $user->user_message_delete_selected(array($pmconvo_info['pmconvo_id']), $b);
  if($b == 0) { header("Location: user_messages.php"); exit(); } else { header("Location: user_messages_outbox.php"); exit(); }
}


// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('b', $b);
$smarty->assign('blockerror', $blockerror);
$smarty->assign_by_ref('pms',           $pm_info['pms']);
$smarty->assign_by_ref('collaborators', $pm_info['collaborators']);
$smarty->assign_by_ref('pmconvo_info',  $pmconvo_info);

include "footer.php";
?>