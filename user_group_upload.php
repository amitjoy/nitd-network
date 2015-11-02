<?php

$page = "user_group_upload";
include "header.php";

if(isset($_POST['group_id'])) { $group_id = $_POST['group_id']; } elseif(isset($_GET['group_id'])) { $group_id = $_GET['group_id']; } else { $group_id = 0; }
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }

// ENSURE GROUPS ARE ENABLED FOR THIS USER
if( ~(int)$user->level_info['level_group_allow'] & 1 )
{
  //header("Location: user_home.php");
  exit();
}

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if( !$user->user_exists && $setting['setting_permission_group'] ) { exit(); }

// INITIALIZE GROUP OBJECT
$group = new se_group($user->user_info[user_id], $group_id);
if($group->group_exists == 0) { exit(); }

// CHECK IF USER IS ALLOWED TO UPLOAD PHOTOS
$privacy_max = $group->group_privacy_max($user);
if(!($privacy_max & $group->group_info['group_privacy'])) { exit(); }
if(!($privacy_max & $group->group_info['group_upload'])) { exit(); }

// GET ALBUM INFO
$groupalbum_info = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_groupalbums WHERE groupalbum_group_id='{$group->group_info['group_id']}' LIMIT 1"));


// SET RESULT AND ERROR VARS
$result = "";
$is_error = 0;
$show_uploader = 1;
$file_result = Array();

// GET TOTAL SPACE USED
$space_used = $group->group_media_space();
if( $group->groupowner_level_info['level_group_album_storage'] )
{
  $space_left = $group->groupowner_level_info['level_group_album_storage'] - $space_used;
}
else
{
  $space_left = ( $dfs=disk_free_space("/") ? $dfs : pow(2, 32) );
} 



// UPLOAD FILES
if($task == "doupload")
{
  $isAjax = $_POST['isAjax'];
  $file_result = Array();

  // WORKAROUND FOR FLASH UPLOADER
  if($_FILES['file1']['type'] == "application/octet-stream" && $isAjax) { 
    $file_types = explode(",", str_replace(" ", "", strtolower($group->groupowner_level_info['level_group_album_mimes'])));
    $_FILES['file1']['type'] = $file_types[0];
  }

  // RUN FILE UPLOAD FUNCTION FOR EACH SUBMITTED FILE
  $update_album = 0;
  $action_media = Array();
  for($f=1;$f<6;$f++)
  {
    $fileid = "file".$f;
    if($_FILES[$fileid]['name'] != "")
    {
      $file_result[$fileid] = $group->group_media_upload($fileid, $groupalbum_info['groupalbum_id'], $space_left);
      if($file_result[$fileid]['is_error'] == 0)
      {
        $file_result[$fileid]['message'] = 2000248;
        $media_path = str_replace('./', '', $group->group_dir($group->group_info['group_id']).$file_result[$fileid]['groupmedia_id']."_thumb.jpg");
        $media_link = "group_album_file.php?group_id={$group->group_info['group_id']}&groupmedia_id={$file_result[$fileid]['groupmedia_id']}";
        
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
    $database->database_query("UPDATE se_groupalbums SET groupalbum_dateupdated='".time()."' WHERE groupalbum_id='{$groupalbum_info['groupalbum_id']}'");

    // UPDATE LAST UPDATE DATE (SAY THAT 10 TIMES FAST)
    $group->group_lastupdate();

    // INSERT ACTION
    $group_title = $group->group_info['group_title'];
    if(strlen($group_title) > 100) { $group_title = substr($group_title, 0, 97)."..."; }
    $actions->actions_add($user, "newgroupmedia", Array($user->user_info['user_username'], $user->user_displayname, $group->group_info['group_id'], $group_title), $action_media, 60, FALSE, "group", $group->group_info['group_id'], $group->group_info['group_privacy']);
  }

  // OUTPUT JSON RESULT
  if($isAjax)
  {
    SE_Language::load();
    if($update_album)
    {
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

} // END TASK



// GET MAX FILESIZE ALLOWED
$max_filesize_kb = ($group->groupowner_level_info['level_group_album_maxsize']) / 1024;
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
$_SESSION['upload_token'] = md5(uniqid(rand(), true));
$_SESSION['action'] = "user_group_upload.php";


// SET INPUTS
$inputs = Array('group_id' => $group->group_info['group_id']);


// ASSIGN VARIABLES AND SHOW UPLOAD FILES PAGE
$smarty->assign('show_uploader', $show_uploader);
$smarty->assign('session_id', session_id());
$smarty->assign('upload_token', $_SESSION['upload_token']);
$smarty->assign('file_result', $file_result);
$smarty->assign('groupalbum_info', $groupalbum_info);
$smarty->assign('inputs', $inputs);
$smarty->assign('space_left', $space_left_mb);
$smarty->assign('allowed_exts', str_replace(",", ", ", $group->groupowner_level_info['level_group_album_exts']));
$smarty->assign('max_filesize', $max_filesize_kb);

// SET UPLOADER PARAMS
$smarty->assign('user_upload_allowed_extensions', $group->groupowner_level_info['level_group_album_exts']);
$smarty->assign('user_upload_max_size', $group->groupowner_level_info['level_group_album_maxsize']);

include "footer.php";
?>