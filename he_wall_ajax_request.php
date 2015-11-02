<?php

define('SE_PAGE_AJAX', TRUE);
$page = "he_wall_ajax_request";

include "header.php";

$task = isset($_POST['task']) ? $_POST['task'] : false;
$task = ( !$task && isset($_GET['task']) ) ? $_GET['task'] : $task;

$wall_object = isset($_POST['wall_object']) ? $_POST['wall_object'] : false;
$wall_object = ( !$wall_object && isset($_GET['wall_object']) ) ? $_GET['wall_object'] : $wall_object;

$wall_object_id = isset($_POST['wall_object_id']) ? $_POST['wall_object_id'] : false;
$wall_object_id = ( !$wall_object_id && isset($_GET['wall_object_id']) ) ? $_GET['wall_object_id'] : $wall_object_id;

if ( $wall_object == 'userhome' )
{
	$action_object_owner = 'user';
}
else
{
	$action_object_owner = $wall_object;
}

//TASKS

$headers_sent = false;

if ( $task == 'hide_action' && $user->user_exists )
{
	$action_id = isset($_POST['action_id']) ? (int)$_POST['action_id'] : 0;
	
	$result = he_wall::hide_action($user->user_info['user_id'], $action_id);	
}
elseif ( $task == 'remove_action' && $user->user_exists )
{
	$action_id = isset($_POST['action_id']) ? (int)$_POST['action_id'] : 0;
		
	$result = he_wall::remove_action($user->user_info['user_id'], $action_id);	
}
elseif ( $task == 'like_action' )
{
	$action_id = isset($_POST['action_id']) ? (int)$_POST['action_id'] : 0;
	
	$result = he_wall::like_action($user->user_info['user_id'], $action_id);
	
	if ( $result['like'] )
	{
		he_wall::new_like_notify($action_id);
	}
}
elseif ( $task == 'add_comment' )
{
	$action_id = isset($_POST['action_id']) ? (int)$_POST['action_id'] : 0;
	$text = isset($_POST['text']) ? trim($_POST['text']) : '';
	
	if ( strlen($text) > 1000 )
	{
		$result = array( 'result' => 0, 'message' => SE_Language::get(690706058) );
	}
	elseif ( $comment_id = he_wall::add_comment($user->user_info['user_id'], $action_id, $text) )
	{
		$comment_info = he_wall::get_comment($comment_id);
		$comment_info['text'] = he_wall_format_text($comment_info['text']);
        
		he_wall::new_comment_notify($action_id);
		
        $result = array( 'result' => 1, 'comment_info' => $comment_info );
	}
	else
	{
		$result = array( 'result' => 0, 'message' => 'Error' );
	}
}
elseif ( $task == 'delete_comment' )
{
	$comment_id = isset($_POST['comment_id']) ? (int)$_POST['comment_id'] : 0;
	
	$result = he_wall::delete_comment($comment_id, $user->user_info['user_id']);
}
elseif ( $task == 'show_more' )
{
	$last_action_id = isset($_POST['last_action_id']) ? (int)$_POST['last_action_id'] : false;
	
    $result = he_wall_actions_display($wall_object, $wall_object_id, false, $last_action_id);
}
elseif ( $task == 'post_action' )
{
	$first_action_id = isset($_POST['first_action_id']) ? (int)$_POST['first_action_id'] : false;
	$action_privacy_level = isset($_POST['action_privacy_level']) ? (int)$_POST['action_privacy_level'] : 63;
	$text = isset($_POST['text']) ? trim($_POST['text']) : '';
    $new_action_id = he_wall::new_action_id();
    
    $text = he_wall_format_text($text);
    
    if ( !$user->user_exists || !$text )
    {
        return false;
    }

    $replace_arr = array(
        $user->user_info['user_username'],
        $user->user_displayname,
        $text,
        he_wall::get_wall_link($wall_object, $wall_object_id) );
    
    $actions->actions_add($user, 'wallpost', $replace_arr, array(), 0, false, $action_object_owner, $wall_object_id, $action_privacy_level);
    
    he_wall::new_post_notify($wall_object, $wall_object_id, $new_action_id);
    
    $result = he_wall_actions_display($wall_object, $wall_object_id, $first_action_id);
}
elseif ( $task == 'share_photo' )
{
	$headers_sent = true;
	
    $new_action_id = he_wall::new_action_id();
    $text = isset($_POST['text']) ? trim($_POST['text']) : '';
    $privacy_level = isset($_POST['wall_action_privacy']) ? (int)$_POST['wall_action_privacy'] : 63;
    
    $upload_result = array();
    
    if ( $wall_object == 'group' )
    {
    	$upload_result = he_wall_group_photo_upload($wall_object_id, $text, $new_action_id);
    }
    elseif ( $wall_object == 'pages' )
    {
    	$upload_result = he_wall_pages_photo_upload($wall_object_id);
    }
    elseif ( isset($global_plugins['album']) && $setting['setting_he_wall_photo_sync'] )
    {
    	$upload_result = he_wall_photo_upload($text, $new_action_id);
    }
    else
    {
        $upload_result = he_wall_custom_photo_upload($new_action_id);
    }
    
    if ( $upload_result['result'] == 1 )
    {
    	$text = he_wall_format_text($text);
    	$replace_arr = array(
    	    $user->user_info['user_username'],
    	    $user->user_displayname,
    	    $text,
    	    he_wall::get_wall_link($wall_object, $wall_object_id) );
    	
    	$action_media = array();
    	$action_media[] = array(
            'media_link' => $url->url_base . 'wall_action.php?id=' . $new_action_id,
    	    'media_path' => $upload_result['media_path'],
            'media_width' => $upload_result['media_width'],
            'media_height' => $upload_result['media_height']
    	);
    	
    	$actions->actions_add($user, 'wallpostphoto', $replace_arr, $action_media, 0, true, $action_object_owner, $wall_object_id, $privacy_level);
        he_wall::new_post_notify($wall_object, $wall_object_id, $new_action_id);
    	
    	$result = array( 'result' => 1 );
    }
    else
    {
        $result = array( 'result' => 0, 'message' => $upload_result['error'] );
    }
}
elseif ( $task == 'share_music' )
{
	$headers_sent = true;
	
    $text = isset($_POST['text']) ? trim($_POST['text']) : '';
    $privacy_level = isset($_POST['wall_action_privacy']) ? (int)$_POST['wall_action_privacy'] : 63;
    $new_action_id = he_wall::new_action_id();    
    
    $upload_result = array();
    
    if ( isset($global_plugins['music']) && $setting['setting_he_wall_music_sync'] )
    {
    	$upload_result = he_wall_music_upload();
    }
    else
    {
    	$upload_result = he_wall_custom_music_upload($new_action_id);
    }
    
    if ( $upload_result['result'] == 1 )
    {
    	$text = he_wall_format_text($text);
        $player = '<div class="wall_music_container"><div id="action_music_' . $new_action_id . '" class="wall_music_player">' .
            '<script type="text/javascript">/* <![CDATA[ */' .
                'AudioPlayer.embed("action_music_' . $new_action_id . '", ' .
                    '{soundFile: "' . $upload_result['file_url'] . '",titles: "' . $upload_result['title'] . '"});' .
			'/* ]]> */</script>' .
			'</div></div>';
    
        $replace_arr = array(
            $user->user_info['user_username'],
            $user->user_displayname,
            $text,
            $upload_result['title'],
            $player,
            he_wall::get_wall_link($wall_object, $wall_object_id) );

        $actions->actions_add($user, 'wallpostmusic', $replace_arr, array(), 0, false, $action_object_owner, $wall_object_id, $privacy_level);

        he_wall::new_post_notify($wall_object, $wall_object_id, $new_action_id);        
        
        if ( isset($upload_result['music_id']) && $upload_result['music_id'] )
        {
        	he_wall::add_music_action_link($user->user_info['user_id'], $new_action_id, $upload_result['music_id']);
        }
        
        $result = array( 'result' => 1 );
    }
    else
    {
        $result = array( 'result' => 0, 'message' => $upload_result['error'] );
    }
}
elseif ( $task == 'new_actions' )
{
	$first_action_id = isset($_POST['first_action_id']) ? (int)$_POST['first_action_id'] : false;
	
    $result = he_wall_actions_display($wall_object, $wall_object_id, $first_action_id);
}
elseif ( $task == 'post_link' )
{
    $first_action_id = isset($_POST['first_action_id']) ? (int)$_POST['first_action_id'] : false;
    $action_privacy_level = isset($_POST['action_privacy_level']) ? (int)$_POST['action_privacy_level'] : 63;
    $text = isset($_POST['text']) ? trim($_POST['text']) : '';
    $link = isset($_POST['link']) ? trim($_POST['link']) : '';
    
    $new_action_id = he_wall::new_action_id();
    
    if ( strstr($link, 'https://') !== false )
    {
    	$link_label = str_replace('https://', '', $link);
    	$link_url = 'https://' . $link_label;
    }
    else
    {
    	$link_label = str_replace('http://', '', $link);
    	$link_url = 'http://' . $link_label;
    }
    
    $text = he_wall_format_text($text);
    $replace_arr = array(
        $user->user_info['user_username'],
        $user->user_displayname,
        $text,
        $link_url,
        $link_label,
        he_wall::get_wall_link($wall_object, $wall_object_id) );

    $actions->actions_add($user, 'wallpostlink', $replace_arr, array(), 0, false, $action_object_owner, $wall_object_id, $action_privacy_level);
    
    he_wall::new_post_notify($wall_object, $wall_object_id, $new_action_id);
    
    $result = he_wall_actions_display($wall_object, $wall_object_id, $first_action_id);
}
elseif ( $task == 'post_video' )
{
    $first_action_id = isset($_POST['first_action_id']) ? (int)$_POST['first_action_id'] : false;
    $action_privacy_level = isset($_POST['action_privacy_level']) ? (int)$_POST['action_privacy_level'] : 63;
    $text = isset($_POST['text']) ? trim($_POST['text']) : '';
    $video_provider = isset($_POST['video_provider']) ? trim($_POST['video_provider']) : '';
    $video_url = isset($_POST['video_url']) ? trim($_POST['video_url']) : '';
    
    if ( $video_provider != 'youtube' && $video_provider != 'vimeo' )
    {
    	$result = array( 'result' => 0, 'message' => SE_Language::get(690706072) );
    }
    elseif ( $video_url == '' )
    {
    	$result = array( 'result' => 0, 'message' => SE_Language::get(690706073) );
    }
    else
    {
    	$new_action_id = he_wall::new_action_id(); 
    	$pages_id = ( $wall_object == 'pages' ) ? $wall_object_id : 0; 
    	
    	if ( $pages_id && $video_provider == 'vimeo' )
    	{
    		$upload_result = he_wall_vimeo_video_upload($new_action_id, $video_url, $pages_id);
    	}
    	elseif ( $video_provider == 'vimeo' )
    	{
    		$upload_result = he_wall_vimeo_video_upload($new_action_id, $video_url);
    	}
    	elseif ( $video_provider == 'youtube' )
    	{
    	    if ( $pages_id )
            {
                $upload_result = he_wall_youtube_video_custom_upload($new_action_id, $video_url, $pages_id);
            }
            elseif ( isset($global_plugins['video']) && $setting['setting_he_wall_video_sync'] )
            {
            	$upload_result = he_wall_youtube_video_upload($new_action_id, $video_url, $action_privacy_level);
            }
            else
            {
                $upload_result = he_wall_youtube_video_custom_upload($new_action_id, $video_url);
            }
    	}
    	
    	if ( $upload_result['result'] == 1  )
    	{
    		$text = he_wall_format_text($text);
    		
    		$video_player = '<a href="javascript://" onclick="he_wall_show_player(this);" class="photo_cont video_thumb">' .
                '<span class="video_length">' . $upload_result['video_length'] . '</span>' .
                '<img class="recentaction_media" src="' . $upload_result['media_src'] . '"/></a>' .
                '<div class="video_cont display_none">' . $upload_result['player'] . '</div>';

    		$video_info = '<div class="video_info"><a href="' . $upload_result['video_url'] . '" class="video_title">' . $upload_result['title'] .
                '</a><div class="video_desc">' . he_wall_format_text($upload_result['description']) . '</div></div>';
    		
    		$replace_arr = array(
    		    $user->user_info['user_username'],
    		    $user->user_displayname,
    		    $text,
    		    $video_player,
    		    $video_info,
    		    he_wall::get_wall_link($wall_object, $wall_object_id) );

            $actions->actions_add($user, 'wallpostvideo', $replace_arr, array(), 0, false, $action_object_owner, $wall_object_id, $action_privacy_level);
            
            he_wall::new_post_notify($wall_object, $wall_object_id, $new_action_id);
            
            $result = he_wall_actions_display($wall_object, $wall_object_id, $first_action_id);
            $result['result'] = 1;
    	}
    	else
    	{
    		$result = $upload_result; 
    	}
    }
}
elseif ( $task == 'paging' )
{
	$action_id = $_GET['action_id'];
	$count = $_GET['count'];
	
	$total_comments = he_wall::total_comments( $action_id );
	
	$per_click = $setting['setting_he_wall_comments_per_page'];
	
	$per_click = ($total_comments-$count-$per_click < 0) ? $total_comments-$count : $setting['setting_he_wall_comments_per_page'];
	 
	$result_2 = he_wall::get_paging_comments( $action_id, ($total_comments-$count-$per_click), $per_click);
	
	$count = $result_2['count'];
	$action_comments = $result_2['action_comments'];
	ksort( $action_comments );
	$smarty->assign('action_comments', $action_comments);
	$smarty->assign('action_id', $action_id);
	
	$result = array( 'action_id' => $action_id, 'count' => $count, 'html' => he_wall_include_footer_logic('he_wall_comments.tpl') );
}


// CONSTRUCT AND OUTPUT JSON
if ( !$headers_sent )
{
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
    header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    header("Pragma: no-cache"); // HTTP/1.0
    header("Content-Type: application/json");
}

echo json_encode($result);


exit();
?>