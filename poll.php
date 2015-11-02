<?php

$page = "poll";
include "header.php";


$poll_id = ( !empty($_POST['poll_id']) ? $_POST['poll_id'] : ( !empty($_GET['poll_id']) ? $_GET['poll_id'] : NULL ) );


// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if( (!$user->user_exists && !$setting['setting_permission_poll']) || ($user->user_exists && (1 & ~(int)$user->level_info['level_poll_allow'])) )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}


// INIT POLL OBJECT
$poll_object = new se_poll($owner->user_info['user_id'], $poll_id);
$poll_object->poll_info['poll_voted_array'] = explode(",", $poll_object->poll_info['poll_voted']);


// DISPLAY ERROR PAGE IF NO OWNER
if( !$owner->user_exists || !$poll_object->poll_exists )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 828);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}


// ENSURE POLLS ARE ENABLED FOR THIS USER
if( (4 & ~(int)$owner->level_info['level_poll_allow']) || $poll_object->poll_info['poll_user_id']!=$owner->user_info['user_id'] )
{
  header("Location: ".$url->url_create('profile', $owner->user_info['user_username']));
  exit();
}


// GET PRIVACY LEVELS
$privacy_max = $owner->user_privacy_max($user);
$allowed_to_view = ($privacy_max & $poll_object->poll_info['poll_privacy']);
$allowed_to_comment = ($privacy_max & $poll_object->poll_info['poll_comments']);

if( !$allowed_to_view )
{
  header("Location: ".$url->url_create('profile', $owner->user_info['user_username']));
  exit();
}


// GET POLL COMMENTS
$comment = new se_comment('poll', 'poll_id', $poll_object->poll_info['poll_id']);
$total_comments = $comment->comment_total();


// UPDATE POLL VIEWS
$poll_object->poll_view();


// UPDATE NOTIFICATIONS
if( $user->user_info['user_id']==$owner->user_info['user_id'])
{
  $database->database_query("
    DELETE FROM
      se_notifys
    USING
      se_notifys
    LEFT JOIN
      se_notifytypes
      ON se_notifys.notify_notifytype_id=se_notifytypes.notifytype_id
    WHERE
      se_notifys.notify_user_id='{$owner->user_info['user_id']}' AND
      se_notifytypes.notifytype_name='pollcomment' AND
      notify_object_id='{$poll_object->poll_info['poll_id']}'
  ");
}


// SMARTY
$smarty->assign('total_comments', $total_comments);
$smarty->assign('allowed_to_view', $allowed_to_view);
$smarty->assign('allowed_to_comment', $allowed_to_comment);
$smarty->assign('poll_object', $poll_object);
$smarty->assign('poll_id', $poll_id);
include "footer.php";
?>