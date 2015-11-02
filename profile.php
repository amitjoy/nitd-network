<?php

$page = "profile";
include "header.php";

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if($user->user_exists == 0 && $setting['setting_permission_profile'] == 0)
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

// DISPLAY ERROR PAGE IF NO OWNER
if($owner->user_exists == 0)
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 828);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

// GET VIEW AND VARS
if(isset($_POST['v'])) { $v = $_POST['v']; } elseif(isset($_GET['v'])) { $v = $_GET['v']; } else { $v = "profile"; }
if(isset($_POST['search'])) { $search = $_POST['search']; } elseif(isset($_GET['search'])) { $search = $_GET['search']; } else { $search = ""; }
if(isset($_POST['m'])) { $m = $_POST['m']; } elseif(isset($_GET['m'])) { $m = $_GET['m']; } else { $m = 0; }
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }

// VALIDATE VIEW VAR
if($v != "profile" && $v != "friends" && $v != "comments" && !array_key_exists($v, $global_plugins)) { $v = "profile"; }

// GET PRIVACY LEVEL
$privacy_max = $owner->user_privacy_max($user);
$allowed_to_view = ($privacy_max & $owner->user_info['user_privacy']);
$is_profile_private = !$allowed_to_view;

// CHECK IF USER IS ALLOWED TO COMMENT
$allowed_to_comment = ($privacy_max & $owner->user_info['user_comments']);


// UPDATE PROFILE VIEWS IF PROFILE VISIBLE
if($is_profile_private == 0 && $user->user_info['user_id'] != $owner->user_info['user_id'])
{
  $profile_viewers = "";
  if( $owner->user_info['user_saveviews'] )
  {
    $view_query = $database->database_query("SELECT profileview_viewers FROM se_profileviews WHERE profileview_user_id='{$owner->user_info['user_id']}'");
    if($database->database_num_rows($view_query) == 1)
    {
      $views = $database->database_fetch_assoc($view_query);
      $profile_viewers = $views['profileview_viewers'];
    }
    if( $user->user_exists )
    {
      $profile_viewers_array = explode(",", $profile_viewers);
      if( in_array($user->user_info['user_id'], $profile_viewers_array) )
      {
        array_splice($profile_viewers_array, array_search($user->user_info['user_id'], $profile_viewers_array), 1);
      }
      $profile_viewers_array[] = $user->user_info['user_id'];
      krsort($profile_viewers_array);
      $profile_viewers = implode(",", array_filter($profile_viewers_array));
    }
  }
  $database->database_query("INSERT INTO se_profileviews (profileview_user_id, profileview_views, profileview_viewers) VALUES ('{$owner->user_info['user_id']}', '1', '{$profile_viewers}') ON DUPLICATE KEY UPDATE profileview_views=profileview_views+1, profileview_viewers='{$profile_viewers}'");
}


// DELETE COMMENT NOTIFICATIONS IF VIEWING COMMENT PAGE
if( $v == "comments" && $user->user_info['user_id'] == $owner->user_info['user_id'])
{
  $database->database_query("DELETE FROM se_notifys WHERE notify_user_id='{$owner->user_info['user_id']}' AND notify_notifytype_id='3' AND notify_object_id='{$owner->user_info['user_id']}'");
}

// GET PROFILE COMMENTS
$comment = new se_comment('profile', 'user_id', $owner->user_info['user_id']);
$total_comments = $comment->comment_total();

// GET PROFILE FIELDS
$field = new se_field("profile", $owner->profile_info);
$field->cat_list(0, 1, 0, "profilecat_id='{$owner->user_info['user_profilecat_id']}'", "", "");



// SET WHERE CLAUSE FOR FRIEND LIST
if($search != "")
{
  $is_where = 1;
  $where = "(se_users.user_username LIKE '%$search%' OR CONCAT(se_users.user_fname, ' ', se_users.user_lname) LIKE '%$search%' OR se_users.user_email LIKE '%$search%')";
}
else
{
  $is_where = 0;
  $where = "";
}

if($m == 1 && $user->user_exists == 1)
{
  if($where != "") { $where .= " AND "; }
  $where .= "(SELECT TRUE FROM se_friends AS t1 WHERE t1.friend_user_id1='{$user->user_info['user_id']}' AND t1.friend_user_id2=se_friends.friend_user_id2)";
}

// DECIDE WHETHER TO SHOW DETAILS
$connection_types = explode("<!>", trim($setting['setting_connection_types']));
$show_details = ( count($connection_types) && $setting['setting_connection_other'] && $setting['setting_connection_explain'] );

// GET TOTAL FRIENDS
$total_friends = $owner->user_friend_total(0, 1, $is_where, $where);

// MAKE FRIEND PAGES AND GET FRIEND ARRAY
$friends_per_page = 10;
if($v == "friends") { $p_friends = $p; } else { $p_friends = 1; }
$page_vars_friends = make_page($total_friends, $friends_per_page, $p_friends);
$friends = $owner->user_friend_list($page_vars_friends[0], $friends_per_page, 0, 1, "se_users.user_username", $where, $show_details);

// GET MASTER TOTAL OF FRIENDS
$total_friends_all = $owner->user_friend_total(0);

