<?php

$page = "admin_video_utilities";
include "admin_header.php";

$task         = ( !empty($_POST['task'])          ? $_POST['task']          : ( !empty($_GET['task'])         ? $_GET['task']         : NULL  ) );
$file         = ( !empty($_POST['file'])          ? $_POST['file']          : ( !empty($_GET['file'])         ? $_GET['file']         : NULL  ) );

$video_object = new se_video();


// :( Get the files each page view just so we can show or hide the log browser button
$log_files = array();
$log_files_short = array();
$dir = '../uploads_video/encoding/debug';
if($handle = opendir($dir))
{
  while(($file = readdir($handle)) !== false)
  {
    if($file != '.' && $file != '..')
    {
      $size = filesize('../uploads_video/encoding/debug/'.$file);
      
      // Guess file type
      $type = "Unknown";
      if( $file=="index.php" )
      {
        // Ignore index.php
        continue;
      }
      elseif( preg_match('/^\d+$/', $file) )
      {
        $type = "Video Encoding Shell Script";
      }
      elseif( preg_match('/^\d+\.ffmpeg\.log$/', $file) )
      {
        $type = "Ffmpeg Log File";
      }
      elseif( preg_match('/^\d+\.log$/', $file) )
      {
        $type = "Video Encoding Shell Script Log File";
      }
      elseif( preg_match('/^\d+_complete/', $file) )
      {
        // Ignore completed/duration files
        continue;
      }
      
      $log_files[] = array('file' => $file, 'size' => $size, 'type' => $type);
      $log_files_short[] = $file;
    }
  }
  closedir($handle);
}


$log_browser_enabled = ( $video_object->debug && !empty($log_files_short) );




if( $task=="version" )
{
  $result = NULL;
  exec($setting['setting_video_ffmpeg_path'].' -version 2>&1', $result);
  $smarty->assign('version_output', join("\n", $result));
}

elseif( $task=="formats" )
{
  $result = NULL;
  exec($setting['setting_video_ffmpeg_path'].' -formats 2>&1', $result);
  $smarty->assign('format_output', join("\n", $result));
}

elseif( $task=="logbrowse" )
{
  // Moved
}

elseif( $task=="logfile" )
{
  $file = str_replace('/', '', $file);
  $file = str_replace('\\', '', $file);
  $file = str_replace('..', '', $file);
  
  if( in_array($file, $log_files_short) )
    $log_content = file('../uploads_video/encoding/debug/'.$file);
  else
    $log_content = "Invalid file";
  
  $smarty->assign('log_output', join("\n", $log_content));
  $smarty->assign('log_filename', $file);
}


$smarty->assign('task', $task);
$smarty->assign('log_browser_enabled', $log_browser_enabled);
$smarty->assign_by_ref('log_files', $log_files);
$smarty->assign_by_ref('video_object', $video_object);
include "admin_footer.php";
?>