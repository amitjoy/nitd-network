<?php

$page = "admin_document";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = 'main'; }

$is_error = 0;

if(!empty($_POST['submit']))
{
	

	$api_key = $_POST['api_key'];
	$secret_key = $_POST['secret_key'];
	$default_visibility = $_POST['default_visibility'];
	$visibility_option = $_POST['visibility_option'];
	$licensing_scribd = $_POST['licensing_scribd'];
	$licensing_option = isset($_POST['licensing_option']) ? 1 : 0;
	$download_format = $_POST['download_format'];
	$include_full_text = isset($_POST['include_full_text']) ? 1 : 0;
	$save_local_server = isset($_POST['save_local_server']) ? 1 : 0;
	$setting_permission = $_POST['setting_permission_document'];
	$setting_profile_block = $_POST['setting_profile_block'];

	$download_allow = $_POST['download_allow'];
	$download_option_show = $_POST['download_option_show'];
	$secure_allow = $_POST['secure_allow'];
  $secure_option_show = $_POST['secure_option_show'];
  $email_allow = $_POST['email_allow'];
  $email_option_show = $_POST['email_option_show'];
  $license_key = $_POST['lsettings'];
  
	$document = new Document($api_key, $secret_key);
  
	$return_lsettings = document_lsettings($license_key, 'document');
	if (!empty($return_lsettings)) {
     $is_error = 1;
		 $smarty->assign('error_message_lsetting', $return_lsettings);
	}	

	try {
  	$result = $document->getList();
	}
  catch(Exception $e) {
    $code =  $e->getCode();
    if ($code == 401) {
  	  $message =  $e->getMessage() . ': Api key is not correct';
  	  $is_error = 1;
  	  $smarty->assign('api_error', $message);
    } 
  }
	if($secret_key == "") {
		$is_error = 1;
		$smarty->assign('error', 650003124);
		
	}
	
  if($_POST['submit'] == 'Update') {
  	if($is_error != 1) {
			$query = "UPDATE se_document_parameters SET api_key = '".$api_key."', secret_key = '".$secret_key."', default_visibility='".$default_visibility."', visibility_option='".$visibility_option."',  secure_allow='".$secure_allow."', secure_option_show='".$secure_option_show."',  download_allow='".$download_allow."', download_option_show='".$download_option_show."', email_allow='".$email_allow."', email_option_show='".$email_option_show."', licensing_scribd='".$licensing_scribd."', licensing_option='".$licensing_option."', download_format='".$download_format."',  include_full_text = '".$include_full_text."', save_local_server = '".$save_local_server."', permission_document = '$setting_permission', document_block = '$setting_profile_block', license_key = '$license_key' WHERE id = 1";
			
			
			 if( !empty($download_allow) && empty($download_option_show) )
      $database->database_query("UPDATE se_documents INNER JOIN se_users ON se_users.user_id=se_documents.document_user_id SET se_documents.document_download='1' ") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
      
      if( !empty($email_allow) && empty($email_option_show) )
      $database->database_query("UPDATE se_documents INNER JOIN se_users ON se_users.user_id=se_documents.document_user_id SET se_documents.document_attachment='1'") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
			
			$result = $database->database_query($query);
			$aff_rows = $database->database_affected_rows();
			if($aff_rows == 1)
			{
				$smarty->assign("confirm", 1);
		  }
		  else {
		  	$smarty->assign("error", 650003125);
		  }
  	} else {
  		$param_array = array();
  		$param_array['api_key'] = $api_key;
  		$param_array['secret_key'] = $secret_key;
  		$param_array['default_visibility'] = $default_visibility;
  		$param_array['visibility_option'] = $visibility_option;
  		$param_array['licensing_scribd'] = $licensing_scribd;
  		$param_array['download_format'] = $download_format;
  		$param_array['include_full_text'] = $include_full_text;
  		$param_array['save_local_server'] = $save_local_server;
  		$param_array['permission_document'] = $setting_permission;
  		$param_array['document_block'] = $setting_profile_block;
  		$param_array['secure_allow'] = $secure_allow;
  		$param_array['secure_option_show'] = $secure_option_show;
  		$param_array['download_allow'] = $download_allow;
  		$param_array['download_option_show'] = $download_option_show;
  		$param_array['email_allow'] = $email_allow;
  		$param_array['email_option_show'] = $email_option_show;
  		$param_array['license_key'] = $license_key;
  	}
  }	 
}

if(empty($is_error)) {
	$query = "SELECT * FROM se_document_parameters";
	$result = $database->database_query($query);
	$param_array = $database->database_fetch_assoc($result);
}



$smarty->assign("param_array", $param_array);
$smarty->assign("categories", $category_array);
$smarty->assign("is_error", $is_error);
include "admin_footer.php";
?>