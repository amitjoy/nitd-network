<?php

$page = "admin_userconnection";
include "admin_header.php";

if (isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
$is_error = 0;
$result = 0;
$success_message = '';
$error_level_value = '';

if ($task == "edit") {
	
	$key_lsetting = $_POST['lsettings'];
	$return_lsettings = userconnections_lsettings($key_lsetting, 'userconnection');
	if (!empty($return_lsettings)) {
	  $is_error = true;
	  $smarty->assign('error_message_lsetting', $return_lsettings);
	}
	// GET POST VARIABLES
	$is_message =  $_POST['is_message'];
	$level = $_POST['level'];
	$level = 1+$level;
	$userconnection_position = $_POST['userconnection_position'];
	$userconnection_arrow = $_POST['userconnection_arrow'];
	$userconnection_degree = $_POST['userconnection_degree'];
	$profile_page = $_POST['profile_page'];
	$user_home_page = $_POST['user_home_page'];
	
	if (empty($profile_page)) {
		$profile_page = 0;
	}
	
	if (empty($user_home_page)) {
		$user_home_page = 0;
	}
	
	if ($is_message) {
		$message = $_POST['message'];
		if (empty($message)) {
	 		$is_error = true;
	  	$smarty->assign ('error_message', 650002013);
  	}
	}
	
	if (empty($level)) {
	  $is_error = true;
	  $smarty->assign ('error_level_value', 650002010);
  }

 
  // if error message is false and is_message is 1 then update the userconnection settings(level,is_message,message)
  if (empty($is_error) && $is_message) {
	  $database->database_query ("UPDATE userconnection_settings SET level = '$level', is_message =  '$is_message', message = '$message', userconnection_position = '$userconnection_position', userconnection_arrow = '$userconnection_arrow', userconnection_degree = '$userconnection_degree', profile_page ='$profile_page', user_home_page = '$user_home_page', license_key='$key_lsetting' ");
	  
	  $smarty->assign ('success_message', 650002011);
  }
  // else if only error message is false and is_message is 0 then update only level and is_message
	elseif (empty($is_error)) {
		$database->database_query ("UPDATE userconnection_settings SET level = '$level', is_message =  '$is_message', userconnection_position = '$userconnection_position', userconnection_arrow = '$userconnection_arrow', userconnection_degree = '$userconnection_degree', profile_page ='$profile_page', user_home_page = '$user_home_page', license_key='$key_lsetting' ");
	  
		$smarty->assign ('success_message', 650002011);
	}
	else {
  	// AN ERROR OCCURED SEND THE DATA BACK
    $result = array(
    	'is_message'     => $is_message,
    	'level'          => $level,  
    	'message'        => $message, 
    	'userconnection_position'   => $userconnection_position,
    	'userconnection_arrow'      => $userconnection_arrow,
    	'userconnection_degree'     => $userconnection_degree,
    	'profile_page'   => $profile_page,
    	'user_home_page' => $user_home_page,
    	'license_key' => $key_lsetting
    );
  }
}

// GET USER LEVEL ARRAY
if (empty($is_error)) {
	$row = $database->database_query ("SELECT * FROM userconnection_settings");
	$result = $database->database_fetch_assoc ($row);
	// PUT VALUES IN GLOBAL VARIABLE $USERCONNECTION_SETTING
	global $userconnection_setting;
	$userconnection_setting = $result;
	$result['level']--;
		
} 
$smarty->assign ('result', $result);
include "admin_footer.php";
?>