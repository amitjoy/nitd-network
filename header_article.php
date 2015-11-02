<?php
// ENSURE THIS IS BEING INCLUDED IN AN SE SCRIPT
if(!defined('SE_PAGE')) { exit(); }

//include_once "./lang/lang_".$global_lang."_article.php";
include_once "./include/class_radcodes.php";
include_once "./include/class_article.php";
include_once "./include/functions_article.php";

SE_Language::_preload_multi(11150101, 11150102);


// SET MAIN MENU VARS
if($user->user_exists != 0 || $setting[setting_permission_article] != 0) {
  $plugin_vars[menu_main] = Array('file' => 'articles.php', 'title' => 11150101);
}
// SET USER MENU VARS
if($user->level_info[level_article_allow] == 1) {
  $plugin_vars[menu_user] = Array('file' => 'user_article_settings.php', 'icon' => 'article16.gif', 'title' => 11150102);
}

// SET PROFILE MENU VARS
if($owner->level_info[level_article_allow] == 1 && $page == "profile") {

  $rc_article = new rc_article($owner->user_info[user_id]);
  $article_entries_per_page = 5;
  $sort = "article_date_start DESC";

  // GET PRIVACY LEVEL AND SET WHERE
  $article_privacy_max = $owner->user_privacy_max($user);
  $where = "(article_privacy & $article_privacy_max) AND article_approved = '1' AND article_draft = '0'";

  // GET TOTAL ENTRIES
  $total_article_entries = $rc_article->article_total($where);

  // GET ENTRY ARRAY
  $article_entries = $rc_article->article_list(0, $article_entries_per_page, $sort, $where, 1);  
  
  $smarty->assign('article_entries', $article_entries);
  $smarty->assign('total_article_entries', $total_article_entries);
  
  // SET PROFILE MENU VARS
  if(count($article_entries) > 0) {

    // DETERMINE WHERE TO SHOW ALBUMS
    $level_article_profile = explode(",", $owner->level_info[level_article_profile]);
    if(!in_array($owner->user_info[user_profile_article], $level_article_profile)) { $user_profile_article = $level_article_profile[0]; } else { $user_profile_article = $owner->user_info[user_profile_article]; }

    $user_profile_article = "side";
    
    // SHOW ALBUM IN APPROPRIATE LOCATION
    if($user_profile_article == "tab") {
      $plugin_vars[menu_profile_tab] = Array('file'=> 'profile_article_tab.tpl', 'title' => 11150101);
    } else {
      $plugin_vars[menu_profile_side] = Array('file'=> 'profile_article_side.tpl', 'title' => 11150101);
    }
  }

}

