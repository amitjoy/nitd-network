<?php


//
//  THIS CLASS CONTAINS EVENT-RELATED METHODS 
//
//  METHODS IN THIS CLASS:
//
//    se_event()
//
//    event_total()
//    event_list()
//    event_edit()
//    event_edit_settings()
//    event_delete()
//    event_lastupdate()
//    event_privacy_max()
//
//    event_member_total()
//    event_member_list()
//
//    event_dir()
//
//    event_photo()
//    event_photo_upload()
//    event_photo_delete()
//
//    event_media_upload()
//    event_media_space()
//    event_media_total()
//    event_media_list()
//    event_media_update()
//    event_media_delete()
//
//    event_calendar_generate()
//
//    event_member_invite()
//    event_member_approve()
//    event_member_reject()
//    event_member_cancel()
//    event_member_remove()
//
//    event_join()
//    event_leave()
//    event_rsvp()
//
//    event_rsvp_level()
//    event_generate_javascript_structure()
//


defined('SE_PAGE') or exit();





class se_event
{
	// INITIALIZE VARIABLES
	var $is_error;			        // DETERMINES WHETHER THERE IS AN ERROR OR NOT
	var $error_message;		      // CONTAINS RELEVANT ERROR MESSAGE

	var $user_id;			          // CONTAINS THE USER ID OF THE USER WHOSE EVENTS WE ARE EDITING OR THE LOGGED-IN USER
	var $is_member;			        // DETERMINES WHETHER USER IS IN THE EVENTMEMBER TABLE OR NOT

	var $event_exists;		      // DETERMINES WHETHER THE EVENT HAS BEEN SET AND EXISTS OR NOT

	var $event_info;		        // CONTAINS THE EVENT INFO OF THE EVENT WE ARE EDITING
	var $eventvalue_info;		    // CONTAINS THE EVENT FIELD VALUE INFO
	var $eventowner_level_info;	// CONTAINS THE EVENT CREATOR'S LEVEL INFO
	var $eventmember_info;		  // CONTAINS THE EVENT MEMBER INFO FOR THE LOGGED-IN USER

	var $moderation_privacy;	  // CONTAINS THE PRIVACY LEVEL THAT IS ALLOWED TO MODERATE FOR THIS USER
  
  
  var $event_rsvp_levels = array(
    '-2'  => 3000081,
    '-1'  => 3000080,
    '0'   => 3000081,
    '1'   => 3000082,
    '2'   => 3000083,
    '3'   => 3000084,
    '4'   => 3000085
  );
  
  
  
  
  
  
  
  //
	// THIS METHOD SETS INITIAL VARS
  //
	// INPUT:
  //    $user_id    (OPTIONAL) REPRESENTING THE USER ID OF THE USER WHOSE EVENTS WE ARE CONCERNED WITH
	//	  $event_id   (OPTIONAL) REPRESENTING THE EVENT ID OF THE EVENT WE ARE CONCERNED WITH
  //
	// OUTPUT: 
  //    void
  //
  
	function se_event($user_id=NULL, $event_id=NULL)
  {
	  global $database, $user, $owner;
    
	  $this->user_id            = $user_id;
	  $this->event_exists       = FALSE;
	  $this->is_member          = FALSE;
	  $this->is_member_waiting  = FALSE;
	  $this->user_rank          = 0;
	  $this->moderation_privacy = 3;
    
    // RSVP LEVELS
    foreach( $this->event_rsvp_levels as $rsvp_level )
      SE_Language::_preload($rsvp_level);
    
    // EVENT INFO
	  if( $event_id )
    {
      $sql = "SELECT * FROM se_events WHERE event_id='{$event_id}' LIMIT 1";
      $resource = $database->database_query($sql);
      
	    if( $database->database_num_rows($resource) )
      {
	      $this->event_exists = TRUE;
	      $this->event_info   = $database->database_fetch_assoc($resource);
        
        // GET FIELD VALUE INFO
        $sql = "SELECT * FROM se_eventvalues WHERE eventvalue_event_id='{$event_id}' LIMIT 1";
        $resource = $database->database_query($sql);
        
        if( $database->database_num_rows($resource) )
          $this->eventvalue_info = $database->database_fetch_assoc($resource);
	      
        
	      // GET LEVEL INFO
	      if( $this->event_info['event_user_id']==$user->user_info['user_id'])
        {
	        $this->eventowner_level_info =& $user->level_info;
	      }
        elseif( $this->event_info['event_user_id']==$owner->user_info['user_id'])
        {
	        $this->eventowner_level_info =& $owner->level_info;
	      }
        else
        {
          $sql = "SELECT se_levels.* FROM se_users LEFT JOIN se_levels ON se_users.user_level_id=se_levels.level_id WHERE se_users.user_id='{$this->event_info['event_user_id']}'";
          $resource = $database->database_query($sql);
          $this->eventowner_level_info = $database->database_fetch_assoc($resource);
	      }
        
        
        // GET MEMBER INFO OF CURRENT USER
	      if( $this->user_id )
        {
          $sql = "SELECT * FROM se_eventmembers WHERE eventmember_user_id='{$this->user_id}' AND eventmember_event_id='{$event_id}' LIMIT 1";
	        $resource = $database->database_query($sql);
          
	        if( $database->database_num_rows($resource) )
          {
	          $this->eventmember_info = $database->database_fetch_assoc($resource);
            $this->is_member = ( $this->eventmember_info['eventmember_status'] && $this->eventmember_info['eventmember_approved'] );
            $this->is_member_waiting = ( $this->eventmember_info['eventmember_status'] xor $this->eventmember_info['eventmember_approved'] );
            $this->user_rank =& $this->eventmember_info['eventmember_rank'];
	        }
	      }
	    }
	  }
	}
  
  // END se_event() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD RETURNS THE TOTAL NUMBER OF EVENTS
  //
	// INPUT:
  //    $where          (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
	//	  $event_details  (OPTIONAL) REPRESENTING A BOOLEAN THAT DETERMINES WHETHER TO RETRIEVE EVENT CREATOR
  //
	// OUTPUT:
  //    AN INTEGER REPRESENTING THE NUMBER OF EVENTS
  //
  
	function event_total($where=NULL, $event_details=FALSE)
  {
	  global $database;
    
	  // BEGIN QUERY
	  $sql = "
      SELECT
        NULL
    ";
    
	  // IF USER ID NOT EMPTY, GET USER AS MEMBER
	  if( $this->user_id ) $sql .= ",
        se_eventmembers.eventmember_status
    ";
    
	  // CONTINUE QUERY
	  $sql .= "
      FROM
        se_events
    ";
    
	  // IF USER ID NOT EMPTY, SELECT FROM EVENTMEMBER TABLE
	  if( $this->user_id ) $sql .= "
      LEFT JOIN
        se_eventmembers
        ON se_eventmembers.eventmember_event_id=se_events.event_id
    "; 
    
	  // CONTINUE QUERY IF NECESSARY
	  if( $event_details ) $sql .= "
      LEFT JOIN
        se_users
        ON se_users.user_id=se_events.event_user_id
    ";
    
	  // ADD WHERE IF NECESSARY
	  if( $where || $this->user_id ) $sql .= "
      WHERE
    ";
    
	  // IF USER ID NOT EMPTY, MAKE SURE USER IS A MEMBER
	  if( $this->user_id ) $sql .= "
        se_eventmembers.eventmember_user_id='{$this->user_id}' &&
        se_eventmembers.eventmember_approved=1
    ";
    
	  // INSERT AND IF NECESSARY
	  if( $this->user_id && $where )
      $sql .= " AND";
    
	  // ADD WHERE CLAUSE, IF NECESSARY
	  if( $where ) $sql .= "
        $where
    ";
    
	  // ADD GROUP BY
	  $sql .= "
      GROUP BY
        event_id
    ";
    
	  // RUN QUERY
    $resource = $database->database_query($sql);
	  return (int) $database->database_num_rows($resource);
	}
  
  // END event_total() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD RETURNS AN ARRAY OF EVENTS
  //
	// INPUT:
  //    $start          REPRESENTING THE EVENT TO START WITH
	//	  $limit          REPRESENTING THE NUMBER OF EVENTS TO RETURN
	//	  $sort_by        (OPTIONAL) REPRESENTING THE ORDER BY CLAUSE
	//	  $where          (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
	//	  $event_details  (OPTIONAL) REPRESENTING A BOOLEAN THAT DETERMINES WHETHER TO RETRIEVE EVENT CREATOR
  //
	// OUTPUT:
  //    AN ARRAY OF EVENTS
  //
  
