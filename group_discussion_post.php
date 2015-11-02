<?php

$page = "group_discussion_post";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['group_id'])) { $group_id = $_POST['group_id']; } elseif(isset($_GET['group_id'])) { $group_id = $_GET['group_id']; } else { $group_id = 0; }

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if( (!$user->user_exists && !$setting['setting_permission_group']) || ($user->user_exists && (~(int)$user->level_info['level_group_allow'] & 1)) )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

// DISPLAY ERROR PAGE IF NO OWNER
$group = new se_group($user->user_info['user_id'], $group_id);
if( !$group->group_exists )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 2000219);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

// GET PRIVACY LEVEL
$privacy_max = $group->group_privacy_max($user);
if( !($privacy_max & $group->group_info['group_privacy']) )
{
  header("Location: ".$url->url_create('group', NULL, $group->group_info['group_id']));
  exit();
}

// CHECK IF USER IS ALLOWED TO DISCUSS
if( !($privacy_max & $group->group_info['group_discussion']) )
{
  header("Location: ".$url->url_create('group', NULL, $group->group_info['group_id'])."&v=discussions");
  exit();
}


// SET VARS
$is_error = 0;
$topic_subject = "";
$topic_body = "";


// IF A TOPIC IS BEING POSTED
if($task == "topic_create")
{
  $topic_date = time();
  $topic_subject = censor($_POST['topic_subject']);
  $topic_body = $_POST['topic_body'];

  // ADD BREAKS AND TOPIC BODY
  $topic_body = $group->group_post_bbcode_parse_clean($topic_body);
  $topic_body = addslashes(stripslashes($topic_body));
  
  // RETRIEVE AND CHECK SECURITY CODE IF NECESSARY
  if( $setting['setting_group_discussion_code'] )
  {
    if( !session_id() ) session_start();
    $code = $_SESSION['code'];
    if($code == "") { $code = randomcode(); }
    if($_POST['comment_secure'] != $code) { $is_error = 832; }
  }

  // MAKE SURE TOPIC BODY IS NOT EMPTY
  if(trim($topic_body) == "") { $is_error = 2000298; }

  // CHECK THAT SUBJECT IS NOT EMPTY
  if(trim($topic_subject) == "") { $is_error = 2000299; }

  // ADD TOPIC IF NO ERROR
  if( !$is_error )
  {
    $database->database_query("UPDATE se_groups SET group_totaltopics=group_totaltopics+1 WHERE group_id='{$group->group_info['group_id']}' LIMIT 1");
    $database->database_query("INSERT INTO se_grouptopics (grouptopic_group_id, grouptopic_creatoruser_id, grouptopic_date, grouptopic_subject, grouptopic_totalposts) VALUES ('{$group->group_info['group_id']}', '{$user->user_info['user_id']}', '{$topic_date}', '{$topic_subject}', 1)");
    $topic_id = $database->database_insert_id();
    $database->database_query("INSERT INTO se_groupposts (grouppost_grouptopic_id, grouppost_authoruser_id, grouppost_date, grouppost_body) VALUES ('{$topic_id}', '{$user->user_info['user_id']}', '{$topic_date}', '{$topic_body}')");
    $post_id = $database->database_insert_id();
    
    // INSERT ACTION IF USER EXISTS
    if( $user->user_exists )
    {
      $poster = $user->user_displayname;
      $topic_body_encoded = strip_tags($topic_body, '<br>');
      if( strlen($topic_body_encoded) > 250 )
        $topic_body_encoded = substr($topic_body_encoded, 0, 247)."...";
      $actions->actions_add($user, "grouptopic", Array($user->user_info['user_username'], $user->user_displayname, $group->group_info['group_id'], $group->group_info['group_title'], $topic_id, $topic_subject, $topic_body_encoded), Array(), 0, false, 'group', $group->group_info['group_id'], $group->group_info['group_privacy']);
    }
    else
    {
      SE_Language::_preload(835);
      SE_Language::load();
      $poster = SE_Language::_get(835);
    }

    // SEND GROUP POST NOTIFICATION IF COMMENTER IS NOT OWNER
    if( $group->group_info['group_user_id'] != $user->user_info['user_id'] )
    { 
      $groupowner = new se_user(Array($group->group_info['group_user_id']));
      $notifytype = $notify->notify_add($group->group_info['group_user_id'], 'grouppost', $group->group_info['group_id'], Array($group->group_info['group_id']), Array($group->group_info['group_title']));
      $object_url = $url->url_base.vsprintf($notifytype[notifytype_url], Array($group->group_info['group_id']));
      $groupowner->user_settings();
      if( $groupowner->usersetting_info['usersetting_notify_grouppost'] )
      {
        send_systememail("grouppost", $groupowner->user_info['user_email'], Array($groupowner->user_displayname, $poster, "<a href=\"$object_url\">$object_url</a>"));
      }
    }
    
    $group->group_lastupdate();
    
    header("Location: ".$url->url_create('group_discussion_post', NULL, $group->group_info['group_id'], $topic_id, $post_id));
    exit();
  }
}


