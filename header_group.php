<?php

// ENSURE THIS IS BEING INCLUDED IN AN SE SCRIPT
defined('SE_PAGE') or exit();

// INCLUDE GROUP FILES
include "./include/class_group.php";
include "./include/functions_group.php";


// PRELOAD LANGUAGE
SE_Language::_preload(2000007);


// SET MENU VARS
if( ($user->user_exists && (int)$user->level_info['level_group_allow'] & 1) || (!$user->user_exists && $setting['setting_permission_group']) )
  $plugin_vars['menu_main'] = Array('file' => 'browse_groups.php', 'title' => 2000007);

if( (int)$user->level_info['level_group_allow'] & 2 )
  $plugin_vars['menu_user'] = Array('file' => 'user_group.php', 'icon' => 'group_group16.gif', 'title' => 2000007);


// SET WHAT'S NEW PAGE UPDATES
if( ($user->level_info['level_group_allow'] & 1) && $page == "user_home" )
{
  // GET GROUP SUBSCRIPTIONS
  $group_subscribes = Array();
  $group_subscribe_query = $database->database_query("SELECT se_groupsubscribes.groupsubscribe_time, se_groups.group_id, se_groups.group_title, count(se_groupcomments.groupcomment_id) AS total_comments FROM se_groupsubscribes LEFT JOIN se_groups ON se_groupsubscribes.groupsubscribe_group_id=se_groups.group_id LEFT JOIN se_groupcomments ON se_groups.group_id=se_groupcomments.groupcomment_group_id AND se_groupcomments.groupcomment_date>se_groupsubscribes.groupsubscribe_time WHERE se_groupsubscribes.groupsubscribe_user_id='{$user->user_info['user_id']}' GROUP BY se_groups.group_id ORDER BY se_groups.group_title");
  $total_group_subscribes = $database->database_num_rows($group_subscribe_query);
  while($subscribe_info = $database->database_fetch_assoc($group_subscribe_query))
  {
    $subscribe_info['total_photos'] = $database->database_num_rows($database->database_query("SELECT NULL FROM se_groupmedia INNER JOIN se_groupalbums ON se_groupmedia.groupmedia_groupalbum_id=se_groupalbums.groupalbum_id AND se_groupalbums.groupalbum_group_id='{$subscribe_info['group_id']}' WHERE se_groupmedia.groupmedia_date>'{$subscribe_info['groupsubscribe_time']}'"));
    $subscribe_info['total_posts'] = $database->database_num_rows($database->database_query("SELECT NULL FROM se_groupposts INNER JOIN se_grouptopics ON se_groupposts.grouppost_grouptopic_id=se_grouptopics.grouptopic_id AND se_grouptopics.grouptopic_group_id='{$subscribe_info['group_id']}' WHERE se_groupposts.grouppost_date>'{$subscribe_info['groupsubscribe_time']}'"));
    $group_subscribes[] = $subscribe_info;
  }

  // ASSIGN GROUP SUBSCRIPTION SMARY VARIABLE
  $smarty->assign('group_subscribes', $group_subscribes);
  $smarty->assign('total_group_subscribes', $total_group_subscribes);

  // SET PROFILE MENU VARS
  if( $total_group_subscribes )
  {
    $plugin_vars['menu_userhome'] = Array('file'=> 'user_home_group.tpl');
  }
}


// SET PROFILE MENU VARS
if( ($owner->level_info['level_group_allow'] & 2) && $page == "profile")
{
  $group = new se_group($owner->user_info['user_id']);
  $sort_by = "se_groupmembers.groupmember_rank DESC, se_groups.group_title";
  $where = "(se_groupmembers.groupmember_status='1')";

  // GET TOTAL GROUPS
  $total_groups = $group->group_total($where);

  // GET GROUPS ARRAY
  $groups = $group->group_list(0, $total_groups, $sort_by, $where);

  // ASSIGN GROUPS SMARY VARIABLE
  $smarty->assign('groups', $groups);
  $smarty->assign('total_groups', $total_groups);


  // SET PROFILE MENU VARS
  if( $total_groups )
  {
    $plugin_vars['menu_profile_tab'] = "";
    $plugin_vars['menu_profile_side'] = Array('file'=> 'profile_group.tpl', 'title' => 2000007, 'name' => 'group');
  }
}


// Use new template hooks
if( is_a($smarty, 'SESmarty') )
{
  $plugin_vars['uses_tpl_hooks'] = TRUE;
  
  if( !empty($plugin_vars['menu_main']) )
    $smarty->assign_hook('menu_main', $plugin_vars['menu_main']);
  
  if( !empty($plugin_vars['menu_user']) )
    $smarty->assign_hook('menu_user_apps', $plugin_vars['menu_user']);
  
  if( !empty($plugin_vars['menu_profile_side']) )
    $smarty->assign_hook('profile_side', $plugin_vars['menu_profile_side']);
  
  if( !empty($plugin_vars['menu_profile_tab']) )
    $smarty->assign_hook('profile_tab', $plugin_vars['menu_profile_tab']);
  
  if( !empty($plugin_vars['menu_userhome']) )
    $smarty->assign_hook('user_home', $plugin_vars['menu_userhome']);

  if( strpos($page, 'group')!==FALSE || $page=="profile" )
    $smarty->assign_hook('styles', './templates/styles_group.css');
}



// SET HOOKS
SE_Hook::register("se_search_do", 'search_group');
SE_Hook::register("se_user_delete", 'deleteuser_group');
SE_Hook::register("se_mediatag", 'mediatag_group');
SE_Hook::register("se_action_privacy", 'action_privacy_group');
SE_Hook::register("se_site_statistics", 'site_statistics_group');

?>