	function event_list($start, $limit, $sort_by="event_id DESC", $where=NULL, $event_details=FALSE)
  {
	  global $database, $user;
    
    if( !$start ) $start = '0';
    
	  // BEGIN QUERY
	  $sql = "
      SELECT
        se_events.*
    ";
    
	  // SELECT RELEVANT EVENT DETAILS IF NECESSARY
	  if( $event_details ) $sql .= ",
        se_users.user_id,
        se_users.user_username,
        se_users.user_photo,
        se_users.user_fname,
        se_users.user_lname
    ";
    
	  // IF USER ID NOT EMPTY, GET USER AS MEMBER
	  if( $this->user_id ) $sql .= ",
        se_eventmembers.eventmember_status,
        se_eventmembers.eventmember_approved,
        se_eventmembers.eventmember_rsvp,
        se_eventmembers.eventmember_rank
    ";
    
	  // CONTINUE QUERY
	  $sql .= "
      FROM
        se_events
    ";

	  // IF USER ID NOT EMPTY, SELECT FROM EVENTMEMBER TABLE
	  if( $this->user_id ) $sql .= "
      LEFT JOIN
        se_eventmembers
        ON se_eventmembers.eventmember_event_id=se_events.event_id
    ";
    
	  // CONTINUE QUERY IF NECESSARY
	  if( $event_details ) $sql .= "
      LEFT JOIN
        se_users
        ON se_users.user_id=se_events.event_user_id
    ";
    
	  // ADD WHERE IF NECESSARY
	  if( $where || $this->user_id ) $sql .= "
      WHERE
    ";
    
	  // IF USER ID NOT EMPTY, MAKE SURE USER IS A MEMBER
	  if( $this->user_id ) $sql .= "
        se_eventmembers.eventmember_user_id='{$this->user_id}'
        /*
        &&
        se_eventmembers.eventmember_approved=1
        */
    ";
    
	  // INSERT AND IF NECESSARY
	  if( $this->user_id && $where )
      $sql .= " AND";
    
	  // ADD WHERE CLAUSE, IF NECESSARY
	  if( $where ) $sql .= "
        $where
    ";
    
	  // ADD GROUP BY, ORDER, AND LIMIT CLAUSE
	  $sql .= "
      ORDER BY
        $sort_by
      LIMIT
        $start, $limit
    ";
    
	  // RUN QUERY
    $resource = $database->database_query($sql);
    
	  // GET EVENTS INTO AN ARRAY
	  $event_array = array();
	  while( $event_info=$database->database_fetch_assoc($resource) )
    {
      $event_item = array();
      
	    // CREATE OBJECT FOR EVENT
	    $event = new se_event($event_info['user_id']);
	    $event->event_exists = TRUE;
	    $event->is_member = ( $event_info['eventmember_status'] && $event_info['eventmember_approved'] );
	    $event->is_member_waiting = ( $event_info['eventmember_status'] xor $event_info['eventmember_approved'] );
	    $event->user_rank = ( isset($event_info['eventmember_rank']) ? $event_info['eventmember_rank'] : 0 );
	    $event->event_info =& $event_info;
      $event->event_info['event_desc'] = htmlspecialchars_decode($event->event_info['event_desc'], ENT_QUOTES);
      
	    // CREATE OBJECT FOR EVENT CREATOR IF EVENT DETAILS
	    if( $event_details )
      {
      	$creator = new se_user();
	      $creator->user_exists = TRUE;
	      $creator->user_info['user_id']        = $event_info['user_id'];
	      $creator->user_info['user_username']  = $event_info['user_username'];
	      $creator->user_info['user_photo']     = $event_info['user_photo'];
	      $creator->user_info['user_fname']     = $event_info['user_fname'];
	      $creator->user_info['user_lname']     = $event_info['user_lname'];
	      $creator->user_displayname();
        
        $event_item['event_creator'] = &$creator;
	    }
      
      
	    // SET EVENT ARRAY
      $event_item['event'] = &$event;
      $event_item['event_status'] = $event_info['eventmember_status'];
      $event_item['event_approved'] = $event_info['eventmember_approved'];
      $event_item['event_rsvp'] = $event_info['eventmember_rsvp'];
      $event_item['event_rank'] = $event_info['eventmember_rank'];
      
      // PRELOAD
      if( $event_info['eventmember_status'] && !$event_info['eventmember_approved'] )
        $event_item['event_rsvp_lvid'] = $this->event_rsvp_levels['-1'];
      elseif( !$event_info['eventmember_status'] && $event_info['eventmember_approved'] )
        $event_item['event_rsvp_lvid'] = $this->event_rsvp_levels['-2'];
      else
        $event_item['event_rsvp_lvid'] = $this->event_rsvp_levels[$event_info['eventmember_rsvp']];
      
	    $event_array[] = $event_item;
      unset($event_info, $event, $creator, $event_item);
	  }
    
	  // RETURN ARRAY
	  return $event_array;
	}
  
  // END event_list() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD CREATES/EDITS AN EVENT
  //
	// INPUT:
  //    $event_title        REPRESENTING THE EVENT TITLE
	//	  $event_desc         REPRESENTING THE EVENT DESCRIPTION
	//	  $event_eventcat_id  REPRESENTING THE EVENT CATEGORY ID
	//	  $event_date_start   REPRESENTING THE EVENT'S START TIMESTAMP
	//	  $event_date_end     REPRESENTING THE EVENT'S END TIMESTAMP
	//	  $event_host         REPRESENTING THE EVENT'S HOST
	//	  $event_location     REPRESENTING THE EVENT'S LOCATION
	//	  $event_field_query  REPRESENTING THE QUERY TO UPDATE THE EVENT'S FIELDS
  //
	// OUTPUT:
  //    THE EVENT'S ID OR FALSE
  //
  
	function event_edit(&$event_title, &$event_desc, &$event_eventcat_id, $event_date_start, $event_date_end, &$event_host, &$event_location, $event_field_query)
  {
	  global $database, $user, $actions;
    
    // VALIDATE OWNER
    if( $this->event_exists && $this->user_rank<2 )
    {
      $this->is_error = 3000248;
      return FALSE;
    }
    
    // INIT VARS
    $event_id       = ( !empty($this->event_info['event_id']) ? $this->event_info['event_id'] : NULL );
    $event_title    = censor($event_title);
    $event_desc     = censor(str_replace("\r\n", "<br />", html_entity_decode($event_desc, ENT_QUOTES)));
    $event_desc     = security(cleanHTML($event_desc, $user->level_info['level_event_html']));
    $event_host     = censor($event_host);
    $event_location = censor(str_replace("\r\n", "<br />", $event_location));
    $time           = time();
    
    // CHECK TO MAKE SURE TITLE HAS BEEN ENTERED
    if( !trim($event_title) )
    {
      $this->is_error = 3000246;
      return FALSE;
    }
    
    // CHECK TO MAKE SURE CATEGORY HAS BEEN SELECTED
    if( !$event_eventcat_id )
    {
      $this->is_error = 3000247;
      return FALSE;
    }
    
    // CHECK TO MAKE SURE END DATE IS AFTER START DATE (IF END DATE IS SET)
    if( $event_date_end && $event_date_end<$event_date_start )
    {
      $this->is_error = 3000249;
      return FALSE;
    }
    
    // CHECK TO MAKE SURE THAT START DATE IS IN THE FUTURE IF BACKDATING NOT ALLOWED
    if( !$user->level_info['level_event_backdate'] && $event_date_start<time() )
    {
      // IF CREATING, ERROR
      if( !$this->event_exists )
      {
        $this->is_error = 3000250;
        return FALSE;
      }
      
      // IF EDITING, ERROR ONLY IF THERE IS NO CHANGE
      elseif( $event_date_start!=$this->event_info['event_date_start'] || $event_date_end!=$this->event_info['event_date_end'] )
      {
        $this->is_error = 3000250;
        return FALSE;
      }
    }
    
    // CREATE
    if( !$event_id )
    {
      // ADD ROW TO EVENTS TABLE
      $sql = "
        INSERT INTO se_events (
          event_user_id,
          event_eventcat_id,
          event_title,
          event_desc,
          event_date_start,
          event_date_end,
          event_host,
          event_location,
          event_datecreated
        ) VALUES (
          '{$this->user_id}',
          '{$event_eventcat_id}',
          '{$event_title}',
          '{$event_desc}',
          '{$event_date_start}',
          '{$event_date_end}',
          '{$event_host}',
          '{$event_location}',
          '{$time}'
        )
      ";
      
      $resource = $database->database_query($sql);
      $event_id = $database->database_insert_id();
      
      // MAKE EVENT EXIST
      if( $event_id )
      {
        $this->event_exists = TRUE;
        $this->is_member = TRUE;
        $this->user_rank = 3;
        $this->event_info['event_id'] = $event_id;
        $this->event_info['event_user_id'] = $this->user_id;
        $this->eventowner_level_info =& $user->level_info;
      }
      
      // MAKE CREATOR A MEMBER
      $sql = "INSERT INTO se_eventmembers (eventmember_user_id, eventmember_event_id, eventmember_status, eventmember_approved, eventmember_rank) VALUES ('{$this->user_id}', '{$event_id}', '1', '1', '3')";
      $resource = $database->database_query($sql);
      
      // ADD EVENT STYLES ROW
      $sql = "INSERT INTO se_eventstyles (eventstyle_event_id) VALUES ('{$event_id}')";
      $resource = $database->database_query($sql);
      
      // ADD EVENT VALUES ROW
      $sql = "INSERT INTO se_eventvalues (eventvalue_event_id) VALUES ('{$event_id}')";
      $resource = $database->database_query($sql);
      
      
      // ADD EVENT ALBUM
      $sql = "
        INSERT INTO se_eventalbums
          (eventalbum_event_id, eventalbum_datecreated, eventalbum_dateupdated, eventalbum_title, eventalbum_desc, eventalbum_search, eventalbum_privacy, eventalbum_comments)
        VALUES
          ('{$event_id}', '{$time}', '{$time}', '', '', '{$this->event_info['event_search']}', '{$this->event_info['event_privacy']}', '{$this->event_info['event_comments']}')
      ";
      
      $resource = $database->database_query($sql);
      
      
      // INSERT ACTION
      $event_title = $this->event_info['event_title'];
      if(strlen($event_title) > 100) { $event_title = substr($event_title, 0, 97)."..."; }
      $actions->actions_add(
        $user,
        "newevent",
        Array(
          $user->user_info['user_username'],
          $user->user_displayname,
          $this->event_info['event_id'],
          $event_title
        ),
        NULL,
        NULL,
        FALSE,
        "event",
        $event_id,
        $this->event_info['event_privacy']
      );
    }
    
    // EDIT
    else
    {
      // IF NEW INVITE ONLY SETTING IS CHANGED TO 0, APPROVE ALL REQUESTS FOR INVITATION
      if( !$event_inviteonly )
      {
        $sql = "UPDATE se_eventmembers SET eventmember_status='1' WHERE eventmember_event_id='{$this->event_info['event_id']}' AND eventmember_status='0'";
        $resource = $database->database_query($sql);
      }
      
      // UPDATE EVENT
      $sql = "
        UPDATE
          se_events
        SET
          event_title='{$event_title}',
          event_eventcat_id='{$event_eventcat_id}',
          event_desc='{$event_desc}',
          event_date_start='{$event_date_start}',
          event_date_end='{$event_date_end}',
          event_host='{$event_host}',
          event_location='{$event_location}',
          event_dateupdated={$time}
        WHERE
          event_id='{$event_id}'
        LIMIT
          1
      ";
      
      $resource = $database->database_query($sql);
    }
    
    // TODO: UPDATE EVENT VALUES $event_field_query
    if( !empty($event_field_query) )
    {
      $sql = " UPDATE se_eventvalues SET {$event_field_query} WHERE eventvalue_event_id='{$event_id}' LIMIT 1";
      $resource = $database->database_query($sql);
    }
    
    
	  // ADD EVENT DIRECTORY
	  $event_directory = $this->event_dir($event_id);
	  $event_path_array = explode("/", $event_directory);
	  array_pop($event_path_array);
	  array_pop($event_path_array);
	  $subdir = implode("/", $event_path_array)."/";
    
	  if( !is_dir($subdir) )
    { 
	    mkdir($subdir, 0777); 
	    chmod($subdir, 0777); 
	    if( $handle=fopen($subdir."index.php", 'x+') )
        fclose($handle);
	  }
    
    if( !is_dir($event_directory) )
    {
      mkdir($event_directory, 0777);
      chmod($event_directory, 0777);
	    if( $handle=fopen($event_directory."/index.php", 'x+') )
        fclose($event_directory);
    }
    
	  return $event_id;
	}
  
