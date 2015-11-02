<?php

$page = "event";
include "header.php";


$task           = ( !empty($_POST['task'])            ? $_POST['task']            : ( !empty($_GET['task'])           ? $_GET['task']           : NULL  ) );
$event_id       = ( !empty($_POST['event_id'])        ? $_POST['event_id']        : ( !empty($_GET['event_id'])       ? $_GET['event_id']       : NULL  ) );
$v              = ( !empty($_POST['v'])               ? $_POST['v']               : ( !empty($_GET['v'])              ? $_GET['v']              : NULL  ) );
$p              = ( !empty($_POST['p'])               ? $_POST['p']               : ( !empty($_GET['p'])              ? $_GET['p']              : 1     ) );

$v_members      = ( isset($_POST['v_members'])        ? $_POST['v_members']       : ( isset($_GET['v_members'])       ? $_GET['v_members']      : NULL  ) );
$search_members = ( !empty($_POST['search_members'])  ? $_POST['search_members']  : ( !empty($_GET['search_members']) ? $_GET['search_members'] : NULL  ) );


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
$event = new se_event($user->user_info['user_id'], $event_id);

if( !$event->event_exists )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 828);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}


// GET PRIVACY LEVEL
$privacy_max = $event->event_privacy_max($user);
$allowed_to_view    = (bool) ( $privacy_max & $event->event_info['event_privacy'] );
$allowed_to_comment = (bool) ( $privacy_max & $event->event_info['event_comments'] );
$allowed_to_upload  = (bool) ( $privacy_max & $event->event_info['event_upload'] );
$allowed_to_invite  = (bool) ( $event->event_info['event_user_id']==$user->user_info['user_id'] );

// UPDATE EVENT VIEWS IF EVENT VISIBLE
if( $allowed_to_view )
{
  $event->event_info['event_views']++;
  $sql = "UPDATE se_events SET event_views=event_views+1 WHERE event_id='{$event->event_info['event_id']}' LIMIT 1";
  $database->database_query($sql);
}


// DELETE COMMENT NOTIFICATIONS /*IF VIEWING COMMENT PAGE*/
if( $user->user_info['user_id']==$event->event_info['event_user_id'])
{
  $sql = "DELETE FROM se_notifys USING se_notifys LEFT JOIN se_notifytypes ON se_notifys.notify_notifytype_id=se_notifytypes.notifytype_id WHERE se_notifys.notify_user_id='{$event->event_info['event_user_id']}' AND se_notifytypes.notifytype_name='eventcomment' AND notify_object_id='{$event->event_info[event_id]}'";
  $database->database_query($sql);
}


// GET EVENT MEDIA
$eventalbum_info = $database->database_fetch_assoc($database->database_query("SELECT eventalbum_id FROM se_eventalbums WHERE eventalbum_event_id='{$event->event_info['event_id']}' LIMIT 1"));


// GET EVENT FIELDS
$eventcat_info = $database->database_fetch_assoc($database->database_query("SELECT t1.eventcat_id AS subcat_id, t1.eventcat_title AS subcat_title, t1.eventcat_dependency AS subcat_dependency, t2.eventcat_id AS cat_id, t2.eventcat_title AS cat_title FROM se_eventcats AS t1 LEFT JOIN se_eventcats AS t2 ON t1.eventcat_dependency=t2.eventcat_id WHERE t1.eventcat_id='{$event->event_info['event_eventcat_id']}'"));
if($eventcat_info['subcat_dependency'] == 0) { $cat_where = "eventcat_id='{$event->event_info['event_eventcat_id']}'"; } else { $cat_where = "eventcat_id='{$eventcat_info['subcat_dependency']}'"; }
$field = new se_field("event", $event->eventvalue_info);
$field->cat_list(0, 1, 0, $cat_where, "eventcat_id='0'", "");


// MAKE MEMBER PAGES AND GET TOTAL MEMBERS AND GET MEMBER ARRAY
$members_where_clause = array();

if( $v_members>0 || $v_members==="0" )
  $members_where_clause[] = "se_eventmembers.eventmember_status=1 && se_eventmembers.eventmember_approved=1 && se_eventmembers.eventmember_rsvp='{$v_members}'";

