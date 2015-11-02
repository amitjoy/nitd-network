<?php

$page = "quiz";
include "header.php";

$task = ( isset($_POST['task']) && $_POST['task'] ) ? trim($_POST['task']) : '';
$task = ( !$task && (isset($_GET['task']) && $_GET['task']) ) ? $_GET['task'] : $task;

$quiz_id = ( isset($_GET['quiz_id']) && $_GET['quiz_id'] ) ? (int)$_GET['quiz_id'] : 0;

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if ( !$user->user_exists )
{
	$page = "error";
	$smarty->assign('error_header', 639);
	$smarty->assign('error_message', 656);
	$smarty->assign('error_submit', 641);
	include "footer.php";
}

$quiz_info = he_quiz::get_quiz_info($quiz_id);

if (!($quiz_info)){
	$page = "error";
	$smarty->assign('error_header', 639);
	$smarty->assign('error_message', 690691178);
	$smarty->assign('error_submit', 641);
	include "footer.php";
}

$steps = he_quiz::check_steps($quiz_id); 

if ( array_sum($steps) < 4 || $quiz_info['status']==0 )
{
	$page = "error";
	$smarty->assign('error_header', 639);
	$smarty->assign('error_message', 690691179);
	$smarty->assign('error_submit', 641);
	include "footer.php";
}

$quiz_questions = he_quiz::get_quiz_questions($quiz_id);

$message = array();

if ( $task == 'get_result' )
{
	$answers = $_POST['answer'];
	$result_id = he_quiz::save_user_play($user->user_info['user_id'], $quiz_id, $answers);
	
	if ( $result_id )
	{
		$replace_arr = array( $user->user_info['user_username'], $user->user_displayname, $quiz_id, $quiz_info['name'] );
		$actions->actions_add($user, 'playquiz', $replace_arr);
	}
	
	header("Location: quiz_result.php?quiz_id=$quiz_id");
	exit();
}

$photo_url = he_quiz::photo_url();

$smarty->assign('quiz_info', $quiz_info);
$smarty->assign('quiz_questions', $quiz_questions);
$smarty->assign('photo_url', $photo_url);

include "footer.php";
?>