// A REPLY IS BEING POSTED
elseif($task == "reply_do")
{
  $grouptopic_id = $_POST['grouptopic_id'];
  $grouppost_body = $_POST['grouppost_body'];
  
  // VALIDATE GROUPTOPIC ID
  $topic_query = $database->database_query("SELECT grouptopic_id, grouptopic_subject FROM se_grouptopics WHERE grouptopic_id='{$grouptopic_id}' AND grouptopic_group_id='{$group->group_info['group_id']}' LIMIT 1");
  if($database->database_num_rows($topic_query) != 1) { exit(); }
  $grouptopic_info = $database->database_fetch_assoc($topic_query);
  
  // Clean HTML and pre-process bbcode
  $grouppost_body = $group->group_post_bbcode_parse_clean($grouppost_body);
  $grouppost_body = addslashes(stripslashes($grouppost_body));
  
  // RETRIEVE AND CHECK SECURITY CODE IF NECESSARY
  if( $setting['setting_group_discussion_code'] )
  {
    if( !session_id() ) session_start();
    $code = $_SESSION['code'];
    if($code == "") { $code = randomcode(); }
    if($_POST['comment_secure'] != $code) { $is_error = 832; }
  }

  // MAKE SURE TOPIC BODY IS NOT EMPTY
  if( !trim($grouppost_body) ) { $is_error = 2000298; }

  // RUN JAVASCRIPT FUNCTION
  echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><script type=\"text/javascript\">";

  if( $is_error )
  {
    $error = SE_Language::get($is_error);
    echo "window.parent.document.getElementById('post_error').innerHTML = '{$error}';";
    echo "window.parent.document.getElementById('post_error').style.display = 'block';";
  }
  else
  {
    $database->database_query("UPDATE se_grouptopics SET grouptopic_totalposts=grouptopic_totalposts+1 WHERE grouptopic_id='{$grouptopic_id}' LIMIT 1");
    $database->database_query("INSERT INTO se_groupposts (grouppost_grouptopic_id, grouppost_authoruser_id, grouppost_date, grouppost_body) VALUES ('{$grouptopic_id}', '{$user->user_info['user_id']}', '".time()."', '{$grouppost_body}')");
    $post_id = $database->database_insert_id();
    
    // INSERT ACTION IF USER EXISTS
    if( $user->user_exists )
    {
      $poster = $user->user_displayname;
      
      $grouppost_body_encoded = strip_tags($grouppost_body, '<br>');
      if( strlen($grouppost_body_encoded) > 250 )
        $grouppost_body_encoded = substr($grouppost_body_encoded, 0, 247)."...";
      
      $actions->actions_add($user, "grouppost", Array($user->user_info['user_username'], $user->user_displayname, $group->group_info['group_id'], $grouptopic_info['grouptopic_id'], $grouptopic_info['grouptopic_subject'], $post_id, $grouppost_body_encoded), Array(), 0, false, 'group', $group->group_info['group_id'], $group->group_info['group_privacy']);
    }
    else
    {
      SE_Language::_preload(835);
      SE_Language::load();
      $poster = SE_Language::_get(835);
    }

    // SEND GROUP POST NOTIFICATION IF COMMENTER IS NOT OWNER
    if( $group->group_info['group_user_id'] != $user->user_info['user_id'] )
    { 
      $groupowner = new se_user(Array($group->group_info['group_user_id']));
      $notifytype = $notify->notify_add($group->group_info['group_user_id'], 'grouppost', $group->group_info['group_id'], Array($group->group_info['group_id']), Array($group->group_info['group_title']));
      $object_url = $url->url_base.vsprintf($notifytype[notifytype_url], Array($group->group_info[group_id]));
      $groupowner->user_settings();
      if( $groupowner->usersetting_info['usersetting_notify_grouppost'] )
      {
        send_systememail("grouppost", $groupowner->user_info['user_email'], Array($groupowner->user_displayname, $poster, "<a href=\"$object_url\">$object_url</a>"));
      }
    }
    
    $group->group_lastupdate();
    
    echo "window.parent.location.href = '".$url->url_create('group_discussion_post', NULL, $group->group_info['group_id'], $grouptopic_id, $post_id)."';";
  }
  echo "</script></head><body></body></html>";
  exit();

}

// GET CUSTOM GROUP STYLE IF ALLOWED
if( $group->groupowner_level_info['level_group_style'] )
{ 
  $groupstyle_info = $database->database_fetch_assoc($database->database_query("SELECT groupstyle_css FROM se_groupstyles WHERE groupstyle_group_id='{$group->group_info['group_id']}' LIMIT 1")); 
  $global_css = $groupstyle_info['groupstyle_css'];
}

// SET GLOBAL PAGE TITLE
$global_page_title[0] = 2000328; 
$global_page_description[0] = 2000329;

// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('group', $group);
$smarty->assign('is_error', $is_error);
$smarty->assign('topic_subject', $topic_subject);
$smarty->assign('topic_body', str_replace("<br>", "\r\n", $topic_body));
include "footer.php";
?>