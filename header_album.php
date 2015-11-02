<?php

// ENSURE THIS IS BEING INCLUDED IN AN SE SCRIPT
defined('SE_PAGE') or exit();


// INCLUDE ALBUM FILES
include "./include/class_album.php";
include "./include/functions_album.php";


// PRELOAD LANGUAGE
SE_Language::_preload_multi(1000007, 1000123, 1000137);


// SET MENU VARS
if( ($user->user_exists && $user->level_info['level_album_allow']) || (!$user->user_exists && $setting['setting_permission_album']) )
  $plugin_vars['menu_main'] = Array('file' => 'browse_albums.php', 'title' => 1000123);

if( $user->user_exists && $user->level_info['level_album_allow'] )
  $plugin_vars['menu_user'] = Array('file' => 'user_album.php', 'icon' => 'album_album16.gif', 'title' => 1000007);


// SET PROFILE MENU VARS
if( $owner->level_info['level_album_allow'] && $page == "profile" )
{
  // START ALBUM
  $album = new se_album($owner->user_info['user_id']);
  $sort = "album_id DESC";

  // GET PRIVACY LEVEL AND SET WHERE
  $album_privacy_max = $owner->user_privacy_max($user);
  $where = "(album_privacy & $album_privacy_max)";

  // GET TOTAL ALBUMS
  $total_albums = $album->album_total($where);

  // GET ALBUM ARRAY
  $albums = $album->album_list(0, $total_albums, $sort, $where);

  // ASSIGN ALBUMS SMARY VARIABLE
  $smarty->assign('albums', $albums);
  $smarty->assign('total_albums', $total_albums);

  // SET PROFILE MENU VARS
  if( $total_albums )
  {
    // DETERMINE WHERE TO SHOW ALBUMS
    $level_album_profile = explode(",", $owner->level_info['level_album_profile']);
    $user_profile_album = ( in_array($owner->user_info['user_profile_album'], $level_album_profile) ? $owner->user_info['user_profile_album'] : $level_album_profile[0] );
    
    // SHOW ALBUM IN APPROPRIATE LOCATION
    if( $user_profile_album == "tab" )
    {
      $plugin_vars['menu_profile_tab'] = Array('file'=> 'profile_album_tab.tpl', 'title' => 1000007, 'name' => 'album');
    }
    else
    {
      $plugin_vars['menu_profile_side'] = Array('file'=> 'profile_album_side.tpl', 'title' => 1000007, 'name' => 'album');
    }
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

  if( strpos($page, 'album')!==FALSE || $page=="profile" )
    $smarty->assign_hook('styles', './templates/styles_album.css');
}


// SET HOOKS
SE_Hook::register("se_search_do", 'search_album');
SE_Hook::register("se_user_delete", 'deleteuser_album');
SE_Hook::register("se_mediatag", 'mediatag_album');
SE_Hook::register("se_site_statistics", 'site_statistics_album');

?>