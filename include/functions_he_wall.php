<?php

function he_wall_include_footer_logic($page)
{
    global $smarty, $setting, $user, $owner, $url, $misc, $datetime, $database, $admin, $ads, $se_javascript, $lang_packlist, $global_plugins, $global_timezone;
    
    // GET LANGUAGES AVAILABLE IF NECESSARY
    if($setting['setting_lang_anonymous'] == 1 || ($setting['setting_lang_allow'] == 1 && $user->user_exists != 0))
    {
        $lang_packlist_raw = SECore::getLanguages();
        //$lang_packlist = SELanguage::list_packs();
        ksort($lang_packlist_raw);
        $lang_packlist = array_values($lang_packlist_raw);
    }

    $smarty->assign_by_ref('url', $url);
    $smarty->assign_by_ref('misc', $misc);
    $smarty->assign_by_ref('datetime', $datetime);
    $smarty->assign_by_ref('database', $database);
    $smarty->assign_by_ref('admin', $admin);
    $smarty->assign_by_ref('user', $user);
    $smarty->assign_by_ref('owner', $owner);
    $smarty->assign_by_ref('ads', $ads);
    $smarty->assign_by_ref('setting', $setting);
    $smarty->assign_by_ref('se_javascript', $se_javascript);
    $smarty->assign('lang_packlist', $lang_packlist);
    $smarty->assign('global_plugins', $global_plugins);
    $smarty->assign('global_page', $page);
    $smarty->assign('global_timezone', $global_timezone);
    $smarty->assign('global_language', SELanguage::info('language_id'));
    
    return $smarty->fetch($page, null, null, false);
}

function frontend_he_wall_display( $params )
{
    global $user, $setting, $smarty;
    
    $wall_object = $params['object'];
    $wall_object_id = (int)$params['object_id'];
    $where_clause = '';
    
    $setting_actions_visibility = 0;
    $setting_actions_actionsper = $setting['setting_actions_actionsonprofile'];
    $allow_post = 0;
    
    if ( !$setting['setting_he_wall_guest_view'] && $user->level_info['level_wall_allowed'] == 0 )
    {
        return '<center>' . SE_Language::get(690706102) . '</center>';
    }
    
    if ( $wall_object == 'user' )
    {
        $owner = new se_user(array( $wall_object_id) );
        $privacy_max = $owner->user_privacy_max($user);
        $allow_post = (int)( $privacy_max == 1 || $privacy_max == 2 );
        
        $actiontype_ids = he_wall::get_actiontype_ids();
        $actiontype_ids_str = implode(',', $actiontype_ids);
        
        $where_clause .= "
             IF ( 
                se_actions.action_actiontype_id IN ($actiontype_ids_str),
                ( se_actions.action_object_owner='user' AND se_actions.action_object_owner_id='{$owner->user_info['user_id']}'),
                se_actions.action_user_id='{$owner->user_info['user_id']}'
             )
            ";
    }
    elseif ( $wall_object == 'userhome' )
    {
        $owner = new se_user(array($wall_object_id));
        $privacy_max = $owner->user_privacy_max($user);
        $allow_post = (int)( $privacy_max == 1 || $privacy_max == 2 );
        
        $setting_actions_visibility = 10;
        $setting_actions_actionsper = $setting['setting_actions_actionsperuser'];
        
        $where_clause .= "1";
    }
    elseif ( $wall_object == 'group' )
    {
        $owner = new se_group($user->user_info['user_id'], $wall_object_id);
        $privacy_max = $owner->user_rank;
        $allow_post = (int)( $privacy_max != -1 );
        
        $setting_actions_visibility = 10;
        $setting_actions_actionsper = $setting['setting_actions_actionsonprofile'];
        
        $where_clause .= "se_actions.action_object_owner='group' AND se_actions.action_object_owner_id='$wall_object_id'";
    }
    elseif ( $wall_object == 'pages' )
    {
        $where_clause .= " se_actions.action_object_owner='pages' AND se_actions.action_object_owner_id='$wall_object_id'";
    }
    else
    {
        $where_clause .= '';
    }
    
    $hidden_action_ids = he_wall::get_hidden_actions($user->user_info['user_id']);
    
    if ( $hidden_action_ids )
    {
        $where_clause .= ( $where_clause != '' ) ? " AND " : '';
        $where_clause .= "se_actions.action_id NOT IN (" . implode(',', $hidden_action_ids) .")";
    }
    
    $wall_actions = he_wall::actions_display($setting_actions_visibility, $setting_actions_actionsper, $where_clause);
    
    $action_owner_ids = array();
    foreach ($wall_actions as $key=>$wall_action) {
        $action_owner_ids[] = $wall_action['action_user_id'];
    }
    
    $action_owner_ids = array_unique($action_owner_ids);
    $action_owners = he_wall::get_users($action_owner_ids);    
    
    $action_ids = array();
    foreach ($wall_actions as $key=>$wall_action)
    {
        $action_ids[] = $wall_action['action_id'];
        $wall_actions[$key]['owner'] = $action_owners[$wall_action['action_user_id']];
    }
    
    $action_likes = he_wall::get_likes($action_ids);
    $action_comments = he_wall::get_comments($action_ids);
    
    //Check Privacy    
    $privacy_options = he_wall::get_privacy_options($wall_object, $wall_object_id);
    
    $smarty->assign('wall_uid', uniqid());
    $smarty->assign('js_action_ids', json_encode($action_ids));
    $smarty->assign('wall_actions', $wall_actions);
    $smarty->assign('wall_action_count', count($wall_actions));
    $smarty->assign('action_likes', $action_likes);
    $smarty->assign('action_comments', $action_comments);
    $smarty->assign('wall_object', $wall_object);
    $smarty->assign('wall_object_id', $wall_object_id);
    $smarty->assign('allow_post', $allow_post);
    
    $smarty->assign('privacy_options', $privacy_options);
    
    return he_wall_include_footer_logic('wall.tpl');
}

