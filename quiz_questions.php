<?php


$page = "quiz_questions";
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
$min_question_count = $setting['setting_he_quiz_min_question'];

if ( !$steps['questions'] )
{
    $page = "error";
    $smarty->assign('error_header', 639);
    $smarty->assign('error_message', 656);
    $smarty->assign('error_submit', 641);
    include "footer.php";
}


if ( $task == 'save_questions' )
{    
    $quiz_error = false;
    $quiz_notice = false;    
    $questions = array();
    $question_arr = array();

    foreach ( $_POST['question_key'] as $q_index => $q_key ) 
    {    
        $q_key = (int)$q_key;
        $question_id = (int)$_POST['question_id'][$q_index];
        $question_text = trim($_POST['question_text'][$q_index]);
        $photo = trim($_POST['photo'][$q_index]);
        $photo_key = 'photo_' . ( $q_index + 1 );
        
        $answers = array();
        foreach ( $_POST['answer_id'][$q_key] as $a_index => $answer_id )
        {
            $answer_id = (int)$answer_id;
            $answer_result_id = (int)$_POST['answer_result_id'][$q_key][$a_index];
            $answer_label = trim($_POST['answer_label'][$q_key][$a_index]);
                        
            $answers[$answer_result_id] = array(
                'id' => $answer_id,
                'question_id' => $question_id,
                'result_id' => $answer_result_id,
                'label' => $answer_label );
            
            $quiz_notice = ( !strlen($answer_label) || !$answer_result_id ) ? false : $quiz_notice;
        }
        
        $question = array(
            'id' => $question_id,
            'text' => $question_text,
            'answers' => $answers,
            'photo' => $photo,
            'filename' => $photo_key );
        
        $question_arr[] = $question;
        
        if ( !strlen($question_text) )
        {
            $quiz_error = true;
        }
        else 
        {
            $questions[] = $question;
        }
    }
    
    if ( $min_question_count > count($questions) )
    {
        $message = array( 
            'type' => 'error', 
            'title' => SE_Language::get(690691116, array( $min_question_count )), 
            'text' => SE_Language::get(690691117) );
    }
    elseif ( $quiz_error )
    {
        $message = array( 
            'type' => 'error', 
            'title' => SE_Language::get(690691118), 
            'text' => SE_Language::get(690691119) );        
    }
    elseif ( $quiz_notice )
    {
        $message = array( 
            'type' => 'error', 
            'title' => SE_Language::get(690691120),
            'text' => SE_Language::get(690691121) );    
    }
    else
    {
        he_quiz::save_questions($quiz_id, $questions);
        
        header("Location: quiz_publish.php?quiz_id=$quiz_id");
        exit();
    }
}

if ( !isset($question_arr) )
{
    $question_arr = he_quiz::get_questions($quiz_id, true);
    $question_arr = he_quiz::get_answers($quiz_id, $question_arr);
}

$results =  he_quiz::get_results($quiz_id, false, true);
$photo_url = he_quiz::photo_url();

$smarty->assign('quiz_id', $quiz_id);
$smarty->assign('min_question_count', $min_question_count);
$smarty->assign('question_arr', $question_arr);
$smarty->assign('results', $results);
$smarty->assign('steps', $steps);
$smarty->assign('message', $message);
$smarty->assign('photo_url', $photo_url);

include "footer.php";
?>