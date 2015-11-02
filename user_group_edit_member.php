<?php

$page = "user_group_edit_member";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['group_id'])) { $group_id = $_POST['group_id']; } elseif(isset($_GET['group_id'])) { $group_id = $_GET['group_id']; } else { $group_id = 0; }
if(isset($_POST['groupmember_id'])) { $groupmember_id = $_POST['groupmember_id']; } elseif(isset($_GET['groupmember_id'])) { $groupmember_id = $_GET['groupmember_id']; } else { $groupmember_id = 0; }

// ENSURE GROUPS ARE ENABLED FOR THIS USER
if( ~(int)$user->level_info['level_group_allow'] & 2 )
{
  header("Location: user_home.php");
  exit();
}

// VALIDATE MEMBER
$member_query = $database->database_query("SELECT * FROM se_groupmembers WHERE se_groupmembers.groupmember_id='{$groupmember_id}' LIMIT 1");
if($database->database_num_rows($member_query) != 1) { exit(); }
$groupmember_info = $database->database_fetch_assoc($member_query);


// INITIALIZE GROUP OBJECT
$group = new se_group($user->user_info['user_id'], $group_id);
$result = 0;

if($group->group_exists == 0) { exit(); }
if($group->user_rank == 0 || $group->user_rank == -1) { exit(); }
if($group->user_rank < $groupmember_info['groupmember_rank']) { exit(); }



// SAVE CHANGES TO MEMBER
if($task == "save_do") {
  $member_rank = $_POST['member_rank'];
  $member_title = $_POST['member_title'];

  // DO NOT CHANGE TITLE IF ADMIN HAS TURNED OFF MEMBER TITLES
  if($group->groupowner_level_info['level_group_titles'] != 1) { $member_title = $groupmember_info['groupmember_title']; }

  // DO NOT CHANGE RANK IF ADMIN HAS TURNED OFF OFFICERS BUT MEMBER WAS SOMEHOW SET TO OFFICER
  if($group->groupowner_level_info['level_group_officers'] != 1 && $member_rank == 1) { $member_rank = $groupmember_info['groupmember_rank']; }

  // IF EDITOR IS AN OFFICER, DONT ALLOW RANK CHANGE
  if($group->user_rank != 2) { $member_rank = $groupmember_info['groupmember_rank']; }

  // SAVE CHANGES
  $database->database_query("UPDATE se_groupmembers SET groupmember_title='{$member_title}', groupmember_rank='{$member_rank}' WHERE groupmember_id='{$groupmember_info['groupmember_id']}' AND groupmember_group_id='{$group->group_info['group_id']}' LIMIT 1");

  // IF THIS MEMBER WAS SET TO OWNER, DEMOTE OLD OWNER TO MEMBER
  if($groupmember_info['groupmember_rank'] < 2 && $member_rank == 2)
  {
    $database->database_query("UPDATE se_groupmembers SET groupmember_rank='0' WHERE groupmember_id<>'{$groupmember_info['groupmember_id']}' AND groupmember_rank='2' AND groupmember_group_id='{$group->group_info['group_id']}'");
    $database->database_query("UPDATE se_groups SET group_user_id='{$groupmember_info['groupmember_user_id']}' WHERE group_id='{$group->group_info['group_id']}' LIMIT 1");
  }

  // SEND BACK TO VIEW MEMBERS PAGE
  $result = 191;
}



// ASSIGN SMARTY VARIABLES AND DISPLAY EDIT MEMBER PAGE
$smarty->assign('result', $result);
$smarty->assign('groupmember_info', $groupmember_info);
$smarty->assign('group', $group);
include "footer.php";
?>