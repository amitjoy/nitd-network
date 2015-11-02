<?php
$page = "user_group_manage";
include "header.php";

if(isset($_POST['group_id'])) { $group_id = $_POST['group_id']; } elseif(isset($_GET['group_id'])) { $group_id = $_GET['group_id']; } else { $group_id = 0; }
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }

// ENSURE GROUPS ARE ENABLED FOR THIS USER
if( ~(int)$user->level_info['level_group_allow'] & 2 )
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

// WANTS TO JOIN GROUP
if($group->user_rank == -1 && $group->groupmember_info['groupmember_approved'] == 0)
{
  $subpage = "join";
  if($group->groupmember_info['groupmember_id'] != 0 && $group->groupmember_info['groupmember_approved'] == 0)
  {
    $subpage = "waiting";
  }
}

// WANTS TO LEAVE GROUP
else
{
  $subpage = "leave";
  if($group->groupmember_info['groupmember_status'] == 0)
  {
    $subpage = "confirm";
  }
}



// LEAVE GROUP
if($task == "leave_do" && $subpage == "leave")
{
  $database->database_query("DELETE FROM se_groupmembers WHERE groupmember_group_id='{$group->group_info['group_id']}' AND groupmember_user_id='{$user->user_info['user_id']}' LIMIT 1");
  $database->database_query("DELETE FROM se_groupsubscribes WHERE groupsubscribe_group_id='{$group->group_info['group_id']}' AND groupsubscribe_user_id='{$user->user_info['user_id']}'");
  $database->database_query("UPDATE se_groups SET group_totalmembers=group_totalmembers-1 WHERE group_id='{$group->group_info['group_id']}' LIMIT 1");
  
  // IF USER IS OWNER OF GROUP, DELETE THE GROUP
  if($group->user_rank == 2) { $group->group_delete(); }

  // INSERT ACTION
  $group_title_short = $group->group_info['group_title'];
  if(strlen($group_title_short) > 100) { $group_title_short = substr($group_title_short, 0, 97); $group_title_short .= "..."; }
  $actions->actions_add($user, "leavegroup", Array($user->user_info['user_username'], $user->user_displayname, $group->group_info['group_id'], $group_title_short), Array(), 0, false, "group", $group->group_info['group_id'], $group->group_info['group_privacy']);

  // SET RESULT
  $result = 2000161;
}




// ACCEPT INVITATION
elseif($task == "accept_do" && $subpage == "confirm")
{
  // JOIN GROUP
  $database->database_query("UPDATE se_groupmembers SET groupmember_status='1', groupmember_approved='1' WHERE groupmember_user_id='{$user->user_info['user_id']}' AND groupmember_group_id='{$group->group_info['group_id']}'");
  $database->database_query("UPDATE se_groups SET group_totalmembers=group_totalmembers+1 WHERE group_id='{$group->group_info['group_id']}' LIMIT 1");
  
  // INSERT ACTION
  $group_title_short = $group->group_info['group_title'];
  if(strlen($group_title_short) > 100) { $group_title_short = substr($group_title_short, 0, 97); $group_title_short .= "..."; }
  $actions->actions_add($user, "joingroup", Array($user->user_info['user_username'], $user->user_displayname, $group->group_info['group_id'], $group_title_short), Array(), 0, false, "group", $group->group_info['group_id'], $group->group_info['group_privacy']);

  // DELETE NOTIFICATION
  $database->database_query("DELETE FROM se_notifys USING se_notifys LEFT JOIN se_notifytypes ON se_notifys.notify_notifytype_id=se_notifytypes.notifytype_id WHERE se_notifys.notify_user_id='{$user->user_info['user_id']}' AND se_notifytypes.notifytype_name='groupinvite' AND notify_object_id='{$group->group_info['group_id']}'");

  // SET RESULT 
  $result = 2000162;
}





