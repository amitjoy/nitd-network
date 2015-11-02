<?php

$page = "user_poll_edit";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['poll_id'])) { $poll_id = $_POST['poll_id']; } elseif(isset($_GET['poll_id'])) { $poll_id = $_GET['poll_id']; } else { $poll_id = 0; }

$poll_title     = ( !empty($_POST['poll_title'])    ? $_POST['poll_title']    : NULL );
$poll_desc      = ( !empty($_POST['poll_desc'])     ? $_POST['poll_desc']     : NULL );
$poll_options   = ( !empty($_POST['poll_options'])  ? $_POST['poll_options']  : NULL );
$poll_search    = ( !empty($_POST['poll_search'])   ? $_POST['poll_search']   : NULL );
$poll_privacy   = ( !empty($_POST['poll_privacy'])  ? $_POST['poll_privacy']  : NULL );
$poll_comments  = ( !empty($_POST['poll_comments']) ? $_POST['poll_comments'] : NULL );

// SET EMPTY VARS
$is_error = FALSE;

// ENSURE POLLS ARE ENABLED FOR THIS USER
if( 4 & ~(int)$user->level_info['level_poll_allow'] )
{
  header("Location: user_home.php");
  exit();
}

// CREATE POLL OBJECT
$poll = new se_poll($user->user_info['user_id'], $poll_id);

// VERIFY POLLS EXISTS AND OWNER
if( !$poll->poll_exists || $poll->poll_info['poll_user_id']!=$user->user_info['user_id'] )
{
  header("Location: user_poll.php");
  exit();
}


// GET CURRENT POLL DATA
$poll_title = $poll->poll_info['poll_title'];
$poll_desc = $poll->poll_info['poll_desc'];

// EDIT THIS POLL
if($task == "doedit")
{
  $poll_title = $_POST['poll_title'];
  $poll_desc = $_POST['poll_desc'];
  $poll_search = $_POST['poll_search'];
  $poll_privacy = $_POST['poll_privacy'];
  $poll_comments = $_POST['poll_comments'];
  
  // HTML SUPPORT
  $poll_title = censor(cleanHTML(htmlspecialchars_decode($poll_title), $setting['setting_poll_html']));
  $poll_desc = censor(cleanHTML(htmlspecialchars_decode($poll_desc), $setting['setting_poll_html']));

  // MAKE SURE TITLE IS PROVIDED
  if( !trim($poll_title) )
  {
    $is_error = 2500123;
  }

  // EDIT POLL
  if( !$is_error )
  {
    $poll->poll_edit($poll_title, $poll_desc, $poll_search, $poll_privacy, $poll_comments);
    header("Location: user_poll.php");
    exit();
  }

}


// GET PREVIOUS PRIVACY SETTINGS
$level_poll_privacy = unserialize($user->level_info['level_poll_privacy']);
rsort($level_poll_privacy);
for($c=0;$c<count($level_poll_privacy);$c++) {
  if(user_privacy_levels($level_poll_privacy[$c]) != "") {
    SE_Language::_preload(user_privacy_levels($level_poll_privacy[$c]));
    $privacy_options[$level_poll_privacy[$c]] = user_privacy_levels($level_poll_privacy[$c]);
  }
}

$level_poll_comments = unserialize($user->level_info['level_poll_comments']);
rsort($level_poll_comments);
for($c=0;$c<count($level_poll_comments);$c++) {
  if(user_privacy_levels($level_poll_comments[$c]) != "") {
    SE_Language::_preload(user_privacy_levels($level_poll_comments[$c]));
    $comment_options[$level_poll_comments[$c]] = user_privacy_levels($level_poll_comments[$c]);
  }
}

$smarty->assign('poll_search', $poll->poll_info['poll_search']);
$smarty->assign('poll_privacy', $poll->poll_info['poll_privacy']);
$smarty->assign('poll_comments', $poll->poll_info['poll_comments']);
$smarty->assign('privacy_options', $privacy_options);
$smarty->assign('comment_options', $comment_options);
$smarty->assign('search_polls', $user->level_info['level_poll_search']);
$smarty->assign('poll_title', $poll_title);
$smarty->assign('poll_desc', $poll_desc);
$smarty->assign('poll_id', $poll_id);
$smarty->assign('poll_length', $poll_length);
$smarty->assign('is_error', $is_error);
include "footer.php";
?>