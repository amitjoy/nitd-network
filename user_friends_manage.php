<?php

$page = "user_friends_manage";
include "header.php";

$task = ( isset($_POST['task']) ? $_POST['task'] : ( isset($_GET['task']) ? $_GET['task'] : NULL ) ); 

// INITIALIZE VARS
$result = 0;
$friendship_allowed = 1;

// GET FRIEND TYPES IF AVAILABLE
$connection_types = array_filter(explode("<!>", trim($setting['setting_connection_types'])));

// ENSURE CONECTIONS ARE ALLOWED FOR THIS USER
switch( $setting['setting_connection_allow'] )
{
  // ANYONE CAN INVITE EACH OTHER TO BE FRIENDS
  case "3": break;
  // CHECK IF IN SAME SUBNETWORK
  case "2": if( $user->user_info['user_subnet_id'] != $owner->user_info['user_subnet_id'] ) { $friendship_allowed = 0; } break;
  // CHECK IF FRIEND OF FRIEND
  case "1": if( !$user->user_friend_of_friend($owner->user_info['user_id']) ) { $friendship_allowed = 0; } break;
  // NO ONE CAN INVITE EACH OTHER TO BE FRIENDS
  case "0": $friendship_allowed = 0; break;
}

// Secure action
if( empty($_POST['user']) && in_array($task, array('edit_do', 'reject_do', 'cancel_do', 'remove_do', 'add_do')) )
{
  echo 'tsk tsk tsk';
  exit();
}

// NO OWNER, FRIENDSHIP NOT ALLOWED, USER IS TRYING TO FRIEND THEMSELVES, USER IS ON BLOCKLIST
if( !$owner->user_exists || !$friendship_allowed || $owner->user_info['user_id'] == $user->user_info['user_id'] || $owner->user_blocked($user->user_info['user_id']) )
  exit();


// DECIDE WHICH PAGE TO SHOW
if( $owner->user_friended($user->user_info['user_id'], 0) )
{
  if($task == "reject")
  {
    $subpage = "reject";
  }
  elseif($setting['setting_connection_framework'] == 1 || $setting['setting_connection_framework'] == 3 || $user->user_friended($owner->user_info['user_id']))
  {
    $subpage = "confirm";
  }
  else
  {
    $subpage = "add";
  }
}
elseif($user->user_friended($owner->user_info['user_id']))
{
  if($task == "remove")
  {
    $subpage = "remove";
  }
  else
  {
    $subpage = "edit";
    $friendship = $database->database_fetch_assoc($database->database_query("SELECT friend_id, friend_type FROM se_friends WHERE friend_user_id2='{$owner->user_info['user_id']}' AND friend_user_id1='{$user->user_info['user_id']}' AND friend_status='1'"));
    $friend_explain = $database->database_fetch_assoc($database->database_query("SELECT friendexplain_id, friendexplain_body FROM se_friendexplains WHERE friendexplain_friend_id='{$friendship['friend_id']}'"));
    
    $friend_type = $friendship['friend_type'];
    $friend_type_other = $friendship['friend_type'];
    $friend_explain = $friend_explain['friendexplain_body'];
    
    if(count($connection_types) == 0) { $friend_type = ""; }
    if($setting['setting_connection_other'] == 0) { $friend_type_other = ""; }
    if($setting['setting_connection_explain'] == 0) { $friend_explain = ""; }
    if(in_array($friend_type, $connection_types)) { $friend_type_other = ""; }
  }
}
elseif($user->user_friended($owner->user_info['user_id'], 0))
{
  $subpage = "cancel";
}
else
{
  $subpage = "add";
}



// EDIT FRIEND DETAILS
if( $task == "edit_do" )
{
  $friend_type = $_POST['friend_type'];
  $friend_type_other = censor($_POST['friend_type_other']);
  $friend_explain = censor($_POST['friend_explain']);
  
  if(count($connection_types) == 0) { $friend_type = ""; }
  if($setting['setting_connection_other'] == 0) { $friend_type_other = ""; }
  if($setting['setting_connection_explain'] == 0) { $friend_explain = ""; }

  if(in_array($friend_type, $connection_types)) { $friend_type_other = ""; }
  if($friend_type == "other_friendtype") { $friend_type = ""; }
  if(trim($friend_type_other) != "") { $friend_type = $friend_type_other; }

  $database->database_query("UPDATE se_friends SET friend_type='{$friend_type}' WHERE friend_id='{$friendship['friend_id']}'");
  $database->database_query("UPDATE se_friendexplains SET friendexplain_body='{$friend_explain}' WHERE friendexplain_friend_id='{$friendship['friend_id']}'");    

  $status = "remove";
  $result = 923;
}


// REJECT FRIEND REQUEST
elseif( $task == "reject_do" )
{
  $owner->user_friend_remove($user->user_info['user_id']);
  $database->database_query("DELETE FROM se_notifys WHERE notify_user_id='{$user->user_info['user_id']}' AND notify_notifytype_id='1' AND notify_object_id='{$owner->user_info['user_id']}'");
  $status = "remove";
  $result = 914;
}


// CANCEL FRIEND REQUEST
elseif( $task == "cancel_do" )
{
  $user->user_friend_remove($owner->user_info['user_id']);
  $database->database_query("DELETE FROM se_notifys WHERE notify_user_id='{$owner->user_info['user_id']}' AND notify_notifytype_id='1' AND notify_object_id='{$user->user_info['user_id']}'");
  $status = "remove";
  $result = 920;
}


