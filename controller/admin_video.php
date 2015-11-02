<?php

$page = "admin_video";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = "main"; }


// SET RESULT VARIABLE
$result = 0;


// SAVE CHANGES
if($task == "dosave") {
  $setting[setting_permission_video] = $_POST['setting_permission_video'];
  $setting[setting_video_ffmpeg_path] = $_POST['setting_video_ffmpeg_path'];
  $setting[setting_video_width] = $_POST['setting_video_width'];
  $setting[setting_video_height] = $_POST['setting_video_height'];
  $setting[setting_video_thumb_width] = $_POST['setting_video_thumb_width'];
  $setting[setting_video_thumb_height] = $_POST['setting_video_thumb_height'];
  $setting[setting_video_mimes] = $_POST['setting_video_mimes'];
  $setting[setting_video_exts] = $_POST['setting_video_exts'];
  $setting[setting_video_max_jobs] = $_POST['setting_video_max_jobs'];
  $setting[setting_video_cronjob] = $_POST['setting_video_cronjob'];
  
  // ENSURE THAT WIDTHS/HEIGHTS ARE EVEN
  if($setting[setting_video_width]%2 != 0) { $setting[setting_video_width] = $setting[setting_video_width]+1; }
  if($setting[setting_video_height]%2 != 0) { $setting[setting_video_height] = $setting[setting_video_height]+1; }
  if($setting[setting_video_thumb_width]%2 != 0) { $setting[setting_video_thumb_width] = $setting[setting_video_thumb_width]+1; }
  if($setting[setting_video_thumb_height]%2 != 0) { $setting[setting_video_thumb_height] = $setting[setting_video_thumb_height]+1; }

  $database->database_query("UPDATE se_settings SET 
			setting_permission_video='$setting[setting_permission_video]',
			setting_video_ffmpeg_path='$setting[setting_video_ffmpeg_path]',
			setting_video_width='$setting[setting_video_width]',
			setting_video_height='$setting[setting_video_height]',
			setting_video_thumb_width='$setting[setting_video_thumb_width]',
			setting_video_thumb_height='$setting[setting_video_thumb_height]',
			setting_video_mimes='$setting[setting_video_mimes]',
			setting_video_exts='$setting[setting_video_exts]',
			setting_video_max_jobs='$setting[setting_video_max_jobs]',
			setting_video_cronjob='$setting[setting_video_cronjob]'");

  $result = 1;
}


// ASSIGN VARIABLES AND SHOW VIDEO SETTINGS PAGE
$smarty->assign('result', $result);
include "admin_footer.php";
?>