  // END event_edit() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD EDITS AN EVENT'S SETTINGS
  //
	// INPUT:
	//	  $event_search     REPRESENTING WHETHER THE EVENT SHOULD BE SEARCHABLE
	//	  $event_inviteonly REPRESENTING WHETHER UNINVITED USERS MUST REQUEST A MEMBERSHIP TO RSVP
	//	  $event_privacy    REPRESENTING WHO CAN VIEW THE EVENT
	//	  $event_comments   REPRESENTING WHO CAN POST COMMENTS ON THE EVENT
	//	  $event_upload     REPRESENTING WHO CAN POST UPLOAD PHOTOS TO THE EVENT
	//	  $event_tag        REPRESENTING WHO CAN POST TAG PHOTOS IN THE EVENT
	//	  $event_invite     REPRESENTING WHO CAN INVITE PPL
  //
	// OUTPUT:
  //    TRUE/FALSE
  //
  
	function event_edit_settings(&$event_search, &$event_inviteonly, &$event_privacy, &$event_comments, &$event_upload, &$event_tag, &$event_invite)
  {
	  global $database;
    
    // VALIDATE OWNER
    if( $this->user_rank<2 )
    {
      $this->is_error = 3000248;
      return FALSE;
    }
    
    // GET PRIVACY SETTINGS
    $level_event_privacy = unserialize($this->eventowner_level_info['level_event_privacy']);
    rsort($level_event_privacy);
    $level_event_comments = unserialize($this->eventowner_level_info['level_event_comments']);
    rsort($level_event_comments);
    $level_event_upload = unserialize($this->eventowner_level_info['level_event_upload']);
    rsort($level_event_upload);
    $level_event_tag = unserialize($this->eventowner_level_info['level_event_tag']);
    rsort($level_event_tag);
    
    // MAKE SURE SUBMITTED PRIVACY OPTIONS ARE ALLOWED, IF NOT, SET TO MOST PUBLIC
    if( !in_array($event_privacy, $level_event_privacy) )
      $event_privacy = $level_event_privacy[0];
    if( !in_array($event_comments, $level_event_comments))
      $event_comments = $level_event_comments[0];
    if( !in_array($event_upload, $level_event_upload))
      $event_upload = $level_event_upload[0];
    if( !in_array($event_tag, $level_event_tag))
      $event_tag = $level_event_tag[0];
    
    // CHECK THAT SEARCH IS NOT BLANK
    if( !$this->eventowner_level_info['level_event_search'] )
      $event_search = 1;
    
    // CHECK THAT INVITE ONLY IS NOT BLANK
    if( !$this->eventowner_level_info['level_event_inviteonly'] )
      $event_inviteonly = 0;
    
    $time = time();
    
    // UPDATE EVENT
    $sql = "
      UPDATE
        se_events
      SET
        event_search='{$event_search}',
        event_invite='{$event_invite}',
        event_inviteonly='{$event_inviteonly}',
        event_privacy='{$event_privacy}',
        event_comments='{$event_comments}',
        event_upload='{$event_upload}',
        event_tag='{$event_tag}',
        event_dateupdated={$time}
      WHERE
        event_id='{$this->event_info['event_id']}'
      LIMIT
        1
    ";
    
    $resource = $database->database_query($sql);
    $result1 = (bool) $database->database_affected_rows($resource);
    
    // UPDATE EVENT ALBUMS
    $sql = "UPDATE se_eventalbums SET eventalbum_privacy='{$event_privacy}', eventalbum_comments='{$event_comments}', eventalbum_search='{$event_search}', eventalbum_tag='{$event_tag}' WHERE eventalbum_event_id='{$this->event_info['event_id']}'";
    $resource = $database->database_query($sql);
    $result2 = (bool) $database->database_affected_rows($resource);
    
    return ($result1 && $result2);
  }
  
  // END event_edit_settings() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD DELETES AN EVENT
  //
	// INPUT:
  //    $event_id   (OPTIONAL) REPRESENTING THE ID OR AN ARRAY OF IDS OF EVENTS TO DELETE
  //
	// OUTPUT:
  //    void
  //
  
	function event_delete($event_id=NULL)
  {
	  global $database;
    
    // IF EMPTY, TRY TO GET FROM OBJECT
	  if( !$event_id && !$this->event_exists )
      return FALSE;
    elseif( !$event_id )
      $event_id = $this->event_info['event_id'];
    
    // IF ARRAY
    if( is_array($event_id) )
      return array_map(array(&$this, 'event_delete'), $event_id);
    
	  // DELETE EVENT ROW
	  $sql = "DELETE FROM se_events WHERE se_events.event_id='{$event_id}'";
    if( $this->user_id ) $sql .= " && event_user_id='{$this->user_id}'";
    $resource = $database->database_query($sql);
    
    // EVENT WAS NOT DELETED, POSSIBLE SECURITY VIOLATION
    if( !$database->database_affected_rows($resource) )
      return FALSE;
    
	  // DELETE EVENT ALBUM, MEDIA, MEDIA COMMENTS
    $sql = "DELETE FROM se_eventalbums, se_eventmedia, se_eventmediacomments USING se_eventalbums LEFT JOIN se_eventmedia ON se_eventalbums.eventalbum_id=se_eventmedia.eventmedia_eventalbum_id LEFT JOIN se_eventmediacomments ON se_eventmedia.eventmedia_id=se_eventmediacomments.eventmediacomment_eventmedia_id WHERE se_eventalbums.eventalbum_event_id='{$event_id}'";
	  $resource = $database->database_query($sql);
    
	  // DELETE ALL MEMBERS
	  $sql = "DELETE FROM se_eventmembers WHERE se_eventmembers.eventmember_event_id='{$event_id}'";
    $resource = $database->database_query($sql);
    
	  // DELETE EVENT STYLE
	  $sql = "DELETE FROM se_eventstyles WHERE se_eventstyles.eventstyle_event_id='{$event_id}'";
    $resource = $database->database_query($sql);
    
	  // DELETE EVENT COMMENTS
	  $sql = "DELETE FROM se_eventcomments WHERE se_eventcomments.eventcomment_event_id='{$event_id}'";
    $resource = $database->database_query($sql);
    
	  // DELETE EVENT VALUES
	  $sql = "DELETE FROM se_eventvalues WHERE se_eventvalues.eventvalue_event_id='{$event_id}'";
    $resource = $database->database_query($sql);
    
	  // DELETE EVENT'S FILES
    $event_directory = $this->event_dir($event_id);
    
	  if( !is_dir($event_directory) && is_dir(".".$event_directory) )
      $event_directory = ".".$event_directory;
    
    if( is_dir($event_directory) && ($dh=@opendir($dir)) )
    {
	    while( ($file = @readdir($dh))!==FALSE )
      {
	      if( $file=="." || $file==".." ) continue;
	      @unlink($dir.$file);
	    }
	    @closedir($dh);
      @rmdir($dir);
	  }
    
    return TRUE;
	}
  
  // END event_delete() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD UPDATES THE EVENT'S LAST UPDATE DATE
  //
	// INPUT: 
  //    void
  //
	// OUTPUT: 
  //    void
  //
  
	function event_lastupdate()
  {
	  global $database;
    $sql = "UPDATE se_events SET event_dateupdated='".time()."' WHERE event_id='{$this->event_info['event_id']}'";
	  $resource = $database->database_query($sql);
	}
  
  // END event_lastupdate() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD RETURNS MAXIMUM PRIVACY LEVEL VIEWABLE BY A USER WITH REGARD TO THE CURRENT EVENT
  //
	// INPUT:
  //    $user REPRESENTING A USER OBJECT
  //
	// OUTPUT:
  //    RETURNS PRIVACY LEVEL OF GIVEN USER WITH RESPECT TO CURRENT EVENT
  //
  
	function event_privacy_max($user)
  {
	  global $database;
    
    if( !is_object($user) || !$user->user_exists )
      return 64;
    
	  switch( TRUE )
    {
	    // EVENT CREATOR
	    case( $this->event_info['event_user_id']==$user->user_info['user_id'] ):
	      return 1;
	      break;
        
	    // EVENT INVITEE
	    case($database->database_num_rows($database->database_query("SELECT eventmember_id FROM se_eventmembers WHERE eventmember_user_id='{$user->user_info['user_id']}' AND eventmember_event_id='{$this->event_info['event_id']}' AND eventmember_status<>'-1'")) != 0):
	      return 2;
	      break;
        
	    // EVENT CREATOR'S FRIEND
	    case($database->database_num_rows($database->database_query("SELECT friend_id FROM se_friends WHERE friend_user_id1='{$this->event_info['event_user_id']}' AND friend_user_id2='{$user->user_info['user_id']}'")) != 0):
	      return 4;
	      break;
        
	    // EVENT INVITEE'S FRIEND
	    case($database->database_num_rows($database->database_query("SELECT se_friends.friend_id FROM se_eventmembers LEFT JOIN se_friends ON se_eventmembers.eventmember_user_id=se_friends.friend_user_id1 AND se_eventmembers.eventmember_status<>'-1' WHERE se_eventmembers.eventmember_event_id='{$this->event_info['event_id']}' AND se_friends.friend_user_id2='{$user->user_info['user_id']}'")) != 0):
	      return 8;
	      break;
        
	    // FRIEND OF EVENT INVITEE'S FRIENDS
	    case($database->database_num_rows($database->database_query("SELECT t2.friend_user_id2 FROM se_eventmembers LEFT JOIN se_friends AS t1 ON se_eventmembers.eventmember_user_id=t1.friend_user_id1 AND se_eventmembers.eventmember_status<>'-1' LEFT JOIN se_friends AS t2 ON t1.friend_user_id2=t2.friend_user_id1 WHERE se_eventmembers.eventmember_event_id='{$this->event_info['event_id']}' AND t2.friend_user_id2='{$user->user_info['user_id']}'")) != 0):
	      return 16;
	      break;
        
	    // REGISTERED USER
	    case( $user->user_exists ):
	      return 32;
	      break;
        
	    // EVERYONE (DEFAULT)
	    default:	
	      return 64;
	      break;
	  }
	}
  
  // END event_privacy_max() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD RETURNS THE TOTAL NUMBER OF INVITED USERS IN AN EVENT
  //
	// INPUT:
  //    $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
	//	  $member_details (OPTIONAL) REPRESENTING WHETHER TO JOIN TO THE USER TABLE FOR SEARCH PURPOSES
  //
	// OUTPUT:
  //    AN INTEGER REPRESENTING THE NUMBER OF EVENT MEMBERS
  //
  
