<?php


//  THIS CLASS CONTAINS POLL-RELATED METHODS 
//  METHODS IN THIS CLASS:
//
//    se_poll()
//
//    poll_create()
//    poll_edit()
//    poll_delete()
//    poll_vote()
//    poll_update()
//    poll_toggle_closed()
//
//    poll_total()
//    poll_list()


defined('SE_PAGE') or exit();


class se_poll
{
	// INITIALIZE VARIABLES
	var $is_error;				// DETERMINES WHETHER THERE IS AN ERROR OR NOT
	var $error_message;   // CONTAINS RELEVANT ERROR MESSAGE
	var $user_id;				  // CONTAINS THE USER ID OF THE USER WHOSE POLL WE ARE EDITING
  
	var $poll_exists;			// DETERMINES WHETHER THE POLL HAS BEEN SET AND EXISTS OR NOT
	var $poll_info;				// CONTAINS THE POLL INFO OF THE POLL WE ARE EDITING
	var $poll_owner;      // CONTAINS THE POLL INFO OF THE POLL WE ARE EDITING
  
  var $poll_stats;
  
  
  
  
  function se_poll($user_id=NULL, $poll_id=NULL)
  {
	  global $database, $user, $owner;
    
    if( empty($user_id) || !is_numeric($user_id) ) $user_id = NULL;
    if( empty($poll_id) || !is_numeric($poll_id) ) $poll_id = NULL;
    
	  $this->user_id = $user_id;
	  $this->poll_exists = FALSE;
    
	  if( $poll_id )
    {
      // GENERATE QUERY
      $sql = "
        SELECT
          *
        FROM
          se_polls
        WHERE
          poll_id='{$poll_id}'
      ";
      
      if( $user_id ) $sql .= " &&
        poll_user_id='{$user_id}'
      ";
      
      $sql .= "
        LIMIT
          1
      ";
      
      $resource = $database->database_query($sql) or die($database->database_error()."<br /><b>SQL:</b> $sql");
      
      if( $database->database_num_rows($resource) )
      {
        $this->poll_info = $database->database_fetch_assoc($resource);
	      $this->poll_exists = TRUE;
        
        // GENERATE STATS
        $this->poll_info['poll_options']  = unserialize($this->poll_info['poll_options']);
        $this->poll_info['poll_answers']  = unserialize($this->poll_info['poll_answers']);
        $this->poll_info['poll_voted']    = unserialize($this->poll_info['poll_voted']);
        
        // GET IF THIS POLL CAN BE VOTED ON FOR THE CURRENT USER
        if( !$user->user_exists || in_array($user->user_info['user_id'], $this->poll_info['poll_voted']) )
          $this->poll_info['poll_viewonly'] = TRUE;
        elseif( $this->poll_info['poll_closed'] )
          $this->poll_info['poll_viewonly'] = TRUE;
        else
          $this->poll_info['poll_viewonly'] = FALSE;
        
        // GET OWNER INFO
        if( $user->user_exists && $this->poll_info['poll_user_id']==$user->user_info['user_id'] )
          $this->poll_owner =& $user;
        elseif( $owner->user_exists && $this->poll_info['poll_user_id']==$owner->user_info['user_id'] )
          $this->poll_owner =& $owner;
        else
          $this->poll_owner = new se_user(array($this->poll_info['poll_user_id']));
      }
    }
  }
  
  
  
  
  
