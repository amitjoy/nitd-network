<?php

$page = "user_album_add";
include "header.php";

$task = ( isset($_POST['task']) ? $_POST['task'] : NULL );

// ENSURE ALBUMS ARE ENABLED FOR THIS USER
if( !$user->level_info['level_album_allow'] )
{
  header("Location: user_home.php");
  exit();
}

// CHECK THAT MAX ALBUMS HAVEN'T BEEN REACHED
$album = new se_album($user->user_info['user_id']);
$total_albums = $album->album_total();
if($total_albums >= $user->level_info['level_album_maxnum']) { $task = "main"; }

// GET PRIVACY SETTINGS
$level_album_privacy = unserialize($user->level_info['level_album_privacy']);
rsort($level_album_privacy);
$level_album_comments = unserialize($user->level_info['level_album_comments']);
rsort($level_album_comments);
$level_album_tag = unserialize($user->level_info['level_album_tag']);
rsort($level_album_tag);

// SET VARS
$is_error = 0;
$album_title = "";
$album_desc = "";
$album_search = 1;
$album_privacy = $level_album_privacy[0];
$album_comments = $level_album_comments[0];
$album_tag = $level_album_tag[0];


if($task == "doadd")
{
  $album_title = censor($_POST['album_title']);
  $album_desc = censor(str_replace("\r\n", "<br>", $_POST['album_desc']));
  $album_search = $_POST['album_search'];
  $album_privacy = $_POST['album_privacy'];
  $album_comments = $_POST['album_comments'];
  $album_tag = $_POST['album_tag'];
  $album_datecreated = time();

  // MAKE SURE SUBMITTED PRIVACY OPTIONS ARE ALLOWED, IF NOT, SET TO EVERYONE
  if(!in_array($album_privacy, $level_album_privacy)) { $album_privacy = $level_album_privacy[0]; }
  if(!in_array($album_comments, $level_album_comments)) { $album_comments = $level_album_comments[0]; }
  if(!in_array($album_tag, $level_album_tag)) { $album_tag = $level_album_tag[0]; }

  // CHECK THAT TITLE IS NOT BLANK
  if(trim($album_title) == "") { $is_error = 1000073; }

  // IF NO ERROR, CONTINUE
  if($is_error == 0) {

    // GET MAX ORDER
    $max = $database->database_fetch_assoc($database->database_query("SELECT max(album_order) AS max FROM se_albums WHERE album_user_id='{$user->user_info['user_id']}'"));
    $album_order = $max['max']+1;

    // INSERT NEW ALBUM INTO DATABASE
    $database->database_query("INSERT INTO se_albums (
				album_user_id,
				album_datecreated,
				album_dateupdated,
				album_title, 
				album_desc, 
				album_search,
				album_privacy,
				album_comments,
				album_tag,
				album_order
				) VALUES (
				'{$user->user_info['user_id']}',
				'$album_datecreated',
				'$album_datecreated',
				'$album_title',
				'$album_desc',
				'$album_search',
				'$album_privacy',
				'$album_comments',
				'$album_tag',
				'$album_order')
				");

    // GET ALBUM ID
    $album_id = $database->database_insert_id();

    // UPDATE LAST UPDATE DATE (SAY THAT 10 TIMES FAST)
    $user->user_lastupdate();

    // INSERT ACTION
    if(strlen($album_title) > 100) { $album_title = substr($album_title, 0, 97); $album_title .= "..."; }
    $actions->actions_add($user, "newalbum", Array($user->user_info['user_username'], $user->user_displayname, $album_id, $album_title), Array(), 0, FALSE, "user", $user->user_info['user_id'], $album_privacy);

    // CALL ALBUM CREATION HOOK
    ($hook = SE_Hook::exists('se_album_create')) ? SE_Hook::call($hook, array()) : NULL;

    // SEND TO UPLOAD PAGE
    header("Location: user_album_upload.php?album_id={$album_id}&new_album=1");
    exit();
  }
}





// GET PREVIOUS PRIVACY SETTINGS
for($c=0;$c<count($level_album_privacy);$c++) {
  if(user_privacy_levels($level_album_privacy[$c]) != "") {
    SE_Language::_preload(user_privacy_levels($level_album_privacy[$c]));
    $privacy_options[$level_album_privacy[$c]] = user_privacy_levels($level_album_privacy[$c]);
  }
}

for($c=0;$c<count($level_album_comments);$c++) {
  if(user_privacy_levels($level_album_comments[$c]) != "") {
    SE_Language::_preload(user_privacy_levels($level_album_comments[$c]));
    $comment_options[$level_album_comments[$c]] = user_privacy_levels($level_album_comments[$c]);
  }
}

for($c=0;$c<count($level_album_tag);$c++) {
  if(user_privacy_levels($level_album_tag[$c]) != "") {
    SE_Language::_preload(user_privacy_levels($level_album_tag[$c]));
    $tag_options[$level_album_tag[$c]] = user_privacy_levels($level_album_tag[$c]);
  }
}



// ASSIGN VARIABLES AND SHOW ADD ALBUM PAGE
$smarty->assign('is_error', $is_error);
$smarty->assign('total_albums', $total_albums);
$smarty->assign('album_title', $album_title);
$smarty->assign('album_desc', str_replace("<br>", "\r\n", $album_desc));
$smarty->assign('album_search', $album_search);
$smarty->assign('album_privacy', $album_privacy);
$smarty->assign('album_comments', $album_comments);
$smarty->assign('album_tag', $album_tag);
$smarty->assign('privacy_options', $privacy_options);
$smarty->assign('comment_options', $comment_options);
$smarty->assign('tag_options', $tag_options);
include "footer.php";
?>