<?php
$page = "user_event_edit_settings";
include "header.php";


$task       = ( !empty($_POST['task'])      ? $_POST['task']      : ( !empty($_GET['task'])       ? $_GET['task']       : NULL ) );
$event_id   = ( !empty($_POST['event_id'])  ? $_POST['event_id']  : ( !empty($_GET['event_id'])   ? $_GET['event_id']   : NULL ) );


// ENSURE EVENTS ARE ENABLED FOR THIS USER
if( 3 & ~$user->level_info['level_event_allow'] )
{
  header("Location: user_home.php");
  exit();
}


// INITIALIZE EVENT OBJECT
$event = new se_event($user->user_info['user_id'], $event_id);

if( !$event->event_exists || $event->event_info['event_user_id']!=$user->user_info['user_id'] )
{
  header("Location: user_event.php");
  exit();
}


// SET EMPTY VARS
$result = FALSE;


// GET PRIVACY SETTINGS
$level_event_privacy = unserialize($user->level_info['level_event_privacy']);
rsort($level_event_privacy);
for($c=0;$c<count($level_event_privacy);$c++) {
  if(event_privacy_levels($level_event_privacy[$c]) != "") {
    SE_Language::_preload(event_privacy_levels($level_event_privacy[$c]));
    $privacy_options[$level_event_privacy[$c]] = event_privacy_levels($level_event_privacy[$c]);
  }
}

$level_event_comments = unserialize($user->level_info['level_event_comments']);
rsort($level_event_comments);
for($c=0;$c<count($level_event_comments);$c++) {
  if(event_privacy_levels($level_event_comments[$c]) != "") {
    SE_Language::_preload(event_privacy_levels($level_event_comments[$c]));
    $comment_options[$level_event_comments[$c]] = event_privacy_levels($level_event_comments[$c]);
  }
}

$level_event_upload = unserialize($user->level_info['level_event_upload']);
rsort($level_event_upload);
for($c=0;$c<count($level_event_upload);$c++) {
  if(event_privacy_levels($level_event_upload[$c]) != "") {
    SE_Language::_preload(event_privacy_levels($level_event_upload[$c]));
    $upload_options[$level_event_upload[$c]] = event_privacy_levels($level_event_upload[$c]);
  }
}

$level_event_tag = unserialize($user->level_info['level_event_tag']);
rsort($level_event_tag);
for($c=0;$c<count($level_event_tag);$c++) {
  if(event_privacy_levels($level_event_tag[$c]) != "") {
    SE_Language::_preload(event_privacy_levels($level_event_tag[$c]));
    $tag_options[$level_event_tag[$c]] = event_privacy_levels($level_event_tag[$c]);
  }
}



// SAVE
if( $task=="dosave" )
{
  $style_event                           = $_POST['style_event'];
  $event->event_info['event_search']     = $_POST['event_search'];
  $event->event_info['event_invite']     = $_POST['event_invite'];
  $event->event_info['event_inviteonly'] = $_POST['event_inviteonly'];
  $event->event_info['event_privacy']    = $_POST['event_privacy'];
  $event->event_info['event_comments']   = $_POST['event_comments'];
  $event->event_info['event_upload']     = $_POST['event_upload'];
  $event->event_info['event_tag']        = $_POST['event_tag'];
  
  // CHECK IF CUSTOM CSS IS DISABLED BY ADMIN OR UPDATE
  if( $event->eventowner_level_info['level_event_style'] )
  {
    $style_event = addslashes(str_replace("-moz-binding", "", strip_tags(htmlspecialchars_decode($_POST['style_event'], ENT_QUOTES))));
    $sql = "UPDATE se_eventstyles SET eventstyle_css='{$style_event}' WHERE eventstyle_event_id='{$event->event_info['event_id']}'";
    $resource = $database->database_query($sql);
  }
  
  // UPDATE EVENT SETTINGS
  $event->event_edit_settings(
    $event->event_info['event_search'],
    $event->event_info['event_inviteonly'],
    $event->event_info['event_privacy'],
    $event->event_info['event_comments'],
    $event->event_info['event_upload'],
    $event->event_info['event_tag'],
    $event->event_info['event_invite']
  );
}



// GET THIS USER'S EVENT CSS IF ALLOWED
if( $event->eventowner_level_info['level_event_style'] )
{
  $sql = "SELECT eventstyle_css FROM se_eventstyles WHERE eventstyle_event_id='{$event->event_info['event_id']}' LIMIT 1";
  $resource = $database->database_query($sql);

  if( $database->database_num_rows($style_query) )
  { 
    $style_info = $database->database_fetch_assoc($style_query);
    $style_event = $style_info['eventstyle_css'];
  }
  else
  {
    $sql = "INSERT INTO se_eventstyles (eventstyle_event_id, eventstyle_css) VALUES ('{$event->event_info['event_id']}', '')";
    $resource = $database->database_query($sql);
    $style_event = '';
  }
}



// ASSIGN SMARTY VARIABLES AND DISPLAY EDIT STYLE PAGE
$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);

$smarty->assign_by_ref('event', $event);
$smarty->assign_by_ref('privacy_options', $privacy_options);
$smarty->assign_by_ref('comment_options', $comment_options);
$smarty->assign_by_ref('upload_options',  $upload_options);
$smarty->assign_by_ref('tag_options',     $tag_options);

$smarty->assign('style_event', htmlspecialchars($style_event, ENT_QUOTES, 'UTF-8'));

include "footer.php";
?>