<?php


$page = "group_album_file";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = ""; }
if(isset($_POST['group_id'])) { $group_id = $_POST['group_id']; } elseif(isset($_GET['group_id'])) { $group_id = $_GET['group_id']; } else { $group_id = 0; }
if(isset($_POST['groupmedia_id'])) { $groupmedia_id = $_POST['groupmedia_id']; } elseif(isset($_GET['groupmedia_id'])) { $groupmedia_id = $_GET['groupmedia_id']; } else { $groupmedia_id = 0; }

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



// MAKE SURE MEDIA EXISTS
$media_query = $database->database_query("SELECT se_groupmedia.*, se_groupalbums.*, se_users.user_id, se_users.user_username, se_users.user_fname, se_users.user_lname FROM se_groupmedia LEFT JOIN se_groupalbums ON se_groupmedia.groupmedia_groupalbum_id=se_groupalbums.groupalbum_id LEFT JOIN se_users ON se_groupmedia.groupmedia_user_id WHERE se_groupmedia.groupmedia_id='{$groupmedia_id}' AND se_groupalbums.groupalbum_group_id='{$group->group_info['group_id']}' LIMIT 1");
if( !$database->database_num_rows($media_query) )
{
  header("Location: ".$url->url_create('group', NULL, $group->group_info['group_id']));
  exit();
}

$media_info = $database->database_fetch_assoc($media_query);

$uploader = new se_user();
if( $media_info['groupmedia_user_id'] != $media_info['user_id'] )
{
  $uploader->user_exists = FALSE;
}
else
{
  $uploader->user_exists = TRUE;
  $uploader->user_info['user_id'] = $media_info['user_id'];
  $uploader->user_info['user_username'] = $media_info['user_username'];
  $uploader->user_info['user_fname'] = $media_info['user_fname'];
  $uploader->user_info['user_lname'] = $media_info['user_lname'];
  $uploader->user_displayname();
}
$media_info['uploader'] = $uploader;


// GET PRIVACY LEVEL
$privacy_max = $group->group_privacy_max($user);
if( !($privacy_max & $group->group_info['group_privacy']) )
{
  header("Location: ".$url->url_create("group", NULL, $group->group_info['group_id']));
  exit();
}


// GET MEDIA IN ALBUM FOR CAROUSEL
$media_array = Array();
$media_query = $database->database_query("SELECT groupmedia_id, groupmedia_ext, '{$group->group_info['group_id']}' AS groupalbum_group_id FROM se_groupmedia WHERE groupmedia_groupalbum_id='{$media_info['groupalbum_id']}' ORDER BY groupmedia_date DESC");
while($thismedia = $database->database_fetch_assoc($media_query)) { $media_array[$thismedia['groupmedia_id']] = $thismedia; }


// IF USER IS ALLOWED, CHECK TASK
if( $group->user_rank == 2 || $group->user_rank == 1 || ($media_info['uploader']->user_exists && $user->user_info['user_id'] == $media_info['uploader']->user_info['user_id']) )
{
  // DELETE PHOTO
  if($task == "media_delete")
  {
    $media_path = $group->group_dir($group->group_info['group_id']).$media_info['groupmedia_id'].".".$media_info['groupmedia_ext'];
    if(file_exists($media_path)) { @unlink($media_path); }
    $thumb_path = $group->group_dir($group->group_info['group_id']).$media_info['groupmedia_id']."_thumb.jpg";
    if(file_exists($thumb_path)) { @unlink($thumb_path); }
    $action_thumb_path = $url->url_base.substr($group->group_dir($group->group_info['group_id']), 2).$media_info['groupmedia_id']."_thumb.jpg";
    
    // DELETE ACTION MEDIA IF NECESSARY
    $database->database_query("DELETE FROM se_actionmedia WHERE actionmedia_path = '{$action_thumb_path}'");
    
    // DELETE MEDIA FROM DATABASE
    $database->database_query("DELETE FROM se_groupmedia, se_groupmediacomments, se_groupmediatags USING se_groupmedia LEFT JOIN se_groupmediacomments ON se_groupmedia.groupmedia_id=se_groupmediacomments.groupmediacomment_groupmedia_id LEFT JOIN se_groupmediatags ON se_groupmedia.groupmedia_id=se_groupmediatags.groupmediatag_groupmedia_id WHERE se_groupmedia.groupmedia_id='{$media_info['groupmedia_id']}'");
    
    // UPDATE CACHED TOTALS
    $database->database_query("UPDATE se_groupalbums SET groupalbum_totalfiles=groupalbum_totalfiles-1, groupalbum_totalspace=groupalbum_totalspace-'{$media_info['groupmedia_filesize']}' WHERE groupalbum_id='{$media_info['groupmedia_groupalbum_id']}' LIMIT 1");
    
    // SEND USER TO NEXT PHOTO
    $media_keys = array_keys($media_array);
    $current_index = array_search($media_info[groupmedia_id], $media_keys);
    if($current_index+1 == count($media_array)) { $next_index = 0; } else { $next_index = $current_index+1; }
    header("Location: ".$url->url_create('group_media', NULL, $group->group_info['group_id'], $media_keys[$next_index]));
    exit();
  }
  
  
  // EDIT PHOTO
  elseif($task == "media_edit")
  {
    $media_info['groupmedia_title'] = $_POST['groupmedia_title'];
    $media_info['groupmedia_desc'] = $_POST['groupmedia_desc'];
    $database->database_query("UPDATE se_groupmedia SET groupmedia_title='{$media_info['groupmedia_title']}', groupmedia_desc='{$media_info['groupmedia_desc']}' WHERE groupmedia_id='{$media_info['groupmedia_id']}' LIMIT 1");
  }
}



