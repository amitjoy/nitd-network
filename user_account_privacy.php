<?php


$page = "user_account_privacy";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }

// SET RESULT VARIABLES
$result = 0;
$is_error = 0;

// GET PRIVACY SETTINGS
$level_profile_privacy = unserialize($user->level_info['level_profile_privacy']);
$level_profile_comments = unserialize($user->level_info['level_profile_comments']);

// SAVE ACCOUNT SETTINGS
if($task == "dosave")
{
  $user->user_info['user_invisible'] = $_POST['user_invisible'];
  $user->user_info['user_saveviews'] = $_POST['user_saveviews'];
  $privacy_profile = $_POST['privacy_profile'];
  $comments_profile = $_POST['comments_profile'];
  $search_profile = $_POST['search_profile'];
  $user_blocklist = $_POST['user_blocklist'];

  // MAKE SURE SUBMITTED PRIVACY OPTIONS ARE ALLOWED, IF NOT, SET TO EVERYONE
  if(!$user->level_info['level_profile_search']) { $search_profile = 1; }
  if(!in_array($privacy_profile, $level_profile_privacy)) { $privacy_profile = $level_profile_privacy[0]; }
  if(!in_array($comments_profile, $level_profile_comments)) { $comments_profile = $level_profile_comments[0]; }

  // GET ACTION TYPES TO PUBLISH
  $actiontype_disallowed = Array();
  if($setting[setting_actions_privacy] == 1)
  {
    $actiontype = $_POST['actiontype'];
    $actiontype_query = $database->database_query("SELECT actiontype_id FROM se_actiontypes WHERE actiontype_id NOT IN ('".implode("', '", $actiontype)."')");
    while($actiontype_info = $database->database_fetch_assoc($actiontype_query))
    {
      $actiontype_disallowed[] = $actiontype_info['actiontype_id'];
    }
    
    // SAVE DISALLOWED ACTION TYPES IN THE USER SETTINGS TABLE
    $database->database_query("UPDATE se_usersettings SET usersetting_actions_dontpublish='".implode(",", $actiontype_disallowed)."' WHERE usersetting_user_id='{$user->user_info['user_id']}' LIMIT 1");
    
    // Flush cached usersettings
    $usersettings_static =& SEUser::getUserSettings($user->user_info['user_id']);
    $usersettings_static = NULL;
    
    $cache_object = SECache::getInstance();
    if( is_object($cache_object) )
    {
      $cache_object->remove('site_user_settings_'.$user->user_info['user_id']);
    }
  }

  // GET BLOCKED LIST
  $block_list = Array();
  if($user->level_info[level_profile_block] != 0)
  {
    $user_blocklist = array_unique($user_blocklist);
    for($b=0;$b<count($user_blocklist);$b++)
    {
      if($user_blocklist[$b] != $user->user_info['user_id'])
      {
        $block_list[] = $user_blocklist[$b];
        $user->user_friend_remove($user_blocklist[$b]);
      }
    }
  }

  // UPDATE DATABASE
  $database->database_query("UPDATE se_users SET user_invisible='{$user->user_info['user_invisible']}', user_blocklist='".implode(",", $block_list)."', user_saveviews='{$user->user_info['user_saveviews']}', user_privacy='{$privacy_profile}', user_comments='{$comments_profile}', user_search='{$search_profile}' WHERE user_id='{$user->user_info['user_id']}' LIMIT 1");

  // UPDATE ACTIONS
  $database->database_query("UPDATE se_actions SET action_object_privacy='$privacy_profile' WHERE action_object_owner='user' AND action_object_owner_id='{$user->user_info['user_id']}'");


  $user->user_info['user_privacy'] = $privacy_profile;
  $user->user_info['user_comments'] = $comments_profile;
  $user->user_info['user_search'] = $search_profile;
  $user->user_info['user_blocklist'] = implode(",", $block_list);
  $result = 1;
}


// POPULATE USER SETTINGS ARRAY
$user->user_settings();

// CREATE ARRAY OF ACTION TYPES
$actiontypes_disallowed = explode(",", $user->usersetting_info['usersetting_actions_dontpublish']);
$actiontypes_query = $database->database_query("SELECT * FROM se_actiontypes WHERE actiontype_enabled=1 AND actiontype_setting=1");
while($actiontype = $database->database_fetch_assoc($actiontypes_query))
{
  // MAKE THIS ACTION TYPE SELECTED IF ITS NOT DISALLOWED BY USER
  $actiontype_selected = 1;
  if(in_array($actiontype['actiontype_id'], $actiontypes_disallowed)) { $actiontype_selected = 0; }
  SE_Language::_preload($actiontype['actiontype_desc']);
  $actiontypes_array[] = Array(
    'actiontype_id' => $actiontype['actiontype_id'],
    'actiontype_selected' => $actiontype_selected,
    'actiontype_desc' => $actiontype['actiontype_desc']
  );
}


// CREATE ARRAY OF BLOCKED USERS
if($user->user_info['user_blocklist'] != "")
{
  $blocked_users = $database->database_query("SELECT user_id, user_username, user_fname, user_lname, user_photo FROM se_users WHERE user_id IN('".implode("','", array_filter(explode(",", $user->user_info['user_blocklist'])))."')");
  while($block = $database->database_fetch_assoc($blocked_users))
  {
    $block_user = new se_user();
    $block_user->user_info['user_id'] = $block['user_id'];
    $block_user->user_info['user_username'] = $block['user_username'];
    $block_user->user_info['user_photo'] = $block['user_photo'];
    $block_user->user_info['user_fname'] = $block['user_fname'];
    $block_user->user_info['user_lname'] = $block['user_lname'];
    $block_user->user_displayname();
    
    $block_array[] = $block_user;
  }
}


// GET PREVIOUS PRIVACY SETTINGS
for($c=0;$c<count($level_profile_privacy);$c++) {
  if(user_privacy_levels($level_profile_privacy[$c]) != "") {
    SE_Language::_preload(user_privacy_levels($level_profile_privacy[$c]));
    $privacy_options[$level_profile_privacy[$c]] = user_privacy_levels($level_profile_privacy[$c]);
  }
}

for($c=0;$c<count($level_profile_comments);$c++) {
  if(user_privacy_levels($level_profile_comments[$c]) != "") {
    SE_Language::_preload(user_privacy_levels($level_profile_comments[$c]));
    $comment_options[$level_profile_comments[$c]] = user_privacy_levels($level_profile_comments[$c]);
  }
}

// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);
$smarty->assign('blocked_users', $block_array);
$smarty->assign('actiontypes', $actiontypes_array);
$smarty->assign('privacy_options', $privacy_options);
$smarty->assign('comment_options', $comment_options);
include "footer.php";
?>