// UNFRIEND USER
elseif( $task == "remove_do" )
{
  $user->user_friend_remove($owner->user_info['user_id']);
  $status = "add";
  $result = 890;
}


// CONFIRM OR ADD FRIEND
elseif( $task == "add_do" )
{
  $friend_type = $_POST['friend_type'];
  $friend_type_other = censor($_POST['friend_type_other']);
  $friend_explain = censor($_POST['friend_explain']);
  
  if(count($connection_types) == 0) { $friend_type = ""; }
  if($setting['setting_connection_other'] == 0) { $friend_type_other = ""; }
  if($setting['setting_connection_explain'] == 0) { $friend_explain = ""; }

  if($friend_type == "other_friendtype") { $friend_type = ""; }
  if(trim($friend_type_other) != "") { $friend_type = $friend_type_other; }

  // DETERMINE FRIENDSHIP FRAMEWORK
  switch($setting['setting_connection_framework'])
  {
    case "0": $direction = 2; $friend_status = 0; $status = "pending"; $result = 878; break;
    case "1": $direction = 1; $friend_status = 0; $status = "pending"; $result = 878; break;
    case "2": $direction = 2; $friend_status = 1; $status = "remove"; $result = 879; break;
    case "3": $direction = 1; $friend_status = 1; $status = "remove"; $result = 879; break;      
  }

  // IF CONFIRMING AN EXISTING FRIEND REQUEST
  if($owner->user_friended($user->user_info['user_id'], 0))
  { 
    // CONFIRM FRIENDSHIP
    $database->database_query("UPDATE se_friends SET friend_status='1' WHERE friend_user_id1='{$owner->user_info['user_id']}' AND friend_user_id2='{$user->user_info['user_id']}' AND friend_status='0'");
    
    // INSERT ACTION
    $actions->actions_add($owner, "addfriend", Array($owner->user_info['user_username'], $owner->user_displayname, $user->user_info['user_username'], $user->user_displayname), Array(), 0, false, "user", $owner->user_info['user_id'], $owner->user_info['user_privacy']);
    
    // DELETE NOTIFICATION
    $database->database_query("DELETE FROM se_notifys WHERE notify_user_id='{$user->user_info['user_id']}' AND notify_notifytype_id='1' AND notify_object_id='{$owner->user_info['user_id']}'");
    
    // IF TWO-WAY CONNECTION, INSERT OTHER DIRECTION
    if( $direction == 2 && !$user->user_friended($owner->user_info['user_id']) )
    { 
      $user->user_friend_add($owner->user_info['user_id'], 1, $friend_type, $friend_explain); 
      $actions->actions_add($user, "addfriend", Array($user->user_info['user_username'], $user->user_displayname, $owner->user_info['user_username'], $owner->user_displayname), Array(), 0, false, "user", $user->user_info['user_id'], $user->user_info['user_privacy']);
      $status = "remove";
      $result = 879;
    }
    elseif( $user->user_friended($owner->user_info['user_id']) )
    {
      $status = "remove";
      $result = 886;
    }
    else
    {
      $status = "add";
      $result = 886;
    }
  }
  
  // CREATING A NEW FRIENDSHIP
  else
  {
    // CREATE FRIENDSHIP
    $user->user_friend_add($owner->user_info['user_id'], $friend_status, $friend_type, $friend_explain);
    
    // INSERT ACTION
    if($friend_status == 1)
    { 
      $actions->actions_add($user, "addfriend", Array($user->user_info['user_username'], $user->user_displayname, $owner->user_info['user_username'], $owner->user_displayname), Array(), 0, false, "user", $user->user_info['user_id'], $user->user_info['user_privacy']); 
    }
    else
    {
      $notify->notify_add($owner->user_info['user_id'], 'friendrequest', $user->user_info['user_id']);
    }
    
    // IF TWO-WAY CONNECTION AND NON-CONFIRMED, INSERT OTHER DIRECTION AND ACTION
    if( $direction == 2 && $friend_status == 1 && !$owner->user_friended($user->user_info['user_id']) )
    { 
      $owner->user_friend_add($user->user_info['user_id'], $friend_status, '', ''); 
      $actions->actions_add($owner, "addfriend", Array($owner->user_info['user_username'], $owner->user_displayname, $user->user_info['user_username'], $user->user_displayname), Array(), 0, false, "user", $owner->user_info['user_id'], $owner->user_info['user_privacy']);
    }
    
    // SEND FRIENDSHIP EMAIL
    $owner->user_settings();
    if( $owner->usersetting_info['usersetting_notify_friendrequest'] )
    {
      send_systememail('friendrequest', $owner->user_info['user_email'], Array($owner->user_displayname, $user->user_displayname, "<a href=\"".$url->url_base."login.php\">".$url->url_base."login.php</a>"));
    }
  }
  
  // UPDATE STATS
  update_stats("friends");
}  




// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('result', $result);
$smarty->assign('status', $status);
$smarty->assign('subpage', $subpage);
$smarty->assign('connection_types', $connection_types);
$smarty->assign('friend_type', $friend_type);
$smarty->assign('friend_type_other', $friend_type_other);
$smarty->assign('friend_explain', $friend_explain);
include "footer.php";
?>