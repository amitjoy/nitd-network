<?php

$page = "user_group_add";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }

// ENSURE GROUPS ARE ENABLED FOR THIS USER
if( ~(int)$user->level_info['level_group_allow'] & 4 )
{
  header("Location: user_home.php");
  exit();
}


// GET PRIVACY SETTINGS
$level_group_privacy = unserialize($user->level_info['level_group_privacy']);
rsort($level_group_privacy);
$level_group_comments = unserialize($user->level_info['level_group_comments']);
rsort($level_group_comments);
$level_group_discussion = unserialize($user->level_info['level_group_discussion']);
rsort($level_group_discussion);
$level_group_upload = unserialize($user->level_info['level_group_upload']);
rsort($level_group_upload);
$level_group_tag = unserialize($user->level_info['level_group_tag']);
rsort($level_group_tag);


// INITIALIZE VARIABLES
$is_error = 0;
$group_info['group_title'] = "";
$group_info['group_desc'] = "";
$group_info['group_groupcat_id'] = 0;
$group_info['group_groupsubcat_id'] = 0;
$group_info['group_approval'] = 0;
$group_info['group_invite'] = 1;
$group_info['group_search'] = 1;
$group_info['group_privacy'] = $level_group_privacy[0];
$group_info['group_comments'] = $level_group_comments[0];
$group_info['group_discussion'] = $level_group_discussion[0];
$group_info['group_upload'] = $level_group_upload[0];
$groupalbum_info['groupalbum_tag'] = $level_group_tag[0];


// INITIALIZE GROUP OBJECT
$group = new se_group($user->user_info['user_id'], 0);


// CHECK TO MAKE SURE USER HAS LESS THAN MAX NUMBER OF GROUPS ALLOWED
$owned_where = "(se_groups.group_user_id='{$user->user_info['user_id']}')";
$total_groups_owned = $group->group_total($owned_where);
if($total_groups_owned >= $user->level_info['level_group_maxnum']) { $is_error = 2000114; $task = "main"; }



// ATTEMPT TO ADD GROUP
if($task == "doadd")
{
  $group_info['group_title'] = censor($_POST['group_title']);
  $group_info['group_desc'] = censor(str_replace("\r\n", "<br>", $_POST['group_desc']));
  $group_info['group_approval'] = $_POST['group_approval'];
  $group_info['group_invite'] = $_POST['group_invite'];
  $group_info['group_search'] = $_POST['group_search'];
  $group_info['group_privacy'] = $_POST['group_privacy'];
  $group_info['group_comments'] = $_POST['group_comments'];
  $group_info['group_discussion'] = $_POST['group_discussion'];
  $group_info['group_upload'] = $_POST['group_upload'];
  $groupalbum_info['groupalbum_tag'] = $_POST['groupalbum_tag'];
  $group_info['group_groupcat_id'] = $_POST['group_groupcat_id'];
  $group_info['group_groupsubcat_id'] = $_POST['group_groupsubcat_id'];

  // GET FIELDS
  $field = new se_field("group");
  $field->cat_list(1, 0, 0, "groupcat_id='{$group_info['group_groupcat_id']}'", "", "");
  $selected_fields = $field->fields_all;
  $is_error = $field->is_error; 
 
  // CHECK TO MAKE SURE TITLE HAS BEEN ENTERED
  if(str_replace(" ", "", $group_info['group_title']) == "") { $is_error = 2000115; }

  // CHECK TO MAKE SURE CATEGORY HAS BEEN SELECTED
  if($group_info['group_groupcat_id'] == 0) { $is_error = 2000117; }

  // MAKE SURE SUBMITTED PRIVACY OPTIONS ARE ALLOWED, IF NOT, SET TO EVERYONE
  if(!in_array($group_info['group_privacy'], $level_group_privacy)) { $group_info['group_privacy'] = $level_group_privacy[0]; }
  if(!in_array($group_info['group_comments'], $level_group_comments)) { $group_info['group_comments'] = $level_group_comments[0]; }
  if(!in_array($group_info['group_discussion'], $level_group_discussion)) { $group_info['group_discussion'] = $level_group_discussion[0]; }
  if(!in_array($group_info['group_upload'], $level_group_upload)) { $group_info['group_upload'] = $level_group_upload[0]; }
  if(!in_array($groupalbum_info['groupalbum_tag'], $level_group_tag)) { $groupalbum_info['groupalbum_tag'] = $level_group_tag[0]; }

  // CHECK THAT SEARCH IS NOT BLANK
  if( !$user->level_info['level_group_search'] ) { $group_info['group_search'] = 1; }

  // CHECK THAT INVITE IS NOT BLANK - seems to be missing from level setting page, add?
  //if( !$user->level_info['level_group_invite'] ) { $group->group_info['group_invite'] = 1; }

  // CHECK THAT APPROVAL IS NOT BLANK
  if( !$user->level_info['level_group_approval'] ) { $group_info['group_approval'] = 0; }


  // IF NO ERROR, SAVE GROUP
  if($is_error == 0)
  {
    // SET GROUP CATEGORY ID
    if($group_info['group_groupsubcat_id'] != "" && $group_info['group_groupsubcat_id'] != 0) { $group_info['group_groupcat_id'] = $group_info['group_groupsubcat_id']; }
    
    $group_id = $group->group_create(
      $group_info['group_title'],
      $group_info['group_desc'],
      $group_info['group_groupcat_id'],
      $group_info['group_approval'],
      $group_info['group_invite'],
      $group_info['group_search'],
      $group_info['group_privacy'],
      $group_info['group_comments'],
      $group_info['group_discussion'],
      $group_info['group_upload'],
      $groupalbum_info['groupalbum_tag'],
      $field->field_query
    );
    
    // INSERT ACTION
    $group_title_short = $group_info[group_title];
    if(strlen($group_title_short) > 100) { $group_title_short = substr($group_title_short, 0, 97); $group_title_short .= "..."; }
    $actions->actions_add($user, "newgroup", Array($user->user_info['user_username'], $user->user_displayname, $group_id, $group_title_short), Array(), 0, false, "group", $group_id, $group_info['group_privacy']);

    header("Location: user_group_edit.php?group_id={$group_id}&justadded=1");
    exit();
  }
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


// GET FIELDS
$field = new se_field("group");
$field->cat_list(0, 0, 0, "", "", "");
$cat_array = $field->cats;
if($is_error != 0) {
  $selected_cat_array = array_filter($cat_array, create_function('$a', 'if($a[cat_id] == "'.$group_info['group_groupcat_id'].'") { return $a; }'));
  while(list($key, $val) = each($selected_cat_array)) {
    $cat_array[$key][fields] = $selected_fields;
  }
}

// REMOVE BREAKS
$group_info['group_desc'] = str_replace("<br>", "\r\n", $group_info['group_desc']);


// ASSIGN VARIABLES AND SHOW ADD GROUPS PAGE
$smarty->assign('is_error', $is_error);
$smarty->assign('cats', $cat_array);
$smarty->assign('group_info', $group_info);
$smarty->assign('groupalbum_info', $groupalbum_info);
$smarty->assign('privacy_options', $privacy_options);
$smarty->assign('comment_options', $comment_options);
$smarty->assign('discussion_options', $discussion_options);
$smarty->assign('upload_options', $upload_options);
$smarty->assign('tag_options', $tag_options);
include "footer.php";
?>