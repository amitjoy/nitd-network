<?php

$page = "quiz_result";
include "header.php";


$task = ( isset($_POST['task']) && $_POST['task'] ) ? trim($_POST['task']) : '';
$task = ( !$task && (isset($_GET['task']) && $_GET['task']) ) ? $_GET['task'] : $task;

$quiz_id = ( isset($_GET['quiz_id']) && $_GET['quiz_id'] ) ? (int)$_GET['quiz_id'] : 0;

$result_id = he_quiz::user_result($user->user_info['user_id'], $quiz_id);

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if ( !$user->user_exists || !$quiz_id || !$result_id )
{
	$page = "error";
	$smarty->assign('error_header', 639);
	$smarty->assign('error_message', 656);
	$smarty->assign('error_submit', 641);
	include "footer.php";
}

$quiz_info = he_quiz::get_quiz_info($quiz_id);
$quiz_result = he_quiz::result_info($result_id);

$friend_list = $user->user_friend_list(0, 10);
$message = array( 'title' => SE_Language::get(690691154), 'text' => SE_Language::get(690691155), 'type' => 'success' ); 
$photo_url = he_quiz::photo_url();

$smarty->assign('message', $message);
$smarty->assign('quiz_info', $quiz_info);
$smarty->assign('quiz_result', $quiz_result);
$smarty->assign('photo_url', $photo_url);


include "footer.php";
?>