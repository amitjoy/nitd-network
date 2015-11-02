<?php

$page = "albums";
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


// SET PRIVACY LEVEL AND WHERE CLAUSE
$privacy_max = $owner->user_privacy_max($user);
$where = "(album_privacy & $privacy_max)";


// CREATE ALBUM OBJECT
$album = new se_album($owner->user_info['user_id']);

// GET TOTAL ALBUMS
$total_albums = $album->album_total($where);

// GET ALBUM ARRAY
$album_array = $album->album_list(0, $total_albums, "album_order ASC", $where);

// GET CUSTOM ALBUM STYLE IF ALLOWED
if( $owner->level_info['level_album_style'] )
{
  $albumstyle_info = $database->database_fetch_assoc($database->database_query("SELECT albumstyle_css FROM se_albumstyles WHERE albumstyle_user_id='{$owner->user_info['user_id']}' LIMIT 1"));
  $global_css = $albumstyle_info['albumstyle_css'];
}

// SET GLOBAL PAGE TITLE
$global_page_title[0] = 1000160;
$global_page_title[1] = $owner->user_displayname;
$global_page_description[0] = 1000161;
$global_page_description[1] = $owner->user_displayname;

// ASSIGN SMARTY VARIABLES AND DISPLAY ALBUMS PAGE
$smarty->assign('albums', $album_array);
$smarty->assign('total_albums', $total_albums);
include "footer.php";
?>