	function event_member_total($where=NULL, $member_details=FALSE)
  {
	  global $database;
    
	  // BEGIN QUERY
	  $sql = "
      SELECT
        NULL
      FROM
        se_eventmembers
    ";
    
	  // JOIN TO USER TABLE IF NECESSARY
	  if( $member_details ) $sql .= "
      LEFT JOIN
        se_users
        ON se_eventmembers.eventmember_user_id=se_users.user_id
    ";
    
	  // ADD WHERE IF NECESSARY
	  if( $this->event_exists || $where ) $sql .= "
      WHERE
    ";
    
	  // IF EVENT ID IS SET
	  if( $this->event_exists ) $sql .= "
        se_eventmembers.eventmember_event_id='{$this->event_info['event_id']}'
    ";
    
	  // ADD AND IF NECESSARY
	  if( $this->event_exists && $where ) $sql .= " AND"; 
    
	  // ADD REST OF WHERE CLAUSE
	  if( $where ) $sql .= "
        $where
    ";
    
	  // RUN QUERY
	  $resource = $database->database_query($sql);
	  return (int) $database->database_num_rows($resource);
	}
  
  // END event_member_total() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD RETURNS AN ARRAY OF EVENT INVITEES
  //
	// INPUT:
  //    $start REPRESENTING THE EVENT MEMBER TO START WITH
	//	  $limit REPRESENTING THE NUMBER OF EVENT MEMBERS TO RETURN
	//	  $sort_by (OPTIONAL) REPRESENTING THE ORDER BY CLAUSE
	//	  $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
  //
	// OUTPUT:
  //    AN ARRAY OF EVENT MEMBERS
  //
  
	function event_member_list($start, $limit, $sort_by="eventmember_id DESC", $where=NULL)
  {
	  global $database;
    
    if( !$start ) $start = '0';
    
	  // BEGIN QUERY
	  $sql = "
      SELECT
        se_eventmembers.*,
        se_users.user_id,
        se_users.user_username,
        se_users.user_photo,
        se_users.user_fname,
        se_users.user_lname,
        se_users.user_dateupdated,
        se_users.user_lastlogindate,
        se_users.user_signupdate
    ";
    
    // IF USER IS SET
    if( $this->user_id ) $sql .= ",
        (SELECT TRUE FROM se_friends WHERE friend_user_id1='{$this->user_id}' && friend_user_id2=se_users.user_id LIMIT 1) AS is_viewers_friend,
        (SELECT TRUE FROM se_users AS ivbl WHERE ivbl.user_id=se_users.user_id && ivbl.user_blocklist LIKE '%{$this->user_id}%' LIMIT 1) AS is_viewer_blocklisted
    ";
    
    $sql .= "
      FROM
        se_eventmembers
      LEFT JOIN
        se_users
        ON se_users.user_id=se_eventmembers.eventmember_user_id
    ";
    
	  // ADD WHERE IF NECESSARY
	  if( $this->event_exists || $where ) $sql .= "
      WHERE
    ";
    
	  // IF EVENT ID IS SET
	  if( $this->event_exists ) $sql .= "
        se_eventmembers.eventmember_event_id='{$this->event_info['event_id']}'
    ";
    
	  // ADD AND IF NECESSARY
	  if( $this->event_exists && $where ) $sql .= " AND"; 
    
	  // ADD REST OF WHERE CLAUSE
	  if( $where ) $sql .= "
        $where
    ";
    
	  // ADD ORDER, AND LIMIT CLAUSE
	  $sql .= "
      ORDER BY
        $sort_by
      LIMIT
        $start, $limit
    ";
    
	  // RUN QUERY
	  $resource = $database->database_query($sql);
    
	  // GET EVENT MEMBERS INTO AN ARRAY
	  $eventmember_array = array();
	  while( $eventmember_info=$database->database_fetch_assoc($resource) )
    {
	    // CREATE OBJECT FOR MEMBER
	    $member = new se_user();
	    $member->user_exists = TRUE;
	    $member->user_info['user_id']             = $eventmember_info['user_id'];
	    $member->user_info['user_username']       = $eventmember_info['user_username'];
	    $member->user_info['user_photo']          = $eventmember_info['user_photo'];
	    $member->user_info['user_fname']          = $eventmember_info['user_fname'];
	    $member->user_info['user_lname']          = $eventmember_info['user_lname'];
	    $member->user_info['user_dateupdated']    = $eventmember_info['user_dateupdated'];
	    $member->user_info['user_lastlogindate']  = $eventmember_info['user_lastlogindate'];
	    $member->user_info['user_signupdate']     = $eventmember_info['user_signupdate'];
	    $member->is_viewers_friend                = $eventmember_info['is_viewers_friend'];
	    $member->is_viewers_blocklist             = $eventmember_info['is_viewers_blocklist'];
	    $member->user_displayname();
      
      if( $eventmember_info['eventmember_status'] && !$eventmember_info['eventmember_approved'] )
        $eventmember_info['eventmember_rsvp_lvid'] = $this->event_rsvp_levels['-1'];
      elseif( !$eventmember_info['eventmember_status'] && $eventmember_info['eventmember_approved'] )
        $eventmember_info['eventmember_rsvp_lvid'] = $this->event_rsvp_levels['-2'];
      else
        $eventmember_info['eventmember_rsvp_lvid'] = $this->event_rsvp_levels[$eventmember_info['eventmember_rsvp']];
      
	    // SET EVENT ARRAY
	    $eventmember_array[] = array(
        'eventmember_id'        => $eventmember_info['eventmember_id'],
        'eventmember_status'    => $eventmember_info['eventmember_status'],
        'eventmember_approved'  => $eventmember_info['eventmember_approved'],
        'eventmember_rsvp'      => $eventmember_info['eventmember_rsvp'],
        'eventmember_rsvp_lvid' => $eventmember_info['eventmember_rsvp_lvid'],
        'eventmember_title'     => $eventmember_info['eventmember_title'],
        'eventmember_rank'      => $eventmember_info['eventmember_rank'],
        'member'                => &$member
      );
      
      unset($member);
	  }
    
	  return $eventmember_array;
	}
  
  // END event_member_list() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD RETURNS THE PATH TO THE GIVEN EVENT'S DIRECTORY
  //
	// INPUT:
  //    $event_id (OPTIONAL) REPRESENTING A EVENT'S EVENT_ID
  //
	// OUTPUT:
  //    A STRING REPRESENTING THE RELATIVE PATH TO THE EVENT'S DIRECTORY
  //
  
	function event_dir($event_id=NULL)
  {
	  if( !$event_id && $this->event_exists )
      $event_id = $this->event_info[event_id];
    elseif( !$event_id )
      return FALSE;
      
	  $subdir = $event_id + 999 - ( ( $event_id - 1 ) % 1000 );
	  $eventdir = "./uploads_event/{$subdir}/{$event_id}/";
	  return $eventdir;
	}
  
  // END event_dir() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD OUTPUTS THE PATH TO THE EVENT'S PHOTO OR THE GIVEN NOPHOTO IMAGE
  //
	// INPUT:
  //    $nophoto_image (OPTIONAL) REPRESENTING THE PATH TO AN IMAGE TO OUTPUT IF NO PHOTO EXISTS
  //
	// OUTPUT:
  //    A STRING CONTAINING THE PATH TO THE EVENT'S PHOTO
  //
  
	function event_photo($nophoto_image=NULL, $thumb=FALSE)
  {
    if( empty($this->event_info['event_photo']) )
      return $nophoto_image;
    
	  $event_dir = $this->event_dir($this->event_info['event_id']);
	  $event_photo = $event_dir.$this->event_info['event_photo'];
    if( $thumb )
    {
      $event_thumb = substr($event_photo, 0, strrpos($event_photo, "."))."_thumb".substr($event_photo, strrpos($event_photo, "."));
      if( file_exists($event_thumb) )
        return $event_thumb;
    }
    
    if( file_exists($event_photo) )
      return $event_photo;
    
    return $nophoto_image;
	}
  
  // END event_photo() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD UPLOADS AN EVENT PHOTO ACCORDING TO SPECIFICATIONS AND RETURNS EVENT PHOTO
  //
	// INPUT:
  //    $photo_name REPRESENTING THE NAME OF THE FILE INPUT
  //
	// OUTPUT: 
  //    void
  //
  
	function event_photo_upload($photo_name)
  {
	  global $database, $url;

	  // SET KEY VARIABLES
	  $file_maxsize = "4194304";
	  $file_exts = explode(",", str_replace(" ", "", strtolower($this->eventowner_level_info['level_event_photo_exts'])));
	  $file_types = explode(",", str_replace(" ", "", strtolower("image/jpeg, image/jpg, image/jpe, image/pjpeg, image/pjpg, image/x-jpeg, x-jpg, image/gif, image/x-gif, image/png, image/x-png")));
	  $file_maxwidth = $this->eventowner_level_info['level_event_photo_width'];
	  $file_maxheight = $this->eventowner_level_info['level_event_photo_height'];
	  $photo_newname = "0_".rand(1000, 9999).".jpg";
	  $file_dest = $this->event_dir($this->event_info['event_id']).$photo_newname;
	  $thumb_dest = substr($file_dest, 0, strrpos($file_dest, "."))."_thumb".substr($file_dest, strrpos($file_dest, "."));
    
	  $new_photo = new se_upload();
	  $new_photo->new_upload($photo_name, $file_maxsize, $file_exts, $file_types, $file_maxwidth, $file_maxheight);
    
	  // UPLOAD AND RESIZE PHOTO IF NO ERROR
	  if( !$new_photo->is_error )
    {
	    // DELETE OLD AVATAR IF EXISTS
	    $this->event_photo_delete();
      
	    // UPLOAD THUMB
	    $new_photo->upload_thumb($thumb_dest);
      
	    // CHECK IF IMAGE RESIZING IS AVAILABLE, OTHERWISE MOVE UPLOADED IMAGE
	    if( $new_photo->is_image )
	      $new_photo->upload_photo($file_dest);
	    else
	      $new_photo->upload_file($file_dest);
      
	    // UPDATE EVENT INFO WITH IMAGE IF STILL NO ERROR
	    if( !$new_photo->is_error )
      {
	      $sql = "UPDATE se_events SET event_photo='{$photo_newname}' WHERE event_id='{$this->event_info['event_id']}'";
        $resource = $database->database_query($sql);
	      $this->event_info['event_photo'] = $photo_newname;
	    }
	  }
    
	  $this->is_error = $new_photo->is_error;
	  $this->error_message = $new_photo->error_message;
	}
  
