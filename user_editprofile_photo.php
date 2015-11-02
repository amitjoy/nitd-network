<?php

$page = "user_editprofile_photo";
include "header.php";

$task = ( isset($_POST['task']) ? $_POST['task'] : NULL );

// CHECK FOR ADMIN ALLOWANCE OF PHOTO
if( !$user->level_info['level_photo_allow'] )
{
  header("Location: user_home.php");
  exit();
}

// SET ERROR VARIABLES
$is_error = 0;

// DELETE PHOTO
if( $task == "remove" )
{
  $user->user_photo_delete();
  $user->user_lastupdate();
  exit();
}


// UPLOAD PHOTO
if( $task == "upload" )
{
  $user->user_photo_upload("photo");
  $is_error = $user->is_error;
  if( !$is_error )
  {
    // SAVE LAST UPDATE DATE
    $user->user_lastupdate(); 
    
    // DETERMINE SIZE OF THUMBNAIL TO SHOW IN ACTION
    $photo_width = $misc->photo_size($user->user_photo(), "100", "100", "w");
    $photo_height = $misc->photo_size($user->user_photo(), "100", "100", "h");
    
    // INSERT ACTION
    $action_media = Array(Array('media_link'=>$url->url_create('profile', $user->user_info['user_username']), 'media_path'=>$user->user_photo(), 'media_width'=>$photo_width, 'media_height'=>$photo_height));
    $actions->actions_add($user, "editphoto", Array($user->user_info['user_username'], $user->user_displayname), $action_media, 999999999, TRUE, "user", $user->user_info['user_id'], $user->user_info['user_privacy']);
  }
}

// GET TABS TO DISPLAY ON TOP MENU
$field = new se_field("profile", $user->profile_info);
$field->cat_list(0, 0, 0, "profilecat_id='{$user->user_info['user_profilecat_id']}'");
$cat_array = $field->subcats;


// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('is_error', $is_error);
$smarty->assign('cats', $cat_array);
include "footer.php";
?>