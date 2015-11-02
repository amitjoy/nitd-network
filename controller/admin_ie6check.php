<?php

$page = "admin_ie6check";
include "admin_header.php";

$task = ( !empty($_POST['task']) ? $_POST['task']  : ( !empty($_GET['task']) ? $_GET['task'] : NULL ) );

//Fatching the browsers from the database to show the prefield data to admin.
$result = $database->database_fetch_assoc($database->database_query("SELECT ie6_browser, ie6_message FROM se_ie6_settings WHERE id = 1"));

//Assigning the browers to the variable
$browsers = $result['ie6_browser'];
//Assigning the message to the variable
$message = $result['ie6_message'];

$browsers = explode(",",$browsers); 

//Making a arrray for browsers information
foreach($browsers as $value) {
  if($value == 'safari') {
  	$browsers_info['safari'] = $value;
  }
  else if ($value == 'opera')	{
  	$browsers_info['opera'] = $value;
  }
  else if ($value == 'netscape')	{
  	$browsers_info['netscape'] = $value;
  }  	
  else if ($value == 'explorer')	{
  	$browsers_info['explorer'] = $value;
  }  	
  else if ($value == 'mozila')	{
  	$browsers_info['mozila'] = $value;
  }
  else if ($value == 'chrome')	{
  	$browsers_info['chrome'] = $value;
  }  	
}
//Making a array of all the browsers to show prefield
$browsers = $browsers_info;

// IF the admin want to update the browsers and message.
if($task == "update") {
	
	//Getting the browser when admin update the browsers
	$browsers = $_POST['browser'];
	
	  
	//Getting the message which we want to show in pop up
	$message = $_POST['show_message'];
		
	//Checking the browsers if admin not selected any one 
	if(empty($browsers[0]) ){
		$smarty->assign("browser_error", 650005016);
		$is_error = true;
	}
		
	//Prepareing the browser information 
	$browsers_info = implode(",", $browsers);
	 	
	//Checking the messsage if admin left empty this field
	if(empty($message)) {
		$smarty->assign("message_error", 650005017);
		$is_error = true;
	}
	
	//Updating the result 
	if(!($is_error)) {
		$sql = $database->database_query("UPDATE se_ie6_settings SET ie6_browser = '$browsers_info', ie6_message = '$message' WHERE id = 1");
		$smarty->assign("confirm", 1);
	}
	
	//Exploding the array 
	$browsers_update = explode(",", $browsers_info);
	
	//Making a arrray for browsers information when admin update the browsers
	foreach($browsers_update as $value) {
	  if($value == 'safari') {
	  	$browsers_information['safari'] = $value;
	  }
	  else if ($value == 'opera')	{
	  	$browsers_information['opera'] = $value;
	  }
	  else if ($value == 'netscape')	{
	  	$browsers_information['netscape'] = $value;
	  }  	
	  else if ($value == 'explorer')	{
	  	$browsers_information['explorer'] = $value;
	  }  	
	  else if ($value == 'mozila')	{
	  	$browsers_information['mozila'] = $value;
	  }
	  else if ($value == 'chrome')	{
	  	$browsers_information['chrome'] = $value;
	  }  	
	}
	//Making a array of all the browsers to show prefield
	$browsers = $browsers_information;
}


//Showing the prefields of message and browers to the admin  
$check_Array = array( 'browsers' => $browsers, 'message' => $message );

$smarty->assign('check_Array', $check_Array);
	
include "admin_footer.php";
?>