function he_wall_recent_activity()
{
    global $smarty;
    
    $smarty->assign('actions', array());
}

function he_wall_actions_display( $wall_object, $wall_object_id, $first_id = false, $last_id = false )
{
    global $user, $setting, $smarty;
        
    $setting_actions_visibility = 0;
    $setting_actions_actionsper = $setting['setting_actions_actionsonprofile'];
    
    if ( $wall_object == 'user' )
    {
        $owner = new se_user(array( $wall_object_id ));
        
        $actiontype_ids = he_wall::get_actiontype_ids();
        $actiontype_ids_str = implode(',', $actiontype_ids);
        
        $where_clause .= "
             IF ( 
                se_actions.action_actiontype_id IN ($actiontype_ids_str),
                ( se_actions.action_object_owner='user' AND se_actions.action_object_owner_id='{$owner->user_info['user_id']}'),
                se_actions.action_user_id='{$owner->user_info['user_id']}'
             )
            ";
    }
    elseif ( $wall_object == 'userhome' )
    {
        $owner = new se_user(array($wall_object_id));
        
        $setting_actions_visibility = 10;
        $setting_actions_actionsper = $setting['setting_actions_actionsperuser'];
        
        $where_clause .= "1";
    }
    elseif ( $wall_object == 'group' )
    {
        $owner = new se_group($user->user_info['user_id'], $wall_object_id);
        $privacy_max = $owner->user_rank;
        $allow_post = (int)( $privacy_max != -1 );
        
        $setting_actions_visibility = 10;
        $setting_actions_actionsper = $setting['setting_actions_actionsonprofile'];
        
        $where_clause .= "se_actions.action_object_owner='group' AND se_actions.action_object_owner_id='$wall_object_id'";
    }
    elseif ( $wall_object == 'pages' )
    {
        $setting_actions_visibility = 10;
        
        $where_clause .= " se_actions.action_object_owner='pages' AND se_actions.action_object_owner_id='$wall_object_id'";
    }
    else
    {
        $where_clause .= '';
    }
    
    $hidden_action_ids = he_wall::get_hidden_actions($user->user_info['user_id']);
    
    if ( $hidden_action_ids )
    {
        $where_clause .= ( $where_clause != '' ) ? " AND " : '';
        $where_clause .= " se_actions.action_id NOT IN (" . implode(',', $hidden_action_ids) .")";
    }
    
    $wall_actions = he_wall::actions_display($setting_actions_visibility, $setting_actions_actionsper, $where_clause, $last_id, $first_id);
    
    $action_owner_ids = array();
    foreach ($wall_actions as $key=>$wall_action) {
        $action_owner_ids[] = $wall_action['action_user_id'];
    }
    
    $action_owner_ids = array_unique($action_owner_ids);
    $action_owners = he_wall::get_users($action_owner_ids);    
    
    $action_ids = array();
    foreach ($wall_actions as $key=>$wall_action)
    {
        $action_ids[] = $wall_action['action_id'];
        $wall_actions[$key]['owner'] = $action_owners[$wall_action['action_user_id']]; 
    }
    
    $action_likes = he_wall::get_likes($action_ids);
    $action_comments = he_wall::get_comments($action_ids);

    $video_actiontype_id = he_wall::video_actiontype_id();
    
    $smarty->assign('wall_actions', $wall_actions);
    $smarty->assign('action_likes', $action_likes);
    $smarty->assign('action_comments', $action_comments);
    $smarty->assign('video_actiontype_id', $video_actiontype_id);
    
    return array( 'action_ids' => $action_ids, 'html' => he_wall_include_footer_logic('he_wall_actions.tpl') );
}

function he_wall_delete_user( $user_id )
{
    if ( $user_id == false )
    {
        return false;
    }
    
    $sql = he_database::placeholder('DELETE FROM `se_he_wall_comment` WHERE `author_id`=?', $user_id);
    he_database::query($sql);

    $sql = he_database::placeholder('DELETE FROM `se_he_hidden_action` WHERE `user_id`=?', $user_id);
    he_database::query($sql);
    
    $sql = he_database::placeholder('DELETE FROM `se_he_wall_like` WHERE `user_id`=?', $user_id);
    he_database::query($sql);
    
    he_wall::delete_user_uploads($user_id);
}

function delete_he_wall_action()
{
    $sql = "DELETE FROM se_he_wall_comment WHERE action_id NOT IN (SELECT action_id FROM se_actions)";
    he_database::query($sql);
    
    $sql = "DELETE FROM se_he_wall_hidden_action WHERE action_id NOT IN (SELECT action_id FROM se_actions)";
    he_database::query($sql);
    
    $sql = "DELETE FROM se_he_wall_like WHERE action_id NOT IN (SELECT action_id FROM se_actions)";
    he_database::query($sql);
    
    he_wall::delete_action_uploads();
}

