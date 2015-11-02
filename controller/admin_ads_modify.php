<?php
$page = "admin_ads_modify";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['ad_id'])) { $ad_id = $_POST['ad_id']; } elseif(isset($_GET['ad_id'])) { $ad_id = $_GET['ad_id']; } else { $ad_id = 0; }


// VALIDATE AD ID
$adQuery = $database->database_query("SELECT * FROM se_ads WHERE ad_id='$ad_id'");
if($database->database_num_rows($adQuery) == 1) {
  $ad_info = $database->database_fetch_assoc($adQuery);
  $ad_exists = 1;
  $ad_id = $ad_info['ad_id'];
  $ad_html = $ad_info['ad_html'];
  $ad_name = $ad_info['ad_name'];
  $ad_date_start = $ad_info['ad_date_start'];
  $ad_date_end = $ad_info['ad_date_end'];
  if($ad_date_end != 0) { $ad_date_end_options = 1; }
  $ad_limit_views = $ad_info['ad_limit_views'];
  $ad_limit_clicks = $ad_info['ad_limit_clicks'];
  $ad_limit_ctr = $ad_info['ad_limit_ctr'];
  $ad_position = $ad_info['ad_position'];
  $ad_levels_array = $ad_info['ad_levels'];
  $ad_subnets_array = $ad_info['ad_subnets'];
  $ad_public = $ad_info['ad_public'];
  $ad_filename = $ad_info['ad_filename'];

// GET NEXT AD ID
} else {
  $qShowStatusResult = $database->database_query("SHOW TABLE STATUS LIKE 'se_ads'");
  while($row = $database->database_fetch_assoc($qShowStatusResult)) { $ad_id = $row['Auto_increment']; }
  $ad_exists = 0;
  $ad_date_start = time();
  $ad_date_end_options = 0;
  $ad_date_end = time()+60*60*2;
  $ad_limit_views = 0;
  $ad_limit_clicks = 0;
  $ad_limit_ctr = 0;
}


// SET EMPTY VARIABLES
$is_error = 0;





