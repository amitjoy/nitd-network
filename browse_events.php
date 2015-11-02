<?php

$page = "browse_events";
include "header.php";


if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
if(isset($_POST['s'])) { $s = $_POST['s']; } elseif(isset($_GET['s'])) { $s = $_GET['s']; } else { $s = "event_datecreated DESC"; }
if(isset($_POST['v'])) { $v = $_POST['v']; } elseif(isset($_GET['v'])) { $v = $_GET['v']; } else { $v = 0; }
if(isset($_POST['eventcat_id'])) { $eventcat_id = $_POST['eventcat_id']; } elseif(isset($_GET['eventcat_id'])) { $eventcat_id = $_GET['eventcat_id']; } else { $eventcat_id = 0; }

// ENSURE SORT/VIEW ARE VALID
if($s != "event_datecreated DESC" && $s != "event_totalmembers DESC" && $s != "event_date_start DESC" && $s != "event_date_end DESC" && $s != "event_date_start ASC" && $s != "event_date_end ASC") { $s = "event_date_start ASC"; }
if($v != "1" && $v != "2" && $v != "3") { $v = 3; }


// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if( (!$user->user_exists && !$setting['setting_permission_event']) || ($user->user_exists && (1 & ~$user->level_info['level_event_allow'])) )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}


// SET WHERE CLAUSE
$where = "CASE
	    WHEN se_events.event_user_id='{$user->user_info['user_id']}'
	      THEN TRUE
	    WHEN ((se_events.event_privacy & 32) AND '{$user->user_exists}'<>0)
	      THEN TRUE
	    WHEN ((se_events.event_privacy & 64) AND '{$user->user_exists}'=0)
	      THEN TRUE
	    WHEN ((se_events.event_privacy & 2) AND (SELECT TRUE FROM se_eventmembers WHERE eventmember_user_id='{$user->user_info['user_id']}' AND eventmember_event_id=se_events.event_id AND eventmember_status=1 LIMIT 1))
	      THEN TRUE
	    WHEN ((se_events.event_privacy & 4) AND '{$user->user_exists}'<>0 AND (SELECT TRUE FROM se_friends WHERE friend_user_id1=se_events.event_user_id AND friend_user_id2='{$user->user_info['user_id']}' AND friend_status=1 LIMIT 1))
	      THEN TRUE
	    WHEN ((se_events.event_privacy & 8) AND '{$user->user_exists}'<>0 AND (SELECT TRUE FROM se_eventmembers LEFT JOIN se_friends ON se_eventmembers.eventmember_user_id=se_friends.friend_user_id1 WHERE se_eventmembers.eventmember_event_id=se_events.event_id AND se_friends.friend_user_id2='{$user->user_info['user_id']}' AND se_eventmembers.eventmember_status=1 AND se_friends.friend_status=1 LIMIT 1))
	      THEN TRUE
	    WHEN ((se_events.event_privacy & 16) AND '{$user->user_exists}'<>0 AND (SELECT TRUE FROM se_eventmembers LEFT JOIN se_friends AS friends_primary ON se_eventmembers.eventmember_user_id=friends_primary.friend_user_id1 LEFT JOIN se_friends AS friends_secondary ON friends_primary.friend_user_id2=friends_secondary.friend_user_id1 WHERE se_eventmembers.eventmember_event_id=se_events.event_id AND se_eventmembers.eventmember_status=1 AND friends_secondary.friend_user_id2='{$user->user_info['user_id']}' AND friends_primary.friend_status=1 AND friends_secondary.friend_status=1 LIMIT 1))
	      THEN TRUE
	    ELSE FALSE
	END";


// ONLY MY FRIENDS' EVENTS
if( $v=="2" && $user->user_exists )
{
  // SET WHERE CLAUSE
  $where .= " AND (SELECT TRUE FROM se_friends LEFT JOIN se_eventmembers ON se_friends.friend_user_id2=se_eventmembers.eventmember_user_id WHERE friend_user_id1='{$user->user_info[user_id]}' AND friend_status=1 AND eventmember_event_id=se_events.event_id AND eventmember_status=1 LIMIT 1)";
}

elseif( $v=="3" )
{
  $where .= " && se_events.event_date_start>'".time()."' ";
  // Force sort?
  $s = "event_date_start ASC";
}


// SPECIFIC EVENT CATEGORY
if( is_numeric($eventcat_id) )
{
  $eventcat_query = $database->database_query("SELECT eventcat_id, eventcat_title, eventcat_dependency FROM se_eventcats WHERE eventcat_id='{$eventcat_id}' LIMIT 1");
  if( $database->database_num_rows($eventcat_query) )
  {
    $eventcat = $database->database_fetch_assoc($eventcat_query);
    if( !$eventcat['eventcat_dependency'] )
    {
      $cat_ids[] = $eventcat['eventcat_id'];
      $depcats = $database->database_query("SELECT eventcat_id FROM se_eventcats WHERE eventcat_id='{$eventcat['eventcat_id']}' OR eventcat_dependency='{$eventcat['eventcat_id']}'");
      while($depcat_info = $database->database_fetch_assoc($depcats)) { $cat_ids[] = $depcat_info['eventcat_id']; }
      $where .= " AND se_events.event_eventcat_id IN('".implode("', '", $cat_ids)."')";
    }
    else
    {
      $where .= " AND se_events.event_eventcat_id={$eventcat[eventcat_id]}";
      $eventsubcat = $eventcat;
      $eventcat = $database->database_fetch_assoc($database->database_query("SELECT eventcat_id, eventcat_title FROM se_eventcats WHERE eventcat_id='{$eventcat['eventcat_dependency']}' LIMIT 1"));
    }
  }
}

// CREATE EVENT OBJECT
$event = new se_event();

// GET TOTAL EVENTS
$total_events = $event->event_total($where);

// MAKE ENTRY PAGES
$events_per_page = 10;
$page_vars = make_page($total_events, $events_per_page, $p);

// GET EVENT ARRAY
$event_array = $event->event_list($page_vars[0], $events_per_page, $s, $where, TRUE);

// GET CATS
$field = new se_field("event");
$field->cat_list(0, 0, 0, "", "", "eventfield_id=0");
$cat_array = $field->cats;

// SET GLOBAL PAGE TITLE
$global_page_title[0] = 3000274; 
$global_page_description[0] = 3000275;


// ASSIGN SMARTY VARIABLES AND DISPLAY EVENTS PAGE
$smarty->assign('eventcat_id', $eventcat_id);
$smarty->assign('eventcat', $eventcat);
$smarty->assign('eventsubcat', $eventsubcat);
$smarty->assign('cats', $cat_array);
$smarty->assign('events', $event_array);
$smarty->assign('total_events', $total_events);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($event_array));
$smarty->assign('s', $s);
$smarty->assign('v', $v);
include "footer.php";
?>