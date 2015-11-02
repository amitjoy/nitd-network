<?php

defined('SE_PAGE') or exit();

include_once "../include/class_he_database.php";
include_once "../include/class_he_wall.php";
include_once "../include/class_he_upload.php";
include_once "../include/functions_he_wall.php";

delete_he_wall_action();

SE_Hook::register("se_user_delete", 'he_wall_delete_user');

$task = isset($_POST['task']) ? $_POST['task'] : false;
$task = ( isset($_GET['task']) && !$task ) ? $_GET['task'] : $task; 

if ( $page == 'admin_viewmusic' && $task == 'deletesong' )
{
    $music_id = isset($_POST['music_id']) ? (int)$_POST['music_id'] : 0;
    $user_id = he_wall::get_music_owner($music_id);
    
    he_wall::delete_music_action($user_id, $music_id);
}

if ( $page == 'admin_viewmusic' && $task == 'delete_selected' && $_POST['delete_entry'] )
{
	foreach ( $_POST['delete_entry'] as $music_id )
	{
		$user_id = he_wall::get_music_owner($music_id);
		
		he_wall::delete_music_action($user_id, $music_id);
	}
}

if ( $page == 'admin_viewalbums' && $task == 'deletealbum' )
{
	$album_id = isset($_GET['album_id']) ? (int)$_GET['album_id'] : 0;
	$user_id = he_wall::get_album_owner($album_id);

	he_wall::delete_wall_album($user_id, $album_id);
}

if ( $page == 'admin_viewalbums' && $task == 'delete' )
{
    foreach ( $_POST as $var_key => $value)
    {
        if ( strstr($var_key, 'delete_album_') === false )
        {
            continue;
        }
    	
    	$album_id = substr($var_key, 13);
        $user_id = he_wall::get_album_owner($album_id);  	
    	
        he_wall::delete_wall_album($user_id, $album_id);
    }
}

if ( $page == 'admin_viewvideos' && $task == 'deletevideo' )
{
	$video_id = isset($_GET['video_id']) ? (int)$_GET['video_id'] : 0;
	$video_id = ( isset($_POST['video_id']) && !$video_id ) ? (int)$_POST['video_id'] : $video_id;
	
	$user_id = he_wall::get_video_owner($video_id);

	he_wall::delete_video_action($user_id, $video_id);
}

if ( $page == 'admin_viewvideos' && $task == 'delete' )
{
    foreach ( $_POST as $var_key => $value)
    {
        if ( strstr($var_key, 'delete_video_') === false )
        {
            continue;
        }
    	
    	$video_id = substr($var_key, 13);
        $user_id = he_wall::get_video_owner($video_id);  	
    	
        he_wall::delete_video_action($user_id, $video_id);
    }
}

?>