function delete_he_wall_action_info()
{
    global $user, $page;
    
    $task = isset($_GET['task']) ? $_GET['task'] : false;
    $task = ( !$task && isset($_POST['task']) ) ? $_POST['task'] : $task; 
    
    if ( $page == 'music_ajax' && $task == 'deletesong' )
    {
        $music_id = isset($_POST['music_id']) ? (int)$_POST['music_id'] : 0;
        
        he_wall::delete_music_action($user->user_info['user_id'], $music_id);
    }
    
    if ( $page == 'user_video' && $task == 'delete' )
    {
        $video_id = isset($_POST['video_id']) ? (int)$_POST['video_id'] : 0;
        
        he_wall::delete_video_action($user->user_info['user_id'], $video_id);
    }
    
    if ( $page == 'album_ajax' && $task == 'album_delete' )
    {
        $album_id = isset($_POST['album_id']) ? (int)$_POST['album_id'] : 0;

        he_wall::delete_wall_album($user->user_info['user_id'], $album_id);
    }
    
    if ( $page == 'user_album' && $task == 'delete' )
    {
        $album_id = isset($_GET['album_id']) ? (int)$_GET['album_id'] : 0;
        
        he_wall::delete_wall_album($user->user_info['user_id'], $album_id);
    }

    if ( $page == 'user_album_update' && $task == 'doupdate' )
    {
        $media_ids = isset($_POST['delete']) ? $_POST['delete'] : 0;
        
        he_wall::delete_wall_media($user->user_info['user_id'], $_POST['delete']);
    }
    
    if ( $page == 'group_album_file' && $task == 'media_delete' )
    {
        $group_id = isset($_GET['group_id']) ? (int)$_GET['group_id'] : 0;
        $media_id = isset($_GET['groupmedia_id']) ? (int)$_GET['groupmedia_id'] : 0;
        
        he_wall::delete_group_media($user->user_info['user_id'], $group_id, $media_id);
    }
    
    if ( $page == 'user_group_edit' && $task == 'delete_do' )
    {
        $group_id = isset($_POST['group_id']) ? (int)$_POST['group_id'] : 0;

        he_wall::delete_group_actions($group_id);
    }
}

function action_privacy_wall( $args )
{
    global $user;

    $args['actions_query'] .= " WHEN se_actions.action_object_owner='userhome' THEN
            CASE
              WHEN se_actions.action_user_id='{$user->user_info['user_id']}'
                THEN TRUE
              WHEN ((se_actions.action_object_privacy & @SE_PRIVACY_REGISTERED) AND '{$user->user_exists}'<>0)
                THEN TRUE
              WHEN ((se_actions.action_object_privacy & @SE_PRIVACY_ANONYMOUS) AND '{$user->user_exists}'=0)
                THEN TRUE
              WHEN ((se_actions.action_object_privacy & @SE_PRIVACY_SELF) AND se_actions.action_object_owner_id='{$user->user_info['user_id']}')
                THEN TRUE
              WHEN ((se_actions.action_object_privacy & @SE_PRIVACY_FRIEND) AND (SELECT TRUE FROM se_friends WHERE friend_user_id1=se_actions.action_object_owner_id AND friend_user_id2='{$user->user_info['user_id']}' AND friend_status='1' LIMIT 1))
                THEN TRUE
              WHEN ((se_actions.action_object_privacy & @SE_PRIVACY_SUBNET) AND '{$user->user_exists}'<>0 AND (SELECT TRUE FROM se_users WHERE user_id=se_actions.action_object_owner_id AND user_subnet_id='{$user->user_info['user_subnet_id']}' LIMIT 1))
                THEN TRUE
              WHEN ((se_actions.action_object_privacy & @SE_PRIVACY_FRIEND2) AND (SELECT TRUE FROM se_friends AS friends_primary LEFT JOIN se_users ON friends_primary.friend_user_id1=se_users.user_id LEFT JOIN se_friends AS friends_secondary ON friends_primary.friend_user_id2=friends_secondary.friend_user_id1 WHERE friends_primary.friend_user_id1=se_actions.action_object_owner_id AND friends_secondary.friend_user_id2='{$user->user_info['user_id']}' AND se_users.user_subnet_id='{$user->user_info['user_subnet_id']}' LIMIT 1))
                THEN TRUE
              ELSE FALSE
            END";
}

function he_wall_custom_music_upload($new_action_id )
{
    global $user, $url, $setting;
    
    $max_filesize = (int)$setting['setting_he_wall_music_filesize']*1024;
    
    $file_exts = explode(',', $setting['setting_he_wall_music_exts']);
    $file_exts = array_map('trim', $file_exts);
    $file_types = explode(',', $setting['setting_he_wall_music_mimes']);
    $file_types = array_map('trim', $file_types);
    
    $new_upload = new se_upload();
    $he_upload = new he_upload($user->user_info['user_id'], 'wall_music'); 
    
    $new_upload->new_upload('wall_music', $max_filesize, $file_exts, $file_types);
    
    if ( $new_upload->is_error )
    {
        $result = array( 'result' => 0, 'error' => SE_Language::get($new_upload->is_error) );
    }
    else
    {
        $title = $new_upload->file_name;
        $upload_id = $he_upload->new_upload($new_action_id, 'wall_music', $title);

        $filename = "{$he_upload->instance_type}_{$upload_id}.{$new_upload->file_ext}";
        $new_upload->upload_file("./uploads_wall/$filename");
        
        if ( $new_upload->is_error )
        {
            $he_upload->delete_upload($upload_id);
            
            $result = array( 'result' => 0, 'error' => SE_Language::get($new_upload->is_error) );
        }
        else
        {
            $he_upload->save_upload($upload_id, $filename);
            
            $file_url = $url->url_base . 'uploads_wall/' . $filename;
                
            $result = array( 'result' => 1, 'file_url' => $file_url, 'title' => $title );
        }
    }
    
    return $result;
}

