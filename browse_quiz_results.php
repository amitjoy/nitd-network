<?php


$page = "browse_quiz_results";
include "header.php";

$quiz_id = ( isset($_GET['quiz_id']) && $_GET['quiz_id'] ) ? (int)$_GET['quiz_id'] : 0;
$quiz_info = he_quiz::get_quiz_info($quiz_id);


// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if ( !$user->user_exists || !$quiz_info )
{
	$page = "error";
	$smarty->assign('error_header', 639);
	$smarty->assign('error_message', 656);
	$smarty->assign('error_submit', 641);
	include "footer.php";
}

$quiz_results = he_quiz::get_results($quiz_id);
$quiz_takes = he_quiz::get_quiz_takes($quiz_id);

$comment = new se_comment('he_quiz', 'quiz_id', $quiz_id);
$total_comments = $comment->comment_total();

$allowed_to_comment = true;//TODO
$photo_url = he_quiz::photo_url();

$smarty->assign('message', $message);
$smarty->assign('quiz_info', $quiz_info);
$smarty->assign('quiz_results', $quiz_results);
$smarty->assign('quiz_takes', $quiz_takes);

$smarty->assign('total_comments', $total_comments);
$smarty->assign('allowed_to_comment', $allowed_to_comment);

$smarty->assign('photo_url', $photo_url);

include "footer.php";
?>