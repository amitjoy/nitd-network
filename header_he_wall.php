<?php


defined('SE_PAGE') or exit();

include_once "./include/class_he_database.php";
include_once "./include/class_he_wall.php";
include_once "./include/class_he_upload.php";
include_once "./include/functions_he_wall.php";


if ( in_array($page, array( 'wall_action', 'profile', 'user_home', 'home', 'group', 'network', 'pages' )) )
{
    $smarty->assign('he_wall_page', true);
}

if ( in_array($page, array( 'wall_action', 'profile', 'user_home', 'group', 'network', 'pages' )) )
{
    $smarty->assign('he_wall_show_video_player', true);
}

if( $setting['setting_he_wall_guest_view'] || $user->level_info['level_wall_allowed'] == true )
{
    // Use template hooks
    if( is_a($smarty, 'SESmarty') && $page == 'profile' )
    {
      $plugin_vars['menu_profile_tab'] = array( 'file'=> 'profile_he_wall_tab.tpl', 'title' => 690706002, 'name' => 'wall' );
    }
    
    if ( $page == 'profile' )
    {
        SE_Hook::register('se_footer', 'he_wall_recent_activity');
    }
    elseif ( $page == 'group' )
    {
        $smarty->assign('he_wall_group_page', 1);
        SE_Hook::register('se_footer', 'he_wall_recent_activity');
    }
    
    SE_Hook::register("se_user_delete", 'he_wall_delete_user');
    
    if ( !defined(SE_PAGE_AJAX) )
    {
        delete_he_wall_action();
    }

    delete_he_wall_action_info();
}

$smarty->register_function('he_wall_display', 'frontend_he_wall_display');
$smarty->register_modifier('he_wall_format_text', 'smarty_modifier_he_wall_format_text');
?>