<?php

ob_start();
$page = "chat_frame";
include "header.php";

if(isset($_GET['task'])) { $task = $_GET['task']; } elseif(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = ""; }


// BLANK TASK
if($task == "blank") { exit(); }



// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN
if( !$user->user_exists || !$user->level_info['level_chat_allow'] )
{
  $f_res = '';
  switch( $task ) {
    case 'chat_update': $f_res = 'dropped';
    case 'chat_login': $f_res = 'fail';
  }
  
  // OUTPUT RESULT
  ob_end_clean();
  echo $f_res;
  exit();
}




// UPDATE CHAT
// THIS TASK, WHEN CALLED BY AJAX, OUTPUTS AN IMPLODED FOUR-PART ARRAY
// THE FIRST PART IS A RESULT CODE WITH ANY OF THE FOLLOWING VALUES:
//    success - UPDATE WAS SUCCESSFUL
//    dropped - USER HAS BEEN KICKED OR CHATROOM HAS BEEN DISABLED BY ADMIN
// THE SECOND PART CONTAINS ANY NEW MESSAGES (HTML) THAT NEED TO BE ADDED TO THIS USER'S VIEW OF THE CHAT
// THE THIRD PART CONTAINS A LIST OF ONLINE USERS (HTML)
// THE FOURTH PART CONTAINS THE NUMBER OF ONLINE USERS

if($task == "chat_update")
{
  SE_Language::_preload(3500024);
  SE_Language::load();
  
  // GET TIME OF LAST UPDATE
  $lastupdate = $_COOKIE['se_chat_lastupdate'];

  // PREPARE OUTPUT ARRAY AND RESULT CODE VARIABLE
  $update_result = "success";
  $chat_message = "";
  $chat_onlineusers = "";
  $chat_onlineusers_num = 0;

  // CHECK IF CHATROOM IS DISABLED
  if( !$setting['setting_chat_enabled'] )
  {
    $update_result = "dropped";
  }
  
  // CHAT ROOM IS ENABLED, CONTINUE WITH UPDATE
  else
  {
    // LOGOUT USERS FROM CHAT THAT HAVN'T UPDATED IN 15 SECONDS
    $time_logout = time() - 15;
    $logged_out = $database->database_query("SELECT chatuser_user_username, chatmessage_user_displayname FROM se_chatusers WHERE chatuser_lastupdate<'$time_logout' AND chatuser_user_id<>'".$user->user_info[user_id]."'");
    if($database->database_num_rows($logged_out) > 0)
    {
      // INSERT LOGOUT MESSAGE
      while($logged_out_info = $database->database_fetch_assoc($logged_out))
      {
        $message_username = "";
        $message_welcome = sprintf(SE_Language::get(3500024), $logged_out_info['chatmessage_user_displayname']);
        $database->database_query("INSERT INTO se_chatmessages (chatmessage_time, chatmessage_user_username, chatmessage_content) VALUES ('".time()."', '$message_username', '$message_welcome')");
      }
      $database->database_query("DELETE FROM se_chatusers WHERE chatuser_lastupdate<'$time_logout' AND chatuser_user_id!='".$user->user_info['user_id']."'");
    }
    
    // GET CHAT MESSAGES
    $messages = $database->database_query("SELECT chatmessage_id, chatmessage_time, chatmessage_user_username, chatmessage_user_displayname, chatmessage_content FROM se_chatmessages WHERE chatmessage_user_username!='{$user->user_info['user_username']}' AND chatmessage_time>='$lastupdate' ORDER BY chatmessage_id ASC");
    while($message = $database->database_fetch_assoc($messages))
    {
      // SET COOKIE WITH THE TIME OF THIS UPDATE
      setcookie("se_chat_lastupdate", "$message[chatmessage_time]", time()+3600);
      
      // SET AUTHOR TEXT
      if($message[chatmessage_user_username] != "") {
        $chatmessage_author = $message['chatmessage_user_displayname'];
      } else {
        $chatmessage_author = "";
      }
      
      // ADD TIMESTAMP TO MESSAGES (CHAR, PLEASE MAKE THIS USE THE USER'S TIMEZONE AND ADMIN'S TIMEFORMAT)
      $chatmessage_time = $datetime->timezone($message[chatmessage_time], $global_timezone);
      
      $chat_messages .= "<message>\r\n";
      $chat_messages .= "<id>$message[chatmessage_id]</id>\r\n";
      $chat_messages .= "<author>$chatmessage_author</author>\r\n";
      $chat_messages .= "<time>$chatmessage_time</time>\r\n";
      $chat_messages .= "<content>".htmlspecialchars($message[chatmessage_content], ENT_QUOTES)."</content>\r\n";
      $chat_messages .= "</message>\r\n";
    }
    
    // GET ONLINE USERS
    $thisuser_loggedin = 0;
    $onlineusers = $database->database_query("SELECT * FROM se_chatusers ORDER BY chatuser_id DESC");
    while($onlineuser = $database->database_fetch_assoc($onlineusers)) {
      if($onlineuser[chatuser_user_id] != $user->user_info[user_id]) {
        if($onlineuser[chatuser_user_photo] != "") { $user_photo = $url->url_userdir($onlineuser[chatuser_user_id]).$onlineuser[chatuser_user_photo]; } else { $user_photo = ""; }
        $chat_onlineusers .= "<user>\r\n";
        $chat_onlineusers .= "<username>{$onlineuser['chatuser_user_username']}</username>\r\n";
        $chat_onlineusers .= "<displayname>{$onlineuser['chatuser_user_displayname']}</displayname>\r\n";
        $chat_onlineusers .= "<photo>$user_photo</photo>\r\n";
        $chat_onlineusers .= "</user>\r\n";
      } else {
        $thisuser_loggedin = 1;
      }
    }

    // ADD NUMBER OF ONLINE USERS TO OUTPUT ARRAY
    $chat_onlineusers_num = $database->database_num_rows($onlineusers) - 1;

    // UPDATE THIS USER'S UPDATE TIME
    $time_update = time();
    $database->database_query("UPDATE se_chatusers SET chatuser_lastupdate='$time_update' WHERE chatuser_user_id='".$user->user_info[user_id]."' LIMIT 1");

  }

  // IF THIS USER HAS NO ROW IN THE CHATUSERS TABLE, THEY HAVE PROBABLY BEEN KICKED
  if($thisuser_loggedin == 0) {
    $update_result = "dropped";
  }

  // PUT TOGETHER OUTPUT ARRAY
  $chat_output = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
  $chat_output .= "<response>\r\n";
  $chat_output .= "<result>$update_result</result>\r\n";
  $chat_output .= "<onlineusers_num>$chat_onlineusers_num</onlineusers_num>\r\n";
  $chat_output .= "<onlineusers>$chat_onlineusers</onlineusers>\r\n";
  $chat_output .= "<messages>$chat_messages</messages>\r\n";
  $chat_output .= "</response>";

  // PRINT IMPLODED OUTPUT ARRAY
  ob_end_clean();
  header("Content-Type: text/xml");
  echo $chat_output;
  exit();
}






