<?php

$page = "document";
include "header.php";

if(isset($_POST['document_id'])) { $doc_id = $_POST['document_id']; } elseif(isset($_GET['document_id'])) { $doc_id = $_GET['document_id']; } else { $doc_id = 0; }


$query = "SELECT * FROM se_document_parameters";
$params = $database->database_fetch_assoc($database->database_query($query));


// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if($user->user_exists == 0 & $params['permission_document'] == 0) {
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

// DISPLAY ERROR PAGE IF NO DOCUMENT OWNER
if($owner->user_exists == 0) {
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 650003033);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

// TO BE VERIFY THAT DOCUMNETS HAD ENABLED FOR THIS USER
if($owner->level_info['level_document_allow'] == 0) {
	header("Location: ".$url->url_create('profile', $owner->user_info['user_username'])); 
	exit(); 
}

if($doc_id == 0) {
	header('location:documents.php?user='.$owner->user_info['user_username']);
}

// FIND PRIVACY LEVEL
$privacy_max = $owner->user_privacy_max($user);


$document = new Document(null, null, $owner->user_info['user_id'], $doc_id);

// CHECK FOR PRIVACY
if(!($document->document_info['document_privacy'] & $privacy_max)) {
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 650003034);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

// DO NOT SHOW DOCUMENT IF IT IS NOT PUBLISHED to  OTHER USER
if($user->user_info['user_id'] != $owner->user_info['user_id'] && $document->document_info['document_publish'] != 1) {
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 650003034);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}



// DO NOT SHOW DOCUMENT IF IT IS NOT ACTIVE
if($user->user_info['user_id'] != $owner->user_info['user_id'] && $document->document_info['document_approved'] != 1) {
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 650003034);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}


//GETTING THE MAIN CATEGORY NAME(IF THE ASSOCIATED CATEGORY IS A SUB CATEGORY)
$cat_id = $document->document_info['category_id'];

$main_cat = $database->database_fetch_assoc($database->database_query("SELECT t2.category_id, t2.category_name FROM se_document_categories as t1 INNER JOIN se_document_categories t2 ON t1.cat_dependency = t2.category_id WHERE t1.category_id = '$cat_id'"));

if(!empty($main_cat)) {
	$smarty->assign('main_cat', $main_cat);
}



//CHECKING THE STATUS OF THE DOCUMENT ON SCRIBD
$scribd_api_key = $params['api_key'];
$scribd_secret = $params['secret_key'];
$scribd = new Document($scribd_api_key, $scribd_secret, $document->document_info['document_user_id']);
$scribd->my_user_id = $document->document_info['document_user_id'];
if($document->document_exists != "") {
	
try {
	$stat = trim($scribd->getConversionStatus($document->document_info['document_doc_id']));
}
catch(Exception $e) {
$message =  $e->getMessage();
$excep_error = 1;
$smarty->assign('excep_message', $message);
} 



	if($document->document_info['document_download'] && $stat == 'DONE' && $params['download_allow']) {
	
		try {
			$link = $scribd->getDownloadUrl($document->document_info['document_doc_id'], $params['download_format']);
		}
		catch(Exception $e) {
			$message =  $e->getMessage();
			$excep_error = 1;
			$smarty->assign('excep_message', $message);
		} 
		$smarty->assign('link', trim($link['download_link']));
		if($params['include_full_text'] == 1) { 
			$doc_full_text =  nl2br($document->document_info['document_fulltext']);
			$smarty->assign("doc_full_text", $doc_full_text);
		}
	}
}

