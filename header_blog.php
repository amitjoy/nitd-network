<?php


// ENSURE THIS IS BEING INCLUDED IN AN SE SCRIPT
defined('SE_PAGE') or exit();

// INCLUDE BLOG FILES
include "./include/class_blog.php";
include "./include/class_blog_trackback.php";
include "./include/functions_blog.php";


// PRELOAD LANGUAGE
SE_Language::_preload(1500007);


// SET MENU VARS
if( (!$user->user_exists && $setting['setting_permission_blog']) || ($user->user_exists && $user->level_info['level_blog_view']) )
  $plugin_vars['menu_main'] = array('file' => 'browse_blogs.php', 'title' => 1500007);

if( $user->user_exists && $user->level_info['level_blog_view'] )
  $plugin_vars['menu_user'] = array('file' => 'user_blog.php', 'icon' => 'blog_blog16.gif', 'title' => 1500007);


// SET PROFILE MENU VARS
if( $owner->level_info['level_blog_create'] && $page=="profile" )
{
  // START BLOG
  $blog = new se_blog($owner->user_info['user_id']);
  $entries_per_page = 5;
  $sort = "blogentry_date DESC";

  // GET PRIVACY LEVEL AND SET WHERE
  $blog_privacy_max = $owner->user_privacy_max($user);
  $where = "(blogentry_privacy & $blog_privacy_max)";

  // GET TOTAL ENTRIES
  $total_blogentries = $blog->blog_entries_total($where);

  // GET ENTRY ARRAY
  $blogentries = $blog->blog_entries_list(0, $entries_per_page, $sort, $where);

  // ASSIGN ENTRIES SMARY VARIABLE
  $smarty->assign_by_ref('blogentries', $blogentries);
  $smarty->assign('total_blogentries', $total_blogentries);


  // SET PROFILE MENU VARS
  if( $total_blogentries )
  {
    $plugin_vars['menu_profile_tab'] = Array('file'=> 'profile_blog.tpl', 'title' => 1500007, 'name' => 'blog');
    $plugin_vars['menu_profile_side'] = "";
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

  if( strpos($page, 'blog')!==FALSE || $page=="profile" )
    $smarty->assign_hook('styles', './templates/styles_blog.css');
}



// SET HOOKS
SE_Hook::register("se_search_do", 'search_blog');
SE_Hook::register("se_user_delete", 'deleteuser_blog');
SE_Hook::register("se_site_statistics", 'site_statistics_blog');

?>