  function poll_create($poll_title, $poll_desc, $poll_options_raw, $poll_search, $poll_privacy, $poll_comments)
  {
	  global $database, $user;
    
    $poll_title = censor($poll_title);
    $poll_desc = censor($poll_desc);
    $poll_options_raw = array_map('censor', $poll_options_raw);
    
    // GET PRIVACY SETTINGS
    $level_poll_privacy = unserialize($user->level_info['level_poll_privacy']);
    rsort($level_poll_privacy);
    $level_poll_comments = unserialize($user->level_info['level_poll_comments']);
    rsort($level_poll_comments);
    
    // MAKE SURE SUBMITTED PRIVACY OPTIONS ARE ALLOWED, IF NOT, SET TO MOST PUBLIC
    if( !in_array($poll_privacy, $level_poll_privacy) )
      $poll_privacy = $level_poll_privacy[0];
    if( !in_array($poll_comments, $level_poll_comments))
      $poll_comments = $level_poll_comments[0];
    
    // CHECK THAT SEARCH IS NOT BLANK
    if( !$user->level_info['level_poll_search'] )
      $poll_search = 1;
    
    // GET START AND END DATES
    $poll_datecreated = time();
    $poll_options = array();
    $poll_answers = array();
    $poll_voted   = array();
    
    // Make options and answers
    $poll_option_index = 0;
    foreach( $poll_options_raw as $poll_option_label )
    {
      $poll_options[$poll_option_index] = $poll_option_label;
      $poll_answers[$poll_option_index] = 0;
      $poll_option_index++;
    }
    
    $poll_options = serialize($poll_options);
    $poll_answers = serialize($poll_answers);
    $poll_voted   = serialize($poll_voted);
    
    // GENERATE QUERY
    $sql = "
      INSERT INTO
        se_polls
      (
        poll_user_id,
        poll_datecreated,
        poll_title,
        poll_desc,
        poll_options,
        poll_answers,
        poll_voted,
        poll_search,
        poll_privacy,
        poll_comments
      )
      VALUES
      (
        '{$user->user_info['user_id']}',
        '{$poll_datecreated}',
        '".$database->database_real_escape_string($poll_title)."',
        '".$database->database_real_escape_string($poll_desc)."',
        '".$database->database_real_escape_string($poll_options)."',
        '{$poll_answers}',
        '{$poll_voted}',
        '{$poll_search}',
        '{$poll_privacy}',
        '{$poll_comments}'
      )
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
    
    return (bool) $database->database_affected_rows($resource);
	}
  
  
  
  
  
  function poll_edit($poll_title, $poll_desc, $poll_search, $poll_privacy, $poll_comments)
  {
	  global $user, $database;
    
    $poll_title = censor($poll_title);
    $poll_desc = censor($poll_desc);
    
    // GET PRIVACY SETTINGS
    $level_poll_privacy = unserialize($user->level_info['level_poll_privacy']);
    rsort($level_poll_privacy);
    $level_poll_comments = unserialize($user->level_info['level_poll_comments']);
    rsort($level_poll_comments);
    
    // MAKE SURE SUBMITTED PRIVACY OPTIONS ARE ALLOWED, IF NOT, SET TO MOST PUBLIC
    if( !in_array($poll_privacy, $level_poll_privacy) )
      $poll_privacy = $level_poll_privacy[0];
    if( !in_array($poll_comments, $level_poll_comments))
      $poll_comments = $level_poll_comments[0];
    
    // CHECK THAT SEARCH IS NOT BLANK
    if( !$user->level_info['level_poll_search'] )
      $poll_search = 1;
    
    $sql = "
      UPDATE
        se_polls
      SET
        poll_title='".$database->database_real_escape_string($poll_title)."',
        poll_desc='".$database->database_real_escape_string($poll_desc)."',
        poll_search='{$poll_search}',
        poll_privacy='{$poll_privacy}',
        poll_comments='{$poll_comments}'
      WHERE
        poll_id='{$this->poll_info['poll_id']}' &&
        poll_user_id='{$this->user_id}'
      LIMIT
        1
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
    
    return (bool) $database->database_affected_rows($resource);
  }
  
  
  
  
  
  function poll_delete($poll_id)
  {
	  global $database;
    
	  // CREATE DELETE QUERY
	  $sql = "DELETE FROM se_polls WHERE";
    
    // SINGLE
    if( is_numeric($poll_id) )
      $sql .= " se_polls.poll_id='{$poll_id}'";
    elseif( is_array($poll_id) )
      $sql .= " se_polls.poll_id IN('".join("','", $poll_id)."')";
    else
      return FALSE;
    
	  // IF USER ID IS NOT EMPTY, ADD USER ID CLAUSE
	  if( $this->user_id ) 
	    $sql .= " && se_polls.poll_user_id='{$this->user_id}'"; 
    
	  // RUN QUERIES
    $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
    
    return (bool) $database->database_affected_rows($resource);
  }
  
  
  
  
  
