<?php

if(!defined('SE_PAGE')) { exit(); }
include "include/functions_qinformer.php";

$qinformer_settings = get_qinformer_settings();
if(isset($qinformer_settings['enabled']) && (int)$qinformer_settings['enabled'] 
	&& (strpos($_SERVER['PHP_SELF'], 'profile.php') === false))
$smarty->assign('qinformer_is_enabled', (int)$qinformer_settings['enabled']);
else  $smarty->assign('qinformer_is_enabled', 0);
?>