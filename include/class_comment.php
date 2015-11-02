<?php

//  THIS CLASS CONTAINS COMMENT-RELATED METHODS 
//  IT IS USED FOR ALL COMMENTING (INCLUDING PLUGINS)
//  METHODS IN THIS CLASS:
//    se_comment()
//    comment_total()
//    comment_list()
//    comment_post()
//    comment_edit()
//    comment_delete()
//    comment_delete_selected()





class se_comment
{
	// INITIALIZE VARIABLES
	var $is_error;			// DETERMINES WHETHER THERE IS AN ERROR OR NOT

	var $comment_type;		          // CONTAINS THE PREFIX CORRESPONDING TO THE COMMENT TYPE (EX: PROFILE FOR SE_PROFILECOMMENTS)
	var $comment_identifier;	      // CONTAINS THE IDENTIFYING COLUMN IN THE TABLE (EX: USER_ID FOR SE_PROFILECOMMENTS)
	var $comment_identifying_value;	// CONTAINS THE VALUE TO MATCH TO THE IDENTIFIER
	var $comment_parent_type;	      // CONTAINS THE PREFIX CORRESPONDING TO THE COMMENT'S PARENT TYPE (EX: USERS FOR SE_USERS, MUSIC FOR SE_MUSIC)
	var $comment_parent_identifier;	// CONTAINS THE IDENTIFYING COLUMN IN THE COMMENT'S PARENT'S TABLE (EX: USER FOR SE_USERS, MUSIC FOR SE_MUSIC)








	// THIS METHOD SETS INITIAL VARS
	// INPUT: $type REPRESENTING THE PREFIX CORRESPONDING TO THE COMMENT TYPE
	//	  $identifier REPRESENTING THE IDENTIFYING COLUMN IN THE TABLE
	// OUTPUT: 
  
	function se_comment($type, $identifier, $identifying_value, $parent_type=NULL, $parent_identifier=NULL)
  {
	  $this->comment_type = $type;
	  $this->comment_identifier = $identifier;
	  $this->comment_identifying_value = $identifying_value;
    
	  $this->comment_parent_type = $parent_type;
	  $this->comment_parent_identifier = $parent_identifier;
	}
  
  // END se_comment() METHOD








	// THIS METHOD RETURNS THE TOTAL NUMBER OF COMMENTS
	// INPUT:
	// OUTPUT: AN INTEGER REPRESENTING THE NUMBER OF COMMENTS
  
	function comment_total()
  {
	  global $database;
    
    // New handling. On failure, will use old handling
    if( $this->comment_parent_type && $this->comment_parent_identifier )
    {
      $comment_query = "SELECT `{$this->comment_parent_identifier}_totalcomments` AS total_comments FROM `se_{$this->comment_parent_type}` WHERE `{$this->comment_parent_identifier}_id`='{$this->comment_identifying_value}' LIMIT 1";
      $resource = $database->database_query($comment_query);
      
      if( $resource )
      {
        $result = $database->database_fetch_assoc($resource);
        return (int) $result['total_comments'];
      }
    }
    
    // Old handling
	  $comment_query = "SELECT `{$this->comment_type}comment_id` FROM `se_{$this->comment_type}comments` WHERE `{$this->comment_type}comment_{$this->comment_identifier}`='{$this->comment_identifying_value}'";
	  $resource = $database->database_query($comment_query);
    
    if( !$resource ) return FALSE;
    
	  return (int) $database->database_num_rows($resource);
	}
  
  // END comment_total() METHOD








	// THIS METHOD RETURNS AN ARRAY CONTAINING COMMENT INFO
	// INPUT: $start REPRESENTING THE COMMENT TO START WITH
	//	  $limit REPRESENTING THE NUMBER OF COMMENTS TO RETURN
	// OUTPUT: AN ARRAY OF COMMENTS
  
