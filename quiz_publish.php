<?php


$page = "quiz_publish";
include "header.php";


$task = ( isset($_POST['task']) && $_POST['task'] ) ? trim($_POST['task']) : '';
$task = ( !$task && (isset($_GET['task']) && $_GET['task']) ) ? $_GET['task'] : $task;

$quiz_id = ( isset($_GET['quiz_id']) && $_GET['quiz_id'] ) ? (int)$_GET['quiz_id'] : false;

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if ( !$user->user_exists )
{
	$page = "error";
	$smarty->assign('error_header', 639);
	$smarty->assign('error_message', 656);
	$smarty->assign('error_submit', 641);
	include "footer.php";
}

if ( !$quiz_id || $user->user_info['user_id'] != he_quiz::get_owner($quiz_id) )
{
	$page = "error";
	$smarty->assign('error_header', 639);
	$smarty->assign('error_message', 656);
	$smarty->assign('error_submit', 641);
	include "footer.php";
}

$message = array();

$steps = he_quiz::check_steps($quiz_id);

if ( !$steps['publish'] )
{
	$page = "error";
	$smarty->assign('error_header', 639);
	$smarty->assign('error_message', 656);
	$smarty->assign('error_submit', 641);
	include "footer.php";
}


if ( $task == 'publish_quiz' )
{
	$status = ( $_POST['publish'] ) ? 1 : 0;
	he_quiz::publish_quiz($quiz_id, $status);
	
	header("Location: user_quiz.php");
}


$smarty->assign('quiz_id', $quiz_id);
$smarty->assign('steps', $steps);
$smarty->assign('message', $message);

include "footer.php";
?>