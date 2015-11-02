<?php

$page = "profile_photos_file";
include "header.php";


// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if($user->user_exists == 0 && $setting['setting_permission_profile'] == 0) {
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

// DISPLAY ERROR PAGE IF NO OWNER
if($owner->user_exists == 0) {
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 828);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}


if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['type'])) { $type = $_POST['type']; } elseif(isset($_GET['type'])) { $type = $_GET['type']; } else { $type = ""; }
if(isset($_POST['media_id'])) { $media_id = $_POST['media_id']; } elseif(isset($_GET['media_id'])) { $media_id = $_GET['media_id']; } else { $media_id = 0; }


// SET VARS
$media_per_page = 20;

// CHECK PRIVACY
$privacy_max = $owner->user_privacy_max($user);
if( !($owner->user_info['user_privacy'] & $privacy_max) )
{
  header("Location: ".$url->url_create('profile', $owner->user_info['user_username']));
  exit();
}



// START QUERY
$photo_query = "";
$tag_query = Array();

// CALL TAG HOOK
($hook = SE_Hook::exists('se_mediatag')) ? SE_Hook::call($hook, array()) : NULL;

// GET TOTAL PHOTOS
$total_files = $database->database_num_rows($database->database_query($photo_query));

// ADD TO PHOTO QUERY
$photo_query .= " ORDER BY mediatag_date DESC";

// MAKE MEDIA PAGES
$page_vars = make_page($total_files, $media_per_page, $p);

// RUN TAG QUERY
$media = $database->database_query($photo_query);

// GET MEDIA INTO AN ARRAY
$media_array = Array();
while($media_info = $database->database_fetch_assoc($media)) { $media_array[$media_info['type'].$media_info['media_id']] = $media_info; }




// MAKE SURE MEDIA EXISTS
if(!array_key_exists($type.$media_id, $media_array)) { header("Location: profile_photos.php?user=".$owner->user_info['user_username']); exit(); }
$media_info = $media_array[$type.$media_id];


// UPDATE NOTIFICATIONS
if($user->user_info['user_id'] == $owner->user_info['user_id'])
{
  $type = str_replace("media", "", $media_info['type']);
  $database->database_query("DELETE FROM se_notifys USING se_notifys LEFT JOIN se_notifytypes ON se_notifys.notify_notifytype_id=se_notifytypes.notifytype_id WHERE se_notifys.notify_user_id='{$owner->user_info['user_id']}' AND se_notifytypes.notifytype_name='new{$type}tag' AND notify_object_id='{$media_info['media_id']}'");
}


// GET ALBUM TAG PRIVACY
$allowed_to_tag = (bool) $media_info['allowed_to_tag'];

// GET ALBUM COMMENT PRIVACY
$allowed_to_comment = (bool) $media_info['allowed_to_comment'];


// GET OWNER INFO
$page_owner = $owner;
$owner = new se_user(Array($media_info['owner_user_id']));



// GET ALBUM OWNER IF NECESSARY
if($media_info['user_id'] != 0)
{
  $media_info['user'] = new se_user();
  $media_info['user']->user_exists = 1;
  $media_info['user']->user_info['user_id'] = $media_info['user_id'];
  $media_info['user']->user_info['user_username'] = $media_info['user_username'];
  $media_info['user']->user_info['user_fname'] = $media_info['user_fname'];
  $media_info['user']->user_info['user_lname'] = $media_info['user_lname'];
  $media_info['user']->user_displayname();
}




// GET MEDIA WIDTH/HEIGHT
$mediasize = @getimagesize($media_info['media_dir'].$media_info['media_id'].'.'.$media_info['media_ext']);
$media_info['media_width'] = $mediasize[0];
$media_info['media_height'] = $mediasize[1];



// GET MEDIA COMMENTS
$comment = new se_comment($media_info['type'], $media_info['type_id'], $media_info['media_id']);
$total_comments = $comment->comment_total();



// RETRIEVE TAGS FOR THIS PHOTO
$tag_array = Array();
$tags = $database->database_query(str_replace("[media_id]", $media_info['media_id'], $tag_query[$media_info['type']]));
while($tag = $database->database_fetch_assoc($tags))
{ 
  $taggeduser = new se_user();
  if($tag['user_id'] != NULL)
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

  $tag['tagged_user'] = $taggeduser;
  $tag_array[] = $tag; 
}


// SET GLOBAL PAGE TITLE
$global_page_title[0] = 1204;
$global_page_title[1] = $page_owner->user_displayname;
$global_page_title[2] = count($media_array);
$global_page_description[0] = 1204;
$global_page_description[1] = $page_owner->user_displayname;
$global_page_description[2] = count($media_array);

// ASSIGN VARIABLES AND DISPLAY ALBUM FILE PAGE
$smarty->assign('page_owner', $page_owner);
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