<?php

$page = "video";
include "header.php";


// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if( !$user->user_exists && !$setting['setting_permission_video'] )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

// DISPLAY ERROR PAGE IF NO OWNER
if( !$owner->user_exists )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 828);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}




if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['video_id'])) { $video_id = $_POST['video_id']; } elseif(isset($_GET['video_id'])) { $video_id = $_GET['video_id']; } else { $video_id = 0; }

// MAKE SURE VIDEO EXISTS
$video_query = $database->database_query("SELECT * FROM se_videos WHERE video_id='$video_id' AND video_user_id='{$owner->user_info['user_id']}' AND video_is_converted='1' LIMIT 1");

if($database->database_num_rows($video_query) != 1) { header("Location: ".$url->url_create('profile', $owner->user_info['user_username'])); exit(); }
$video_info = $database->database_fetch_assoc($video_query);

// CHECK PRIVACY
$privacy_max = $owner->user_privacy_max($user);
if(!($video_info[video_privacy] & $privacy_max)) {
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 5500148);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

// INSTANTIATE VIDEO AND SET VIDEO VARS
$video = new se_video($owner->user_info['user_id']);
$video_info['video_dir'] = $video->video_dir();

// ENSURE VIDEOS ARE ENABLED FOR THIS USER
if ((($video_info['video_type'] == 0) &&  (!$owner->level_info['level_video_allow'])) || (($video_info['video_type'] == 1) && (!$owner->level_info['level_youtube_allow'])))
      {
  header("Location: ".$url->url_create('profile', $owner->user_info['user_username']));
  exit();
}




$video_info['video_rating_full'] = floor($video_info['video_cache_rating']);
$video_info['video_rating_part'] = (($video_info['video_cache_rating']-$video_info['video_rating_full']) == 0) ? 0 : 1;
$video_info['video_rating_none'] = 5-$video_info['video_rating_full']-$video_info['video_rating_part'];
$video_info['video_rating_total'] = $database->database_num_rows($database->database_query("SELECT NULL FROM se_videoratings WHERE videorating_video_id='{$video_info['video_id']}'"));
// GET VIDEO COMMENT PRIVACY
$allowed_to_comment = ($privacy_max & $video_info['video_comments']);


// CHECK IF USER IS ALLOWED TO RATE
$allowed_to_rate = 1;
if($database->database_num_rows($database->database_query("SELECT NULL FROM se_videoratings WHERE videorating_video_id='{$video_info['video_id']}' AND videorating_user_id='{$user->user_info['user_id']}'")) != 0 || !$user->user_exists) { $allowed_to_rate = 0; }


// RATE VIDEO
if($task == "rate_do")
{
  $rating = (int) $_POST['rating'];

  if($allowed_to_rate)
  {
    if($rating <= 5 && $rating >= 1)
    {
      $database->database_query("INSERT INTO se_videoratings (videorating_video_id, videorating_user_id, videorating_rating) VALUES ('{$video_info['video_id']}', '{$user->user_info['user_id']}', '{$rating}')");

      // GET AVERAGE RATING / NUM VOTES FOR BAYESIAN WEIGHTED RATING
      $avg = $database->database_fetch_assoc($database->database_query("SELECT avg(video_cache_rating) AS average_rating, avg(video_cache_rating_total) AS average_total FROM se_videos WHERE video_cache_rating_total<>'0'"));

      // GET TOTAL RATING
      $new_rating = ($video_info['video_cache_rating']*$video_info['video_rating_total']+$rating)/($video_info['video_rating_total']+1);
      $new_total = $video_info['video_rating_total']+1;
      $new_rating_weighted = ( ($avg['average_total'] * $avg['average_rating']) + ($new_total * $new_rating) ) / ($avg['average_total'] + $new_total);

      // SET NEW CACHED RATINGS
      $database->database_query("UPDATE se_videos SET video_cache_rating='{$new_rating}', video_cache_rating_weighted='{$new_rating_weighted}', video_cache_rating_total='{$new_total}' WHERE video_id='{$video_info['video_id']}'");
      $allowed_to_rate = 0;
      $video_info['video_rating_full'] = floor($new_rating);
      $video_info['video_rating_part'] = (($new_rating-$video_info['video_rating_full']) == 0) ? 0 : 1;
      $video_info['video_rating_none'] = 5-$video_info['video_rating_full']-$video_info['video_rating_part'];
      $video_info['video_rating_total'] = $new_total;
    }
  }

  $response_array = array(
			'allowed_to_rate' => (bool) $allowed_to_rate,
			'rating_full' => (int) $video_info['video_rating_full'],
			'rating_part' => (int) $video_info['video_rating_part'],
			'rating_none' => (int) $video_info['video_rating_none'],
			'rating_total' => (int) $video_info['video_rating_total']
			);

  // OUTPUT JSON
  echo json_encode($response_array);
  exit();

}



// GET USER'S VIDEOS FOR CAROUSEL
$total_videos = $video->video_total("(video_is_converted='1')");
$video_array = $video->video_list(0, $video->video_total(), "video_id DESC", "(video_is_converted='1')", 1);


// GET VIDEO COMMENTS
$comment = new se_comment('video', 'video_id', $video_info['video_id']);
$total_comments = $comment->comment_total();


// UPDATE VIDEO VIEWS
if($user->user_info['user_id'] != $owner->user_info['user_id'])
{
  $video_views_new = $video_info['video_views'] + 1;
  $database->database_query("UPDATE se_videos SET video_views='{$video_views_new}' WHERE video_id='{$video_info['video_id']}' LIMIT 1");
}

// UPDATE NOTIFICATIONS
if($user->user_info['user_id'] == $owner->user_info['user_id'])
{
  $database->database_query("DELETE FROM se_notifys USING se_notifys LEFT JOIN se_notifytypes ON se_notifys.notify_notifytype_id=se_notifytypes.notifytype_id WHERE se_notifys.notify_user_id='{$owner->user_info['user_id']}' AND se_notifytypes.notifytype_name='videocomment' AND notify_object_id='{$video_info['video_id']}'");
}





// SET GLOBAL PAGE TITLE
$global_page_title[0] = 5500151;
$global_page_title[1] = $owner->user_displayname;
$global_page_title[2] = $video_info['video_title'];
$global_page_description[0] = 5500102;
$global_page_description[1] = $video_info['video_desc'];

// ASSIGN VARIABLES AND DISPLAY VIDEO PAGE
$smarty->assign('video_info', $video_info);
$smarty->assign('total_comments', $total_comments);
$smarty->assign('allowed_to_comment', $allowed_to_comment);
$smarty->assign('allowed_to_rate', $allowed_to_rate);
$smarty->assign('videos', $video_array);
$smarty->assign('total_videos', $total_videos);
include "footer.php";
?>