// CANCEL BANNER (DELETE FROM SERVER AFTER ITS BEEN UPLOADED)
if($task == "cancelbanner") {
  $banner_filename = $_POST['banner_filename_delete'];
  $bannerfile = "../uploads_admin/ads/$banner_filename";
  if(@file_exists($bannerfile)) { unlink($bannerfile); }
  echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'></head><body></body></html>";
  exit;




// DO BANNER UPLOAD
} elseif($task == "doupload") {

  // SET KEY VARIABLES
  $file_maxsize = "307200"; 
  $file_exts = Array('jpg', 'jpeg', 'gif', 'png');
  $file_types = Array('image/jpeg', 'image/jpg', 'image/jpe', 'image/pjpeg', 'image/pjpg', 'image/x-jpeg', 'x-jpg', 'image/gif', 'image/x-gif', 'image/png', 'image/x-png');
  $file_maxwidth = "1000"; 
  $file_maxheight = "1000";
  $ext = str_replace(".", "", strrchr($_FILES['file1']['name'], "."));
  $rand = rand(100000000, 999999999);
  $photo_newname = "banner$rand.".$ext;
  $file_dest = "../uploads_admin/ads/$photo_newname"; 
  $photo_name = "file1"; 
  $new_photo = new se_upload();
  $new_photo->new_upload($photo_name, $file_maxsize, $file_exts, $file_types, $file_maxwidth, $file_maxheight);

  // UPLOAD BANNER IF NO ERROR
  if($new_photo->is_error == 0) { $new_photo->upload_file($file_dest); }

  echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><script type='text/javascript'>";
  echo "window.parent.uploadbanner_result('$photo_newname', '".$new_photo->is_error."');";
  echo "</script></head><body></body></html>";
  exit();




// SAVE CAMPAIGN
} elseif($task == "dosave") {

  $ad_html = $_POST['ad_html'];
  $ad_name = $_POST['ad_name'];
  $ad_date_start_month = $_POST['ad_date_start_month'];
  $ad_date_start_day = $_POST['ad_date_start_day'];
  $ad_date_start_year = $_POST['ad_date_start_year'];
  $ad_date_start_hour = $_POST['ad_date_start_hour'];
  $ad_date_start_minute = $_POST['ad_date_start_minute'];
  $ad_date_start_ampm = $_POST['ad_date_start_ampm'];
  $ad_date_end_options = $_POST['ad_date_end_options'];
  $ad_date_end_month = $_POST['ad_date_end_month'];
  $ad_date_end_day = $_POST['ad_date_end_day'];
  $ad_date_end_year = $_POST['ad_date_end_year'];
  $ad_date_end_hour = $_POST['ad_date_end_hour'];
  $ad_date_end_minute = $_POST['ad_date_end_minute'];
  $ad_date_end_ampm = $_POST['ad_date_end_ampm'];
  $ad_limit_views = $_POST['ad_limit_views'];
  $ad_limit_views_unlimited = $_POST['ad_limit_views_unlimited'];
  $ad_limit_clicks = $_POST['ad_limit_clicks'];
  $ad_limit_clicks_unlimited = $_POST['ad_limit_clicks_unlimited'];
  $ad_limit_ctr = $_POST['ad_limit_ctr'];
  $ad_limit_ctr_unlimited = $_POST['ad_limit_ctr_unlimited'];
  $ad_position = $_POST['banner_position'];
  $ad_levels = $_POST['ad_levels'];
  $ad_subnets = $_POST['ad_subnets'];
  $ad_public = $_POST['ad_public'];
  $ad_filename = $_POST['banner_filename'];

  // CONVERT TO 24HR FORMAT
  $ad_date_start_hour_absolute = (($ad_date_start_hour == 12) ? 0 : $ad_date_start_hour) + $ad_date_start_ampm*12;
  $ad_date_start = $datetime->untimezone(mktime($ad_date_start_hour_absolute, $ad_date_start_minute, '0', $ad_date_start_month, $ad_date_start_day, $ad_date_start_year), $setting[setting_timezone]); 

  // CHECK FOR ERRORS
  if($ad_html == "") { $is_error = 386; }
  if($ad_name == "") { $is_error = 387; }
  if($ad_date_start_month == "" || $ad_date_start_day == "" || $ad_date_start_year == "" || $ad_date_start_hour == "" || $ad_date_start_minute == "" || $ad_date_start_ampm == "") {
    $is_error = 388;
  }

  // CHECK FOR END DATE
  if($ad_date_end_options == 0) {
    $ad_date_end = 0;
  } else {
    $ad_date_end_hour_absolute = (($ad_date_end_hour == 12) ? 0 : $ad_date_end_hour) + $ad_date_end_ampm*12;
    $ad_date_end = $datetime->untimezone(mktime($ad_date_end_hour_absolute, $ad_date_end_minute, '0', $ad_date_end_month, $ad_date_end_day, $ad_date_end_year), $setting[setting_timezone]); 
    if($ad_date_end_month == "" || $ad_date_end_day == "" || $ad_date_end_year == "" || $ad_date_end_hour == "" || $ad_date_end_minute == "" || $ad_date_end_ampm == "") {
      $is_error = 389;
    }
    if($ad_date_end <= $ad_date_start) { $is_error = 390; }
  }

  // DETERMINE VIEWS LIMIT
  if($ad_limit_views_unlimited == 1) {
    $ad_limit_views = 0;
  } else {
    if($ad_limit_views == "") { $is_error = 391; }
    $ad_limit_views = (int)str_replace(".", "", str_replace(",", "", $ad_limit_views));
  }

  // DETERMINE CLICKS LIMIT
  if($ad_limit_clicks_unlimited == 1) {
    $ad_limit_clicks = 0;
  } else {
    if($ad_limit_clicks == "") { $is_error = 392; }
    $ad_limit_clicks = (int)str_replace(".", "", str_replace(",", "", $ad_limit_clicks));
  }

  // DETERMINE MINIMUM CTR LIMIT
  if($ad_limit_ctr_unlimited == 1) {
    $ad_limit_ctr = 0;
  } else {
    $ad_limit_ctr = str_replace("%", "", $ad_limit_ctr);
    if(!is_numeric($ad_limit_ctr) || $ad_limit_ctr > 100) { $is_error = 393; }
  }


  // SET USER LEVELS AUDIENCE
  if($ad_levels) { $ad_levels_array = ",".implode(",", $ad_levels).","; }

  // SET SUBNETS AUDIENCE
  if($ad_subnets) { $ad_subnets_array = ",".implode(",", $ad_subnets).","; }

  // IF NO ERROR, INSERT INTO ADS TABLE
  if($is_error == 0) {

    // IF EDITING EXISTING AD
    if($ad_exists == 1) {
      if($ad_filename && $ad_info[ad_filename] && $ad_info[ad_filename] != $ad_filename) {
        $bannerfile = "../uploads_admin/ads/$ad_info[ad_filename]";
        if(@file_exists($bannerfile)) { @unlink($bannerfile); }
      }

      $database->database_query("UPDATE se_ads SET ad_name='$ad_name',
						 ad_date_start='$ad_date_start',
						 ad_date_end='$ad_date_end',
						 ad_limit_views='$ad_limit_views',
						 ad_limit_clicks='$ad_limit_clicks',
						 ad_limit_ctr='$ad_limit_ctr',
						 ad_public='$ad_public',
						 ad_position='$ad_position',
						 ad_levels='$ad_levels_array',
						 ad_subnets='$ad_subnets_array',
						 ad_html='$ad_html',
						 ad_filename='$ad_filename' WHERE ad_id='$ad_id' LIMIT 1");

    // IF CREATING NEW ADD
    } else {
      $database->database_query("INSERT INTO se_ads (ad_name,
							ad_date_start,
							ad_date_end,
							ad_limit_views,
							ad_limit_clicks,
							ad_limit_ctr,
							ad_public,
							ad_position,
							ad_levels,
							ad_subnets,
							ad_html,
							ad_filename
							) VALUES (
							'$ad_name',
							'$ad_date_start',
							'$ad_date_end',
							'$ad_limit_views',
							'$ad_limit_clicks',
							'$ad_limit_ctr',
							'$ad_public',
							'$ad_position',
							'$ad_levels_array',
							'$ad_subnets_array',
							'$ad_html',
							'$ad_filename')");
    }

    header("Location: admin_ads.php");
    exit;
  }
}


// GET USER LEVELS
$levels = $database->database_query("SELECT level_id, level_name, level_default FROM se_levels");
$level_array = Array();
$ad_levels_array = explode(",", $ad_levels_array);
while($level_info = $database->database_fetch_assoc($levels)) {
  $level_array[] = Array('level_id' => $level_info[level_id], 'level_name' => $level_info[level_name], 'level_default' => $level_info[level_default]);
}

// GET SUBNETS
$subnets = $database->database_query("SELECT subnet_id, subnet_name FROM se_subnets");
$subnet_array = Array();
$ad_subnets_array = explode(",", $ad_subnets_array);
while($subnet_info = $database->database_fetch_assoc($subnets)) {
  SE_Language::_preload($subnet_info[subnet_name]);
  $subnet_array[] = $subnet_info;
}

// ADD PERCENT SIGN TO MIN CTR VALUE
if($ad_limit_ctr != "") { $ad_limit_ctr .= "%"; }



// ASSIGN VARIABLES AND SHOW ADMIN ADS PAGE
$smarty->assign("ad_id", $ad_id);
$smarty->assign("ad_exists", $ad_exists);
$smarty->assign("is_error", $is_error);
$smarty->assign("ad_html", htmlspecialchars_decode($ad_html, ENT_QUOTES));
$smarty->assign("ad_html_encoded", $ad_html);
$smarty->assign("ad_filename", $ad_filename);
$smarty->assign("ad_name", $ad_name);
$smarty->assign("nowdate", $datetime->cdate('M j Y g:i A', $datetime->timezone(time(), $setting[setting_timezone])));
$smarty->assign("ad_date_start", $datetime->timezone($ad_date_start, $setting[setting_timezone]));
$smarty->assign("ad_date_end_options", $ad_date_end_options);
$smarty->assign("ad_date_end", $datetime->timezone($ad_date_end, $setting[setting_timezone]));
$smarty->assign("ad_limit_views", $ad_limit_views);
$smarty->assign("ad_limit_clicks", $ad_limit_clicks);
$smarty->assign("ad_limit_ctr", $ad_limit_ctr);
$smarty->assign("ad_position", $ad_position);
$smarty->assign("levels", $level_array);
$smarty->assign("subnets", $subnet_array);
$smarty->assign("ad_levels_array", $ad_levels_array);
$smarty->assign("ad_subnets_array", $ad_subnets_array);
$smarty->assign("ad_public", $ad_public);
include "admin_footer.php";
?>