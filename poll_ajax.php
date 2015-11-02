<?php

$page = "poll_ajax";
include "header.php";

// SET VARS
$task = ( !empty($_POST['task']) ? $_POST['task'] : ( !empty($_GET['task']) ? $_GET['task'] : NULL ) );
$vote = ( isset($_POST['vote']) ? $_POST['vote'] : ( isset($_GET['vote']) ? $_GET['vote'] : NULL ) );
$poll_id = ( !empty($_POST['poll_id']) ? $_POST['poll_id'] : ( !empty($_GET['poll_id']) ? $_GET['poll_id'] : NULL ) );
$poll_closed = ( !empty($_POST['poll_closed']) ? $_POST['poll_closed'] : ( !empty($_GET['poll_closed']) ? $_GET['poll_closed'] : NULL ) );
$poll_profile = ( !empty($_POST['poll_profile']) ? $_POST['poll_profile'] : ( !empty($_GET['poll_profile']) ? $_GET['poll_profile'] : NULL ) );

$is_error = FALSE;


// VOTE
if( $task=="votepoll" )
{
  if( !$user->user_exists || (2 & ~(int)$user->level_info['level_poll_allow']) )
  {
    echo json_encode(array('result' => 'failure'));
    exit();
  }
  
  $poll_object = new se_poll(NULL, $poll_id);
  $result = $poll_object->poll_vote($vote);
  
  $poll_info = $poll_object->poll_info;
  unset($poll_info['poll_voted']);
  
  
  if( !$result )
  {
    SE_Language::_preload($poll_object->is_error);
    SE_Language::load();
    echo json_encode(array(
      'result' => 'failure',
      'message' => SE_Language::_get($poll_object->is_error),
      'debug' => $vote . ' ' . $poll_object->poll_info['poll_answers'][$vote]
    ));
    exit();
  }
  
  echo json_encode($poll_info);
  
  exit();
}


// INFO
elseif( $task=="infopoll" )
{
  if( (!$user->user_exists && !$setting['setting_permission_poll']) || ($user->user_exists && (1 & ~(int)$user->level_info['level_poll_allow'])) )
  {
    echo json_encode(array('result' => 'failure'));
    exit();
  }
  
  $poll_object = new se_poll(NULL, $poll_id);
  //$poll_object = new se_poll($user->user_info['user_id'], $poll_id);
  $poll_info = $poll_object->poll_info;
  unset($poll_info['poll_voted']);
  
  // SEND
  echo json_encode($poll_info);
}


// CLOSE
elseif( $task=="togglepoll" )
{
  if( !$user->user_exists || (4 & ~(int)$user->level_info['level_poll_allow']) )
  {
    echo json_encode(array('result' => 'failure'));
    exit();
  }
  
  $poll_object = new se_poll($user->user_info['user_id'], $poll_id);
  
  if( $poll_id && $poll_object->poll_toggle_closed($poll_closed) )
    echo '{"result":"success"}';
  else
    echo '{"result":"failure"}';
  
  exit();
}


// DELETE
elseif( $task=="deletepoll" )
{
  if( !$user->user_exists || (4 & ~(int)$user->level_info['level_poll_allow']) )
  {
    echo json_encode(array('result' => 'failure'));
    exit();
  }
  
  $poll_object = new se_poll($user->user_info['user_id'], $poll_id);
  
  if( $poll_id && $poll_object->poll_delete($poll_id) )
    echo '{"result":"success"}';
  else
    echo '{"result":"failure"}';
  
  exit();
}

?>