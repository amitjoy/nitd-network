<?php

$page = "user_document";
include "header.php";

if(isset($_POST['document_id'])) { $doc_id = $_POST['document_id']; } elseif(isset($_GET['document_id'])) { $doc_id = $_GET['document_id']; } else { $doc_id = 0; }

// ENSURE DOCUMNETS ARE ENABLED FOR THIS USER
if( !$user->level_info['level_document_allow'] )
{
  header("Location: user_home.php");
  exit();
}


$submit_value = 'Publish';
$document_attachment = 1;
$document_download = 1;
$document_secure = 1;
$error_array = array();

$query = "SELECT * FROM se_document_parameters";
$params = $database->database_fetch_assoc($database->database_query($query));

$scribd_api_key = $params['api_key'];
$scribd_secret = $params['secret_key'];
$scribd = new Document($scribd_api_key, $scribd_secret, $user->user_info['user_id']);

try {
	$result = $scribd->getList();
}
catch(Exception $e) {
    $code =  $e->getCode();
    if ($code == 401) {
  	  $message =  $e->getMessage() . ': Api key is not correct';
  	  $is_error = 1;
  	  $smarty->assign('api_error', $message);
    } 
  }


if ($is_error == 1) {
	$page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 650003224);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}



//CHECK IF THE USER'S UPLOADED DOCUMENTS HAS NOT REACHED TO MAXIMUM ALLOWED DOCUMENTS
if($doc_id == 0 && $user->level_info['level_document_entries'] != 0) {
	$where= " se_documents.document_user_id=".$user->user_info[user_id]."";
	$total_entries = $scribd->documents_total($where);
	if($total_entries >= $user->level_info['level_document_entries']) {
		$page = "error";
	  $smarty->assign('error_header', 639);
	  $smarty->assign('error_message', 650003128);
	  $smarty->assign('error_submit', 641);
	  include "footer.php";
	}
	
}

$scribd->my_user_id = $user->user_info['user_id']; 

if($doc_id != 0) {	
	$document = $database->database_fetch_assoc($database->database_query("Select * FROM se_documents WHERE document_id = '$doc_id' AND document_user_id = '".$user->user_info['user_id']."'"));
	if($document) {
		//GETTING THE DOCUMENT TAGS
		$tags_array = array();
		$tags = $database->database_query("SELECT tag_name FROM se_document_tags INNER JOIN se_documenttags ON se_document_tags.tag_id = se_documenttags.id WHERE se_document_tags.document_id='{$document['document_id']}' ORDER BY se_document_tags.id");
		while($info = $database->database_fetch_assoc($tags)) {
			$tags_array[] = $info['tag_name'];
		}
		$document_category = $document['document_category_id'];
		//CHECKING IF THIS CATEGORY ID DEPENDENT
		if($document_category != 0) {
			$result = $database->database_fetch_assoc($database->database_query("SELECT cat_dependency FROM se_document_categories WHERE category_id = '$document_category'"));
			$dependency = $result['cat_dependency'];
			$smarty->assign('dependency', $dependency);
		}
		
		$doc_title = $document['document_title'];
		$doc_slug = $document['document_slug'];
		$description = $document['document_description'];
		$default_visibility = $document['document_private'];
		$license = $document['document_license'];
		$document_search = $document['document_search'];
		$document_privacy = $document['document_privacy'];
		$document_comments = $document['document_comments'];
		$document_doc_id = $document['document_doc_id'];
		$document_attachment = $document['document_attachment'];
		$document_secure = $document['document_secure'];
		$document_download= $document['document_download'];
		$document_filepath = $document['document_filepath'];
		$tags = implode(', ', $tags_array);
		$submit_value = 'Update';
		
	}
	else {
		  $page = "error";
		  $smarty->assign('error_header', 639);
		  $smarty->assign('error_message', 650003127);
		  $smarty->assign('error_submit', 641);
		  include "footer.php";
	}
}





