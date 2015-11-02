<?php


$page = "quiz_general";
include "header.php";

if ( !$user->user_exists )
{
    $page = "error";
    $smarty->assign('error_header', 639);
    $smarty->assign('error_message', 656);
    $smarty->assign('error_submit', 641);
    include "footer.php";
}

$task = ( isset($_POST['task']) && $_POST['task'] ) ? trim($_POST['task']) : '';
$task = ( !$task && (isset($_GET['task']) && $_GET['task']) ) ? $_GET['task'] : $task;

$quiz_id = ( isset($_GET['quiz_id']) && $_GET['quiz_id'] ) ? (int)$_GET['quiz_id'] : false;

if ( $quiz_id && $user->user_info['user_id'] != he_quiz::get_owner($quiz_id) )
{
    $page = "error";
    $smarty->assign('error_header', 639);
    $smarty->assign('error_message', 656);
    $smarty->assign('error_submit', 641);
    include "footer.php";
}

$message = array();

$steps = he_quiz::check_steps($quiz_id);

if ( $task == 'save_general' )
{
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $cat_id = (int)$_POST['cat_id'];
    $photo = $_FILES['photo'];

    $result = he_quiz::save_quiz($quiz_id, $user->user_info['user_id'], $name, $description, $cat_id);
    $quiz_id = ( !$quiz_id && $result ) ? $result : $quiz_id;

    if ( !$result )
    {
        $message = array( 'type' => 'error', 'title' => SE_Language::get(690691090), 'text' => SE_Language::get(690691094) );
    }
    else
    {
        $file_upload = he_quiz::save_photo($quiz_id, $photo);

        if ( !$file_upload['result'] && $photo['name'] )
        {
            $message = array( 'type' => 'notice', 'text' => $file_upload['error'] );
            $redirect_url = "quiz_general.php?quiz_id=$quiz_id";
        }
        else
        {
            $redirect_url = "quiz_results.php?quiz_id=$quiz_id";
        }

        header("Location: $redirect_url");
    }
}

$general_info = ( $quiz_id ) ? he_quiz::general_info($quiz_id) : $_POST;
$quiz_cats = he_quiz::find_cats();

$smarty->assign('quiz_id', $quiz_id);
$smarty->assign('general_info', $general_info);
$smarty->assign('steps', $steps);
$smarty->assign('message', $message);
$smarty->assign('quiz_cats', $quiz_cats);

include "footer.php";

?>