<?php
// ENSURE THIS IS BEING INCLUDED IN AN SE SCRIPT
if(!defined('SE_PAGE')) { exit(); }

//include_once "./lang/lang_".$global_lang."_education.php";
include_once "./include/class_radcodes.php";
include_once "./include/class_education.php";
include_once "./include/functions_education.php";

SE_Language::_preload_multi(11040101, 11040102, 11040103);
SE_Language::load();

// SET MAIN MENU VARS
//$plugin_vars[menu_main] = Array('file' => 'search_education.php', 'title' => 11020106);

// SET USER MENU VARS
if($user->level_info[level_education_allow] == 1) {
  $plugin_vars[menu_user] = Array('file' => 'user_education.php', 'icon' => 'education16.gif', 'title' => 11040102);
}

// SET PROFILE MENU VARS
if($owner->level_info[level_education_allow] == 1 && $page == "profile") {

  $rc_education = new rc_education($owner->user_info[user_id]);
  $educations = $rc_education->get_educations();
  $educations = $rc_education->build_searchable_fields($educations);
  $total_educations = count($educations);

  $smarty->assign('educations', $educations);
  $smarty->assign('total_educations', $total_educations);
  
  // SET PROFILE MENU VARS
  if($total_educations > 0) {

    // DETERMINE WHERE TO SHOW ALBUMS
    $level_education_profile = explode(",", $owner->level_info[level_education_profile]);
    if(!in_array($owner->user_info[user_profile_education], $level_education_profile)) { $user_profile_education = $level_education_profile[0]; } else { $user_profile_education = $owner->user_info[user_profile_education]; }

    $user_profile_education = "tab"; // default to tab for now .. v3.03
    
    // SHOW ALBUM IN APPROPRIATE LOCATION
    if($user_profile_education == "tab") {
      $plugin_vars[menu_profile_tab] = Array('file'=> 'profile_education_tab.tpl', 'title' => 11040101);
    } else {
      $plugin_vars[menu_profile_side] = Array('file'=> 'profile_education_side.tpl', 'title' => 11040101);
    }
  }

}