function he_wall_music_upload()
{
    global $user, $url;
    
    if ( !$user->level_info['level_music_allow'] )
    {
        return array( 'result' => 0, 'error' => SE_Language::get(690706066) );
    }
    
    $music = new se_music($user->user_info['user_id']);
    $music_numleft = ( $user->level_info['level_music_maxnum'] - $music->music_total() );    

    // GET TOTAL SPACE USED
    $space_used = $music->music_space();
    
    if ( $user->level_info['level_music_storage'] )
    {
        $space_left = $user->level_info['level_music_storage'] - $space_used;
    }
    else
    {
        $space_left = ( $dfs = disk_free_space("/") ? $dfs : pow(2, 32) );
    }
    
    $file_result = $music->music_upload('wall_music', $space_left);
    
    if ( !$file_result['is_error'] )
    {
        $file_path = $url->url_userdir($user->user_info['user_id']) . $file_result['music_id'] . '.' . $file_result['music_ext'];
        $file_url = str_replace('./', $url->url_base, $file_path);
        
        // UPDATE LAST UPDATE DATE (SAY THAT 10 TIMES FAST)
        $user->user_lastupdate();
        
        $result = array( 'result' => 1, 'file_url' => $file_url, 'title' => $file_result['music_title'], 'music_id' => $file_result['music_id'] );
    }
    else
    {
        $error_msg = sprintf(SE_Language::_get($file_result['is_error']), $file_result['file_name']);
        
        $result = array( 'result' => 0, 'error' => $error_msg );
    }
    
    return $result;
}

function he_wall_custom_photo_upload( $new_action_id )
{
    global $user, $misc, $setting;
    
    $max_filesize = (int)$setting['setting_he_wall_photo_filesize'] * 1024;
    
    $file_exts = explode(',', $setting['setting_he_wall_photo_exts']);
    $file_exts = array_map('trim', $file_exts);
    $file_types = explode(',', $setting['setting_he_wall_photo_mimes']);
    $file_types = array_map('trim', $file_types);
    
    $width = (int)$setting['setting_he_wall_photo_width'];
    $height = (int)$setting['setting_he_wall_photo_height'];
    $thumb_width = (int)$setting['setting_he_wall_photo_thumb_width'];
    $thumb_height = (int)$setting['setting_he_wall_photo_thumb_height'];
    
    $new_upload = new se_upload();
    $he_upload = new he_upload($user->user_info['user_id'], 'wall_photo'); 
    
    $new_upload->new_upload('wall_photo', $max_filesize, $file_exts, $file_types);
    
    if ( $new_upload->is_error )
    {
        $result = array( 'result' => 0, 'error' => SE_Language::get($new_upload->is_error) );
    }
    else
    {
        $upload_id = $he_upload->new_upload($new_action_id);
        
        $file_name = "{$he_upload->instance_type}_{$upload_id}.{$new_upload->file_ext}";
        $file_thumb = "{$he_upload->instance_type}_{$upload_id}_thumb.{$new_upload->file_ext}";
        $file_thumb_path = "./uploads_wall/$file_thumb";
        
        $new_upload->upload_photo($file_thumb_path, $thumb_width, $thumb_height);
        $new_upload->upload_photo("./uploads_wall/$file_name", $width, $height);
        
        $media_width = $misc->photo_size($file_thumb_path, $thumb_width, $thumb_height, "w");
        $media_height = $misc->photo_size($file_thumb_path, $thumb_width, $thumb_height, "h");
        
        if ( $new_upload->is_error )
        {
            $he_upload->delete_upload($upload_id);
            
            $result = array( 'result' => 0, 'error' => SE_Language::get($new_upload->is_error) );
        }
        else
        {  
            $he_upload->save_upload($upload_id, $file_name);
            
            $result = array( 'result' => 1, 'media_path' => $file_thumb_path, 'media_width' => $media_width, 'media_height' => $media_height );
        }
    }
    
    return $result;
}

function he_wall_pages_photo_upload( $pages_id )
{
    global $user, $misc, $setting;

    $max_filesize = 8*1024*1024;
    $file_exts = array('jpg', 'jpeg', 'gif', 'png');
    $file_types = array('image/jpeg', 'image/jpg', 'image/jpe', 'image/pjpeg', 'image/pjpg', 'image/x-jpeg', 'x-jpg', 
        'image/gif', 'image/x-gif', 'image/png', 'image/x-png');
    
    $width = 500;//TODO
    $height = 500;
    $thumb_width = 70;
    $thumb_height = 70;
    
    $new_upload = new se_upload();
    $he_upload = new he_upload($user->user_info['user_id'], 'pages_photo'); 
    
    $new_upload->new_upload('wall_photo', $max_filesize, $file_exts, $file_types);
    
    if ( $new_upload->is_error )
    {
        $result = array( 'result' => 0, 'error' => SE_Language::get($new_upload->is_error) );
    }
    else
    {
        $upload_id = $he_upload->new_upload($pages_id);
        
        $file_name = "{$he_upload->instance_type}_{$upload_id}.{$new_upload->file_ext}";
        $file_thumb = "{$he_upload->instance_type}_{$upload_id}_thumb.{$new_upload->file_ext}";
        $file_thumb_path = "./uploads_pages/$file_thumb";
        
        $new_upload->upload_photo($file_thumb_path, $thumb_width, $thumb_height);
        $new_upload->upload_photo("./uploads_pages/$file_name", $width, $height);
        
        $media_width = $misc->photo_size($file_thumb_path, $thumb_width, $thumb_height, "w");
        $media_height = $misc->photo_size($file_thumb_path, $thumb_width, $thumb_height, "h");
        
        if ( $new_upload->is_error )
        {
            $he_upload->delete_upload($upload_id);
            
            $result = array( 'result' => 0, 'error' => SE_Language::get($new_upload->is_error) );
        }
        else
        {  
            $he_upload->save_upload($upload_id, $file_name);
            
            $result = array( 'result' => 1, 'media_path' => $file_thumb_path, 'media_width' => $media_width, 'media_height' => $media_height );
        }
    }
    
    return $result;
}

