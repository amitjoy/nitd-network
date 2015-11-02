<?php


//  THIS FILE CONTAINS EMAIL-RELATED FUNCTIONS
//  FUNCTIONS IN THIS CLASS:
//    send_generic()
//    send_systememail()














// THIS FUNCTION SENDS A CUSTOM EMAIL
// INPUT: $recipient REPRESENTING THE RECIPIENT'S EMAIL
//	  $sender REPRESENTING THE SENDER'S NAME/EMAIL
//	  $subject REPRESENTING THE EMAIL SUBJECT
//	  $message REPRESENTING THE EMAIL MESSAGE
//	  $search REPRESENTING THE ARRAY OF STRINGS TO SEARCH FOR
//	  $replace REPRESENTING THE ARRAY OF STRINGS TO REPLACE $search WITH
//	  $bcc (OPTIONAL) REPRESENTING WHETHER TO BCC ALL RECIPIENTS
// OUTPUT: 
require_once ("phpmailer/class.phpmailer.php");


function send_generic($recipient, $sender, $subject, $message, $search, $replace, $bcc = FALSE)
{
	global $setting, $setting_smtp_email, $database;
		
	$setting_email_query = $database->database_query("SELECT * FROM se_settings_email LIMIT 1");
	$setting_smtp_email = $database->database_fetch_assoc($setting_email_query);
	
	// DECODE SUBJECT AND EMAIL FOR SENDING
	$subject = htmlspecialchars_decode($subject, ENT_QUOTES);
	$message = htmlspecialchars_decode($message, ENT_QUOTES);

	// REPLACE VARIABLES IN SUBJECT AND MESSAGE
	$subject = str_replace($search, $replace, $subject);
	$message = str_replace($search, $replace, $message);

	// ENCODE SUBJECT FOR UTF8
	$subject="=?UTF-8?B?".base64_encode($subject)."?=";

	// REPLACE CARRIAGE RETURNS WITH BREAKS
	$message = str_replace("\n", "<br>", $message);

	// SET HEADERS
	$headers = "MIME-Version: 1.0"."\n";
	$headers .= "Content-type: text/html; charset=utf-8"."\n";
	$headers .= "Content-Transfer-Encoding: 8bit"."\n";
	$headers .= "From: $sender"."\n";
	$headers .= "Return-Path: $sender"."\n";
	$headers .= "Reply-To: $sender\n";

	// IF BCC, SET TO AND BCC
	if($bcc) {
	  $headers .= "Bcc: $recipient\n";
	  $recipient = "noreply@domain.com";
	}

	// SEND MAIL
	
	if ($setting_smtp_email['email_method']=="mail") {
		mail($recipient, $subject, $message, $headers);
		
	} elseif ($setting_smtp_email['email_method']=="smtp") {
		
		$mailer = new PHPMailer();
		$mailer->IsSMTP();
		$mailer->Subject = $subject;
		$mailer->From = $setting['setting_email_fromemail'];
		$mailer->FromName = $setting['setting_email_fromname'];
		$mailer->MsgHTML($message);
		$mailer->AddAddress($recipient);
		$mailer->Host = $setting_smtp_email['smtp_host'];
		$mailer->Username = $setting_smtp_email['smtp_user'];
		$mailer->Password = $setting_smtp_email['smtp_pass'];
		$mailer->Port = $setting_smtp_email['smtp_port'];
		if ($setting_smtp_email['smtp_port']==465) {
			$mailer->SMTPSecure = "ssl";
		} else {
			$mailer->SMTPSecure = "";
		}
		$mailer->Send();
		
	}
	

	return true;
}

// END send_generic() FUNCTION









// THIS FUNCTION SENDS A CUSTOM EMAIL
// INPUT: $systememail REPRESENTING THE SYSTEM EMAIL TO SEND
//	  $recipient_email REPRESENTING THE EMAIL(S) OF THE RECIPIENT
//	  $replace (OPTIONAL) REPRESENTING THE VARIABLES TO BE INSERTED
//	  $bcc (OPTIONAL) REPRESENTING WHETHER TO BCC ALL RECIPIENTS
// OUTPUT: 

function send_systememail($systememail, $recipient_email, $replace = Array(), $bcc = FALSE)
{
	global $setting, $database, $setting_smtp_email;
		
	$setting_email_query = $database->database_query("SELECT * FROM se_settings_email LIMIT 1");
	$setting_smtp_email = $database->database_fetch_assoc($setting_email_query);

	// RETRIEVE EMAIL INFO
	$email = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_systememails WHERE systememail_name='{$systememail}' LIMIT 1"));

	SE_Language::_preload_multi($email['systememail_subject'], $email['systememail_body']);
	SE_Language::load();

	// GET/DECODE SUBJECT AND MESSAGE 
	$subject = htmlspecialchars_decode(SE_Language::_get($email['systememail_subject']), ENT_QUOTES);
	$message = htmlspecialchars_decode(SE_Language::_get($email['systememail_body']), ENT_QUOTES);

	// REPLACE VARIABLES IN SUBJECT AND MESSAGE
	$subject = vsprintf($subject, $replace);
	$message = vsprintf($message, $replace);

	// ENCODE SUBJECT FOR UTF8
	$subject="=?UTF-8?B?".base64_encode($subject)."?=";

	// REPLACE CARRIAGE RETURNS WITH BREAKS
	$message = str_replace("\n", "<br>", $message);

	// SET HEADERS
	$sender = "{$setting['setting_email_fromname']} <{$setting['setting_email_fromemail']}>";
	$headers = "MIME-Version: 1.0"."\n";
	$headers .= "Content-type: text/html; charset=utf-8"."\n";
	$headers .= "Content-Transfer-Encoding: 8bit"."\n";
	$headers .= "From: $sender"."\n";
	$headers .= "Return-Path: $sender"."\n";
	$headers .= "Reply-To: $sender\n";

	// IF BCC, SET TO AND BCC
	if( $bcc )
  	{
	  $headers .= "Bcc: $recipient_email\n";
	  $recipient_email = "noreply@domain.com";
	}

	// SEND MAIL

	if ($setting_smtp_email['email_method']=="mail") {
		mail($recipient_email, $subject, $message, $headers);
		
	} elseif ($setting_smtp_email['email_method']=="smtp") {
			
		$mailer = new PHPMailer();
		$mailer->IsSMTP();
		$mailer->Subject = $subject;
		$mailer->From = $setting['setting_email_fromemail'];
		$mailer->FromName = $setting['setting_email_fromname'];
		$mailer->MsgHTML($message);
		$mailer->AddAddress($recipient_email);
		$mailer->Host = $setting_smtp_email['smtp_host'];
		$mailer->Username = $setting_smtp_email['smtp_user'];
		$mailer->Password = $setting_smtp_email['smtp_pass'];
		$mailer->Port = $setting_smtp_email['smtp_port'];
		if ($setting_smtp_email['smtp_port']==465) {
			$mailer->SMTPSecure = "ssl";
		} else {
			$mailer->SMTPSecure = "";
		}
		$mailer->Send();
		
	}

	return true;
}

// END send_systememail() FUNCTION


?>