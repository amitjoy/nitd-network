<?php

$page = "user_video_upload";
include "header.php";

$task         = ( !empty($_POST['task'])          ? $_POST['task']          : ( !empty($_GET['task'])         ? $_GET['task']         : 'create'  ) );
$video_id     = ( !empty($_POST['video_id'])      ? $_POST['video_id']      : ( !empty($_GET['video_id'])     ? $_GET['video_id']     : NULL      ) );
$video_type   = ( !empty($_POST['video_type'])    ? $_POST['video_type']    : ( !empty($_GET['video_type'])   ? $_GET['video_type']   : 0         ) );


// User may not upload videos
if( (!$user->level_info['level_video_allow'] || empty($setting['setting_video_ffmpeg_path'])) && ($task=="create" || $task=="docreate" || $task=="upload") )
{
  header("Location: user_home.php");
  exit();
}

if( !$user->level_info['level_youtube_allow'] && ($task=="youtube" || $task=="doembed") )
{
  header("Location: user_home.php");
  exit();
}


// CREATE VIDEO OBJECT
$video = new se_video($user->user_info['user_id'], $video_id);


// User has too many videos
$total_videos = $video->video_total();
if( $total_videos >= $user->level_info['level_video_maxnum'] )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 5500201);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}




// GET PRIVACY SETTINGS
$level_video_privacy = unserialize($user->level_info['level_video_privacy']);
rsort($level_video_privacy);
$level_video_comments = unserialize($user->level_info['level_video_comments']);
rsort($level_video_comments);


// SET RESULT AND ERROR VARS
$result = FALSE;
$is_error = 0;
$show_uploader = 1;
$file_result = array();
$max_uploads = 1;





// Init default values
if( $task=="create"  || $task=="youtube")
{
  $video->video_info = array(
    'video_title' => "",
    'video_desc' => "",
    'video_search' => TRUE,
    'video_privacy' => $level_video_privacy[0],
    'video_comments' => $level_video_comments[0]
  );
}


if( $task=="upload" && $video->video_exists && !empty($video->video_info['video_uploaded']) )
{
  header("Location: user_video.php");
  exit();
}


// UPLOAD FILES
if( $task=="docreate" || $task=="doembed" )
{
  $video->video_info['video_title']     = censor($_POST['video_title']);
  $video->video_info['video_desc']      = censor(str_replace("\r\n", "<br>", $_POST['video_desc']));
  $video->video_info['video_search']    = $_POST['video_search'];
  $video->video_info['video_privacy']   = $_POST['video_privacy'];
  $video->video_info['video_comments']  = $_POST['video_comments'];

  // MAKE SURE SUBMITTED PRIVACY OPTIONS ARE ALLOWED, IF NOT, SET TO EVERYONE
  if( !$user->level_info['level_video_search'] )
    $video->video_info['video_search'] = TRUE;
  if( !in_array($video->video_info['video_privacy'], $level_video_privacy) )
    $video->video_info['video_privacy'] = $level_video_privacy[0];
  if( !in_array($video->video_info['video_comments'], $level_video_comments) )
    $video->video_info['video_comments'] = $level_video_comments[0];
  
  // GET YOUTUBE CODE
  $video_youtube_code = ( $task=="doembed" ? extract_youtube_code($_POST['video_url']) : NULL );
  
  // CREATE VIDEO
  $result = $video->video_edit(
    $video->video_info['video_title'],
    $video->video_info['video_desc'],
    $video->video_info['video_search'],
    $video->video_info['video_privacy'],
    $video->video_info['video_comments'],
    $video_type,
    $video_youtube_code
  );
  
  if( $task=="docreate" )
  {
    if( $result )
    {
      header("Location: user_video_upload.php?task=upload&video_id=".$video->video_info['video_id']);
      exit();
    }
    else
    {
      $task = "create";      
      $is_error = $video->is_error;
    }
  }
  if( $task=="doembed" )
  {
    if( $result )
    {
      header("Location: user_video.php?video_id=".$video->video_info['video_id']."&user=".$user->user_info['user_username']);
      exit();
    }
    else
    {
      $task = "youtube";      
      $is_error = $video->is_error;
    }
  }
}