if( $v_members==-1 )
  $members_where_clause[] = "se_eventmembers.eventmember_status=0 && se_eventmembers.eventmember_approved=1";
  
if( $v_members==-2 )
  $members_where_clause[] = "se_eventmembers.eventmember_status=1 && se_eventmembers.eventmember_approved=0";

//if( !empty($search_members) )
//  $members_where_clause[] = "(se_users.user_username LIKE '%{$search_members}%' OR se_users.user_email LIKE '%{$search_members}%' OR CONCAT(se_users.user_fname, ' ', se_users.user_lname) LIKE '%{$search_members}%')";

if( !empty($members_where_clause) )
  $members_where = implode(" && ", $members_where_clause);
else
  $members_where = "(se_eventmembers.eventmember_status=1 && se_eventmembers.eventmember_approved=1)";

//echo $members_where;

$total_members = $event->event_member_total($members_where, TRUE);
$members_per_page = 10;
if($v == "members") { $p_members = $p; } else { $p_members = 1; }
$page_vars_members = make_page($total_members, $members_per_page, $p_members);
$members = $event->event_member_list($page_vars_members[0], $members_per_page, "se_users.user_username", $members_where);
//$members = $event->event_member_list($page_vars_members[0], $members_per_page, "is_viewers_friend DESC, se_users.user_username", implode(" AND ", $where));


// GET MASTER TOTAL OF MEMBERS
$total_members_waiting = $event->event_member_total("(se_eventmembers.eventmember_status=1 && se_eventmembers.eventmember_approved=1 && se_eventmembers.eventmember_rsvp=0)");
$total_members_attending = $event->event_member_total("(se_eventmembers.eventmember_status=1 && se_eventmembers.eventmember_approved=1 && se_eventmembers.eventmember_rsvp=1)");
$total_members_maybeattending = $event->event_member_total("(se_eventmembers.eventmember_status=1 && se_eventmembers.eventmember_approved=1 && se_eventmembers.eventmember_rsvp=2)");
$total_members_notattending = $event->event_member_total("(se_eventmembers.eventmember_status=1 && se_eventmembers.eventmember_approved=1 && se_eventmembers.eventmember_rsvp=3)");


// GET OFFICERS
$where_officers = "se_eventmembers.eventmember_rank>1 AND se_eventmembers.eventmember_status='1' AND se_eventmembers.eventmember_approved='1'";
$total_officers = $event->event_member_total($where_officers, 0);
$officers = $event->event_member_list(0, $total_officers, "se_eventmembers.eventmember_rank DESC, se_users.user_username", $where_officers);



// GET CUSTOM EVENT STYLE IF ALLOWED
if( $event->eventowner_level_info['level_event_style'] && $allowed_to_view )
{ 
  $eventstyle_info = $database->database_fetch_assoc($database->database_query("SELECT eventstyle_css FROM se_eventstyles WHERE eventstyle_event_id='{$event->event_info['event_id']}' LIMIT 1")); 
  $global_css = $eventstyle_info['eventstyle_css'];
}


// SET GLOBAL PAGE TITLE
$global_page_title[0] = 3000272; 
$global_page_title[1] = $event->event_info['event_title'];
$global_page_description[0] = 3000273;
$global_page_description[1] = $event->event_info['event_desc'];




