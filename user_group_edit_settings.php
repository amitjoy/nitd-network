<?php

$page = "user_group_edit_settings";
include "header.php";

if(isset($_POST['group_id'])) { $group_id = $_POST['group_id']; } elseif(isset($_GET['group_id'])) { $group_id = $_GET['group_id']; } else { $group_id = 0; }
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }

// ENSURE GROUPS ARE ENABLED FOR THIS USER
if( ~(int)$user->level_info['level_group_allow'] & 2 )
{
  header("Location: user_home.php");
  exit();
}

// INITIALIZE GROUP OBJECT
$group = new se_group($user->user_info[user_id], $group_id);

if($group->group_exists == 0) { header("Location: user_group.php"); exit(); }
if($group->user_rank == 0 || $group->user_rank == -1) { header("Location: user_group.php"); exit(); }

// GET PRIVACY SETTINGS
$level_group_privacy = unserialize($group->groupowner_level_info['level_group_privacy']);
rsort($level_group_privacy);
$level_group_comments = unserialize($group->groupowner_level_info['level_group_comments']);
rsort($level_group_comments);
$level_group_discussion = unserialize($group->groupowner_level_info['level_group_discussion']);
rsort($level_group_discussion);
$level_group_upload = unserialize($group->groupowner_level_info['level_group_upload']);
rsort($level_group_upload);
$level_group_tag = unserialize($group->groupowner_level_info['level_group_tag']);
rsort($level_group_tag);

// SET EMPTY VARS
$result = 0;


$groupalbum_info = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_groupalbums WHERE groupalbum_group_id='{$group->group_info['group_id']}' LIMIT 1"));


// SAVE NEW CSS
if($task == "dosave")
{
  $group->group_info['group_approval'] = $_POST['group_approval'];
  $group->group_info['group_search'] = $_POST['group_search'];
  $group->group_info['group_invite'] = $_POST['group_invite'];
  $group->group_info['group_privacy'] = $_POST['group_privacy'];
  $group->group_info['group_comments'] = $_POST['group_comments'];
  $group->group_info['group_discussion'] = $_POST['group_discussion'];
  $group->group_info['group_upload'] = $_POST['group_upload'];
  $groupalbum_info['groupalbum_tag'] = $_POST['groupalbum_tag'];
  $style_group = addslashes(str_replace("-moz-binding", "", strip_tags(htmlspecialchars_decode($_POST['style_group'], ENT_QUOTES))));


  // MAKE SURE SUBMITTED PRIVACY OPTIONS ARE ALLOWED, IF NOT, SET TO EVERYONE
  if(!in_array($group->group_info['group_privacy'], $level_group_privacy)) { $group->group_info['group_privacy'] = $level_group_privacy[0]; }
  if(!in_array($group->group_info['group_comments'], $level_group_comments)) { $group->group_info['group_comments'] = $level_group_comments[0]; }
  if(!in_array($group->group_info['group_discussion'], $level_group_discussion)) { $group->group_info['group_discussion'] = $level_group_discussion[0]; }
  if(!in_array($group->group_info['group_upload'], $level_group_upload)) { $group->group_info['group_upload'] = $level_group_upload[0]; }
  if(!in_array($groupalbum_info['groupalbum_tag'], $level_group_tag)) { $groupalbum_info['groupalbum_tag'] = $level_group_tag[0]; }

  // CHECK THAT SEARCH IS NOT BLANK
  if($user->level_info['level_group_search'] == 0) { $group->group_info['group_search'] = 1; }

  // CHECK THAT INVITE IS NOT BLANK - seems to be missing from level setting page, add?
  //if($user->level_info['level_group_invite'] == 0) { $group->group_info['group_invite'] = 1; }

  // CHECK THAT APPROVAL IS NOT BLANK
  if($user->level_info['level_group_approval'] == 0) { $group->group_info['group_approval'] = 0; }

  // IF NEW MEMBER APPROVAL SETTING IS CHANGED TO 0, APPROVE ALL WAITING MEMBERS
  if($group->group_info['group_approval'] == 0) {
    $database->database_query("UPDATE se_groupmembers SET groupmember_status='1', groupmember_approved='1' WHERE groupmember_group_id='".$group->group_info[group_id]."' AND groupmember_approved='0'");
  }

  // UPDATE GROUP
  $database->database_query("UPDATE se_groups SET group_search='{$group->group_info['group_search']}', group_invite='{$group->group_info['group_invite']}', group_privacy='{$group->group_info['group_privacy']}', group_comments='{$group->group_info['group_comments']}', group_discussion='{$group->group_info['group_discussion']}', group_upload='{$group->group_info['group_upload']}', group_approval='{$group->group_info['group_approval']}' WHERE group_id='{$group->group_info['group_id']}'");
  $database->database_query("UPDATE se_groupalbums SET groupalbum_privacy='{$group->group_info['group_privacy']}', groupalbum_comments='{$group->group_info['group_comments']}', groupalbum_tag='{$groupalbum_info['groupalbum_tag']}', groupalbum_search='{$group->group_info['group_search']}' WHERE groupalbum_group_id='{$group->group_info['group_id']}'");

  // UPDATE ACTIONS
  $database->database_query("UPDATE se_actions SET action_object_privacy='{$group->group_info['group_privacy']}' WHERE action_object_owner='group' AND action_object_owner_id='{$group->group_info['group_id']}'");

  // UPDATE STYLE
  $database->database_query("UPDATE se_groupstyles SET groupstyle_css='{$style_group}' WHERE groupstyle_group_id='{$group->group_info['group_id']}'");
  $result = 1;
}


