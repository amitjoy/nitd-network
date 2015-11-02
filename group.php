<?php

$page = "group";
include "header.php";

if(isset($_POST['group_id'])) { $group_id = $_POST['group_id']; } elseif(isset($_GET['group_id'])) { $group_id = $_GET['group_id']; } else { $group_id = 0; }

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if( (!$user->user_exists && !$setting['setting_permission_group']) || ($user->user_exists && (~(int)$user->level_info['level_group_allow'] & 1)) )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

// DISPLAY ERROR PAGE IF NO OWNER
$group = new se_group($user->user_info['user_id'], $group_id);
if( !$group->group_exists )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 2000219);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}


// GET VIEW AND VARS
if(isset($_POST['v'])) { $v = $_POST['v']; } elseif(isset($_GET['v'])) { $v = $_GET['v']; } else { $v = "group"; }
if(isset($_POST['search'])) { $search = $_POST['search']; } elseif(isset($_GET['search'])) { $search = $_GET['search']; } else { $search = ""; }
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = ""; }
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }

// VALIDATE VIEW VAR
if($v != "group" && $v != "members" && $v != "comments" && $v != "photos" && $v != "discussions") { $v = "group"; }


// DELETE DISCUSSION TOPIC
if( $task == "topic_delete" && ($group->user_rank == 2 || $group->user_rank == 1) )
{
  $grouptopic_id = $_GET['grouptopic_id'];
  $resource = $database->database_query("SELECT NULL FROM se_grouptopics WHERE se_grouptopics.grouptopic_id='{$grouptopic_id}' LIMIT 1");
  
  if( $database->database_num_rows($resource) )
  {
    $database->database_query("DELETE FROM se_grouptopics WHERE se_grouptopics.grouptopic_id='{$grouptopic_id}' LIMIT 1");
    $database->database_query("DELETE FROM se_groupposts WHERE se_groupposts.grouppost_grouptopic_id='{$grouptopic_id}' LIMIT 1");
    $database->database_query("UPDATE se_groups SET group_totaltopics=group_totaltopics-1 WHERE group_id='{$group->group_info['group_id']}' LIMIT 1");
    $group->group_info['group_totaltopics']--;
  }
}
  

// RETRIEVE FILES
elseif($task == "files_get")
{
  // GET VARS
  if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
  if(isset($_POST['cpp'])) { $cpp = $_POST['cpp']; } elseif(isset($_GET['cpp'])) { $cpp = $_GET['cpp']; } else { $cpp = 1; }

  // GET GROUP ALBUM INFO
  $groupalbum_info = $database->database_fetch_assoc($database->database_query("SELECT groupalbum_id FROM se_groupalbums WHERE groupalbum_group_id='{$group->group_info['group_id']}' LIMIT 1"));

  // GET TOTAL FILES
  $total_files = $group->group_media_total($groupalbum_info['groupalbum_id']);

  // MAKE FILE PAGES AND GET FILE ARRAY
  $page_vars = make_page($total_files, $cpp, $p);
  $group_files = $group->group_media_list($page_vars[0], $cpp, $sort_by = "groupmedia_date DESC", $where = "");


  // CONSTRUCT JSON RESPONSE
  $file_output = Array('total_files' => (int) $total_files,
			'maxpage' => (int) $page_vars[2],
			'p_start' => (int) ($page_vars[0]+1),
			'p_end' => (int) ($page_vars[0]+count($group_files)),
			'p' => (int) $page_vars[1],
			'files' => $group_files);
  echo json_encode($file_output);
  exit();

}


// GET PRIVACY LEVEL
$privacy_max = $group->group_privacy_max($user);
$allowed_to_view = (bool) ($privacy_max & $group->group_info['group_privacy']);
$is_group_private = !$allowed_to_view;

// CHECK IF USER IS ALLOWED TO COMMENT
$allowed_to_comment = (bool) ($privacy_max & $group->group_info['group_comments']);

// CHECK IF USER IS ALLOWED TO POST IN DISCUSSION
$allowed_to_discuss = (bool) ($privacy_max & $group->group_info['group_discussion']);

// CHECK IF USER IS ALLOWED TO UPLOAD PHOTOS
$allowed_to_upload = (bool) ($privacy_max & $group->group_info['group_upload']);

