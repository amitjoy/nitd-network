<?php

$page = "user_event_add";
include "header.php";


$task       = ( !empty($_POST['task'])      ? $_POST['task']      : ( !empty($_GET['task'])     ? $_GET['task']     : NULL ) );


// ENSURE EVENTS ARE ENABLED FOR THIS USER
if( 4 & ~$user->level_info['level_event_allow'] )
{
  header("Location: user_home.php");
  exit();
}


// Get a date and time format that we can use
$compatible_input_dateformat = $setting['setting_dateformat'];
switch( $compatible_input_dateformat )
{ 
  //US
  default: case 'n/j/Y': case 'n.j.Y': case 'n-j-Y': case 'M. j, Y': case 'F j, Y': case 'l, F j, Y':
    $compatible_input_dateformat = 'm/d/Y';
    break;
  //EU
  case 'j/n/Y': case 'j.n.Y': case 'j F Y': case 'D-j-M-Y': case 'D j M Y': case 'D j F Y': case 'l j F Y': case 'j-M-Y':
    $compatible_input_dateformat = 'd/m/Y';
    break;
  //JA/ZH
  case 'Y/n/j': case 'Y-n-j': case 'Y-m-d': case 'Ynj': case 'Y-M-j':
    $compatible_input_dateformat = 'Y/m/d';
    break;
}

$compatible_input_timeformat = $setting['setting_timeformat'];
switch( $compatible_input_timeformat )
{
  //STD
  default: case 'g:i A': case 'h:i A': case 'g:i': case 'h:i':
    $compatible_input_timeformat = 'g:i A';
    break;
  //24
  case 'H:i': case 'H\hi':
    $compatible_input_timeformat = 'H:i';
    break;
}


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


// INITIALIZE EVENT OBJECT
$event = new se_event($user->user_info['user_id'], NULL);


// INITIALIZE VARIABLES
$is_error = FALSE;

$event->event_info = array(
  'event_title'           => NULL,
  'event_desc'            => NULL,
  'event_host'            => NULL,
  'event_location'        => NULL,
  'event_privacy'         => $level_event_privacy[0],
  'event_comments'        => $level_event_comments[0],
  'event_upload'          => $level_event_upload[0],
  'event_tag'             => $level_event_tag[0],
  'event_search'          => 1,
  'event_eventcat_id'     => NULL,
  'event_eventsubcat_id'  => NULL,
  'event_date_start'      => time() + (60*60),
  'event_date_end'        => time() + (60*60*2),
  'event_inviteonly'      => 0,
  'event_invite'          => 1
);


// ATTEMPT TO ADD EVENT
if( $task=="doadd" )
{
  $event->event_info['event_title']           = $_POST['event_title'];
  $event->event_info['event_desc']            = $_POST['event_desc'];
  $event->event_info['event_host']            = $_POST['event_host'];
  $event->event_info['event_location']        = $_POST['event_location'];
  $event->event_info['event_eventcat_id']     = $_POST['event_eventcat_id'];
  $event->event_info['event_eventsubcat_id']  = $_POST['event_eventsubcat_id'];
  $event->event_info['event_invite']          = $_POST['event_invite'];
  $event->event_info['event_inviteonly']      = $_POST['event_inviteonly'];
  $event->event_info['event_search']          = $_POST['event_search'];
  $event->event_info['event_privacy']         = $_POST['event_privacy'];
  $event->event_info['event_comments']        = $_POST['event_comments'];
  $event->event_info['event_upload']          = $_POST['event_upload'];
  $event->event_info['event_tag']             = $_POST['event_tag'];
  
  $event_date_start = $_POST['event_date_start'];
  $event_time_start = preg_replace('/[^aAmMpP0-9:]/', '', $_POST['event_time_start']);
  $event_date_end   = $_POST['event_date_end'];
  $event_time_end   = preg_replace('/[^aAmMpP0-9:]/', '', $_POST['event_time_end']);
  
  // Process time
  $event_date_start_array = split('/', $event_date_start);
  $event_date_end_array = split('/', $event_date_end);
  
  // Fix for other orders
  //EU
  if( $compatible_input_dateformat=='d/m/Y' )
  {
    $event_date_start = "{$event_date_start_array[1]}/{$event_date_start_array[0]}/{$event_date_start_array[2]}";
    $event_date_end = "{$event_date_end_array[1]}/{$event_date_end_array[0]}/{$event_date_end_array[2]}";
  }
  //JA/ZH
  elseif( $compatible_input_dateformat=='Y/m/d' )
  {
    $event_date_start = "{$event_date_start_array[1]}/{$event_date_start_array[2]}/{$event_date_start_array[0]}";
    $event_date_end = "{$event_date_end_array[1]}/{$event_date_end_array[2]}/{$event_date_end_array[0]}";
  }
  
  $event_date_start_processed = strtotime("{$event_date_start} {$event_time_start}");
  $event_date_end_processed   = strtotime("{$event_date_end} {$event_time_end}");
  
  // FIX RESULT FOR PHP4 AND UNTIMEZONE
  if( $event_date_start_processed===-1  )
    $event_date_start_processed = FALSE;
  else
    $event_date_start_processed = $datetime->untimezone($event_date_start_processed, $global_timezone);
  
  if( $event_date_end_processed===-1    )
    $event_date_end_processed   = FALSE;
  else
    $event_date_end_processed = $datetime->untimezone($event_date_end_processed, $global_timezone);
  
  $event->event_info['event_date_start']  = $event_date_start_processed;
  $event->event_info['event_date_end']    = $event_date_end_processed;
  
  
  // GET FIELDS
  $field = new se_field("event", $event->eventvalue_info);
  $field->cat_list(1, 0, 0, "eventcat_id='{$event->event_info['event_eventcat_id']}'", "", "");
  $selected_fields = $field->fields_all;
  $is_error = $field->is_error;
  
  // SET EVENT CATEGORY ID
  if( !empty($event->event_info['event_eventsubcat_id']) )
    $event->event_info['event_eventcat_id'] = $event->event_info['event_eventsubcat_id'];

  // SAVE
  $event->event_edit(
    $event->event_info['event_title'],
    $event->event_info['event_desc'],
    $event->event_info['event_eventcat_id'],
    $event->event_info['event_date_start'],
    $event->event_info['event_date_end'],
    $event->event_info['event_host'],
    $event->event_info['event_location'],
    $field->field_query
  );
  
  if( !$event->is_error )
  {
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
    
    header("Location: user_event_edit.php?event_id={$event_id}&justadded=1");
    exit();
  }
  
  else
  {
    SE_Language::_preload($is_error = $event->is_error);
    /*
    var_dump(array(
      $event_title,
      $event_desc,
      $eventcat_id,
      $event_date_start_processed,
      $event_date_end_processed,
      $event_host,
      $event_location,
      $event_search,
      $event_privacy,
      $event_comments,
      $event_inviteonly
    ));
    */
  }
}


