<?php

$page = "lostpass";
include "header.php";

$task = ( isset($_POST['task']) ? $_POST['task'] : ( isset($_GET['task']) ? $_GET['task'] : NULL ) );

// SET ERROR VARS
$is_error = 0;
$submitted = 0;


if($task == "send_email")
{
  $new_user = new se_user(Array(0, "", $_POST['user_email']), Array('user_id, user_email, user_username'));
  $submitted = 1;

  if( !$new_user->user_exists )
  {
    $is_error = 748;
  }
  else
  {
    $lostpassword_code = randomcode(15);
    $lostpassword_time = time();
    if( send_systememail('lostpassword', $new_user->user_info['user_email'], Array($new_user->user_displayname, $new_user->user_info['user_email'], "<a href=\"".$url->url_base."lostpass_reset.php?user=".$new_user->user_info['user_username']."&r=$lostpassword_code\">".$url->url_base."lostpass_reset.php?user=".$new_user->user_info['user_username']."&r=$lostpassword_code</a>")) )
    {
      $database->database_query("UPDATE se_usersettings SET usersetting_lostpassword_code='$lostpassword_code', usersetting_lostpassword_time='$lostpassword_time' WHERE usersetting_user_id='{$new_user->user_info['user_id']}' LIMIT 1");
      
      $cache_object = SECache::getInstance();
      if( is_object($cache_object) )
      {
        $cache_object->remove('site_user_settings_'.$new_user->user_info['user_id']);
      }
    }
    else
    {
      $is_error = 748;
    }
  }
}


// SET GLOBAL PAGE TITLE
$global_page_title[0] = 33;
$global_page_description[0] = 34;


// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('is_error', $is_error);
$smarty->assign('submitted', $submitted);
include "footer.php";
?>