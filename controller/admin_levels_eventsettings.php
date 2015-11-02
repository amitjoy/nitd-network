<?php

$page = "admin_levels_eventsettings";
include "admin_header.php";

$task     = ( !empty($_POST['task'])      ? $_POST['task']      : ( !empty($_GET['task'])     ? $_GET['task']     : NULL ) );
$level_id = ( !empty($_POST['level_id'])  ? $_POST['level_id']  : ( !empty($_GET['level_id']) ? $_GET['level_id'] : NULL ) );


// VALIDATE LEVEL ID
$sql = "SELECT * FROM se_levels WHERE level_id='{$level_id}' LIMIT 1";
$resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");

if( !$database->database_num_rows($resource) )
{ 
  header("Location: admin_levels.php");
  exit();
}

$level_info = $database->database_fetch_assoc($resource);


// SET RESULT AND ERROR VARS
$result = FALSE;
$is_error = FALSE;



// SAVE CHANGES
if($task == "dosave")
{
  $level_event_allow = $_POST['level_event_allow'];
  $level_event_photo = $_POST['level_event_photo'];
  $level_event_photo_width = $_POST['level_event_photo_width'];
  $level_event_photo_height = $_POST['level_event_photo_height'];
  $level_event_photo_exts = str_replace(", ", ",", $_POST['level_event_photo_exts']);
  $level_event_inviteonly = $_POST['level_event_inviteonly'];
  $level_event_style = $_POST['level_event_style'];
  $level_event_album_exts = str_replace(", ", ",", $_POST['level_event_album_exts']);
  $level_event_album_mimes = str_replace(", ", ",", $_POST['level_event_album_mimes']);
  $level_event_album_storage = $_POST['level_event_album_storage'];
  $level_event_album_maxsize = $_POST['level_event_album_maxsize'];
  $level_event_album_width = $_POST['level_event_album_width'];
  $level_event_album_height = $_POST['level_event_album_height'];
  $level_event_search = $_POST['level_event_search'];
  
  $level_event_privacy = is_array($_POST['level_event_privacy']) ? $_POST['level_event_privacy'] : array();
  $level_event_comments = is_array($_POST['level_event_comments']) ? $_POST['level_event_comments'] : array();
  $level_event_upload = is_array($_POST['level_event_upload']) ? $_POST['level_event_upload'] : array();
  $level_event_tag = is_array($_POST['level_event_tag']) ? $_POST['level_event_tag'] : array();
  
  $level_event_html = str_replace(" ", "", $_POST['level_event_html']);
  $level_event_backdate = !empty($_POST['level_event_backdate']);
  
  // CHECK THAT A NUMBER BETWEEN 1 AND 999 WAS ENTERED FOR WIDTH AND HEIGHT
  if( !is_numeric($level_event_photo_width) || !is_numeric($level_event_photo_height) || $level_event_photo_width < 1 || $level_event_photo_height < 1 || $level_event_photo_width > 999 || $level_event_photo_height > 999)
    $is_error = 3000027;
  
  // CHECK THAT A NUMBER BETWEEN 1 AND 204800 (200MB) WAS ENTERED FOR MAXSIZE
  elseif( !is_numeric($level_event_album_maxsize) || $level_event_album_maxsize < 1 || $level_event_album_maxsize > 204800)
    $is_error =3000028;

  // CHECK THAT WIDTH AND HEIGHT ARE NUMBERS
  elseif( !is_numeric($level_event_album_width) || !is_numeric($level_event_album_height) )
    $is_error = 3000029;
  
  
  // IF THERE WERE NO ERRORS, SAVE CHANGES
  if( !$is_error )
  {
    // GET PRIVACY AND PRIVACY DIFFERENCES
    if( empty($level_event_privacy) || !is_array($level_event_privacy) ) $level_event_privacy = array(255);
    rsort($level_event_privacy);
    $new_privacy_options = $level_event_privacy;
    $level_event_privacy = serialize($level_event_privacy);
    
    // GET COMMENT AND COMMENT DIFFERENCES
    if( empty($level_event_comments) || !is_array($level_event_comments) ) $level_event_comments = array(255);
    rsort($level_event_comments);
    $new_comments_options = $level_event_comments;
    $level_event_comments = serialize($level_event_comments);
    
    // GET UPLOAD AND UPLOAD DIFFERENCES
    if( empty($level_event_upload) || !is_array($level_event_upload) ) $level_event_upload = array(127);
    rsort($level_event_upload);
    $new_upload_options = $level_event_upload;
    $level_event_upload = serialize($level_event_upload);
    
    // GET TAG AND TAG DIFFERENCES
    if( empty($level_event_tag) || !is_array($level_event_tag) ) $level_event_tag = array(127);
    rsort($level_event_tag);
    $new_tag_options = $level_event_tag;
    $level_event_tag = serialize($level_event_tag);
    
    
    // SAVE OTHER SETTINGS
    $level_event_album_maxsize = $level_event_album_maxsize * 1024;
    
    $sql = "
      UPDATE
        se_levels
      SET
        level_event_allow='$level_event_allow',
        level_event_search='$level_event_search',
        level_event_photo='$level_event_photo',
        level_event_photo_width='$level_event_photo_width',
        level_event_photo_height='$level_event_photo_height',
        level_event_photo_exts='$level_event_photo_exts',
        level_event_inviteonly='$level_event_inviteonly',
        level_event_style='$level_event_style',
        level_event_album_exts='$level_event_album_exts',
        level_event_album_mimes='$level_event_album_mimes',
        level_event_album_storage='$level_event_album_storage',
        level_event_album_maxsize='$level_event_album_maxsize',
        level_event_album_width='$level_event_album_width',
        level_event_album_height='$level_event_album_height',
        
        level_event_privacy='$level_event_privacy',
        level_event_comments='$level_event_comments',
        level_event_upload='$level_event_upload',
        level_event_tag='$level_event_tag',
        
        level_event_html='$level_event_html',
        level_event_backdate='$level_event_backdate'
      WHERE
        level_id='{$level_info['level_id']}'
      LIMIT
        1
    ";
    
    $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    if( !$level_event_search )
      $database->database_query("UPDATE se_events INNER JOIN se_users ON se_events.event_user_id=se_users.user_id SET se_events.event_search='1' WHERE se_users.user_level_id='{$level_info['level_id']}'") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    if( !$level_event_inviteonly )
      $database->database_query("UPDATE se_events INNER JOIN se_users ON se_events.event_user_id=se_users.user_id SET se_events.event_inviteonly='0' WHERE se_users.user_level_id='{$level_info['level_id']}' && se_events.event_inviteonly='1'") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    $database->database_query("UPDATE se_events INNER JOIN se_users ON se_events.event_user_id=se_users.user_id SET se_events.event_privacy='{$new_privacy_options[0]}' WHERE se_users.user_level_id='{$level_info['level_id']}' && se_events.event_privacy NOT IN('".join("','", $new_privacy_options)."')") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    $database->database_query("UPDATE se_events INNER JOIN se_users ON se_events.event_user_id=se_users.user_id SET se_events.event_comments='{$new_comments_options[0]}' WHERE se_users.user_level_id='{$level_info['level_id']}' && se_events.event_comments NOT IN('".join("','", $new_comments_options)."')") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    $database->database_query("UPDATE se_events INNER JOIN se_users ON se_events.event_user_id=se_users.user_id SET se_events.event_upload='{$new_upload_options[0]}' WHERE se_users.user_level_id='{$level_info['level_id']}' && se_events.event_upload NOT IN('".join("','", $new_upload_options)."')") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    $database->database_query("UPDATE se_events INNER JOIN se_users ON se_events.event_user_id=se_users.user_id SET se_events.event_tag='{$new_tag_options[0]}' WHERE se_users.user_level_id='{$level_info['level_id']}' && se_events.event_tag NOT IN('".join("','", $new_tag_options)."')") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    $sql = "SELECT * FROM se_levels WHERE level_id='{$level_info['level_id']}' LIMIT 1";
    $resource = $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    $level_info = $database->database_fetch_assoc($resource);
    
    $result = TRUE;
  }
}



