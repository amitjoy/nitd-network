<?php
$page = "admin_profile_edit";
include "admin_header.php";

$task = ( isset($_POST['task']) ? $_POST['task'] : ( isset($_GET['task']) ? $_GET['task'] : NULL ) );
$profilecat_id = ( isset($_POST['profilecat_id']) ? $_POST['profilecat_id'] : ( isset($_GET['profilecat_id']) ? $_GET['profilecat_id'] : NULL ) );
$method_default = ( isset($_POST['method_default']) ? $_POST['method_default'] : ( isset($_GET['method_default']) ? $_GET['method_default'] : NULL ) );


// GET TABS AND FIELDS
$sql = "SELECT * FROM se_profilecats WHERE profilecat_id='{$profilecat_id}' LIMIT 1";
$resource = $database->database_query($sql);

if( $resource && $database->database_num_rows($resource) )
  $profilecat_info = $database->database_fetch_assoc($resource);

if( empty($profilecat_info) || !empty($profilecat_info['profilecat_dependency']) )
{
  header('Location: admin_profile.php');
  exit();
}

SELanguage::_preload($profilecat_info['profilecat_title']);


// Save
if( $task=="dosave" )
{
  $profilecat_displayname_method_allowed = $_POST['profilecat_displayname_method_allowed'];
  $profilecat_displayname_method_custom = $_POST['profilecat_displayname_method_custom'];
  $profilecat_displayname_method_allowed = ( !empty($profilecat_displayname_method_allowed) && is_array($profilecat_displayname_method_allowed) ? array_sum($profilecat_displayname_method_allowed) : 0 );
  
  $sql = "
    UPDATE
      se_profilecats
    SET
      profilecat_displayname_method_allowed='{$profilecat_displayname_method_allowed}',
      profilecat_displayname_method_custom='{$profilecat_displayname_method_custom}'
    WHERE
      profilecat_id='{$profilecat_id}'
    LIMIT
      1
  ";
  
  $resource = $database->database_query($sql);
  
  // Reload info
  $sql = "SELECT * FROM se_profilecats WHERE profilecat_id='{$profilecat_id}' LIMIT 1";
  $resource = $database->database_query($sql);
  $profilecat_info = $database->database_fetch_assoc($resource);
}



// ASSIGN VARIABLES AND SHOW ADMIN PROFILE PAGE
$smarty->assign_by_ref('profilecat_info', $profilecat_info);
include "admin_footer.php";
?>