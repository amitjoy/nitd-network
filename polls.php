<?php

$page = "polls";
include "header.php";

if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
if(isset($_POST['s'])) { $s = $_POST['s']; } elseif(isset($_GET['s'])) { $s = $_GET['s']; } else { $s = "dd"; }

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if( (!$user->user_exists && !$setting['setting_permission_poll']) || ($user->user_exists && (1 & ~(int)$user->level_info['level_poll_allow'])) )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

// DISPLAY ERROR PAGE IF NO OWNER
if( !$owner->user_exists )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 828);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

// ENSURE POLLS ARE ENABLED FOR THIS USER
if( 4 & ~(int)$owner->level_info['level_poll_allow'] )
{
  header("Location: ".$url->url_create('profile', $owner->user_info['user_username']));
  exit();
}

// SET PRIVACY LEVEL AND WHERE CLAUSE
$privacy_max = $owner->user_privacy_max($user);
$where = "(poll_privacy & $privacy_max)";

// CREATE POLL OBJECT
$entries_per_page = $owner->level_info['level_poll_entries'];
$poll = new se_poll($owner->user_info['user_id']);

// GET TOTAL ENTRIES
$total_polls = $poll->poll_total($where);

// MAKE ENTRY PAGES
$page_vars = make_page($total_polls, $entries_per_page, $p);

// GET ENTRY ARRAY
$polls = $poll->poll_list($page_vars[0], $entries_per_page, "poll_id DESC", $where);

$smarty->assign('polls', $polls);
$smarty->assign('s', $s);
$smarty->assign('d', $d);
$smarty->assign('t', $t);
$smarty->assign('c', $c);
$smarty->assign('search', $search);
$smarty->assign('total_polls', $total_polls);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($polls));
include "footer.php";
?>