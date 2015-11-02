<?php

$page = "user_album_upload";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = "main"; }
if(isset($_POST['album_id'])) { $album_id = $_POST['album_id']; } elseif(isset($_GET['album_id'])) { $album_id = $_GET['album_id']; } else { $album_id = 0; }

// ENSURE ALBUMS ARE ENABLED FOR THIS USER
if( !$user->level_info['level_album_allow'] )
{
  header("Location: user_home.php");
  exit();
}

// BE SURE ALBUM BELONGS TO THIS USER
$album = $database->database_query("SELECT * FROM se_albums WHERE album_id='{$album_id}' AND album_user_id='{$user->user_info['user_id']}' LIMIT 1");
if( !$database->database_num_rows($album) )
{
  header("Location: user_album.php");
  exit();
}

$album_info = $database->database_fetch_assoc($album);


// SET ALBUM
$album = new se_album($user->user_info['user_id']);

// SET RESULT AND ERROR VARS
$result = "";
$is_error = 0;
$show_uploader = 1;
$file_result = Array();

// GET TOTAL SPACE USED
$space_used = $album->album_space();
if($user->level_info['level_album_storage']) {
  $space_left = $user->level_info['level_album_storage'] - $space_used;
} else {
  $space_left = ( $dfs=disk_free_space("/") ? $dfs : pow(2, 32) );
} 



// UPLOAD FILES
if($task == "doupload")
{
  $isAjax = $_POST['isAjax'];
  $file_result = Array();

  // WORKAROUND FOR FLASH UPLOADER
  if($_FILES['file1']['type'] == "application/octet-stream" && $isAjax) { 
    $file_types = explode(",", str_replace(" ", "", strtolower($user->level_info['level_album_mimes'])));
    $_FILES['file1']['type'] = $file_types[0];
  }

  // RUN FILE UPLOAD FUNCTION FOR EACH SUBMITTED FILE
  $update_album = 0;
  $new_album_cover = "";
  $action_media = Array();
  for($f=1;$f<6;$f++)
  {
    $fileid = "file".$f;
    if( !empty($_FILES[$fileid]['name']) )
    {
      $file_result[$fileid] = $album->album_media_upload($fileid, $album_id, $space_left);
      if( !$file_result[$fileid]['is_error'] )
      {
        $file_result[$fileid]['message'] = 1000086;
        $new_album_cover = $file_result[$fileid]['media_id'];
        $media_path = str_replace('./', '', $url->url_userdir($user->user_info['user_id']).$file_result[$fileid]['media_id']."_thumb.jpg");
        $media_link = str_replace($url->url_base, '', $url->url_create('album_file', $user->user_info['user_username'], $album_id, $file_result[$fileid]['media_id']));
        
        if( file_exists($media_path) )
        {
          $media_width = $misc->photo_size($media_path, "100", "100", "w");
          $media_height = $misc->photo_size($media_path, "100", "100", "h");
          $action_media[] = Array(
            'media_link' => $media_link,
            'media_path' => $media_path,
            'media_width' => $media_width,
            'media_height' => $media_height
          );
        }
        $update_album = 1;
      }
      else
      {
        $file_result[$fileid]['message'] = $file_result[$fileid]['is_error'];
      }
      SE_Language::_preload($file_result[$fileid]['message']);
    }
  }

  // UPDATE ALBUM UPDATED DATE AND ALBUM COVER IF FILE UPLOADED
  if($update_album)
  {
    $newdate = time();
    if($album_info['album_cover'] != 0) { $new_album_cover = $album_info['album_cover']; }
    $database->database_query("UPDATE se_albums SET album_cover='{$new_album_cover}', album_dateupdated='{$newdate}' WHERE album_id='{$album_id}'");
    
    // UPDATE LAST UPDATE DATE (SAY THAT 10 TIMES FAST)
    $user->user_lastupdate();
    
    // INSERT ACTION
    $album_title = $album_info['album_title'];
    if(strlen($album_title) > 100) { $album_title = substr($album_title, 0, 97)."..."; }
    $actions->actions_add($user, "newmedia", Array($user->user_info['user_username'], $user->user_displayname, $album_id, $album_title), $action_media, 60, FALSE, "user", $user->user_info['user_id'], $album_info['album_privacy']);
  }

  // OUTPUT JSON RESULT
  if($isAjax)
  {
    SE_Language::load();
    if($update_album) {
      $result = "success"; 
      $size = sprintf(SE_Language::_get($file_result['file1']['message']), $file_result['file1']['file_name']);
      $error = null; 
    }
    else
    {
      $result = "failure";
      $error = sprintf(SE_Language::_get($file_result['file1']['message']), $file_result['file1']['file_name']);
      $size = null;
    }
    $json = '{"result":"'.$result.'","error":"'.$error.'","size":"'.$size.'"}';
    if(!headers_sent()) { header('Content-type: application/json'); }
    echo $json;
    exit();
  }
  
  // SHOW PAGE WITH RESULTS
  else
  {
   $show_uploader = 0;
  }
}

// END TASK



// FIND OUT IF ALBUM WAS JUST CREATED
if(isset($_GET['new_album']) AND $_GET['new_album'] == 1) { $new_album = 1; } else { $new_album = 0; }

// GET MAX FILESIZE ALLOWED
$max_filesize_kb = ($user->level_info['level_album_maxsize']) / 1024;
$max_filesize_kb = round($max_filesize_kb, 0);

// CONVERT UPDATED SPACE LEFT TO MB
$space_left_mb = ($space_left / 1024) / 1024;
$space_left_mb = round($space_left_mb, 2);


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
$_SESSION['upload_token'] = md5(uniqid(rand(), TRUE));
$_SESSION['action'] = "user_album_upload.php";


// SET INPUTS
$inputs = Array('album_id' => $album_info['album_id']);

// ASSIGN VARIABLES AND SHOW UPLOAD FILES PAGE
$smarty->assign('new_album', $new_album);
$smarty->assign('show_uploader', $show_uploader);
$smarty->assign('session_id', session_id());
$smarty->assign('upload_token', $_SESSION['upload_token']);
$smarty->assign('file_result', $file_result);
$smarty->assign('album_info', $album_info);
$smarty->assign('inputs', $inputs);
$smarty->assign('space_left', $space_left_mb);
$smarty->assign('allowed_exts', str_replace(",", ", ", $user->level_info['level_album_exts']));
$smarty->assign('max_filesize', $max_filesize_kb);

// SET UPLOADER PARAMS
$smarty->assign('user_upload_max_size', $user->level_info['level_album_maxsize']);
$smarty->assign('user_upload_allowed_extensions', $user->level_info['level_album_exts']);

include "footer.php";
?>