  // END event_photo_upload() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD DELETES A EVENT PHOTO
  //
	// INPUT: 
  //    void
  //
	// OUTPUT: 
  //    void
  //
  
	function event_photo_delete()
  {
	  global $database;
    
	  if( $event_photo=$this->event_photo() )
    {
      $sql = "UPDATE se_events SET event_photo='' WHERE event_id='{$this->event_info['event_id']}'";
      $resource = $database->database_query($sql);
	    $this->event_info['event_photo'] = NULL;
	    unlink($event_photo);
	  }
	}
  
  // END event_photo_delete() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD UPLOADS MEDIA TO A EVENT ALBUM
  //
	// INPUT:
  //    $file_name REPRESENTING THE NAME OF THE FILE INPUT
	//	  $eventalbum_id REPRESENTING THE ID OF THE EVENT ALBUM TO UPLOAD THE MEDIA TO
	//	  $space_left REPRESENTING THE AMOUNT OF SPACE LEFT
  //
	// OUTPUT:
  //    void
  //
  
	function event_media_upload($file_name, $eventalbum_id, &$space_left)
  {
	  global $class_event, $database, $url;
    
	  // SET KEY VARIABLES
	  $file_maxsize   = $this->eventowner_level_info['level_event_album_maxsize'];
	  $file_exts      = explode(",", str_replace(" ", "", strtolower($this->eventowner_level_info['level_event_album_exts'])));
	  $file_types     = explode(",", str_replace(" ", "", strtolower($this->eventowner_level_info['level_event_album_mimes'])));
	  $file_maxwidth  = $this->eventowner_level_info['level_event_album_width'];
	  $file_maxheight = $this->eventowner_level_info['level_event_album_height'];
    $time = time();
    
	  $new_media = new se_upload();
	  $new_media->new_upload($file_name, $file_maxsize, $file_exts, $file_types, $file_maxwidth, $file_maxheight);
    
	  // UPLOAD AND RESIZE PHOTO IF NO ERROR
	  if( !$new_media->is_error )
    {
	    // INSERT ROW INTO MEDIA TABLE
      $sql = "
        INSERT INTO se_eventmedia
          (eventmedia_eventalbum_id, eventmedia_date)
        VALUES
        ('{$eventalbum_id}', '{$time}')
      ";
      $resource = $database->database_query($sql);
	    $eventmedia_id = $database->database_insert_id();
      
	    // CHECK IF IMAGE RESIZING IS AVAILABLE, OTHERWISE MOVE UPLOADED IMAGE
      $event_dir = $this->event_dir($this->event_info['event_id']);
	    if( $new_media->is_image )
      {
	      $file_dest  = "{$event_dir}{$eventmedia_id}.jpg";
	      $thumb_dest = "{$event_dir}{$eventmedia_id}_thumb.jpg";
        
	      // UPLOAD THUMB
	      $new_media->upload_thumb($thumb_dest, 200);
        
	      // UPLOAD PHOTO
	      $new_media->upload_photo($file_dest);
	      $file_ext = "jpg";
	      $file_filesize = filesize($file_dest);
	    }
      
      else
      {
	      $file_dest  = "{$event_dir}{$eventmedia_id}.{$new_media->file_ext}";
	      $thumb_dest = "{$event_dir}{$eventmedia_id}_thumb.jpg";
        
	      if( $new_media->file_ext=='gif' )
	        $new_media->upload_thumb($thumb_dest, 200);
        
	      $new_media->upload_file($file_dest);
	      $file_ext = $new_media->file_ext;
	      $file_filesize = filesize($file_dest);
	    }
      
	    // CHECK SPACE LEFT
	    if( $space_left!==FALSE && $file_filesize>$space_left )
      {
	      $new_media->is_error = 1;
	      $new_media->error_message = $class_event[1].$_FILES[$file_name]['name']; // TODO LANG
	    }
      elseif( $space_left!==FALSE )
      {
	      $space_left = $space_left-$file_filesize;
	    }

	    // DELETE FROM DATABASE IF ERROR
	    if( $new_media->is_error )
      {
	      $sql = "DELETE FROM se_eventmedia WHERE eventmedia_id='{$eventmedia_id}' AND eventmedia_eventalbum_id='{$eventalbum_id}'";
	      $resource = $database->database_query($sql);
        @unlink($file_dest);
      }
      
	    // UPDATE ROW IF NO ERROR
	    else
      {
	      $sql = "UPDATE se_eventmedia SET eventmedia_ext='{$file_ext}', eventmedia_filesize='{$file_filesize}' WHERE eventmedia_id='{$eventmedia_id}' AND eventmedia_eventalbum_id='{$eventalbum_id}'";
        $resource = $database->database_query($sql);
        
        if( !is_numeric($file_filesize) ) $file_filesize = 0;
        $sql = "UPDATE se_eventalbums SET eventalbum_totalfiles=eventalbum_totalfiles+1, eventalbum_totalspace=eventalbum_totalspace+'{$file_filesize}' WHERE eventalbum_id='{$eventalbum_id}' LIMIT 1";
        $resource = $database->database_query($sql);
      }
	  }
    
	  // IF ERROR
	  if( $new_media->is_error )
    {
	    $new_media->error_message = $_FILES[$file_name]['name']." - ".SE_Language::get($new_media->is_error);
	  }
    
	  // RETURN FILE STATS
	  return array(
      'is_error'            => $new_media->is_error,
			'error_message'       => $new_media->error_message,
			'file_name'           => $_FILES[$file_name]['name'],
			'eventmedia_id'       => $eventmedia_id,
			'eventmedia_ext'      => $file_ext,
			'eventmedia_filesize' => $file_filesize
    );
	}
  
  // END event_media_upload() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD RETURNS THE SPACE USED
  //
	// INPUT:
  //    $eventalbum_id (OPTIONAL) REPRESENTING THE ID OF THE ALBUM TO CALCULATE
  //
	// OUTPUT:
  //    AN INTEGER REPRESENTING THE SPACE USED
  //
  
	function event_media_space($eventalbum_id=NULL)
  {
	  global $database;
    
	  // BEGIN QUERY
	  $sql = "
      SELECT
        SUM(se_eventmedia.eventmedia_filesize) AS total_space
      FROM
        se_eventalbums
      LEFT JOIN
        se_eventmedia
        ON se_eventalbums.eventalbum_id=se_eventmedia.eventmedia_eventalbum_id
    ";
    
	  // ADD WHERE IF NECESSARY
	  if( $this->event_exists || $eventalbum_id ) $sql .= "
      WHERE
    ";
    
	  // IF EVENT EXISTS, SPECIFY EVENT ID
	  if( $this->event_exists ) $sql .= "
        se_eventalbums.eventalbum_event_id='{$this->event_info['event_id']}'
    ";
    
	  // ADD AND IF NECESSARY
	  if( $this->event_exists && $eventalbum_id ) $sql .= " AND";
    
	  // SPECIFY ALBUM ID IF NECESSARY
	  if( $eventalbum_id ) $sql .= "
        se_eventalbums.eventalbum_id='{$eventalbum_id}'
    ";
    
	  // GET AND RETURN TOTAL SPACE USED
    $resource = $database->database_query($sql);
	  $space_info = $database->database_fetch_assoc($resource);
	  return $space_info['total_space'];
	}
  
  // END event_media_space() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD RETURNS THE NUMBER OF EVENT MEDIA
  //
	// INPUT:
  //    $eventalbum_id (OPTIONAL) REPRESENTING THE ID OF THE EVENT ALBUM TO CALCULATE
  //
	// OUTPUT:
  //    AN INTEGER REPRESENTING THE NUMBER OF FILES
  //
  
	function event_media_total($eventalbum_id=NULL)
  {
	  global $database;
    
    $sql = "SELECT eventalbum_totalfiles AS total_files FROM se_eventalbums ";
    
	  // ADD WHERE IF NECESSARY
	  if( $this->event_exists || $eventalbum_id ) $sql .= "
      WHERE
    ";
    
	  // IF EVENT EXISTS, SPECIFY EVENT ID
	  if( $this->event_exists ) $sql .= "
        se_eventalbums.eventalbum_event_id='{$this->event_info['event_id']}'
    ";
    
	  // ADD AND IF NECESSARY
	  if( $this->event_exists && $eventalbum_id ) $sql .= " AND";
    
	  // SPECIFY ALBUM ID IF NECESSARY
	  if( $eventalbum_id ) $sql .= "
        se_eventalbums.eventalbum_id='{$eventalbum_id}'
    ";
    
	  // GET AND RETURN TOTAL FILES
    $resource = $database->database_query($sql);
	  $file_info = $database->database_fetch_assoc($resource);
	  return $file_info['total_files'];
	}
  
  // END event_media_total() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD RETURNS AN ARRAY OF EVENT MEDIA
  //
	// INPUT:
  //    $start REPRESENTING THE EVENT MEDIA TO START WITH
	//	  $limit REPRESENTING THE NUMBER OF EVENT MEDIA TO RETURN
	//	  $sort_by (OPTIONAL) REPRESENTING THE ORDER BY CLAUSE
	//	  $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
  //
	// OUTPUT:
  //    AN ARRAY OF EVENT MEDIA
  //
  
	function event_media_list($start, $limit, $sort_by="eventmedia_id DESC", $where=NULL)
  {
	  global $database;
    
    // ???
    if( !$sort_by ) $sort_by = "eventmedia_id DESC";
    
	  // BEGIN QUERY
	  $sql = "
      SELECT
        se_eventmedia.*,
        se_eventalbums.eventalbum_id,
        se_eventalbums.eventalbum_event_id,
        se_eventalbums.eventalbum_title
      FROM
        se_eventmedia
      LEFT JOIN
        se_eventalbums
        ON se_eventalbums.eventalbum_id=se_eventmedia.eventmedia_eventalbum_id
    ";
    
	  // ADD WHERE IF NECESSARY
	  if( $this->event_exists || $where ) $sql .= "
      WHERE
    ";
    
	  // IF EVENT EXISTS, SPECIFY EVENT ID
	  if( $this->event_exists ) $sql .= "
        se_eventalbums.eventalbum_event_id='{$this->event_info['event_id']}'
    ";
    
	  // ADD AND IF NECESSARY
	  if( $this->event_exists && $where ) $sql .= " AND";
    
	  // ADD ADDITIONAL WHERE CLAUSE
	  if( $where ) $sql .= "
        $where
    ";
    
	  // ADD GROUP BY, ORDER, AND LIMIT CLAUSE
	  $sql .= "
      GROUP BY
        eventmedia_id
      ORDER BY
        $sort_by
      LIMIT
        $start, $limit
    ";
    
	  // RUN QUERY
    $resource = $database->database_query($sql);
    
	  // GET EVENT MEDIA INTO AN ARRAY
	  $eventmedia_array = array();
	  while( $eventmedia_info=$database->database_fetch_assoc($resource) )
    {
      $eventmedia_info['eventmedia_desc'] = str_replace("<br />", "\r\n", $eventmedia_info['eventmedia_desc']);
      $eventmedia_info['eventmedia_comments_total'] = $eventmedia_info['eventmedia_totalcomments'];
      
	    // CREATE ARRAY OF MEDIA DATA
	    $eventmedia_array[] = $eventmedia_info;
	  }
    
	  return $eventmedia_array;
	}
  
