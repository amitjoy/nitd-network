<?php

$page = "home";
include "header.php";


// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if( !$user->user_exists && !$setting['setting_permission_portal'] )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}





// IF PREVIOUSLY LOGGED IN EMAIL COOKIE AVAILABLE, SET IT
$prev_email = ( isset($_COOKIE['prev_email']) ? $_COOKIE['prev_email'] : "" );





// UPDATE REFERRING URLS TABLE
update_refurls();





// NOTE: THESE FUNCTIONS ARE MOSTLY IN include/functions_general.php
// Moved for simplicity in caching the results

// GET RECENT SIGNUPS
$signup_array = recent_signups();

$smarty->assign_by_ref('signups', $signup_array);





// GET RECENT POPULAR USERS (MOST FRIENDS)
$friend_array = popular_users();

$smarty->assign_by_ref('friends', $friend_array);





// GET RECENT LOGINS
$login_array = recent_logins();

$smarty->assign_by_ref('logins', $login_array);





// GET NEWS ITEMS
$news_array = site_news();

$smarty->assign_by_ref('news', $news_array);





// GET TOTALS
$stats_array = site_statistics();

$smarty->assign_by_ref('site_statistics', $stats_array);

// Backwards compatibility with old home.tpl template
$total_members = ( isset($stats_array['members']['stat']) ? $stats_array['members']['stat'] : 0 );
$total_friends = ( isset($stats_array['friends']['stat']) ? $stats_array['friends']['stat'] : 0 );
$total_comments = ( isset($stats_array['comments']['stat']) ? $stats_array['comments']['stat'] : 0 );

$smarty->assign('total_members', $total_members);
$smarty->assign('total_friends', $total_friends);
$smarty->assign('total_comments', $total_comments);





// Get online users
$online_array = online_users();

$smarty->assign_by_ref('online_users', $online_array);





// Get actions feed - Has code in it that is preventing direct caching
$actions_array = $actions->actions_display(0, $setting['setting_actions_actionsperuser']);

$smarty->assign_by_ref('actions', $actions_array);





// ASSIGN SMARTY VARIABLES AND INCLUDE FOOTER
$smarty->assign('prev_email', $prev_email);
$smarty->assign('ip', $_SERVER['REMOTE_ADDR']);

include "footer.php";
?>