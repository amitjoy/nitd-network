<?php

$page = "browse_quiz";
include "header.php";


$current_page = ( isset($_GET['page']) && $_GET['page'] ) ? (int)$_GET['page'] : 1;
$mode = ( isset($_GET['mode']) && $_GET['mode'] ) ? trim($_GET['mode']) : 'popular';
$cat_id =  ( isset($_GET['cat_id']) && $_GET['cat_id'] ) ? (int)$_GET['cat_id'] : 0;

//TODO get from configs
$on_page = 10;
$pages = 5;

$first = ( $current_page - 1 ) * $on_page;
$where_cond = ( $cat_id != 0 ) ? he_database::placeholder( "AND `quiz`.`cat_id`=?", $cat_id ) : '';

if ( $mode != 'commented' )
{
    $quiz_arr = he_quiz::quiz_list($first, $on_page, $mode, $where_cond);
    $quiz_total = he_quiz::quiz_total($where_cond);
}
else
{
    $quiz_arr = he_quiz::recent_commented_list($first, $on_page, $where_cond);
    $quiz_total = he_quiz::recent_commented_total($where_cond);
}

$quiz_cats = he_quiz::find_cats($where_cond);
$taked_quiz_list = he_quiz::recent_taked_quizzes();

$smarty->assign('mode', $mode);
$smarty->assign('current_page', $current_page);
$smarty->assign('quiz_arr', $quiz_arr);
$smarty->assign('message', $message);
$smarty->assign('paging', array( 'total' => $quiz_total, 'on_page' => $on_page, 'pages' => $pages ));
$smarty->assign('quiz_cats', $quiz_cats);
$smarty->assign('taked_quiz_list', $taked_quiz_list);
$smarty->assign('cat_id', $cat_id);

include "footer.php";
?>