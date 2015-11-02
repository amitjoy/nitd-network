<?php


//  THIS CLASS IS USED TO OUTPUT AND UPDATE NOTIFICATIONS
//  METHODS IN THIS CLASS:
//    notify_add()
//    notify_summary()



class se_notify
{
	// THIS METHOD ADDS A NEW NOTIFICATION
	// INPUT: $user_id REPRESENTING THE USER ID OF THE USER WHO COMMITTED THE ACTION
	//	  $notifytype REPRESENTING THE ID OF THE TYPE OF NOTIFICATION
	//	  $notify_object_id REPRESENTING THE ID OF THE OBJECT (FOR LATER DELETING PURPOSES)
	//	  $urlvars (OPTIONAL) REPRESENTING VARS TO USE IN THE NOTIFYTYPE URL
	//	  $replace (OPTIONAL) REPRESENTING AN ARRAY OF VALUES FOR THE NOTIFICATION TEXT STRING (MUST CORRESPOND TO NOTIFYTYPE_VARS)
	//	  $update (OPTIONAL) REPRESENTING WHETHER TO INSERT A NEW NOTIFICATION IF AN OLD ONE WITH THE SAME OBJECT ID EXISTS
  
	function notify_add($user_id, $notifytype, $notify_object_id = 0, $urlvars = Array(), $replace = Array(), $update = FALSE)
  {
	  global $database, $setting;
    
	  // GET CURRENT DATE
	  $nowdate = time();
    
	  // GET NOTIFY TYPE
	  $notifytype_query = $database->database_query("SELECT * FROM se_notifytypes WHERE notifytype_name='{$notifytype}'");
	  if($database->database_num_rows($notifytype_query) != 1) { return false; }
	  $notifytype = $database->database_fetch_assoc($notifytype_query);
    
	  // SERIALIZE APPROPRIATE VARS
	  $notify_text = serialize($replace);
	  $notify_urlvars = serialize($urlvars);

	  // RETRIEVE OLD NOTIFICATION IF UPDATE NECESSARY
	  $insert = TRUE;
	  if($update)
    {
	    $old_notify = $database->database_fetch_assoc($database->database_query("SELECT count(*) AS total_notifys FROM se_notifys WHERE notify_user_id='{$user_id}' AND notify_notifytype_id='{$notifytype['notifytype_id']}' AND notify_object_id='{$notify_object_id}'"));
	    if( $old_notify['total_notifys'] ) $insert = FALSE;
	  }

	  // INSERT DATA
	  if($insert)
    {
	    $database->database_query("
        INSERT INTO se_notifys(
          notify_user_id,
          notify_notifytype_id, 
          notify_object_id,
          notify_urlvars,
          notify_text
        ) VALUES (
          '{$user_id}',
          '{$notifytype['notifytype_id']}',
          '{$notify_object_id}',
          '{$notify_urlvars}', 
          '{$notify_text}'
        )
      ");
	  }
    
    $database->database_query("UPDATE se_users SET user_hasnotifys=1 WHERE user_id='{$user_id}' LIMIT 1");
    
	  // RETURN NOTIFY TYPE
	  return $notifytype;
	}










	// THIS METHOD DELETES A NOTIFICATION
  
	function notify_delete($notifytype_id, $notify_grouped)
  {
    global $user, $database;
    
    if( !$notifytype_id || !$user->user_exists )
      return FALSE;
    
    // BUILD QUERY
    $delete_query = "DELETE FROM se_notifys WHERE notify_notifytype_id='{$notifytype_id}' AND notify_user_id='{$user->user_info['user_id']}'";
    if( $notify_grouped ) $delete_query .= " AND notify_object_id='{$notify_grouped}'";
    
    // DELETE ACTION (IF OWNED BY LOGGED-IN USER)
    $database->database_query($delete_query);
    
    // UPDATE user notify cache
    $resource = $database->database_query("SELECT NULL FROM se_notifys WHERE notify_user_id='{$user->user_info['user_id']}' LIMIT 1");
    $has_notifys = $database->database_num_rows($resource);
    
    if( $has_notifys != $user->user_info['user_hasnotifys'] )
    {
      $has_notifys = ( $user->user_info['user_hasnotifys'] ? '1' : '0' );
      $database->database_query("UPDATE se_users SET user_hasnotifys={$has_notifys} WHERE user_id='{$user->user_info['user_id']}' LIMIT 1");
    }
    
    return TRUE;
	}
  
  // END notify_delete() METHOD










	// THIS METHOD DISPLAYS A SUMMARY OF NOTIFICATIONS RELATING TO A SPECIFIC USER
	// INPUT: 
	// OUTPUT: SUMMARY OF NOTIFICATIONS FOR THAT USER
  
	function notify_summary()
  {
	  global $database, $user;
    
    $total_notifications = 0;
    $notify_array = array();
    
	  // CHECK THAT USER EXISTS
	  if( is_object($user) && $user->user_exists && $user->user_info['user_hasnotifys'] )
    {
      // BUILD NOTIFICATION QUERY
      $notify_query = "
        (
          SELECT 
            '0' AS notify_grouped,
            count(se_notifys.notify_id) AS total_notifications, 
            se_notifytypes.notifytype_id, 
            se_notifytypes.notifytype_desc, 
            se_notifytypes.notifytype_icon, 
            se_notifytypes.notifytype_url, 
            se_notifys.notify_urlvars, 
            se_notifys.notify_text 
          FROM se_notifys 
          LEFT JOIN se_notifytypes 
          ON se_notifys.notify_notifytype_id=se_notifytypes.notifytype_id 
          WHERE 
            notify_user_id='{$user->user_info['user_id']}'
          AND
            notifytype_group=1
          GROUP BY se_notifys.notify_notifytype_id
        ) UNION ALL (
          SELECT 
            se_notifys.notify_object_id AS notify_grouped,
            count(se_notifys.notify_id) AS total_notifications, 
            se_notifytypes.notifytype_id, 
            se_notifytypes.notifytype_desc, 
            se_notifytypes.notifytype_icon, 
            se_notifytypes.notifytype_url, 
            se_notifys.notify_urlvars, 
            se_notifys.notify_text 
          FROM se_notifys 
          LEFT JOIN se_notifytypes 
          ON se_notifys.notify_notifytype_id=se_notifytypes.notifytype_id 
          WHERE 
            notify_user_id='{$user->user_info['user_id']}' 
          AND
            notifytype_group=0
          GROUP BY se_notifys.notify_notifytype_id, se_notifys.notify_object_id
        )
      ";
      
      // GET NOTIFICATIONS
      $notifys = $database->database_query($notify_query);
      while( $notify = $database->database_fetch_assoc($notifys) )
      {
        // REGISTER PRELOADED TEXT
        SE_Language::_preload($notify['notifytype_desc']);
        
        // GET URL VARS
        $urlvars = unserialize($notify['notify_urlvars']);
        $notify_url = vsprintf($notify['notifytype_url'], $urlvars);
        
        // GET DESC TEXT VARS
        $notify_text = unserialize($notify['notify_text']);
        
        // ADD THIS NOTIFICATION TO OUTPUT ARRAY
        $total_notifications += $notify['total_notifications'];
        $notify_array[] = Array(
          'notifytype_id' => $notify['notifytype_id'],
          'notify_grouped' => $notify['notify_grouped'],
          'notify_icon' => $notify['notifytype_icon'],
          'notify_url' => $notify_url,
          'notify_desc' => $notify['notifytype_desc'],
          'notify_text' => $notify_text,
          'notify_total' => $notify['total_notifications']
        );
      }
    }
    
	  // RETURN LIST OF NOTIFICATIONS
	  return array(
      'total' => (int) $total_notifications,
      'total_grouped' => (int) count($notify_array),
      'notifys' => $notify_array
    );
	}
  
  // END notify_summary() METHOD
}

?>