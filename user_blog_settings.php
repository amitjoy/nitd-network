<?php

$page = "user_blog_settings";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }


// SET VARS
$result = 0;


// SAVE NEW CSS
if($task == "dosave")
{
  $style_blog = addslashes(str_replace("-moz-binding", "", strip_tags(htmlspecialchars_decode($_POST['style_blog'], ENT_QUOTES))));
  $user->usersetting_info['usersetting_notify_blogcomment'] = $usersetting_notify_blogcomment = $_POST['usersetting_notify_blogcomment'];
  $user->usersetting_info['usersetting_notify_blogtrackback'] = $usersetting_notify_blogtrackback = $_POST['usersetting_notify_blogtrackback'];
  $user->usersetting_info['usersetting_notify_newblogsubscriptionentry'] = $usersetting_notify_newblogsubscriptionentry = $_POST['usersetting_notify_newblogsubscriptionentry'];
  
  // STYLES
  $sql = "UPDATE se_blogstyles SET blogstyle_css='{$style_blog}' WHERE blogstyle_user_id='{$user->user_info['user_id']}'";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  // USERSETTINGS
  $sql = "
    UPDATE
      se_usersettings
    SET
      usersetting_notify_blogcomment='$usersetting_notify_blogcomment',
      usersetting_notify_blogtrackback='$usersetting_notify_blogtrackback',
      usersetting_notify_newblogsubscriptionentry='$usersetting_notify_newblogsubscriptionentry'
    WHERE
      usersetting_user_id='{$user->user_info['user_id']}'
  ";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  
  $user->user_lastupdate();
  $result = 1;
}



// GET THIS USER'S BLOG CSS
$style_query = $database->database_query("SELECT blogstyle_css FROM se_blogstyles WHERE blogstyle_user_id='{$user->user_info['user_id']}' LIMIT 1");
if($database->database_num_rows($style_query) == 1) { 
  $style_info = $database->database_fetch_assoc($style_query); 
} else {
  $database->database_query("INSERT INTO se_blogstyles (blogstyle_user_id, blogstyle_css) VALUES ('{$user->user_info['user_id']}', '')");
  $style_info = $database->database_fetch_assoc($database->database_query("SELECT blogstyle_css FROM se_blogstyles WHERE blogstyle_user_id='{$user->user_info['user_id']}' LIMIT 1")); 
}


// ASSIGN USER SETTINGS
$user->user_settings();

// ASSIGN SMARTY VARIABLES AND DISPLAY BLOG STYLE PAGE
$smarty->assign('style_blog', htmlspecialchars($style_info['blogstyle_css'], ENT_QUOTES, 'UTF-8'));
$smarty->assign('result', $result);
include "footer.php";
?>