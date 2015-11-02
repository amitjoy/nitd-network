<?php

$page = "event_ajax";
include "header.php";

$task     = ( !empty($_POST['task'])      ? $_POST['task']      : NULL );
$view     = ( !empty($_POST['view'])      ? $_POST['view']      : NULL );
$date     = ( !empty($_POST['date'])      ? $_POST['date']      : NULL );
$user_id  = ( !empty($_POST['user_id'])   ? $_POST['user_id']   : NULL );
$event_id = ( !empty($_POST['event_id'])  ? $_POST['event_id']  : NULL );
$invites  = ( !empty($_POST['invites'])   ? $_POST['invites']   : NULL );

$event = new se_event(( $user->user_exists ? $user->user_info['user_id']  : NULL ), $event_id);



// LIST
if( $task=="eventcalendar" )
{
  $eventlist = $event->event_calendar_generate($date, $view);
  
  echo json_encode($eventlist);
  
  exit();
}


// INFO
elseif( $task=="eventinfo" )
{
  if( !$event->event_exists )
  {
    echo json_encode(array(
      'result' => FALSE
    ));
  }
  else
  {
    echo json_encode(array(
      'result' => TRUE,
      'event_info' => $event->event_info,
      'eventmember_info' => $event->eventmember_info
    ));
  }
  
  exit();
}


// DELETE
elseif( $task=="eventdelete" )
{
  if( $user->user_exists && $event->event_delete() )
    echo json_encode(array('result' => TRUE));
  else
    echo json_encode(array('result' => FALSE, 'error' => SE_Language::get($event->is_error)));
  
  exit();
}


// JOIN
elseif( $task=="eventjoin" || $task=="eventrequestsend" )
{
  if( $user->user_exists && $event->event_join() )
    echo json_encode(array('result' => TRUE));
  else
    echo json_encode(array('result' => FALSE, 'error' => SE_Language::get($event->is_error)));
  
  exit();
}


// REMOVE
elseif( $task=="eventleave" || $task=="eventrequestcancel" )
{
  if( $user->user_exists && $event->event_leave() )
    echo json_encode(array('result' => TRUE));
  else
    echo json_encode(array('result' => FALSE, 'error' => SE_Language::get($event->is_error)));
  
  exit();
}


// RSVP
elseif( $task=="eventrsvp" )
{
  $event_rsvp = ( !empty($_POST['event_rsvp'])  ? $_POST['event_rsvp']  : NULL );
  
  // Try to join if not a member
  if( $user->user_exists && !$event->is_member && (!$event->event_info['event_inviteonly'] || ($event->event_info['event_inviteonly'] && !empty($event->eventmember_info['eventmember_approved']))) )
  {
    $event->event_join();
  }
  
  if( $user->user_exists && $event->event_rsvp($event_rsvp) )
    echo json_encode(array('result' => TRUE));
  else
    echo json_encode(array('result' => FALSE, 'error' => SE_Language::get($event->is_error)));
  
  exit();
}


// MEMBER ACCEPT
elseif( $task=="eventmemberaccept" || $task=="eventmemberapprove" )
{
  if( $user->user_exists && $event->event_member_approve($user_id) )
    echo json_encode(array('result' => TRUE));
  else
    echo json_encode(array('result' => FALSE, 'error' => SE_Language::get($event->is_error)));
  
  exit();
}


// MEMBER REJECT
elseif( $task=="eventmemberreject" )
{
  if( $user->user_exists && $event->event_member_reject($user_id) )
    echo json_encode(array('result' => TRUE));
  else
    echo json_encode(array('result' => FALSE, 'error' => SE_Language::get($event->is_error)));
  
  exit();
}


// MEMBER DELETE
elseif( $task=="eventmemberdelete" )
{
  if( $user->user_exists && $event->event_member_remove($user_id) )
    echo json_encode(array('result' => TRUE));
  else
    echo json_encode(array('result' => FALSE, 'error' => SE_Language::get($event->is_error)));
  
  exit();
}


// MEMBER CANCEL
elseif( $task=="eventmembercancel" )
{
  if( $user->user_exists && $event->event_member_cancel($user_id) )
    echo json_encode(array('result' => TRUE));
  else
    echo json_encode(array('result' => FALSE, 'error' => SE_Language::get($event->is_error)));
  
  exit();
}