  // END event_media_list() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD UPDATES EVENT MEDIA INFORMATION
  //
	// INPUT:
  //    $start REPRESENTING THE EVENT MEDIA TO START WITH
	//	  $limit REPRESENTING THE NUMBER OF EVENT MEDIA TO RETURN
	//	  $sort_by (OPTIONAL) REPRESENTING THE ORDER BY CLAUSE
	//	  $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
  //
	// OUTPUT:
  //    AN ARRAY CONTAINING ALL THE EVENT MEDIA ID
  //
  
	function event_media_update($start, $limit, $sort_by="eventmedia_id DESC", $where=NULL)
  {
	  global $database;
    
	  // BEGIN QUERY
	  $sql = "
      SELECT
        se_eventmedia.*,
        se_eventalbums.eventalbum_id,
        se_eventalbums.eventalbum_event_id,
        se_eventalbums.eventalbum_title
      FROM
        se_eventmedia
    ";
    
    $sql .= "
      LEFT JOIN
        se_eventalbums
        ON se_eventalbums.eventalbum_id=se_eventmedia.eventmedia_eventalbum_id
    ";
    
	  // ADD WHERE IF NECESSARY
	  if( $this->event_exists || $where ) $sql .= "
      WHERE
    ";
    
	  // IF EVENT EXISTS, SPECIFY EVENT ID
	  if( $this->event_exists ) $sql .= "
        se_eventalbums.eventalbum_event_id='{$this->event_info['event_id']}'
    ";
    
	  // ADD AND IF NECESSARY
	  if( $this->event_exists && $where ) $sql .= " AND";
    
	  // ADD ADDITIONAL WHERE CLAUSE
	  if( $where ) $sql .= "
        $where
    ";
    
	  // ADD GROUP BY, ORDER, AND LIMIT CLAUSE
	  $sql .= "
      GROUP BY
        eventmedia_id
      ORDER BY
        $sort_by
      LIMIT
        $start, $limit
    ";
    
	  // RUN QUERY
    $resource = $database->database_query($sql);
    
	  // GET EVENT MEDIA INTO AN ARRAY
	  $eventmedia_array = array();
	  while( $eventmedia_info=$database->database_fetch_assoc($eventmedia) )
    {
	    $var1 = "eventmedia_title_{$eventmedia_info['eventmedia_id']}";
	    $var2 = "eventmedia_desc_{$eventmedia_info['eventmedia_id']}";
	    $eventmedia_title = censor($_POST[$var1]);
	    $eventmedia_desc = censor(str_replace("\r\n", "<br>", $_POST[$var2]));
	    if( $eventmedia_title!=$eventmedia_info[eventmedia_title] || $eventmedia_desc!=$eventmedia_info[eventmedia_desc] )
      {
        $sql = "UPDATE se_eventmedia SET eventmedia_title='{$eventmedia_title}', eventmedia_desc='{$eventmedia_desc}' WHERE eventmedia_id='{$eventmedia_info['eventmedia_id']}'";
	      $database->database_query($sql);
	    }
	    $eventmedia_array[] = $eventmedia_info['eventmedia_id'];
	  }
    
	  return $eventmedia_array;
	}
  
  // END event_media_update() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD DELETES SELECTED EVENT MEDIA
  //
	// INPUT:
  //    $start REPRESENTING THE EVENT MEDIA TO START WITH
	//	  $limit REPRESENTING THE NUMBER OF EVENT MEDIA TO RETURN
	//	  $sort_by (OPTIONAL) REPRESENTING THE ORDER BY CLAUSE
	//	  $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
  //
	// OUTPUT:
  //    void
  //
  
	function event_media_delete($start, $limit, $sort_by = "eventmedia_id DESC", $where = "")
  {
	  global $database, $url;

	  // BEGIN QUERY
	  $eventmedia_query = "
      SELECT
        se_eventmedia.*,
        se_eventalbums.eventalbum_id,
        se_eventalbums.eventalbum_event_id,
        se_eventalbums.eventalbum_title
    ";
	
	  // CONTINUE QUERY
	  $eventmedia_query .= "
      FROM
        se_eventmedia
      LEFT JOIN
        se_eventalbums
        ON se_eventalbums.eventalbum_id=se_eventmedia.eventmedia_eventalbum_id
    ";

	  // ADD WHERE IF NECESSARY
	  if($this->event_exists != 0 | $where != "") { $eventmedia_query .= " WHERE"; }

	  // IF EVENT EXISTS, SPECIFY EVENT ID
	  if($this->event_exists != 0) { $eventmedia_query .= " se_eventalbums.eventalbum_event_id='{$this->event_info['event_id']}'"; }

	  // ADD AND IF NECESSARY
	  if($this->event_exists != 0 & $where != "") { $eventmedia_query .= " AND"; }

	  // ADD ADDITIONAL WHERE CLAUSE
	  if($where != "") { $eventmedia_query .= " $where"; }

	  // ADD GROUP BY, ORDER, AND LIMIT CLAUSE
	  $eventmedia_query .= " GROUP BY eventmedia_id ORDER BY $sort_by LIMIT $start, $limit";

	  // RUN QUERY
	  $eventmedia = $database->database_query($eventmedia_query);

	  // LOOP OVER EVENT MEDIA
	  $eventmedia_delete = "";
	  while($eventmedia_info = $database->database_fetch_assoc($eventmedia))
    {
	    $var = "delete_eventmedia_".$eventmedia_info['eventmedia_id'];
	    if($_POST[$var] == 1)
      { 
	      if($eventmedia_delete != "") { $eventmedia_delete .= " OR "; }
	      $eventmedia_delete .= "eventmedia_id='{$eventmedia_info['eventmedia_id']}'";
	      $eventmedia_path = $this->event_dir($this->event_info['event_id']).$eventmedia_info['eventmedia_id'].".".$eventmedia_info['eventmedia_ext'];
	      if(file_exists($eventmedia_path)) { unlink($eventmedia_path); }
	      $thumb_path = $this->event_dir($this->event_info['event_id']).$eventmedia_info['eventmedia_id']."_thumb.".$eventmedia_info['eventmedia_ext'];
	      if(file_exists($thumb_path)) { unlink($thumb_path); }
        
        if( !is_numeric($eventmedia_info['eventmedia_filesize']) ) $eventmedia_info['eventmedia_filesize'] = 0;
        $sql = "UPDATE se_eventalbums SET eventalbum_totalfiles=eventalbum_totalfiles-1, eventalbum_totalspace=eventalbum_totalspace-'{$eventmedia_info['eventmedia_filesize']}' WHERE eventalbum_id='{$eventmedia_info['eventmedia_eventalbum_id']}' LIMIT 1";
        $resource = $database->database_query($sql);
	    }
	  }

	  // IF DELETE CLAUSE IS NOT EMPTY, DELETE EVENT MEDIA
	  if($eventmedia_delete != "") { $database->database_query("DELETE FROM se_eventmedia, se_eventmediacomments USING se_eventmedia LEFT JOIN se_eventmediacomments ON se_eventmedia.eventmedia_id=se_eventmediacomments.eventmediacomment_eventmedia_id WHERE ($eventmedia_delete)"); }

	}
  
  // END event_media_delete() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD 
  //
  
  function event_calendar_generate($date, $view="month")
  {
    global $database, $datetime, $global_timezone, $user;
    
    // Make sure date is numeric
    if( !is_numeric($date) ) $date = time();
    
    // Reset to beginning of period/Get next and last periods/Get number of periods
    if( $view=="day" )
    {
      $date       = mktime(0, 0, 0, date("m", $date), date("j", $date),     date("Y", $date));
      $date_next  = mktime(0, 0, 0, date("m", $date), date("j", $date) + 1, date("Y", $date));
      $date_last  = mktime(0, 0, 0, date("m", $date), date("j", $date) - 1, date("Y", $date));
      $date_selector = "G";
    }
    
    elseif( $view=="month" )
    {
      $date       = mktime(0, 0, 0, date("m", $date),     1, date("Y", $date));
      $date_next  = mktime(0, 0, 0, date("m", $date) + 1, 1, date("Y", $date));
      $date_last  = mktime(0, 0, 0, date("m", $date) - 1, 1, date("Y", $date));
      $date_selector = "j";
    }
    
    elseif( $view=="year" )
    {
      $date       = mktime(0, 0, 0, 1, 1, date("Y", $date));
      $date_next  = mktime(0, 0, 0, 1, 1, date("Y", $date) + 1);
      $date_last  = mktime(0, 0, 0, 1, 1, date("Y", $date) - 1);
      $date_selector  = "m";
      $date_selector2 = "j";
    }
    
    // SELECT EVENTS
    $sql = "
      SELECT
        event_id,
        event_title,
        event_date_start
      FROM
        se_events
      WHERE
        event_user_id='{$this->user_id}' &&
        event_date_start>='{$date_next}' &&
        event_date_start<='{$date_last}'
    ";
    
    $resource = $database->database_query($sql);
    
    $event_array = array();
    while( $event_info=$database->database_fetch_assoc($resource) )
    {
      $event_info['event_period_value'] = date("{$date_selector}", $event_info['event_date_start']);
      if( $date_selector2 )
        $event_info['event_period_value2'] = date("{$date_selector2}", $event_info['event_date_start']);
      
      $event_array[] = $event_info;
    }
    
    return array(
      'date'      => $date,
      'date_next' => $date_next,
      'date_last' => $date_last,
      'events'    => &$event_array
    );
  }
  
  // END event_calendar_generate() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD IS USED TO INVITE USERS TO AN EVENT
  //
  