// GET THIS USER'S GROUP CSS
$style_query = $database->database_query("SELECT groupstyle_css FROM se_groupstyles WHERE groupstyle_group_id='{$group->group_info['group_id']}' LIMIT 1");
if($database->database_num_rows($style_query) == 1)
{ 
  $style_info = $database->database_fetch_assoc($style_query); 
}
else
{
  $database->database_query("INSERT INTO se_groupstyles (groupstyle_group_id, groupstyle_css) VALUES ('{$group->group_info['group_id']}', '')");
  $style_info = $database->database_fetch_assoc($database->database_query("SELECT groupstyle_css FROM se_groupstyles WHERE groupstyle_group_id='{$group->group_info['group_id']}' LIMIT 1")); 
}


// GET PREVIOUS PRIVACY SETTINGS
for($c=0;$c<count($level_group_privacy);$c++) {
  if(group_privacy_levels($level_group_privacy[$c]) != "") {
    SE_Language::_preload(group_privacy_levels($level_group_privacy[$c]));
    $privacy_options[$level_group_privacy[$c]] = group_privacy_levels($level_group_privacy[$c]);
  }
}

for($c=0;$c<count($level_group_comments);$c++) {
  if(group_privacy_levels($level_group_comments[$c]) != "") {
    SE_Language::_preload(group_privacy_levels($level_group_comments[$c]));
    $comment_options[$level_group_comments[$c]] = group_privacy_levels($level_group_comments[$c]);
  }
}

for($c=0;$c<count($level_group_discussion);$c++) {
  if(group_privacy_levels($level_group_discussion[$c]) != "") {
    SE_Language::_preload(group_privacy_levels($level_group_discussion[$c]));
    $discussion_options[$level_group_discussion[$c]] = group_privacy_levels($level_group_discussion[$c]);
  }
}

for($c=0;$c<count($level_group_upload);$c++) {
  if(group_privacy_levels($level_group_upload[$c]) != "") {
    SE_Language::_preload(group_privacy_levels($level_group_upload[$c]));
    $upload_options[$level_group_upload[$c]] = group_privacy_levels($level_group_upload[$c]);
  }
}

for($c=0;$c<count($level_group_tag);$c++) {
  if(group_privacy_levels($level_group_tag[$c]) != "") {
    SE_Language::_preload(group_privacy_levels($level_group_tag[$c]));
    $tag_options[$level_group_tag[$c]] = group_privacy_levels($level_group_tag[$c]);
  }
}


// ASSIGN SMARTY VARIABLES AND DISPLAY EDIT STYLE PAGE
$smarty->assign('group', $group);
$smarty->assign('groupalbum_info', $groupalbum_info);
$smarty->assign('privacy_options', $privacy_options);
$smarty->assign('comment_options', $comment_options);
$smarty->assign('discussion_options', $discussion_options);
$smarty->assign('upload_options', $upload_options);
$smarty->assign('tag_options', $tag_options);
$smarty->assign('result', $result);
$smarty->assign('style_group', htmlspecialchars($style_info['groupstyle_css'], ENT_QUOTES, 'UTF-8'));
include "footer.php";
?>