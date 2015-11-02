<?php

$page = "admin_levels_pollsettings";
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

$level_info = $database->database_fetch_assoc($level);



// SET RESULT AND ERROR VARS
$result = 0;
$is_error = 0;

// SAVE CHANGES
if($task == "dosave")
{
  $level_poll_allow     = ( !empty($_POST['level_poll_allow'])      ? $_POST['level_poll_allow']    : NULL    );
  $level_poll_entries   = ( !empty($_POST['level_poll_entries'])    ? $_POST['level_poll_entries']  : NULL    );
  $level_poll_search    = ( !empty($_POST['level_poll_search'])     ? $_POST['level_poll_search']   : NULL    );
  $level_poll_privacy   = ( is_array($_POST['level_poll_privacy'])  ? $_POST['level_poll_privacy']  : array() );
  $level_poll_comments  = ( is_array($_POST['level_poll_comments']) ? $_POST['level_poll_comments'] : array() );
  
  
  // CHECK THAT A NUMBER BETWEEN 1 AND 999 WAS ENTERED FOR poll ENTRIES
  if( !is_numeric($level_poll_entries) || $level_poll_entries < 1 || $level_poll_entries > 999 )
    $is_error = 2500089;
  
  if( !$is_error )
  {
    // GET PRIVACY AND PRIVACY DIFFERENCES
    if( empty($level_poll_privacy) || !is_array($level_poll_privacy) ) $level_poll_privacy = array(63);
    rsort($level_poll_privacy);
    $new_privacy_options = $level_poll_privacy;
    $level_poll_privacy = serialize($level_poll_privacy);
    
    // GET COMMENT AND COMMENT DIFFERENCES
    if( empty($level_poll_comments) || !is_array($level_poll_comments) ) $level_poll_comments = array(63);
    rsort($level_poll_comments);
    $new_comments_options = $level_poll_comments;
    $level_poll_comments = serialize($level_poll_comments);
    
    
    // SAVE SETTINGS
    $level_poll_album_maxsize = $level_poll_album_maxsize * 1024;
    
    $sql = "
      UPDATE
        se_levels
      SET 
        level_poll_search='$level_poll_search',
        level_poll_privacy='$level_poll_privacy',
        level_poll_comments='$level_poll_comments',
        level_poll_allow='$level_poll_allow',
        level_poll_entries='$level_poll_entries'
      WHERE
        level_id='{$level_info['level_id']}'
      LIMIT
        1
    ";
    
    $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
    
    if( !$level_poll_search )
    {
      $database->database_query("UPDATE se_polls INNER JOIN se_users ON se_users.user_id=se_polls.poll_user_id SET se_polls.poll_search='1' WHERE se_users.user_level_id='{$level_info['level_id']}'") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    }
    
    $database->database_query("UPDATE se_polls INNER JOIN se_users ON se_users.user_id=se_polls.poll_user_id SET poll_privacy='{$new_privacy_options[0]}' WHERE se_users.user_level_id='{$level_info['level_id']}' && se_polls.poll_privacy NOT IN('".join("','", $new_privacy_options)."')") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    $database->database_query("UPDATE se_polls INNER JOIN se_users ON se_users.user_id=se_polls.poll_user_id SET poll_comments='{$new_comments_options[0]}' WHERE se_users.user_level_id='{$level_info['level_id']}' && se_polls.poll_comments NOT IN('".join("','", $new_comments_options)."')") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    $level_info = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_levels WHERE level_id='{$level_info['level_id']}'"));
    $result = 1;
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


// ASSIGN VARIABLES AND SHOW poll SETTINGS PAGE
$smarty->assign_by_ref('level_info', $level_info);

$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);
$smarty->assign('level_id', $level_info['level_id']);
$smarty->assign('level_name', $level_info['level_name']);
$smarty->assign('entries_value', $level_info['level_poll_entries']);
$smarty->assign('poll_search', $level_info['level_poll_search']);
$smarty->assign('poll_privacy', unserialize($level_info['level_poll_privacy']));
$smarty->assign('poll_comments', unserialize($level_info['level_poll_comments']));
$smarty->assign('privacy_options', $privacy_options);
$smarty->assign('comment_options', $comment_options);
include "admin_footer.php";
?>