  function poll_vote($vote_id)
  {
    global $database, $user, $actions, $owner;
    
    $this->is_error = NULL;
    
    // VALIDATION
    if( !$user->user_exists )
      $this->is_error = 2500091;
    elseif( !$this->poll_exists )
      $this->is_error = 2500090;
    elseif( !$this->poll_info['poll_id'] )
      $this->is_error = 2500090;
    elseif( $this->poll_info['poll_closed'] )
      $this->is_error = 2500094;
    elseif( !isset($vote_id) || !isset($this->poll_info['poll_answers'][$vote_id]) )
      $this->is_error = 2500092;
    elseif( in_array($user->user_info['user_id'], $this->poll_info['poll_voted']) )
      $this->is_error = 2500093;
    
    if( $this->is_error ) return FALSE;
    
    // UPDATE
    $this->poll_info['poll_answers'][$vote_id]++;
    $this->poll_info['poll_totalvotes']++;
    $this->poll_info['poll_voted'][] = $user->user_info['user_id'];
    
    $answer_string  = serialize($this->poll_info['poll_answers']);
    $voted_string   = serialize($this->poll_info['poll_voted']);
    
    // GENERATE QUERY
    $sql = "
      UPDATE
        se_polls
      SET
        poll_answers='{$answer_string}',
        poll_voted='{$voted_string}',
        poll_totalvotes=poll_totalvotes+1
      WHERE
        poll_id='{$this->poll_info['poll_id']}'
      LIMIT
        1
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
    if( !$database->database_affected_rows($resource) ) return FALSE;
    
    
    // INSERT ACTION
    $poll_title = $this->poll_info['poll_title'];
    if( strlen($poll_title)>100 )
      $poll_title = substr($poll_title, 0, 97)."...";
    
    $actions->actions_add($user, "votepoll", array(
      $user->user_info['user_username'],
      $user->user_displayname,
      $this->poll_owner->user_info['user_username'],
      $this->poll_info['poll_id'],
      $poll_title
    ));
    
    return TRUE;
  }
  
  
  
  
  
  function poll_update()
  {
	  global $database;
    
    if( !$this->poll_exists || !$this->poll_info['poll_id'] )
      return FALSE;
    
	  // CREATE UPDATE QUERY
    $sql = "
      UPDATE
        se_polls
      SET
        poll_dateupdated='".time()."'
      WHERE
        poll_id='{$this->poll_info['poll_id']}'
      LIMIT
        1
    ";
    
	  // RUN QUERIES
    $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
    
    return (bool) $database->database_affected_rows($resource);
  }
  
  
  
  
  
  function poll_toggle_closed($force_value=NULL)
  {
	  global $database, $user;
    
    if( !$this->poll_exists || !$this->poll_info['poll_id'] || $user->user_info['user_id']!=$this->poll_info['poll_user_id'] )
      return FALSE;
    
    $new_value = ( isset($force_value) ? $force_value : !($this->poll_info['poll_closed']) );
    
	  // CREATE UPDATE QUERY
    $sql = "
      UPDATE
        se_polls
      SET
        poll_closed='{$new_value}'
      WHERE
        poll_id='{$this->poll_info['poll_id']}'
      LIMIT
        1
    ";
    
	  // RUN QUERIES
    $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
    
    return (bool) $database->database_affected_rows($resource);
  }
  
  
  
  
  
  function poll_view()
  {
	  global $database;
    
    if( !$this->poll_exists || !$this->poll_info['poll_id'] )
      return FALSE;
    
	  // CREATE UPDATE QUERY
    $sql = "
      UPDATE
        se_polls
      SET
        poll_views=poll_views+1
      WHERE
        poll_id='{$this->poll_info['poll_id']}'
      LIMIT
        1
    ";
    
	  // RUN QUERIES
    $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
    
    return (bool) $database->database_affected_rows($resource);
  }
  
  
  
  
  
  
  
  
  
