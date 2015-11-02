<?php

$page = "profile_event_calendar";
include "header.php";

// GET DATE VARS
$now_adjusted = $datetime->timezone(time(), $global_timezone);
$date = ( (isset($_GET['date']) && is_numeric($_GET['date'])) ? $_GET['date'] : $now_adjusted );

// SELECT MONTH
$date       = mktime(0, 0, 0, date("m", $date)  , 1, date("Y", $date));
$date_next  = mktime(0, 0, 0, date("m", $date)+1, 1, date("Y", $date));
$date_last  = mktime(0, 0, 0, date("m", $date)-1, 1, date("Y", $date));

// SET VARIABLES AND INITIALIZE EVENT OBJECT
$event = new se_event($owner->user_info['user_id']);
$sort_by = "se_events.event_date_start ASC";
$where = "(se_events.event_date_start>='".$datetime->untimezone($date, $global_timezone)."' AND se_events.event_date_start<'".$datetime->untimezone($date_next, $global_timezone)."' AND se_eventmembers.eventmember_status<>'0' AND se_eventmembers.eventmember_status<>'3')";

// GET NUMBER OF DAYS IN MONTH
$days_in_month = date('t', $date);

// GET FIRST AND LAST DAY OF THE MONTH
$month_text = htmlentities($datetime->cdate("F", $date), NULL, 'utf-8');
$month_year = htmlentities($datetime->cdate("Y", $date), NULL, 'utf-8');
$first_day_of_month = date("w", $date);
if($first_day_of_month == 0) { $first_day_of_month = 7; }
$last_day_of_month = ($first_day_of_month-1)+$days_in_month;

// GET TOTAL NUMBER OF CELLS ON TABLE
$total_cells = (floor($last_day_of_month/7)+1)*7;

// GET TOTAL EVENTS
$total_events = $event->event_total($where);

// GET EVENTS ARRAY
$event_array = $event->event_list(0, $total_events, $sort_by, $where, 1);

// REARRANGE EVENTS ARRAY
for($e=0;$e<count($event_array);$e++)
{
  $day = date("j", $datetime->timezone($event_array[$e]['event']->event_info['event_date_start'], $global_timezone));
  $events[$day][] = $event_array[$e];
}

// GET TODAYS DAY AND MONTH
$today_day = date('j', $now_adjusted);
$today_month = mktime(0, 0, 0, date("m", $now_adjusted), 1, date("Y", $now_adjusted));
$today_month = $datetime->timezone($today_month, $global_timezone);

// TIMEZONE 'EM
$date = $datetime->timezone($date, $global_timezone);
//$date_next = $datetime->timezone($date_next, $global_timezone);
//$date_last = $datetime->timezone($date_last, $global_timezone);

// ASSIGN VARIABLES AND SHOW VIEW EVENTS PAGE
$smarty->assign('events', $events);
$smarty->assign('total_events', $total_events);
$smarty->assign('days_in_month', $days_in_month);
$smarty->assign('first_day_of_month', $first_day_of_month);
$smarty->assign('last_day_of_month', $last_day_of_month);
$smarty->assign('total_cells', $total_cells);
$smarty->assign('month', $month_text);
$smarty->assign('year', $month_year);
$smarty->assign('date_last', $date_last);
$smarty->assign('date_current', $date);
$smarty->assign('date_next', $date_next);
$smarty->assign('today_day', $today_day);
$smarty->assign('today_month', $today_month);

// ASSIGN VARIABLES AND INCLUDE FOOTER
include "footer.php";
?>