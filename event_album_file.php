<?php

$page = "event_album_file";
include "header.php";


$task           = ( !empty($_POST['task'])          ? $_POST['task']          : ( !empty($_GET['task'])           ? $_GET['task']           : NULL ) );
$event_id       = ( !empty($_POST['event_id'])      ? $_POST['event_id']      : ( !empty($_GET['event_id'])       ? $_GET['event_id']       : NULL ) );
$eventmedia_id  = ( !empty($_POST['eventmedia_id']) ? $_POST['eventmedia_id'] : ( !empty($_GET['eventmedia_id'])  ? $_GET['eventmedia_id']  : NULL ) );

/*
echo $_SERVER['REQUEST_URI'];
print_r($_SERVER);
print_r($_GET);
print_r($_POST);
*/

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if( (!$user->user_exists && !$setting['setting_permission_event']) || ($user->user_exists && (1 & ~$user->level_info['level_event_allow'])) )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

// DISPLAY ERROR PAGE IF NO OWNER
$event = new se_event($user->user_info[user_id], $event_id);

if( !$event->event_exists )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 2000219);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}



// MAKE SURE MEDIA EXISTS
$sql = "SELECT se_eventmedia.*, se_eventalbums.*, se_users.user_id, se_users.user_username, se_users.user_fname, se_users.user_lname FROM se_eventmedia LEFT JOIN se_eventalbums ON se_eventmedia.eventmedia_eventalbum_id=se_eventalbums.eventalbum_id LEFT JOIN se_users ON se_eventmedia.eventmedia_user_id WHERE se_eventmedia.eventmedia_id='{$eventmedia_id}' AND se_eventalbums.eventalbum_event_id={$event->event_info[event_id]} LIMIT 1";
$resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");

if( !$database->database_num_rows($resource) )
{
  header("Location: ".$url->url_create('event', NULL, $event->event_info['event_id']));
  exit();
}

$media_info = $database->database_fetch_assoc($resource);

$uploader = new se_user();
if( $media_info['eventmedia_user_id']!=$media_info['user_id'])
{
  $uploader->user_exists = FALSE;
}
else
{
  $uploader->user_exists = TRUE;
  $uploader->user_info['user_id']       = $media_info['user_id'];
  $uploader->user_info['user_username'] = $media_info['user_username'];
  $uploader->user_info['user_fname']    = $media_info['user_fname'];
  $uploader->user_info['user_lname']    = $media_info['user_lname'];
  $uploader->user_displayname();
}

$media_info['uploader'] =& $uploader;


// GET PRIVACY LEVEL
$privacy_max = $event->event_privacy_max($user);
if($privacy_max & ~ $event->event_info['event_privacy'] )
{
  header("Location: ".$url->url_create('event', NULL, $event->event_info['event_id']));
  exit();
}


// GET MEDIA IN ALBUM FOR CAROUSEL
$media_array = Array();
$media_query = $database->database_query("SELECT eventmedia_id, eventmedia_ext, '{$event->event_info['event_id']}' AS eventalbum_event_id FROM se_eventmedia WHERE eventmedia_eventalbum_id='{$media_info['eventalbum_id']}' ORDER BY eventmedia_date DESC");
while($thismedia = $database->database_fetch_assoc($media_query)) { $media_array[$thismedia[eventmedia_id]] = $thismedia; }


// IF USER IS ALLOWED, CHECK TASK
if( $event->user_rank>=2 || ($media_info['uploader']->user_exists && $user->user_info['user_id'] == $media_info['uploader']->user_info['user_id']) )
{

  // DELETE PHOTO
  if($task == "media_delete")
  {
    $media_path = $event->event_dir($event->event_info['event_id']).$media_info['eventmedia_id'].".".$media_info['eventmedia_ext'];
    if(file_exists($media_path)) { unlink($media_path); }
    $thumb_path = $event->event_dir($event->event_info['event_id']).$media_info['eventmedia_id']."_thumb.jpg";
    if(file_exists($thumb_path)) { unlink($thumb_path); }
    $action_thumb_path = $url->url_base.substr($event->event_dir($event->event_info['event_id']), 2).$media_info['eventmedia_id']."_thumb.jpg";

    // DELETE ACTION MEDIA IF NECESSARY
    $database->database_query("DELETE FROM se_actionmedia WHERE actionmedia_path = '{$action_thumb_path}'");
 
    // DELETE MEDIA FROM DATABASE
    $database->database_query("DELETE FROM se_eventmedia, se_eventmediacomments, se_eventmediatags USING se_eventmedia LEFT JOIN se_eventmediacomments ON se_eventmedia.eventmedia_id=se_eventmediacomments.eventmediacomment_eventmedia_id LEFT JOIN se_eventmediatags ON se_eventmedia.eventmedia_id=se_eventmediatags.eventmediatag_eventmedia_id WHERE se_eventmedia.eventmedia_id='{$media_info['eventmedia_id']}'");
 
    // DECREMENT EVENT ALBUM FILES
    $sql = "UPDATE se_eventalbums SET eventalbum_totalfiles=eventalbum_totalfiles-1 WHERE eventalbum_id='{$media_info['eventmedia_eventalbum_id']}' LIMIT 1";
    $database->database_query($sql);
    
    // SEND USER TO NEXT PHOTO
    $media_keys = array_keys($media_array);
    $current_index = array_search($media_info['eventmedia_id'], $media_keys);
    if($current_index+1 == count($media_array)) { $next_index = 0; } else { $next_index = $current_index+1; }
    header("Location: ".$url->url_create('event_media', NULL, $event->event_info['event_id'], $media_keys[$next_index]));
    exit();
  }


  // EDIT PHOTO
  elseif($task == "media_edit")
  {
    $media_info['eventmedia_title'] = $_POST['eventmedia_title'];
    $media_info['eventmedia_desc'] = $_POST['eventmedia_desc'];
    
    $database->database_query("UPDATE se_eventmedia SET eventmedia_title='{$media_info['eventmedia_title']}', eventmedia_desc='{$media_info['eventmedia_desc']}' WHERE eventmedia_id='{$media_info['eventmedia_id']}'");
  }
}



