<?php

$page = "user_event_edit_members";
include "header.php";

$task       = ( !empty($_POST['task'])      ? $_POST['task']      : ( !empty($_GET['task'])       ? $_GET['task']       : NULL  ) );
$event_id   = ( !empty($_POST['event_id'])  ? $_POST['event_id']  : ( !empty($_GET['event_id'])   ? $_GET['event_id']   : NULL  ) );
$search     = ( !empty($_POST['search'])    ? $_POST['search']    : ( !empty($_GET['search'])     ? $_GET['search']     : NULL  ) );
$p          = ( !empty($_POST['p'])         ? $_POST['p']         : ( !empty($_GET['p'])          ? $_GET['p']          : 1     ) );
$s          = ( !empty($_POST['s'])         ? $_POST['s']         : ( !empty($_GET['s'])          ? $_GET['s']          : NULL  ) );
$v          = (  isset($_POST['v'])         ? $_POST['v']         : (  isset($_GET['v'])          ? $_GET['v']          : NULL  ) );


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

if( $event->event_info['event_user_id']!=$user->user_info['user_id'])
{
  header("Location: user_event.php");
  exit();
}


// SET VARS
$result = FALSE;
$where = NULL;
$where_clause = array();

if( $s!="se_users.user_dateupdated DESC" && $s!="se_users.user_lastlogindate DESC" )
  $s = "se_users.user_dateupdated DESC";

if( $v>0 || $v==="0" )
  $where_clause[] = "se_eventmembers.eventmember_status=1 && se_eventmembers.eventmember_approved=1 && se_eventmembers.eventmember_rsvp='{$v}'";

if( $v==-1 )
  $where_clause[] = "se_eventmembers.eventmember_status=0 && se_eventmembers.eventmember_approved=1";
  
if( $v==-2 )
  $where_clause[] = "se_eventmembers.eventmember_status=1 && se_eventmembers.eventmember_approved=0";
  
if( empty($v) && $v!=="0" )
  $where_clause[] = "se_eventmembers.eventmember_status=1 && se_eventmembers.eventmember_approved=1";

if( !empty($search) )
  $where_clause[] = "(se_users.user_username LIKE '%{$search}%' OR se_users.user_email LIKE '%{$search}%' OR CONCAT(se_users.user_fname, ' ', se_users.user_lname) LIKE '%{$search}%')";

if( !empty($where_clause) )
  $where = implode(" && ", $where_clause);




// GET TOTAL MEMBERS
$total_members = $event->event_member_total($where, TRUE);

// MAKE MEMBER PAGES
$members_per_page = 10;
$page_vars = make_page($total_members, $members_per_page, $p);

// GET MEMBER ARRAY
$members = $event->event_member_list($page_vars[0], $members_per_page, $s, $where);



// ASSIGN VARIABLES AND SHOW USER EDIT EVENT MEMBERS PAGE
$smarty->assign_by_ref('event', $event);
$smarty->assign_by_ref('members', $members);
$smarty->assign('total_members', $total_members);

$smarty->assign('search', $search);
$smarty->assign('s', $s);
$smarty->assign('v', $v);

$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($members));

$smarty->assign('result', $result);
include "footer.php";
?>