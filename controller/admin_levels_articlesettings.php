<?
$page = "admin_levels_articlesettings";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = "main"; }
if(isset($_POST['level_id'])) { $level_id = $_POST['level_id']; } elseif(isset($_GET['level_id'])) { $level_id = $_GET['level_id']; } else { $level_id = 0; }

// VALIDATE LEVEL ID
$level = $database->database_query("SELECT * FROM se_levels WHERE level_id='$level_id'");
if($database->database_num_rows($level) != 1) { 
  header("Location: admin_levels.php");
  exit();
}
$level_info = $database->database_fetch_assoc($level);


// SET RESULT AND ERROR VARS
$result = 0;
$is_error = 0;




// SAVE CHANGES
if($task == "dosave") {
  $level_article_allow = $_POST['level_article_allow'];
  $level_article_photo = $_POST['level_article_photo'];
  $level_article_photo_width = $_POST['level_article_photo_width'];
  $level_article_photo_height = $_POST['level_article_photo_height'];
  $level_article_photo_exts = str_replace(", ", ",", $_POST['level_article_photo_exts']);
  $level_article_approved = $_POST['level_article_approved'];
  $level_article_album_exts = str_replace(", ", ",", $_POST['level_article_album_exts']);
  $level_article_album_mimes = str_replace(", ", ",", $_POST['level_article_album_mimes']);
  $level_article_album_storage = $_POST['level_article_album_storage'];
  $level_article_album_maxsize = $_POST['level_article_album_maxsize'];
  $level_article_album_width = $_POST['level_article_album_width'];
  $level_article_album_height = $_POST['level_article_album_height'];
  $level_article_search = $_POST['level_article_search'];

  
  $level_info[level_article_privacy] = is_array($_POST['level_article_privacy']) ? $_POST['level_article_privacy'] : Array(0);
  $level_info[level_article_comments] = is_array($_POST['level_article_comments']) ? $_POST['level_article_comments'] : Array(0);  
  // GET PRIVACY AND PRIVACY DIFFERENCES
  sort($level_info[level_article_privacy]);
  $new_privacy_options = $level_info[level_article_privacy];
  $level_info[level_article_privacy] = serialize($level_info[level_article_privacy]);

  // GET COMMENT AND COMMENT DIFFERENCES
  sort($level_info[level_article_comments]);
  $new_comments_options = $level_info[level_article_comments];
  $level_info[level_article_comments] = serialize($level_info[level_article_comments]);
  
  // CHECK THAT A NUMBER BETWEEN 1 AND 999 WAS ENTERED FOR WIDTH AND HEIGHT
  if(!is_numeric($level_article_photo_width) OR !is_numeric($level_article_photo_height) OR $level_article_photo_width < 1 OR $level_article_photo_height < 1 OR $level_article_photo_width > 999 OR $level_article_photo_height > 999) {
    $is_error = 1;
    $error_message = 11150348;
  }

  // CHECK THAT A NUMBER BETWEEN 1 AND 204800 (200MB) WAS ENTERED FOR MAXSIZE
  if(!is_numeric($level_article_album_maxsize) OR $level_article_album_maxsize < 1 OR $level_article_album_maxsize > 204800) {
    $is_error = 1;
    $error_message = 11150349;
  }

  // CHECK THAT WIDTH AND HEIGHT ARE NUMBERS
  if(!is_numeric($level_article_album_width) OR !is_numeric($level_article_album_height)) {
    $is_error = 1;
    $error_message = 11150350;
  }

  // IF THERE WERE NO ERRORS, SAVE CHANGES
  if($is_error == 0) {

    // SAVE OTHER SETTINGS
    $level_article_album_maxsize = $level_article_album_maxsize*1024;
    $database->database_query("UPDATE se_levels SET 
			level_article_search='$level_article_search',
			level_article_comments='$level_info[level_article_comments]',
			level_article_privacy='$level_info[level_article_privacy]',
			level_article_allow='$level_article_allow',
			level_article_photo='$level_article_photo',
			level_article_photo_width='$level_article_photo_width',
			level_article_photo_height='$level_article_photo_height',
			level_article_photo_exts='$level_article_photo_exts',
			
			level_article_approved='$level_article_approved',
			level_article_album_exts='$level_article_album_exts',
			level_article_album_mimes='$level_article_album_mimes',
			level_article_album_storage='$level_article_album_storage',
			level_article_album_maxsize='$level_article_album_maxsize',
			level_article_album_width='$level_article_album_width',
			level_article_album_height='$level_article_album_height' WHERE level_id='$level_info[level_id]'");
    if($level_article_search == 0) { $database->database_query("UPDATE se_articles, se_users SET article_search='1' WHERE se_users.user_level_id='$level_info[level_id]' AND se_articles.article_user_id=se_users.user_id"); }
    
    $database->database_query("UPDATE se_articles, se_users SET se_articles.article_privacy='".$new_privacy_options[0]."' WHERE se_users.user_level_id='$level_info[level_id]' AND se_articles.article_privacy NOT IN('".join("','", $new_privacy_options)."')");
    $database->database_query("UPDATE se_articles, se_users SET se_articles.article_comments='".$new_comments_options[0]."' WHERE se_users.user_level_id='$level_info[level_id]' AND se_articles.article_comments NOT IN('".join("','", $new_comments_options)."')");
    
    
    $level_info = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_levels WHERE level_id='$level_info[level_id]'"));
    $result = 1;
  }
}








// ADD SPACES BACK AFTER COMMAS
$level_article_photo_exts = str_replace(",", ", ", $level_info[level_article_photo_exts]);
$level_article_album_exts = str_replace(",", ", ", $level_info[level_article_album_exts]);
$level_article_album_mimes = str_replace(",", ", ", $level_info[level_article_album_mimes]);
$level_article_album_maxsize = $level_info[level_article_album_maxsize]/1024;

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


// ASSIGN VARIABLES AND SHOW USER EVENTS PAGE
$smarty->assign('level_info', $level_info);

$smarty->assign('level_id', $level_info[level_id]);
$smarty->assign('level_name', $level_info[level_name]);
$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);
$smarty->assign('error_message', $error_message);
$smarty->assign('article_allow', $level_info[level_article_allow]);
$smarty->assign('article_photo', $level_info[level_article_photo]);
$smarty->assign('article_photo_width', $level_info[level_article_photo_width]);
$smarty->assign('article_photo_height', $level_info[level_article_photo_height]);
$smarty->assign('article_photo_exts', $level_article_photo_exts);
//$smarty->assign('article_inviteonly', $level_info[level_article_inviteonly]);
$smarty->assign('article_approved', $level_info[level_article_approved]);
$smarty->assign('article_album_exts', $level_article_album_exts);
$smarty->assign('article_album_mimes', $level_article_album_mimes);
$smarty->assign('article_album_storage', $level_info[level_article_album_storage]);
$smarty->assign('article_album_maxsize', $level_article_album_maxsize);
$smarty->assign('article_album_width', $level_info[level_article_album_width]);
$smarty->assign('article_album_height', $level_info[level_article_album_height]);
$smarty->assign('article_search', $level_info[level_article_search]);
$smarty->assign('article_privacy', $privacy_options);
$smarty->assign('article_comments', $comment_options);
$smarty->assign('level_article_privacy', unserialize($level_info[level_article_privacy]));
$smarty->assign('level_article_comments', unserialize($level_info[level_article_comments]));
include "admin_footer.php";