// SEND MESSAGE TO CHAT
if($task == "chat_send") {

  // GET POSTED MESSAGE
  $chat_msg = $_POST['chat_msg'];
  if($chat_msg == "") { exit(); }

  // GET POSTED STYLES
  $chat_bold_value = $_POST['chat_bold_value'];
  $chat_italic_value = $_POST['chat_italic_value'];
  $chat_underline_value = $_POST['chat_underline_value'];
  $chat_color_value = $_POST['chat_color_value'];

  // ADD STYLES TO MESSAGE
  if($chat_bold_value == 1) {
    $chat_msg = "<b>$chat_msg</b>";
  }
  if($chat_italic_value == 1) {
    $chat_msg = "<i>$chat_msg</i>";
  }
  if($chat_underline_value == 1) {
    $chat_msg = "<u>$chat_msg</u>";
  }
  if($chat_color_value != "") {
    $chat_msg = "<font style=\'color: #$chat_color_value;\'>".$chat_msg."</font>";
  }

  // GET CURRENT TIME
  $time = time();

  // GET SESSION ID OF POSTER
  $chatuser_session_id = $_SESSION['chatuser_session_id'];

  // CHECK IF MORE THAN 50 MESSAGES ARE IN DATABASE - IF SO, DELETE THEM
  $messages_query = $database->database_query("SELECT chatmessage_id FROM se_chatmessages ORDER BY chatmessage_id ASC LIMIT 50");
  if($database->database_num_rows($messages_query) > 49) {
    $messages_query_delete = $database->database_query("DELETE FROM se_chatmessages ORDER BY chatmessage_id ASC LIMIT 50");
  }

  // ADD NEW MESSAGE TO DATABASE
  $database->database_query("
    INSERT INTO se_chatmessages
      (chatmessage_time, chatmessage_user_username, chatmessage_user_displayname, chatmessage_content)
    VALUES
      ('$time', '{$user->user_info['user_username']}', '".addslashes($user->user_displayname_short)."', '$chat_msg')
  ");
  
  ob_end_clean();
  exit();
}






// LOGIN TO CHAT (START SESSION)
// THIS TASK, WHEN CALLED BY AJAX, OUTPUTS ANY OF THE FOLLOWING CODES:
// fail - THE USER COULD NOT BE LOGGED IN FOR SOME REASON (DEFUALT)
// banned - THE USER HAS BEEN BANNED BY THE ADMIN
// kicked - THE USER HAS RECENTLY BEEN KICKED
// chatoff - THE CHATROOM HAS BEEN DISABLED BY THE ADMIN
// success - THE USER LOGGED IN SUCCESSFULLY

if($task == "chat_login")
{
  SE_Language::_preload(3500023);
  SE_Language::load();
  
  // PREPARE RESULT VARIABLE
  $login_result = "success";

  // SET TIME OF UPDATE COOKIE TO NOW
  setcookie("se_chat_lastupdate", time(), time()+3600);

  // GET CURRENT TIME
  $nowtime = time();

  // DELETE ANY KICKS THAT HAVE EXPIRED
  $database->database_query("DELETE FROM se_chatbans WHERE chatban_bandate>'0' AND chatban_bandate<$nowtime");

  // DELETE ANY LOGGED OUT USERS
  $time_logout = time() - 15;
  $database->database_query("DELETE FROM se_chatusers WHERE chatuser_lastupdate<'$time_logout' AND chatuser_user_id!='{$user->user_info['user_id']}'");

  // CHECK IF CHATROOM IS DISABLED
  if( !$setting['setting_chat_enabled'] )
  {
    $login_result = "chatoff";
  }

  // CHECK IF USER HAS BEEN KICKED OR BANNED
  if($login_result == "success")
  {
    $banned_user_query = $database->database_query("SELECT chatban_id, chatban_bandate FROM se_chatbans WHERE chatban_user_id='{$user->user_info['user_id']}' LIMIT 1");
    if($database->database_num_rows($banned_user_query) != 1)
    {
      $login_result = "success";
    }
    else
    {
      $banned_user_info = $database->database_fetch_assoc($banned_user_query);
      if($banned_user_info[chatban_bandate] > 0) {
        $login_result = "kicked";
      } else {
        $login_result = "banned";
      }
    }
  }

  // CHECK IF USER IS ALREADY LOGGED IN - IF SO, DO NOTHING
  if($login_result == "success")
  {
    $chatuser_query = $database->database_query("SELECT chatuser_user_id FROM se_chatusers WHERE chatuser_user_id='".$user->user_info[user_id]."' LIMIT 1");
    if( !$database->database_num_rows($chatuser_query) )
    {
      // GET USER PHOTO, ADD USER TO DATABASE
      $chatuser_photo_path = $url->url_userdir($user->user_info[user_id]).$user->user_info[user_photo];
      if(!file_exists($chatuser_photo_path) OR $user->user_info[user_photo] == "") {
        $chatuser_photo = "";
      } else {
        $chatuser_photo = $user->user_info[user_photo];
      }
      
      $database->database_query("
        INSERT INTO se_chatusers
          (chatuser_user_id, chatuser_user_username, chatuser_user_displayname, chatuser_lastupdate, chatuser_user_photo)
        VALUES
          ('{$user->user_info['user_id']}', '{$user->user_info['user_username']}', '".addslashes($user->user_displayname)."', '$nowtime', '$chatuser_photo')
      ");
      
      // INSERT WELCOME MESSAGE
      $time_welcome = time();
      $message_username = "";
      $message_welcome = sprintf(SE_Language::get(3500023), $user->user_displayname);
      $database->database_query("
        INSERT INTO se_chatmessages
          (chatmessage_time, chatmessage_user_username, chatmessage_content)
        VALUES
          ('$time_welcome', '$message_username', '$message_welcome')
      ");
    }
  }

  // OUTPUT RESULT
  ob_end_clean();
  echo $login_result;
  exit;


}





// SET SMARTY VARS AND DISPLAY PAGE
$smarty->assign('server_time', $datetime->timezone(time(), $global_timezone)*1000);
ob_end_clean();
include "footer.php";
?>