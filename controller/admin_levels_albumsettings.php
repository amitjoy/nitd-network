<?php

$page = "admin_levels_albumsettings";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = "main"; }
if(isset($_POST['level_id'])) { $level_id = $_POST['level_id']; } elseif(isset($_GET['level_id'])) { $level_id = $_GET['level_id']; } else { $level_id = 0; }

// VALIDATE LEVEL ID
$level = $database->database_query("SELECT * FROM se_levels WHERE level_id='$level_id'");
if($database->database_num_rows($level) != 1) { header("Location: admin_levels.php"); exit(); }
$level_info = $database->database_fetch_assoc($level);

// SET RESULT VARIABLE
$result = 0;
$is_error = 0;


// SAVE CHANGES
if($task == "dosave")
{
  $level_info['level_album_allow']    = $_POST['level_album_allow'];
  $level_info['level_album_exts']     = str_replace(", ", ",", $_POST['level_album_exts']);
  $level_info['level_album_mimes']    = str_replace(", ", ",", $_POST['level_album_mimes']);
  $level_info['level_album_storage']  = $_POST['level_album_storage'];
  $level_info['level_album_maxsize']  = $_POST['level_album_maxsize'];
  $level_info['level_album_width']    = $_POST['level_album_width'];
  $level_info['level_album_height']   = $_POST['level_album_height'];
  $level_info['level_album_style']    = $_POST['level_album_style'];
  $level_info['level_album_maxnum']   = $_POST['level_album_maxnum'];
  $level_info['level_album_search']   = $_POST['level_album_search'];
  $level_info['level_album_privacy']  = is_array($_POST['level_album_privacy']) ? $_POST['level_album_privacy'] : Array();
  $level_info['level_album_comments'] = is_array($_POST['level_album_comments']) ? $_POST['level_album_comments'] : Array();
  $level_info['level_album_tag']      = is_array($_POST['level_album_tag']) ? $_POST['level_album_tag'] : Array();
  $level_info['level_album_profile']  = is_array($_POST['level_album_profile']) ? $_POST['level_album_profile'] : Array('tab');

  // IMPLODE PROFILE OPTIONS
  $level_album_profile = $level_info[level_album_profile];
  $level_info[level_album_profile] = implode(",", $level_info[level_album_profile]);

  // GET PRIVACY AND PRIVACY DIFFERENCES
  if( empty($level_info[level_album_privacy]) || !is_array($level_info[level_album_privacy]) ) $level_info[level_album_privacy] = array(63);
  rsort($level_info[level_album_privacy]);
  $new_privacy_options = $level_info[level_album_privacy];
  $level_info[level_album_privacy] = serialize($level_info[level_album_privacy]);

  // GET COMMENT AND COMMENT DIFFERENCES
  if( empty($level_info[level_album_comments]) || !is_array($level_info[level_album_comments]) ) $level_info[level_album_comments] = array(63);
  rsort($level_info[level_album_comments]);
  $new_comments_options = $level_info[level_album_comments];
  $level_info[level_album_comments] = serialize($level_info[level_album_comments]);

  // GET TAG AND TAG DIFFERENCES
  if( empty($level_info[level_album_tag]) || !is_array($level_info[level_album_tag]) ) $level_info[level_album_tag] = array(63);
  rsort($level_info[level_album_tag]);
  $new_tag_options = $level_info[level_album_tag];
  $level_info[level_album_tag] = serialize($level_info[level_album_tag]);

  // CHECK THAT A NUMBER BETWEEN 1 AND 204800 (200MB) WAS ENTERED FOR MAXSIZE
  if(!is_numeric($level_info[level_album_maxsize]) || $level_info[level_album_maxsize] < 1 || $level_info[level_album_maxsize] > 204800)
  {
    $is_error = 1000012;
  }
  
  // CHECK THAT WIDTH AND HEIGHT ARE NUMBERS
  elseif(!is_numeric($level_info[level_album_width]) || !is_numeric($level_info[level_album_height]))
  {
    $is_error = 1000013;
  }
  
  // CHECK THAT MAX ALBUMS IS A NUMBER
  elseif(!is_numeric($level_info[level_album_maxnum]) || $level_info[level_album_maxnum] < 1 || $level_info[level_album_maxnum] > 999)
  {
    $is_error = 1000014;
  }
  
  else
  {
    $level_info[level_album_maxsize] = $level_info[level_album_maxsize]*1024;
    
    $database->database_query("
      UPDATE se_levels SET 
			level_album_search='$level_info[level_album_search]',
			level_album_privacy='$level_info[level_album_privacy]',
			level_album_comments='$level_info[level_album_comments]',
			level_album_tag='$level_info[level_album_tag]',
			level_album_allow='$level_info[level_album_allow]',
			level_album_maxnum='$level_info[level_album_maxnum]',
			level_album_exts='$level_info[level_album_exts]',
			level_album_mimes='$level_info[level_album_mimes]',
			level_album_storage='$level_info[level_album_storage]',
			level_album_maxsize='$level_info[level_album_maxsize]',
			level_album_width='$level_info[level_album_width]',
			level_album_height='$level_info[level_album_height]',
			level_album_style='$level_info[level_album_style]',
			level_album_profile='$level_info[level_album_profile]'
      WHERE level_id='$level_info[level_id]' LIMIT 1
    ");
    
    if( !$level_info['level_album_search'] )
    {
      $database->database_query("UPDATE se_albums INNER JOIN se_users ON se_albums.album_user_id=se_users.user_id SET se_albums.album_search='1' WHERE se_users.user_level_id='{$level_info['level_id']}'") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    }
    
    $database->database_query("UPDATE se_users SET user_profile_album='{$level_album_profile[0]}' WHERE user_level_id='{$level_info['level_id']}' AND user_profile_album NOT IN('".join("','", $level_album_profile)."')") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    $database->database_query("UPDATE se_albums INNER JOIN se_users ON se_users.user_id=se_albums.album_user_id SET se_albums.album_privacy='{$new_privacy_options[0]}' WHERE se_users.user_level_id='{$level_info['level_id']}' AND se_albums.album_privacy NOT IN('".join("','", $new_privacy_options)."')") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    $database->database_query("UPDATE se_albums INNER JOIN se_users ON se_users.user_id=se_albums.album_user_id SET se_albums.album_comments='{$new_comments_options[0]}' WHERE se_users.user_level_id='{$level_info['level_id']}' AND se_albums.album_comments NOT IN('".join("','", $new_comments_options)."')") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    $database->database_query("UPDATE se_albums INNER JOIN se_users ON se_users.user_id=se_albums.album_user_id SET se_albums.album_tag='{$new_tag_options[0]}' WHERE se_users.user_level_id='{$level_info['level_id']}' AND se_albums.album_tag NOT IN('".join("','", $new_tag_options)."')") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    $result = 1;
  }
} // END DOSAVE TASK



// ADD SPACES AFTER COMMAS
$level_info[level_album_exts] = str_replace(",", ", ", $level_info[level_album_exts]);
$level_info[level_album_mimes] = str_replace(",", ", ", $level_info[level_album_mimes]);
$level_info[level_album_maxsize] = $level_info[level_album_maxsize]/1024;

// GET PREVIOUS PRIVACY SETTINGS
for($c=6;$c>0;$c--) {
  $priv = pow(2, $c)-1;
  if(user_privacy_levels($priv) != "") {
    SE_Language::_preload(user_privacy_levels($priv));
    $privacy_options[$priv] = user_privacy_levels($priv);
  }
}

for($c=6;$c>=0;$c--) {
  $priv = pow(2, $c)-1;
  if(user_privacy_levels($priv) != "") {
    SE_Language::_preload(user_privacy_levels($priv));
    $comment_options[$priv] = user_privacy_levels($priv);
  }
}

for($c=6;$c>=0;$c--) {
  $priv = pow(2, $c)-1;
  if(user_privacy_levels($priv) != "") {
    SE_Language::_preload(user_privacy_levels($priv));
    $tag_options[$priv] = user_privacy_levels($priv);
  }
}


// ASSIGN VARIABLES AND SHOW ALBUM SETTINGS PAGE
$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);
$smarty->assign('level_info', $level_info);
$smarty->assign('level_album_privacy', unserialize($level_info[level_album_privacy]));
$smarty->assign('level_album_comments', unserialize($level_info[level_album_comments]));
$smarty->assign('level_album_tag', unserialize($level_info[level_album_tag]));
$smarty->assign('level_album_profile', explode(",", $level_info[level_album_profile]));
$smarty->assign('album_privacy', $privacy_options);
$smarty->assign('album_comments', $comment_options);
$smarty->assign('album_tag', $tag_options);
include "admin_footer.php";
?>