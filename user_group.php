<?php

$page = "user_group";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }

// ENSURE GROUPS ARE ENABLED FOR THIS USER
if( ~(int)$user->level_info['level_group_allow'] & 2 )
{
  header("Location: user_home.php");
  exit();
}

// SET VARIABLES AND INITIALIZE GROUP OBJECT
$group = new se_group($user->user_info['user_id']);
$sort_by = "se_groupmembers.groupmember_rank DESC, se_groups.group_title";
$where = "(se_groupmembers.groupmember_status='1')";

// GET TOTAL GROUPS
$total_groups = $group->group_total($where);

// GET GROUPS ARRAY
$group_array = $group->group_list(0, $total_groups, $sort_by, $where, 1);


// GET GROUPS INVITED TO
$invite_where = "(se_groupmembers.groupmember_status='0' AND se_groupmembers.groupmember_approved='1')";
$total_invites = $group->group_total($invite_where);
$invite_array = $group->group_list(0, $total_invites, $sort_by, $invite_where, 1);

// ASSIGN VARIABLES AND SHOW VIEW GROUPS PAGE
$smarty->assign('groups', $group_array);
$smarty->assign('total_groups', $total_groups);
$smarty->assign('invites', $invite_array);
$smarty->assign('total_invites', $total_invites);
include "footer.php";
?>