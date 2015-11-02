<?php

$page = "user_home";
include "header.php";


if(isset($_GET['task'])) { $task = $_GET['task']; } elseif(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = "main"; }


// RESET PROFILE VIEWS
if($task == "resetviews")
{
  $database->database_query("UPDATE se_profileviews SET profileview_views=0, profileview_viewers='' WHERE profileview_user_id='{$user->user_info['user_id']}' LIMIT 1");
}



// POPULATE USER SETTINGS ARRAY
$user->user_settings();



// GET NEWS ITEMS
$news_array = site_news();

$smarty->assign_by_ref('news', $news_array);



// RETRIEVE VIEWS AND VIEWERS IF NECESSARY
$profile_views = 0;
$profile_viewers = Array();
$view_query = $database->database_query("SELECT profileview_views, profileview_viewers FROM se_profileviews WHERE profileview_user_id='{$user->user_info['user_id']}'");
if($database->database_num_rows($view_query) == 1)
{
  $views = $database->database_fetch_assoc($view_query);
  $profile_views = $views['profileview_views'];
  if($views['profileview_viewers'] == "") { $views['profileview_viewers'] = "''"; }
  $viewer_query = $database->database_query("SELECT user_id, user_username, user_fname, user_lname FROM se_users WHERE user_id IN ({$views['profileview_viewers']})");
  while($viewer_info = $database->database_fetch_assoc($viewer_query))
  {
    $viewer = new se_user();
    $viewer->user_info['user_id'] = $viewer_info['user_id'];
    $viewer->user_info['user_username'] = $viewer_info['user_username'];
    $viewer->user_info['user_fname'] = $viewer_info['user_fname'];
    $viewer->user_info['user_lname'] = $viewer_info['user_lname'];
    $viewer->user_displayname();
    
    // SET PROFILE VIEWERS 
    $profile_viewers[] = $viewer;
  }
  
  $profile_viewers_array = explode(",", $views['profileview_viewers']);
  usort($profile_viewers, create_function('$a,$b', 'global $profile_viewers_array; if(array_search($a->user_info["user_id"], $profile_viewers_array) == array_search($b->user_info["user_id"], $profile_viewers_array)) { return 0; } else { return (array_search($a->user_info["user_id"], $profile_viewers_array) < array_search($b->user_info["user_id"], $profile_viewers_array)) ? -1 : 1; }'));
}


// CREATE ARRAY OF ACTION TYPES
$actiontypes_array = $actions->actions_allowed();

$smarty->assign_by_ref('actiontypes', $actiontypes_array);



// GET UPCOMING BIRTHDAYS, START BY CHECKING FOR BIRTHDAY PROFILE FIELDS
$birthday_array = friends_birthdays();

$smarty->assign_by_ref('birthdays', $birthday_array);




// Get online users
$online_array = online_users();

$smarty->assign_by_ref('online_users', $online_array);





// Get actions feed - Has code in it that is preventing direct caching
$actions_array = $actions->actions_display($setting['setting_actions_visibility'], $setting['setting_actions_actionsperuser']);

$smarty->assign_by_ref('actions', $actions_array);




// ASSIGN SMARTY VARS AND INCLUDE FOOTER
$smarty->assign('profile_views', $profile_views);
$smarty->assign('profile_viewers', $profile_viewers);
$smarty->assign('total_friend_requests', $user->user_friend_total(1, 0));
include "footer.php";
?>