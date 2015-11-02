<?php

$page = "admin_wall";
include "admin_header.php";

global $setting;

if (isset($_POST['task'])) $task = $_POST['task'];
if ( isset($_POST['comments_per_page']) ) $comments_per_page = $_POST['comments_per_page'];
if ( isset($_POST['actions_per_page']) ) $actions_per_page = $_POST['actions_per_page'];

$music_plugin_installed = isset($global_plugins['music']);
$album_plugin_installed = isset($global_plugins['album']);
$video_plugin_installed = isset($global_plugins['video']);

// SET RESULT VARIABLE
$result = 0;

if ( $task == 'dosave' )
{
	$result = 1;
	
	$setting['setting_he_wall_comments_per_page'] = (int)$_POST['setting_he_wall_comments_per_page'];
	$setting['setting_he_wall_actions_per_page'] = (int)$_POST['setting_he_wall_actions_per_page'];
	$setting['setting_he_wall_guest_view'] = (int)$_POST['setting_he_wall_guest_view'];
	$setting['setting_he_wall_music_sync'] = (int)$_POST['setting_he_wall_music_sync'];
	$setting['setting_he_wall_video_sync'] = (int)$_POST['setting_he_wall_video_sync'];
	$setting['setting_he_wall_photo_sync'] = (int)$_POST['setting_he_wall_photo_sync'];

	$setting['setting_he_wall_video_player_width'] = (int)$_POST['setting_he_wall_video_player_width'];
	$setting['setting_he_wall_video_player_height'] = (int)$_POST['setting_he_wall_video_player_height'];
	$setting['setting_he_wall_video_thumb_width'] = (int)$_POST['setting_he_wall_video_thumb_width'];
	$setting['setting_he_wall_video_thumb_height'] = (int)$_POST['setting_he_wall_video_thumb_height'];
	
	$setting['setting_he_wall_photo_width'] = (int)$_POST['setting_he_wall_photo_width'];
	$setting['setting_he_wall_photo_height'] = (int)$_POST['setting_he_wall_photo_height'];
	$setting['setting_he_wall_photo_thumb_width'] = (int)$_POST['setting_he_wall_photo_thumb_width'];
	$setting['setting_he_wall_photo_thumb_height'] = (int)$_POST['setting_he_wall_photo_thumb_height'];
	$setting['setting_he_wall_photo_filesize'] = (int)$_POST['setting_he_wall_photo_filesize'];
	$setting['setting_he_wall_photo_exts'] = trim($_POST['setting_he_wall_photo_exts']);
	$setting['setting_he_wall_photo_mimes'] = trim($_POST['setting_he_wall_photo_mimes']);
	
	$setting['setting_he_wall_music_filesize'] = (int)$_POST['setting_he_wall_music_filesize'];
	$setting['setting_he_wall_music_exts'] = trim($_POST['setting_he_wall_music_exts']);
	$setting['setting_he_wall_music_mimes'] = trim($_POST['setting_he_wall_music_mimes']);

    $sql = he_database::placeholder( "UPDATE `se_settings` SET 
        `setting_he_wall_comments_per_page`=?, 
        `setting_he_wall_actions_per_page`=?, 
        `setting_he_wall_music_sync`=?,
        `setting_he_wall_video_sync`=?,
        `setting_he_wall_photo_sync`=?,
        `setting_he_wall_guest_view`=?,
        
        `setting_he_wall_video_player_width`=?,
        `setting_he_wall_video_player_height`=?,
        `setting_he_wall_video_thumb_width`=?,
        `setting_he_wall_video_thumb_height`=?,
        
        `setting_he_wall_photo_width`=?,
        `setting_he_wall_photo_height`=?,
        `setting_he_wall_photo_thumb_width`=?,
        `setting_he_wall_photo_thumb_height`=?,
        `setting_he_wall_photo_filesize`=?,
        `setting_he_wall_photo_exts`='?',
        `setting_he_wall_photo_mimes`='?',
        
        `setting_he_wall_music_filesize`=?,
        `setting_he_wall_music_exts`='?',
        `setting_he_wall_music_mimes`='?'",

        $setting['setting_he_wall_comments_per_page'], 
        $setting['setting_he_wall_actions_per_page'],
        $setting['setting_he_wall_music_sync'],
        $setting['setting_he_wall_video_sync'],
        $setting['setting_he_wall_photo_sync'],
        $setting['setting_he_wall_guest_view'],
        
        $setting['setting_he_wall_video_player_width'],
        $setting['setting_he_wall_video_player_height'],
        $setting['setting_he_wall_video_thumb_width'],
        $setting['setting_he_wall_video_thumb_height'],
        
        $setting['setting_he_wall_photo_width'],
        $setting['setting_he_wall_photo_height'],
        $setting['setting_he_wall_photo_thumb_width'],
        $setting['setting_he_wall_photo_thumb_height'],
        $setting['setting_he_wall_photo_filesize'],
        $setting['setting_he_wall_photo_exts'],
        $setting['setting_he_wall_photo_mimes'],
        
        $setting['setting_he_wall_music_filesize'],
        $setting['setting_he_wall_music_exts'],
        $setting['setting_he_wall_music_mimes']
     );
	
    he_database::query($sql);
}

$smarty->assign('music_plugin_installed', $music_plugin_installed);
$smarty->assign('album_plugin_installed', $album_plugin_installed);
$smarty->assign('video_plugin_installed', $video_plugin_installed);

$smarty->assign('result', $result);

include "admin_footer.php";

?>