if(isset($_POST['upload']) || isset($_POST['draft'])) {
	$document_category = $_POST['document_category'];
	if(isset($_POST['document_subcat_'.$document_category])) {
		$smarty->assign('dependency', $document_category);
		$document_category = $_POST['document_subcat_'.$document_category];
	}
	$doc_title = filter_var($_POST['doc_title'], FILTER_SANITIZE_STRING);
	$doc_slug = filter_var($_POST['doc_title'], FILTER_SANITIZE_STRING);
	
	// create document slug
	$doc_slug = document_createslug($doc_slug);
	
  $description = filter_var($_POST['doc_description'], FILTER_SANITIZE_STRING);
  $default_visibility = $_POST['default_visibility'];
  $license = $_POST['license_document'];
  $document_search = $_POST['document_search'];
  $document_comments = $_POST['document_comments'];
  $document_privacy = $_POST['document_privacy'];
  $document_download = $_POST['document_download'];
  $document_attachment = $_POST['allow_attachment'];
  $document_secure = $_POST['document_secure'];
  $document_notify = $_POST['document_notify'];
  $tags = $_POST['doc_tags'];

  
  if($params['licensing_option'] == 0) {
	  $license_document = $params['licensing_scribd'];
  }
	else {
		$license_document = $license;
	}
	
  // IF ADMIN HAD ALLOWD THE DOWNLOAD THEN TAKE THE VLAUE FROM THE $_POST OTHERWISE ALWAYS TAKES NO DOWNLOADING   		
		if ($params['download_allow'] == 0) {
		  $document_download = 0;
		}
		
		// IF ADMIN HAD ALLOWD THE EMAIL ATTACHMNET THEN TAKE THE VLAUE FROM THE $_POST OTHERWISE ALWAYS TAKES NO DOWNLOADING   		
		if ($params['email_allow'] == 0) {
		  $document_attachment = 0;
		}
		
		// IF ADMIN HAD ALLOWD THE SECURE DOCUMNET UPLOAIDNG THEN TAKE THE VLAUE FROM THE $_POST OTHERWISE ALWAYS TAKES NO DOWNLOADING   		
		if ($params['secure_allow'] == 0) {
		  $document_secure = 0;
		}
	
		
		// IF ADMIN HAD ALLOWD THE SECURE DOCUMNET UPLOAIDNG THEN TAKE THE VLAUE FROM THE $_POST OTHERWISE ALWAYS TAKES NO DOWNLOADING   		
		if ($params['default_visibility'] == 'private') {
		  $access = 'private';
		} else {
 	    $access = $default_visibility;
    }
	

  if($user->level_info['level_document_filesize'] > 0) {
  	$file_maxsize = $user->level_info['level_document_filesize']*1024;
  }
  else {
  	$file_maxsize = (int)ini_get('upload_max_filesize')*1024*1024;
  }
	$file_exts = explode(",", str_replace(" ", "", strtolower($user->level_info['level_document_file_exts'])));
	$file_types = explode(",", str_replace(" ", "", strtolower($user->level_info['level_document_file_mimes'])));
	$file_maxwidth = "1000";
	$file_maxheight = "1000";
	
	
  	
  // MAKE SURE  TITLE IS NOT EMPTY
  if(str_replace(" ", "", $doc_title) == "") {
    $is_error = 1;
    $error = 650003129;
    $error_array[] = $error;
  }

  // MAKE SURE A CATEGORY IS SELECTED
  if($document_category == '0') {
    $is_error = 1;
    $error = 650003142;
    $error_array[] = $error;
  }
  
  //MAKE SURE IF EXTENSION OF THE UPLOADED FILE SHOULD BE IN THE LIST OF ALLOWABLE EXTENSIONS
  IF(!empty($_FILES['document']['name'])) {
  	$ext = str_replace(".", "", strrchr($_FILES['document']['name'], "."));
  	if(!in_array($ext, $file_exts)) {
	  	$is_error = 1;
	  	$error = 650003130;
	  	$error_array[] = $error;
  	}
  	#commented for now, we are not checking mime type now
//  	$file_mime = $_FILES['document']['type'];
//  	if(!in_array($file_mime, $file_types)) {
//  		$is_error = 1;
//  		$error = 650003130;
//  		$error_array[] = $error;
//  	}
  }
  
  //MAKING SURE FILE SIZE IS NOT MORE THAN THE ADMIN ALLOWED FOR THIS LEVEL
  IF(!empty($_FILES['document']['size']) && $user->level_info['level_document_filesize'] > 0) {
  	if($_FILES['document']['size'] > ($user->level_info['level_document_filesize']*1024)) {
	  	$is_error = 1;
	  	$error = 650003212;
	  	$error_array[] = $error;
  	}
  }
  
   if($_POST['upload'] == "Publish" || $_POST['draft'] == "Draft") {  	
	  // MAKE SURE A DOCUMENT IS SELECTED
	  if(str_replace(" ", "", $_FILES['document']['name']) == "") {
	    $is_error = 1;
	    $error = 650003131;
	    $error_array[] = $error;
	  }
  	
 
  	if($is_error != 1) {

  		
			$ext = str_replace(".", "", strrchr($_FILES['document']['name'], "."));
			$rand = rand(100000000, 999999999);
			if($ext!="")
			$file_newname = "doc_$rand."."$ext";
			$user_id = $user->user_info['user_id'];
			$subdir = "./uploads_document/$user_id";
			if(!is_dir($subdir)) {
				mkdir($subdir, 0777); 
	    	chmod($subdir, 0777); 
			}
			$file_dest = "$subdir/$file_newname";
			$file_name = "document";
			$new_file = new se_upload();
			$new_file->new_upload($file_name, $file_maxsize, $file_exts, $file_types, $file_maxwidth, $file_maxheight);
			if($new_file->is_error == 0) {
				$new_file->upload_file($file_dest); 
				$time = time();
				$starttime = time();
			  
			  $file_scribd = $file_dest;
				$doc_type = null;
				if($document_download == 1)
				$download = "download-pdf";
				else
				$download = "view-only";
			
			  $rev_id = null;
			  // Uploading the document from our server to Scribd's Server
			  
			  try {
					$data = $scribd->upload($file_scribd, $doc_type, $access, $rev_id, $download, $document_secure);
				}
				catch(Exception $e) {
					$message =  $e->getMessage();
					$excep_error = 1;
					$smarty->assign('excep_message', $message);
				} 
			  
	   
		    if(!empty($data['doc_id'])) {
		   	
		   
			    // File conversion on Scribd is successful
			    //if($stat == 'DONE') {
			   			if($license_document == 'ns')
			   			{
			   				$scribd_license = null;
			   			}
			   			else {
			   				$scribd_license = $license_document;
			   			}
			   			
			   			
			   			try {
							  $changesetting = $scribd->changeSettings($data['doc_id'], $doc_title, $description, $access, $scribd_license, $document_download);			   		
			   	  	  $setting = $scribd->getSettings($data['doc_id']);
							}
							catch(Exception $e) {
								$message =  $e->getMessage();
								$excep_error = 1;
								$smarty->assign('excep_message', $message);
							} 
			  
				  	$full_text = "";

				  	if($params['save_local_server'] == 0) {
          		$file_path = "";
          		unlink($file_scribd);
          	}
          	else {
          		$file_path = $file_scribd;
          	}
          	if($user->level_info['level_document_approved'] == 1) {
          		$approved = 1;
          	}
          	else {
          		$approved = 0;
          	}
          	if($user->level_info['level_document_search'] == 0) {
          		$document_search = 1;
          	}
			   		$user_id = $user->user_info['user_id'];
			   		$file_size = $_FILES['document']['size'];
			   		$file_mime = $_FILES['document']['type'];
			   		$document_id = $data['doc_id'];
			   		$secret_password = $data['secret_password'];
			   		$access_key = $data['access_key'];
			   		$thumbnail_url = trim($setting['thumbnail_url']);
			   		$document_publish = isset($_POST['upload']) ? 1 : 0;
			   		$time = time();
			   		$query = "INSERT INTO se_documents(document_user_id, document_category_id, document_title, document_slug, document_description, document_filename, document_filepath, document_filemime, document_filesize, document_doc_id, document_secret_password, document_access_key, document_private, document_license, document_fulltext, document_thumbnail, document_search, document_privacy, document_comments, document_datecreated, document_dateupdated, document_approved, document_publish, document_attachment, document_download, document_secure) VALUES('$user_id', '$document_category','$doc_title', '$doc_slug', '$description', '$file_newname', '$file_path', '$file_mime', '$file_size', '$document_id', '$secret_password', '$access_key', '$access', '$license_document', '$full_text' ,'$thumbnail_url', '$document_search', '$document_privacy', '$document_privacy', '$time', '$time', '$approved', '$document_publish', '$document_attachment', '$document_download', '$document_secure')";

			   		$result = $database->database_query($query);
			   		$id = $database->database_insert_id();
			   		
			   		# SAVING THE TAGS ASSOCIATED WITH THE DOCUMENT
			   		$tags_array = explode(",", $tags);
			   		$added_tags = array();
			   		foreach($tags_array as $value) {
			   			$value = trim($value);
			   			if(!empty($value) && !in_array($value, $added_tags)) {
				   			//CHECKING IF THIS TAG IS ALREADY IS IN DATABASE OR NOT
				   			$query = "SELECT id FROM se_documenttags WHERE tag_name = '{$value}'";
				   			$result = $database->database_query($query);
				   			if($database->database_num_rows($result) > 0) {
				   				$record = $database->database_fetch_assoc($result);
				   				$tag_id = $record['id'];
				   			}
				   			else {
				   				$database->database_query("INSERT INTO se_documenttags(tag_name) VALUES ('{$value}')");
				   				$tag_id = $database->database_insert_id();
				   			}
				   			
				   			//INSERTING VALUE INTO SE_DOCUMENT_TAGS TABLE
				   			$database->database_query("INSERT INTO se_document_tags(document_id, tag_id) VALUES ('{$id}', '{$tag_id}')");
				   		}
				   		$added_tags[] = $value;
			   		}
			   		
			   		if (!(empty($document_publish) || empty($approved) || empty($document_search))) {
				   	  if(strlen($doc_title) > 100) { $doc_title = substr($doc_title, 0, 97); $doc_title .= "..."; }
				   		$actions->actions_add($user, "documentadd", array(
			        $user->user_info['user_username'],
			        $user->user_displayname,
			        $id,
			        $doc_title
			        ));
			   		}
			   		if($params['save_local_server'] == 0) {
			   			unlink($file_scribd);
			   		}
			   		header("location:user_documents.php?success=1");
//			  	}
//			    else {
//			   	  //UNLINK THE UPLOADED FILE BECAUSE FILE CONVERSION FAILS ON SCRIBD
//		   	    unlink($file_scribd);
//		   	    $is_error = 1;
//				    $error = 650003132;
//			    }
		    }
		    else {
		  		$is_error = 1;
			    $error_array[] = 650003222;
			    unlink($file_scribd);
		  	}
			}
			else {
	  		$is_error = 1;
		    $error_array[] = $new_file->is_error;
	  	}
  	}
  	
  }
  
  
  
  // EDIT THE DOCUMNET FROM HERE
  
   if($_POST['upload'] == 'Update') {
  	 if(!empty($_FILES['document']['name'])) {
  	   if($is_error != 1) {
			   $ext = str_replace(".", "", strrchr($_FILES['document']['name'], "."));
				 $rand = rand(100000000, 999999999);
				 if($ext!="")
				 $file_newname = "doc_$rand."."$ext";
				 $user_id = $user->user_info['user_id'];
				 $subdir = "./uploads_document/$user_id";
				 if(!is_dir($subdir)) {
					 mkdir($subdir, 0777); 
			     chmod($subdir, 0777); 
				 }
				 $file_dest = "$subdir/$file_newname";
				 $file_name = "document";
				 $new_file = new se_upload();
				 $new_file->new_upload($file_name, $file_maxsize, $file_exts, $file_types, $file_maxwidth, $file_maxheight);
				 if($new_file->is_error == 0) {
					 $new_file->upload_file($file_dest); 
					 $time = time();
					 $starttime = time();
				  
	  			 $file_scribd = $file_dest;
					 $doc_type = null;
					 $download = "view-only";

					 $rev_id = $document['document_doc_id'];
					 // Uploading the document from our server to Scribd's Server	
					 				 
				 		try {
						$data = $scribd->upload($file_scribd, $doc_type, $access, $rev_id, $download, $document_secure);
						}
						catch(Exception $e) {
							$message =  $e->getMessage();
							$excep_error = 1;
							$smarty->assign('excep_message', $message);
						} 
			       if(!empty($data['doc_id'])) {
				   	
				   	   // After uploading on SCRIBD, the file conversion begins
				   	   
				   	   		try {
										$stat = $scribd->getConversionStatus($data['doc_id']);
									}
									catch(Exception $e) {
										$message =  $e->getMessage();
										$excep_error = 1;
										$smarty->assign('excep_message', $message);
									} 
				   	   
					   	 if($params['licensing_option'] == 0) {
					   		 $license_document = $params['licensing_scribd'];
					   	 }
					   	else {
					   		$license_document = $license;
					   	}
					   	
					   		if($license_document == 'ns')
				   			{
				   				$scribd_license = null;
				   			}
				   			else {
				   				$scribd_license = $license_document;
				   			}
			   			
			   					try {
										  $changesetting = $scribd->changeSettings($data['doc_id'], $doc_title, $description, $access, $scribd_license, $document_download);
				   	          $setting = $scribd->getSettings($data['doc_id']);
									}
									catch(Exception $e) {
										$message =  $e->getMessage();
										$excep_error = 1;
										$smarty->assign('excep_message', $message);
									} 
				  	    $full_text = "";


              if($params['save_local_server'] == 0) {
              	$file_path = "";
              	unlink($file_scribd);
              }
              else {
              	$file_path = $file_scribd;
              }
              if($user->user_info['level_document_approved'] == 1) {
              	$approved = 1;
              }
              else {
              	$approved = 0;
              }
					   	$user_id = $user->user_info['user_id'];
					   	$file_size = $_FILES['document']['size'];
					   	$file_mime = $_FILES['document']['type'];
					   	$document_id = $data['doc_id'];
					   	$secret_password = $data['secret_password'];
					   	$access_key = $data['access_key'];
					   	$thumbnail_url = trim($setting['thumbnail_url']);
					   	$time = time();
					   	
					   	$query = "UPDATE se_documents SET document_category_id = '$document_category', document_title = '$doc_title', document_slug = '$doc_slug', document_description = '$description', document_filename = '$file_newname', document_filepath = '$file_path', document_filemime = '$file_mime', document_filesize = '$file_size', document_private = '$default_visibility', document_license = '$license_document', document_fulltext = '$full_text', document_thumbnail = '$thumbnail_url', document_search = '$document_search', document_privacy = '$document_privacy', document_comments= '$document_comments', document_dateupdated = '$time', document_attachment='$document_attachment', document_status=0, document_download='$document_download' WHERE document_id = '$doc_id'";
					   	$result = $database->database_query($query);
					   	
					   	//UPDATING DOCUMENT TAGS
					   	#FIRST DELETING ALL DOCUMENT TAGS
					   	$database->database_query("DELETE FROM se_document_tags WHERE document_id='$doc_id'");
					   	
					   	# SAVING THE TAGS ASSOCIATED WITH THE DOCUMENT
				   		$tags_array = explode(",", $tags);
				   		$added_tags = array();
				   		foreach($tags_array as $value) {
				   			$value = trim($value);
					   		if(!empty($value) && !in_array($value, $added_tags)) {
					   			//CHECKING IF THIS TAG IS ALREADY IS IN DATABASE OR NOT
					   			$query = "SELECT id FROM se_documenttags WHERE tag_name = '{$value}'";
					   			$result = $database->database_query($query);
					   			if($database->database_num_rows($result) > 0) {
					   				$record = $database->database_fetch_assoc($result);
					   				$tag_id = $record['id'];
					   			}
					   			else {
					   				$database->database_query("INSERT INTO se_documenttags(tag_name) VALUES ('{$value}')");
					   				$tag_id = $database->database_insert_id();
					   			}
					   			
					   			//INSERTING VALUE INTO SE_DOCUMENT_TAGS TABLE
					   			$database->database_query("INSERT INTO se_document_tags(document_id, tag_id) VALUES ('{$doc_id}', '{$tag_id}')");
					   		}
					   		$added_tags[] = $value;
				   		}
				   		
					   	if($params['save_local_server'] == 0) {
					   		unlink($file_scribd);
					   	}
					   	
					   	//DELETE OLD FILE FROM LOCAL SERVER IF EXISTS
					   	if($_POST['document_filepath'] != '') {
					   		unlink($_POST['document_filepath']);
					   	}
					   	header("location:user_documents.php?success=2");
//					   }
//					   else {
//					   	 //UNLINK THE UPLOADED FILE BECAUSE FILE CONVERSION FAILS ON SCRIBD
//				   	   unlink($file_scribd );
//				   	   $is_error = 1;
//				       $error = 650003132;
//					   }
				   }
				   else {
			  		$is_error = 1;
				    $error_array[] = 650003223;
				    unlink($file_scribd);
			  	}
				}
				else {
		  		$is_error = 1;
			    $error_array[] = $new_file->is_error;
		  	}
	  	}
  		
  		
  	}
  	else {
  		if($is_error != 1) {
  			if($license_document == 'ns')
   			{
   				$scribd_license = null;
   			}
   			else {
   				$scribd_license = $license_document;
   			}
   			
				try {
					$changesetting = $scribd->changeSettings($_POST['document_doc_id'], $doc_title, $description, $access, $scribd_license, $document_download); 
				}
				catch(Exception $e) {
					$message =  $e->getMessage();
					$excep_error = 1;
					$smarty->assign('excep_message', $message);
				} 
			
  			
	  		$time = time();
	  		$query = "UPDATE se_documents SET document_category_id = '$document_category', document_title = '$doc_title', document_slug = '$doc_slug', document_description = '$description', document_private = '$default_visibility', document_license = '$license_document', document_search = '$document_search', document_privacy = '$document_privacy', document_comments = '$document_comments', document_dateupdated = '$time', document_attachment='$document_attachment', document_download='$document_download' WHERE document_id = '$doc_id'";
	  		$result = $database->database_query($query);
	  		
	  			//UPDATING DOCUMENT TAGS
			   	#FIRST DELETING ALL DOCUMENT TAGS
			   	$database->database_query("DELETE FROM se_document_tags WHERE document_id='$doc_id'");
			   	
			   	# SAVING THE TAGS ASSOCIATED WITH THE DOCUMENT
		   		$tags_array = explode(",", $tags);
		   		$added_tags = array();
		   		foreach($tags_array as $value) {
		   			$value = trim($value);
		   			if(!empty($value) && !in_array($value, $added_tags)) {
			   			//CHECKING IF THIS TAG IS ALREADY IS IN DATABASE OR NOT
			   			$query = "SELECT id FROM se_documenttags WHERE tag_name = '{$value}'";
			   			$result = $database->database_query($query);
			   			if($database->database_num_rows($result) > 0) {
			   				$record = $database->database_fetch_assoc($result);
			   				$tag_id = $record['id'];
			   			}
			   			else {
			   				$database->database_query("INSERT INTO se_documenttags(tag_name) VALUES ('{$value}')");
			   				$tag_id = $database->database_insert_id();
			   			}
			   			
			   			//INSERTING VALUE INTO SE_DOCUMENT_TAGS TABLE
			   			$database->database_query("INSERT INTO se_document_tags(document_id, tag_id) VALUES ('{$doc_id}', '{$tag_id}')");
			   		}
			   		$added_tags[] = $value;
		   		}
	  		
				header("location:user_documents.php?success=2");
  		}
  	}
  }
  
}


  
//GETTING THE DOCUMENT_CATEGORIES AND SUBCATEGORIES
$result = $database->database_query("SELECT * FROM se_document_categories WHERE cat_dependency='0' ORDER BY cat_order");
$categories = array();
if($database->database_num_rows($result) > 0) {
	while($info = $database->database_fetch_assoc($result)) {
		//GETTING SUB CATEGORIES ASSOCIATED WITH THIS CATEGORY
		$sub_cat_array = array();
		$sub_cat = $database->database_query("SELECT * FROM se_document_categories WHERE cat_dependency = '{$info['category_id']}' ORDER BY cat_order");
		while($info2 = $database->database_fetch_assoc($sub_cat)) {
			$tmp_array = array('sub_cat_id' => $info2['category_id'],
												 'sub_cat_name' => $info2['category_name'],
												 'order' => $info2['cat_order']		
			);
			$sub_cat_array[] = $tmp_array;
		}
		$category_array = array('category_id' => $info['category_id'],
		                        'category_name' => $info['category_name'],
		                        'order' => $info['order'],
		                        'sub_categories' => $sub_cat_array  
		                  );
		$categories[] = $category_array;                  
		
	}
}


