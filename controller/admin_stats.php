<?php

$page = "admin_stats";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['graph'])) { $graph = $_POST['graph']; } elseif(isset($_GET['graph'])) { $graph = $_GET['graph']; } else { $graph = "visits"; }
if(isset($_POST['period'])) { $period = $_POST['period']; } elseif(isset($_GET['period'])) { $period = $_GET['period']; } else { $period = "week"; }
if(isset($_POST['start'])) { $start = $_POST['start']; } elseif(isset($_GET['start'])) { $start = $_GET['start']; } else { $start = 1; }


// CLEAR REFERRER LIST IF REQUESTED
if($task == "clearrefs") {
  $database->database_query("DELETE FROM se_statrefs");
  header("Location: admin_stats.php?graph=referrers");
  exit;
}

// GENERATE CHART DATA
if($task == "getdata") {

  // INCLUDE FLASH CHART FUNCTIONS
  include_once "../include/charts/charts.php";

  // SET CHART TYPE
  $chart['chart_type'] = "line";

  // SET STYLES
  $chart['chart_border'] = array('top_thickness' => 1,
                                 'bottom_thickness' => 1,
                                 'left_thickness' => 1,
                                 'right_thickness' => 1,
				 'color' => "666666");
  $chart['axis_category'] = array ('size' => 10,
                                   'color' => "333333");
  $chart['axis_value'] = array('size' => 10,
                               'color' =>  "333333");
  $chart['legend_label'] = array('size' => 12,
                                 'color' => "000000");
  $chart['chart_pref'] = array('line_thickness' => 2,
			       'point_shape' => "none",
			       'fill_shape' => true);
  $chart['chart_value'] = array('prefix' => "",
				'suffix' => "",
				'decimals' => 0,
				'separator' => "",
				'position' => "cursor",
				'hide_zero' => true,
				'as_percentage' => false,
				'font' => "arial",
				'bold' => true,
				'size' => 12,
				'color' => "000000",
				'alpha' => 75 );
  $chart['chart_grid_h'] = array('alpha' => 5,
				 'color' => "000000",
				 'thickness' => 1,
				 'type' => "solid");
  $chart['chart_grid_v'] = array('alpha' => 5,
				 'color' => "000000",
				 'thickness' => 1,
				 'type' => "solid");

  // GET LANGUAGE VARIABLES
  SE_Language::_preload_multi(508, 480, 481, 482, 512);
  SE_Language::load();

  // SET LEGEND LABEL AND QUERY VARIABLE
  $chart['chart_data'][0][0] = "";
  switch($graph) {
    case "visits":
      $var = "stat_views";
      $chart['chart_data'][1][0] = SE_Language::_get(508);
      break;
    case "logins":
      $var = "stat_logins";
      $chart['chart_data'][1][0] = SE_Language::_get(480);
      break;
    case "signups":
      $var = "stat_signups";
      $chart['chart_data'][1][0] = SE_Language::_get(481);
      break;
    case "friends":
      $var = "stat_friends";
      $chart['chart_data'][1][0] = SE_Language::_get(482);
      break;
    }

  // SET PERIOD
  switch($period) {
    case "week":
      $interval = "86400";
      $stat_date_format = "D";
      $date_compare = "j";
      $num_points = 8;
      if(date('w', time()) == 0) { $day_num = 7; } else { $day_num = date('w', time()); }
      $old_stat_date = mktime(0, 0, 0, date('n', time()), date('j', time())-$day_num+1-7*($start-1), date('Y', time()));
      $last_stat_date = mktime(0, 0, 0, date('n', time()), date('j', time())-$day_num+1-7*($start-1)+7, date('Y', time()));
      $chart['chart_data'][1][0] .= " (".SE_Language::_get(512)." ".$datetime->cdate("M jS", $old_stat_date).")";
      break;
    case "month":
      $interval = "86400";
      $stat_date_format = "j";
      $date_compare = "j";
      $num_points = date("t", time())+1;
      $old_stat_date = mktime(0, 0, 0, date('n', time())-($start-1), 1, date('Y', time()));
      $last_stat_date = mktime(0, 0, 0, date('n', time())-($start-1)+1, 1, date('Y', time()));
      $chart['chart_data'][1][0] .= " (".$datetime->cdate("F", $old_stat_date).")";
      break;
    case "year":
      $interval = "2678400";
      $stat_date_format = "M.";
      $date_compare = "n";
      $num_points = 13;
      $old_stat_date = mktime(0, 0, 0, 1, 1, date('Y', time())-($start-1));
      $last_stat_date = mktime(0, 0, 0, 1, 1, date('Y', time())-($start-1)+1);
      $chart['chart_data'][1][0] .= " (".$datetime->cdate("Y", $old_stat_date).")";
      break;
  }

  // RUN QUERY
  $stats = $database->database_query("SELECT stat_date, $var AS stat_var FROM se_stats WHERE stat_date<=$last_stat_date AND stat_date>=$old_stat_date ORDER BY stat_date ASC");

  // SET VARS
  $count = 0;
  $old_stat_date = $old_stat_date-$interval;

  // PUT STATS INTO ARRAY FOR GRAPH
  while($stat = $database->database_fetch_assoc($stats)) {
    while($stat[stat_date]-$old_stat_date>$interval) {
      $new_stat_date = $old_stat_date + $interval;
      $count++;
      $chart['chart_data'][0][$count] = date($stat_date_format, $new_stat_date);
      $chart['chart_data'][1][$count] = 0;
      $old_stat_date = $new_stat_date;
    }
    if(date($date_compare, $old_stat_date) == date($date_compare, $stat[stat_date])) {
      $chart['chart_data'][1][$count] += $stat[stat_var];
    } else {
      $count++;
      $chart['chart_data'][0][$count] = date($stat_date_format, $stat[stat_date]);
      $chart['chart_data'][1][$count] = $stat[stat_var];
      $old_stat_date = $stat[stat_date];
    }
  }

  while(count($chart['chart_data'][0])<$num_points) {
      $new_stat_date = $old_stat_date + $interval;
      $count++;
      $chart['chart_data'][0][$count] = date($stat_date_format, $new_stat_date);
      $chart['chart_data'][1][$count] = 0;
      $old_stat_date = $new_stat_date;
  }


  // OUTPUT CHART
  SendChartData($chart);
  exit();
}








