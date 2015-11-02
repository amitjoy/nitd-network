<?php

$page = "admin_qinformer";
include "admin_header.php";

$field = new se_field("profile");
$field->cat_list();
$cat_array = $field->cats;


$task = "";
$iSuccess=0;
if(isset($_REQUEST['task']) && $_REQUEST['task'])
	$task = $_REQUEST['task'];

$qinformer_settings = get_qinformer_settings();

$qinformer_enabled = (isset($qinformer_settings['enabled']) && $qinformer_settings['enabled'] ? true : false);
$qinformer_fields = explode("|", (isset($qinformer_settings['fields']) ? $qinformer_settings['fields'] : ''));

if ($task == 'qinformer_settings')
{
		$qinformer_enabled = isset($_POST['qinformer_enabled']) && $_POST['qinformer_enabled'] ? $_POST['qinformer_enabled'] : 0;
		$qinformer_fields = isset($_POST['fields']) && $_POST['fields'] ? $_POST['fields'] : '';
		$string_f='';
		$count_fields = count($qinformer_fields); 
		for ($i=0; $i < $count_fields; $i++) {
			if ($i < $count_fields-1) $separator="|";
			else $separator='';	
 		    $string_f .= $qinformer_fields[$i].$separator;
		}

			$sql = "
				REPLACE INTO `se_qinformer_settings`
				SET 
					`id` = 1,
					`enabled` = '".$qinformer_enabled."',
					`fields` = '".$string_f."'";
			$database->database_query($sql);
	
			$iSuccess=9000756;
}

$smarty->assign( 'qinformer_enabled', $qinformer_enabled);
$smarty->assign( 'is_success', $iSuccess );
$smarty->assign( 'username', $qinformer_username );
$smarty->assign( 'fields', $qinformer_fields );
$smarty->assign('cats', $cat_array);
include "admin_footer.php";
?>