// REJECT INVITATION
elseif($task == "reject_do" && $subpage == "confirm")
{
  // DELETE GROUPMEMBER ROW
  $database->database_query("DELETE FROM se_groupmembers WHERE groupmember_user_id='{$user->user_info['user_id']}' AND groupmember_group_id='{$group->group_info['group_id']}'");

  // DELETE NOTIFICATION
  $database->database_query("DELETE FROM se_notifys USING se_notifys LEFT JOIN se_notifytypes ON se_notifys.notify_notifytype_id=se_notifytypes.notifytype_id WHERE se_notifys.notify_user_id='{$user->user_info['user_id']}' AND se_notifytypes.notifytype_name='groupinvite' AND notify_object_id='{$group->group_info['group_id']}'");

  // SET RESULT
  $result = 2000163;
}





// JOIN GROUP
elseif($task == "join_do" && $subpage == "join")
{
  // IF GROUP REQUIRES APPROVAL
  if($group->group_info['group_approval'] == 1)
  {
    $database->database_query("
      INSERT INTO se_groupmembers (
        groupmember_user_id,
        groupmember_group_id,
        groupmember_status,
        groupmember_approved,
        groupmember_rank
      ) VALUES (
        '{$user->user_info['user_id']}',
        '{$group->group_info['group_id']}',
        '0',
        '0',
        '0'
      )
    ");
    
    // NOTIFY GROUP OWNER
    $group_title_short = $group->group_info['group_title'];
    if(strlen($group_title_short) > 100) { $group_title_short = substr($group_title_short, 0, 97); $group_title_short .= "..."; }
    
    $sql = "SELECT se_users.user_id, se_users.user_username, se_users.user_email, se_users.user_fname, se_users.user_lname, se_usersettings.usersetting_notify_groupmemberrequest FROM se_users LEFT JOIN se_usersettings ON se_users.user_id=se_usersettings.usersetting_user_id WHERE se_users.user_id='{$group->group_info['group_user_id']}'";
    $groupowner_info = $database->database_fetch_assoc($database->database_query($sql));
    
    if( $groupowner_info['usersetting_notify_groupmemberrequest'] == 1 )
    {
      $group_owner = new se_user();
      $group_owner->user_info['user_id'] = $groupowner_info['user_id'];
      $group_owner->user_info['user_username'] = $groupowner_info['user_username'];
      $group_owner->user_info['user_email'] = $groupowner_info['user_email'];
      $group_owner->user_info['user_fname'] = $groupowner_info['user_fname'];
      $group_owner->user_info['user_lname'] = $groupowner_info['user_lname'];
      $group_owner->user_displayname();
      
      send_systememail(
      'groupmemberrequest',
      $groupowner_info['user_email'],
      Array(
        $group_owner->user_displayname,
        $user->user_displayname,
        $group->group_info['group_title'],
        "<a href=\"".$url->url_base."login.php\">".$url->url_base."login.php</a>"
      ));
    }
    
    $notify->notify_add($groupowner_info['user_id'], 'groupmemberrequest', $user->user_info['user_id'], Array('', $group->group_info['group_id']), Array($group_title_short));
    
    // SET RESULT
    $result = 2000164;
  }
  
  // IF GROUP DOES NOT REQUIRE APPROVAL
  else
  {
    $database->database_query("
      INSERT INTO se_groupmembers (
        groupmember_user_id,
        groupmember_group_id,
        groupmember_status,
        groupmember_approved,
        groupmember_rank
      ) VALUES (
        '{$user->user_info['user_id']}',
        '{$group->group_info['group_id']}',
        '1',
        '1',
        '0'
      )
    ");
    
    $database->database_query("UPDATE se_groups SET group_totalmembers=group_totalmembers+1 WHERE group_id='{$group->group_info['group_id']}' LIMIT 1");
    
    // INSERT ACTION
    $group_title_short = $group->group_info['group_title'];
    if(strlen($group_title_short) > 100) { $group_title_short = substr($group_title_short, 0, 97); $group_title_short .= "..."; }
    $actions->actions_add($user, "joingroup", Array($user->user_info['user_username'], $user->user_displayname, $group->group_info['group_id'], $group_title_short), Array(), 0, false, "group", $group->group_info['group_id'], $group->group_info['group_privacy']);
    
    // SET RESULT
    $result = 2000162;
  }

}



// ASSIGN SMARTY VARIABLES AND DISPLAY JOIN GROUPS PAGE
$smarty->assign('group', $group);
$smarty->assign('result', $result);
$smarty->assign('subpage', $subpage);
include "footer.php";
?>