// ADD SPACES BACK AFTER COMMAS
$level_event_photo_exts     = str_replace(",", ", ", $level_info['level_event_photo_exts']);
$level_event_album_exts     = str_replace(",", ", ", $level_info['level_event_album_exts']);
$level_event_album_mimes    = str_replace(",", ", ", $level_info['level_event_album_mimes']);
$level_event_album_maxsize  = $level_info['level_event_album_maxsize'] / 1024;



// GET PREVIOUS PRIVACY SETTINGS
for( $c=7;$c>1;$c-- )
{
  $priv = pow(2, $c)-1;
  $plv = event_privacy_levels($priv);
  if( !$plv ) continue;
  SE_Language::_preload($plv);
  $privacy_options[$priv] = $plv;
}

for( $c=7;$c>=0;$c-- )
{
  $priv = pow(2, $c)-1;
  $plv = event_privacy_levels($priv);
  if( !$plv ) continue;
  SE_Language::_preload($plv);
  $comment_options[$priv] = $plv;
}

for( $c=6;$c>=0;$c-- )
{
  $priv = pow(2, $c)-1;
  $plv = event_privacy_levels($priv);
  if( !$plv ) continue;
  SE_Language::_preload($plv);
  $upload_options[$priv] = $plv;
}

for( $c=7;$c>=0;$c-- )
{
  $priv = pow(2, $c)-1;
  $plv = event_privacy_levels($priv);
  if( !$plv ) continue;
  SE_Language::_preload($plv);
  $tag_options[$priv] = $plv;
}




// ASSIGN VARIABLES AND SHOW USER EVENTS PAGE
$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);

$smarty->assign('level_id', $level_info['level_id']);
$smarty->assign_by_ref('level_info', $level_info);

$smarty->assign('level_event_photo_exts', $level_event_photo_exts);
$smarty->assign('level_event_album_exts', $level_event_album_exts);
$smarty->assign('level_event_album_mimes', $level_event_album_mimes);
$smarty->assign('level_event_album_maxsize', $level_event_album_maxsize);

$smarty->assign_by_ref('level_event_privacy', unserialize($level_info['level_event_privacy']));
$smarty->assign_by_ref('level_event_comments', unserialize($level_info['level_event_comments']));
$smarty->assign_by_ref('level_event_upload', unserialize($level_info['level_event_upload']));
$smarty->assign_by_ref('level_event_tag', unserialize($level_info['level_event_tag']));

$smarty->assign_by_ref('privacy_options', $privacy_options);
$smarty->assign_by_ref('comment_options', $comment_options);
$smarty->assign_by_ref('upload_options', $upload_options);
$smarty->assign_by_ref('tag_options', $tag_options);

include "admin_footer.php";
?>