function he_wall_photo_upload( $media_desc, $new_action_id )
{
    global $user, $url, $misc, $setting;
    
    if ( !$user->level_info['level_album_allow'] )
    {
        return array( 'result' => 0, 'error' => SE_Language::get(690706067) );
    }
    
    $album_id = he_wall::get_wall_album($user->user_info['user_id']);
    
    if ( !$album_id )
    {
        //create new album
        $album_id = he_wall::create_wall_album($user->user_info['user_id']);
    }

    $album_info = he_wall::get_wall_album_info($album_id);
    
    // SET ALBUM
    $album = new se_album($user->user_info['user_id']);

    // GET TOTAL SPACE USED
    $space_used = $album->album_space();
    
    if ( $user->level_info['level_album_storage'] )
    {
        $space_left = $user->level_info['level_album_storage'] - $space_used;
    }
    else
    {
        $space_left = ( $dfs = disk_free_space("/") ? $dfs : pow(2, 32) );
    } 

    $new_album_cover = '';

    $file_result = $album->album_media_upload('wall_photo', $album_id, $space_left);
    
    if ( !$file_result['is_error'] )
    {
        $new_album_cover = $file_result['media_id'];
        $media_path = $url->url_base . substr($url->url_userdir($user->user_info['user_id']), 2) . $file_result['media_id'] . "_thumb.jpg";
        $local_media_path = substr($url->url_userdir($user->user_info['user_id']), 2) . $file_result['media_id']."_thumb.jpg";

        if ( file_exists('./' . $local_media_path) )
        { 
            $thumb_width = (int)$setting['setting_he_wall_photo_thumb_width'];
            $thumb_height = (int)$setting['setting_he_wall_photo_thumb_height'];
    
            $media_width = $misc->photo_size($local_media_path, $thumb_width, $thumb_height, "w");
            $media_height = $misc->photo_size($local_media_path, $thumb_width, $thumb_height, "h");
             
            $result = array( 'result' => 1, 'media_path' => $media_path, 'media_width' => $media_width, 'media_height' => $media_height );
        }
        else
        {
             $result = array( 'result' => 0, 'error' => './' . $local_media_path );
        }
    
        $new_album_cover = ( $album_info['album_cover'] ) ? $album_info['album_cover'] : $new_album_cover;
        
        he_wall::update_wall_album($album_id, $new_album_cover);
        he_wall::update_wall_album_media($file_result['media_id'], $media_desc);
        he_wall::add_wall_album_media($new_action_id, $file_result['media_id']);
        
        // UPDATE LAST UPDATE DATE (SAY THAT 10 TIMES FAST)
        $user->user_lastupdate();
    }
    else
    {
        $error_msg = sprintf(SE_Language::_get($file_result['is_error']), $file_result['file_name']);
        
        $result = array( 'result' => 0, 'error' => $error_msg );
    }
    
    return $result;
}

function he_wall_group_photo_upload( $group_id, $media_desc, $new_action_id )
{
    global $user, $url, $misc, $setting;
    
    $group = new se_group($user->user_info['user_id'], $group_id);
    
    // CHECK IF USER IS ALLOWED TO UPLOAD PHOTOS
    $privacy_max = $group->group_privacy_max($user);
    
    if ( !($privacy_max & $group->group_info['group_privacy']) || !($privacy_max & $group->group_info['group_upload']) )
    {
        $result = array( 'result' => 0, 'error' => SE_Language::get(690706101) );
    }
    
    $album_id = he_wall::get_group_album($group_id);
    
    // GET TOTAL SPACE USED
    $space_used = $group->group_media_space();
    
    if ( $group->groupowner_level_info['level_group_album_storage'] )
    {
        $space_left = $group->groupowner_level_info['level_group_album_storage'] - $space_used;
    }
    else
    {
        $space_left = ( $dfs = disk_free_space("/") ? $dfs : pow(2, 32) );
    }
    
    $update_album = 0;
    $file_result = $group->group_media_upload('wall_photo', $album_id, $space_left);
    
    if ( $file_result['is_error'] == 0 )
    {
        $media_path = str_replace('./', '', $group->group_dir($group->group_info['group_id']).$file_result['groupmedia_id']."_thumb.jpg");
        $media_link = "group_album_file.php?group_id={$group->group_info['group_id']}&groupmedia_id={$file_result['groupmedia_id']}";
        
        if ( file_exists($media_path) )
        {
            $thumb_width = (int)$setting['setting_he_wall_photo_thumb_width'];
            $thumb_height = (int)$setting['setting_he_wall_photo_thumb_height'];
            
            $media_width = $misc->photo_size($media_path, $thumb_width, $thumb_height, "w");
            $media_height = $misc->photo_size($media_path, $thumb_width, $thumb_height, "h");
            
            $result = array( 'result' => 1, 'media_path' => $media_path, 'media_width' => $media_width, 'media_height' => $media_height );
        }
        
        // UPDATE ALBUM UPDATED DATE
        he_wall::update_group_album($album_id);
        he_wall::update_group_album_media($file_result['groupmedia_id'], $media_desc);
        he_wall::add_group_album_media($new_action_id, $file_result['groupmedia_id']);
        
        // UPDATE LAST UPDATE DATE (SAY THAT 10 TIMES FAST)
        $group->group_lastupdate();
    }
    else
    {
        $error_msg = sprintf(SE_Language::_get($file_result['is_error']), $file_result['file_name']);
        
        $result = array( 'result' => 0, 'error' => $error_msg );
    }
    
    return $result;
}

