<?php

$page = "user_quiz";
include "header.php";


$task = ( isset($_POST['task']) && $_POST['task'] ) ? trim($_POST['task']) : '';
$task = ( !$task && (isset($_GET['task']) && $_GET['task']) ) ? $_GET['task'] : $task;

$current_page = ( isset($_GET['page']) && $_GET['page'] ) ? (int)$_GET['page'] : 1;
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

$message = array();

if ( $task == 'delete' )
{ 
	he_quiz::delete_quiz($quiz_id);

	header("Location: user_quiz.php");
	exit();
}
elseif ( $task == 'publish' || $task == 'unpublish' )
{
	$status = ( $task == 'publish' ) ? 1 : 0;
	
	he_quiz::publish_quiz($quiz_id, $status);
	
	header("Location: user_quiz.php?page=" . $current_page);
	exit();	
}

//TODO get from configs
$on_page = 10;
$pages = 5;

$first = ( $current_page - 1 ) * $on_page;

$quiz_arr = he_quiz::user_quiz_list($user->user_info['user_id'], $first, $on_page);
$quiz_total = he_quiz::user_quiz_total($user->user_info['user_id']);

$smarty->assign('current_page', $current_page);
$smarty->assign('quiz_arr', $quiz_arr);
$smarty->assign('message', $message);
$smarty->assign('paging', array( 'total' => $quiz_total, 'on_page' => $on_page, 'pages' => $pages ));

include "footer.php";
?>