// CHECK IF USER IS ALLOWED TO INVITE MEMBERS
$allowed_to_invite = (bool) ( $group->user_rank>=1 || ($group->user_rank>-1 && $group->group_info['group_invite']) );


// UPDATE GROUP VIEWS IF GROUP VISIBLE
if( $allowed_to_view )
{
  $group_views = $group->group_info['group_views'] + 1;
  $database->database_query("UPDATE se_groups SET group_views=group_views+1 WHERE group_id='{$group->group_info['group_id']}' LIMIT 1");
}

// DELETE COMMENT NOTIFICATIONS IF VIEWING COMMENT PAGE
if( /* $v == "discussions" && */ $user->user_info['user_id'] == $group->group_info['group_user_id'] )
{
  $database->database_query("DELETE FROM se_notifys USING se_notifys LEFT JOIN se_notifytypes ON se_notifys.notify_notifytype_id=se_notifytypes.notifytype_id WHERE se_notifys.notify_user_id='{$group->group_info['group_user_id']}' AND se_notifytypes.notifytype_name='groupcomment' AND notify_object_id='{$group->group_info['group_id']}'");
}

// DELETE POST NOTIFICATIONS IF VIEWING DISCUSSION PAGE
if( /* $v == "discussions" && */ $user->user_info['user_id'] == $group->group_info['group_user_id'] )
{
  $database->database_query("DELETE FROM se_notifys USING se_notifys LEFT JOIN se_notifytypes ON se_notifys.notify_notifytype_id=se_notifytypes.notifytype_id WHERE se_notifys.notify_user_id='{$group->group_info['group_user_id']}' AND se_notifytypes.notifytype_name='grouppost' AND notify_object_id='{$group->group_info['group_id']}'");
}

// GET GROUP COMMENTS
$comment = new se_comment('group', 'group_id', $group->group_info['group_id']);
$total_comments = $comment->comment_total();

// GET GROUP MEDIA
$groupalbum_info = $database->database_fetch_assoc($database->database_query("SELECT groupalbum_id FROM se_groupalbums WHERE groupalbum_group_id='{$group->group_info['group_id']}' LIMIT 1"));
$total_files = $group->group_media_total($groupalbum_info[groupalbum_id]);

// GET GROUP FIELDS
$groupcat_info = $database->database_fetch_assoc($database->database_query("SELECT t1.groupcat_id AS subcat_id, t1.groupcat_title AS subcat_title, t1.groupcat_dependency AS subcat_dependency, t2.groupcat_id AS cat_id, t2.groupcat_title AS cat_title FROM se_groupcats AS t1 LEFT JOIN se_groupcats AS t2 ON t1.groupcat_dependency=t2.groupcat_id WHERE t1.groupcat_id='{$group->group_info['group_groupcat_id']}'"));
if($groupcat_info['subcat_dependency'] == 0) { $cat_where = "groupcat_id='{$group->group_info['group_groupcat_id']}'"; } else { $cat_where = "groupcat_id='{$groupcat_info['subcat_dependency']}'"; }
$field = new se_field("group", $group->groupvalue_info);
$field->cat_list(0, 1, 0, $cat_where, "groupcat_id='0'", "");

// SET WHERE CLAUSE FOR MEMBER LIST
$where[] = "(se_groupmembers.groupmember_status='1')";
if($search != "") { $where[] = "(se_users.user_username LIKE '%{$search}%' OR CONCAT(se_users.user_fname, ' ', se_users.user_lname) LIKE '%{$search}%' OR se_users.user_email LIKE '%{$search}%')"; }

// GET TOTAL MEMBERS
$total_members = $group->group_member_total(implode(" AND ", $where), 1);

// MAKE MEMBER PAGES AND GET MEMBER ARRAY
$members_per_page = 10;
if($v == "members") { $p_members = $p; } else { $p_members = 1; }
$page_vars_members = make_page($total_members, $members_per_page, $p_members);
$members = $group->group_member_list($page_vars_members[0], $members_per_page, "is_viewers_friend DESC, se_users.user_username", implode(" AND ", $where));

// GET MASTER TOTAL OF MEMBERS
$total_members_all = $group->group_member_total("(se_groupmembers.groupmember_status='1')");

// GET OFFICERS
$where_officers = "se_groupmembers.groupmember_rank<>'0' AND se_groupmembers.groupmember_status='1' AND se_groupmembers.groupmember_approved='1'";
$total_officers = $group->group_member_total($where_officers, 0);
$officers = $group->group_member_list(0, $total_officers, "se_groupmembers.groupmember_rank DESC, se_users.user_username", $where_officers);

