<?php

$page = "user_document_email";
include "header.php";

if(isset($_POST['document_id'])) { $doc_id = $_POST['document_id']; } elseif(isset($_GET['document_id'])) { $doc_id = $_GET['document_id']; } else { $doc_id = 0; }



$query = "SELECT * FROM se_document_parameters";
$params = $database->database_fetch_assoc($database->database_query($query));


// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if($user->user_exists == 0 & $params[permission_document] == 0) {
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
if($owner->level_info[level_document_allow] == 0) {
	header("Location: ".$url->url_create('profile', $owner->user_info[user_username])); 
	exit(); 
}

if($doc_id == 0) {
	header('location:documents.php?user='.$owner->user_info['user_username']);
}

// FIND PRIVACY LEVEL
$privacy_max = $owner->user_privacy_max($user);


$document = new Document(null, null, $owner->user_info['user_id'], $doc_id);

// CHECK FOR PRIVACY
if(!($document->document_info[document_privacy] & $privacy_max)) {
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
if(($document->document_info['document_attachment'] != 1) || ($document->document_info['document_status'] != 1) ||  ($params['email_allow'] != 1 )) {
	$page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 650003034);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}
$time_out = 50000;

if(isset($_POST['submit']) && $_POST['submit'] == 'Send') {
	$to = $_POST['to'];
	
	$subject = $_POST['subject'];
	
	$user_message = $_POST['message'];
	
	//IF THE RECEIVER FIELD IS EMPTY(TO)
	
	if(empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
    $is_error = 1;
    $error = 650003208;
    $error_array[] = $error;
  }
	
  if(empty($subject)) {
    $is_error = 1;
    $error = 650003209;
    $error_array[] = $error;
  }
  if($is_error != 1) {
  	
  	$from = $user->user_info['user_displayname']. '<' . $user->user_info['user_email'] . '>';
		
		$fileatt_type = $document->document_info['document_filemime'];
		
		$fileatt_name = $document->document_info['document_filename'];
		$headers = "From: $from";
  	
    $scribd_api_key = $params['api_key'];
		$scribd_secret = $params['secret_key'];
    $scribd = new Document($scribd_api_key, $scribd_secret, $document->document_info['document_user_id']);
    $scribd->my_user_id = $document->document_info['document_user_id']; 
    
    	
		try {
			$link = $scribd->getDownloadUrl($document->document_info['document_doc_id'], 'original');
		}
		catch(Exception $e) {
			$message =  $e->getMessage();
			$excep_error = 1;
			$smarty->assign('excep_message', $message);
		} 
    
    
    $link = trim($link['download_link']);
		$data = file_get_contents($link);
		
		
		$semi_rand = md5(time());
		$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
		
		$headers .= "\nMIME-Version: 1.0\n" .
		"Content-Type: multipart/mixed;\n" .
		" boundary=\"{$mime_boundary}\"";
		
		$email_message = "This is a multi-part message in MIME format.\n\n" .
		"--{$mime_boundary}\n" .
		"Content-Type:text/html; charset=\"iso-8859-1\"\n" .
		"Content-Transfer-Encoding: 7bit\n\n" . $user_message .
		$email_message . "\n\n";
		
		$data = chunk_split(base64_encode($data));
		
		$email_message .= "--{$mime_boundary}\n" .
		"Content-Type: {$fileatt_type};\n" .
		" name=\"{$fileatt_name}\"\n" .
		//"Content-Disposition: attachment;\n" .
		//" filename=\"{$fileatt_name}\"\n" .
		"Content-Transfer-Encoding: base64\n\n" .
		$data . "\n\n" .
		"--{$mime_boundary}--\n"; 
  	
  	
  	
  	

  	$mail_sent = mail( $to, $subject, $email_message, $headers );
		if($mail_sent) {
			$smarty->assign('msg', 650003210);
			$time_out = 7000;
			$smarty->assign("no_form", 1);
		}
		else {
			$is_error = 1;
			$error_array[] = 650003211;
			$time_out = 50000;
			$smarty->assign("no_form", 1);
		}
	}
	$smarty->assign('to', $to);
	$smarty->assign('subject', $subject);
	$smarty->assign('message', $user_message);
}

$smarty->assign('document', $document);
$smarty->assign('is_error', $is_error);
$smarty->assign('time_out', $time_out);
$smarty->assign('error_array', array_unique($error_array));

$smarty->assign("excep_error", $excep_error);
$smarty->assign("excep_message", $excep_message);

include "footer.php";
?>