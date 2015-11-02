<?php

$page = "admin_levels_usersettings";
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
if($task == "dosave") {
  $level_info[level_photo_allow] = $_POST['level_photo_allow'];
  $level_info[level_photo_width] = $_POST['level_photo_width'];
  $level_info[level_photo_height] = $_POST['level_photo_height'];
  $level_info[level_photo_exts] = $_POST['level_photo_exts'];
  $level_info[level_profile_style] = $_POST['level_profile_style'];
  $level_info[level_profile_style_sample] = $_POST['level_profile_style_sample'];
  $level_info[level_profile_status] = $_POST['level_profile_status'];
  $level_info[level_profile_invisible] = $_POST['level_profile_invisible'];
  $level_info[level_profile_views] = $_POST['level_profile_views'];
  $level_info[level_profile_change] = $_POST['level_profile_change'];
  $level_info[level_profile_delete] = $_POST['level_profile_delete'];
  $level_info[level_profile_block] = $_POST['level_profile_block'];
  $level_info[level_profile_search] = $_POST['level_profile_search'];
  $level_info[level_profile_privacy] = is_array($_POST['level_profile_privacy']) ? $_POST['level_profile_privacy'] : Array();
  $level_info[level_profile_comments] = is_array($_POST['level_profile_comments']) ? $_POST['level_profile_comments'] : Array();

  // GET PRIVACY AND PRIVACY DIFFERENCES
  if( empty($level_info[level_profile_privacy]) || !is_array($level_info[level_profile_privacy]) ) $level_info[level_profile_privacy] = array(63);
  rsort($level_info[level_profile_privacy]);
  $new_privacy_options = $level_info[level_profile_privacy];
  $level_info[level_profile_privacy] = serialize($level_info[level_profile_privacy]);

  // GET COMMENT AND COMMENT DIFFERENCES
  if( empty($level_info[level_profile_comments]) || !is_array($level_info[level_profile_comments]) ) $level_info[level_profile_comments] = array(63);
  rsort($level_info[level_profile_comments]);
  $new_comments_options = $level_info[level_profile_comments];
  $level_info[level_profile_comments] = serialize($level_info[level_profile_comments]);
 
  // LOOP THROUGH EXTENSIONS AND CHECK FOR INVALID FILE TYPES
  $extensions = explode(",", str_replace(" ", "", $level_info[level_photo_exts]));
  for($e=0;$e<count($extensions);$e++) {
    if($extensions[$e] != "" && $extensions[$e] != "jpg" && $extensions[$e] != "jpeg" && $extensions[$e] != "gif" && $extensions[$e] != "png") {
      $is_error = 290;
    }
  }

  // CHECK THAT A NUMBER BETWEEN 1 AND 999 WAS ENTERED FOR WIDTH AND HEIGHT
  if(!is_numeric($level_info[level_photo_width]) || !is_numeric($level_info[level_photo_height]) || $level_info[level_photo_width] < 1 || $level_info[level_photo_height] < 1 || $level_info[level_photo_width] > 999 || $level_info[level_photo_height] > 999) {
    $is_error = 291;
  }

  // SAVE SETTINGS IF NO ERROR
  if($is_error == 0) {
    $database->database_query("UPDATE se_levels SET 
			level_profile_search='$level_info[level_profile_search]',
			level_profile_privacy='$level_info[level_profile_privacy]',
			level_profile_block='$level_info[level_profile_block]',
			level_profile_comments='$level_info[level_profile_comments]',
			level_photo_allow='$level_info[level_photo_allow]',
			level_photo_width='$level_info[level_photo_width]',
			level_photo_height='$level_info[level_photo_height]',
			level_photo_exts='$level_info[level_photo_exts]',
			level_profile_style='$level_info[level_profile_style]',
			level_profile_style_sample='$level_info[level_profile_style_sample]',
			level_profile_status='$level_info[level_profile_status]',
			level_profile_invisible='$level_info[level_profile_invisible]',
			level_profile_views='$level_info[level_profile_views]',
			level_profile_change='$level_info[level_profile_change]',
			level_profile_delete='$level_info[level_profile_delete]' WHERE level_id='$level_id'");
    if($level_info[level_profile_search] == 0) { $database->database_query("UPDATE se_users SET user_search='1' WHERE user_level_id='$level_id'"); }
    $database->database_query("UPDATE se_users SET user_privacy='".$new_privacy_options[0]."' WHERE user_level_id='$level_id' && user_privacy NOT IN('".join("','", $new_privacy_options)."')");
    $database->database_query("UPDATE se_users SET user_comments='".$new_comments_options[0]."' WHERE user_level_id='$level_id' && user_comments NOT IN('".join("','", $new_comments_options)."')");
    $result = 1;
  }
}


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


// ASSIGN VARIABLES AND SHOW GENERAL USER SETTINGS PAGE
$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);
$smarty->assign('level_info', $level_info);
$smarty->assign('level_profile_privacy', unserialize($level_info[level_profile_privacy]));
$smarty->assign('level_profile_comments', unserialize($level_info[level_profile_comments]));
$smarty->assign('profile_privacy', $privacy_options);
$smarty->assign('profile_comments', $comment_options);
include "admin_footer.php";
?>