// GET FIELDS
$field = new se_field("event", $event->eventvalue_info);
$field->cat_list(0, 0, 0, "", "", "");
$cat_array = $field->cats;
if( $is_error )
{
  $eventcat_id = ( $event->event_info['event_eventcat_id'] ? $event->event_info['event_eventcat_id'] : '0' );
  $selected_cat_array = array_filter($cat_array, create_function('$a', 'if($a[cat_id] == '.$eventcat_id.') { return $a; }'));
  while(list($key, $val) = each($selected_cat_array)) {
    $cat_array[$key][fields] = $selected_fields;
  }
}
//var_dump($cat_array);

// GET SUBCAT IF NECESSARY
$thiscat = $database->database_fetch_assoc($database->database_query("SELECT eventcat_id, eventcat_dependency FROM se_eventcats WHERE eventcat_id='{$event->event_info['event_eventcat_id']}' LIMIT 1"));
if( !$thiscat['eventcat_dependency'] )
{
  $event->event_info['event_eventsubcat_id'] = 0;
}
else
{
  $event->event_info['event_eventsubcat_id'] = $event->event_info['event_eventcat_id'];
  $event->event_info['event_eventcat_id'] = $thiscat['eventcat_dependency'];
}


// REMOVE BREAKS
$event->event_info['event_desc'] = str_replace("<br />", "\r\n", $event->event_info['event_desc']);
$event->event_info['event_location'] = str_replace("<br />", "\r\n", $event->event_info['event_location']);



// GET DAY AND MONTH NAMES FROM LOCALE FOR POPUP CALENDAR
$month_names = array();
for($i=1; $i<=12; $i++)
  $month_names[$i] = strftime('%B', mktime(0, 0, 0, $i, 1, 0));

$day_names = array();
for($i=1; $i<=7; $i++)
  $day_names[$i] = strftime('%A', mktime(0, 0, 0, 11, $i+1, 2008));



// ASSIGN VARIABLES AND SHOW ADD EVENTS PAGE
$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);
$smarty->assign('compatible_input_dateformat', $compatible_input_dateformat);
$smarty->assign('compatible_input_timeformat', $compatible_input_timeformat);
$smarty->assign('event_date_start_tz', $datetime->timezone($event->event_info['event_date_start'], $global_timezone));
$smarty->assign('event_date_end_tz', $datetime->timezone($event->event_info['event_date_end'], $global_timezone));

$smarty->assign_by_ref('cats', $cat_array);
$smarty->assign_by_ref('event', $event);
$smarty->assign_by_ref('privacy_options', $privacy_options);
$smarty->assign_by_ref('comment_options', $comment_options);
$smarty->assign_by_ref('upload_options',  $upload_options);
$smarty->assign_by_ref('tag_options',     $tag_options);
$smarty->assign_by_ref('month_names',     $month_names);
$smarty->assign_by_ref('day_names',       $day_names);
include "footer.php";
?>