function he_wall_vimeo_video_upload( $new_action_id, $video_url, $pages_id = 0 )
{
    global $setting, $user, $url;
    
    $video_url_arr = explode('vimeo.com/', $video_url);
    $video_code = $video_url_arr[1];
    
    $video_info_json = @file_get_contents("http://vimeo.com/api/v2/video/{$video_code}.json");
    
    if ( $video_info_json === false )
    {
        return array( 'result' => 0, 'message' => SE_Language::get(690706074) );
    }
    
    $video_info = ( $video_info_json ) ? json_decode($video_info_json, true) : array();
    
    if ( !$video_info )
    {
    	return array( 'result' => 0, 'message' => SE_Language::get(690706074) );
    }
    
    $video_id = isset($video_info[0]['id']) ? $video_info[0]['id'] : '';
    $video_title = isset($video_info[0]['title']) ? $video_info[0]['title'] : '';
    $video_description = isset($video_info[0]['description']) ? $video_info[0]['description'] : '';
    $video_duration = isset($video_info[0]['duration']) ? $video_info[0]['duration'] :0;
    $video_thumb_src = isset($video_info[0]['thumbnail_medium']) ? $video_info[0]['thumbnail_medium'] : '';
    
    $instance_id = ( $pages_id ) ? $pages_id : $new_action_id;
    $instance_type = ( $pages_id ) ? 'pages_video' : 'wall_video';
    $upload_dir = ( $pages_id ) ? './uploads_pages/' : './uploads_wall/';
    
    $he_upload = new he_upload($user->user_info['user_id'], $instance_type);
    
    $upload_id = $he_upload->new_upload($instance_id);
    $file_name = "{$he_upload->instance_type}_{$upload_id}.jpg";
    $file_path = $upload_dir . $file_name;
    
    $video_result = he_wall_update_video_thumb($video_thumb_src, $file_path);
    $he_upload->save_upload($upload_id, $file_name);
    
    $video_result['result'] = 1;
    $video_result['title'] = $video_title;
    $video_result['description'] = strip_tags($video_description);
    $video_result['media_src'] = $url->url_base . substr($file_path, 2);
    $video_result['video_url'] = $url->url_base . 'wall_action.php?id=' . $new_action_id;
    $video_result['video_length'] = he_wall_format_duration($video_duration);
    
    if ( $pages_id )
    {
    	$pages_video = new he_pages_video($pages_id, $user->user_info['user_id']);
    	$video_result['video_id'] = $pages_video->video_add($video_id, 'vimeo', $video_title, $video_result['description'], $file_name, $video_result['video_length']);
    }
    
    $width = $setting['setting_he_wall_video_player_width'];
    $height = $setting['setting_he_wall_video_player_height'];
    
    $video_result['player'] = '<object width="' . $width .' " height="' . $height . '"><param name="wmode" value="transparent"></param><param name="allowfullscreen" value="true"></param><param name="allowscriptaccess" value="always"></param><param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=' . $video_id . '&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=1&amp;color=00adef&amp;fullscreen=1"></param><embed wmode="transparent" src="http://vimeo.com/moogaloop.swf?clip_id=' . $video_id . '&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=1&amp;color=00adef&amp;fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="' . $width .' " height="' . $height . '"></embed></object>';
    
    return $video_result;
}

