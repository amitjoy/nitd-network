<?php

defined('SE_PAGE') or exit();

SE_DEBUG ? $_benchmark->end('page') : NULL;
SE_DEBUG ? $_benchmark->start('shutdown') : NULL;

// GET LANGUAGES AVAILABLE IF NECESSARY
if($setting['setting_lang_anonymous'] == 1 || ($setting['setting_lang_allow'] == 1 && $user->user_exists != 0))
{
  $lang_packlist_raw = SECore::getLanguages();
  //$lang_packlist = SELanguage::list_packs();
  ksort($lang_packlist_raw);
  $lang_packlist = array_values($lang_packlist_raw);
}


// ASSIGN LOGGED-IN USER VARS
if( $user->user_exists )
{ 
  $smarty->assign('user_unread_pms', $user->user_message_total(0, 1));
}

// Token
if( !defined('SE_PAGE_AJAX') )
{
  $token = md5(uniqid(mt_rand(), true));
  $session->set('token', $token);
  $smarty->assign('token', $token);
}

// CALL SPECIFIC PAGE HOOK
($hook = SE_Hook::exists('se_'.$page)) ? SE_Hook::call($hook, array()) : NULL;

// CALL FOOTER HOOK
($hook = SE_Hook::exists('se_footer')) ? SE_Hook::call($hook, array()) : NULL;

// CHECK IF IN SMOOTHBOX
$global_smoothbox = false;
if(isset($_GET['in_smoothbox'])) { if($_GET['in_smoothbox'] == true) { $global_smoothbox = true; }}

// ASSIGN GLOBAL SMARTY OBJECTS/VARIABLES
$smarty->assign_by_ref('url', $url);
$smarty->assign_by_ref('misc', $misc);
$smarty->assign_by_ref('datetime', $datetime);
$smarty->assign_by_ref('database', $database);
$smarty->assign_by_ref('admin', $admin);
$smarty->assign_by_ref('user', $user);
$smarty->assign_by_ref('owner', $owner);
$smarty->assign_by_ref('ads', $ads);
$smarty->assign_by_ref('setting', $setting);
$smarty->assign_by_ref('se_javascript', $se_javascript);
$smarty->assign('lang_packlist', $lang_packlist);
$smarty->assign('notifys', $notify->notify_summary());
$smarty->assign('global_plugins', $global_plugins);
$smarty->assign('global_smoothbox', $global_smoothbox);
$smarty->assign('global_page', $page);
$smarty->assign('global_page_title', ( !empty($global_page_title) ? $global_page_title : NULL ));
$smarty->assign('global_page_description', ( !empty($global_page_description) ? str_replace("\"", "'", $global_page_description) : NULL ));
$smarty->assign('global_css', $global_css);
$smarty->assign('global_timezone', $global_timezone);
$smarty->assign('global_language', SELanguage::info('language_id'));

if( SE_DEBUG )
{
  $_benchmark->end('shutdown');
  
  $smarty->assign('debug_uid', $_benchmark->getUid());
  $smarty->assign_by_ref('debug_benchmark_object', $_benchmark);
  
  $_benchmark->start('output');
}


// DISPLAY PAGE
$smarty->display("$page.tpl");


if( SE_DEBUG )
{
  $_benchmark->end('output');
  $_benchmark->end('total');
  
  $smarty->assign('debug_benchmark', $_benchmark->getLog());
  $smarty->assign('debug_benchmark_total', $_benchmark->getTotalTime());
  
  // Save logging info
  file_put_contents('./log/'.$_benchmark->getUid().'.html', $smarty->fetch('debug.tpl'));
  //file_put_contents(SE_ROOT.DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR.$_benchmark->getUid(), $smarty->fetch('debug.tpl'));
}

exit();
?>