// GET MASTER TOTAL OF ALL MUTUAL FRIENDS
$total_friends_mut = $owner->user_friend_total(0, 1, 0, "(SELECT TRUE FROM se_friends AS t1 WHERE t1.friend_user_id1='{$user->user_info['user_id']}' AND t1.friend_user_id2=se_friends.friend_user_id2)");


// GET CUSTOM PROFILE STYLE IF ALLOWED
if( $owner->level_info['level_profile_style'] && !$is_profile_private )
{ 
  $profilestyle_info = $database->database_fetch_assoc($database->database_query("SELECT profilestyle_css FROM se_profilestyles WHERE profilestyle_user_id='{$owner->user_info['user_id']}' LIMIT 1")); 
  $global_css = $profilestyle_info[profilestyle_css];
}
elseif( $owner->level_info['level_profile_style_sample'] && !$is_profile_private )
{
  $profilestyle_info = $database->database_fetch_assoc($database->database_query("SELECT stylesample_css FROM se_profilestyles LEFT JOIN se_stylesamples ON se_profilestyles.profilestyle_stylesample_id=se_stylesamples.stylesample_id WHERE profilestyle_user_id='{$owner->user_info['user_id']}' LIMIT 1")); 
  $global_css = $profilestyle_info['stylesample_css'];
}


// ENSURE CONECTIONS ARE ALLOWED FOR THIS USER AND THAT OWNER HAS NOT BLOCKED USER
$is_friend = $user->user_friended($owner->user_info['user_id']);
if( $user->user_friended($owner->user_info['user_id'], 0))
{
  $is_friend_pending = 2;
}
elseif( $owner->user_friended($user->user_info['user_id'], 0) )
{
  $is_friend_pending = 1;
}
else
{
  $is_friend_pending = 0;
}

$friendship_allowed = 1;
switch($setting['setting_connection_allow'])
{
  case "3":
    // ANYONE CAN INVITE EACH OTHER TO BE FRIENDS
    break;
  case "2":
    // CHECK IF IN SAME SUBNETWORK
    if($user->user_info['user_subnet_id'] != $owner->user_info['user_subnet_id']) { $friendship_allowed = 0; }
    break;
  case "1":
    // CHECK IF FRIEND OF FRIEND
    if($user->user_friend_of_friend($owner->user_info['user_id']) == FALSE) { $friendship_allowed = 0; }
    break;
  case "0":
    // NO ONE CAN INVITE EACH OTHER TO BE FRIENDS
    $friendship_allowed = 0;
    break;
}

if($owner->user_blocked($user->user_info['user_id'])) { $friendship_allowed = 0; }
if($is_friend) { $friendship_allowed = 1; }


// GET PHOTOS USER IS TAGGED IN
$photo_query = "";
($hook = SE_Hook::exists('se_mediatag')) ? SE_Hook::call($hook, array()) : NULL;
$total_photo_tags = $database->database_num_rows($database->database_query($photo_query));


// DETERMINE IF USER IS ONLINE
$online_users_array = online_users();
if(in_array($owner->user_info['user_username'], $online_users_array[2])) { $is_online = 1; } else { $is_online = 0; }

// GET PROFILE VIEWS
$profile_views = 0;
$view_query = $database->database_query("SELECT profileview_views, profileview_viewers FROM se_profileviews WHERE profileview_user_id='{$owner->user_info['user_id']}'");
if($database->database_num_rows($view_query) == 1)
{
  $views = $database->database_fetch_assoc($view_query);
  $profile_views = $views['profileview_views'];
}

// SET GLOBAL PAGE TITLE
$global_page_title[0] = 509;
$global_page_title[1] = $owner->user_displayname;
$global_page_description[0] = 1158;
$global_page_description[1] = $owner->user_displayname;
$global_page_description[2] = strip_tags(implode(" - ", $field->field_values));

// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('v', $v);
$smarty->assign('profile_views', $profile_views);
$smarty->assign('cats', $field->cats);
$smarty->assign('is_profile_private', $is_profile_private);
$smarty->assign('allowed_to_comment', $allowed_to_comment);
$smarty->assign('total_comments', $total_comments);
$smarty->assign('total_photo_tags', $total_photo_tags);
$smarty->assign('m', $m);
$smarty->assign('search', $search);
$smarty->assign('friends', $friends);
$smarty->assign('total_friends', $total_friends);
$smarty->assign('total_friends_all', $total_friends_all);
$smarty->assign('total_friends_mut', $total_friends_mut);
$smarty->assign('maxpage_friends', $page_vars_friends[2]);
$smarty->assign('p_start_friends', $page_vars_friends[0]+1);
$smarty->assign('p_end_friends', $page_vars_friends[0]+count($friends));
$smarty->assign('p_friends', $page_vars_friends[1]);
$smarty->assign('is_friend', $is_friend);
$smarty->assign('is_friend_pending', $is_friend_pending);
$smarty->assign('friendship_allowed', $friendship_allowed);
$smarty->assign('is_online', $is_online);
$smarty->assign('actions', $actions->actions_display(0, $setting['setting_actions_actionsonprofile'], "se_actions.action_user_id='{$owner->user_info['user_id']}'"));
include "footer.php";
?>