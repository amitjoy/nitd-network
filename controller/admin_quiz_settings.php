<?php


$page = "admin_quiz_settings";
include "admin_header.php";

$task = ( isset($_POST['task']) || $_POST['task'] ) ? $_POST['task'] : '';


if ( $task == 'save_settings' )
{
	$setting['setting_he_quiz_min_result'] = $_POST['setting_he_quiz_min_result'];
	$setting['setting_he_quiz_min_question'] = $_POST['setting_he_quiz_min_question'];
	$setting['setting_he_quiz_approval_status'] = (int)isset($_POST['setting_he_quiz_approval_status']);	 
	$database->database_query("UPDATE se_settings SET 
		setting_he_quiz_min_result='{$setting['setting_he_quiz_min_result']}', 
		setting_he_quiz_min_question='{$setting['setting_he_quiz_min_question']}',
		setting_he_quiz_approval_status='{$setting['setting_he_quiz_approval_status']}'");
}

if ( $task == 'save_changes' )
{
    $cat_ids = array();
    
    foreach ( $_POST['cat_label'] as $index => $label )
    {
        $cat_id = $_POST['cat_id'][$index];
        
        if ( !$cat_id )
        {
            $cat_id = (int)he_quiz::add_cat($label);
        }
        else
        {
            he_quiz::update_cat($cat_id, $label);
        }
        
        $cat_ids[] = $cat_id;
    }
    
    he_quiz::delete_cats($cat_ids);
}

$cats = he_quiz::find_cats();

$smarty->assign('setting_he_quiz_min_result', $setting['setting_he_quiz_min_result']);
$smarty->assign('setting_he_quiz_min_question', $setting['setting_he_quiz_min_question']);
$smarty->assign('setting_he_quiz_approval_status', $setting['setting_he_quiz_approval_status']);
$smarty->assign('cats', $cats);

include "admin_footer.php";

?>