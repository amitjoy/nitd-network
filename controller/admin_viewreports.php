<?php

$page = "admin_viewreports";
include "admin_header.php";

if(isset($_POST['s'])) { $s = $_POST['s']; } elseif(isset($_GET['s'])) { $s = $_GET['s']; } else { $s = "id"; }
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
if(isset($_POST['f_object'])) { $f_object = $_POST['f_object']; } elseif(isset($_GET['f_object'])) { $f_object = $_GET['f_object']; } else { $f_object = ""; }
if(isset($_POST['f_reason'])) { $f_reason = $_POST['f_reason']; } elseif(isset($_GET['f_reason'])) { $f_reason = $_GET['f_reason']; } else { $f_reason = ""; }
if(isset($_POST['f_details'])) { $f_details = $_POST['f_details']; } elseif(isset($_GET['f_details'])) { $f_email = $_GET['f_details']; } else { $f_details = ""; }
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }


// DELETE SINGLE REPORT
if($task == "delete") {
  $report_id = $_GET['report_id'];
  $report_query = $database->database_query("SELECT report_id FROM se_reports WHERE report_id='$report_id' LIMIT 1");
  if($database->database_num_rows($report_query) != 0) {
    $database->database_query("DELETE FROM se_reports WHERE report_id='$report_id'");
  }
}


// DELETE MULTIPLE REPORTS
if($task == "delete_multi") {
  $delete = $_POST['delete'];
  if(count($delete) != 0) {
    $database->database_query("DELETE FROM se_reports WHERE report_id IN(".implode(",", $delete).")");
  }
}



// SET REPORT SORT-BY VARIABLES FOR HEADING LINKS
$i = "id";   // REPORT_ID
$u = "u";    // USER_USERNAME

// SET SORT VARIABLE FOR DATABASE QUERY
if($s == "i") {
  $sort = "se_reports.report_id";
  $i = "id";
} elseif($s == "id") {
  $sort = "se_reports.report_id DESC";
  $i = "i";
} elseif($s == "u") {
  $sort = "se_users.user_username";
  $u = "ud";
} elseif($s == "ud") {
  $sort = "se_users.user_username DESC";
  $u = "u";
} else {
  $sort = "se_reports.report_id DESC";
  $i = "i";
}


// CONSTRUCT QUERY USING FILTERS
$reports_query = "SELECT se_reports.*, se_users.user_id, se_users.user_username FROM se_reports LEFT JOIN se_users ON se_reports.report_user_id=se_users.user_id";

if($f_object != "") { $where_clause[] = "se_reports.report_object='$f_object'"; }
if($f_reason != "") { $where_clause[] = "se_reports.report_reason='$f_reason'"; }
if($f_details != "") { $where_clause[] = " se_reports.report_details LIKE '%$f_details%'"; }
if(count($where_clause) != 0) { $reports_query .= " WHERE ".implode(" AND ", $where_clause); }


// GET TOTAL REPORTS
$total_reports = $database->database_num_rows($database->database_query($reports_query));

// MAKE REPORTS PAGES
$reports_per_page = 100;
$page_vars = make_page($total_reports, $reports_per_page, $p);

$page_array = Array();
for($x=0;$x<=$page_vars[2]-1;$x++) {
  if($x+1 == $page_vars[1]) { $link = "1"; } else { $link = "0"; }
  $page_array[$x] = Array('page' => $x+1,
			  'link' => $link);
}

$reports_query .= " ORDER BY $sort LIMIT $page_vars[0], $reports_per_page";



// PULL REPORTS INTO AN ARRAY
$reports = $database->database_query($reports_query);
while($report_info = $database->database_fetch_assoc($reports)) { $report_array[] = $report_info; }



// ASSIGN VARIABLES AND SHOW VIEW REPORTS PAGE
$smarty->assign('total_reports', $total_reports);
$smarty->assign('pages', $page_array);
$smarty->assign('reports', $report_array);
$smarty->assign('i', $i);
$smarty->assign('u', $u);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('s', $s);
$smarty->assign('f_object', $f_object);
$smarty->assign('f_reason', $f_reason);
$smarty->assign('f_details', $f_details);
include "admin_footer.php";
?>