// INCLUDE FLASH CHART FUNCTIONS FOR GRAPHS
include_once "../include/charts/charts.php";
$chart = InsertChart("../include/charts/charts.swf", "../include/charts/charts_library", "admin_stats.php?task=getdata&graph=$graph&period=$period&start=$start&uniqueID=".uniqid(rand(),true), 550, 400, "FFFFFF");









// OUTPUT SPACE USED INFO
if($graph == "space") {

  // CLEAR CHART
  $chart = "";
  $dbsize = 0;
  $mediasize = 0;
  $size_divisor = pow(1024, 2);

  // GET TOTAL DB SIZE
  $rows = $database->database_query("SHOW TABLE STATUS");
  while ($row = $database->database_fetch_assoc($rows)) {
    $dbsize += $row['Data_length'] + $row['Index_length'];
  }

  // GET SIZE OF USER FOLDER
  $mediasize = dirsize("../uploads_user/");

  // GET TOTAL SPACE ESTIMATION
  $totalspace = $mediasize + $dbsize;

  // Format numbers
  $dbsize     = number_format($dbsize/$size_divisor    , 2);
  $mediasize  = number_format($mediasize/$size_divisor , 2);
  $totalspace = number_format($totalspace/$size_divisor, 2);

}









if($graph == "referrers") {

  // CLEAR CHART
  $chart = "";

  $referrers = $database->database_query("SELECT * FROM se_statrefs ORDER BY statref_hits DESC LIMIT 100");
  $referrer_array = Array();
  $referrer_count = 0;
  while($referrer = $database->database_fetch_assoc($referrers)) {
    $referrer_array[$referrer_count] = Array('referrer_url' => $referrer[statref_url],
					     'referrer_hits' => $referrer[statref_hits]);
    $referrer_count++;
  }

}







if($graph == "summary") {

  // CLEAR CHART
  $chart = "";

  // GET QUICK STATISTICS
  $total_users = $database->database_fetch_assoc($database->database_query("SELECT count(*) AS total_users FROM se_users"));
  $total_messages = $database->database_fetch_assoc($database->database_query("SELECT count(*) AS total_messages FROM se_pms"));

  // GET TOTAL COMMENTS
  $total_comments = 0;
  $comment_tables = $database->database_query("SHOW TABLES FROM `$database_name` LIKE 'se_%comments'");
  while($table_info = $database->database_fetch_array($comment_tables)) {
    $comment_type = strrev(substr(strrev(substr($table_info[0], 3)), 8));
    $table_comments = $database->database_fetch_assoc($database->database_query("SELECT count(*) AS total_comments FROM se_".$comment_type."comments"));
    $total_comments += $table_comments[total_comments];
  }

}




// ASSIGN VARIABLES AND SHOW STATS PAGE
$smarty->assign('chart', $chart);
$smarty->assign('total_users_num', $total_users[total_users]);
$smarty->assign('total_messages_num', $total_messages[total_messages]);
$smarty->assign('total_comments_num', $total_comments);
$smarty->assign('referrers', $referrer_array);
$smarty->assign('referrers_total', $referrer_count);
$smarty->assign('graph', $graph);
$smarty->assign('period', $period);
$smarty->assign('start', $start);
$smarty->assign('media', $mediasize);
$smarty->assign('database', $dbsize);
$smarty->assign('totalspace', $totalspace);
include "admin_footer.php";
?>