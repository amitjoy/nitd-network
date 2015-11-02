<?php

$page = "user_group_edit_members";
include "header.php";

if(isset($_POST['group_id'])) { $group_id = $_POST['group_id']; } elseif(isset($_GET['group_id'])) { $group_id = $_GET['group_id']; } else { $group_id = 0; }
if(isset($_POST['groupmember_id'])) { $groupmember_id = $_POST['groupmember_id']; } elseif(isset($_GET['groupmember_id'])) { $groupmember_id = $_GET['groupmember_id']; } else { $groupmember_id = 0; }
if(isset($_POST['search'])) { $search = $_POST['search']; } elseif(isset($_GET['search'])) { $search = $_GET['search']; } else { $search = ""; }
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = ""; }
if(isset($_POST['v'])) { $v = $_POST['v']; } elseif(isset($_GET['v'])) { $v = $_GET['v']; } else { $v = 0; }
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
if(isset($_POST['s'])) { $s = $_POST['s']; } elseif(isset($_GET['s'])) { $s = $_GET['s']; } else { $s = "u"; }

// ENSURE GROUPS ARE ENABLED FOR THIS USER
if( ~(int)$user->level_info['level_group_allow'] & 2 )
{
  header("Location: user_home.php");
  exit();
}

// INITIALIZE GROUP OBJECT
$group = new se_group($user->user_info['user_id'], $group_id);

if($group->group_exists == 0) { header("Location: user_group.php"); exit(); }
if($group->user_rank == 0 || $group->user_rank == -1) { header("Location: user_group.php"); exit(); }


// APPROVE MEMBERSHIP REQUEST
if($task == "approve")
{
  $member_query = $database->database_query("SELECT * FROM se_groupmembers WHERE groupmember_id='{$groupmember_id}' AND groupmember_approved=0 AND groupmember_group_id='{$group->group_info['group_id']}'");
  if( $database->database_num_rows($member_query) )
  {
    $member_info = $database->database_fetch_assoc($member_query);
    $database->database_query("UPDATE se_groupmembers SET groupmember_status=1, groupmember_approved=1 WHERE groupmember_id='{$groupmember_id}' AND groupmember_group_id='{$group->group_info['group_id']}'");
    $database->database_query("UPDATE se_groups SET group_totalmembers=group_totalmembers+1 WHERE group_id='{$group->group_info['group_id']}' LIMIT 1");
    $database->database_query("DELETE FROM se_notifys USING se_notifys LEFT JOIN se_notifytypes ON se_notifys.notify_notifytype_id=se_notifytypes.notifytype_id WHERE se_notifys.notify_user_id='{$group->group_info['group_user_id']}' AND se_notifytypes.notifytype_name='groupmemberrequest' AND notify_object_id='{$member_info['groupmember_user_id']}' AND notify_urlvars='".serialize(Array('', $group->group_info['group_id']))."'");
  }
}

// REJECT MEMBERSHIP REQUEST
elseif($task == "reject")
{
  $member_query = $database->database_query("SELECT * FROM se_groupmembers WHERE groupmember_id='{$groupmember_id}' AND groupmember_approved=0 AND groupmember_group_id='{$group->group_info['group_id']}'");
  if( $database->database_num_rows($member_query) )
  {
    $member_info = $database->database_fetch_assoc($member_query);
    $database->database_query("DELETE FROM se_groupmembers WHERE groupmember_id='{$groupmember_id}' AND groupmember_group_id='{$group->group_info['group_id']}'");
    $database->database_query("DELETE FROM se_notifys USING se_notifys LEFT JOIN se_notifytypes ON se_notifys.notify_notifytype_id=se_notifytypes.notifytype_id WHERE se_notifys.notify_user_id='{$group->group_info['group_user_id']}' AND se_notifytypes.notifytype_name='groupmemberrequest' AND notify_object_id='{$member_info['groupmember_user_id']}' AND notify_urlvars='".serialize(Array('', $group->group_info['group_id']))."'");
  }
}