// MEMBER INVITE
elseif( $task=="eventmemberinvite" )
{
  if( $user->user_exists && is_array($invites) && !empty($invites) && ($invites_sent=$event->event_member_invite($invites)) )
    echo json_encode(array('result' => TRUE, 'invites_sent' => $invites_sent));
  else
    echo json_encode(array('result' => FALSE, 'error' => SE_Language::get($event->is_error)));
  
  exit();
}


// GET FRIENDS
elseif( $task=="getfriends" )
{
  $results = array();
  //$sql = "SELECT user_id, user_username, user_fname, user_lname FROM se_friends LEFT JOIN se_users ON se_friends.friend_user_id2=se_users.user_id LEFT JOIN se_levels ON se_users.user_level_id=se_levels.level_id LEFT JOIN se_eventmembers ON se_users.user_id=se_eventmembers.eventmember_user_id AND se_eventmembers.eventmember_event_id={$event->event_info[event_id]} WHERE se_friends.friend_status=1 AND se_friends.friend_user_id1='{$user->user_info['user_id']}' AND se_eventmembers.eventmember_id IS NULL ORDER BY user_fname, user_lname, user_username";
  $sql = "SELECT user_id, user_username, user_fname, user_lname FROM se_friends LEFT JOIN se_users ON se_friends.friend_user_id2=se_users.user_id LEFT JOIN se_levels ON se_users.user_level_id=se_levels.level_id LEFT JOIN se_eventmembers ON se_users.user_id=se_eventmembers.eventmember_user_id AND se_eventmembers.eventmember_event_id={$event->event_info[event_id]} WHERE (se_levels.level_event_allow & 2) AND se_friends.friend_status=1 AND se_friends.friend_user_id1='{$user->user_info['user_id']}' AND se_eventmembers.eventmember_id IS NULL ORDER BY user_fname, user_lname, user_username";
  $resource = $database->database_query($sql);
  
  while( $friend_info=$database->database_fetch_assoc($resource) )
  {
    $friend = new se_user();
    $friend->user_info['user_id']       = $friend_info['user_id'];
    $friend->user_info['user_username'] = $friend_info['user_username'];
    $friend->user_info['user_fname']    = $friend_info['user_fname'];
    $friend->user_info['user_lname']    = $friend_info['user_lname'];
    $friend->user_displayname();
    
    $results[$friend_info['user_id']] = $friend->user_displayname;
  }

  // OUTPUT JSON
  header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
  header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
  header ("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
  header ("Pragma: no-cache"); // HTTP/1.0
  header ("Content-Type: application/json");
  
  echo json_encode(array(
    'result'  => TRUE,
    'friends' => &$results
  ));
  exit();
}


// GET FRIENDS
elseif( $task=="getfiles" )
{
  // GET VARS
  $p    = ( !empty($_POST['p'])   ? $_POST['p']   : 1 );
  $cpp  = ( !empty($_POST['cpp']) ? $_POST['cpp'] : 1 );
  
  // GET EVENT ALBUM INFO
  $sql = "SELECT eventalbum_id,eventalbum_totalfiles FROM se_eventalbums WHERE eventalbum_event_id='".$event->event_info['event_id']."' LIMIT 1";
  $resource = $database->database_query($sql);
  $eventalbum_info = $database->database_fetch_assoc($resource);
  $total_files = $eventalbum_info['eventalbum_totalfiles'];
  
  // MAKE FILE PAGES AND GET FILE ARRAY
  $page_vars = make_page($total_files, $cpp, $p);
  $event_files = $event->event_media_list($page_vars[0], $cpp, NULL, NULL);
  
  // CONSTRUCT JSON RESPONSE
  echo json_encode(array(
    'total_files' => (int) $total_files,
    'maxpage'     => (int) $page_vars[2],
    'p_start'     => (int) ($page_vars[0]+1),
    'p_end'       => (int) ($page_vars[0]+count($event_files)),
    'p'           => (int) $page_vars[1],
    'files'       => &$event_files
  ));
  
  exit();
}


?>