if($stat == 'DONE') {
	try {
		//GETTING DOCUMENT'S FULL TEXT
		$texturl = $scribd->getDownloadUrl($document->document_info['document_doc_id'], 'txt');
		if($document->document_info['document_status'] != 1) {
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
			
			
			$setting = $scribd->getSettings($document->document_info['document_doc_id']);
			$thumbnail_url = trim($setting['thumbnail_url']);
			
			//UPDATING DOCUMENT STATUS AND FULL TEXT
			$database->database_query("UPDATE se_documents SET document_fulltext='$full_text', document_thumbnail='$thumbnail_url', document_status = '1' WHERE document_id='{$document->document_info['document_id']}'");
			// BECUSE OF LARGE DOCUMNET IF SQL QUEY FALIED THEN WE RUN ANOTHER QUERY FOR THIS  SO THAT STATUS CAN BE 1
			if ($database->database_error()) {
			  $database->database_query("UPDATE se_documents SET document_status = '1' WHERE document_id='{$value->document_info['document_id']}'");
			}
			$document->document_info['document_status'] = 1;
		}
	}
	catch(Exception $e) {
		if($document->document_info['document_status'] != 3 && $e->getCode() == 619) {
			$database->database_query("UPDATE se_documents SET document_status = '3' WHERE document_id='{$document->document_info['document_id']}'");
//			$subject = 'Document Delete';
//			$message = 'Document Delete';
//			send_generic($document->document_owner->user_info['user_email'], 'admin', $subject, $message);
			$document->document_info['document_status'] = 3;
		}
	} 
}
elseif ($stat == 'ERROR') {
	if($document->document_info['document_status'] != 2) {
		$database->database_query("UPDATE se_documents SET document_status = '2' WHERE document_id='{$document->document_info['document_id']}'");
//		$subject = 'Document conversion failed at scribd';
//		$message = 'Document conversion failed at scribd';
//		send_generic($document->document_owner->user_info['user_email'], 'admin', $subject, $message);
		$document->document_info['document_status'] = 2;
	}
}




// UPDATE DOCUMENT VIEWS
$document->document_view();

//  CHECK ENTRY COMMENT PRIVACY
$allowed_to_comment = 1;
if( !($privacy_max & $document->document_info['document_comments']) ) 
 $allowed_to_comment = 0;
 // GET DOCUMENT COMMENTS
$comment = new se_comment('document', 'document_id', $document->document_info['document_id']);
$total_comments = $comment->comment_total();

// UPDATE NOTIFICATIONS
if( $user->user_info['user_id']==$owner->user_info['user_id'])
{
  $database->database_query("
    DELETE FROM
      se_notifys
    USING
      se_notifys
    LEFT JOIN
      se_notifytypes
      ON se_notifys.notify_notifytype_id=se_notifytypes.notifytype_id
    WHERE
      se_notifys.notify_user_id='{$owner->user_info[user_id]}' AND
      se_notifytypes.notifytype_name='documentcomment' AND
      notify_object_id='{$document->document_info['document_id']}'
  ");
}

//SETTING PARAMETERS FOR SECURE DOCUMENT
if($user->user_info['user_id'] == 0) {
	$uid = mt_rand(1000, 100000);
}
else {
	$uid = $user->user_info['user_id'];
}

$sessionId = session_id();
$signature = md5($scribd_secret.'document_id'.$document->document_info['document_doc_id'].'session_id'.$sessionId.'user_identifier'.$uid);
$smarty->assign('uid', $uid);
$smarty->assign('sessionId', $sessionId);
$smarty->assign('signature', $signature);






//  FOR SHOWING THE USER'S DOCUMENT ON THE RIGHT SIDE BLOCK. WE ARE USING BELOW CODE.
//GETTING USER'S DOCUMENTS AND SELECTING TWO RANDOM DOCUMENTS AMONG THEM
// SET PRIVACY LEVEL AND WHERE CLAUSE
$privacy_max = $owner->user_privacy_max($user);
$where_tmp = "(document_privacy & $privacy_max) AND (document_approved = '1') AND (document_publish = '1') AND (document_status = 1) AND (document_user_id = '{$owner->user_info['user_id']}') AND (se_documents.document_id NOT IN('{$document->document_info['document_id']}')) ";
$total_entries = $document->documents_total($where_tmp);
$documents = $document->documents_list(0, 3, "RAND()", $where_tmp, 1);
// GET TOTAL DOCUMNETS


$smarty->assign('total_comments', $total_comments);
$smarty->assign('allowed_to_comment', $allowed_to_comment);
$smarty->assign("document", $document);
$smarty->assign("documents", $documents);
$smarty->assign("total_entries", $total_entries);

$smarty->assign("excep_error", $excep_error);
$smarty->assign("excep_message", $excep_message);
$smarty->assign("params", $params);
include "footer.php";
?>