<?php

$page = "user_report";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = "main"; }
if(isset($_POST['return_url'])) { $return_url = $_POST['return_url']; } elseif(isset($_GET['return_url'])) { $return_url = $_GET['return_url']; } else { $return_url = ""; }

// EXIT IF USER IS NOT LOGGED IN
if( !$user->user_exists ) { exit(); }

// MAKE SURE RETURN URL IS SET
if($return_url == "") { exit(); }

// ENCODE RETURN URL
$return_url = urldecode($return_url);
$return_url = str_replace("&amp;", "&", $return_url);

// SET VARS
$submitted = 0;


// SEND REPORT
if($task == "dosend")
{
  // ENCODE RETURN URL
  $return_url = urlencode($return_url);

  // GET REPORT REASON AND DETAILS
  $report_reason = $_POST['report_reason'];
  $report_details = $_POST['report_details'];

  // ADD TO DATABASE
  $database->database_query("INSERT INTO se_reports (report_user_id, 
				       report_url, 
				       report_reason, 
				       report_details) VALUES (
				      '{$user->user_info['user_id']}',
				      '{$return_url}', 
				      '{$report_reason}', 
				      '{$report_details}')");

  // IF DATABASE HAS OVER 5000 REPORTS, CLEAN THEM OUT
  $reports_total = $database->database_num_rows($database->database_query("SELECT report_id FROM se_reports"));
  if($reports_total > 5000)
  {
    $database->database_query("DELETE FROM se_reports WHERE report_id ORDER BY report_id ASC LIMIT 100");
  }

  $submitted = 1;
}




// ASSIGN SMARTY VARIABLES AND INCLUDE FOOTER
$smarty->assign('return_url', $return_url);
$smarty->assign('submitted', $submitted);
include "footer.php";
?>