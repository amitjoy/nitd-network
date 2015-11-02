<?php

$page = "documents";
include "header.php";

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if($user->user_exists == 0 & $params[permission_document] == 0) {
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}


// DISPLAY ERROR PAGE IF NO OWNER
if($owner->user_exists == 0)
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 650003033);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

// TO BE VERIFY THAT DOCUMNETS HAD ENABLED FOR THIS USER
if($owner->level_info[level_document_allow] == 0) {
	header("Location: ".$url->url_create('profile', $owner->user_info[user_username])); 
	exit(); 
}


// PARSE GET/POST
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
if(isset($_POST['s'])) { $s = $_POST['s']; } elseif(isset($_GET['s'])) { $s = $_GET['s']; } else { $s = "created DESC"; }

// ENSURE SORT/VIEW ARE VALID
if($s != "document_datecreated DESC"  && $s != "document_views DESC" && $s != "document_dateupdated DESC" && $s != 'total_comments DESC') { $s = "document_datecreated DESC"; }


// CREATE SCRIBD OBJECT
$document= new Document(null, null);

// SET PRIVACY LEVEL AND WHERE CLAUSE
$privacy_max = $owner->user_privacy_max($user);
$where = "(document_privacy & $privacy_max) AND (document_approved = '1') AND (document_publish = '1') AND (document_status = 1) AND (document_user_id = '{$owner->user_info['user_id']}') ";

$entries_per_page = 10;

// GET TOTAL DOCUMNETS
$total_entries = $document->documents_total($where);

$page_vars = make_page($total_entries, $entries_per_page, $p);


// GET DOCMENTS ARRAY 
$documents = $document->documents_list($page_vars[0], $entries_per_page, $s, $where, 1);


// ASSIGN SMARTY VARIABLES AND SHOW VIEW DOCUMENTS PAGE
$smarty->assign('documents', $documents);
$smarty->assign('total_entries', $total_entries);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($documents));

include "footer.php";
?>