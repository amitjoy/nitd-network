<?php

// ENSURE THIS IS BEING INCLUDED IN AN SE SCRIPT
defined('SE_PAGE') or exit();


// INCLUDE EVENT FILES
include "./include/class_event.php";
include "./include/functions_event.php";


// PRELOAD LANGUAGE
SE_Language::_preload(3000007);


// SET MENU VARS
if( ($user->user_exists && ($user->level_info['level_event_allow'] & 1)) || (!$user->user_exists && $setting['setting_permission_event']) )
  $plugin_vars['menu_main'] = Array('file' => 'browse_events.php', 'title' => 3000007);

if( ($user->level_info['level_event_allow'] & 1) )
  $plugin_vars['menu_user'] = Array('file' => 'user_event.php', 'icon' => 'event_event16.gif', 'title' => 3000007);


// SET PROFILE MENU VARS
if( ($owner->level_info['level_event_allow'] & 6) && $page=="profile" )
{
  // START CLASSIFIED
  $event = new se_event($owner->user_info['user_id']);
  $events_per_page = 5;
  $sort = "event_date_start DESC";

  // GET PRIVACY LEVEL AND SET WHERE
  $privacy_max = $owner->user_privacy_max($user);
  $where = "(event_privacy & $privacy_max)";

  // GET TOTAL LISTINGS
  $total_events = $event->event_total($where);

  // GET LISTING ARRAY
  $events = $event->event_list(0, $events_per_page, $sort, $where);

  // ASSIGN ENTRIES SMARY VARIABLE
  $smarty->assign_by_ref('events', $events);
  $smarty->assign('total_events', $total_events);
  
  if( $total_events )
  {
    $plugin_vars['menu_profile_tab'] = array('file'=> 'profile_event_list.tpl', 'title' => 3000007, 'name' => 'event');
    $plugin_vars['menu_profile_side'] = Array('file'=> 'profile_event.tpl', 'title' => 3000007, 'name' => 'event');
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
  
  if( strpos($page, 'event')!==FALSE || $page=="profile" )
    $smarty->assign_hook('styles', './templates/styles_event.css');
}
  



// SET HOOKS
SE_Hook::register("se_search_do", 'search_event');
SE_Hook::register("se_user_delete", 'deleteuser_event');
SE_Hook::register("se_mediatag", 'mediatag_event');
SE_Hook::register("se_action_privacy", 'action_privacy_event');
SE_Hook::register("se_site_statistics", 'site_statistics_event');

?>