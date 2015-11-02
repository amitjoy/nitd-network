<?php

$page = "network";
include "header.php";

if(isset($_POST['n'])) { $n = $_POST['n']; } elseif(isset($_GET['n'])) { $n = $_GET['n']; } else { $n = $user->user_info['user_subnet_id']; }


// GET NETWORK INFO
$network = $database->database_query("SELECT * FROM se_subnets WHERE subnet_id='$n'");
if($database->database_num_rows($network) == 0)
{ 
  $n = 0;
  $network_info['subnet_id'] = 0;
  $network_info['subnet_name'] = 152;
}
else
{
  $network_info = $database->database_fetch_assoc($network);
}


// GET NEW MEMBERS ON THIS NETWORK
$signups_query = $database->database_query("SELECT user_id, user_username, user_fname, user_lname, user_photo FROM se_users WHERE user_subnet_id='$n' AND user_verified='1' AND user_enabled='1' AND user_search='1' ORDER BY user_signupdate DESC LIMIT 20");
$signup_array = Array();
while($signup = $database->database_fetch_assoc($signups_query))
{
  $signup_user = new se_user();
  $signup_user->user_info['user_id'] = $signup['user_id'];
  $signup_user->user_info['user_username'] = $signup['user_username'];
  $signup_user->user_info['user_photo'] = $signup['user_photo'];
  $signup_user->user_info['user_fname'] = $signup['user_fname'];
  $signup_user->user_info['user_lname'] = $signup['user_lname'];
  $signup_user->user_displayname();
  $signup_array[] = $signup_user;
}

// GET RECENT STATUS UPDATES
$statuses = $database->database_query("SELECT user_id, user_username, user_fname, user_lname, user_status FROM se_users WHERE user_subnet_id='$n' AND user_id<>'{$user->user_info['user_id']}' AND user_status<>'' ORDER BY user_status_date DESC LIMIT 10");
while($status = $database->database_fetch_assoc($statuses))
{
  $status_user = new se_user();
  $status_user->user_info['user_id'] = $status['user_id'];
  $status_user->user_info['user_username'] = $status['user_username'];
  $status_user->user_info['user_fname'] = $status['user_fname'];
  $status_user->user_info['user_lname'] = $status['user_lname'];
  $status_user->user_displayname();

  $statuses_array[] = Array(
    'status_user_id' => $status['user_id'],
    'status_user_username' => $status['user_username'],
    'status_user_displayname' => $status_user->user_displayname,
    'status_user_status' => $status['user_status']
  );
}


// SET GLOBAL PAGE TITLE
$global_page_title[0] = 1155;
SE_Language::_preload($network_info['subnet_name']);
SE_Language::load();
$global_page_title[1] = SE_Language::_get($network_info['subnet_name']);
$global_page_description = $global_page_title;

// ASSIGN SMARTY VARIABLES AND INCLUDE FOOTER
$smarty->assign('network', $network_info);
$smarty->assign('signups', $signup_array);
$smarty->assign('statuses', $statuses_array);
$smarty->assign('actions', $actions->actions_display(2, $setting['setting_actions_actionsperuser'], "se_users.user_subnet_id='{$network_info['subnet_id']}'"));
include "footer.php";
?>