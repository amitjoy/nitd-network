<?php

$page = "admin_chat";
include "admin_header.php";

$task = ( !empty($_POST['task']) ? $_POST['task'] : ( !empty($_GET['task']) ? $_GET['task'] : 'main' ) );


// SET RESULT VARIABLE
$result = 0;
$chatuser_kicked = "";


if( $task=="kick" )
{
  $chatuser_id = $_GET['chatuser_id'];
  $chatuser_query = $database->database_query("SELECT se_chatusers.chatuser_user_username, se_users.user_id FROM se_chatusers LEFT JOIN se_users ON se_chatusers.chatuser_user_id=se_users.user_id WHERE se_chatusers.chatuser_id='$chatuser_id' LIMIT 1");
  if($database->database_num_rows($chatuser_query) != 1) {
    header("Location: admin_chat.php"); exit;
  } else {
    $chatuser_info = $database->database_fetch_assoc($chatuser_query);
  }
  $nowtime = time() + 300;
  $database->database_query("DELETE FROM se_chatusers WHERE chatuser_id='$chatuser_id' LIMIT 1");
  $database->database_query("INSERT INTO se_chatbans (chatbans_user_id, chatbans_bandate) VALUES ('$chatuser_info[user_id]', '$nowtime')");
  $chatuser_kicked = $chatuser_info[chatuser_user_username];
  $result = 2;
}



// SAVE CHANGES
if($task == "dosave")
{
  $setting_chat_enabled     = $_POST['setting_chat_enabled'];
  $setting_chat_update      = $_POST['setting_chat_update'];
  $setting_chat_showphotos  = $_POST['setting_chat_showphotos'];
  $setting_im_enabled       = $_POST['setting_im_enabled'];
  $setting_im_html          = $_POST['setting_im_html'];
  
  // Check update timeout
  if( !is_numeric($setting_chat_update) || $setting_chat_update>20000 || $setting_chat_update<1500 )
    $setting_chat_update = 2000;
  
  $setting_im_html  = str_replace(" ", "", $setting_im_html);
  
  $sql = "
    UPDATE
      se_settings
    SET 
      setting_chat_enabled='$setting_chat_enabled',
      setting_chat_update='$setting_chat_update',
      setting_chat_showphotos='$setting_chat_showphotos',
      setting_im_enabled='$setting_im_enabled',
      setting_im_html='$setting_im_html'
  ";
  
  $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  
  
  $chatbanned_reset = $database->database_query("DELETE FROM se_chatbans WHERE chatban_bandate='0'");
  $chatbanned = str_replace(" ", "", $_POST['chatbanned']);
  $chatbanned = explode(",", $chatbanned);
  $chatbanned_count = 0;
  while($chatbanned_count <= count($chatbanned)) {
    $chatbanned_username = $chatbanned[$chatbanned_count];
    $thisbanned_query = $database->database_query("SELECT user_id FROM se_users WHERE user_username='$chatbanned_username' LIMIT 1");
    if($database->database_num_rows($thisbanned_query) == 1) {
      $thisbanned_info = $database->database_fetch_assoc($thisbanned_query);
      $database->database_query("INSERT INTO se_chatbans (chatban_user_id, chatban_bandate) VALUES ('$thisbanned_info[user_id]', '0')");
    }
    $chatbanned_count++;
  }

  $setting = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_settings LIMIT 1"));
  $result = 1;
}



// GET LIST OF USERS CURRENTLY CHATTING (ONLY GET USERS THAT HAVE UPDATED WITHIN THE LAST 15 SECONDS)
$updatetime = time() - 15;
$chatusers = $database->database_query("SELECT chatuser_id, chatuser_user_username FROM se_chatusers WHERE chatuser_lastupdate>'$updatetime' ORDER BY chatuser_id DESC");
$chatusers_array = Array();
$chatuser_count = 0;
while($chatuser = $database->database_fetch_assoc($chatusers)) {
  $chatusers_array[$chatuser_count] = Array('chatuser_id' => $chatuser[chatuser_id],
					    'chatuser_user_username' => $chatuser[chatuser_user_username]);
  $chatuser_count++;
}


// GET LIST OF USERNAMES BANNED FROM CHAT
$chatbanneds = $database->database_query("SELECT se_users.user_username, se_chatbans.chatban_user_id FROM se_chatbans LEFT JOIN se_users ON se_users.user_id=se_chatbans.chatban_user_id WHERE se_chatbans.chatban_bandate='0' ORDER BY se_chatbans.chatban_id ASC");
$chatbanneds_array = Array();
$chatbanned_count = 0;
while($chatbanned = $database->database_fetch_assoc($chatbanneds)) {
  $chatbanneds_array[$chatbanned_count] = Array('chatbanned_id' => $chatbanned[chatban_id],
					    'chatbanned_user_username' => $chatbanned[user_username]);
  $chatbanned_count++;
}


// ASSIGN VARIABLES AND SHOW GENERAL SETTINGS PAGE
$smarty->assign('result', $result);
$smarty->assign('chatusers', $chatusers_array);
$smarty->assign('chatuser_kicked', $chatuser_kicked);
$smarty->assign('chatbanned', $chatbanneds_array);
include "admin_footer.php";
?>