<?php


// ENSURE THIS IS BEING INCLUDED IN AN SE SCRIPT
defined('SE_PAGE') or exit();


// INCLUDE FUNCTION FILE
include_once "./include/functions_document.php";

// INCLUDE CLASS FILE
include_once "./include/class_document.php";

// PRELOAD LANGUAGE
SE_Language::_preload(650003010);


$query = "SELECT * FROM se_document_parameters";
$params = $database->database_fetch_assoc($database->database_query($query));



// SET MAIN MENU VARS
if( (!$user->user_exists && $params[permission_document]) || ($user->user_exists && $user->level_info['level_document_allow']) )
{
  $plugin_vars['menu_main'] = Array('file' => 'browse_documents.php', 'title' => 650003010);
}

// SET USER MENU VARS
if( $user->user_exists && $user->level_info['level_document_allow'] )
{
  $plugin_vars[menu_user] = Array('file' => 'user_documents.php', 'icon' => 'document16.gif', 'title' => 650003010);
}



// SET PROFILE MENU VARS
if($owner->level_info['level_document_allow'] && $page == "profile")
{
  //SHOWING A DOCUMENT TAB IF THE USER HAS ATLEAST ONE DOCUMENT
  if($page == "profile")
  {
	  if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
	  
	  $documents = new Document(null, null, $owner->user_info['user_id']);
  
  	  // SET PRIVACY LEVEL AND WHERE CLAUSE
		$privacy_max = $owner->user_privacy_max($user);
		$where = "(document_privacy & $privacy_max) AND (document_approved = '1') AND (document_publish = '1') AND (document_status = 1) AND (document_user_id = '{$owner->user_info['user_id']}') ";
	  $sort = "document_datecreated DESC";
  	
  	$total_docs = $documents->documents_total($where);
	  if($total_docs > 0)
	  {	
	  	//SHOWING A PROFILE TAB
	  	if($params['document_block'] == 1)	{
		  	$entries_per_page = 5;
	  	  $page_vars = make_page($total_docs, $entries_per_page, $p);
				$tab_documents = $documents->documents_list($page_vars[0], $entries_per_page, $sort, $where, 1);
				$smarty->assign('documents', $tab_documents);
				$smarty->assign('total_docs', $total_docs);
				$smarty->assign('p', $page_vars[1]);
				$smarty->assign('maxpage', $page_vars[2]);
				$smarty->assign('p_start', $page_vars[0]+1);
				$smarty->assign('p_end', $page_vars[0]+count($tab_documents));
				
				$plugin_vars['menu_profile_tab'] = Array('file'=> 'profile_document_tab.tpl', 'title' => 650003010, 'name' => 'document');
		  }
		  else {
		  	//SHOWING A SIDE BLOCK ON PROFILE PAGE
		  	//GETTING TWO MOST RECENT DOCUMENTS OF THE USER
		  	$side_documents = $documents->documents_list(0, 2, $sort, $where, 1);
		  	$plugin_vars['menu_profile_side'] = Array('file'=> 'profile_document_side.tpl', 'title' => 650003010, 'name' => 'document');
		  	$smarty->assign('total_docs', $total_docs);
		  	$smarty->assign('documents', $side_documents);
		  }
	  }
  }
}

// Use new template hooks
if( is_a($smarty, 'SESmarty') )
{
  
  if( !empty($plugin_vars['menu_main']) )
    $smarty->assign_hook('menu_main', $plugin_vars['menu_main']);
  
  if( !empty($plugin_vars['menu_user']) )
    $smarty->assign_hook('menu_user_apps', $plugin_vars['menu_user']);
  
  if( !empty($plugin_vars['menu_profile_side']) )
    $smarty->assign_hook('profile_side', $plugin_vars['menu_profile_side']);
  
  if( !empty($plugin_vars['menu_profile_tab']) )
    $smarty->assign_hook('profile_tab', $plugin_vars['menu_profile_tab']);
  
  if( !empty($plugin_vars['menu_userhome']) )
    $smarty->assign_hook('user_home', $plugin_vars['menu_userhome']);
}


SE_Hook::register("se_search_do", "search_documents");

SE_Hook::register("se_user_delete", "deleteuser_document");

SE_Hook::register("se_site_statistics", "site_statistics_document");

?>