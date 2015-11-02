<?php

$page = "user_poll_new";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }

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
$poll = new se_poll($user->user_info['user_id']);


// ADD A NEW POLL
if( $task=="doadd" )
{
  // HTML SUPPORT
  $poll_title = censor(cleanHTML(htmlspecialchars_decode($poll_title), $setting['setting_poll_html']));
  $poll_desc = censor(cleanHTML(htmlspecialchars_decode($poll_desc), $setting['setting_poll_html']));
  
  // REMOVE EMPTY OPTIONS
  $poll_options = array_filter($poll_options);
  
  // GET POLL OPTIONS AND POST POLL
  foreach( $poll_options as $poll_option_index=>$poll_option_label )
  {
    $poll_options[$poll_option_index] = censor(cleanHTML(htmlspecialchars_decode($poll_option_label), $setting['setting_poll_html']));
  }

  // MAKE SURE TITLE IS PROVIDED
  if( !trim($poll_title) )
  {
    $is_error = 2500123;
  }

  // MAKE SURE AT LEAST TWO OPTIONS ARE PROVIDED
  if( !$is_error && count($poll_options)<2 )
  {
    $is_error = 2500124;
  }

  // MAKE SURE NUMBER OF OPTIONS DOESNT EXCEED 20
  if( !$is_error && count($poll_options)>20 )
  {
    $is_error = 2500125;
    $is_error_sprintf_1 = 20;
  }

  // POST POLL
  if( !$is_error )
  {
    // ADD POLL TO DATABSE
    $poll->poll_create($poll_title, $poll_desc, $poll_options, $poll_search, $poll_privacy, $poll_comments);
    
    // INSERT ACTION
    $new_query = $database->database_query("SELECT poll_id FROM se_polls WHERE poll_user_id='{$user->user_info['user_id']}' ORDER BY poll_id DESC LIMIT 1");
    $new_info = $database->database_fetch_assoc($new_query);
    if(strlen($poll_title) > 100) { $poll_title = substr($poll_title, 0, 97); $poll_title .= "..."; }
    $actions->actions_add($user, "newpoll", Array($user->user_info['user_username'], $user->user_displayname, $new_info['poll_id'], $poll_title));
    
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

// SET SOME DEFAULTS
if( !isset($poll_search)   ) { $poll_search = 1; }
if( !isset($poll_privacy)  ) { $poll_privacy = $level_poll_privacy[0];   }
if( !isset($poll_comments) ) { $poll_comments = $level_poll_comments[0]; }


// FIX THE POLL OPTIONS
if( !is_array($poll_options) )  $poll_options = array();
if( count($poll_options)<2 )    $poll_options[] = "";
if( count($poll_options)<2 )    $poll_options[] = "";


$smarty->assign('privacy_options', $privacy_options);
$smarty->assign('comment_options', $comment_options);

$smarty->assign('poll_title', $poll_title);
$smarty->assign('poll_desc', $poll_desc);
$smarty->assign('poll_options', $poll_options);
$smarty->assign('poll_comments', $poll_comments);
$smarty->assign('poll_privacy', $poll_privacy);
$smarty->assign('poll_search', $poll_search);

$smarty->assign('is_error', $is_error);
$smarty->assign('is_error_sprintf_1', $is_error);
include "footer.php";
?>