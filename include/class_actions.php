<?php

//  THIS CLASS IS USED TO OUTPUT AND UPDATE RECENT ACTIVITY ACTIONS
//  METHODS IN THIS CLASS:
//    actions_add()
//    actions_display()
//    actions_allowed()

class se_actions
{
	// THIS METHOD ADDS A NEW ACTION
	// INPUT: $user REPRESENTING THE USER OBJECT OF THE USER WHO COMMITTED THE ACTION
	//	  $actiontype_name REPRESENTING THE TYPE OF ACTION COMMITTED
	//	  $replace (OPTIONAL) REPRESENTING AN ARRAY OF VALUES FOR THE ACTION TEXT STRING (MUST CORRESPOND TO ACTIONTYPE_VARS)
	//	  $action_media (OPTIONAL) REPRESENTING AN ARRAY OF VALUES FOR ACTION MEDIA
	//	  $timeframe (OPTIONAL) REPRESENTING THE TIME (IN SEC) AFTER WHICH TO INSERT A NEW ROW - SET TO 0 TO ALWAYS INSERT A NEW ROW
	//	  $replace_media (OPTIONAL) REPRESENTING WHETHER TO REPLACE MEDIA FOR AN OLD ACTION OR SIMPLY ADD ADDITIONAL MEDIA
	//	  $action_object_owner (OPTIONAL) REPRESENTING THE OWNER OF THE OBJECT (ex: 'user')
	//	  $action_object_owner_id (OPTIONAL) REPRESENTING THE ID OF THE OWNER
	//	  $action_object_privacy (OPTIONAL) REPRESENTING THE PRIVACY OF THE OBJECT
	function actions_add($user, $actiontype_name, $replace = array(), $action_media = array(), $timeframe = 0, $replace_media = false, $action_object_owner = "", $action_object_owner_id = 0, $action_object_privacy = 0)
  {
	  global $database, $setting;
    
	  // GET CURRENT DATE
	  $nowdate = time();
    
	  // GET ACTIONTYPE INFO
	  $actiontype_info = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_actiontypes WHERE actiontype_name='$actiontype_name' LIMIT 1"));
    
	  // DONT PUBLISH IF PRIVACY IS TURNED ON AND USER DISALLOWED THIS ACTION TYPE, OR IS NOT ENABLED BY ADMIN
	  $user->user_settings();
	  $dontpublish_array = array_filter(explode(",", $user->usersetting_info['usersetting_actions_dontpublish']));
    
    $publish = ( $actiontype_info['actiontype_enabled'] && ( ($setting['setting_actions_privacy'] == 1 && !in_array($actiontype_info['actiontype_id'], $dontpublish_array)) || !$setting['setting_actions_privacy'] ) );
    
	  // PUBLISH ACTION
	  if( !$publish ) return;
    
    // DELETE OLDEST ACTION(S) FOR THIS USER IF MAX ACTIONS STORED PER USER IS REACHED
    $totalactions = $database->database_num_rows($database->database_query("SELECT action_id FROM se_actions WHERE action_user_id='{$user->user_info['user_id']}'"));
    if( $totalactions>$setting['setting_actions_actionsonprofile'] )
    {
      $database->database_query("DELETE FROM se_actions WHERE action_user_id='{$user->user_info['user_id']}' ORDER BY action_id ASC LIMIT ".($totalactions-$setting['setting_actions_actionsonprofile']));
      
      // CLEANUP THE ACTION MEDIA TABLE
      $database->database_query("DELETE se_actionmedia.* FROM se_actionmedia LEFT JOIN se_actions ON se_actions.action_id=se_actionmedia.actionmedia_action_id WHERE action_id IS NULL");
    }
    
    // GET PREVIOUS ACTION OF THE SAME TYPE WITH TIMEFRAME SPECIFICATIONS
    $difference = ( ($nowdate < $timeframe) ? 0 : $nowdate - $timeframe );
    
    $prev_query = $database->database_query("SELECT action_id FROM se_actions WHERE action_user_id='{$user->user_info['user_id']}' AND action_actiontype_id='{$actiontype_info['actiontype_id']}' AND action_date>'{$difference}' ORDER BY action_actiontype_id DESC LIMIT 1");
    $update = (bool) $database->database_num_rows($prev_query);
    if( $update ) $prev = $database->database_fetch_assoc($prev_query);
    
    // SERIALIZE APPROPRIATE VARS
    $replace = array_map('stripslashes', $replace);
    $action_text = addslashes(serialize($replace));
    
    // UPDATE OLD ACTION
    if( $update )
    {
      $database->database_query("
        UPDATE se_actions
        SET action_date='{$nowdate}', 
        action_text='{$action_text}',
        action_object_privacy='{$action_object_privacy}'
        WHERE action_id='{$prev['action_id']}' AND
        action_user_id='{$user->user_info['user_id']}' AND
        action_actiontype_id='{$actiontype_info['actiontype_id']}'
      ");
      
      // DELETE OLD MEDIA IF NECESSARY
      if( $replace_media )
      {
        $database->database_query("DELETE FROM se_actionmedia WHERE actionmedia_action_id='{$prev['action_id']}'");
      }
      
      $action_id = $prev['action_id'];
    }
    
    // INSERT NEW ACTION
    else
    {
      $database->database_query("
        INSERT INTO se_actions (
          action_actiontype_id,
          action_date, 
          action_user_id, 
          action_text,
          action_object_owner,
          action_object_owner_id,
          action_object_privacy
        ) VALUES (
          '{$actiontype_info['actiontype_id']}',
          '{$nowdate}', 
          '{$user->user_info['user_id']}', 
          '{$action_text}',
          '{$action_object_owner}',
          '{$action_object_owner_id}',
          '{$action_object_privacy}'
        )
      ");
      
      $action_id = $database->database_insert_id();
    }
    
    // INSERT MEDIA
    if( is_array($action_media) && !empty($action_media) && $action_id )
    {
      foreach( $action_media as $action_media_index=>$action_media_data )
      {
        $database->database_query("
          INSERT INTO se_actionmedia (
            actionmedia_action_id,
            actionmedia_path, 
            actionmedia_link,
            actionmedia_width,
            actionmedia_height
          ) VALUES (
            '{$action_id}',
            '{$action_media_data['media_path']}', 
            '{$action_media_data['media_link']}', 
            '{$action_media_data['media_width']}',
            '{$action_media_data['media_height']}'
          )
        ");
      }
    }
	}
  
  // END actions_add() METHOD










	// THIS METHOD DISPLAYS A LIST OF RECENT UPDATES (ACTIONS)
	// INPUT: $visibility REPRESENTING A VISIBILITY SETTING
	//	  $actionsperuser REPRESENTING HOW MANY ACTIONS PER USER TO DISPLAY
	//	  $where (OPTIONAL) REPRESENTING A WHERE CLAUSE
	// OUTPUT: LIST OF RECENT ACTIONS
	function actions_display($visibility, $actionsperuser, $where = "")
  {
	  global $database, $user, $owner, $setting;
    
    $actions_array = array();
    
    // CACHING
    $cache_object = SECache::getInstance('serial');
    $cache_id = 'actions_'.( $visibility ? $visibility : '0').'_'.$actionsperuser.'_'.( $owner->user_exists ? $owner->user_info['user_id'] : '0' ).'_'.( $user->user_exists ? $user->user_info['user_id'] : '0' ).( $where ? '_'.md5($where) : '');
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
      $actions_query = "SELECT s.user_photo as action_user_photo,se_actions.*, se_actiontypes.actiontype_icon, se_actiontypes.actiontype_text, se_actiontypes.actiontype_media FROM se_actions LEFT JOIN se_actiontypes ON se_actions.action_actiontype_id=se_actiontypes.actiontype_id";
	  $actions_query .= " LEFT OUTER JOIN se_users s ON s.user_id=se_actions.action_user_id ";
      
      // GET USER PREFERENCES, IF USER LOGGED IN
      $user_pref_where = "";
      if( $setting['setting_actions_preference'] == 1 && $user->user_exists )
      {
        if( empty($user->usersetting_info) ) $user->user_settings();
        $usersetting_actions_display = join(',', array_filter(explode(',', $user->usersetting_info['usersetting_actions_display'])));
        $user_pref_where = " se_actiontypes.actiontype_id IN ({$usersetting_actions_display}) AND";
      }
      
      switch($visibility)
      {
        // ALL ACTIONS, NO USER PREFS
        case 0:
          $actions_query .= " WHERE";
        break;
        
        // ALL REGISTERED USERS, EXCLUDING LOGGED IN USER
        case 1:
          $actions_query .= " WHERE se_actions.action_user_id<>'{$user->user_info['user_id']}' AND";
          $actions_query .= $user_pref_where;
        break;
        
        // ONLY MY FRIENDS AND EVERYONE IN MY SUBNET, EXCLUDING LOGGED IN USER
        case 2:
          $actions_query .= " LEFT JOIN se_friends ON se_friends.friend_user_id2=se_actions.action_user_id AND se_friends.friend_user_id1='{$user->user_info['user_id']}' AND se_friends.friend_status='1'";
          $actions_query .= " LEFT JOIN se_users ON se_users.user_id=se_actions.action_user_id";
          $actions_query .= " WHERE se_actions.action_user_id<>'{$user->user_info['user_id']}' AND";
          $actions_query .= " (se_friends.friend_id <> 'NULL' OR se_users.user_subnet_id='{$user->user_info['user_subnet_id']}') AND";
          $actions_query .= $user_pref_where;
        break;
        
        // ONLY MY FRIENDS, EXCLUDING LOGGED IN USER
        case 4:
          $actions_query .= " RIGHT JOIN se_friends ON se_friends.friend_user_id2=se_actions.action_user_id AND se_friends.friend_user_id1='{$user->user_info['user_id']}' AND se_friends.friend_status='1'";
          $actions_query .= " WHERE se_actions.action_user_id<>'{$user->user_info['user_id']}' AND";
          $actions_query .= $user_pref_where;
        break;
      }
      
      // CHECK PRIVACY
      $actions_query .= "
        CASE 
          WHEN se_actions.action_object_owner='user' THEN
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
      if($where != "") { $actions_query .= " ($where) AND"; }
      
      // LIMIT RESULTS TO TIME PERIOD SPECIFIED BY ADMIN
      $actions_query .= " se_actions.action_date>".($nowdate-$setting['setting_actions_showlength']);
      
      // ORDER BY ACTION ID DESCENDING
      $actions_query .= " ORDER BY action_date DESC";
      
      // LIMIT RESULTS TO MAX NUMBER SPECIFIED BY ADMIN
      $actions_query .= " LIMIT {$setting['setting_actions_actionsinlist']}";
      
      // GET RECENT ACTIVITY FEED
      $actions = $database->database_query($actions_query);
      $actions_array = Array();
      $actions_users_array = Array();
      while($action = $database->database_fetch_assoc($actions))
      {
        // ONLY DISPLAY THIS ACTION IF MAX OCCURRANCES PER USER HAS NOT YET BEEN REACHED
        $actions_users_array[] = $action['action_user_id'];
        $occurrances = array_count_values($actions_users_array);
        if($occurrances[$action['action_user_id']] <= $actionsperuser)
        {
          // UNSERIALIZE VARIABLES
          // NOTE: I don't like mb_unserialize: it ignores the strlen param. But it works...
          if( ($action_vars = unserialize($action['action_text']))===FALSE )
            $action_vars = mb_unserialize($action['action_text']);
          
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
			  'user_photo' => $action[action_user_photo],
              //'action_username' => $action_username_info['user_username'],
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
  
  // END actions_display() METHOD
  
  
  
  
  
  
  function actions_allowed()
  {
    global $user, $setting, $database;
    
    if( !$setting['setting_actions_preference'] )
      return FALSE;
    
    $actiontypes_array = NULL;
    
    // CACHING
    $cache_object = SECache::getInstance('serial');
    if( is_object($cache_object) )
    {
      $actiontypes_array = $cache_object->get('actiontypes');
    }
    
    // RETRIEVAL
    if( !is_array($actiontypes_array) || empty($actiontypes_array) )
    {
      $resource = $database->database_query("SELECT actiontype_id, actiontype_desc FROM se_actiontypes WHERE actiontype_enabled=1");
      while( $actiontype = $database->database_fetch_assoc($resource) )
      {
        $actiontypes_array[] = $actiontype;
      }
      
      // CACHE
      if( is_object($cache_object) )
      {
        $cache_object->store($actiontypes_array, 'actiontypes');
      }
    }
    
    
    // POST PROCESSING
    if( empty($user->usersetting_info) ) $user->user_settings();
    $actiontypes_display = explode(",", $user->usersetting_info['usersetting_actions_display']);
    
    foreach( $actiontypes_array as $actiontype_index=>$actiontype )
    {
      SE_Language::_preload($actiontype['actiontype_desc']);
      
      // MAKE THIS ACTION TYPE SELECTED IF ITS NOT DISALLOWED BY USER
      $actiontypes_array[$actiontype_index]['actiontype_selected'] = ( in_array($actiontype['actiontype_id'], $actiontypes_display) );
    }
    
    
    return $actiontypes_array;
  }
}

?>