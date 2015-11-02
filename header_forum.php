<?php

// ENSURE THIS IS BEING INCLUDED IN AN SE SCRIPT
defined('SE_PAGE') or exit();

// INCLUDE FORUM FILES
include "./include/class_forum.php";
include "./include/functions_forum.php";


// PRELOAD LANGUAGE
SE_Language::_preload(6000056);

// LOAD FORUM CLASS
$forum = new se_forum();

// SET MENU VARS
if($setting['setting_forum_status'] != 0) {
  $plugin_vars['menu_main'] = Array('file' => 'forum.php', 'title' => 6000056);
}

// SET PROFILE MENU VARS
if($setting['setting_forum_status'] != 0 && $page == "profile") {

  // SET LEVEL ID
  if($user->user_exists) { $level_id = $user->level_info[level_id]; } else { $level_id = 0; }

  // GET A LIST OF FORUM IDs THAT USER CAN VIEW
  $forum_ids = Array();
  $forums = $database->database_query("SELECT se_forums.forum_id FROM se_forums LEFT JOIN se_forumlevels ON se_forums.forum_id=se_forumlevels.forumlevel_forum_id AND se_forumlevels.forumlevel_level_id='$level_id' LEFT JOIN se_forummoderators ON se_forums.forum_id=se_forummoderators.forummoderator_forum_id AND se_forummoderators.forummoderator_user_id='{$user->user_info[user_id]}' WHERE se_forumlevels.forumlevel_forum_id IS NOT NULL OR se_forummoderators.forummoderator_forum_id IS NOT NULL");
  while($forum_info = $database->database_fetch_assoc($forums)) {
    $forum_ids[] = $forum_info[forum_id];
  }


  // GET TOTAL POSTS FOR OWNER (THAT USER CAN SEE)
  if(count($forum_ids) == 0) {
    $total_posts = 0;
  } else {
    $total_posts = $database->database_num_rows($database->database_query("SELECT NULL FROM se_forumposts LEFT JOIN se_forumtopics ON se_forumposts.forumpost_forumtopic_id=se_forumtopics.forumtopic_id WHERE se_forumposts.forumpost_authoruser_id='{$owner->user_info[user_id]}' AND se_forumposts.forumpost_deleted='0' AND se_forumtopics.forumtopic_forum_id IN ('".implode("', '", $forum_ids)."')"));
  }

  // ASSIGN TOTAL POSTS IN SMARTY
  $smarty->assign('total_posts', $total_posts);

  // DISPLAY TAB ONLY IF USER HAS POSTED
  if($total_posts) {
  
    // MAKE POST PAGES
    if(isset($_POST['p_forum'])) { $p_forum = $_POST['p_forum']; } elseif(isset($_GET['p_forum'])) { $p_forum = $_GET['p_forum']; } else { $p_forum = 1; }
    $posts_per_page = 10;
    $page_vars_forum = make_page($total_posts, $posts_per_page, $p_forum);

    // GET POST ARRAY
    $post_array = Array();
    $posts = $database->database_query("SELECT se_forumtopics.forumtopic_forum_id, se_forumtopics.forumtopic_subject, se_forumposts.forumpost_id, se_forumposts.forumpost_forumtopic_id, se_forumposts.forumpost_date, se_forumposts.forumpost_excerpt FROM se_forumposts LEFT JOIN se_forumtopics ON se_forumposts.forumpost_forumtopic_id=se_forumtopics.forumtopic_id WHERE se_forumposts.forumpost_authoruser_id='{$owner->user_info[user_id]}' AND se_forumposts.forumpost_deleted='0' AND se_forumtopics.forumtopic_forum_id IN ('".implode("', '", $forum_ids)."') ORDER BY se_forumposts.forumpost_id DESC LIMIT $page_vars_forum[0], $posts_per_page");
    while($post_info = $database->database_fetch_assoc($posts)) {

	if(strlen($post_info[forumpost_excerpt]) > 47) { $post_info[forumpost_excerpt] = substr($post_info[forumpost_excerpt], 0, 47)."..."; }
	$post_array[] = $post_info;

    }

    // SET SMARTY VARS
    $smarty->assign('forum_posts', $post_array);
    $smarty->assign('maxpage_forum', $page_vars_forum[2]);
    $smarty->assign('p_start_forum', $page_vars_forum[0]+1);
    $smarty->assign('p_end_forum', $page_vars_forum[0]+count($posts));
    $smarty->assign('p_forum', $page_vars_forum[1]);

    // SET PROFILE MENU VARS
    $plugin_vars['menu_profile_tab'] = Array('file'=> 'profile_forum.tpl', 'title' => 6000070, 'name' => 'forum');
    $plugin_vars['menu_profile_side'] = "";
  }
}









// Use new template hooks
if( is_a($smarty, 'SESmarty') )
{
  $plugin_vars['uses_tpl_hooks'] = TRUE;
  
  if( !empty($plugin_vars['menu_main']) )
    $smarty->assign_hook('menu_main', $plugin_vars['menu_main']);
   
  if( !empty($plugin_vars['menu_profile_side']) )
    $smarty->assign_hook('profile_side', $plugin_vars['menu_profile_side']);
  
  if( !empty($plugin_vars['menu_profile_tab']) )
    $smarty->assign_hook('profile_tab', $plugin_vars['menu_profile_tab']);
  
  if( strpos($page, 'forum')!==FALSE || $page=="profile" )
    $smarty->assign_hook('styles', './templates/styles_forum.css');
}



// SET HOOKS
SE_Hook::register("se_search_do", 'search_forum');
SE_Hook::register("se_user_delete", 'deleteuser_forum');
SE_Hook::register("se_action_privacy", 'action_privacy_forum');
SE_Hook::register("se_site_statistics", 'site_statistics_forum');

?>