	function comment_list($start, $limit)
  {
	  global $database, $setting, $user;
    
	  $comment_array = Array();
	  $comment_query = "
      SELECT 
				`se_{$this->comment_type}comments`.*, 
				se_users.user_id, 
				se_users.user_username, 
				se_users.user_fname,
				se_users.user_lname,
				se_users.user_photo,
			CASE
			  WHEN ((se_users.user_privacy & @SE_PRIVACY_REGISTERED) AND '{$user->user_exists}'<>0)
			    THEN FALSE
			  WHEN ((se_users.user_privacy & @SE_PRIVACY_ANONYMOUS) AND '{$user->user_exists}'=0)
			    THEN FALSE
			  WHEN ((se_users.user_privacy & @SE_PRIVACY_SELF) AND se_users.user_id='{$user->user_info['user_id']}')
			    THEN FALSE
			  WHEN ((se_users.user_privacy & @SE_PRIVACY_FRIEND) AND (SELECT TRUE FROM se_friends WHERE friend_user_id1=se_users.user_id AND friend_user_id2='{$user->user_info['user_id']}' AND friend_status='1' LIMIT 1))
			    THEN FALSE
			  WHEN ((se_users.user_privacy & @SE_PRIVACY_SUBNET) AND se_users.user_subnet_id='{$user->user_info['user_subnet_id']}')
			    THEN FALSE
			  WHEN ((se_users.user_privacy & @SE_PRIVACY_FRIEND2) AND se_users.user_subnet_id='{$user->user_info['user_subnet_id']}' AND (SELECT TRUE FROM se_friends AS friends_primary LEFT JOIN se_friends AS friends_secondary ON friends_primary.friend_user_id2=friends_secondary.friend_user_id1 WHERE friends_primary.friend_user_id1=se_users.user_id AND friends_secondary.friend_user_id2='{$user->user_info['user_id']}' LIMIT 1))
			    THEN FALSE
			  ELSE TRUE
			END
			AS is_profile_private
			FROM 
				`se_{$this->comment_type}comments`
			LEFT JOIN 
				se_users 
			ON 
				`se_{$this->comment_type}comments`.`{$this->comment_type}comment_authoruser_id`=se_users.user_id 
			WHERE 
				`{$this->comment_type}comment_{$this->comment_identifier}`='{$this->comment_identifying_value}' 
			ORDER BY
        `{$this->comment_type}comment_id` DESC 
			LIMIT
        {$start}, {$limit}
    ";
    
	  $comments = $database->database_query($comment_query);
	  while($comment_info = $database->database_fetch_assoc($comments))
    {
	    // CREATE AN OBJECT FOR AUTHOR
	    $author = new se_user();
	    if( $comment_info['user_id'] != $comment_info[$this->comment_type.'comment_authoruser_id'] )
      {
	      $author->user_exists = FALSE;
	    }
      else
      {
	      $author->user_exists = TRUE;
	      $author->user_info['user_id'] = $comment_info['user_id'];
	      $author->user_info['user_username'] = $comment_info['user_username'];
	      $author->user_info['user_fname'] = $comment_info['user_fname'];
	      $author->user_info['user_lname'] = $comment_info['user_lname'];
	      $author->user_info['user_photo'] = $comment_info['user_photo'];
	      $author->user_displayname();
	    }
      
	    // SET COMMENT ARRAY
	    $comment_array[] = Array(
        'comment_id' => $comment_info[$this->comment_type.'comment_id'],
        'comment_authoruser_id' =>$comment_info[$this->comment_type.'comment_authoruser_id'],
        'comment_author' => $author,
        'comment_date' => $comment_info[$this->comment_type.'comment_date'],
        'comment_body' => $comment_info[$this->comment_type.'comment_body'],
        'comment_author_private' => $comment_info['is_profile_private']
      );
	  }
    
	  return $comment_array;
	}
  
  // END comment_list() METHOD








  // THIS METHOD POSTS A COMMENT
  // INPUT: $comment_body REPRESENTING THE COMMENT BODY BEING POSTED
  //	  $comment_secure REPRESENTING THE SECURITY CODE VALUE (IF APPLICABLE)
  //	  $object_title (OPTIONAL) REPRESENTING THE COMMENTED OBJECT'S TITLE
  //	  $object_owner (OPTIONAL) REPRESENTING THE OWNER OF THE OBJECT (ex 'user')
  //	  $object_owner_id (OPTIONAL) REPRESENTING THE OWNER OF THE OBJECT'S ID
  //	  $object_privacy (OPTIONAL) REPRESENTING THE PRIVACY OF THE OBJECT
  // OUTPUT: AN ARRAY CONTAINING ALL THE SAVED COMMENT DATA

