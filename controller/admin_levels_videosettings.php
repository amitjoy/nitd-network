<?php

$page = "admin_levels_videosettings";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = "main"; }
if(isset($_POST['level_id'])) { $level_id = $_POST['level_id']; } elseif(isset($_GET['level_id'])) { $level_id = $_GET['level_id']; } else { $level_id = 0; }

// VALIDATE LEVEL ID
$level = $database->database_query("SELECT * FROM se_levels WHERE level_id='$level_id'");
if($database->database_num_rows($level) != 1) { header("Location: admin_levels.php"); exit(); }
$level_info = $database->database_fetch_assoc($level);

// SET RESULT VARIABLE
$result = 0;
$is_error = 0;


// SAVE CHANGES
if($task == "dosave") {
  $level_info[level_video_allow] = $_POST['level_video_allow'];
  $level_info[level_youtube_allow] = $_POST['level_youtube_allow'];
  $level_info[level_video_maxsize] = $_POST['level_video_maxsize'];
  $level_info[level_video_maxnum] = $_POST['level_video_maxnum'];
  $level_info[level_video_search] = $_POST['level_video_search'];
  $level_info[level_video_privacy] = is_array($_POST['level_video_privacy']) ? $_POST['level_video_privacy'] : Array();
  $level_info[level_video_comments] = is_array($_POST['level_video_comments']) ? $_POST['level_video_comments'] : Array();

  // GET PRIVACY AND PRIVACY DIFFERENCES
  if( empty($level_info[level_video_privacy]) || !is_array($level_info[level_video_privacy]) ) $level_info[level_video_privacy] = array(63);
  rsort($level_info[level_video_privacy]);
  $new_privacy_options = $level_info[level_video_privacy];
  $level_info[level_video_privacy] = serialize($level_info[level_video_privacy]);

  // GET COMMENT AND COMMENT DIFFERENCES
  if( empty($level_info[level_video_comments]) || !is_array($level_info[level_video_comments]) ) $level_info[level_video_comments] = array(63);
  rsort($level_info[level_video_comments]);
  $new_comments_options = $level_info[level_video_comments];
  $level_info[level_video_comments] = serialize($level_info[level_video_comments]);

  // CHECK THAT A NUMBER GREATER THAN 1 WAS ENTERED FOR MAXSIZE
  if(!is_numeric($level_info[level_video_maxsize]) || $level_info[level_video_maxsize] < 1) {
    $is_error = 5500154;
 
  // CHECK THAT MAX VIDEOS IS A NUMBER
  } elseif(!is_numeric($level_info[level_video_maxnum]) || $level_info[level_video_maxnum] < 1) {
    $is_error = 5500155;

  } else {

    $level_info[level_video_maxsize] = $level_info[level_video_maxsize]*1024;
    $database->database_query("UPDATE se_levels SET 
			level_video_search='$level_info[level_video_search]',
			level_video_privacy='$level_info[level_video_privacy]',
			level_video_comments='$level_info[level_video_comments]',
			level_video_allow='$level_info[level_video_allow]',
			level_youtube_allow='$level_info[level_youtube_allow]',
			level_video_maxnum='$level_info[level_video_maxnum]',
			level_video_maxsize='$level_info[level_video_maxsize]'
			WHERE level_id='{$level_info['level_id']}'
    ");
    
    if( !$level_info[level_video_search] )
    {
      $database->database_query("UPDATE se_videos INNER JOIN se_users ON se_users.user_id=se_videos.video_user_id SET se_videos.video_search='1' WHERE se_users.user_level_id='{$level_info['level_id']}'") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    }
    
    $database->database_query("UPDATE se_videos INNER JOIN se_users ON se_users.user_id=se_videos.video_user_id SET se_videos.video_privacy='{$new_privacy_options[0]}' WHERE se_users.user_level_id='$level_info[level_id]' AND se_videos.video_privacy NOT IN('".join("','", $new_privacy_options)."')") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    $database->database_query("UPDATE se_videos INNER JOIN se_users ON se_users.user_id=se_videos.video_user_id SET se_videos.video_comments='{$new_comments_options[0]}' WHERE se_users.user_level_id='$level_info[level_id]' AND se_videos.video_comments NOT IN('".join("','", $new_comments_options)."')") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    $result = 1;
  }

} // END DOSAVE TASK



// GET MAXSIZE INTO KB AGAIN
$level_info[level_video_maxsize] = $level_info[level_video_maxsize]/1024;

// GET PREVIOUS PRIVACY SETTINGS
for($c=6;$c>0;$c--) {
  $priv = pow(2, $c)-1;
  if(user_privacy_levels($priv) != "") {
    SE_Language::_preload(user_privacy_levels($priv));
    $privacy_options[$priv] = user_privacy_levels($priv);
  }
}

for($c=6;$c>=0;$c--) {
  $priv = pow(2, $c)-1;
  if(user_privacy_levels($priv) != "") {
    SE_Language::_preload(user_privacy_levels($priv));
    $comment_options[$priv] = user_privacy_levels($priv);
  }
}




// ASSIGN VARIABLES AND SHOW video SETTINGS PAGE
$smarty->assign('result', $result);
$smarty->assign('ffmpeg_path', $setting['setting_video_ffmpeg_path']);
$smarty->assign('is_error', $is_error);
$smarty->assign('level_info', $level_info);
$smarty->assign('level_video_privacy', unserialize($level_info[level_video_privacy]));
$smarty->assign('level_video_comments', unserialize($level_info[level_video_comments]));
$smarty->assign('video_privacy', $privacy_options);
$smarty->assign('video_comments', $comment_options);
include "admin_footer.php";
?>