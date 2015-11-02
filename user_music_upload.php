<?php
$page = "user_music_upload";
include "header.php";

$task   = ( !empty($_POST['task'])    ? $_POST['task']    : ( !empty($_GET['task'])   ? $_GET['task']   : NULL  ) );
$isAjax = ( !empty($_POST['isAjax'])  ? $_POST['isAjax']  : ( !empty($_GET['isAjax']) ? $_GET['isAjax'] : FALSE ) );


// ENSURE MUSIC IS ENABLED FOR THIS USER
if( !$user->level_info['level_music_allow'] ) { header("Location: user_home.php"); exit(); }


// JSON INCLUDES
if( $isAjax && !function_exists('json_encode') )
{
  include_once "include/xmlrpc/xmlrpc.inc";
  include_once "include/xmlrpc/xmlrpcs.inc";
  include_once "include/xmlrpc/xmlrpc_wrappers.inc";
  include_once "include/jsonrpc/jsonrpc.inc";
  include_once "include/jsonrpc/jsonrpcs.inc";
  include_once "include/jsonrpc/json_extension_api.inc";
}


// SET RESULT AND ERROR VARS
$result = "";
$is_error = FALSE;
$show_uploader = FALSE;
$file_result = array();


// SET MUSIC
$music = new se_music($user->user_info['user_id']);


// UPLOAD FRAME
if( $task=="doupload" )
{
  $isAjax = $_POST['isAjax'];
  $file_result = array();

  // WORKAROUND FOR FLASH UPLOADER
  if( $_FILES['file1']['type']=="application/octet-stream" && $isAjax )
  { 
    $file_types = explode(",", str_replace(" ", "", strtolower($user->level_info['level_music_mimes'])));
    $_FILES['file1']['type'] = $file_types[0];
  }
  
  // GET TOTAL SPACE USED
  $space_used = $music->music_space();
  if($user->level_info[level_music_storage]) {
    $space_left = $user->level_info['level_music_storage'] - $space_used;
  } else {
    $space_left = ( $dfs=disk_free_space("/") ? $dfs : pow(2, 32) );
  }
  
  // RUN FILE UPLOAD FUNCTION FOR EACH SUBMITTED FILE
  $action_music = array();
  for( $file_index=1; $file_index<6; $file_index++ )
  {
    $file_param = "file{$file_index}";
    if( empty($_FILES[$file_param]) ) continue;
    
    $file_result[$file_param] = $music->music_upload($file_param, $space_left);
    
    if( !$file_result[$file_param]['is_error'] )
    {
      $file_result[$file_param]['message'] = 4000085;
      
      // INSERT ACTION
      $actions->actions_add($user, "newmusic", array($user->user_info['user_username'], $user->user_displayname));
      
      // UPDATE LAST UPDATE DATE (SAY THAT 10 TIMES FAST)
      $user->user_lastupdate();
    }
    else
    {
      $file_result[$file_param]['message'] = $file_result[$file_param]['is_error'];
    }
    SE_Language::_preload($file_result[$file_param]['message']);
  }

  // OUTPUT JSON RESULT
  if( $isAjax )
  {
    SE_Language::load();
    if( !$file_result[$file_param]['is_error'] )
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
    if( !headers_sent() ) { header('Content-type: application/json'); }
    $json_response = json_encode(array('result'=>$result,'error'=>$error,'size'=>$size));
    if( function_exists('handleErrorInSocialEngine') ) handleErrorInSocialEngine(E_USER_ERROR, $json_response);
    echo $json_response;
    exit();
  }

  // SHOW PAGE WITH RESULTS
  else
  {
    $show_uploader = 0;
  }
}




// GET TOTAL SPACE USED
$space_used = $music->music_space();
if($user->level_info[level_music_storage]) {
  $space_left = $user->level_info['level_music_storage'] - $space_used;
} else {
  $space_left = ( $dfs=disk_free_space("/") ? $dfs : pow(2, 32) );
}


// GET MAX FILESIZE ALLOWED
$max_filesize_kb = ($user->level_info[level_music_maxsize] / 1024) / 1024;
$max_filesize_kb = round($max_filesize_kb, 0);


// CONVERT UPDATED SPACE LEFT TO MB
$space_left_mb = ($space_left / 1024) / 1024;
$space_left_mb = round($space_left_mb, 2);


// START NEW SESSION AND SET SESSION VARS FOR UPLOADER
session_start();
$_SESSION = array();
session_regenerate_id();
$_SESSION['upload_token'] = md5(uniqid(rand(), true));
$_SESSION['action'] = "user_music_upload.php";
$_SESSION['user_id'] = $_COOKIE['user_id'];
$_SESSION['user_email'] = $_COOKIE['user_email'];
$_SESSION['user_password'] = $_COOKIE['user_password'];

// SET INPUTS
$inputs = array();


// ASSIGN VARIABLES AND SHOW UPLOAD FILES PAGE
$smarty->assign('music_title', $music->music_title);
$smarty->assign('allowed_exts', str_replace(",", ", ", $user->level_info[level_music_exts]));

$smarty->assign('space_left', $space_left_mb);
$smarty->assign('max_filesize', $max_filesize_kb);

$smarty->assign('show_uploader', $show_uploader);
$smarty->assign('inputs', $inputs);
$smarty->assign('file_result', $file_result);
$smarty->assign('session_id', session_id());
$smarty->assign('upload_token', $_SESSION['upload_token']);
include "footer.php";
?>