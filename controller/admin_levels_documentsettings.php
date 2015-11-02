<?php
$page = "admin_levels_documentsettings";
include "admin_header.php";


if(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = "main"; }
if(isset($_POST['level_id'])) { $level_id = $_POST['level_id']; } elseif(isset($_GET['level_id'])) { $level_id = $_GET['level_id']; } else { $level_id = 0; }

// VALIDATE LEVEL ID
$level = $database->database_query("SELECT * FROM se_levels WHERE level_id='$level_id'");


if($database->database_num_rows($level) != 1)
{ 
  header("Location: admin_levels.php");
  exit();
}


// SET RESULT AND ERROR VARS
$result = 0;
$is_error = 0;

$level_info = $database->database_fetch_assoc($level);

#GETTING VALUE OF upload_max_filesize
$max_size = (int)ini_get('upload_max_filesize')*1024;

if($task == "dosave") {
	
	
	// FIND THE FORM SUBMISSION DATA
	$level_document_search = $_POST['level_document_search'];	
	$level_document_allow = $_POST['level_document_allow'];
	$level_document_filesize = $_POST['level_document_filesize'];
  $level_document_entries = $_POST['level_document_entries'];
  $level_document_approved = (!empty($_POST['level_document_approved']) ? 1 : 0);
  $level_document_privacy   = ( is_array($_POST['level_document_privacy']) ? $_POST['level_document_privacy'] : array() );
  $level_document_comments  = ( is_array($_POST['level_document_comments']) ? $_POST['level_document_comments'] : array() );  


  
  // CHECK THAT A NUMBER BETWEEN 1 AND 999 WAS ENTERED FOR DOCUMENTS ENTRIES
  if(!is_numeric($level_document_entries) OR $level_document_entries < 0 OR $level_document_entries > 999) {
    $is_error = 1;
    $error_message = 650003126;
  }
  
  // GET PRIVACY AND PRIVACY DIFFERENCES
  if( empty($level_document_privacy) || !is_array($level_document_privacy) ) $level_document_privacy = array(63);
  rsort($level_document_privacy);
  $new_privacy_options = $level_document_privacy;
  $level_document_privacy = serialize($level_document_privacy);
  
  // GET COMMENT AND COMMENT DIFFERENCES
  if( empty($level_document_comments) || !is_array($level_document_comments) ) $level_document_comments = array(63);
  rsort($level_document_comments);
  $new_comments_options = $level_document_comments;
  $level_document_comments = serialize($level_document_comments);
    

  //CHECKING THAT THE MAXIMUM FILE SIZE DOES NOT EXCEED upload_max_filesize
	if($level_document_filesize > $max_size) {
		$is_error = 1;
    $error_message = 650003141;
	}
	
	
 if($is_error == 0) {
 	
 	 // SAVE SETTINGS
    $level_scribd_album_maxsize = $level_scribd_album_maxsize*1024;
    
        $sql = "
      UPDATE
        se_levels
      SET 
			level_document_search='$level_document_search',
			level_document_approved='$level_document_approved',
      level_document_comments='$level_document_comments',
      level_document_privacy='$level_document_privacy',
			level_document_allow='$level_document_allow',
			level_document_filesize='$level_document_filesize',
			level_document_entries='$level_document_entries'
      WHERE
        level_id='{$level_info['level_id']}'
      LIMIT
        1
    ";
     $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
     if( !$level_document_search )
     {
      $database->database_query("UPDATE se_documents INNER JOIN se_users ON se_users.user_id=se_documents.document_user_id SET se_documents.document_search='1' WHERE se_users.user_level_id='{$level_info['level_id']}'") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    }
    
     $database->database_query("UPDATE se_documents INNER JOIN se_users ON se_users.user_id=se_documents.document_user_id SET document_privacy='{$new_privacy_options[0]}' WHERE se_users.user_level_id='{$level_info['level_id']}' && se_documents.document_privacy NOT IN('".join("','", $new_privacy_options)."')") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
     
    $database->database_query("UPDATE se_documents INNER JOIN se_users ON se_users.user_id=se_documents.document_user_id SET document_comments='{$new_comments_options[0]}' WHERE se_users.user_level_id='{$level_info['level_id']}' && se_documents.document_comments NOT IN('".join("','", $new_comments_options)."')") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    $level_info = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_levels WHERE level_id='{$level_info['level_id']}'"));
    $result = 1;
	
 }
 else {
 	 $level_info[level_document_filesize] = $level_document_filesize;
 }
  if($result)
  {
  	$smarty->assign('success', 1);
  }
}


// GET PREVIOUS PRIVACY SETTINGS
for($c=7;$c>=1;$c--)
{
  $priv = pow(2, $c)-1;
  if(user_privacy_levels($priv) != "") {
    SE_Language::_preload(user_privacy_levels($priv));
    $privacy_options[$priv] = user_privacy_levels($priv);
  }
}

for($c=7;$c>=0;$c--)
{
  $priv = pow(2, $c)-1;
  if(user_privacy_levels($priv) != "") {
    SE_Language::_preload(user_privacy_levels($priv));
    $comment_options[$priv] = user_privacy_levels($priv);
  }
}


if (empty($level_document_filesize)) {
	$level_document_filesize = (int)ini_get('upload_max_filesize')*1024;
}

$smarty->assign('max_size', $max_size);
$smarty->assign('is_error', $is_error);
$smarty->assign('error_message', $error_message);
$smarty->assign('document_privacy', $privacy_options);
$smarty->assign('document_comments', $comment_options);
$smarty->assign('document_search', $level_info[level_document_search]);
$smarty->assign('document_approved', $level_info[level_document_approved]);
$smarty->assign('level_document_privacy', unserialize($level_info[level_document_privacy]));
$smarty->assign('level_document_comments', unserialize($level_info[level_document_comments]));
$smarty->assign('document_allow', $level_info[level_document_allow]);
$smarty->assign('document_filesize', $level_document_filesize);
$smarty->assign('entries_value', $level_info[level_document_entries]);
$smarty->assign('level_info', $level_info);
$smarty->assign('level_id', $level_info['level_id']);
$smarty->assign('level_name', $level_info['level_name']);

include "admin_footer.php";
?>