// GET DOCUMENT PREVIOUS PRIVACY SETTINGS
$level_document_privacy = unserialize($user->level_info[level_document_privacy]);
rsort($level_document_privacy);
for($c=0;$c<count($level_document_privacy);$c++) {
  if(user_privacy_levels($level_document_privacy[$c]) != "") {
    SE_Language::_preload(user_privacy_levels($level_document_privacy[$c]));
    $privacy_options[$level_document_privacy[$c]] = user_privacy_levels($level_document_privacy[$c]);
  }
}

$level_document_comments = unserialize($user->level_info[level_document_comments]);
rsort($level_document_comments);
for($c=0;$c<count($level_document_comments);$c++) {
  if(user_privacy_levels($level_document_comments[$c]) != "") {
    SE_Language::_preload(user_privacy_levels($level_document_comments[$c]));
    $comment_options[$level_document_comments[$c]] = user_privacy_levels($level_document_comments[$c]);
  }
}

// SET SOME DEFAULTS DOCUMENT VALUES
if(!isset($document_search)) {
	$document_search = 1; 
}
if(!isset($document_privacy)) {
	$document_privacy = $level_document_privacy[0];
}
if(!isset($document_comments)) { 
	$document_comments = $level_document_comments[0];
}


if($user->level_info['level_document_filesize'] > 0) {
	$file_maxsize = $user->level_info['level_document_filesize'];
}
else {
	$file_maxsize = (int)ini_get('upload_max_filesize')*1024;
}

