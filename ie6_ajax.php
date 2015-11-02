<?php

define('SE_PAGE_AJAX', TRUE);
$page = "ie6_ajax";

include "header.php";

//Taking the session object
$session_object =& SESession::getInstance();

//Getting the value of show_ie6_popup from the session
$show_ie6_popups = $session_object->get('show_ie6_popup');

$task = $_POST['task'];

if($task == 'show_popup') {
	if($show_ie6_popups == 2) {
		//Setting the show_ie6_popup to 1 in the session
		$session_object->set('show_ie6_popup', 1);
	}
}
?>