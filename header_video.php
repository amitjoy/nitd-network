<?php

// ENSURE THIS IS BEING INCLUDED IN AN SE SCRIPT
defined('SE_PAGE') or exit();


// INCLUDE VIDEO FILES
include "./include/class_video.php";
include "./include/functions_video.php";


// PRELOAD LANGUAGE
SE_Language::_preload(5500098);


// SET MAIN MENU VARS
if($user->user_exists && ($user->level_info['level_video_allow'] || $user->level_info['level_youtube_allow']) || (!$user->user_exists && $setting['setting_permission_video']))
  $plugin_vars['menu_main'] = Array('file' => 'browse_videos.php', 'title' => 5500098);

if( $user->user_exists && ($user->level_info['level_video_allow'] || $user->level_info['level_youtube_allow']))
  $plugin_vars['menu_user'] = Array('file' => 'user_video.php', 'icon' => 'video_video16.gif', 'title' => 5500098);


// SET PROFILE MENU VARS
if( ($owner->level_info['level_video_allow'] || $owner->level_info['level_youtube_allow']) && $page == "profile")
{
  // START VIDEO
  $video = new se_video($owner->user_info['user_id']);
  $sort = "video_id DESC";

  // GET PRIVACY LEVEL AND SET WHERE
  $video_privacy_max = $owner->user_privacy_max($user);
  $where = "video_is_converted=1 AND (video_privacy & $video_privacy_max)";

  // GET TOTAL VIDEOS
  $total_videos = $video->video_total($where);

  // GET VIDEO ARRAY
  $videos = $video->video_list(0, $total_videos, $sort, $where);

  // ASSIGN VIDEOS SMARY VARIABLE
  $smarty->assign('videos', $videos);
  $smarty->assign('total_videos', $total_videos);

  // SET PROFILE MENU VARS
  if( $total_videos )
  {
    $plugin_vars['menu_profile_tab'] = Array('file'=> 'profile_video_tab.tpl', 'title' => 5500098, 'name' => 'video');
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
  
  if( !empty($plugin_vars['menu_userhome']) )
    $smarty->assign_hook('user_home', $plugin_vars['menu_userhome']);

  if( strpos($page, 'video')!==FALSE || $page=="profile" )
    $smarty->assign_hook('styles', './templates/styles_video.css');
}


// MANAGE JOB QUEUE
if( !$setting['setting_video_cronjob'] )
{
  video_manage_jobs();
}


// SET HOOKS
SE_Hook::register("se_search_do", 'search_video');
SE_Hook::register("se_user_delete", 'deleteuser_video');
SE_Hook::register("se_site_statistics", 'site_statistics_video');

?>