  function event_member_invite($user_ids)
  {
    global $user, $database, $url, $notify;
    
    if( empty($user_ids) )
      return FALSE;
    
    if( !is_array($user_ids) )
      $user_ids = array($user_ids);
    
    if( count($user_ids)>10 )
      array_splice($user_ids, 10);
    
    // GET INVITED USER INFO AND CHECK IF ALREADY IS MEMBER OR BEEN INVITED
    $sql = "
      SELECT
        se_users.user_id,
        se_users.user_username,
        se_users.user_fname,
        se_users.user_lname,
        se_users.user_email,
        se_usersettings.usersetting_notify_eventinvite
      FROM
        se_users
      LEFT JOIN
        se_usersettings
        ON se_users.user_id=se_usersettings.usersetting_user_id
      LEFT JOIN
        se_eventmembers
        ON (se_users.user_id=se_eventmembers.eventmember_user_id && se_eventmembers.eventmember_event_id='{$this->event_info['event_id']}')
      WHERE
        se_users.user_id IN('".join("', '", $user_ids)."') &&
        se_eventmembers.eventmember_id IS NULL
    ";
    $resource = $database->database_query($sql);
    
    // USER DOES NOT EXIST OR HAS ALREADY BEEN INVITED
    if( !$database->database_num_rows($resource) )
      return FALSE;
    
    // INSERT INTO MEMBERS
    $invites_sent = 0;
    while( $user_info = $database->database_fetch_assoc($resource) )
    {
      // Create user object for displayname
      $recipient_object = new se_user();
      $recipient_object->user_info['user_id']       = $user_info['user_id'];
      $recipient_object->user_info['user_username'] = $user_info['user_username'];
      $recipient_object->user_info['user_fname']    = $user_info['user_fname'];
      $recipient_object->user_info['user_lname']    = $user_info['user_lname'];
      $recipient_object->user_displayname();
      
      // INSERT INTO EVENT MEMBERS
      $sql = "INSERT INTO se_eventmembers (eventmember_user_id, eventmember_event_id, eventmember_status, eventmember_approved) VALUES ('{$user_info['user_id']}','{$this->event_info['event_id']}','0', '1')";
      $database->database_query($sql);
      
      // NOTIFICATION
      $notifytype = $notify->notify_add(
        $recipient_object->user_info['user_id'],
        "eventinvite",
        $this->event_info['event_id'],
        Array(
          $user->user_info['user_username'],
          $this->event_info['event_id']
        ),
        Array(
          $this->event_info['event_title'],
        )
      );
      
      
      // EMAIL NOTIFICATION
      if( $user_info['usersetting_notify_eventinvite'] )
      {
        send_systememail(
          'eventinvite',
          $user_info['user_email'],
          array(
            $recipient_object->user_displayname,
            $this->event_info['event_title'],
            "<a href=\"{$url->url_base}login.php\">{$url->url_base}login.php</a>"
          )
        );
      }
      
      $invites_sent++;
      unset($recipient_object);
    }
    
    return $invites_sent;
  }
  
  // END event_member_invite() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD IS USED TO APPROVE AN INVITATION REQUEST
  //
  
  function event_member_approve($eventmember_user_id)
  {
    global $database, $actions, $user;
    
    // VERIFY ABILITY
    if( !$this->event_exists || $this->user_rank<2 )
    {
      $this->is_error = 3000248;
      return FALSE;
    }
    
    // GET MEMBER INFO
    $sql = "
      SELECT
        se_eventmembers.*,
        se_users.user_id,
        se_users.user_username,
        se_users.user_fname,
        se_users.user_lname
      FROM
        se_eventmembers
      LEFT JOIN
        se_users
        ON se_users.user_id=se_eventmembers.eventmember_user_id
      WHERE
        eventmember_approved=0 &&
        eventmember_user_id='{$eventmember_user_id}' &&
        eventmember_event_id='{$this->event_info['event_id']}'
      LIMIT
        1
    ";
    
    $resource = $database->database_query($sql);
    
    if( !$database->database_num_rows($resource) )
    {
      $this->is_error = 3000251;
      return FALSE;
    }
    
    $eventmember_info = $database->database_fetch_assoc($resource);
    
    // UPDATE MEMBER INFO
    $sql = "UPDATE se_eventmembers SET eventmember_status=1, eventmember_approved=1 WHERE eventmember_user_id='{$eventmember_user_id}' && eventmember_event_id='{$this->event_info['event_id']}' LIMIT 1";
    $resource = $database->database_query($sql);
    $result = (bool) $database->database_affected_rows($resource);
    
    // INCREMENT MEMBER COUNT
    $sql = "UPDATE se_events SET event_totalmembers=event_totalmembers+1 WHERE event_id='{$this->event_info['event_id']}' LIMIT 1";
    $database->database_query($sql);
    
    // REMOVE NOTIFICATIONS
    $sql = "DELETE FROM se_notifys USING se_notifys LEFT JOIN se_notifytypes ON se_notifys.notify_notifytype_id=se_notifytypes.notifytype_id WHERE se_notifys.notify_user_id='{$this->event_info['event_user_id']}' AND se_notifytypes.notifytype_name='eventmemberrequest' AND notify_object_id='{$this->event_info['event_id']}'";
    $resource = $database->database_query($sql);
    
    // INSERT ACTION
    $approved_user = new se_user();
    $approved_user->user_info['user_id'] = $eventmember_info['user_id'];
    $approved_user->user_info['user_username'] = $eventmember_info['user_username'];
    $approved_user->user_info['user_fname'] = $eventmember_info['user_fname'];
    $approved_user->user_info['user_lname'] = $eventmember_info['user_lname'];
    $approved_user->user_displayname();
    
    $event_title = $this->event_info['event_title'];
    if(strlen($event_title) > 100) { $event_title = substr($event_title, 0, 97)."..."; }
    $actions->actions_add(
      $approved_user,
      "joinevent",
      Array(
        $approved_user->user_info['user_username'],
        $approved_user->user_displayname,
        $this->event_info['event_id'],
        $event_title
      ),
      NULL,
      60,
      FALSE,
      "event",
      $this->event_info['event_id'],
      $this->event_info['event_privacy']
    );
    
    return $result;
  }
  
  // END event_member_approve() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD IS USED TO REJECT AN INVITATION REQUEST
  //
  
  function event_member_reject($eventmember_user_id)
  {
    global $database;
    
    // VERIFY ABILITY
    if( !$this->event_exists || $this->user_rank<2 )
    {
      $this->is_error = 3000248;
      return FALSE;
    }
    
    // GET MEMBER INFO
    $sql = "SELECT * FROM se_eventmembers WHERE eventmember_approved=0 && eventmember_user_id='{$eventmember_user_id}' && eventmember_event_id='{$this->event_info['event_id']}' LIMIT 1";
    $resource = $database->database_query($sql);
    
    if( !$database->database_num_rows($resource) )
    {
      $this->is_error = 3000251;
      return FALSE;
    }
    
    // UPDATE MEMBER INFO
    $sql = "DELETE FROM se_eventmembers WHERE eventmember_user_id='{$eventmember_user_id}' && eventmember_event_id='{$this->event_info['event_id']}' LIMIT 1";
    $resource = $database->database_query($sql);
    $result = (bool) $database->database_affected_rows($resource);
    
    // REMOVE NOTIFICATIONS
    $sql = "DELETE FROM se_notifys USING se_notifys LEFT JOIN se_notifytypes ON se_notifys.notify_notifytype_id=se_notifytypes.notifytype_id WHERE se_notifys.notify_user_id='{$this->event_info['event_user_id']}' AND se_notifytypes.notifytype_name='eventmemberrequest' AND notify_object_id='{$this->event_info['event_id']}'";
    $resource = $database->database_query($sql);
    
    return $result;
  }
  
  // END event_member_reject() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD IS USED TO CANCEL AN INVITATION REQUEST (EVENT OWNER->USER)
  //
  
  function event_member_cancel($eventmember_user_id)
  {
    global $database;
    
    // VERIFY ABILITY
    if( !$this->event_exists || $this->user_rank<2 )
    {
      $this->is_error = 3000248;
      return FALSE;
    }
    
    // GET MEMBER INFO
    $sql = "SELECT * FROM se_eventmembers WHERE eventmember_approved=1 && eventmember_status=0 && eventmember_user_id='{$eventmember_user_id}' && eventmember_event_id='{$this->event_info['event_id']}' LIMIT 1";
    $resource = $database->database_query($sql);
    
    if( !$database->database_num_rows($resource) )
    {
      $this->is_error = 3000251;
      return FALSE;
    }
    
    // UPDATE MEMBER INFO
    $sql = "DELETE FROM se_eventmembers WHERE eventmember_user_id='{$eventmember_user_id}' && eventmember_event_id='{$this->event_info['event_id']}' LIMIT 1";
    $resource = $database->database_query($sql);
    $result = (bool) $database->database_affected_rows($resource);
    
    
    // REMOVE NOTIFICATIONS
    $sql = "DELETE FROM se_notifys USING se_notifys LEFT JOIN se_notifytypes ON se_notifys.notify_notifytype_id=se_notifytypes.notifytype_id WHERE se_notifys.notify_user_id='{$this->event_info['event_user_id']}' AND se_notifytypes.notifytype_name='eventinvite' AND notify_object_id='{$member_info['eventmember_user_id']}'";
    $resource = $database->database_query($sql);
    
    return $result;
  }
  
  // END event_member_cancel() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD IS USED TO REMOVE AN EVENT MEMBER (EVENT OWNER->USER)
  //
  
  function event_member_remove($eventmember_user_id)
  {
    global $database;
    
    // VERIFY ABILITY
    if( !$this->event_exists || $this->user_rank<2 )
    {
      $this->is_error = 3000248;
      return FALSE;
    }
    
    // GET MEMBER INFO
    $sql = "SELECT * FROM se_eventmembers WHERE eventmember_approved=1 && eventmember_status=1 && eventmember_user_id='{$eventmember_user_id}' && eventmember_event_id='{$this->event_info['event_id']}' LIMIT 1";
    $resource = $database->database_query($sql);
    
    if( !$database->database_num_rows($resource) )
    {
      $this->is_error = 3000251;
      return FALSE;
    }
    
    // UPDATE MEMBER INFO
    $sql = "DELETE FROM se_eventmembers WHERE eventmember_user_id='{$eventmember_user_id}' && eventmember_event_id='{$this->event_info['event_id']}' LIMIT 1";
    $resource = $database->database_query($sql);
    $result = (bool) $database->database_affected_rows($resource);
    
    // DECREMENT MEMBER COUNT
    $sql = "UPDATE se_events SET event_totalmembers=event_totalmembers-1 WHERE event_id='{$this->event_info['event_id']}' LIMIT 1";
    $database->database_query($sql);
    
    return $result;
  }
  
