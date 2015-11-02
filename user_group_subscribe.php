<?php

$page = "user_group_subscribe";
include "header.php";

if(isset($_POST['group_id'])) { $group_id = $_POST['group_id']; } elseif(isset($_GET['group_id'])) { $group_id = $_GET['group_id']; } else { $group_id = 0; }
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }

// ENSURE GROUPS ARE ENABLED FOR THIS USER
if( ~(int)$user->level_info['level_group_allow'] & 1 )
{
  header("Location: user_home.php");
  exit();
}

// SET EMPTY VARS
$result = 0;
$is_error = 0;

// INITIALIZE GROUP OBJECT
$group = new se_group($user->user_info['user_id'], $group_id);
if($group->group_exists == 0) { exit(); }
if($group->user_rank == -1) { exit(); }

// CHECK IF USER IS SUBSCRIBED OR NOT
if($database->database_num_rows($database->database_query("SELECT NULL FROM se_groupsubscribes WHERE groupsubscribe_group_id='{$group->group_info['group_id']}' AND groupsubscribe_user_id='{$user->user_info['user_id']}' LIMIT 1")) == 1)
{
  $is_subscribed = 1;
}
else
{
  $is_subscribed = 0;
}




// SUBSCRIBE TO GROUP
if($task == "subscribe_do" && !$is_subscribed)
{
  $database->database_query("INSERT INTO se_groupsubscribes (groupsubscribe_group_id, groupsubscribe_user_id, groupsubscribe_time) VALUES ('{$group->group_info['group_id']}', '{$user->user_info['user_id']}', '".time()."')");

  // SET RESULT
  $result = 2000238;
}


// UNSUBSCRIBE TO GROUP
elseif($task == "unsubscribe_do" && $is_subscribed)
{
  $database->database_query("DELETE FROM se_groupsubscribes WHERE groupsubscribe_group_id='{$group->group_info['group_id']}' AND groupsubscribe_user_id='{$user->user_info['user_id']}'");
 
  // SET RESULT 
  $result = 2000239;
}



// ASSIGN SMARTY VARIABLES AND DISPLAY JOIN GROUPS PAGE
$smarty->assign('group', $group);
$smarty->assign('result', $result);
$smarty->assign('is_subscribed', $is_subscribed);
include "footer.php";
?>