$smarty->assign('category_id', $document_category);
$smarty->assign('doc_title', $doc_title);
$smarty->assign('doc_id', $doc_id);
$smarty->assign('doc_description', $description);
$smarty->assign('is_error', $is_error);
$smarty->assign('error', $error);
$smarty->assign('error_array', array_unique($error_array));
$smarty->assign('default_visibility', $default_visibility);
$smarty->assign('license', $license);
$smarty->assign('document_comments', $document_comments);
$smarty->assign('document_privacy', $document_privacy);
$smarty->assign('document_search', $document_search);
$smarty->assign('privacy_options', $privacy_options);
$smarty->assign('comment_options', $comment_options);
$smarty->assign('submit_value', $submit_value);
$smarty->assign('document_doc_id', $document_doc_id);
$smarty->assign('document_filepath', $document_filepath);
$smarty->assign("params", $params);
$smarty->assign("categories", $categories);
$smarty->assign("doc_tags", $tags);
$smarty->assign("document_attachment", $document_attachment);
$smarty->assign("document_secure", $document_secure);
$smarty->assign("document_download", $document_download);
$smarty->assign("file_maxsize", $file_maxsize);
$smarty->assign("excep_error", $excep_error);
$smarty->assign("excep_message", $excep_message);
include "footer.php";
?>