  // END event_member_remove() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD 
  //
  
  function event_join()
  {
    global $user, $database, $url, $actions, $notify;
    
    // JOIN
    if( !$this->is_member && !$this->is_member_waiting )
    {
      $new_member_approved = ( $this->event_info['event_inviteonly'] ? '0' : '1' );
      
      // INSERT
      $sql = "INSERT INTO se_eventmembers (eventmember_user_id, eventmember_event_id, eventmember_status, eventmember_approved, eventmember_rank) VALUES ('{$this->user_id}', '{$this->event_info['event_id']}', '1', '{$new_member_approved}', '1')";
      $database->database_query($sql);
      
      // UPDATE MEMBER INFO
      $this->is_member = (bool) $new_member_approved;
      $this->is_member_waiting = !$new_member_approved;
      $this->eventmember_info['eventmember_approved'] = (int) $new_member_approved;
      
      // NOTIFY EVENT OWNER IF REQUESTING APPROVAL
      if( !$new_member_approved )
      {
        $sql = "SELECT se_users.user_id, se_users.user_username, se_users.user_email, se_users.user_fname, se_users.user_lname, se_usersettings.usersetting_notify_eventmemberrequest FROM se_users LEFT JOIN se_usersettings ON se_users.user_id=se_usersettings.usersetting_user_id WHERE se_users.user_id='{$this->event_info['event_user_id']}'";
        $resource = $database->database_query($sql);
        
        if( !$database->database_num_rows($resource) )
          return FALSE;
        
        $eventowner_info = $database->database_fetch_assoc($resource);
        
        // Create user object for displayname
        $recipient_object = new se_user();
        $recipient_object->user_info['user_id']       = $eventowner_info['user_id'];
        $recipient_object->user_info['user_username'] = $eventowner_info['user_username'];
        $recipient_object->user_info['user_fname']    = $eventowner_info['user_fname'];
        $recipient_object->user_info['user_lname']    = $eventowner_info['user_lname'];
        $recipient_object->user_displayname();
        
        // NOTIFICATION
        $notifytype = $notify->notify_add(
          $recipient_object->user_info['user_id'],
          "eventmemberrequest",
          $this->event_info['event_id'],
          Array(
            $user->user_info['user_username'],
            $this->event_info['event_id']
          ),
          Array(
            $this->event_info['event_title'],
          )
        );
        
        if( $eventowner_info['usersetting_notify_eventmemberrequest'] )
        {
          send_systememail(
            'eventmemberrequest',
            $eventowner_info['user_email'],
            array(
              $recipient_object->user_displayname,
              $user->user_displayname,
              $this->event_info['event_title'],
              "<a href=\"{$url->url_base}login.php\">{$url->url_base}login.php</a>"
            )
          );
        }
      }
      
      // OTHERWISE INCREMENT MEMBER COUNT
      else
      {
        $sql = "UPDATE se_events SET event_totalmembers=event_totalmembers+1 WHERE event_id='{$this->event_info['event_id']}' LIMIT 1";
        $database->database_query($sql);
      }
    }
    
    // ACCEPT INVITATION
    elseif( $this->is_member_waiting && $this->eventmember_info['eventmember_approved'] )
    {
      $sql = "UPDATE se_eventmembers SET eventmember_status=1 WHERE eventmember_event_id='{$this->event_info['event_id']}' && eventmember_user_id='{$this->user_id}' LIMIT 1";
      $database->database_query($sql);
      
      // INCREMENT MEMBER COUNT
      $sql = "UPDATE se_events SET event_totalmembers=event_totalmembers+1 WHERE event_id='{$this->event_info['event_id']}' LIMIT 1";
      $database->database_query($sql);
    
      // UPDATE MEMBER INFO
      $this->is_member = TRUE;
      $this->is_member_waiting = FALSE;
      $this->eventmember_info['eventmember_status'] = 1;
    }
    
    // SECURITY
    else
    {
      $this->is_error = 3000248;
      return FALSE;
    }
    
    
    // DELETE NOTIFICATION
    $sql = "DELETE FROM se_notifys USING se_notifys LEFT JOIN se_notifytypes ON se_notifys.notify_notifytype_id=se_notifytypes.notifytype_id WHERE se_notifys.notify_user_id='{$this->user_id}' AND se_notifytypes.notifytype_name='eventinvite' AND notify_object_id='{$this->event_info['event_id']}'";
    $database->database_query($sql);
    
    
    // INSERT ACTION IF NOT REQUESTING APPROVAL
    if( $new_member_approved || !empty($this->eventmember_info['eventmember_approved']) )
    {
      $event_title = $this->event_info['event_title'];
      if(strlen($event_title) > 100) { $event_title = substr($event_title, 0, 97)."..."; }
      $actions->actions_add(
        $user,
        "joinevent",
        Array(
          $user->user_info['user_username'],
          $user->user_displayname,
          $this->event_info['event_id'],
          $event_title
        ),
        NULL,
        60,
        FALSE,
        "event",
        $this->event_info['event_id'],
        $this->event_info['event_privacy']
      );
    }
    
    
    return TRUE;
  }
  
  // END event_join() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD 
  //
  
  function event_leave()
  {
    global $user, $database, $actions;
    
    // If the event is not invite only, or user is already a member
    if( !$this->is_member && !$this->is_member_waiting )
    {
      $this->is_error = 3000251;
      return FALSE;
    }
    
    // REMOVE MEMBER
    $sql = "DELETE FROM se_eventmembers WHERE eventmember_event_id='{$this->event_info['event_id']}' && eventmember_user_id='{$this->user_id}' LIMIT 1";
    $resource = $database->database_query($sql);
    $result = (bool) $database->database_affected_rows($resource);
    
    // IF USER IS OWNER, DELETE
    if( $this->user_id==$this->event_info['event_user_id'] )
    {
      $this->event_delete();
    }
    
    // INSERT ACTION IF NOT CANCELING REQUEST
    elseif( $this->is_member && $result )
    {
      $event_title = $this->event_info['event_title'];
      if(strlen($event_title) > 100) { $event_title = substr($event_title, 0, 97)."..."; }
      $actions->actions_add(
        $user,
        "leaveevent",
        Array(
          $user->user_info['user_username'],
          $user->user_displayname,
          $this->event_info['event_id'],
          $event_title
        ),
        NULL,
        60,
        FALSE,
        "event",
        $this->event_info['event_id'],
        $this->event_info['event_privacy']
      );
    }
    
    // DECREMENT USER COUNT
    if( $this->user_id!=$this->event_info['event_user_id'] )
    {
      $sql = "UPDATE se_events SET event_totalmembers=event_totalmembers-1 WHERE event_id='{$this->event_info['event_id']}' LIMIT 1";
      $database->database_query($sql);
    }
    
    // DELETE NOTIFICATION
    $sql = "DELETE FROM se_notifys USING se_notifys LEFT JOIN se_notifytypes ON se_notifys.notify_notifytype_id=se_notifytypes.notifytype_id WHERE se_notifys.notify_user_id='{$this->user_id}' AND se_notifytypes.notifytype_name='eventinvite' AND notify_object_id='{$this->event_info['event_id']}'";
    $database->database_query($sql);
    
    return $result;
  }
  
  // END event_leave() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD 
  //
  
  function event_rsvp($event_rsvp)
  {
    global $user, $database, $actions;
    
    // Validate RSVP value
    if( !is_numeric($event_rsvp) || $event_rsvp<-1 || $event_rsvp>4 )
    {
      $this->is_error = 3000248;
      return FALSE;
    }
    
    // Ensure user is allowed to do this
    if( !$this->is_member )
    {
      $this->is_error = 3000248;
      return FALSE;
    }
    
    // Ignore if user didn't change their response
    if( $this->eventmember_info['eventmember_rsvp']==$event_rsvp )
    {
      return TRUE;
    }
    
    // INSERT
    $sql = "UPDATE se_eventmembers SET eventmember_rsvp='{$event_rsvp}' WHERE eventmember_user_id='{$this->user_id}' && eventmember_event_id='{$this->event_info['event_id']}' && eventmember_status=1 && eventmember_approved=1";
    $resource = $database->database_query($sql);
    
    if( !$database->database_affected_rows($resource) )
    {
      $this->is_error = 3000251;
      return FALSE;
    }
    
    // UPDATE
    /*
    else
    {
      $sql = "INSERT INTO se_eventmembers (eventmember_user_id, eventmember_event_id, eventmember_status) VALUES ('{$this->user_id}', '{$this->event_info['event_id']}', '{$event_rsvp}')";
      $database->database_query($sql);
    }
    */
    
    // INSERT ACTION IF ATTENDING
    if( $event_rsvp==1 )
    {
      $event_title = $this->event_info['event_title'];
      if(strlen($event_title) > 100) { $event_title = substr($event_title, 0, 97)."..."; }
      $actions->actions_add(
        $user,
        "attendevent",
        Array(
          $user->user_info['user_username'],
          $user->user_displayname,
          $this->event_info['event_id'],
          $event_title
        ),
        NULL,
        60,
        FALSE,
        "event",
        $this->event_info['event_id'],
        $this->event_info['event_privacy']
      );
    }
    
    return TRUE;
  }
  
  // END event_rsvp() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD 
  //
  
  function event_rsvp_level($event_rsvp)
  {
    return $this->event_rsvp_levels[$event_rsvp];
  }
  
  // END event_rsvp() METHOD
  
  
  
  
  
  
  
  
  //
	// THIS METHOD 
  //
  
  function event_generate_javascript_structure()
  {
    return json_encode(array(
      'event_exists'          => (bool) $this->event_exists,
      'is_member'             => (bool) $this->is_member,
      'is_member_waiting'     => (bool) $this->is_member_waiting,
      'user_rank'             => (int)  $this->user_rank,
      
      'event_id'              => (int)  $this->event_info['event_id'],
      'event_user_id'         => (int)  $this->event_info['event_user_id'],
      'event_inviteonly'      => (bool) $this->event_info['event_inviteonly'],
      'event_invite'          => (int)  $this->event_info['event_invite'],
      
      'eventmember_approved'  => (bool) $this->eventmember_info['eventmember_approved'],
      'eventmember_status'    => (bool) $this->eventmember_info['eventmember_status'],
      'eventmember_rank'      => (int)  $this->eventmember_info['eventmember_rank'],
      'eventmember_rsvp'      => (int)  $this->eventmember_info['eventmember_rsvp']
    ));
  }
  
  // END event_rsvp() METHOD
  
  
}

?>