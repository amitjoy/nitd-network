<?php
$page = "user_documents";
include "header.php";



if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
if(isset($_POST['search'])) { $search = $_POST['search']; } elseif(isset($_GET['search'])) { $search = $_GET['search']; } else { $search = ""; }
if(isset($_GET['success']))
$success = $_GET['success'];


// ENSURE DOCUMNETS ARE ENABLED FOR THIS USER
if( !$user->level_info['level_document_allow'] )
{
  header("Location: user_home.php");
  exit();
}

$query = "SELECT * FROM se_document_parameters";
$params = $database->database_fetch_assoc($database->database_query($query));


// SET SORT BY DEFAULT
$sort = "document_datecreated DESC";

$where= " se_documents.document_user_id=".$user->user_info[user_id]."";
if($search != "") { $where.= " AND (document_title LIKE '%$search%' OR document_description LIKE '%$search%'  OR document_fulltext LIKE '%$search%' OR tag_name LIKE '%$search%')"; } 


$scribd_api_key = $params['api_key'];
$scribd_secret = $params['secret_key'];


// ITIALIZE THE DOCUMENT OBJECT
$entries_per_page = 10;
$scribd = new Document($scribd_api_key, $scribd_secret, $user->user_info['user_id']);

if($task == 'delete') {
	$doc_id = $_GET['document_id'];
	$result = $database->database_fetch_assoc($database->database_query("SELECT document_id FROM se_documents WHERE document_id = '$doc_id' AND document_user_id = '".$user->user_info['user_id']."'"));
	if(!empty($result)) {
		
		try {
		  $scribd->document_delete($doc_id);
		}
		catch(Exception $e) {
			$message =  $e->getMessage();
			$excep_error = 1;
			$smarty->assign('excep_message', $message);
		} 
		
	
		$smarty->assign('msg', 'Document has been deleted');
	}
}

if($task == 'publish') {
	$doc_id = $_GET['document_id'];
	$result = $database->database_fetch_assoc($database->database_query("SELECT document_id FROM se_documents WHERE document_id = '$doc_id' AND document_user_id = '".$user->user_info['user_id']."' AND document_publish = 0"));
	if(!empty($result)) {
		$database->database_query("UPDATE se_documents SET document_publish = 1 WHERE document_id = '{$doc_id}'");
		$smarty->assign('msg', 'Document has been published successfully.');
	}
}

//BLOCK FOR UPDATING CONVERSION STATUS OF THE DOCUMENT
$where_tmp = " se_documents.document_user_id=".$user->user_info[user_id]." AND se_documents.document_status = 0";
$doc_forUpdate = $scribd->documents_list(0, 5, 'document_datecreated DESC', $where_tmp, 1);
foreach($doc_forUpdate as $value) {
	$scribd_tmp = new Document($scribd_api_key, $scribd_secret, $value->document_info['document_user_id']);
  $scribd_tmp->my_user_id = $value->document_info['document_user_id']; 
  
  
  try {
			$stat = trim($scribd_tmp->getConversionStatus($value->document_info['document_doc_id']));
	 }
		catch(Exception $e) {
			$message =  $e->getMessage();
			$excep_error = 1;
			$smarty->assign('excep_message', $message);
  } 
  
  if($stat == 'DONE') {
  	
  	try {
	  	//GETTING DOCUMENT'S FULL TEXT
	  	$texturl = $scribd_tmp->getDownloadUrl($value->document_info['document_doc_id'], 'txt');
			//for some reason, the URL comes back with leading and trailing spaces
			$texturl = trim($texturl['download_link']);
			
			$file_contents = file_get_contents($texturl);
			if (empty($file_contents)) {
				$site_url = $texturl;
				$ch = curl_init();
				$timeout = 0; // set to zero for no timeout
				curl_setopt ($ch, CURLOPT_URL, $site_url);
				curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
				
				ob_start();
				curl_exec($ch);
				curl_close($ch);
				$file_contents = ob_get_contents();
				ob_end_clean();
			}
			$full_text = mysql_real_escape_string($file_contents);
			
			$setting = $scribd_tmp->getSettings($value->document_info['document_doc_id']);
			$thumbnail_url = trim($setting['thumbnail_url']);
			
			//UPDATING DOCUMENT STATUS AND FULL TEXT
			$database->database_query("UPDATE se_documents SET document_fulltext='$full_text', document_thumbnail='$thumbnail_url', document_status = '1' WHERE document_id='{$value->document_info['document_id']}'");
			// BECUSE OF LARGE DOCUMNET IF SQL QUEY FALIED THEN WE RUN ANOTHER QUERY FOR THIS  SO THAT STATUS CAN BE 1
			if ($database->database_error()) {
			  $database->database_query("UPDATE se_documents SET document_status = '1' WHERE document_id='{$value->document_info['document_id']}'");
			}
		}
	  catch(Exception $e) {
	  	if($e->getCode() == 619) {
		  	$database->database_query("UPDATE se_documents SET document_status = '3' WHERE document_id='{$value->document_info['document_id']}'");
//		  	$subject = 'Document Delete';
//				$message = 'Document Delete';
//				send_generic($value->document_owner->user_info['user_email'], 'admin', $subject, $message);
	  	}
	  }
  	
  }
  elseif ($stat == 'ERROR') {
  	$database->database_query("UPDATE se_documents SET document_status = '2' WHERE document_id='{$value->document_info['document_id']}'");
  	//SENDING AN EMAIL TO THE OWNER
//  	$subject = 'Document conversion failed at scribd';
//		$message = 'Document conversion failed at scribd';
//		send_generic($value->document_owner->user_info['user_email'], 'admin', $subject, $message);
  }
}


//  FIND THE DOCUMENTS OF USER
$total_entries = $scribd->documents_total($where);
$page_vars = make_page($total_entries, $entries_per_page, $p);
$documents = $scribd->documents_list($page_vars[0], $entries_per_page, $sort, $where, 1);



$smarty->assign('documents', $documents);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($documents));
$smarty->assign('thumb_size', $params['thumbnail_size']);
$smarty->assign('success', $success);
$smarty->assign('search', $search);
$smarty->assign('total_entries', $total_entries);

$smarty->assign("excep_error", $excep_error);
$smarty->assign("excep_message", $excep_message);

include "footer.php";
?>