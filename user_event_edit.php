<?php

$page = "user_event_edit";
include "header.php";


$task       = ( !empty($_POST['task'])      ? $_POST['task']      : ( !empty($_GET['task'])       ? $_GET['task']       : NULL ) );
$event_id   = ( !empty($_POST['event_id'])  ? $_POST['event_id']  : ( !empty($_GET['event_id'])   ? $_GET['event_id']   : NULL ) );
$justadded  = ( !empty($_POST['justadded']) ? $_POST['justadded'] : ( !empty($_GET['justadded'])  ? $_GET['justadded']  : NULL ) );


// ENSURE EVENT CREATION IS ENABLED FOR THIS USER
if( 3 & ~$user->level_info['level_event_allow'] )
{
  header("Location: user_home.php");
  exit();
}


// INITIALIZE EVENT OBJECT
$event = new se_event($user->user_info['user_id'], $event_id);


if( !$event->event_exists )
{
  header("Location: user_event.php");
  exit();
}

if( $event->event_info['event_user_id']!=$user->user_info['user_id'] )
{
  header("Location: user_event.php");
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


// SET ERROR VARIABLES
$is_error = FALSE;
$result = FALSE;


// CHECK FOR ADMIN ALLOWANCE OF PHOTO
if( !$event->eventowner_level_info['level_event_photo'] && ($task == "remove" || $task == "upload")) $task = "main";


// DELETE PHOTO
if( $task=="remove" )
{
  $event->event_photo_delete();
  $event->event_lastupdate();
}

// UPLOAD PHOTO
elseif( $task=="upload" )
{
  $event->event_photo_upload("photo");
  $is_error = $event->is_error;
  $error_message = $event->error_message;
  if($is_error == 0) { $event->event_lastupdate(); }
}

// SAVE
elseif( $task=="dosave" )
{
  $event->event_info['event_title']           = $_POST['event_title'];
  $event->event_info['event_desc']            = $_POST['event_desc'];
  $event->event_info['event_host']            = $_POST['event_host'];
  $event->event_info['event_location']        = $_POST['event_location'];
  $event->event_info['event_eventcat_id']     = $_POST['event_eventcat_id'];
  $event->event_info['event_eventsubcat_id']  = $_POST['event_eventsubcat_id'];
  
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
    $event_date_start_processed,
    $event_date_end_processed,
    $event->event_info['event_host'],
    $event->event_info['event_location'],
    $field->field_query
  );
  
  
  if( !$event->is_error )
  {
    // SET RESULT MESSAGE
    $result = TRUE;
    
    // RESET RESULTS
    $event->eventvalue_info = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_eventvalues WHERE eventvalue_event_id='{$event->event_info['event_id']}' LIMIT 1"));
  }
  
  else
  {
    SE_Language::_preload($is_error = $event->is_error);
  }
}




// GET FIELDS
$field = new se_field("event", $event->eventvalue_info);
$field->cat_list(0, 0, 0, "", "", "");
$cat_array = $field->cats;
if( $is_error )
{
  $eventcat_id = ( $event->event_info['event_eventcat_id'] ? $event->event_info['event_eventcat_id'] : '0' );
  $selected_cat_array = array_filter($cat_array, create_function('$a', 'if($a[cat_id] == "'.$eventcat_id.'") { return $a; }'));
  while(list($key, $val) = each($selected_cat_array)) {
    $cat_array[$key]['fields'] = $selected_fields;
  }
}


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



// REPLACE LINE BREAKS
$event->event_info['event_desc'] = str_replace("<br />", "\r\n", $event->event_info['event_desc']);
$event->event_info['event_location'] = str_replace("<br />", "\r\n", $event->event_info['event_location']);



// GET MONTH NAMES FROM LOCALE FOR POPUP CALENDAR
$month_names = array();
for($i=1; $i<=12; $i++)
  $month_names[$i] = strftime('%B', mktime(0, 0, 0, $i, 1, 0));

$day_names = array();
for($i=1; $i<=7; $i++)
  $day_names[$i] = strftime('%A', mktime(0, 0, 0, 11, $i+1, 2008));




// ASSIGN VARIABLES AND SHOW USER EDIT EVENT PAGE
$smarty->assign('result',     $result);
$smarty->assign('is_error',   $is_error);
$smarty->assign('justadded',  $justadded);
$smarty->assign('compatible_input_dateformat', $compatible_input_dateformat);
$smarty->assign('compatible_input_timeformat', $compatible_input_timeformat);
$smarty->assign('event_date_start_tz', $datetime->timezone($event->event_info['event_date_start'], $global_timezone));
$smarty->assign('event_date_end_tz', $datetime->timezone($event->event_info['event_date_end'], $global_timezone));

$smarty->assign_by_ref('event',       $event);
$smarty->assign_by_ref('cats',        $cat_array);
$smarty->assign_by_ref('month_names', $month_names);
$smarty->assign_by_ref('day_names',   $day_names);

include "footer.php";
?>