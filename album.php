<?php


$page = "album";
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
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
if(isset($_GET['album_id'])) { $album_id = $_GET['album_id']; } else { $album_id = 0; }


// SET VARS
$media_per_page = 20;

// GET ALBUM INFO
$album_query = $database->database_query("SELECT * FROM se_albums WHERE album_id='{$album_id}' AND album_user_id='{$owner->user_info['user_id']}'");
if($database->database_num_rows($album_query) != 1) { header("Location: ".$url->url_create('albums', $owner->user_info['user_username'])); exit(); }
$album_info = $database->database_fetch_assoc($album_query);

// CREATE ALBUM OBJECT
$album = new se_album($owner->user_info['user_id']);

// SET WHERE/SORTBY
$where = "(media_album_id='{$album_info['album_id']}')";
$sortby = "media_order ASC";
$select = "";

// GET CUSTOM ALBUM STYLE IF ALLOWED
if($owner->level_info['level_album_style'] != 0) {
  $albumstyle_info = $database->database_fetch_assoc($database->database_query("SELECT albumstyle_css FROM se_albumstyles WHERE albumstyle_user_id='{$owner->user_info['user_id']}' LIMIT 1"));
  $global_css = $albumstyle_info['albumstyle_css'];
}

// CHECK PRIVACY
$privacy_max = $owner->user_privacy_max($user);
if(!($album_info['album_privacy'] & $privacy_max)) {
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 1000125);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

// UPDATE ALBUM VIEWS
if( $user->user_info[user_id] != $owner->user_info['user_id'] )
{
  $album_views_new = $album_info[album_views] + 1;
  $database->database_query("UPDATE se_albums SET album_views='{$album_views_new}' WHERE album_id='{$album_info['album_id']}' LIMIT 1");
}


// GET TOTAL FILES IN ALBUM
$total_files = $album->album_files($album_info['album_id'], $where);

// MAKE MEDIA PAGES
$page_vars = make_page($total_files, $media_per_page, $p);

// GET MEDIA ARRAY
$file_array = $album->album_media_list($page_vars[0], $media_per_page, $sortby, $where, $select);

// SET GLOBAL PAGE TITLE
$global_page_title[0] = 1000155;
$global_page_title[1] = $owner->user_displayname;
$global_page_title[2] = $album_info['album_title'];
$global_page_description[0] = 1000156;
$global_page_description[1] = $album_info['album_desc'];

// ASSIGN VARIABLES AND DISPLAY ALBUM PAGE
$smarty->assign('album_info', $album_info);
$smarty->assign('files', $file_array);
$smarty->assign('total_files', $total_files);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($file_array));
include "footer.php";
?>