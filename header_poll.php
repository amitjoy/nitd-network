<?php

// ENSURE THIS IS BEING INCLUDED IN AN SE SCRIPT
defined('SE_PAGE') or exit();

// INCLUDE POLL FILES
include "./include/class_poll.php";
include "./include/functions_poll.php";


// PRELOAD LANGUAGE
SE_Language::_preload(2500005);


// SET MAIN MENU VARS
if( (!$user->user_exists && $setting['setting_permission_poll']) || ($user->user_exists && (1 & (int)$user->level_info['level_poll_allow'])) )
  $plugin_vars['menu_main'] = array('file' => 'browse_polls.php', 'title' => 2500005);

if( $user->user_exists && (4 & (int)$user->level_info['level_poll_allow']) )
  $plugin_vars['menu_user'] = array('file' => 'user_poll.php', 'icon' => 'poll_poll16.gif', 'title' => 2500005);


// SET PROFILE MENU VARS
if( (4 & (int)$owner->level_info['level_poll_allow']) && $page=="profile" )
{
  // START poll
  $poll = new se_poll($owner->user_info['user_id']);
  $entries_per_page = 5;
  $sort = "poll_datecreated DESC";

  // GET PRIVACY LEVEL AND SET WHERE
  $privacy_max = $owner->user_privacy_max($user);
  $where = "(poll_privacy & $privacy_max)";

  // GET TOTAL ENTRIES
  $total_polls = $poll->poll_total($where);

  // GET ENTRY ARRAY
  $polls = $poll->poll_list(0, $entries_per_page, $sort, $where);

  // ASSIGN ENTRIES SMARY VARIABLE
  $smarty->assign('polls', $polls);
  $smarty->assign('total_polls', $total_polls);
  
  // SET PROFILE MENU VARS
  $plugin_vars['menu_profile_side'] = NULL;
  if( $total_polls )
  {
    $plugin_vars['menu_profile_tab'] = array('file'=> 'profile_poll.tpl', 'title' => 2500005, 'name' => 'poll');
  }
}


// Use template hooks
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

  if( strpos($page, 'poll')!==FALSE || $page=="profile" )
  {
    $smarty->assign_hook('styles', './templates/styles_poll.css');
  }
}


// SET HOOKS
SE_Hook::register("se_search_do", "search_poll");
SE_Hook::register("se_user_delete", "deleteuser_poll");
SE_Hook::register("se_site_statistics", "site_statistics_poll");

?>