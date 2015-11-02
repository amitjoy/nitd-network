<?php 

//Declaring the session object.
$session_object =& SESession::getInstance();

//Getting the value of 'show_ie6_popup' from the session
$show_ie6_popup = $session_object->get('show_ie6_popup');

//Checking the 'show_ie6_popup' field in the session which is 1 or 2.
if($show_ie6_popup != 1) {
	$session_object->set('show_ie6_popup', 2);
	//Checking the browsers which is used by Person/Admin
	$ua = $_SERVER['HTTP_USER_AGENT'];
  if (strpos($ua, 'MSIE') != false && substr($ua, strpos($ua, 'MSIE')+5,1) < 7) {
		$result = $database->database_fetch_assoc($database->database_query("SELECT ie6_browser, ie6_message FROM se_ie6_settings WHERE id = 1"));
		$browsers = $result['ie6_browser'];
		$message = $result['ie6_message'];
		$browsers = explode(",", $browsers);
		$smarty->assign("browsers", $browsers);
		$smarty->assign('message', $message);
		$smarty->assign('check', 2);
	}  
} 
?>