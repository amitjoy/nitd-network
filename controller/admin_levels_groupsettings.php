<?php

$page = "admin_levels_groupsettings";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = "main"; }
if(isset($_POST['level_id'])) { $level_id = $_POST['level_id']; } elseif(isset($_GET['level_id'])) { $level_id = $_GET['level_id']; } else { $level_id = 0; }

// VALIDATE LEVEL ID
$level = $database->database_query("SELECT * FROM se_levels WHERE level_id='$level_id'");
if($database->database_num_rows($level) != 1) { header("Location: admin_levels.php"); exit(); }
$level_info = $database->database_fetch_assoc($level);


// SET RESULT AND ERROR VARS
$result = 0;
$is_error = 0;




// SAVE CHANGES
if($task == "dosave")
{
  $level_info[level_group_allow] = $_POST['level_group_allow'];
  $level_info[level_group_photo] = $_POST['level_group_photo'];
  $level_info[level_group_photo_width] = $_POST['level_group_photo_width'];
  $level_info[level_group_photo_height] = $_POST['level_group_photo_height'];
  $level_info[level_group_photo_exts] = str_replace(", ", ",", $_POST['level_group_photo_exts']);
  $level_info[level_group_titles] = $_POST['level_group_titles'];
  $level_info[level_group_officers] = $_POST['level_group_officers'];
  $level_info[level_group_approval] = $_POST['level_group_approval'];
  $level_info[level_group_style] = $_POST['level_group_style'];
  $level_info[level_group_album_exts] = str_replace(", ", ",", $_POST['level_group_album_exts']);
  $level_info[level_group_album_mimes] = str_replace(", ", ",", $_POST['level_group_album_mimes']);
  $level_info[level_group_album_storage] = $_POST['level_group_album_storage'];
  $level_info[level_group_album_maxsize] = $_POST['level_group_album_maxsize'];
  $level_info[level_group_album_width] = $_POST['level_group_album_width'];
  $level_info[level_group_album_height] = $_POST['level_group_album_height'];
  $level_info[level_group_maxnum] = $_POST['level_group_maxnum'];
  $level_info[level_group_search] = $_POST['level_group_search'];
  $level_info[level_group_privacy] = is_array($_POST['level_group_privacy']) ? $_POST['level_group_privacy'] : Array();
  $level_info[level_group_comments] = is_array($_POST['level_group_comments']) ? $_POST['level_group_comments'] : Array();
  $level_info[level_group_discussion] = is_array($_POST['level_group_discussion']) ? $_POST['level_group_discussion'] : Array();
  $level_info[level_group_upload] = is_array($_POST['level_group_upload']) ? $_POST['level_group_upload'] : Array();
  $level_info[level_group_tag] = is_array($_POST['level_group_tag']) ? $_POST['level_group_tag'] : Array();

  // CHECK THAT A NUMBER BETWEEN 1 AND 999 WAS ENTERED FOR WIDTH AND HEIGHT
  if(!is_numeric($level_info[level_group_photo_width]) || !is_numeric($level_info[level_group_photo_height]) || $level_info[level_group_photo_width] < 1 || $level_info[level_group_photo_height] < 1 || $level_info[level_group_photo_width] > 999 || $level_info[level_group_photo_height] > 999) { $is_error = 2000019; }

  // CHECK THAT A NUMBER BETWEEN 1 AND 204800 (200MB) WAS ENTERED FOR MAXSIZE
  if(!is_numeric($level_info[level_group_album_maxsize]) || $level_info[level_group_album_maxsize] < 1 || $level_info[level_group_album_maxsize] > 204800) { $is_error = 2000020; }

  // CHECK THAT WIDTH AND HEIGHT ARE NUMBERS
  if(!is_numeric($level_info[level_group_album_width]) || !is_numeric($level_info[level_group_album_height])) { $is_error = 2000021; }

  // CHECK THAT MAX GROUPS IS A NUMBER
  if(!is_numeric($level_info[level_group_maxnum]) || $level_info[level_group_maxnum] < 1 || $level_info[level_group_maxnum] > 999) { $is_error = 2000022; }

  // IF THERE WERE NO ERRORS, SAVE CHANGES
  if($is_error == 0)
  {
    // IF ALLOW OFFICERS WAS SET FROM YES TO NO, DEMOTE ALL OFFICERS TO MEMBERS
    if($level_info[level_group_officers] == 0) { $database->database_query("UPDATE se_groupmembers SET groupmember_rank='0' WHERE groupmember_rank='1'"); }

    // GET PRIVACY AND PRIVACY DIFFERENCES
    if( empty($level_info[level_group_privacy]) || !is_array($level_info[level_group_privacy]) ) $level_info[level_group_privacy] = array(255);
    rsort($level_info[level_group_privacy]);
    $new_privacy_options = $level_info[level_group_privacy];
    $level_info[level_group_privacy] = serialize($level_info[level_group_privacy]);

    // GET COMMENT AND COMMENT DIFFERENCES
    if( empty($level_info[level_group_comments]) || !is_array($level_info[level_group_comments]) ) $level_info[level_group_comments] = array(255);
    rsort($level_info[level_group_comments]);
    $new_comments_options = $level_info[level_group_comments];
    $level_info[level_group_comments] = serialize($level_info[level_group_comments]);

    // GET DISCUSSION AND DISCUSSION DIFFERENCES
    if( empty($level_info[level_group_discussion]) || !is_array($level_info[level_group_discussion]) ) $level_info[level_group_discussion] = array(255);
    rsort($level_info[level_group_discussion]);
    $new_discussion_options = $level_info[level_group_discussion];
    $level_info[level_group_discussion] = serialize($level_info[level_group_discussion]);

    // GET UPLOAD AND UPLOAD DIFFERENCES
    if( empty($level_info[level_group_upload]) || !is_array($level_info[level_group_upload]) ) $level_info[level_group_upload] = array(127);
    rsort($level_info[level_group_upload]);
    $new_upload_options = $level_info[level_group_upload];
    $level_info[level_group_upload] = serialize($level_info[level_group_upload]);

    // GET TAG AND TAG DIFFERENCES
    if( empty($level_info[level_group_tag]) || !is_array($level_info[level_group_tag]) ) $level_info[level_group_tag] = array(127);
    rsort($level_info[level_group_tag]);
    $new_tag_options = $level_info[level_group_tag];
    $level_info[level_group_tag] = serialize($level_info[level_group_tag]);

    // SAVE OTHER SETTINGS
    $level_info[level_group_album_maxsize] = $level_info[level_group_album_maxsize]*1024;
    $database->database_query("UPDATE se_levels SET 
			level_group_search='$level_info[level_group_search]',
			level_group_discussion='$level_info[level_group_discussion]',
			level_group_comments='$level_info[level_group_comments]',
			level_group_privacy='$level_info[level_group_privacy]',
			level_group_upload='$level_info[level_group_upload]',
			level_group_tag='$level_info[level_group_tag]',
			level_group_allow='$level_info[level_group_allow]',
			level_group_photo='$level_info[level_group_photo]',
			level_group_photo_width='$level_info[level_group_photo_width]',
			level_group_photo_height='$level_info[level_group_photo_height]',
			level_group_photo_exts='$level_info[level_group_photo_exts]',
			level_group_titles='$level_info[level_group_titles]',
			level_group_officers='$level_info[level_group_officers]',
			level_group_approval='$level_info[level_group_approval]',
			level_group_style='$level_info[level_group_style]',
			level_group_album_exts='$level_info[level_group_album_exts]',
			level_group_album_mimes='$level_info[level_group_album_mimes]',
			level_group_album_storage='$level_info[level_group_album_storage]',
			level_group_album_maxsize='$level_info[level_group_album_maxsize]',
			level_group_album_width='$level_info[level_group_album_width]',
			level_group_album_height='$level_info[level_group_album_height]',
			level_group_maxnum='$level_info[level_group_maxnum]' WHERE level_id='{$level_info['level_id']}'
    ");
    
    if( !$level_info['level_group_search'] )
    {
      $database->database_query("UPDATE se_groups INNER JOIN se_users ON se_users.user_id=se_groups.group_user_id SET se_groups.group_search='1' WHERE se_users.user_level_id='{$level_info['level_id']}' AND se_groups.group_user_id=se_users.user_id") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    }
    
    $database->database_query("UPDATE se_groups INNER JOIN se_users ON se_users.user_id=se_groups.group_user_id SET se_groups.group_privacy='{$new_privacy_options[0]}' WHERE se_users.user_level_id='{$level_info['level_id']}' && se_groups.group_privacy NOT IN('".join("','", $new_privacy_options)."')") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    $database->database_query("UPDATE se_groups INNER JOIN se_users ON se_users.user_id=se_groups.group_user_id SET se_groups.group_comments='{$new_comments_options[0]}' WHERE se_users.user_level_id='{$level_info['level_id']}' && se_groups.group_comments NOT IN('".join("','", $new_comments_options)."')") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    $database->database_query("UPDATE se_groups INNER JOIN se_users ON se_users.user_id=se_groups.group_user_id SET se_groups.group_discussion='{$new_discussion_options[0]}' WHERE se_users.user_level_id='{$level_info['level_id']}' && se_groups.group_discussion NOT IN('".join("','", $new_discussion_options)."')") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    $database->database_query("UPDATE se_groups INNER JOIN se_users ON se_users.user_id=se_groups.group_user_id SET se_groups.group_upload='{$new_upload_options[0]}' WHERE se_users.user_level_id='{$level_info['level_id']}' && se_groups.group_upload NOT IN('".join("','", $new_upload_options)."')") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    $database->database_query("UPDATE se_groupalbums INNER JOIN se_groups ON se_groups.group_id=se_groupalbums.groupalbum_group_id INNER JOIN se_users ON se_users.user_id=se_groups.group_user_id SET se_groupalbums.groupalbum_tag='{$new_tag_options[0]}' WHERE se_users.user_level_id='{$level_info['level_id']}' && groupalbum_tag NOT IN('".join("','", $new_tag_options)."')") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    $result = 1;
  }
}








// ADD SPACES BACK AFTER COMMAS
$level_info[level_group_photo_exts] = str_replace(",", ", ", $level_info[level_group_photo_exts]);
$level_info[level_group_album_exts] = str_replace(",", ", ", $level_info[level_group_album_exts]);
$level_info[level_group_album_mimes] = str_replace(",", ", ", $level_info[level_group_album_mimes]);
$level_info[level_group_album_maxsize] = $level_info[level_group_album_maxsize]/1024;

// GET PREVIOUS PRIVACY SETTINGS
for($c=8;$c>2;$c--) {
  $priv = pow(2, $c)-1;
  if(group_privacy_levels($priv) != "") {
    SE_Language::_preload(group_privacy_levels($priv));
    $privacy_options[$priv] = group_privacy_levels($priv);
  }
}

for($c=8;$c>=0;$c--) {
  $priv = pow(2, $c)-1;
  if(group_privacy_levels($priv) != "") {
    SE_Language::_preload(group_privacy_levels($priv));
    $comment_options[$priv] = group_privacy_levels($priv);
  }
}

for($c=7;$c>=0;$c--) {
  $priv = pow(2, $c)-1;
  if(group_privacy_levels($priv) != "") {
    SE_Language::_preload(group_privacy_levels($priv));
    $upload_options[$priv] = group_privacy_levels($priv);
  }
}





// ASSIGN VARIABLES AND SHOW USER GROUPS PAGE
$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);
$smarty->assign('level_info', $level_info);
$smarty->assign('level_group_privacy', unserialize($level_info[level_group_privacy]));
$smarty->assign('level_group_comments', unserialize($level_info[level_group_comments]));
$smarty->assign('level_group_discussion', unserialize($level_info[level_group_discussion]));
$smarty->assign('level_group_upload', unserialize($level_info[level_group_upload]));
$smarty->assign('level_group_tag', unserialize($level_info[level_group_tag]));
$smarty->assign('group_privacy', $privacy_options);
$smarty->assign('group_comments', $comment_options);
$smarty->assign('group_discussion', $comment_options);
$smarty->assign('group_upload', $upload_options);
$smarty->assign('group_tag', $comment_options);
include "admin_footer.php";
?>