// GET CUSTOM EVENT STYLE IF ALLOWED
if( $event->eventowner_level_info['level_event_style'] )
{ 
  $eventstyle_info = $database->database_fetch_assoc($database->database_query("SELECT eventstyle_css FROM se_eventstyles WHERE eventstyle_event_id='{$event->event_info['event_id']}' LIMIT 1")); 
  $global_css = $eventstyle_info['eventstyle_css'];
}

// GET MEDIA WIDTH/HEIGHT
$mediasize = @getimagesize($event->event_dir($event->event_info['event_id']).$media_info['eventmedia_id'].'.'.$media_info['eventmedia_ext']);
$media_info['eventmedia_width'] = $mediasize[0];
$media_info['eventmedia_height'] = $mediasize[1];


// CHECK IF USER IS ALLOWED TO TAG PHOTOS
$allowed_to_tag = ($privacy_max & $media_info['eventalbum_tag']);

// CHECK IF USER IS ALLOWED TO COMMENT
$allowed_to_comment = ($privacy_max & $event->event_info['event_comments']);


// UPDATE ALBUM VIEWS
$album_views_new = $media_info['eventalbum_views'] + 1;
$database->database_query("UPDATE se_eventalbums SET eventalbum_views=eventalbum_views+1 WHERE eventalbum_id='{$media_info['eventalbum_id']}' LIMIT 1");


// UPDATE NOTIFICATIONS
if( $user->user_info['user_id']==$event->event_info['event_user_id'] )
{
  $database->database_query("DELETE FROM se_notifys USING se_notifys LEFT JOIN se_notifytypes ON se_notifys.notify_notifytype_id=se_notifytypes.notifytype_id WHERE se_notifys.notify_user_id='{$user->user_info['user_id']}' AND (se_notifytypes.notifytype_name='eventmediacomment' OR se_notifytypes.notifytype_name='eventmediatag' OR se_notifytypes.notifytype_name='neweventtag') AND notify_object_id='{$media_info['eventmedia_id']}'");
}



// RETRIEVE TAGS FOR THIS PHOTO
$tag_array = Array();
$tags = $database->database_query("SELECT se_eventmediatags.*, se_users.user_id, se_users.user_username, se_users.user_fname, se_users.user_lname FROM se_eventmediatags LEFT JOIN se_users ON se_eventmediatags.eventmediatag_user_id=se_users.user_id WHERE eventmediatag_eventmedia_id='{$media_info['eventmedia_id']}' ORDER BY eventmediatag_id ASC");
while($tag = $database->database_fetch_assoc($tags))
{
  $taggeduser = new se_user();
  if( $tag['user_id'] )
  {
    $taggeduser->user_exists = TRUE;
    $taggeduser->user_info['user_id'] = $tag['user_id'];
    $taggeduser->user_info['user_username'] = $tag['user_username'];
    $taggeduser->user_info['user_fname'] = $tag['user_fname'];
    $taggeduser->user_info['user_lname'] = $tag['user_lname'];
    $taggeduser->user_displayname();
  }
  else
  {
    $taggeduser->user_exists = FALSE;
  }

  $tag['tagged_user'] = $taggeduser;
  $tag_array[] = $tag; 
}


// SET EVENT OWNER (OR EDITOR)
if($event->user_rank == 2 || $event->user_rank == 1)
{
  $eventowner = $user;
}
else
{
  $eventowner = new se_user(Array($event->event_info['event_user_id']));
}


// SET GLOBAL PAGE TITLE
$global_page_title[0] = 2000326; 
$global_page_title[1] = $event->event_info['event_title'];
$global_page_description[0] = 2000327;
$global_page_description[1] = $event->event_info['event_title'];


// ASSIGN VARIABLES AND DISPLAY ALBUM FILE PAGE
$smarty->assign('event', $event);
$smarty->assign('eventowner', $eventowner);
$smarty->assign('media_info', $media_info);
$smarty->assign('allowed_to_comment', $allowed_to_comment);
$smarty->assign('allowed_to_tag', $allowed_to_tag);
$smarty->assign('media', $media_array);
$smarty->assign('media_keys', array_keys($media_array));
$smarty->assign('tags', $tag_array);
include "footer.php";
?>