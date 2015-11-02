<?php

$page = "quiz_results";
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
$min_result_count = $setting['setting_he_quiz_min_result'];

if ( !$steps['results'] )
{
    $page = "error";
    $smarty->assign('error_header', 639);
    $smarty->assign('error_message', 656);
    $smarty->assign('error_submit', 641);
    include "footer.php";
}


if ( $task == 'save_results' )
{
    $result_ids = array();
    $result_arr = array();
    $quiz_results = array();
    
    foreach ( $_POST['result_id'] as $index => $result_id )
    {
        $result_id = (int)$result_id;        
        $title = isset($_POST['title'][$index]) ? trim($_POST['title'][$index]) : false;
        $desciption = isset($_POST['description'][$index]) ? trim($_POST['description'][$index]) : false;
        $photo = isset($_POST['photo'][$index]) ? trim($_POST['photo'][$index]) : false;

        $photo_key = 'photo_' . ( $index + 1 );
        
        $quiz_result = array( 
            'id' => $result_id, 
            'title' => $title, 
            'description' => $desciption,
            'photo' => $photo,
            'filename' => $photo_key );
        
        $result_ids[] = $result_id;
        $result_arr[] = $quiz_result;
        
        if ( strlen($title) == 0 )
        {
            continue;
        }
                
        $quiz_results[] = $quiz_result;  
    }
    
    
    if ( count($quiz_results) >= $min_result_count )
    {
        he_quiz::save_results($quiz_id, $quiz_results);
        
        $redirect_url = "quiz_questions.php?quiz_id=$quiz_id";
        
        header("Location: $redirect_url");
        exit();
    }

    $message = array(
        'type' => 'error',
        'title' => SE_Language::get(690691108, array($min_result_count)),
        'text' => SE_Language::get(690691109) );
}

$result_arr = ( $result_arr ) ? $result_arr : he_quiz::get_results($quiz_id, true);
$photo_url = he_quiz::photo_url();

$smarty->assign('quiz_id', $quiz_id);
$smarty->assign('result_arr', $result_arr);
$smarty->assign('steps', $steps);
$smarty->assign('message', $message);
$smarty->assign('min_result_count', $min_result_count);
$smarty->assign('photo_url', $photo_url);

include "footer.php";
?>