<?php

$page = "admin_viewdocuments";
include "admin_header.php";

if(isset($_POST['s'])) { $s = $_POST['s']; } elseif(isset($_GET['s'])) { $s = $_GET['s']; } else { $s = "id"; }
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
if(isset($_POST['f_title'])) { $f_title = $_POST['f_title']; } elseif(isset($_GET['f_title'])) { $f_title = $_GET['f_title']; } else { $f_title = ""; }
if(isset($_POST['f_owner'])) { $f_owner = $_POST['f_owner']; } elseif(isset($_GET['f_owner'])) { $f_owner = $_GET['f_owner']; } else { $f_owner = ""; }
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }

if(isset($_POST['document_id'])) { $doc_id = $_POST['document_id']; } elseif(isset($_GET['document_id'])) { $doc_id = $_GET['document_id']; } else { $doc_id = 0; }
if(isset($_POST['delete_docs'])) { $delete_docs = $_POST['delete_docs']; } elseif(isset($_GET['delete_docs'])) { $delete_docs = $_GET['delete_docs']; } else { $delete_docs = NULL; }



$entries_per_page =100;
$scribd = new Document(null, null);

// DELETE SINGLE ENTRY
if( $task=="deleteentry" )
{

	try {
		$scribd->document_delete($doc_id);
	}
	catch(Exception $e) {
		$message =  $e->getMessage();
		$excep_error = 1;
		$smarty->assign('excep_message', $message);
	} 
}

if ($task == 'approve') {

  if ($doc_id != 0) {
  	$value = $_GET['value'];
  	$database->database_query("UPDATE se_documents SET document_approved = '$value' WHERE document_id = '$doc_id'");
  }

}

if ($task == 'featured') {

  if ($doc_id != 0) {
  	$value = $_GET['value'];
  	$database->database_query("UPDATE se_documents SET document_featured = '$value' WHERE document_id = '$doc_id'");
  }

}


if( $task=="delete" && is_array($delete_docs) && !empty($delete_docs) )
{ 
		try {
		  $scribd->document_delete($delete_docs); 
		}
		catch(Exception $e) {
			$message =  $e->getMessage();
			$excep_error = 1;
			$smarty->assign('excep_message', $message);
		} 
 
}


$i = "id";   // document_id
$t = "t";    // DOCUMENT_TITLE
$o = "o";    // OWNER OF DOCUMENT
$d = "d";    // DATE OF DOCUMENT CREATION
$a = "a";    // APPROVED
$f = "f";    // FEATURED

// SET SORT VARIABLE FOR DATABASE QUERY
if($s == "i") {
  $sort = "se_documents.document_id";
  $i = "id";
} elseif($s == "id") {
  $sort = "se_documents.document_id DESC";
  $i = "i";
} elseif($s == "t") {
  $sort = "se_documents.document_title";
  $t = "td";
} elseif($s == "td") {
  $sort = "se_documents.document_title DESC";
  $t = "t";
} elseif($s == "o") {
  $sort = "se_users.user_username";
  $o = "od";
} elseif($s == "od") {
  $sort = "se_users.user_username DESC";
  $o = "o";
} elseif($s == "d") {
  $sort = "se_documents.document_datecreated";
  $d = "dd";
} elseif($s == "dd") {
  $sort = "se_documents.document_datecreated DESC";
  $d = "d";
} elseif($s == "a") {
  $sort = "se_documents.document_approved";
  $a = "ad";
} elseif($s == "ad") {
  $sort = "se_documents.document_approved DESC";
  $a = "a";
}	elseif($s == "f") {
  $sort = "se_documents.document_featured";
  $f = "fd";
} elseif($s == "fd") {
  $sort = "se_documents.document_featured DESC";
  $f = "f";
} else {
  $sort = "se_documents.document_datecreated DESC";
  $i = "i";
}

// ADD CRITERIA FOR FILTER
$where = "";
if($f_owner != "") { $where .= "se_users.user_username LIKE '%$f_owner%'"; }
if($f_owner != "" & $f_title != "") { $where .= " AND"; }
if($f_title != "") { $where .= " se_documents.document_title LIKE '%$f_title%'"; }
if($where != "") { $where = "(".$where.")"; }

// DELETE NECESSARY ENTRIES
$start = ($p - 1) * $entries_per_page;


// GET TOTAL DOCUMNETS
$total_entries = $scribd->documents_total($where);

// MAKE DOCUMENT PAGES
$page_vars = make_page($total_entries, $entries_per_page, $p);
$page_array = array();
for($x=0;$x<=$page_vars[2]-1;$x++) {
  if($x+1 == $page_vars[1]) { $link = "1"; } else { $link = "0"; }
  $page_array[$x] = array('page' => $x+1,
			  'link' => $link);
}

// GET DOCMENTS ARRAY 
$documents = $scribd->documents_list($page_vars[0], $entries_per_page, $sort, $where, 1);


// ASSIGN SMARTY VARIABLES AND SHOW VIEW DOCUMENTS PAGE
$smarty->assign('total_documents', $total_entries);
$smarty->assign('pages', $page_array);
$smarty->assign('documents', $documents);
$smarty->assign('f_title', $f_title);
$smarty->assign('f_owner', $f_owner);
$smarty->assign('i', $i);
$smarty->assign('t', $t);
$smarty->assign('o', $o);
$smarty->assign('v', $v);
$smarty->assign('d', $d);
$smarty->assign('a', $a);
$smarty->assign('f', $f);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('s', $s);
$smarty->assign("excep_error", $excep_error);
$smarty->assign("excep_message", $excep_message);
include "admin_footer.php";
?>