// CANCEL INVITATION
elseif($task == "cancel")
{
  $member_query = $database->database_query("SELECT * FROM se_groupmembers WHERE groupmember_id='{$groupmember_id}' AND groupmember_approved=1 AND groupmember_status=0 AND groupmember_group_id='{$group->group_info['group_id']}'");
  if( $database->database_num_rows($member_query) )
  {
    $member_info = $database->database_fetch_assoc($member_query);
    $database->database_query("DELETE FROM se_groupmembers WHERE groupmember_id='{$groupmember_id}' AND groupmember_group_id='{$group->group_info['group_id']}'");
    $database->database_query("DELETE FROM se_notifys USING se_notifys LEFT JOIN se_notifytypes ON se_notifys.notify_notifytype_id=se_notifytypes.notifytype_id WHERE se_notifys.notify_user_id='{$member_info['groupmember_user_id']}' AND se_notifytypes.notifytype_name='groupinvite' AND notify_object_id='{$group->group_info['group_id']}'");
  }
}

// REMOVE MEMBER
elseif($task == "remove")
{
  $member_query = $database->database_query("SELECT * FROM se_groupmembers WHERE groupmember_id='{$groupmember_id}' AND groupmember_approved=1 AND groupmember_status=1 AND groupmember_group_id='{$group->group_info['group_id']}'");
  if($database->database_num_rows($member_query) == 1)
  {
    $groupmember = $database->database_fetch_assoc($member_query);
    if(($group->user_rank == 2 || $group->user_rank > $groupmember['groupmember_rank']) && $groupmember['groupmember_user_id'] != $user->user_info['user_id'])
    {
      $database->database_query("DELETE FROM se_groupmembers WHERE groupmember_id='{$groupmember_id}' AND groupmember_group_id='{$group->group_info['group_id']}'");
      $database->database_query("UPDATE se_groups SET group_totalmembers=group_totalmembers-1 WHERE group_id='{$group->group_info['group_id']}' LIMIT 1");
      $database->database_query("DELETE FROM se_groupsubscribes WHERE groupsubscribe_group_id='{$group->group_info['group_id']}' AND groupsubscribe_user_id='{$groupmember['groupmember_user_id']}'");
    }
  }

}




// SET SORT VARIABLE FOR DATABASE QUERY
if($s == "ud") {
  $sort = "se_users.user_dateupdated DESC";
} elseif($s == "ld") {
  $sort = "se_users.user_lastlogindate DESC";
} elseif($s == "t") {
  $sort = "se_groupmembers.groupmember_title";
} elseif($s == "r") {
  $sort = "se_groupmembers.groupmember_rank DESC";
} else {
  $sort = "se_users.user_dateupdated DESC";
}


// SET WHERE CLAUSE
$where_clause = Array();
switch($v) {
  case "1":
    $where_clause[] = "se_groupmembers.groupmember_rank<>'0' AND se_groupmembers.groupmember_status='1' AND se_groupmembers.groupmember_approved='1'";
    break;
  case "2":
    $where_clause[] = "se_groupmembers.groupmember_status='0' AND se_groupmembers.groupmember_approved='1'";
    break;
  case "3":
    $where_clause[] = "se_groupmembers.groupmember_approved='0'";
    break;
  default:
    $where_clause[] = "se_groupmembers.groupmember_status='1' AND se_groupmembers.groupmember_approved='1'";
    break;
}
if($search != "") { $where_clause[] = "(se_users.user_username LIKE '%{$search}%' OR se_users.user_email LIKE '%{$search}%' OR CONCAT(se_users.user_fname, ' ', se_users.user_lname) LIKE '%{$search}%')"; }
$where = implode(" AND ", $where_clause);


// GET TOTAL MEMBERS
$total_members = $group->group_member_total($where, 1);

// MAKE MEMBER PAGES
$members_per_page = 10;
$page_vars = make_page($total_members, $members_per_page, $p);

// GET MEMBER ARRAY
$members = $group->group_member_list($page_vars[0], $members_per_page, $sort, $where);



// ASSIGN VARIABLES AND SHOW USER EDIT GROUP MEMBERS PAGE
$smarty->assign('group', $group);
$smarty->assign('total_members', $total_members);
$smarty->assign('members', $members);
$smarty->assign('search', $search);
$smarty->assign('s', $s);
$smarty->assign('v', $v);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($members));
include "footer.php";
?>