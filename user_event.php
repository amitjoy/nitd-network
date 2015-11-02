<?php

$page = "user_event";
include "header.php";


// ENSURE EVENTS ARE ENABLED FOR THIS USER
if( 3 & ~$user->level_info['level_event_allow'] )
{
  header("Location: user_home.php");
  exit();
}


// SET VARIABLES AND INITIALIZE EVENT OBJECT
$task         = ( !empty($_POST['task'])          ? $_POST['task']          : ( !empty($_GET['task'])         ? $_GET['task']         : NULL    ) );
$view         = ( !empty($_POST['view'])          ? $_POST['view']          : ( !empty($_GET['view'])         ? $_GET['view']         : 'list'  ) );
$date         = ( !empty($_POST['date'])          ? $_POST['date']          : ( !empty($_GET['date'])         ? $_GET['date']         : NULL    ) );
$search       = ( !empty($_POST['search'])        ? $_POST['search']        : ( !empty($_GET['search'])       ? $_GET['search']       : NULL    ) );
$p            = ( !empty($_POST['p'])             ? $_POST['p']             : ( !empty($_GET['p'])            ? $_GET['p']            : 1       ) );
$s            = ( !empty($_POST['s'])             ? $_POST['s']             : ( !empty($_GET['s'])            ? $_GET['s']            : NULL    ) );

$show_notification = !empty($_GET['show_notification']);
if( !empty($search) ) $view = "list";
if( !$s ) $s = "se_events.event_date_start DESC";
$where = NULL;
$events_per_page = 10;

$event = new se_event($user->user_info[user_id]);


// BEGIN DATE PROCESSING
if( !$date || !is_numeric($date) )
  $date = time();


// VIEW: CALENDAR - MONTH
if( $view=="month" )
{
  // GET THIS, LAST AND NEXT MONTHS
  $date       = mktime(0, 0, 0, date("m", $date),   1, date("Y", $date));
  $date_next  = mktime(0, 0, 0, date("m", $date)+1, 1, date("Y", $date));
  $date_last  = mktime(0, 0, 0, date("m", $date)-1, 1, date("Y", $date));

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
  
  // GET TODAYS DAY AND MONTH
  $today_day = date('j', time());
  $today_month = mktime(0, 0, 0, date("m", time()), 1, date("Y", time()));
  
  // GENERATE WHERE
  $where = "(se_events.event_date_start>='{$date}' && se_events.event_date_start<'{$date_next}')";
  
  // GET TOTAL EVENTS/GET EVENTS ARRAY
  $total_events = $event->event_total($where);
  $event_array_raw = $event->event_list(0, $total_events, $s, $where, 1);
  
  // INDEX BY DAY
  foreach($event_array_raw as $event_index=>$event_array_single )
  {
    $day = date("j", $event_array_raw[$event_index]['event']->event_info['event_date_start']);
    $events[$day][] =& $event_array_raw[$event_index];
  }
  
  // ASSIGN
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
}


// VIEW: LIST
elseif( $view=="list" )
{
  if( !empty($search) )
    $where = "se_events.event_title LIKE '%{$search}%' || se_events.event_desc LIKE '%{$search}%'";
  
  // GET TOTAL EVENTS/GET EVENTS ARRAY
  $total_events = $event->event_total($where);
  $page_vars = make_page($total_events, $events_per_page, $p);
  $events = $event->event_list($page_vars[0], $events_per_page, $s, $where, 1);
  
  // ASSIGN
  $smarty->assign('s', $s);
  $smarty->assign('search', $search);

  $smarty->assign('p', $page_vars[1]);
  $smarty->assign('maxpage', $page_vars[2]);
  $smarty->assign('p_start', $page_vars[0]+1);
  $smarty->assign('p_end', $page_vars[0]+count($blogentries));
}


// ASSIGN VARIABLES AND SHOW VIEW EVENTS PAGE
$smarty->assign('view', $view);

$smarty->assign('total_events', $total_events);
$smarty->assign_by_ref('events', $events);

$smarty->assign('show_notification', $show_notification);
include "footer.php";
?>