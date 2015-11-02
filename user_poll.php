<?php

$page = "user_poll";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
if(isset($_POST['s'])) { $s = $_POST['s']; } elseif(isset($_GET['s'])) { $s = $_GET['s']; } else { $s = "dd"; }
if(isset($_POST['search'])) { $search = $_POST['search']; } elseif(isset($_GET['search'])) { $search = $_GET['search']; } else { $search = ""; }

// ENSURE POLLS ARE ENABLED FOR THIS USER
if( 4 & ~(int)$user->level_info['level_poll_allow'] )
{
  header("Location: user_home.php");
  exit();
}

// SET ENTRY SORT-BY VARIABLES FOR HEADING LINKS
$d = "dd";    // poll_DATE
$t = "t";     // poll_TITLE
$c = "cd";    // TOTAL_COMMENTS

// SET SORT VARIABLE FOR DATABASE QUERY
if($s == "d") {
  $sort = "poll_date";
  $d = "dd";
} elseif($s == "dd") {
  $sort = "poll_datecreated DESC";
  $d = "d";
} elseif($s == "t") {
  $sort = "poll_title";
  $t = "td";
} elseif($s == "td") {
  $sort = "poll_title DESC";
  $t = "t";
} elseif($s == "c") {
  $sort = "total_comments";
  $c = "cd";
} elseif($s == "cd") {
  $sort = "total_comments DESC";
  $c = "c";
} else {
  $sort = "poll_datecreated DESC";
  $d = "d";
}

// SET WHERE CLAUSE
if($search != "") { $where = "(poll_title LIKE '%{$search}%' OR poll_desc LIKE '%{$search}%' OR poll_options LIKE '%{$search}%')"; } else { $where = ""; }

// CREATE POLL OBJECT
$entries_per_page = $user->level_info['level_poll_entries'];
$poll = new se_poll($user->user_info['user_id']);

// DELETE NECESSARY ENTRIES
$start = ($p - 1) * $entries_per_page;
if($task == "delete") { $poll->polls_delete($start, $entries_per_page, $sort, $where); }

// GET TOTAL ENTRIES
$total_polls = $poll->poll_total($where);

// MAKE ENTRY PAGES
$page_vars = make_page($total_polls, $entries_per_page, $p);

// GET ENTRY ARRAY
$polls = $poll->poll_list($page_vars[0], $entries_per_page, $sort, $where);

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