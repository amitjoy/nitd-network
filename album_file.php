<?php


$page = "album_file";
include "header.php";


// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if( !$user->user_exists && !$setting['setting_permission_album'] )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

// DISPLAY ERROR PAGE IF NO OWNER
if( !$owner->user_exists )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 828);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

// ENSURE ALBUMS ARE ENABLED FOR THIS USER
if( !$owner->level_info['level_album_allow'] )
{
  header("Location: ".$url->url_create('profile', $owner->user_info['user_username']));
  exit();
}


// PARSE GET/POST
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['media_id'])) { $media_id = $_POST['media_id']; } elseif(isset($_GET['media_id'])) { $media_id = $_GET['media_id']; } else { $media_id = 0; }
if(isset($_POST['album_id'])) { $album_id = $_POST['album_id']; } elseif(isset($_GET['album_id'])) { $album_id = $_GET['album_id']; } else { $album_id = ""; }

// MAKE SURE MEDIA EXISTS
$media_query = $database->database_query("SELECT * FROM se_media WHERE media_id='{$media_id}' LIMIT 1");
if($database->database_num_rows($media_query) != 1) { header("Location: ".$url->url_create('albums', $owner->user_info['user_username'])); exit(); }
$media_info = $database->database_fetch_assoc($media_query);


// BE SURE ALBUM BELONGS TO THIS USER
$album = $database->database_query("SELECT * FROM se_albums WHERE album_id='{$media_info['media_album_id']}' AND album_user_id='{$owner->user_info['user_id']}'");
if($database->database_num_rows($album) != 1) { header("Location: ".$url->url_create('albums', $owner->user_info[user_username])); exit(); }
$album_info = $database->database_fetch_assoc($album);

// CHECK PRIVACY
$privacy_max = $owner->user_privacy_max($user);
if( !($album_info['album_privacy'] & $privacy_max) )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 1000125);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

// GET CUSTOM ALBUM STYLE IF ALLOWED
if( $owner->level_info['level_album_style'] )
{
  $albumstyle_info = $database->database_fetch_assoc($database->database_query("SELECT albumstyle_css FROM se_albumstyles WHERE albumstyle_user_id='{$owner->user_info['user_id']}' LIMIT 1"));
  $global_css = $albumstyle_info['albumstyle_css'];
}

// GET MEDIA IN ALBUM FOR CAROUSEL
$media_array = Array();
$media_query = $database->database_query("SELECT media_id, media_ext, '{$owner->user_info['user_id']}' AS album_user_id FROM se_media WHERE media_album_id='{$album_info['album_id']}' ORDER BY media_order ASC");
while($thismedia = $database->database_fetch_assoc($media_query)) { $media_array[$thismedia['media_id']] = $thismedia; }




// GET MEDIA WIDTH/HEIGHT
$mediasize = @getimagesize($url->url_userdir($owner->user_info['user_id']).$media_info['media_id'].'.'.$media_info['media_ext']);
$media_info[media_width] = $mediasize[0];
$media_info[media_height] = $mediasize[1];


// GET ALBUM TAG PRIVACY
$allowed_to_tag = ($privacy_max & $album_info['album_tag']);

// GET ALBUM COMMENT PRIVACY
$allowed_to_comment = ($privacy_max & $album_info['album_comments']);


// GET MEDIA COMMENTS
$comment = new se_comment('media', 'media_id', $media_info['media_id']);
$total_comments = $comment->comment_total();


// UPDATE ALBUM VIEWS
if($user->user_info[user_id] != $owner->user_info['user_id'])
{
  $album_views_new = $album_info[album_views] + 1;
  $database->database_query("UPDATE se_albums SET album_views='{$album_views_new}' WHERE album_id='{$album_info['album_id']}' LIMIT 1");
}

// UPDATE NOTIFICATIONS
if($user->user_info['user_id'] == $owner->user_info['user_id'])
{
  $database->database_query("DELETE FROM se_notifys USING se_notifys LEFT JOIN se_notifytypes ON se_notifys.notify_notifytype_id=se_notifytypes.notifytype_id WHERE se_notifys.notify_user_id='{$owner->user_info['user_id']}' AND (se_notifytypes.notifytype_name='mediacomment' OR se_notifytypes.notifytype_name='mediatag' OR se_notifytypes.notifytype_name='newtag') AND notify_object_id='{$media_info['media_id']}'");
}



// RETRIEVE TAGS FOR THIS PHOTO
$tag_array = Array();
$tags = $database->database_query("SELECT se_mediatags.*, se_users.user_id, se_users.user_username, se_users.user_fname, se_users.user_lname FROM se_mediatags LEFT JOIN se_users ON se_mediatags.mediatag_user_id=se_users.user_id WHERE mediatag_media_id='{$media_info['media_id']}' ORDER BY mediatag_id ASC");
while($tag = $database->database_fetch_assoc($tags))
{ 
  $taggeduser = new se_user();
  if($tag[user_id] != NULL)
  {
    $taggeduser->user_exists = 1;
    $taggeduser->user_info['user_id'] = $tag['user_id'];
    $taggeduser->user_info['user_username'] = $tag['user_username'];
    $taggeduser->user_info['user_fname'] = $tag['user_fname'];
    $taggeduser->user_info['user_lname'] = $tag['user_lname'];
    $taggeduser->user_displayname();
  }
  else
  {
    $taggeduser->user_exists = 0;
  }

  $tag[tagged_user] = $taggeduser;
  $tag_array[] = $tag; 
}

// SET GLOBAL PAGE TITLE
$global_page_title[0] = 1000158;
$global_page_title[1] = $owner->user_displayname;
$global_page_title[2] = $media_info['media_title'];
$global_page_description[0] = 1000159;
$global_page_description[1] = $media_info['media_desc'];

// ASSIGN VARIABLES AND DISPLAY ALBUM FILE PAGE
$smarty->assign('album_info', $album_info);
$smarty->assign('media_info', $media_info);
$smarty->assign('total_comments', $total_comments);
$smarty->assign('allowed_to_comment', $allowed_to_comment);
$smarty->assign('allowed_to_tag', $allowed_to_tag);
$smarty->assign('media', $media_array);
$smarty->assign('media_keys', array_keys($media_array));
$smarty->assign('tags', $tag_array);
include "footer.php";
?>