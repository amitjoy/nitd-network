<?php

$page = "admin_viewpolls";
include "admin_header.php";

if(isset($_POST['s'])) { $s = $_POST['s']; } elseif(isset($_GET['s'])) { $s = $_GET['s']; } else { $s = "id"; }
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
if(isset($_POST['f_title'])) { $f_title = $_POST['f_title']; } elseif(isset($_GET['f_title'])) { $f_title = $_GET['f_title']; } else { $f_title = ""; }
if(isset($_POST['f_owner'])) { $f_owner = $_POST['f_owner']; } elseif(isset($_GET['f_owner'])) { $f_owner = $_GET['f_owner']; } else { $f_owner = ""; }
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }

if(isset($_POST['poll_id'])) { $poll_id = $_POST['poll_id']; } elseif(isset($_GET['poll_id'])) { $poll_id = $_GET['poll_id']; } else { $poll_id = 0; }
if(isset($_POST['delete_polls'])) { $delete_polls = $_POST['delete_polls']; } elseif(isset($_GET['delete_polls'])) { $delete_polls = $_GET['delete_polls']; } else { $delete_polls = NULL; }


// CREATE poll OBJECT
$entries_per_page = 100;
$poll = new se_poll();

// DELETE SINGLE ENTRY
if( $task=="deleteentry" )
{
  $poll->poll_delete($poll_id);
}

if( $task=="delete" && is_array($delete_polls) && !empty($delete_polls) )
{ 
  $poll->poll_delete($delete_polls); 
}

// SET poll ENTRY SORT-BY VARIABLES FOR HEADING LINKS
$i = "id";   // poll_ID
$t = "t";    // poll_TITLE
$o = "o";    // OWNER OF ENTRY
$v = "v";    // TOTAL VOTES OF ENTRY
$d = "d";    // DATE OF ENTRY

// SET SORT VARIABLE FOR DATABASE QUERY
if($s == "i") {
  $sort = "se_polls.poll_id";
  $i = "id";
} elseif($s == "id") {
  $sort = "se_polls.poll_id DESC";
  $i = "i";
} elseif($s == "t") {
  $sort = "se_polls.poll_title";
  $t = "td";
} elseif($s == "td") {
  $sort = "se_polls.poll_title DESC";
  $t = "t";
} elseif($s == "o") {
  $sort = "se_users.user_username";
  $o = "od";
} elseif($s == "od") {
  $sort = "se_users.user_username DESC";
  $o = "o";
} elseif($s == "v") {
  $sort = "se_polls.poll_totalvotes";
  $v = "vd";
} elseif($s == "vd") {
  $sort = "se_polls.poll_totalvotes DESC";
  $v = "v";
} elseif($s == "d") {
  $sort = "se_polls.poll_datecreated";
  $d = "dd";
} elseif($s == "dd") {
  $sort = "se_polls.poll_datecreated DESC";
  $d = "d";
} else {
  $sort = "se_polls.poll_id DESC";
  $i = "i";
}

// ADD CRITERIA FOR FILTER
$where = "";
if($f_owner != "") { $where .= "se_users.user_username LIKE '%$f_owner%'"; }
if($f_owner != "" & $f_title != "") { $where .= " AND"; }
if($f_title != "") { $where .= " se_polls.poll_title LIKE '%$f_title%'"; }
if($where != "") { $where = "(".$where.")"; }

// DELETE NECESSARY ENTRIES
$start = ($p - 1) * $entries_per_page;


// GET TOTAL ENTRIES
$total_polls = $poll->poll_total($where);

// MAKE ENTRY PAGES
$page_vars = make_page($total_polls, $entries_per_page, $p);
$page_array = Array();
for($x=0;$x<=$page_vars[2]-1;$x++) {
  if($x+1 == $page_vars[1]) { $link = "1"; } else { $link = "0"; }
  $page_array[$x] = Array('page' => $x+1,
			  'link' => $link);
}

// GET ENTRY ARRAY
$polls = $poll->poll_list($page_vars[0], $entries_per_page, $sort, $where, 1);

// ASSIGN VARIABLES AND SHOW VIEW ENTRIES PAGE
$smarty->assign('total_polls', $total_polls);
$smarty->assign('pages', $page_array);
$smarty->assign('polls', $polls);
$smarty->assign('f_title', $f_title);
$smarty->assign('f_owner', $f_owner);
$smarty->assign('i', $i);
$smarty->assign('t', $t);
$smarty->assign('o', $o);
$smarty->assign('v', $v);
$smarty->assign('d', $d);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('s', $s);
include "admin_footer.php";
?>