// CHECK TO SEE IF USER IS SUBSCRIBED TO GROUP AND UPDATE VIEW TIME
if($database->database_num_rows($database->database_query("SELECT NULL FROM se_groupsubscribes WHERE groupsubscribe_group_id='{$group->group_info['group_id']}' AND groupsubscribe_user_id='{$user->user_info['user_id']}' LIMIT 1")) == 1) {
  $is_subscribed = 1;
  $database->database_query("UPDATE se_groupsubscribes SET groupsubscribe_time='".time()."' WHERE groupsubscribe_group_id='{$group->group_info['group_id']}' AND groupsubscribe_user_id='{$user->user_info['user_id']}'");
} else {
  $is_subscribed = 0;
}

// GET TOTAL DISCUSSION TOPICS
$total_topics = $group->group_topic_total();

// MAKE TOPIC PAGES AND GET TOPIC ARRAY
$topics_per_page = 10;
if($v == "discussions") { $p_topics = $p; } else { $p_topics = 1; }
$page_vars_topics = make_page($total_topics, $topics_per_page, $p_topics);
$topics = $group->group_topic_list($page_vars_topics[0], $topics_per_page, "grouptopic_sticky DESC, grouppost_date DESC");
//$topics = $group->group_topic_list($page_vars_topics[0], $topics_per_page, "grouptopic_sticky DESC, grouptopic_date DESC");


// GET CUSTOM GROUP STYLE IF ALLOWED
if( $group->groupowner_level_info['level_group_style'] && !$is_group_private )
{ 
  $groupstyle_info = $database->database_fetch_assoc($database->database_query("SELECT groupstyle_css FROM se_groupstyles WHERE groupstyle_group_id='{$group->group_info['group_id']}' LIMIT 1")); 
  $global_css = $groupstyle_info['groupstyle_css'];
}

// SET GLOBAL PAGE TITLE
$global_page_title[0] = 2000312; 
$global_page_title[1] = $group->group_info['group_title'];
$global_page_description[0] = 2000313;
$global_page_description[1] = $group->group_info['group_desc'];

// GET ACTIONS
$actions_array = $actions->actions_display(0, $setting['setting_actions_actionsonprofile'], "se_actions.action_object_owner='group' AND se_actions.action_object_owner_id='{$group->group_info['group_id']}'");
$smarty->assign_by_ref('actions', $actions_array);


// ASSIGN VARIABLES AND DISPLAY GROUP PAGE
$smarty->assign_by_ref('group', $group);
$smarty->assign_by_ref('cats', $field->cats);
$smarty->assign_by_ref('members', $members);
$smarty->assign_by_ref('officers', $officers);
$smarty->assign_by_ref('topics', $topics);

$smarty->assign('groupcat_info', $groupcat_info);
$smarty->assign('total_comments', $total_comments);
$smarty->assign('total_files', $total_files);
$smarty->assign('is_group_private', $is_group_private);
$smarty->assign('allowed_to_view', $allowed_to_view);
$smarty->assign('allowed_to_comment', $allowed_to_comment);
$smarty->assign('allowed_to_discuss', $allowed_to_discuss);
$smarty->assign('allowed_to_upload', $allowed_to_upload);
$smarty->assign('allowed_to_invite', $allowed_to_invite);
$smarty->assign('is_subscribed', $is_subscribed);
$smarty->assign('v', $v);
$smarty->assign('search', $search);
$smarty->assign('total_members', $total_members);
$smarty->assign('total_members_all', $total_members_all);
$smarty->assign('maxpage_members', $page_vars_members[2]);
$smarty->assign('p_start_members', $page_vars_members[0]+1);
$smarty->assign('p_end_members', $page_vars_members[0]+count($members));
$smarty->assign('p_members', $page_vars_members[1]);
$smarty->assign('total_topics', $total_topics);
$smarty->assign('maxpage_topics', $page_vars_topics[2]);
$smarty->assign('p_start_topics', $page_vars_topics[0]+1);
$smarty->assign('p_end_topics', $page_vars_topics[0]+count($topics));
$smarty->assign('p_topics', $page_vars_topics[1]);
include "footer.php";
?>