// GET CUSTOM GROUP STYLE IF ALLOWED
if( $group->groupowner_level_info['level_group_style'] )
{ 
  $groupstyle_info = $database->database_fetch_assoc($database->database_query("SELECT groupstyle_css FROM se_groupstyles WHERE groupstyle_group_id='{$group->group_info['group_id']}' LIMIT 1")); 
  $global_css = $groupstyle_info['groupstyle_css'];
}

// GET MEDIA WIDTH/HEIGHT
$mediasize = @getimagesize($group->group_dir($group->group_info['group_id']).$media_info['groupmedia_id'].'.'.$media_info['groupmedia_ext']);
$media_info['groupmedia_width'] = $mediasize[0];
$media_info['groupmedia_height'] = $mediasize[1];


// CHECK IF USER IS ALLOWED TO TAG PHOTOS
$allowed_to_tag = ($privacy_max & $media_info['groupalbum_tag']);

// CHECK IF USER IS ALLOWED TO COMMENT
$allowed_to_comment = ($privacy_max & $group->group_info['group_comments']);


// GET MEDIA COMMENTS
$comment = new se_comment('groupmedia', 'groupmedia_id', $media_info['groupmedia_id']);
$total_comments = $comment->comment_total();


// UPDATE ALBUM VIEWS
$album_views_new = ++$media_info['groupalbum_views'];
$database->database_query("UPDATE se_groupalbums SET groupalbum_views=groupalbum_views+1 WHERE groupalbum_id='{$media_info['groupalbum_id']}' LIMIT 1");

// UPDATE NOTIFICATIONS
if( $user->user_info['user_id'] == $group->group_info['group_user_id'] )
{
  $database->database_query("DELETE FROM se_notifys USING se_notifys LEFT JOIN se_notifytypes ON se_notifys.notify_notifytype_id=se_notifytypes.notifytype_id WHERE se_notifys.notify_user_id='{$user->user_info['user_id']}' AND (se_notifytypes.notifytype_name='groupmediacomment' OR se_notifytypes.notifytype_name='groupmediatag' OR se_notifytypes.notifytype_name='newgrouptag') AND notify_object_id='{$media_info['groupmedia_id']}'");
}



// RETRIEVE TAGS FOR THIS PHOTO
$tag_array = Array();
$tags = $database->database_query("SELECT se_groupmediatags.*, se_users.user_id, se_users.user_username, se_users.user_fname, se_users.user_lname FROM se_groupmediatags LEFT JOIN se_users ON se_groupmediatags.groupmediatag_user_id=se_users.user_id WHERE groupmediatag_groupmedia_id='{$media_info['groupmedia_id']}' ORDER BY groupmediatag_id ASC");
while($tag = $database->database_fetch_assoc($tags))
{ 
  $taggeduser = new se_user();
  if( $tag['user_id'] )
  {
    $taggeduser->user_exists = TRUE;
    $taggeduser->user_info['user_id'] = $tag['user_id'];
    $taggeduser->user_info['user_username'] = $tag['user_username'];
    $taggeduser->user_info['user_fname'] = $tag['user_fname'];
    $taggeduser->user_info['user_lname'] = $tag['user_lname'];
    $taggeduser->user_displayname();
  }
  else
  {
    $taggeduser->user_exists = FALSE;
  }

  $tag['tagged_user'] = $taggeduser;
  $tag_array[] = $tag; 
}


// SET GROUP OWNER (OR EDITOR)
if($group->user_rank == 2 || $group->user_rank == 1) {
  $groupowner = $user;
} else {
  $groupowner = new se_user(Array($group->group_info['group_user_id']));
}


// SET GLOBAL PAGE TITLE
$global_page_title[0] = 2000326; 
$global_page_title[1] = $group->group_info['group_title'];
$global_page_description[0] = 2000327;
$global_page_description[1] = $group->group_info['group_title'];


// ASSIGN VARIABLES AND DISPLAY ALBUM FILE PAGE
$smarty->assign('group', $group);
$smarty->assign('groupowner', $groupowner);
$smarty->assign('media_info', $media_info);
$smarty->assign('total_comments', $total_comments);
$smarty->assign('allowed_to_comment', $allowed_to_comment);
$smarty->assign('allowed_to_tag', $allowed_to_tag);
$smarty->assign('media', $media_array);
$smarty->assign('media_keys', array_keys($media_array));
$smarty->assign('tags', $tag_array);
include "footer.php";
?>