  function poll_total($where)
  {
	  global $database;
    
	  // BEGIN ENTRY QUERY
	  $sql = "
      SELECT
        NULL
      FROM
        se_polls
    ";
    
	  // IF NO USER ID SPECIFIED, JOIN TO USER TABLE
	  if( !$this->user_id ) $sql .= "
      LEFT JOIN
        se_users
        ON se_polls.poll_user_id=se_users.user_id
    ";
    
	  // ADD WHERE IF NECESSARY
	  if( !empty($where) || $this->user_id ) $sql .= "
      WHERE
    ";
    
	  // ENSURE USER ID IS NOT EMPTY
	  if( $this->user_id ) $sql .= "
        poll_user_id='{$this->user_id}'
    ";
    
	  // INSERT AND IF NECESSARY
	  if(!empty($where) && $this->user_id ) $sql .= " AND";
    
	  // ADD WHERE CLAUSE, IF NECESSARY
	  if( !empty($where) ) $sql .= "
        $where
    ";
    
	  // GET AND RETURN TOTAL poll ENTRIES
    $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
    
	  $poll_total = $database->database_num_rows($resource);
    
	  return $poll_total;
  }
  
  
  
  function poll_list($start, $limit, $sort_by = "poll_id DESC", $where = "", $poll_details = 0)
  {
	  global $database, $user;
    
	  // BEGIN QUERY
	  $sql = "
      SELECT
        se_polls.*,
        se_polls.poll_totalcomments AS total_comments
    ";
    
	  // SELECT RELEVANT poll DETAILS IF NECESSARY
	  if($poll_details == 1) $sql .= ",
        se_users.user_id,
        se_users.user_username,
        se_users.user_photo,
        se_users.user_fname,
        se_users.user_lname
    ";
    
	  // CONTINUE QUERY
	  $sql .= "
      FROM
        se_polls
    ";
    
	  // CONTINUE QUERY IF NECESSARY
	  if($poll_details == 1) $sql .= "
      LEFT JOIN
        se_users
        ON se_polls.poll_user_id=se_users.user_id
    ";
    
	  // ADD WHERE IF NECESSARY
	  if($where != "" | $this->user_id != 0) $sql .= "
      WHERE
    ";
    
	  // ENSURE USER ID IS NOT EMPTY
	  if($this->user_id != 0) $sql .= "
        poll_user_id='{$this->user_id}'
    ";
    
	  // INSERT AND IF NECESSARY
	  if($this->user_id != 0 & $where != "") $sql .= " AND";
    
	  // ADD WHERE CLAUSE, IF NECESSARY
	  if($where != "") $sql .= "
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
    $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
    
	  // GET pollS INTO AN ARRAY
	  $poll_array = array();
	  while( $poll_info=$database->database_fetch_assoc($resource) )
    {
	    // CREATE OBJECT FOR poll
	    $poll = new se_poll($poll_info['user_id']);
	    $poll->poll_exists = TRUE;
      
	    // CREATE OBJECT FOR poll CREATOR IF poll DETAILS
	    if( $poll_details )
      {
      	$creator = new se_user();
	      $creator->user_exists = TRUE;
	      $creator->user_info['user_id'] = $poll_info['user_id'];
	      $creator->user_info['user_username'] = $poll_info['user_username'];
	      $creator->user_info['user_photo'] = $poll_info['user_photo'];
	      $creator->user_info['user_fname'] = $poll_info['user_fname'];
	      $creator->user_info['user_lname'] = $poll_info['user_lname'];
	      $creator->user_displayname();
        $poll->poll_owner =& $creator;
        unset($creator);
	    }
      
	    // TURN OPTIONS AND ANSWERS INTO ARRAYS, GET TOTAL VOTES
	    $poll_info['poll_options']  = unserialize($poll_info['poll_options']);
	    $poll_info['poll_answers']  = unserialize($poll_info['poll_answers']);
	    $poll_info['poll_voted']    = unserialize($poll_info['poll_voted']);
      
      // GET IF THIS POLL HAS BEEN VOTED ON
      if( !$user->user_exists || in_array($user->user_info['user_id'], $poll_info['poll_voted']) )
        $poll_info['poll_viewonly'] = TRUE;
      else
        $poll_info['poll_viewonly'] = FALSE;
      
      // PRIVACY
			$poll_info['poll_privacy_lang'] = user_privacy_levels($poll_info['poll_privacy']);
      SE_Language::_preload($poll_info['poll_privacy_lang']);
      
	    $poll->poll_info = $poll_info;
      $poll_array[] = $poll;
	  }
    
	  // RETURN ARRAY
	  return $poll_array;
  }
  
}

?>