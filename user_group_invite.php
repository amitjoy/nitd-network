<?php
$page = "user_group_invite";
include "header.php";

if(isset($_GET['group_id'])) { $group_id = $_GET['group_id']; } elseif(isset($_POST['group_id'])) { $group_id = $_POST['group_id']; } else { $group_id = 0; }
if(isset($_GET['task'])) { $task = $_GET['task']; } elseif(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = "main"; }

if( ~(int)$user->level_info['level_group_allow'] & 2 )
{
  header("Location: user_home.php");
  exit();
}

// INITIALIZE GROUP OBJECT
$group = new se_group($user->user_info['user_id'], $group_id);

// CHECK IF GROUP EXISTS
if( !$group->group_exists ) { exit(); }

// CHECK IF ALLOWED TO INVITE
if(  $group->group_info['group_invite'] && $group->user_rank<0 ) exit();
if( !$group->group_info['group_invite'] && $group->user_rank<1 ) exit();

// SET EMPTY VARS
$result = 0;
$is_error = 0;


// RETRIEVE FRIENDS NOT IN GROUP
if($task == "friends_all")
{
  // RETRIEVE ALL FRIENDS
  $results = Array();
  $friends = $database->database_query("SELECT user_id, user_username, user_fname, user_lname FROM se_friends LEFT JOIN se_users ON se_friends.friend_user_id2=se_users.user_id LEFT JOIN se_levels ON se_users.user_level_id=se_levels.level_id LEFT JOIN se_groupmembers ON se_users.user_id=se_groupmembers.groupmember_user_id AND se_groupmembers.groupmember_group_id={$group->group_info[group_id]} WHERE (se_levels.level_group_allow & 1) AND se_friends.friend_status=1 AND se_friends.friend_user_id1='{$user->user_info['user_id']}' AND se_groupmembers.groupmember_id IS NULL ORDER BY user_fname, user_lname, user_username");
  while($friend_info = $database->database_fetch_assoc($friends))
  {
    $friend = new se_user();
    $friend->user_info['user_id'] = $friend_info['user_id'];
    $friend->user_info['user_username'] = $friend_info['user_username'];
    $friend->user_info['user_fname'] = $friend_info['user_fname'];
    $friend->user_info['user_lname'] = $friend_info['user_lname'];
    $friend->user_displayname();
    
    $results[] = "{\"{$friend_info['user_id']}\": \"{$friend->user_displayname}\"}";
  }

  // CONSTRUCT JSON
  $json = "{\"friends\": [".implode(", ", $results)."]}";	

  // OUTPUT JSON
  header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
  header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
  header ("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
  header ("Pragma: no-cache"); // HTTP/1.0
  header("Content-Type: application/json");
  echo $json;
  exit();
}



// INVITE USERS
elseif($task == "invite_do")
{
  $invites = $_POST['invites'];

  $invite_query = $database->database_query("SELECT user_id, user_username, user_email, user_fname, user_lname, usersetting_notify_groupinvite FROM se_friends LEFT JOIN se_users ON se_friends.friend_user_id2=se_users.user_id LEFT JOIN se_usersettings ON se_users.user_id=se_usersettings.usersetting_user_id LEFT JOIN se_levels ON se_users.user_level_id=se_levels.level_id LEFT JOIN se_groupmembers ON se_users.user_id=se_groupmembers.groupmember_user_id AND se_groupmembers.groupmember_group_id='{$group->group_info['group_id']}' WHERE se_users.user_id IN ('".implode("', '", $invites)."') AND (se_levels.level_group_allow & 1) AND se_friends.friend_status=1 AND se_friends.friend_user_id1='{$user->user_info['user_id']}' AND se_groupmembers.groupmember_id IS NULL");
  if( $database->database_num_rows($invite_query) )
  {
    while($invite_info = $database->database_fetch_assoc($invite_query))
    {
      $friend = new se_user();
      $friend->user_info['user_id'] = $invite_info['user_id'];
    	$friend->user_info['user_username'] = $invite_info['user_username'];
    	$friend->user_info['user_fname'] = $invite_info['user_fname'];
    	$friend->user_info['user_lname'] = $invite_info['user_lname'];
      $friend->user_displayname();
      $database->database_query("
        INSERT INTO se_groupmembers (
          groupmember_user_id, 
          groupmember_group_id, 
          groupmember_status,
          groupmember_approved
        ) VALUES (
          '$invite_info[user_id]',
          '{$group->group_info['group_id']}',
          '0',
          '1'
        )
      ");
      if( $invite_info['usersetting_notify_groupinvite'] )
      {
        send_systememail('groupinvite', $invite_info['user_email'], Array($friend->user_displayname, $group->group_info['group_title'], "<a href=\"{$url->url_base}login.php\">{$url->url_base}login.php</a>"));
      }
      $notify->notify_add($invite_info['user_id'], 'groupinvite', $group->group_info['group_id'], Array(NULL,$group->group_info['group_id']),Array($group->group_info['group_title']));
    }
  }

  $result = 2000197;

}








// ASSIGN SMARTY VARIABLES AND DISPLAY EDIT INVITE PAGE
$smarty->assign('result', $result);
$smarty->assign('group', $group);
include "footer.php";
?>