// ########### CALENDAR ###########
/*
$event_day_start = $event->event_info['event_date_start'];
$event_day_end = $event->event_info['event_date_end'];

$event_day_start  = $datetime->timezone($event_day_start, $global_timezone);
$event_day_end    = $datetime->timezone($event_day_end, $global_timezone);

$event_day_start = mktime(0, 0, 0, date("m", $event_day_start), date("j", $event_day_start),  date("Y", $event_day_start));
if( $event->event_info['event_date_end'] )
  $event_day_end   = mktime(0, 0, 0, date("m", $event_day_end),   date("j", $event_day_end),    date("Y", $event_day_end)  );
else
  $event_day_end = $event_day_start;

// CALENDAR YEAR LOOP
$calendar_data = array(
  'today_year'  => mktime(0, 0, 0, 1,                 1,  date("Y", time())),
  'today_month' => mktime(0, 0, 0, date("m", time()), 1,  date("Y", time())),
  'today_day'   => date('j', time()),
  'years' => array()
);

//for( $day_time_value=$event_day_start; $day_time_value<=$event_day_end+86400; $day_time_value+=86400 )
for( $day_time_value_raw=$event_day_start; $day_time_value_raw<=$event_day_end; $day_time_value_raw+=86400 )
{
  $day_time_value  = $day_time_value_raw;
  $year_time_value  = mktime(0, 0, 0, 1,                          1,  date("Y", $day_time_value));
  $month_time_value = mktime(0, 0, 0, date("m", $day_time_value), 1,  date("Y", $day_time_value));
  
  if( !isset($calendar_data['years'][$year_time_value]) )
  {
    $calendar_data['years'][$year_time_value] = array(
      'name' => htmlentities($datetime->cdate("Y", $year_time_value), NULL, 'utf-8'),
      'months' => array()
    );
  }
  
  $loop_year_data =& $calendar_data['years'][$year_time_value];
  
  if( !isset($loop_year_data['months'][$month_time_value]) )
  {
    $loop_year_data['months'][$month_time_value] = array();
  }
  
  $loop_month_data =& $loop_year_data['months'][$month_time_value];
  
  if( !isset($loop_month_data['name']) )
  {
    $loop_month_data['days'] = array();
    $loop_month_data['name'] = htmlentities($datetime->cdate("F", $month_time_value), NULL, 'utf-8');
    
    $loop_month_data['days_in_month'] = date('t', $month_time_value);
    $loop_month_data['first_day_of_month'] = date("w", $month_time_value);
    if( $loop_month_data['first_day_of_month']==0 ) $loop_month_data['first_day_of_month'] = 7;
    $loop_month_data['last_day_of_month'] = ($loop_month_data['first_day_of_month'] - 1) + $loop_month_data['days_in_month'];
    
    $loop_month_data['total_cells'] = (floor($loop_month_data['last_day_of_month'] / 7) + 1) * 7;
  }
  
  $loop_month_data['days'][] = date('j', $day_time_value);
  
  unset($loop_year_data, $loop_month_data);
}


// ASSIGN
$smarty->assign_by_ref('calendar_data', $calendar_data);
*/
// ########### CALENDAR ###########


$event->event_info['event_desc'] = html_entity_decode($event->event_info['event_desc'], ENT_QUOTES);




// ASSIGN VARIABLES AND DISPLAY EVENT PAGE
$smarty->assign_by_ref('event', $event);
$smarty->assign_by_ref('eventcat_info', $eventcat_info);
$smarty->assign_by_ref('eventalbum_info', $eventalbum_info);
$smarty->assign_by_ref('cats', $field->cats);
$smarty->assign_by_ref('members', $members);
$smarty->assign_by_ref('officers', $officers);

$smarty->assign('v', $v);
$smarty->assign('search', $search);

$smarty->assign('allowed_to_view',    $allowed_to_view);
$smarty->assign('allowed_to_comment', $allowed_to_comment);
$smarty->assign('allowed_to_upload',  $allowed_to_upload);
$smarty->assign('allowed_to_invite',  $allowed_to_invite);

$smarty->assign('total_members',      $total_members);
$smarty->assign('maxpage_members',    $page_vars_members[2]);
$smarty->assign('p_start_members',    $page_vars_members[0]+1);
$smarty->assign('p_end_members',      $page_vars_members[0]+count($members));
$smarty->assign('p_members',          $page_vars_members[1]);
$smarty->assign('v_members',          $v_members);
$smarty->assign('search_members',     $search_members);

$smarty->assign('total_members_waiting',        $total_members_waiting);
$smarty->assign('total_members_attending',      $total_members_attending);
$smarty->assign('total_members_maybeattending', $total_members_maybeattending);
$smarty->assign('total_members_notattending',   $total_members_notattending);

$smarty->assign_by_ref('actions', $actions->actions_display(0, $setting['setting_actions_actionsonprofile'], "se_actions.action_object_owner='event' AND se_actions.action_object_owner_id='{$event->event_info['event_id']}'"));

include "footer.php";
?>
