<?php


$page = "browse_groups";
include "header.php";

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if( (!$user->user_exists && !$setting['setting_permission_group']) || ($user->user_exists && (~(int)$user->level_info['level_group_allow'] & 1)) )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}


// PARSE GET/POST
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
if(isset($_POST['s'])) { $s = $_POST['s']; } elseif(isset($_GET['s'])) { $s = $_GET['s']; } else { $s = "group_datecreated DESC"; }
if(isset($_POST['v'])) { $v = $_POST['v']; } elseif(isset($_GET['v'])) { $v = $_GET['v']; } else { $v = 0; }
if(isset($_POST['groupcat_id'])) { $groupcat_id = $_POST['groupcat_id']; } elseif(isset($_GET['groupcat_id'])) { $groupcat_id = $_GET['groupcat_id']; } else { $groupcat_id = 0; }

// ENSURE SORT/VIEW ARE VALID
if($s != "group_datecreated DESC" && $s != "group_totalmembers DESC") { $s = "group_datecreated DESC"; }
if($v != "0" && $v != "1") { $v = 0; }


// SET WHERE CLAUSE
$where = "CASE
	    WHEN se_groups.group_user_id='{$user->user_info['user_id']}'
	      THEN TRUE
	    WHEN ((se_groups.group_privacy & 32) AND '{$user->user_exists}'<>0)
	      THEN TRUE
	    WHEN ((se_groups.group_privacy & 64) AND '{$user->user_exists}'=0)
	      THEN TRUE
	    WHEN ((se_groups.group_privacy & 2) AND (SELECT TRUE FROM se_groupmembers WHERE groupmember_user_id='{$user->user_info['user_id']}' AND groupmember_group_id=se_groups.group_id AND groupmember_status=1 LIMIT 1))
	      THEN TRUE
	    WHEN ((se_groups.group_privacy & 4) AND '{$user->user_exists}'<>0 AND (SELECT TRUE FROM se_friends WHERE friend_user_id1=se_groups.group_user_id AND friend_user_id2='{$user->user_info['user_id']}' AND friend_status=1 LIMIT 1))
	      THEN TRUE
	    WHEN ((se_groups.group_privacy & 8) AND '{$user->user_exists}'<>0 AND (SELECT TRUE FROM se_groupmembers LEFT JOIN se_friends ON se_groupmembers.groupmember_user_id=se_friends.friend_user_id1 WHERE se_groupmembers.groupmember_group_id=se_groups.group_id AND se_friends.friend_user_id2='{$user->user_info['user_id']}' AND se_groupmembers.groupmember_status=1 AND se_friends.friend_status=1 LIMIT 1))
	      THEN TRUE
	    WHEN ((se_groups.group_privacy & 16) AND '{$user->user_exists}'<>0 AND (SELECT TRUE FROM se_groupmembers LEFT JOIN se_friends AS friends_primary ON se_groupmembers.groupmember_user_id=friends_primary.friend_user_id1 LEFT JOIN se_friends AS friends_secondary ON friends_primary.friend_user_id2=friends_secondary.friend_user_id1 WHERE se_groupmembers.groupmember_group_id=se_groups.group_id AND se_groupmembers.groupmember_status=1 AND friends_secondary.friend_user_id2='{$user->user_info['user_id']}' AND friends_primary.friend_status=1 AND friends_secondary.friend_status=1 LIMIT 1))
	      THEN TRUE
	    ELSE FALSE
	END";


// ONLY MY FRIENDS' GROUPS
if($v == "1" && $user->user_exists)
{
  // SET WHERE CLAUSE
  $where .= " AND (SELECT TRUE FROM se_friends LEFT JOIN se_groupmembers ON se_friends.friend_user_id2=se_groupmembers.groupmember_user_id WHERE friend_user_id1='{$user->user_info['user_id']}' AND friend_status=1 AND groupmember_group_id=se_groups.group_id AND groupmember_status=1 LIMIT 1)";
}


// SPECIFIC GROUP CATEGORY
if( is_numeric($groupcat_id) )
{
  $groupcat_query = $database->database_query("SELECT groupcat_id, groupcat_title, groupcat_dependency FROM se_groupcats WHERE groupcat_id='{$groupcat_id}' LIMIT 1");
  if( $database->database_num_rows($groupcat_query) )
  {
    $groupcat = $database->database_fetch_assoc($groupcat_query);
    if( !$groupcat['groupcat_dependency'] )
    {
      $cat_ids[] = $groupcat['groupcat_id'];
      $depcats = $database->database_query("SELECT groupcat_id FROM se_groupcats WHERE groupcat_id='{$groupcat['groupcat_id']}' OR groupcat_dependency='{$groupcat['groupcat_id']}'");
      while($depcat_info = $database->database_fetch_assoc($depcats)) { $cat_ids[] = $depcat_info['groupcat_id']; }
      $where .= " AND se_groups.group_groupcat_id IN('".implode("', '", $cat_ids)."')";
    }
    else
    {
      $where .= " AND se_groups.group_groupcat_id='{$groupcat['groupcat_id']}'";
      $groupsubcat = $groupcat;
      $groupcat = $database->database_fetch_assoc($database->database_query("SELECT groupcat_id, groupcat_title FROM se_groupcats WHERE groupcat_id='{$groupcat['groupcat_dependency']}' LIMIT 1"));
    }
  }
}

// CREATE GROUP OBJECT
$group = new se_group();

// GET TOTAL GROUPS
$total_groups = $group->group_total($where);

// MAKE ENTRY PAGES
$groups_per_page = 10;
$page_vars = make_page($total_groups, $groups_per_page, $p);

// GET GROUP ARRAY
$group_array = $group->group_list($page_vars[0], $groups_per_page, $s, $where, TRUE);

// GET CATS
$field = new se_field("group");
$field->cat_list(0, 0, 0, "", "", "groupfield_id=0");
$cat_array = $field->cats;

// SET GLOBAL PAGE TITLE
$global_page_title[0] = 2000324; 
$global_page_description[0] = 2000325;

// ASSIGN SMARTY VARIABLES AND DISPLAY GROUPS PAGE
$smarty->assign('groupcat_id', $groupcat_id);
$smarty->assign('groupcat', $groupcat);
$smarty->assign('groupsubcat', $groupsubcat);
$smarty->assign('cats', $cat_array);
$smarty->assign('groups', $group_array);
$smarty->assign('total_groups', $total_groups);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($group_array));
$smarty->assign('s', $s);
$smarty->assign('v', $v);
include "footer.php";
?>