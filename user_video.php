<?php

$page = "user_video";
include "header.php";

$task = ( isset($_POST['task']) ? $_POST['task'] : NULL );

// ENSURE VIDEO IS ENABLED FOR THIS USER
if( !($user->level_info['level_video_allow'] || $user->level_info['level_youtube_allow']) )
{
  header("Location: user_home.php");
  exit();
}


// CREATE VIDEO OBJECT
$video = new se_video($user->user_info['user_id']);

// GET PRIVACY SETTINGS
$level_video_privacy = unserialize($user->level_info['level_video_privacy']);
rsort($level_video_privacy);
$level_video_comments = unserialize($user->level_info['level_video_comments']);
rsort($level_video_comments);


// DELETE VIDEO
if( $task == "delete" )
{
  $video->video_delete($_POST['video_id']);
}

// EDIT VIDEO
elseif( $task == "edit" )
{
  $video_id = $_POST['video_id'];
  $video_title = censor($_POST['video_title']);
  $video_desc = censor(str_replace("\r\n", "<br>", $_POST['video_desc']));
  $video_search = $_POST['video_search'];
  $video_privacy = $_POST['video_privacy'];
  $video_comments = $_POST['video_comments'];

  // MAKE SURE SUBMITTED PRIVACY OPTIONS ARE ALLOWED, IF NOT, SET TO EVERYONE
  if(!in_array($video_privacy, $level_video_privacy)) { $video_privacy = $level_video_privacy[0]; }
  if(!in_array($video_comments, $level_video_comments)) { $video_comments = $level_video_comments[0]; }

  $database->database_query("UPDATE se_videos SET video_title='$video_title', video_desc='$video_desc', video_search='$video_search', video_privacy='$video_privacy', video_comments='$video_comments' WHERE video_id='$video_id' AND video_user_id='{$user->user_info['user_id']}'");
}


// GET VIDEOS
$total_videos = $video->video_total("(video_is_converted<>'-1')");
$video_array = $video->video_list(0, $video->video_total(), "video_is_converted, video_id DESC", "", 1);


// GET PREVIOUS PRIVACY SETTINGS
for($c=0;$c<count($level_video_privacy);$c++) {
  if(user_privacy_levels($level_video_privacy[$c]) != "") {
    SE_Language::_preload(user_privacy_levels($level_video_privacy[$c]));
    $privacy_options[$level_video_privacy[$c]] = user_privacy_levels($level_video_privacy[$c]);
  }
}

for($c=0;$c<count($level_video_comments);$c++) {
  if(user_privacy_levels($level_video_comments[$c]) != "") {
    SE_Language::_preload(user_privacy_levels($level_video_comments[$c]));
    $comment_options[$level_video_comments[$c]] = user_privacy_levels($level_video_comments[$c]);
  }
}


// ASSIGN VARIABLES AND SHOW VIEW VIDEOS PAGE
$smarty->assign('videos_total', $total_videos);
$smarty->assign('videos', $video_array);
$smarty->assign('privacy_options', $privacy_options);
$smarty->assign('comment_options', $comment_options);
include "footer.php";
?>