function he_wall_youtube_video_custom_upload( $new_action_id, $video_url, $pages_id = 0 )
{
    global $setting, $user, $url;

    $video_query = parse_url($video_url);
    
    if ( !isset($video_query['query']) || !$video_query['query'] )
    {
        return array( 'result' => 0, 'message' => SE_Language::get(690706074) );
    }
    
    $video_vars = array();
    parse_str($video_query['query'], $video_vars);
    
    if ( !isset($video_vars['v']) || !$video_vars['v'] )
    {
        return array( 'result' => 0, 'message' => SE_Language::get(690706074) );
    }
    
    $video_code = $video_vars['v'];
    
    $video_info_json = @file_get_contents("http://gdata.youtube.com/feeds/api/videos/{$video_code}?alt=json");
    
    if ( $video_info_json === false )
    {
        return array( 'result' => 0, 'message' => SE_Language::get(690706074) );
    }
    
    $video_info = ( $video_info_json ) ? json_decode($video_info_json, true) : array();
    
    $video_title = isset($video_info['entry']['title']['$t']) ? $video_info['entry']['title']['$t'] : '';
    $video_description = isset($video_info['entry']['content']['$t']) ? $video_info['entry']['content']['$t'] : '';
    $video_duration = isset($video_info['entry']['media$group']['yt$duration']['seconds']) ? $video_info['entry']['media$group']['yt$duration']['seconds'] : '';
    $video_thumb = isset($video_info['entry']['media$group']['media$thumbnail'][3]) ? $video_info['entry']['media$group']['media$thumbnail'][3] : '';
        
    $instance_id = ( $pages_id ) ? $pages_id : $new_action_id;
    $instance_type = ( $pages_id ) ? 'pages_video' : 'wall_video';
    $upload_dir = ( $pages_id ) ? './uploads_pages/' : './uploads_wall/';
    
    $he_upload = new he_upload($user->user_info['user_id'], $instance_type);
    
    $upload_id = $he_upload->new_upload($instance_id);
    $file_name = "{$he_upload->instance_type}_{$upload_id}.jpg";
    $file_path = $upload_dir . $file_name;
    
    $video_thumb_src = $video_thumb['url'];
    $thumb_dimensions = array( $video_thumb['width'], $video_thumb['height'] );
    
    $video_result = he_wall_update_video_thumb($video_thumb_src, $file_path, $thumb_dimensions);
    $he_upload->save_upload($upload_id, $file_name);
    
    $video_result['result'] = 1;
    $video_result['title'] = $video_title;
    $video_result['description'] = strip_tags($video_description);
    $video_result['media_src'] = $url->url_base . substr($file_path, 2);
    $video_result['video_url'] = $url->url_base . 'wall_action.php?id=' . $new_action_id;
    $video_result['video_length'] = he_wall_format_duration($video_duration);
    
    if ( $pages_id )
    {
        $pages_video = new he_pages_video($pages_id, $user->user_info['user_id']);
        $video_result['video_id'] = $pages_video->video_add($video_code, 'youtube', $video_title, $video_result['description'], $file_name, $video_result['video_length']);
    }    
    
    $width = $setting['setting_he_wall_video_player_width'];
    $height = $setting['setting_he_wall_video_player_height'];
    
    $video_result['player'] = '<object width="' . $width . '" height="' . $height . '"><param name="wmode" value="transparent"></param><param name="movie" value="http://www.youtube.com/v/' . $video_code . '&hl=en&fs=1"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed wmode="transparent" src="http://www.youtube.com/v/' . $video_code . '&hl=en&fs=1" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="' . $width . '" height="' . $height . '"></embed></object>';
    
    return $video_result;
}

function he_wall_youtube_video_upload( $new_action_id, $video_url, $video_privacy_level )
{
    global $setting, $user, $url;
    
    if ( !$user->level_info['level_youtube_allow'] )
    {
        return array( 'result' => 0, 'message' => SE_Language::get(690706078) );
    }
    
    $video = new se_video($user->user_info['user_id'], 0);
    
    $total_videos = $video->video_total();
    
    if( $total_videos >= $user->level_info['level_video_maxnum'] )
    {
        return array( 'result' => 0, 'message' => SE_Language::get(5500201) );
    }
    
    // GET PRIVACY SETTINGS
    $level_video_privacy = unserialize($user->level_info['level_video_privacy']);
    rsort($level_video_privacy);
    
    $level_video_comments = unserialize($user->level_info['level_video_comments']);
    rsort($level_video_comments);
    
    $video->video_info = array(
        'video_title' => '',
        'video_desc' => '',
        'video_search' => true,
        'video_privacy' => $video_privacy_level,
        'video_comments' => $level_video_comments[0] );
    
    $video_code = extract_youtube_code($video_url);
    
    $video_info_json = @file_get_contents("http://gdata.youtube.com/feeds/api/videos/{$video_code}?alt=json");
    
    if ( $video_info_json === false )
    {
        return array( 'result' => 0, 'message' => SE_Language::get(690706074) );
    }
    
    $video_info = ( $video_info_json ) ? json_decode($video_info_json, true) : array();
    
    $video_title = isset($video_info['entry']['title']['$t']) ? $video_info['entry']['title']['$t'] : '';
    $video_description = isset($video_info['entry']['content']['$t']) ? $video_info['entry']['content']['$t'] : '';
    $video_duration = isset($video_info['entry']['media$group']['yt$duration']['seconds']) ? $video_info['entry']['media$group']['yt$duration']['seconds'] : '';
    $video_thumb = isset($video_info['entry']['media$group']['media$thumbnail'][3]) ? $video_info['entry']['media$group']['media$thumbnail'][3] : '';
 
    $video_thumb_src = $video_thumb['url'];
    $thumb_dimensions = array( $video_thumb['width'], $video_thumb['height'] );
    $video_thumb_dimensions = array( $setting['setting_video_thumb_width'], $setting['setting_video_thumb_height'] );
        
    $video_id = he_wall::add_youtube_video($video_code, censor(str_replace(array("\r", "\n"), "<br>", $video_title)), censor(str_replace(array("\r", "\n"), "<br>", $video_description)), true, $video_privacy_level, $level_video_comments[0]);
    
    he_wall::add_video_action_link($user->user_info['user_id'], $new_action_id, $video_id);
    
    $video_directory = $video->video_dir($user->user_info['user_id'], true);
    $destination = $video_directory . $video_id . '_thumb.jpg';
    
    $video_result = he_wall_update_video_thumb($video_thumb_src, $destination, $thumb_dimensions, $video_thumb_dimensions);
    
    $video_result['result'] = 1;
    $video_result['title'] = $video_title;
    $video_result['description'] = $video_description;
    $video_result['media_src'] = $url->url_base . substr($destination, 2);
    $video_result['video_url'] = $url->url_base . 'wall_action.php?id=' . $new_action_id;
    $video_result['video_length'] = he_wall_format_duration($video_duration);
    
    $width = $setting['setting_he_wall_video_player_width'];
    $height = $setting['setting_he_wall_video_player_height'];
    
    $video_result['player'] = '<object width="' . $width . '" height="' . $height . '"><param name="wmode" value="transparent"></param><param name="movie" value="http://www.youtube.com/v/' . $video_code . '&hl=en&fs=1"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed wmode="transparent" src="http://www.youtube.com/v/' . $video_code . '&hl=en&fs=1" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="' . $width . '" height="' . $height . '"></embed></object>';
    
    return $video_result;
}

