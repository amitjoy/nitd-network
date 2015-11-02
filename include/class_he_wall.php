<?php

class he_wall
{
    function hide_action( $user_id, $action_id )
    {
        if ( !$user_id || !$action_id )
        {
            return false;
        }

        $query = he_database::placeholder( "INSERT INTO `se_he_wall_hidden_action` (`user_id`, `action_id`)
            VALUES(?, ?)", $user_id, $action_id );

        he_database::query($query);

        return true;
    }

    function get_likes( $action_ids )
    {
        if ( !$action_ids )
        {
            return array();
        }

        $action_ids_str = implode(',', $action_ids);
        $query = "SELECT * FROM `se_he_wall_like` WHERE `action_id` IN($action_ids_str)
            ORDER BY `like_stamp`";

        $res = he_database::query($query);
        $action_users = array();

        while ( $row = he_database::fetch_row_from_resource($res) )
        {
            $action_users[$row['action_id']][] = $row['user_id']; 
        }

        $user_ids = array();
        foreach ( $action_users as $action_id => $users )
        {
            if ( count($users) > 2 )
            {
                continue;
            }

            $user_ids = array_merge($user_ids, $users);
        }

        $user_ids = array_unique($user_ids);
        $user_arr = he_wall::fetch_users_by_ids($user_ids);

        $action_likes = array();
        foreach ( $action_users as $action_id => $user_ids )
        {
            $action_likes[$action_id] = he_wall::format_like($user_ids, $user_arr);
        }

        return $action_likes;
    }

    function format_like( $user_ids, $user_arr )
    {
        if ( !$user_ids )
        {
            return false;
        }

        global $user, $url;

        $count = count($user_ids);
        $cur_user_likes = ( $user->user_exists ) ? in_array($user->user_info['user_id'], $user_ids) : false;

        if ( $count == 1 )
        {
            if ( $cur_user_likes )
            {
                $owner_str = SE_Language::get(690706005);
                $value = SE_Language::get(690706003, array( $owner_str ));
            }
            else
            {
                $owner_id = $user_ids[0];
                $owner = $user_arr[$owner_id];
                $owner_url = $url->url_create('profile', $owner->user_info['user_username']);
                $owner_str = '<a href="' . $owner_url . '">' . $owner->user_displayname . '</a>';

                $value = SE_Language::get(690706004, array( $owner_str ));
            }
        }
        elseif ( $count == 2 )
        {
            if ( $user_ids[0] == $user->user_info['user_id'] )
            {
                $other_user_id = $user_ids[1];
            }
            elseif ( $user_ids[1] == $user->user_info['user_id'] )
            {
                $other_user_id = $user_ids[0];
            }

            if ( $cur_user_likes )
            {
                $first_str = SE_Language::get(690706005);

                $second_user = $user_arr[$other_user_id];
                $second_user_url = $url->url_create('profile', $second_user->user_info['user_username']);
                $second_str = '<a href="' . $second_user_url . '">' . $second_user->user_displayname . '</a>';
            }
            else
            {
                $first_id = $user_ids[0];
                $first_user = $user_arr[$first_id];
                $first_user_url = $url->url_create('profile', $first_user->user_info['user_username']);
                $first_str = '<a href="' . $first_user_url . '">' . $first_user->user_displayname . '</a>';

                $second_id = $user_ids[1];
                $second_user = $user_arr[$second_id];
                $second_user_url = $url->url_create('profile', $second_user->user_info['user_username']);
                $second_str = '<a href="' . $second_user_url . '">' . $second_user->user_displayname . '</a>';
            }

            $owners = SE_Language::get(690706006, array( $first_str, $second_str ));
            $value = SE_Language::get(690706003, array( $owners ));
        }
        elseif ( $count > 2 )
        {
            $owners = SE_Language::get(690706007, array( $count ));
            $value = SE_Language::get(690706003, array( $owners ));
        }

        return array( 'like' => $cur_user_likes, 'value' => $value );
    }

    function fetch_users_by_ids( $user_ids )
    {
        if ( !$user_ids )
        {
            return array();
        }

        $user_ids_str = implode(',', $user_ids);

        $query = "SELECT `user_id`, `user_username`, `user_fname`, `user_lname` FROM `se_users`
            WHERE `user_id` IN ($user_ids_str)";

        $res = he_database::query($query);
        $user_arr = array();
        while ( $row = he_database::fetch_row_from_resource($res) )
        {
            $action_user = new se_user();
            $action_user->user_exists = 1;
            $action_user->user_info['user_id'] = $row['user_id'];
            $action_user->user_info['user_username'] = $row['user_username'];
            $action_user->user_info['user_fname'] = $row['user_fname'];
            $action_user->user_info['user_lname'] = $row['user_lname'];
            $action_user->user_displayname();

            $user_arr[$row['user_id']] = $action_user;
        }

        return $user_arr;
    }

    function like_action( $user_id, $action_id )
    {
        if ( !$user_id || !$action_id )
        {
            return false;
        }

        $query = he_database::placeholder( "SELECT COUNT(*) FROM `se_he_wall_like`
            WHERE `user_id`=? AND `action_id`=?", $user_id, $action_id );

        $like = he_database::fetch_field($query);

        if ( $like == 1 )
        {
            $query = he_database::placeholder( "DELETE FROM `se_he_wall_like`
               WHERE `user_id`=? AND `action_id`=?", $user_id, $action_id );
        }
        else
        {
            $query = he_database::placeholder( "INSERT INTO `se_he_wall_like`
                (`user_id`, `action_id`, `like_stamp`) VALUES(?, ?, ?)", $user_id, $action_id, time() );
        }

        he_database::query($query);

        //GET ACTION LIKES
        $query = he_database::placeholder( "SELECT `a`.`user_id`, `u`.`user_username`, `u`.`user_fname`, `u`.`user_lname` 
            FROM `se_he_wall_like` AS `a`
            INNER JOIN `se_users` AS `u` ON (`a`.`user_id`=`u`.`user_id`)
            WHERE `a`.`action_id`=? ORDER BY `a`.`like_stamp`", $action_id );

        $res = he_database::query($query);

        $user_ids = array();
        $user_arr = array();
        while ( $row = he_database::fetch_row_from_resource($res) )
        {
            $action_user = new se_user();
            $action_user->user_exists = 1;
            $action_user->user_info['user_id'] = $row['user_id'];
            $action_user->user_info['user_username'] = $row['user_username'];
            $action_user->user_info['user_fname'] = $row['user_fname'];
            $action_user->user_info['user_lname'] = $row['user_lname'];
            $action_user->user_displayname();

            $user_ids[] = $row['user_id'];
            $user_arr[$row['user_id']] = $action_user;
        }

        $action_info = he_wall::format_like($user_ids, $user_arr);

        $action_info = ( $action_info ) ? $action_info : array( 'like' => 0, 'value' => '' );

        return $action_info;
    }

    function get_comments( $action_ids )
    {
        global $setting;
        if ( !$action_ids )
        {
            return array();
        }

        $limit = $setting['setting_he_wall_comments_per_page'];

        $action_ids_str = implode(',', $action_ids);

        $query = "SELECT `action_id`, COUNT(`id`) AS `count` FROM `se_he_wall_comment`
            WHERE `action_id` IN($action_ids_str)
            GROUP BY `action_id` HAVING `count` > $limit"; 

        $hide_comments = he_database::fetch_column($query, true);
        $hide_comment_action_ids = array_keys($hide_comments);
        $show_comment_action_ids = array_diff($action_ids, $hide_comment_action_ids);

        if ( !$show_comment_action_ids )
        {
            return array( 'counts' => $hide_comments, 'comments' => array() );
        }

        $action_ids_str = implode(',', $show_comment_action_ids);

        $query = "SELECT `c`.*, `u`.`user_username`, `u`.`user_fname`, `u`.`user_lname`, `u`.`user_photo`
            FROM `se_he_wall_comment` AS `c` 
            INNER JOIN `se_users` AS `u` ON (`c`.`author_id`=`u`.`user_id`)
            WHERE `c`.`action_id` IN($action_ids_str)
            ORDER BY `c`.`id`";

        $res = he_database::query($query);

        $user_arr = array();
        $action_comments = array();
        while ( $row = he_database::fetch_row_from_resource($res) )
        {
            $action_id = $row['action_id'];
            $author_id = $row['author_id'];

            if ( !$user_arr[$author_id] )
            {
                $author = new se_user();
                $author->user_exists = 1;
                $author->user_info['user_id'] = $author_id;
                $author->user_info['user_username'] = $row['user_username'];
                $author->user_info['user_fname'] = $row['user_fname'];
                $author->user_info['user_lname'] = $row['user_lname'];
                $author->user_info['user_photo'] = $row['user_photo'];
                $author->user_displayname();

                $user_arr[$author_id] = $author;
            }

            $row['author'] = $user_arr[$author_id];
            $action_comments[$action_id][] = $row;
        }

        return array( 'counts' => $hide_comments, 'comments' => $action_comments );
    }

    function add_comment( $user_id, $action_id, $text )
    {
        if ( !$user_id || !$action_id || !$text )
        {
            return false;
        }

        $query = he_database::placeholder( "INSERT INTO `se_he_wall_comment` 
            (`action_id`, `author_id`, `post_stamp`, `text`) VALUES(?, ?, ?, '?')",
            $action_id, $user_id, time(), $text );

        he_database::query($query);

        return he_database::insert_id(); 
    }

    function get_comment( $comment_id )
    {
        global $datetime; 
        $comment_info = array();

        if ( !$comment_id )
        {
            return $comment_info;
        }

        $query = he_database::placeholder( "SELECT * FROM `se_he_wall_comment` 
            WHERE `id`=?", $comment_id );

        $row = he_database::fetch_row($query);

        if ( !$row )
        {
            return $comment_info;
        }

        $comment_info['id'] = $row['id'];
        $comment_info['text'] = nl2br(strip_tags($row['text']));

        $posted_date = $datetime->time_since($row['post_stamp']);
        $comment_info['posted_date'] = SE_Language::get($posted_date[0], array($posted_date[1])); 

        return $comment_info;
    }

    function delete_comment( $comment_id, $user_id )
    {
        if ( !$comment_id || !$user_id )
        {
            return false;
        }

        $query = he_database::placeholder( "DELETE FROM `se_he_wall_comment`
            WHERE `id`=? AND `author_id`=?", $comment_id, $user_id );

        he_database::query($query);

        return (int)he_database::affected_rows();
    }

    function get_hidden_actions( $user_id )
    {
        if ( !$user_id )
        {
            return array();
        }

        $query = he_database::placeholder( "SELECT `action_id` FROM `se_he_wall_hidden_action`
           WHERE `user_id`=?", $user_id );

        return he_database::fetch_column($query);
    }

    function actions_display( $visibility = 0, $actionsperuser, $where = "", $last_action_id = false, $first_action_id = false )
    {
        global $database, $user, $owner, $setting;

        $actions_array = array();

        // CACHING
        $cache_object = SECache::getInstance('serial');

        $user_id = $user->user_exists ? $user->user_info['user_id'] : 0;
        $user_subnet_id = $user->user_exists ? $user->user_info['user_subnet_id'] : 0;
        $where_md5 = $where ? '_' . md5($where) : '';

        $cache_id = 'he_actions_' . $visibility . '_' . (int)$last_action_id . '_' . (int)$first_action_id . '_' . $user_id . $where_md5;
        if( is_object($cache_object) )
        {
            $actions_array = $cache_object->get($cache_id);
        }

        // GET ACTIONS
        if( empty($actions_array) )
        {
            // GET CURRENT DATE
            $nowdate = time();

            // BEGIN BUILDING QUERY
            $actions_query = "SELECT se_actions.*, se_actiontypes.actiontype_icon, se_actiontypes.actiontype_text, 
                se_actiontypes.actiontype_media FROM se_actions 
                LEFT JOIN se_actiontypes ON se_actions.action_actiontype_id=se_actiontypes.actiontype_id";

            // GET USER PREFERENCES, IF USER LOGGED IN
            $user_pref_where = "";
            if( $setting['setting_actions_preference'] == 1 && $user->user_exists )
            {
                if( empty($user->usersetting_info) )
                {
                    $user->user_settings();
                }

                $usersetting_actions_display = join(',', array_filter(explode(',', $user->usersetting_info['usersetting_actions_display'])));
                $user_pref_where = " se_actiontypes.actiontype_id IN ({$usersetting_actions_display}) AND";
            }
            
            switch( $visibility )
            {
                // ALL ACTIONS, NO USER PREFS
                case 0:
                    $actions_query .= " WHERE";
                break;

                case 10:
                    $actions_query .= " WHERE $user_pref_where";
                break;

                // ALL REGISTERED USERS, EXCLUDING LOGGED IN USER
                case 1:
                    $actions_query .= " WHERE se_actions.action_user_id<>'{$user_id}' AND";
                    $actions_query .= $user_pref_where;
                break;

                // ONLY MY FRIENDS AND EVERYONE IN MY SUBNET, EXCLUDING LOGGED IN USER
                case 2:
                    $actions_query .= " LEFT JOIN se_friends ON se_friends.friend_user_id2=se_actions.action_user_id 
                        AND se_friends.friend_user_id1='{$user_id}' AND se_friends.friend_status='1'";
                    $actions_query .= " LEFT JOIN se_users ON se_users.user_id=se_actions.action_user_id";
                    $actions_query .= " WHERE se_actions.action_user_id<>'{$user_id}' AND";
                    $actions_query .= " (se_friends.friend_id <> 'NULL' OR se_users.user_subnet_id='{$user_subnet_id}') AND";
                    $actions_query .= $user_pref_where;
                break;

                // ONLY MY FRIENDS, EXCLUDING LOGGED IN USER
                case 4:
                    $actions_query .= " RIGHT JOIN se_friends ON se_friends.friend_user_id2=se_actions.action_user_id 
                        AND se_friends.friend_user_id1='{$user_id}' AND se_friends.friend_status='1'";
                    $actions_query .= " WHERE se_actions.action_user_id<>'{$user_id}' AND";
                    $actions_query .= $user_pref_where;
                break;
            }

            // CHECK PRIVACY
            $actions_query .= "
              CASE 
                WHEN se_actions.action_object_owner='user' THEN
                  CASE
                    WHEN se_actions.action_user_id='{$user_id}'
                      THEN TRUE
                    WHEN ((se_actions.action_object_privacy & @SE_PRIVACY_REGISTERED) AND '{$user->user_exists}'<>0)
                      THEN TRUE
                    WHEN ((se_actions.action_object_privacy & @SE_PRIVACY_ANONYMOUS) AND '{$user->user_exists}'=0)
                      THEN TRUE
                    WHEN ((se_actions.action_object_privacy & @SE_PRIVACY_SELF) AND se_actions.action_object_owner_id='{$user_id}')
                      THEN TRUE
                    WHEN ((se_actions.action_object_privacy & @SE_PRIVACY_FRIEND) AND (SELECT TRUE FROM se_friends 
                         WHERE friend_user_id1=se_actions.action_object_owner_id AND friend_user_id2='{$user_id}' AND friend_status='1' LIMIT 1))
                      THEN TRUE
                    WHEN ((se_actions.action_object_privacy & @SE_PRIVACY_SUBNET) AND '{$user->user_exists}'<>0 
                         AND (SELECT TRUE FROM se_users WHERE user_id=se_actions.action_object_owner_id AND user_subnet_id='{$user_subnet_id}' LIMIT 1))
                      THEN TRUE
                    WHEN ((se_actions.action_object_privacy & @SE_PRIVACY_FRIEND2) AND 
                          (SELECT TRUE FROM se_friends AS friends_primary 
                             LEFT JOIN se_users ON friends_primary.friend_user_id1=se_users.user_id 
                             LEFT JOIN se_friends AS friends_secondary ON friends_primary.friend_user_id2=friends_secondary.friend_user_id1 
                             WHERE friends_primary.friend_user_id1=se_actions.action_object_owner_id 
                             AND friends_secondary.friend_user_id2='{$user_id}' AND se_users.user_subnet_id='{$user_subnet_id}' LIMIT 1
                          ))
                      THEN TRUE
                    ELSE FALSE
                END
            ";

            // CALL HOOK
            ($hook = SE_Hook::exists('se_action_privacy')) ? SE_Hook::call($hook, array('actions_query' => &$actions_query)) : NULL;

            // RESUME CASE STATEMENT
            $actions_query .= "
                ELSE TRUE
                END AND
            ";

            // ADD WHERE CLAUSE IF NECESSARY
            if( $where != "" )
            {
                $actions_query .= " ($where) AND";
            }

            // LIMIT RESULTS TO TIME PERIOD SPECIFIED BY ADMIN
            $actions_query .= " se_actions.action_date > " . ($nowdate-$setting['setting_actions_showlength']);
            $actions_query .= ( $last_action_id ) ? " AND se_actions.action_id < " . $last_action_id : '';
            $actions_query .= ( $first_action_id ) ? " AND se_actions.action_id > " . $first_action_id : '';

            // ORDER BY ACTION ID DESCENDING
            $actions_query .= " ORDER BY action_id DESC";

            $limit = $setting['setting_he_wall_actions_per_page'];

            // LIMIT RESULTS TO MAX NUMBER SPECIFIED BY ADMIN
            $actions_query .= " LIMIT $limit";
            // GET RECENT ACTIVITY FEED

            $actions = $database->database_query($actions_query);
            $actions_array = Array();
            $actions_users_array = Array();

            while( $action = $database->database_fetch_assoc($actions) )
            {
                // ONLY DISPLAY THIS ACTION IF MAX OCCURRANCES PER USER HAS NOT YET BEEN REACHED
                $actions_users_array[] = $action['action_user_id'];
                $occurrances = array_count_values($actions_users_array);

                if( $occurrances[$action['action_user_id']] <= $actionsperuser )
                {
                    // UNSERIALIZE VARIABLES
                    // NOTE: I don't like mb_unserialize: it ignores the strlen param. But it works...
                    if( ($action_vars = unserialize($action['action_text']))===FALSE )
                    {
                        $action_vars = mb_unserialize($action['action_text']);
                    }

                    // REGISTER PRELOADED TEXT
                    SE_Language::_preload($action['actiontype_text']);

                    // RETRIEVE MEDIA IF NECESSARY
                    $action_media = false;
                    if( $action['actiontype_media'] )
                    {
                        $action_media = Array();
                        $media = $database->database_query("SELECT * FROM se_actionmedia WHERE actionmedia_action_id='{$action['action_id']}'");
                        while( $media_info = $database->database_fetch_assoc($media) )
                        {
                            $action_media[] = $media_info;
                        }
                    }

                    // ADD THIS ACTION TO OUTPUT ARRAY
                    $actions_array[] = array(
                        'action_id' => $action['action_id'],
                        'action_date' => $action['action_date'],
                        'action_text' => $action['actiontype_text'],
                        'action_vars' => $action_vars,
                        'action_user_id' => $action['action_user_id'],
                        'action_icon' => $action['actiontype_icon'],
                        'action_media' => $action_media
                    );
                }
            }

            // CACHE
            if( is_object($cache_object) )
            {
                $cache_object->store($actions_array, $cache_id);
            }
        }

        // Process actions (load language)
        foreach( $actions_array as $action )
        {
            SE_Language::_preload($action['action_text']);
        }

        // RETURN LIST OF ACTIONS

        return $actions_array;
    }

    function new_action_id()
    {
        $query = "SHOW TABLE STATUS LIKE 'se_actions'";

        $table_status = he_database::fetch_row($query);

        return $table_status['Auto_increment'];
    }

    function total_comments( $action_id )
    {
        $query = he_database::placeholder( "SELECT COUNT(*) FROM `se_he_wall_comment`
            WHERE `action_id`=?", $action_id );

        return (int)he_database::fetch_field($query);
    }

    function get_paging_comments( $action_id, $start = 0, $limit = 0 )
    {
        $query = he_database::placeholder( "SELECT `c`.*, `u`.`user_username`, `u`.`user_fname`, `u`.`user_lname`, `u`.`user_photo`
            FROM `se_he_wall_comment` AS `c` 
            INNER JOIN `se_users` AS `u` ON (`c`.`author_id`=`u`.`user_id`)
            WHERE `c`.`action_id`=?
            ORDER BY `c`.`id`", $action_id );

        if ( $start < 0 )
        {
            $start = 0;
        }
            
        if ( $start != 0 && $limit != 0 )
        {
            $query .= " LIMIT $start, $limit";
        }
        elseif ( $start == 0 && $limit != 0 )
        {
            $query .= " LIMIT $limit";
        }
        
        $count = he_database::num_rows(he_database::query($query));
        $temp = he_database::fetch_array( $query );
        
        foreach ( $temp as $key => $t )
        {
            $temp[$key]['author'] = new se_user(array($t['author_id']));
        }
        
        $action_comments[$action_id] = $temp;
        
        return array( 'count' => $count, 'action_comments' => $action_comments, 'sql' => $query );
    }
    
    function delete_user_uploads( $user_id )
    {
        if ( !$user_id )
        {
            return false;
        }
        
        $dirname = './uploads_wall/';
        
        // delete photos
        $photo_upload = new he_upload($user_id, 'wall_photo');
        $photo_uploads = $photo_upload->get_user_uploads();
        
        foreach ( $photo_uploads as $upload )
        {
            $filename = $upload['uploads_filename'];
            $filename_arr = explode('.', $filename);
            $filename_thumb = $filename_arr[0] . '_thumb.' . $filename_arr[1];
            
            @unlink($dirname . $filename);
            @unlink($dirname . $filename_thumb);
        }
        
        $photo_upload->delete_user_uploads();;
        
        // delete music
        $music_upload = new he_upload($user_id, 'wall_music');
        $music_uploads = $music_upload->get_user_uploads();
        
        foreach ( $music_uploads as $upload )
        {
            $filename = $upload['uploads_filename'];
            
            @unlink($dirname . $filename);
        }
        
        $music_upload->delete_user_uploads();
        
        // delete video thumb
        $video_upload = new he_upload($user_id, 'wall_video');
        $video_uploads = $video_upload->get_user_uploads();
        
        foreach ( $video_uploads as $upload )
        {
            $filename = $upload['uploads_filename'];
            
            @unlink($dirname . $filename);
        }
        
        $video_upload->delete_user_uploads();
    }
    
    function delete_action_uploads()
    {
        $dirname = './uploads_wall/';
        
        //delete files
        $query = "SELECT * FROM `se_he_uploads`
            WHERE `uploads_instance_type` IN ('wall_photo', 'wall_music', 'wall_video')
            AND `uploads_instance_id` NOT IN (SELECT `action_id` FROM `se_actions`)";
        
        $res = he_database::query($query);
        $photo_upload_ids = array();
        $media_upload_ids = array();
        while ( $upload = he_database::fetch_row_from_resource($res) )
        {            
            if ( $upload['uploads_instance_type'] == 'wall_photo' )
            {
                $filename = $upload['uploads_filename'];
                $filename_arr = explode('.', $filename);
                $filename_thumb = $filename_arr[0] . '_thumb.' . $filename_arr[1];
                
                @unlink($dirname . $filename);
                @unlink($dirname . $filename_thumb);
                
                $photo_upload_ids[] = $upload['uploads_id'];
            }
            elseif ( $upload['uploads_instance_type'] == 'wall_music' || $upload['uploads_instance_type'] == 'wall_video' )
            {
                $filename = $upload['uploads_filename'];
            
                @unlink($dirname . $filename);
                
                $media_upload_ids[] = $upload['uploads_id'];
            }
        }
        
        
        if ( $photo_upload_ids )
        {
            $photo_upload_ids_str = implode(',', $photo_upload_ids);
            
            $query = "DELETE FROM `se_he_uploads` WHERE `uploads_instance_type`='wall_photo' 
                AND `uploads_id` IN ( $photo_upload_ids_str )";
            
            he_database::query($query);
        }
        
        if ( $media_upload_ids )
        {
            $media_upload_ids_str = implode(',', $media_upload_ids);
            
            $query = "DELETE FROM `se_he_uploads` WHERE `uploads_instance_type` IN ('wall_music', 'wall_video') 
                AND `uploads_id` IN ( $media_upload_ids_str )";
            
            he_database::query($query);
        }
    }
    
    function get_action_owner( $action_id )
    {
        if ( !$action_id )
        {
            return false;
        }
        
        $query = he_database::placeholder( "SELECT `action_user_id` FROM `se_actions`
           WHERE `action_id`=?", $action_id );
        
        return he_database::fetch_field($query);
    }
    
    function remove_action( $user_id, $action_id )
    {
        if ( !$user_id || !$action_id )
        {
            return 0;
        }
        
        if ( $user_id != he_wall::get_action_owner($action_id) )
        {
            return 0;
        }
        
        he_wall::delete_action_notify($action_id);
        
        $query = he_database::placeholder( "DELETE FROM `se_actions` 
           WHERE `action_id`=?", $action_id );
        he_database::query($query);
        
        // delete music links
        $query = he_database::placeholder( "DELETE FROM `se_he_wall_music_link`
            WHERE `user_id`=? AND `action_id`=?", $user_id, $action_id );
        he_database::query($query);
        
        // delete media links
        $query = he_database::placeholder( "DELETE FROM `se_he_wall_media_link`
            WHERE `user_id`=? AND `action_id`=?", $user_id, $action_id );
        he_database::query($query);
        
        // delete group media links
        $query = he_database::placeholder( "DELETE FROM `se_he_wall_group_media_link`
            WHERE `user_id`=? AND `action_id`=?", $user_id, $action_id );
        he_database::query($query);
        
        // delete video links
        $query = he_database::placeholder( "DELETE FROM `se_he_wall_video_link`
            WHERE `user_id`=? AND `action_id`=?", $user_id, $action_id );
        he_database::query($query);
        
        // delete action likes
        $query = he_database::placeholder( "DELETE FROM `se_he_wall_like`
            WHERE `action_id`=?", $action_id );
        he_database::query($query);

        // delete action comments
        $query = he_database::placeholder( "DELETE FROM `se_he_wall_comment`
            WHERE `action_id`=?", $action_id );
        he_database::query($query);
        
        // delete action hides
        $query = he_database::placeholder( "DELETE FROM `se_he_wall_hidden_action`
            WHERE `action_id`=?", $action_id );
        he_database::query($query);
        
        return 1;
    }
    
    function add_music_action_link( $user_id, $action_id, $music_id )
    {
    	if ( !$user_id || !$action_id || !$music_id )
    	{
    		return false;
    	}
    	
    	$query = he_database::placeholder( "INSERT INTO `se_he_wall_music_link` (`user_id`, `action_id`, `music_id`)
            VALUES(?, ?, ?)", $user_id, $action_id, $music_id );
    	
    	he_database::query($query);
    }
    
    function add_video_action_link( $user_id, $action_id, $video_id )
    {
    	if ( !$user_id || !$action_id || !$video_id )
    	{
    		return false;
    	}
    	
    	$query = he_database::placeholder( "INSERT INTO `se_he_wall_video_link` (`user_id`, `action_id`, `video_id`)
            VALUES(?, ?, ?)", $user_id, $action_id, $video_id );
    	
    	he_database::query($query);
    }
    
    function delete_music_action( $user_id, $music_id )
    {
        if ( !$user_id || !$music_id )
        {
            return false;
        }
        
        $query = he_database::placeholder( "SELECT `action_id` FROM `se_he_wall_music_link`
            WHERE `user_id`=? AND `music_id`=?", $user_id, $music_id );
        
        $action_id = he_database::fetch_field($query);
        
        he_wall::remove_action($user_id, $action_id);
    }
    
    function delete_video_action( $user_id, $video_id )
    {
        if ( !$user_id || !$video_id )
        {
            return false;
        }
        
        $query = he_database::placeholder( "SELECT `action_id` FROM `se_he_wall_video_link`
            WHERE `user_id`=? AND `video_id`=?", $user_id, $video_id );
        
        $action_id = he_database::fetch_field($query);
        
        he_wall::remove_action($user_id, $action_id);
    }
    
    function get_wall_album( $user_id )
    {
    	if ( !$user_id )
    	{
    		return false;
    	}
    	
    	$query = he_database::placeholder( "SELECT `album_id` FROM `se_he_wall_album` 
            WHERE `user_id`=?", $user_id );
        
        return he_database::fetch_field($query);
    }
    
    function create_wall_album( $user_id )
    {
    	if ( !$user_id )
    	{
    		return false;
    	}
    	    	
    	$album_title = SE_Language::get(690706064);
    	$album_desc = SE_Language::get(690706065);
    	$album_search = 63;
    	$album_privacy = 63;
    	$album_comments = 63;
    	$album_tag = 63;
    	
    	//get new album order
        $query = he_database::placeholder( "SELECT MAX(`album_order`) FROM `se_albums` 
            WHERE `album_user_id`=?", $user_id );
        $album_order = (int)he_database::fetch_field($query) + 1;
    	
    	
    	$query = he_database::placeholder( "INSERT INTO `se_albums` (`album_user_id`, `album_datecreated`, `album_dateupdated`,
			`album_title`, `album_desc`, `album_search`, `album_privacy`, `album_comments`, `album_tag`, `album_order`)
			VALUES (?, ?, ?, '?', '?', ?, ?, ?, ?, ?)",
			$user_id, time(), time(), $album_title, $album_desc, $album_search, $album_privacy, $album_comments,
			$album_tag, $album_order );

		he_database::query($query);
		$album_id = he_database::insert_id();
		
		$query = he_database::placeholder( "INSERT INTO `se_he_wall_album` (`user_id`, `album_id`)
            VALUES(?, ?)", $user_id, $album_id );
		
		he_database::query($query);
		
		return $album_id;	
    }
    
    function get_music_owner( $music_id )
    {
    	if ( !$music_id )
    	{
    		return false;
    	}
    	
    	$query = he_database::placeholder( "SELECT `music_user_id` FROM `se_music`
    	   WHERE `music_id`=?", $music_id );
    	
    	return he_database::fetch_field($query);
    }
    
    function get_wall_album_info( $album_id )
    {
    	if ( !$album_id )
    	{
    		return array();
    	}
    	
    	$query = he_database::placeholder( "SELECT * FROM `se_albums`
    	   WHERE `album_id`=?", $album_id );
    	
    	return he_database::fetch_row($query);
    }
    
    function update_wall_album( $album_id, $album_cover )
    {
    	if ( !$album_id || !$album_cover )
    	{
    		return false;
    	}
    	
    	$query = he_database::placeholder( "UPDATE `se_albums` SET `album_cover`='?', `album_dateupdated`=?
            WHERE album_id=?", $album_cover, time(), $album_id );
    	
    	he_database::query($query);
    }
    
    function update_wall_album_media( $media_id, $media_desc )
    {
        if ( !$media_id || !$media_desc )
        {
            return false;
        }
        
        $query = he_database::placeholder( "UPDATE `se_media` SET `media_desc`='?'
            WHERE `media_id`=?", $media_desc, $media_id );
        
        he_database::query($query);
    }
    
    function add_wall_album_media( $action_id, $media_id )
    {
    	global $user;
    	 
        if ( !$action_id || !$media_id || !$user->user_exists )
        {
            return false;
        }
        
        $query = he_database::placeholder( "INSERT INTO `se_he_wall_media_link` (`user_id`, `media_id`, `action_id`)
            VALUES(?, ?, ?)", $user->user_info['user_id'], $media_id, $action_id );
        
        he_database::query($query);
    }
    
    function check_wall_album( $album_id )
    {
    	if ( !$album_id )
        {
            return false;
        }
        
        $query = he_database::placeholder( "SELECT * FROM `se_he_wall_album`
           WHERE `album_id`=?", $album_id );
        
        return he_database::fetch_row($query);
    }
    
    function delete_wall_album( $user_id, $album_id )
    {
    	if ( !$user_id || !$album_id )
    	{
    		return false;
    	}
    	
    	$wall_album_info = he_wall::check_wall_album($album_id);
    	
    	if ( !$wall_album_info || $wall_album_info['user_id'] != $user_id )
    	{
    		return false;
    	}
    	
    	$query = he_database::placeholder( "SELECT `link`.`action_id` FROM `se_he_wall_media_link` AS `link`
            LEFT JOIN `se_media` AS `media` ON (`link`.`media_id`=`media`.`media_id`)
            WHERE `media`.`media_album_id`=?", $album_id );
    	
    	$action_ids = he_database::fetch_column($query);
    	
    	foreach ( $action_ids as $action_id )
    	{
    		he_wall::remove_action($user_id, $action_id);
    	}
    	
    	$query = he_database::placeholder( "DELETE FROM `se_he_wall_album`
    	   WHERE `user_id`=? AND `album_id`=?", $user_id, $album_id );
    	
    	he_database::query($query);
    }
    
    function delete_wall_media( $user_id, $media_ids )
    {
    	if ( !$user_id || !$media_ids )
    	{
    		return false;
    	}
    	
    	$media_ids_str = implode(',', $media_ids);
    	
    	$query = he_database::placeholder( "SELECT `action_id` FROM `se_he_wall_media_link`
            WHERE `user_id`=? AND `media_id` IN ($media_ids_str)", $user_id );
        
        $action_ids = he_database::fetch_column($query);
        
        foreach ( $action_ids as $action_id )
        {
            he_wall::remove_action($user_id, $action_id);
        }
        
        return true;
    }
    
    function get_album_owner( $album_id )
    {
    	if ( !$album_id )
    	{
    		return false;
    	}
    	
    	$query = he_database::placeholder( "SELECT `user_id` FROM `se_he_wall_album`
    	   WHERE `album_id`=?", $album_id );
    	
    	return he_database::fetch_field($query);
    }
    
    function get_video_owner( $video_id )
    {
    	if ( !$video_id )
    	{
    		return false;
    	}
    	
    	$query = he_database::placeholder( "SELECT `video_user_id` FROM `se_videos`
    	   WHERE `video_id`=?", $video_id );
    	
    	return he_database::fetch_field($query);
    }
    
    function video_actiontype_id()
    {
    	$query = "SELECT `actiontype_id` FROM `se_actiontypes` WHERE `actiontype_name`='wallpostvideo'";
    	
    	return he_database::fetch_field($query);
    }
    
    function add_youtube_video( $video_code, $video_title, $video_desc, $video_search, $video_privacy, $video_comments )
    {
        global $user;
    	
        $time = time();
        
        $query = he_database::placeholder( "INSERT INTO `se_videos` (
            `video_user_id`,
			`video_datecreated`,
			`video_title`,
			`video_desc`,
			`video_search`,
			`video_privacy`,
			`video_comments`, 
			`video_type`,
			`video_dateupdated`,
            `video_youtube_code`,
            `video_uploaded`,
            `video_is_converted` 
            ) VALUES ( ?, ?, '?', '?', ?, ?, ?, 1, ?, '?', 1, 1 )",
            $user->user_info['user_id'],
            $time,
            $video_title,
            $video_desc,
            $video_search,
            $video_privacy,
            $video_comments,
            $time,
            $video_code );
        
        he_database::query($query);
        
        return he_database::insert_id();
    }
    
    function get_actiontype( $action_id )
    {
    	if ( !$action_id )
    	{
    		return false;
    	}
    	
    	$query = he_database::placeholder( "SELECT `type`.`actiontype_name` FROM `se_actions` AS `action`
            INNER JOIN `se_actiontypes` AS `type` ON (`action`.`action_actiontype_id`=`type`.`actiontype_id`)
            WHERE `action`.`action_id`=?", $action_id );
    	
    	return he_database::fetch_field($query);
    }
    
    function get_actiontype_ids()
    {
    	$query = "SELECT `actiontype_id` FROM `se_actiontypes` 
            WHERE `actiontype_name` IN ('wallpost', 'wallpostlink', 'wallpostmusic', 
            'wallpostphoto', 'wallpostvideo')";
    	
    	return he_database::fetch_column($query);
    }
    
    function get_group_album( $group_id )
    {
    	if ( !$group_id )
    	{
    		return false;
    	}
    	
    	$query = he_database::placeholder( "SELECT `groupalbum_id` FROM `se_groupalbums`
            WHERE `groupalbum_group_id`=?", $group_id );
    	
    	return he_database::fetch_field($query);
    }
    
    function update_group_album( $album_id )
    {
    	if ( !$album_id )
    	{
    		return false;
    	}
    	
    	$query = he_database::placeholder( "UPDATE `se_groupalbums` SET `groupalbum_dateupdated`=?
            WHERE `groupalbum_id`=?", time(), $album_id );
    	
    	he_database::query($query);
    }
    
    function update_group_album_media( $media_id, $media_desc )
    {
    	if ( !$media_id || !$media_desc )
    	{
    		return false;
    	}
    	
    	$query = he_database::placeholder( "UPDATE `se_groupmedia` SET `groupmedia_desc`='?'
            WHERE `groupmedia_id`=?", $media_desc, $media_id );
    	
    	he_database::query($query);
    }
    
    function add_group_album_media( $action_id, $media_id )
    {
    	global $user;
    	 
        if ( !$action_id || !$media_id || !$user->user_exists )
        {
            return false;
        }
        
        $query = he_database::placeholder( "INSERT INTO `se_he_wall_group_media_link` (`user_id`, `media_id`, `action_id`)
            VALUES(?, ?, ?)", $user->user_info['user_id'], $media_id, $action_id );
        
        he_database::query($query);
    }
    
    function delete_group_media( $user_id, $group_id, $media_id )
    {
    	if ( !$user_id || !$group_id || !$media_id )
    	{
    		return false;
    	}
    	
    	// get media info
    	$query =he_database::placeholder( "SELECT * FROM `se_he_wall_group_media_link`
            WHERE `media_id`=?", $media_id );

    	$media_info = he_database::fetch_row($query);

    	// get user rank
    	$query = he_database::placeholder( "SELECT `groupmember_rank` FROM `se_groupmembers`
            WHERE `groupmember_user_id`=? AND `groupmember_group_id`=?", $user_id, $group_id );
    	
    	$user_rank = he_database::fetch_field($query); 
    	
    	if ( $media_info['user_id'] != $user_id && !in_array($user_rank, array( 1, 2 )) )
    	{
    		return false;
    	}
    	
    	he_wall::remove_action($media_info['user_id'], $media_info['action_id']);    	
    }
    
    function get_wall_owner( $wall_object, $wall_object_id )
    {
    	if ( !$wall_object || !$wall_object_id )
    	{
    		return false;
    	}
    	
    	if ( !in_array($wall_object, array( 'user', 'group' )) )
        {
            return false;
        }
        
        switch ( $wall_object )
        {
            case 'user':
                
                $owner = new se_user(array( $wall_object_id ));
                
                break;
                
            case 'group':
                
                $query = he_database::placeholder( "SELECT `groupmember_user_id` FROM `se_groupmembers`
                    WHERE `groupmember_rank`=2 AND `groupmember_group_id`=? LIMIT 1", $wall_object_id );
                
                $owner_id = he_database::fetch_field($query);
                $owner = new se_user(array( $owner_id ));
                
                break;
            
            default:
                
                break;
        }
        
        if ( !$owner->user_exists || $user->user_info['user_id'] == $owner->user_info['user_id'] )
        {
            return false;
        }
        
        return $owner;
    }
    
    function new_post_notify( $wall_object, $wall_object_id, $action_id )
    {
    	global $user, $notify, $url;
    	
    	if ( !$wall_object || !$wall_object_id || !$action_id )
    	{
    		return false;
    	}
    	
        $owner = he_wall::get_wall_owner($wall_object, $wall_object_id);
        
        if ( !$owner || !$owner->user_exists || $owner->user_info['user_id'] == $user->user_info['user_id'] )
        {
        	return false;
        }
        
        if ( $owner->usersetting_info['usersetting_notify_wallpost'] )
        {
        	$login_url = '<a href="' . $url->url_base . "login.php\">" . $url->url_base . "login.php</a>";
        	$replace_arr = array( $owner->user_displayname, $user->user_displayname, $login_url );
        	
            send_systememail('wallpost', $owner->user_info['user_email'], $replace_arr);
        }
        
        $url_vars = array( '', $action_id );
        $replace_arr = array( $user->user_displayname );
        
        $notify->notify_add($owner->user_info['user_id'], 'wallpost', $action_id, $url_vars, $replace_arr);
    }
    
    function new_comment_notify( $action_id )
    {
        global $user, $notify, $url;
        
        if ( !$action_id )
        {
            return false;
        }
        
        $owner_id = he_wall::get_action_owner($action_id);
        $owner = new se_user(array( $owner_id ));
        
        if ( !$owner->user_exists || $user->user_info['user_id'] == $owner_id )
        {
            return false;
        }
        
        if ( $owner->usersetting_info['usersetting_notify_wallactioncomment'] )
        {
            $login_url = '<a href="' . $url->url_base . "login.php\">" . $url->url_base . "login.php</a>";
            $replace_arr = array( $owner->user_displayname, $user->user_displayname, $login_url );
            
            send_systememail('wallactioncomment', $owner->user_info['user_email'], $replace_arr);
        }
        
        $url_vars = array( '', $action_id );
        $replace_arr = array( $user->user_displayname );
        
        $notify->notify_add($owner->user_info['user_id'], 'wallactioncomment', $action_id, $url_vars, $replace_arr);
    }
    
    function new_like_notify( $action_id )
    {
        global $user, $notify, $url;
        
        if ( !$action_id )
        {
            return false;
        }
        
        $owner_id = he_wall::get_action_owner($action_id);
        $owner = new se_user(array( $owner_id ));
        
        if ( !$owner->user_exists || $user->user_info['user_id'] == $owner_id )
        {
            return false;
        }
        
        if ( $owner->usersetting_info['usersetting_notify_wallactionlike'] )
        {
            $login_url = '<a href="' . $url->url_base . "login.php\">" . $url->url_base . "login.php</a>";
            $replace_arr = array( $owner->user_displayname, $user->user_displayname, $login_url );
            
            send_systememail('wallactionlike', $owner->user_info['user_email'], $replace_arr);
        }
        
        $url_vars = array( '', $action_id );
        $replace_arr = array( $user->user_displayname );
        
        $notify->notify_add($owner->user_info['user_id'], 'wallactionlike', $action_id, $url_vars, $replace_arr);
    }
    
    function get_action_info( $action_id )
    {
    	if ( !$action_id )
    	{
    		return array();
    	}
    	
    	$query = he_database::placeholder( "SELECT * FROM `se_actions` WHERE `action_id`=?", $action_id );
    	
    	return he_database::fetch_row($query);
    }
    
    function get_group_info( $group_id )
    {
    	if ( !$group_id )
    	{
    		return array();
    	}
    	
    	$query = he_database::placeholder( "SELECT * FROM `se_groups` WHERE `group_id`=?", $group_id );
    	
    	return he_database::fetch_row($query);
    }
    
    function group_action_media_id( $action_id )
    {
        if ( !$action_id )
        {
            return false;
        }
        
        $query = he_database::placeholder( "SELECT `media_id` FROM `se_he_wall_group_media_link`
            WHERE `action_id`=?", $action_id );
        
        return he_database::fetch_field($query);
    }
    
    function action_media_id( $action_id )
    {
        if ( !$action_id )
        {
            return false;
        }
        
        $query = he_database::placeholder( "SELECT `media_id` FROM `se_he_wall_media_link`
            WHERE `action_id`=?", $action_id );

        return he_database::fetch_field($query);
    }
    
    function delete_group_actions( $group_id )
    {
    	global $user;
    	
    	if ( !$group_id || !$user->user_exists )
    	{
    		return false;
    	}
    	
        // get user rank
        $query = he_database::placeholder( "SELECT `groupmember_rank` FROM `se_groupmembers`
            WHERE `groupmember_user_id`=? AND `groupmember_group_id`=?", $user->user_info['user_id'], $group_id );
        
        $user_rank = he_database::fetch_field($query);
        
        if ( $user_rank != 2 )
        {
        	return false;
        }
        
        $query = he_database::placeholder( "SELECT `action_id`, `action_user_id` FROM `se_actions`
            WHERE `action_object_owner`='group' AND `action_object_owner_id`=?", $group_id );
        
        $actions = he_database::fetch_column($query, true);
        
        foreach ( $actions as $action_id => $user_id )
        {
        	he_wall::remove_action($user_id, $action_id);
        }

        $query = he_database::placeholder( "DELETE FROM `se_actions`
            WHERE `action_object_owner`='group' AND `action_object_owner_id`=?", $group_id );

        he_database::query($query);
    }
    
    function get_action_filename($action_id)
    {
        if (!$action_id) {
            return false;
        }
        
        $sql = he_database::placeholder("SELECT `uploads_filename` FROM `se_he_uploads` "
            . "WHERE `uploads_instance_id`=?", $action_id);
        
        return he_database::fetch_field($sql);
    }
    
    function get_action_media_ext($action_id, $owner = 'user')
    {
        if (!$action_id) {
            return false;
        }
        
        if ($owner == 'group') {
            $media_id = he_wall::group_action_media_id($action_id);
            
            $sql = he_database::placeholder("SELECT `groupmedia_ext` FROM `se_groupmedia` "
                . "WHERE `groupmedia_id`=?", $media_id);
        } else {
            $media_id = he_wall::action_media_id($action_id);

            $sql = he_database::placeholder("SELECT `media_ext` FROM `se_media` "
                . "WHERE `media_id`=?", $media_id);
        }

        return he_database::fetch_field($sql);
    }
    
    function get_wall_noptifytype_ids()
    {
        $sql = "SELECT `notifytype_id` FROM `se_notifytypes` "
            . "WHERE `notifytype_name` IN ('wallpost', 'wallactioncomment', 'wallactionlike')";
        
        return he_database::fetch_column($sql);
    }
    
    function delete_action_notify($action_id)
    {
        if (!$action_id) {
            return false;
        }
        
        $notifytype_ids = he_wall::get_wall_noptifytype_ids();
        
        if (!$notifytype_ids) {
            return false;
        }
        
        $notifytype_str = implode(',', $notifytype_ids);
        
        $sql = he_database::placeholder("DELETE FROM `se_notifys` "
            . "WHERE `notify_object_id`=? AND `notify_notifytype_id` IN ($notifytype_str)", $action_id);

        he_database::query($sql);
    }
    
    function get_privacy_options($object, $object_id)
    {
        global $user;
        
        if (!$object || !$object_id) {
            return array();
        }
        
        if ($object == 'group') {
            $group = new se_group($user->user_info['user_id'], $object_id);
            
            $level_group_privacy = unserialize($group->groupowner_level_info['level_group_privacy']);
            rsort($level_group_privacy);

            // GET PREVIOUS PRIVACY SETTINGS
            for ($c = 0; $c < count($level_group_privacy); $c++) {
                if (group_privacy_levels($level_group_privacy[$c]) != "") {
                    SE_Language::_preload(group_privacy_levels($level_group_privacy[$c]));
                    $privacy_options[$level_group_privacy[$c]] = group_privacy_levels($level_group_privacy[$c]);
                }
            }
        }
        elseif ($object == 'pages') {
            $level_action_privacy = array(7, 3, 1); //TODO
            // GET PREVIOUS PRIVACY SETTINGS
            for ($c = 0; $c < count($level_action_privacy); $c++){
                if (pages_privacy_levels($level_action_privacy[$c]) != "") {
                    SE_Language::_preload(pages_privacy_levels($level_action_privacy[$c]));
                    $privacy_options[$level_action_privacy[$c]] = pages_privacy_levels($level_action_privacy[$c]);
                }
            }
        }
        else {
            $level_action_privacy = unserialize($user->level_info['level_wall_action_privacy']);
            rsort($level_action_privacy);

            // GET PREVIOUS PRIVACY SETTINGS
            for ($c = 0; $c < count($level_action_privacy); $c++){
                if (user_privacy_levels($level_action_privacy[$c]) != "") {
                    SE_Language::_preload(user_privacy_levels($level_action_privacy[$c]));
                    $privacy_options[$level_action_privacy[$c]] = user_privacy_levels($level_action_privacy[$c]);
                }
            }            
        }
        
        return $privacy_options;
    }
    
    function get_wall_link($object, $object_id)
    {
        global $user, $url;
        
        if (!$object || !$object_id) {
            return '';
        }
        
        if ($object == 'userhome' || !$user->user_exists) {
            return '';
        }
        
        if ($object == 'user' && $user->user_info['user_id'] == $object_id) {
            return '';
        }
        
        if ($object == 'user') {
            
            $sql = he_database::placeholder("SELECT `user_id`, `user_username`, `user_fname`, `user_lname` FROM `se_users` "
                . "WHERE `user_id`=?", $object_id);
            $row = he_database::fetch_row($sql);

            $wall_owner = new se_user();
            $wall_owner->user_exists = 1;
            $wall_owner->user_info['user_id'] = $row['user_id'];
            $wall_owner->user_info['user_username'] = $row['user_username'];
            $wall_owner->user_info['user_fname'] = $row['user_fname'];
            $wall_owner->user_info['user_lname'] = $row['user_lname'];
            $wall_owner->user_displayname();
            
            $wall_url = $url->url_create('profile', $wall_owner->user_info['user_username']);
            $wall_label = $wall_owner->user_displayname;
            
        } elseif ($object == 'group') {
            
            $sql = he_database::placeholder("SELECT `group_title` FROM `se_groups` "
                . "WHERE `group_id`=?", $object_id);
            
            $wall_url = $url->url_create('group', null, $object_id);
            $wall_label = he_database::fetch_field($sql);
        } elseif ($object == 'pages') {
            
            $sql = he_database::placeholder("SELECT `pages_title` FROM `se_pages` "
                . "WHERE `pages_id`=?", $object_id);
            
            $wall_url = $url->url_create('pages', null, $object_id);
            $wall_label = he_database::fetch_field($sql);
        }
        else {
            return '';
        }
        
        return SE_Language::get(690706106, array($wall_url, $wall_label));
    }
    
    function get_users($user_ids)
    {
        if (!$user_ids) {
            return array();
        }
        
        $user_ids_str = implode(',', $user_ids);
        
        $sql = "SELECT `user_id`, `user_username`, `user_fname`, `user_lname`, `user_photo` FROM `se_users`
        	WHERE `user_id` IN ($user_ids_str)";
        
        $user_list = array();
        $res = he_database::query($sql);
        
        while ($row = he_database::fetch_row_from_resource($res)) {
            $author = new se_user();
            $author->user_exists = 1;
            $author->user_info['user_id'] = $row['user_id'];
            $author->user_info['user_username'] = $row['user_username'];
            $author->user_info['user_fname'] = $row['user_fname'];
            $author->user_info['user_lname'] = $row['user_lname'];
            $author->user_info['user_photo'] = $row['user_photo'];
            $author->user_displayname();
            
            $user_list[$row['user_id']] = $author;
        }
        
        return $user_list;
    }
}