elseif( $task=="doupload" )
{
  $isAjax = $_POST['isAjax'];
  $file_result = Array();

  // WORKAROUND FOR FLASH UPLOADER
  if($_FILES['file1']['type'] == "application/octet-stream" && $isAjax)
  { 
    $file_types = explode(",", str_replace(" ", "", strtolower($user->level_info['level_album_mimes'])));
    $_FILES['file1']['type'] = $file_types[0];
  }
  
  for( $f=1; $f<=$max_uploads; $f++ )
  {
    $fileid = "file".$f;
    if( empty($_FILES[$fileid]['name']))
      continue;
    
    $file_result[$fileid] = $video->video_upload("file1");
    $file_result[$fileid]['file_name'] = $file_result[$fileid]['name'] = $_FILES[$fileid]['name'];
    
    if( !$file_result[$fileid]['is_error'] )
    {
      $file_result[$fileid]['message'] = 1000086;
      $result = TRUE;
    }
    else
    {
      $file_result[$fileid]['message'] = $file_result[$fileid]['is_error'];
    }
    SE_Language::_preload($file_result[$fileid]['message']);
  }

  // OUTPUT JSON RESULT
  if( $isAjax )
  {
    SE_Language::load();
    
    if( $result )
    {
      $result = "success"; 
      $size = sprintf(SE_Language::_get($file_result['file1']['message']), $file_result['file1']['name']);
      $error = null; 
    }
    else
    {
      $result = "failure";
      $error = sprintf(SE_Language::_get($file_result['file1']['message']), $file_result['file1']['name']);
      $size = null;
    }
    if(!headers_sent()) { header('Content-type: application/json'); }
    echo json_encode(array('result' => $result, 'error' => $error, 'size' => $size));
    exit();
  }
  
  // SHOW PAGE WITH RESULTS
  else
  {
    $show_uploader = FALSE;
  }
  
  
  if( !$file_result['file1']['is_error'] )
  {
    header("Location: user_video_upload.php?task=complete&video_id=".$video->video_info['video_id']);
    exit();
  }
  
  else
  {
    $task = "upload";
    $is_error = $file_result['file1']['is_error'];
  }
}



// DO CREATE PAGE STUFF
if( $task=="create" || $task=="youtube")
{
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
  
  $smarty->assign('total_videos', $total_videos);
  $smarty->assign('video_title', $video->video_info['video_title']);
  $smarty->assign('video_desc', str_replace("<br>", "\r\n", $video->video_info['video_desc']));
  $smarty->assign('video_search', $video->video_info['video_search']);
  $smarty->assign('video_privacy', $video->video_info['video_privacy']);
  $smarty->assign('video_comments', $video->video_info['video_comments']);
  
  $smarty->assign('privacy_options', $privacy_options);
  $smarty->assign('comment_options', $comment_options);
}


// DO UPLOAD PAGE STUFF
if( $task=="upload" )
{
  // GET MAX FILESIZE ALLOWED
  $max_filesize_kb = ($user->level_info['level_video_maxsize']) / 1024;
  $max_filesize_kb = round($max_filesize_kb, 0);
  
  // START NEW SESSION AND SET SESSION VARS FOR UPLOADER
  // Backwards compatibility with <SE3.10
  if( !session_id() ) session_start();
  if( !empty($_COOKIE['user_id']) )
  {
    $_SESSION['ul_user_id'] = $_COOKIE['user_id'];
    $_SESSION['ul_user_email'] = $_COOKIE['user_email'];
    $_SESSION['ul_user_password'] = $_COOKIE['se_user_pass'];
  }
  
  // Keep with 3.10+
  $_SESSION['upload_token'] = md5(uniqid(rand(), true));
  $_SESSION['action'] = "user_video_upload.php";
  
  // SET INPUTS
  $inputs = Array('video_id' => $video->video_info['video_id'], 'task' => 'doupload');
  
  $smarty->assign('show_uploader', $show_uploader);
  $smarty->assign('session_id', session_id());
  $smarty->assign('upload_token', $_SESSION['upload_token']);
  $smarty->assign('file_result', $file_result);
  $smarty->assign('inputs', $inputs);
  $smarty->assign('allowed_exts', str_replace(",", ", ", $user->level_info['level_video_exts']));
  $smarty->assign('max_filesize', $max_filesize_kb);
  
  // SET UPLOADER PARAMS
  $smarty->assign('user_upload_max_files', 1);
  $smarty->assign('user_upload_max_size', $user->level_info['level_video_maxsize']);
  $smarty->assign('user_upload_allowed_extensions', $user->level_info['level_video_exts']);
}


// ASSIGN VARIABLES AND SHOW UPLOAD FILES PAGE
$smarty->assign('is_error', $is_error);
$smarty->assign('task', $task);
$smarty->assign('video_last_url', $_POST['video_url']);
$smarty->assign_by_ref('video', $video);
include "footer.php";
?>