<?php

/* $Id$ */

define('SE_PAGE_AJAX', TRUE);
$page = "album_ajax";
include "header.php";


// This is ajax
header("Content-Type: application/json");


// Get info (post only)
$task = ( isset($_POST['task']) ? $_POST['task'] : NULL );
$album_id = ( isset($_POST['album_id']) ? $_POST['album_id'] : NULL );
$media_id = ( isset($_POST['media_id']) ? $_POST['media_id'] : NULL );


// Must be logged in to use tasks below this section
if( !$user->user_exists )
{
  echo json_encode(array('result' => FALSE, 'err' => 1));
  exit();
}

// Create album object
$album = new se_album($user->user_info['user_id']);


// Album delete
if( $task=="album_delete" )
{
  // Verify album exists and user is owner
  $sql = "SELECT album_id FROM se_albums WHERE album_id='{$album_id}' AND album_user_id='{$user->user_info['user_id']}'";
  $resource = $database->database_query($sql);
  if( !$database->database_num_rows($resource) )
  {
    echo json_encode(array('result' => FALSE, 'err' => 2));
    exit();
  }
  
  // Execute
  $album->album_delete($album_id);
  
  echo json_encode(array('result' => TRUE));
  exit();
}


// Album Moveup
elseif( $task=="album_moveup" )
{
  // Verify album exists and user is owner
  $sql = "SELECT album_id, album_order FROM se_albums WHERE album_id='{$album_id}' AND album_user_id='{$user->user_info['user_id']}'";
  $resource = $database->database_query($sql);
  if( !$database->database_num_rows($resource) )
  {
    echo json_encode(array('result' => FALSE, 'err' => 3));
    exit();
  }
  
  $album_info = $database->database_fetch_assoc($resource);
  
  // Move album up
  $resource = $database->database_query("SELECT album_id, album_order FROM se_albums WHERE album_user_id='{$user->user_info['user_id']}' AND album_order<'{$album_info['album_order']}' ORDER BY album_order DESC LIMIT 1");
  if( !$database->database_num_rows($resource) )
  {
    echo json_encode(array('result' => FALSE, 'err' => 4));
    exit();
  }

  $prev_info = $database->database_fetch_assoc($resource);
  
  // SWITCH ORDER
  $database->database_query("UPDATE se_albums SET album_order='{$prev_info['album_order']}' WHERE album_id='{$album_info['album_id']}' LIMIT 1");
  $database->database_query("UPDATE se_albums SET album_order='{$album_info['album_order']}' WHERE album_id='{$prev_info['album_id']}' LIMIT 1");
  
  echo json_encode(array(
    'result' => TRUE,
    'current_album_id' => $album_info['album_id'],
    'previous_album_id' => $prev_info['album_id']
  ));
  exit();
}


// Media Rotate
elseif( $task=="media_rotate" )
{
  $degrees = $_POST['degrees'];
  
  // Must be 90 or 270
  if( $degrees!=90 && $degrees!=270 )
  {
    echo json_encode(array('result' => FALSE, 'err' => 5));
    exit();
  }
  
  // ROTATE IMAGE
  $album->album_media_rotate($media_id, $degrees);
  
  // SET THUMBPATH
  $thumb_path = $url->url_userdir($user->user_info['user_id']).$media_id."_thumb.jpg?".rand();
  
  echo json_encode(array(
    'result' => TRUE,
    'path' => $thumb_path
  ));
  exit();
}


// Media Moveup
elseif( $task=="media_moveup" )
{
  $media_query = $database->database_query("SELECT media_id, media_order, media_album_id FROM se_media LEFT JOIN se_albums ON se_media.media_album_id=se_albums.album_id WHERE media_id='{$media_id}' AND se_albums.album_user_id='{$user->user_info['user_id']}'");
  if( !$database->database_num_rows($media_query) )
  {
    echo json_encode(array('result' => FALSE, 'err' => 6));
    exit();
  }
  
  $media_info = $database->database_fetch_assoc($media_query);
  
  $prev_query = $database->database_query("SELECT media_id, media_order FROM se_media LEFT JOIN se_albums ON se_media.media_album_id=se_albums.album_id WHERE se_media.media_album_id='{$media_info['media_album_id']}' AND se_albums.album_user_id='{$user->user_info['user_id']}' AND media_order<'{$media_info['media_order']}' ORDER BY media_order DESC LIMIT 1");
  if( !$database->database_num_rows($prev_query) )
  {
    echo json_encode(array('result' => FALSE, 'err' => 7));
    exit();
  }
  
  $prev_info = $database->database_fetch_assoc($prev_query);
  
  // SWITCH ORDER
  $database->database_query("UPDATE se_media SET media_order='{$prev_info['media_order']}' WHERE media_id='{$media_info['media_id']}'");
  $database->database_query("UPDATE se_media SET media_order='{$media_info['media_order']}' WHERE media_id='{$prev_info['media_id']}'");
  
  // SEND AJAX CONFIRMATION
  echo json_encode(array(
    'result' => TRUE,
    'current_media_id' => $media_info['media_id'],
    'previous_media_id' => $prev_info['media_id']
  ));
  exit();
}


// Media Cover
elseif( $task=="media_cover" )
{
  $resource = $database->database_query("SELECT NULL FROM se_media WHERE media_album_id='{$album_id}' && media_id='{$media_id}' LIMIT 1");
  if( !$database->database_num_rows($resource) )
  {
    echo json_encode(array('result' => FALSE, 'err' => 8));
    exit();
  }
  
  // Set as cover
  $newdate = time();
  $database->database_query("UPDATE se_albums SET album_cover='{$media_id}', album_dateupdated='{$newdate}' WHERE album_id='{$album_id}' LIMIT 1");
  
  // Update user
  $user->user_lastupdate();
  
  echo json_encode(array(
    'result' => TRUE
  ));
  exit();
}


// Media Delete
elseif( $task=="media_delete" )
{
  $result = $album->album_media_delete($media_id); 
  
  echo json_encode(array(
    'result' => $result
  ));
  exit();
}


// Media Move
elseif( $task=="media_move" )
{
  $result = $album->album_media_move($media_id, $album_id); 
  
  echo json_encode(array(
    'result' => $result
  ));
  exit();
}


?>