function he_wall_update_video_thumb( $thumb_source, $destination, $thumb_dimensions = array(), $dimensions = array() )
{
    global $setting, $url, $user;
    
    $width = ( $dimensions[0] ) ? $dimensions[0] : $setting['setting_he_wall_video_thumb_width'];
    $height = ( $dimensions[1] ) ? $dimensions[1] : $setting['setting_he_wall_video_thumb_height'];
        
    $thumb_dimensions = ( $thumb_dimensions ) ? $thumb_dimensions : @getimagesize($thumb_source);
    
    $thumb_width = $thumb_dimensions[0];
    $thumb_height = $thumb_dimensions[1];
    
    $file = imagecreatetruecolor($width, $height);
    $new = imagecreatefromjpeg($thumb_source);
    
    for( $i = 0; $i < 256; $i++ )
    {
        imagecolorallocate($file, $i, $i, $i);
    }
     
    imagecopyresampled($file, $new, 0, 0, 0, 0, $width, $height, $thumb_width, $thumb_height); 
    @imagejpeg($file, $destination, 100);
    ImageDestroy($new);
    ImageDestroy($file);
    
    return array( 'media_path' => $destination, 'media_width' => $thumb_width, 'media_height' => $thumb_height );
}

function he_wall_format_duration( $duration )
{
    $duration_hour = intval($duration/3600);
    $duration_min = intval(($duration%3600)/60);
    $duration_sec = ($duration%3600)%60;
    
    $duration_hour_str = ( $duration_hour > 9 ) ? $duration_hour : "0$duration_hour";
    $duration_min_str = ( $duration_min > 9 ) ? $duration_min : "0$duration_min";
    $duration_sec_str = ( $duration_sec > 9 ) ? $duration_sec : "0$duration_sec";
    
    $duration_str = ( $duration_hour_str != '00' ) 
        ? "$duration_hour_str:$duration_min_str:$duration_sec_str" 
        : "$duration_min_str:$duration_sec_str";
        
    return $duration_str;
}

function he_wall_format_text( $text )
{
    $length = 300;
    
    if ( strlen($text) <= 300 )
    {
        return $text;    
    }

    $short_text = preg_replace('/\s+?(\S+)?$/', '', substr($text, 0, $length+1));
    $short_text = substr($short_text, 0, $length); 
    $hidden_text = '<span class="display_none">' . substr($text, $length) . '</span>';
    $show_more_link = '<a href="javascript://" onclick="he_wall_show_more(this);" class="show_more_text">' . SE_Language::get(690706079) . '</a>';

    $formatted_text = $short_text . '<span>...</span>' . $show_more_link . $hidden_text;
    
    return $formatted_text;
}

function smarty_modifier_he_wall_format_text( $text, $length = 300 )
{
    if ( strlen($text) <= 300 )
    {
        return $text;    
    }

    $short_text = preg_replace('/\s+?(\S+)?$/', '', substr($text, 0, $length+1));
    $short_text = substr($short_text, 0, $length); 
    $hidden_text = '<span class="display_none">' . substr($text, $length) . '</span>';
    $show_more_link = '<a href="javascript://" onclick="he_wall_show_more(this);" class="show_more_text">' . SE_Language::get(690706079) . '</a>';

    $formatted_text = $short_text . '<span>...</span>' . $show_more_link . $hidden_text;
    
    return $formatted_text;
}

function he_wall_convert_actions($actiontype_name, $new_var_index)
{
    global $database;
    
    $sql = "SELECT `actiontype_id` FROM `se_actiontypes` WHERE `actiontype_name`='$actiontype_name'";
    $resource = $database->database_query($sql);
    
    $row = $database->database_fetch_assoc($resource);
    
    if ($row && $row['actiontype_id']) {
        $actiontype_id = $row['actiontype_id'];
        
        $sql = "SELECT `action_id`, `action_text` FROM `se_actions`  WHERE `action_actiontype_id`=$actiontype_id";
        $resource = $database->database_query($sql);
        
        while ($row = $database->database_fetch_assoc($resource)) {
            if(($action_vars = unserialize($row['action_text'])) === FALSE) {
                $action_vars = mb_unserialize($row['action_text']);
            }
            
            if (!isset($action_vars[$new_var_index])) {
                $action_vars[$new_var_index] = ' ';
                $action_text = serialize($action_vars);
                $action_text = $database->database_real_escape_string($action_text);
                
                $sql = "UPDATE `se_actions` SET `action_text`='$action_text' "
                    . " WHERE `action_id`={$row['action_id']}";
                $resource = $database->database_query($sql);
            }
        }
    }
}
