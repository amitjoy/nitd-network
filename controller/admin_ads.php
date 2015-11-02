<?php

$page = "admin_ads";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['ad_id'])) { $ad_id = $_POST['ad_id']; } elseif(isset($_GET['ad_id'])) { $ad_id = $_GET['ad_id']; } else { $ad_id = 0; }
if(isset($_POST['s'])) { $s = $_POST['s']; } elseif(isset($_GET['s'])) { $s = $_GET['s']; } else { $s = "id"; }




// PAUSE AN AD CAMPAIGN
if($task == "pause") {

  // CHECK IF AD CAMPAIGN EXISTS AND PAUSE
  $ad_query = $database->database_query("SELECT ad_id FROM se_ads WHERE ad_id='$ad_id' LIMIT 1");
  if($database->database_num_rows($ad_query) == 1) {
    $database->database_query("UPDATE se_ads SET ad_paused='1' WHERE ad_id='$ad_id' LIMIT 1");
  }



// UNPAUSE AN AD CAMPAIGN
} elseif($task == "unpause") {

  // CHECK IF AD CAMPAIGN EXISTS AND UNPAUSE
  $ad_query = $database->database_query("SELECT ad_id FROM se_ads WHERE ad_id='$ad_id' LIMIT 1");
  if($database->database_num_rows($ad_query) == 1) {
    $database->database_query("UPDATE se_ads SET ad_paused='0' WHERE ad_id='$ad_id' LIMIT 1");
  }


// DELETE A SINGLE AD CAMPAIGN
} elseif($task == "delete") {

  $ad_query = $database->database_query("SELECT ad_id, ad_filename FROM se_ads WHERE ad_id='$ad_id' LIMIT 1");
  if($database->database_num_rows($ad_query) == 1) {
    $database->database_query("DELETE FROM se_ads WHERE ad_id='$ad_id' LIMIT 1");
    $ad_info = $database->database_fetch_assoc($ad_query);
    $bannerfile = "../uploads_admin/ads/$ad_info[ad_filename]";
    if(@file_exists($bannerfile)) {
      @unlink($bannerfile);
    }
  }

}









// SET AD CAMPIGN SORT-BY VARIABLES FOR HEADING LINKS
$i = "id";   // AD_ID
$n = "n";    // AD_NAME
$v = "vd";    // NUMBER OF VIEWS
$c = "cd";    // NUMBER OF CLICKS

// SET SORT VARIABLE FOR DATABASE QUERY
if($s == "i") {
  $sort = "ad_id";
  $i = "id";
} elseif($s == "id") {
  $sort = "ad_id DESC";
  $i = "i";
} elseif($s == "n") {
  $sort = "ad_name";
  $n = "nd";
} elseif($s == "nd") {
  $sort = "ad_name DESC";
  $n = "n";
} elseif($s == "v") {
  $sort = "ad_total_views";
  $v = "vd";
} elseif($s == "vd") {
  $sort = "ad_total_views DESC";
  $v = "v";
} elseif($s == "c") {
  $sort = "ad_total_clicks";
  $c = "cd";
} elseif($s == "cd") {
  $sort = "ad_total_clicks DESC";
  $c = "c";
} else {
  $sort = "ad_id DESC";
  $i = "i";
}




// GET ADS FOR MAIN LIST
$ads = $database->database_query("SELECT * FROM se_ads ORDER BY $sort");
$ad_array = Array();
while($ad_info = $database->database_fetch_assoc($ads)) {

  // DETERMINE CTR
  if($ad_info[ad_total_clicks] == 0) {
    $ad_info[ad_ctr] = "0.00%";
  } elseif($ad_info[ad_total_clicks] > 0) {
    $ad_info[ad_ctr] = round($ad_info[ad_total_clicks] / $ad_info[ad_total_views], 4) * 100;
    if(strlen($ad_info[ad_ctr]) == 1 || strlen($ad_info[ad_ctr]) == 2) {
      $ad_info[ad_ctr] .= ".00";
    }
    $ad_info[ad_ctr] .= "%";
  }

  $ad_array[] = Array('ad_id' => $ad_info[ad_id],
			'ad_name' => $ad_info[ad_name],
			'ad_status' => $ad_status,
			'ad_paused' => $ad_info[ad_paused],
			'ad_total_views' => $ad_info[ad_total_views],
			'ad_total_clicks' => $ad_info[ad_total_clicks],
			'ad_ctr' => $ad_info[ad_ctr],
			'ad_date_start' => $ad_info[ad_date_start],
			'ad_date_end' => $ad_info[ad_date_end]);
}


// ASSIGN VARIABLES AND SHOW ADMIN ADS PAGE
$smarty->assign('s', $s);
$smarty->assign('i', $i);
$smarty->assign('n', $n);
$smarty->assign('v', $v);
$smarty->assign('c', $c);
$smarty->assign('ads', $ad_array);
$smarty->assign('nowdate', time());
include "admin_footer.php";
?>