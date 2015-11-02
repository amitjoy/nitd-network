<?php

$page = "profile_photos";
include "header.php";

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if($user->user_exists == 0 && $setting['setting_permission_profile'] == 0)
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

// DISPLAY ERROR PAGE IF NO OWNER
if($owner->user_exists == 0)
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 828);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }

// SET VARS
$media_per_page = 20;

// CHECK PRIVACY
$privacy_max = $owner->user_privacy_max($user);
if(!($owner->user_info['user_privacy'] & $privacy_max))
{
  header("Location: ".$url->url_create('profile', $owner->user_info['user_username']));
  exit();
}


// START QUERY
$photo_query = "";


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
$file_array = Array();
while($media_info = $database->database_fetch_assoc($media))
{
  // CREATE OBJECT FOR AUTHOR, IF EXISTS
  if($media_info['user_id'] != '0')
  {
    $author = new se_user();
    $author->user_exists = 1;
    $author->user_info['user_id'] = $media_info['user_id'];
    $author->user_info['user_username'] = $media_info['user_username'];
    $author->user_info['user_fname'] = $media_info['user_fname'];
    $author->user_info['user_lname'] = $media_info['user_lname'];
    $author->user_displayname();
  }
  
  // OTHERWISE, SET AUTHOR TO NOTHING
  else
  {
    $author = new se_user();
    $author->user_exists = 0;
  }

  $media_info['author'] = $author;
  $file_array[] = $media_info;

}




// SET GLOBAL PAGE TITLE
$global_page_title[0] = 1204;
$global_page_title[1] = $owner->user_displayname;
$global_page_title[2] = $total_files;
$global_page_description[0] = 1204;
$global_page_description[1] = $owner->user_displayname;
$global_page_description[2] = $total_files;

// ASSIGN VARIABLES AND DISPLAY PHOTOS PAGE
$smarty->assign('files', $file_array);
$smarty->assign('total_files', $total_files);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($file_array));
include "footer.php";
?>