  function comment_post($comment_body, $comment_secure, $object_title = "", $object_owner = "", $object_owner_id = 0, $object_privacy = "")
  {
    global $database, $user, $owner, $setting, $actions, $notify, $url;

    $comment_id = 0;
    $comment_date = time();

    // RETRIEVE AND CHECK SECURITY CODE IF NECESSARY
    if( $setting['setting_comment_code'] )
    {
      // NOW IN HEADER
      $code_found = false;
      if( @$_SESSION['code'] == $comment_secure )
        $code_found = true;

      if( !empty($_SESSION['codes']) && is_array($_SESSION['codes']) )
      {
        foreach( $_SESSION['codes'] as $index => $code_info )
        {
          if( $code_info['code'] == $comment_secure )
          {
            $code_found = true;
            unset($_SESSION['codes'][$index]);
          }
        }
      }

      if( !$code_found )
        $this->is_error = 1;

      
      //session_start();
      //$code = $_SESSION['code'];
      //if($code == "") { $code = randomcode(); }
      //if($comment_secure != $code) { $this->is_error = 1; }
    }

    // MAKE SURE COMMENT BODY IS NOT EMPTY - ADD BREAKS AND CENSOR
    $comment_body = cleanHTML(censor($comment_body), $setting['setting_comment_html'], Array("style"));
    $comment_body = preg_replace('/(\r\n?)/', "\n", $comment_body);
    $comment_body = str_replace("\n", "<br>", $comment_body);
    $comment_body = preg_replace('/(<br>){3,}/is', '<br><br>', $comment_body);
    $comment_body = str_replace("'", "\'", $comment_body);
    if( !trim($comment_body) )
    {
      $this->is_error = 1;
      $comment_body = "";
    }
    
    // ADD COMMENT IF NO ERROR
    if( !$this->is_error )
    {
      $resource = $database->database_query("
        INSERT INTO `se_{$this->comment_type}comments` (
          `{$this->comment_type}comment_{$this->comment_identifier}`,
          `{$this->comment_type}comment_authoruser_id`,
          `{$this->comment_type}comment_date`,
          `{$this->comment_type}comment_body`
        ) VALUES (
          '{$this->comment_identifying_value}',
          '{$user->user_info['user_id']}',
          '{$comment_date}',
          '{$comment_body}'
        )
      ");
      
      $comment_id = $database->database_insert_id();
      // New handling - total cached in parent table
      if( $resource && $this->comment_parent_type && $this->comment_parent_identifier )
      {
        $database->database_query("
          UPDATE
            `se_{$this->comment_parent_type}`
          SET
            `{$this->comment_parent_identifier}_totalcomments`=`{$this->comment_parent_identifier}_totalcomments`+1
          WHERE
            `{$this->comment_identifier}`='{$this->comment_identifying_value}'
          LIMIT
            1
        ");
      }
      
      // INSERT ACTION IF USER EXISTS
      if( $user->user_exists )
      {
	      $commenter = $user->user_displayname;
	      $comment_body_encoded = strip_tags($comment_body);
        
	      if( strlen($comment_body_encoded) > 250 ) $comment_body_encoded = substr($comment_body_encoded, 0, 247)."...";
	      
        $comment_body_encoded = str_replace(Array("<br>", "<br />"), " ", $comment_body_encoded);
        
	      $actions->actions_add($user, $this->comment_type."comment", Array(
          $user->user_info['user_username'],
          $user->user_displayname,
          $owner->user_info['user_username'],
          $owner->user_displayname,
          $comment_body_encoded,
          $this->comment_identifying_value,
          $object_title,
          $object_owner_id
        ), Array(), 0, false, $object_owner, $object_owner_id, $object_privacy);
	    }
      else
      {
	      SE_Language::_preload(835);
	      SE_Language::load();
	      $commenter = SE_Language::_get(835);
	    }

	    // SEND PROFILE COMMENT NOTIFICATION IF COMMENTER IS NOT OWNER
	    if( $owner->user_info['user_id'] != $user->user_info['user_id'] )
      { 
	      $notifytype = $notify->notify_add(
          $owner->user_info['user_id'],
          $this->comment_type."comment",
          $this->comment_identifying_value,
          Array(
            $owner->user_info['user_username'],
            $this->comment_identifying_value,
            $object_owner_id
          ),
          Array($object_title)
        );
	      $object_url = $url->url_base.vsprintf($notifytype['notifytype_url'], Array($owner->user_info['user_username'], $this->comment_identifying_value));
	      $owner->user_settings();
	      if( $owner->usersetting_info['usersetting_notify_'.$this->comment_type.'comment'] )
        {
          send_systememail($this->comment_type."comment", $owner->user_info['user_email'], Array($owner->user_displayname, $commenter, "<a href=\"$object_url\">$object_url</a>"));
        }
	    }
	  }
    
	  return Array(
      'comment_id' => $comment_id,
      'comment_body' => $comment_body,
      'comment_date' => $comment_date
    );
	}
  
  // END comment_post() METHOD








	// THIS METHOD EDITS A COMMENT
	// INPUT: $comment_id REPRESENTING THE ID FOR THE COMMENT BEING EDITED
	//	  $comment_body REPRESENTING THE COMMENT BODY BEING EDITED
	// OUTPUT:
  
	function comment_edit($comment_id, $comment_body)
  {
	  global $database, $user, $setting;
    
	  // MAKE SURE COMMENT BODY IS NOT EMPTY - ADD BREAKS AND CENSOR
	  $comment_body = str_replace("\r\n", "<br>", cleanHTML(censor($comment_body), $setting['setting_comment_html']));
	  $comment_body = preg_replace('/(<br>){3,}/is', '<br><br>', $comment_body);
	  $comment_body = str_replace("'", "\'", $comment_body);
    
	  // EDIT COMMENT IF NO ERROR
	  if( trim($comment_body) )
    {
	    $database->database_query("
        UPDATE
          `se_{$this->comment_type}comments`
        SET
          `{$this->comment_type}comment_body`='{$comment_body}'
        WHERE
          `{$this->comment_type}comment_{$this->comment_identifier}`='{$this->comment_identifying_value}' &&
          `{$this->comment_type}comment_id`='{$comment_id}' &&
          `{$this->comment_type}comment_authoruser_id`='{$user->user_info['user_id']}'
        LIMIT
          1
      ");
	  }
	}
  
  // END comment_edit() METHOD








	// THIS METHOD DELETES A SINGLE COMMENT
	// INPUT: $comment_id REPRESENTING THE ID OF THE COMMENT TO DELETE
	// OUTPUT: 
  
	function comment_delete($comment_id)
  {
	  global $database;
    
	  $resource = $database->database_query("
      DELETE FROM
        `se_{$this->comment_type}comments`
      WHERE
        `{$this->comment_type}comment_{$this->comment_identifier}`='{$this->comment_identifying_value}' &&
        `{$this->comment_type}comment_id`='{$comment_id}'
      LIMIT
        1
    ");
	  
    // New handling - total cached in parent table
    if( $this->comment_parent_type && $this->comment_parent_identifier && $resource && $database->database_affected_rows($resource) )
    {
      $database->database_query("
        UPDATE
          `se_{$this->comment_parent_type}`
        SET
          `{$this->comment_parent_identifier}_totalcomments`=`{$this->comment_parent_identifier}_totalcomments`-1
        WHERE
          `{$this->comment_identifier}`='{$this->comment_identifying_value}'
        LIMIT
          1
      ");
    }
	}
  
  // END comment_delete() METHOD








	// THIS METHOD DELETES MANY COMMENTS BASED ON WHAT HAS BEEN POSTED
	// INPUT: $start REPRESENTING THE COMMENT TO START WITH
	//	  $limit REPRESENTING THE NUMBER OF COMMENTS TO RETURN
	// OUTPUT: 
  
	function comment_delete_selected($start, $limit)
  {
	  global $database;
    
	  $comments = $database->database_query("
      SELECT
        `se_{$this->comment_type}comments`.`{$this->comment_type}comment_id`
      FROM
        `se_{$this->comment_type}comments`
      WHERE
        `{$this->comment_type}comment_{$this->comment_identifier}`='{$this->comment_identifying_value}'
      ORDER BY
        `{$this->comment_type}comment_id` DESC
      LIMIT
        {$start}, {$limit}
    ");
    
    $delete_ids = array();
	  while( $comment_info = $database->database_fetch_assoc($comments) )
    {
	    $var = "comment_".$comment_info[$this->comment_type.'comment_id'];
      
	    if( isset($_POST[$var]) && is_numeric($_POST[$var]) )
      {
        $delete_ids[] = $comment_info[$this->comment_type.'comment_id'];
	    }
	  }
    
    if( !empty($delete_ids) )
    {
      $database->database_query("
        DELETE FROM
          `se_{$this->comment_type}comments`
        WHERE
          `{$this->comment_type}comment_id` IN('".join("', '", $delete_ids)."')
      ");
    }
	}
  
  // END comment_delete_selected() METHOD
}

?>