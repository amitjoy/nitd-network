<?php


defined('SE_PAGE') or exit();

include "./include/class_he_database.php";
include "./include/class_he_quiz.php";
include "./include/functions_he_common.php";
include "./include/class_he_phpmailer.php";
include "./include/class_he_mass_mailing.php";
include "./include/functions_he_quiz.php";

//send messages from queue
$mass_mailing = new he_mass_mailing();
$mass_mailing->cron();

// PRELOAD LANGUAGE
SE_Language::_preload(690691138);
SE_Language::_preload(690691139);

// SET MENU VARS
$plugin_vars['menu_main'] = array(
	'file' => 'browse_quiz.php', 
	'title' => 690691138
);

if ( $user->user_exists )
{
	//USER APPS MENU
	$plugin_vars['menu_user'] = array(
		'file' => 'user_quiz.php', 
		'icon' => 'he_quiz_icon.gif', 
		'title' => 690691139
	);
}

// Use new template hooks
if ( is_a($smarty, 'SESmarty') )
{
	$plugin_vars['uses_tpl_hooks'] = TRUE;
	
	if ( !empty($plugin_vars['menu_main']) )
	{
		$smarty->assign_hook('menu_main', $plugin_vars['menu_main']);
	}
	
	if ( !empty($plugin_vars['menu_user']) )
	{
		$smarty->assign_hook('menu_user_apps', $plugin_vars['menu_user']);
	}
	
	$smarty->assign_hook('styles', './templates/he_styles.css');
	$smarty->assign_hook('styles', './templates/he_quiz_styles.css');
	$smarty->assign_hook('scripts', './include/js/he_contacts.js');
}

$smarty->register_function('he_quiz_paging', 'he_quiz_paging');
$smarty->register_function('he_quiz_list', 'he_quiz_list');

// SET HOOKS
SE_Hook::register("se_user_delete", 'he_quiz_delete_user');

?>