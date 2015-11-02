<?php


//
//  THIS CLASS CONTAINS BLOG ENTRY-RELATED METHODS 
//
//  METHODS IN THIS CLASS:
//
//    se_blog()
//
//    blog_entry_info()
//    blog_entry_post()
//    blog_entry_delete()
//
//    blog_entries_total()
//    blog_entries_list()
//    blog_entries_delete()
//
//    blog_archive_generate()
//    blog_categories_generate()
//    
//    blog_subscription_create()
//    blog_subscription_exists()
//    blog_subscription_delete()
//    blog_subscription_list()
//    blog_subscription_total()
//    blog_subscription_entry_list()
//    blog_subscription_notification()
//
//    blog_trackback_receive()
//    blog_trackback_send()
//    blog_trackback_total()
//    blog_trackback_list()
//    
//    blog_category_create()
//    blog_category_list()
//


defined('SE_PAGE') or exit();




class se_blog
{
	// INITIALIZE VARIABLES
	var $is_error;			// DETERMINES WHETHER THERE IS AN ERROR OR NOT
	var $error_message;		// CONTAINS RELEVANT ERROR MESSAGE

	var $user_id;			// CONTAINS THE USER ID OF THE USER WHOSE BLOG WE ARE EDITING








	//
  // BEGIN METHOD se_blog()
  //
  // OVERVIEW:
  //    THIS METHOD SETS INITIAL VARS
  //
	// INPUT:
  //    $user_id  (OPTIONAL) REPRESENTING THE USER ID OF THE USER WHOSE BLOG WE ARE CONCERNED WITH
  //
	// OUTPUT: 
  //    void
  //
  
	function se_blog($user_id=NULL)
  {
	  $this->user_id = $user_id;
	}
  
  //
  // END METHOD se_blog()
  //







	//
  // BEGIN METHOD blog_entry_info()
  //
  // OVERVIEW:
  //    Gets info about a single blog entry
  //
	// INPUT:
  //    $blogentry_id   REPRESENTING THE ID OF THE ENTRY
  //
	// OUTPUT:
  //    AN ARRAY OF INFO
  //
  
	function blog_entry_info($blogentry_id)
  {
    global $database, $user;
    
    if( !is_numeric($blogentry_id) )
      return FALSE;
    
    $sql = "
      SELECT
        se_blogentries.*
    ";
    
    if( !$this->user_id ) $sql .= ",
        se_users.user_id,
        se_users.user_username,
        se_users.user_photo,
        se_users.user_fname,
        se_users.user_lname
    ";
    
    // IF A USER IS LOGGED IN, SEE IF THEY ARE SUBSCRIBED
    if( $user->user_exists ) $sql .= ",
      IF(se_blogsubscriptions.blogsubscription_id IS NOT NULL, 1, 0) AS is_subscribed
    ";
    
    $sql .= "
      FROM
        se_blogentries
    ";
    
    // IF A USER IS LOGGED IN, SEE IF THEY ARE SUBSCRIBED
    if( $user->user_exists ) $sql .= "
      LEFT JOIN
        se_blogsubscriptions
        ON (se_blogsubscriptions.blogsubscription_user_id='{$user->user_info['user_id']}' && se_blogsubscriptions.blogsubscription_owner_id=se_blogentries.blogentry_user_id)
    ";
    
    if( !$this->user_id ) $sql .= "
      LEFT JOIN
        se_users
        ON se_users.user_id=se_blogentries.blogentry_user_id
    ";
    
    $sql .= "
      WHERE
        blogentry_id='{$blogentry_id}'
    ";
    
    if( $this->user_id ) $sql .= " &&
        blogentry_user_id='{$this->user_id}'
    ";
    
    $sql .= "
      LIMIT
        1
    ";
    
    $resource = $database->database_query($sql);
    
    if( !$database->database_num_rows($resource) )
      return FALSE;
    
    // PREPARE THE DATA
    $blogentry_info = $database->database_fetch_assoc($resource);
    //$blogentry_info['blog_trackbacks'] = split("\n", $blogentry_info['blog_trackbacks']);
    
    return $blogentry_info;
	}
  
  //
  // END METHOD blog_entry_info()
  //







	//
  // BEGIN METHOD blog_entries_total()
  //
  // OVERVIEW:
  //    THIS METHOD RETURNS THE TOTAL NUMBER OF ENTRIES
  //
	// INPUT:
  //    $where  (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
  //
	// OUTPUT:
  //    AN INTEGER REPRESENTING THE NUMBER OF ENTRIES
  //
  
	function blog_entries_total($where = "")
  {
	  global $database;
    
	  // BEGIN ENTRY QUERY
	  $sql = "
      SELECT
        NULL
      FROM
        se_blogentries
    ";
    
	  // IF NO USER ID SPECIFIED, JOIN TO USER TABLE
	  if( !$this->user_id ) $sql .= "
      LEFT JOIN
        se_users
        ON se_blogentries.blogentry_user_id=se_users.user_id
    ";
    
	  // ADD WHERE IF NECESSARY
	  if( !empty($where) || $this->user_id ) $sql .= "
      WHERE
    ";
    
	  // ENSURE USER ID IS NOT EMPTY
	  if( $this->user_id ) $sql .= "
        blogentry_user_id='{$this->user_id}'
    ";
    
	  // INSERT AND IF NECESSARY
	  if( $this->user_id && !empty($where) ) $sql .= " AND";
    
	  // ADD WHERE CLAUSE, IF NECESSARY
	  if( !empty($where) ) $sql .= "
        $where
    ";
    
	  // GET AND RETURN TOTAL BLOG ENTRIES
    $resource = $database->database_query($sql);
	  return $database->database_num_rows($resource);
	}
  
  //
  // END METHOD blog_entries_total()
  //







  //
  // BEGIN METHOD blog_entries_list()
  //
  // OVERVIEW:
	//    THIS METHOD RETURNS AN ARRAY OF BLOG ENTRIES
  //
	// INPUT:
  //    $start    REPRESENTING THE ENTRY TO START WITH
	//	  $limit    REPRESENTING THE NUMBER OF ENTRIES TO RETURN
	//	  $sort_by  (OPTIONAL) REPRESENTING THE ORDER BY CLAUSE
	//	  $where    (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
  //
	// OUTPUT:
  //    AN ARRAY OF BLOG ENTRIES
  //
  
	function blog_entries_list($start, $limit, $sort_by = "blogentry_date DESC", $where=NULL)
  {
	  global $database, $user, $owner;
    
	  // BEGIN QUERY
	  $sql = "
      SELECT
        se_blogentries.*,
        se_blogentrycats.*
    ";
    
    // IF A USER IS LOGGED IN, SEE IF THEY ARE SUBSCRIBED
    if( $user->user_exists ) $sql .= ",
        (SELECT TRUE FROM se_blogsubscriptions WHERE se_blogsubscriptions.blogsubscription_user_id='{$user->user_info['user_id']}' && se_blogsubscriptions.blogsubscription_owner_id=se_blogentries.blogentry_user_id LIMIT 1) AS is_subscribed
    ";
    
	  // IF NO USER ID SPECIFIED, RETRIEVE USER INFORMATION
	  if( !$this->user_id ) $sql .= ",
        se_users.user_id,
        se_users.user_username,
        se_users.user_photo,
        se_users.user_fname,
        se_users.user_lname
    ";
    
	  // CONTINUE QUERY
	  $sql .= "
      FROM
        se_blogentries
      LEFT JOIN
        se_blogentrycats
        ON se_blogentries.blogentry_blogentrycat_id=se_blogentrycats.blogentrycat_id
    ";
    
	  // IF NO USER ID SPECIFIED, JOIN TO USER TABLE
	  if( !$this->user_id ) $sql .= "
      LEFT JOIN
        se_users
        ON se_blogentries.blogentry_user_id=se_users.user_id
    ";
    
	  // ADD WHERE IF NECESSARY
	  if( !empty($where) || $this->user_id ) $sql .= "
      WHERE
    ";
    
	  // ENSURE USER ID IS NOT EMPTY
	  if( $this->user_id ) $sql .= "
        blogentry_user_id='{$this->user_id}'
    ";
    
	  // INSERT AND IF NECESSARY
	  if( $this->user_id && !empty($where) ) $sql .= " AND";
    
	  // ADD WHERE CLAUSE, IF NECESSARY
	  if( !empty($where) ) $sql .= "
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
    
	  // GET BLOG ENTRIES INTO AN ARRAY
	  $blogentry_array = Array();
	  while( $blogentry_info=$database->database_fetch_assoc($resource) )
    {
      // Check title
      if( !trim($blogentry_info['blogentry_title']) )
      {
        SE_Language::_preload(1500015);
        SE_Language::load();
        $blogentry_info['blogentry_title'] = SE_Language::_get(1500015);
      }
      
      // Load category title
      if( !empty($blogentry_info['blogentrycat_languagevar_id']) )
      {
        SE_Language::_preload($blogentry_info['blogentrycat_languagevar_id']);
      }
      
	    // CONVERT HTML CHARACTERS BACK
	    $blogentry_info['blogentry_body'] = str_replace("\r\n", "", htmlspecialchars_decode($blogentry_info['blogentry_body'], ENT_QUOTES));
	    
	    // IF NO USER ID SPECIFIED, CREATE OBJECT FOR AUTHOR
	    if( !$this->user_id )
      {
	      $author = new se_user();
	      $author->user_exists = TRUE;
	      $author->user_info['user_id']       = $blogentry_info['user_id'];
	      $author->user_info['user_username'] = $blogentry_info['user_username'];
	      $author->user_info['user_photo']    = $blogentry_info['user_photo'];
	      $author->user_info['user_fname']    = $blogentry_info['user_fname'];
	      $author->user_info['user_lname']    = $blogentry_info['user_lname'];
	      $author->user_displayname();
        
        $blogentry_info['blogentry_author'] =& $author;
        unset($author);
	    }
      
	    // OTHERWISE, SET AUTHOR TO OWNER/LOGGED-IN USER
      elseif( $owner->user_exists && $owner->user_info['user_id']==$blogentry_info['blogentry_user_id'] )
      {
	      $blogentry_info['blogentry_author'] =& $owner;
	    }
      elseif( $user->user_exists  && $user->user_info['user_id']==$blogentry_info['blogentry_user_id'] )
      {
	      $blogentry_info['blogentry_author'] =& $user;
	    }
      
	    // GET ENTRY COMMENT PRIVACY
      // TODO: FIND A WAY TO MAKE THIS WORK WITH THE AUTHOR
	    $allowed_to_comment = TRUE;
	    if( $owner->user_exists )
      {
	      $comment_level = $owner->user_privacy_max($user, $owner->level_info['level_blog_comments']);
	    }
      $blogentry_info['allowed_to_comment'] = $allowed_to_comment;
      
      
      // GET CATEGORY TITLE
      //$blogentry_info['blogentry_blogentrycat_title'] = $blogentry_info['blogentrycat_title'];
      //unset($blogentry_info['blogentrycat_title']);
      
      
	    // SET BLOGENTRY ARRAY
	    $blogentry_array[] = $blogentry_info;
	  }
    
	  // RETURN ARRAY
	  return $blogentry_array;
	}
  
  //
  // END METHOD blog_entries_list()
  //








  //
  // BEGIN METHOD blog_entries_delete()
  //
  // OVERVIEW:
  //    THIS METHOD DELETES SELECTED BLOG ENTRIES
  //
	// INPUT:
  //    $start REPRESENTING THE ENTRY TO START WITH
	//	  $limit REPRESENTING THE NUMBER OF ENTRIES TO RETURN
	//	  $sort_by (OPTIONAL) REPRESENTING THE ORDER BY CLAUSE
	//	  $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
  //
	// OUTPUT:
  //    void
  //
  
	function blog_entries_delete($start, $limit, $sort_by = "blogentry_date DESC", $where = "") {
	  global $database;
    
	  // BEGIN QUERY
	  $blogentry_query = "SELECT blogentry_id FROM se_blogentries";
    
	  // ADD WHERE IF NECESSARY
	  if($where != "" | $this->user_id != 0) { $blogentry_query .= " WHERE"; }
    
	  // ENSURE USER ID IS NOT EMPTY
	  if($this->user_id != 0) { $blogentry_query .= " blogentry_user_id='{$this->user_id}'"; }
    
	  // INSERT AND IF NECESSARY
	  if($this->user_id != 0 & $where != "") { $blogentry_query .= " AND"; }
    
	  if( $where ) { $blogentry_query .= " $where"; }
    
	  // ADD ORDER, AND LIMIT CLAUSE
	  $blogentry_query .= " ORDER BY $sort_by LIMIT $start, $limit";
    
	  // RUN QUERY
	  $blogentries = $database->database_query($blogentry_query);
    
	  // GET BLOG ENTRIES INTO AN ARRAY
	  $blogentry_delete = "";
	  while($blogentry_info = $database->database_fetch_assoc($blogentries)) {
	    $var = "delete_blogentry_".$blogentry_info['blogentry_id'];
	    if($_POST[$var] == 1) { 
	      if($blogentry_delete != "") { $blogentry_delete .= " OR "; }
	      $blogentry_delete .= "blogentry_id='{$blogentry_info['blogentry_id']}'";
	    }
	  }
    
	  // IF DELETE CLAUSE IS NOT EMPTY, DELETE ENTRIES
	  if($blogentry_delete != "") { 
	    $delete_query = "DELETE FROM se_blogentries, se_blogcomments USING se_blogentries LEFT JOIN se_blogcomments ON se_blogentries.blogentry_id=se_blogcomments.blogcomment_blogentry_id WHERE ";
	    if($this->user_id != 0) { $delete_query .= "se_blogentries.blogentry_user_id='{$this->user_id}' AND "; }
	    $delete_query .= "($blogentry_delete)";
	    $database->database_query($delete_query); 
	  }
	}
  
  //
  // END METHOD blog_entries_delete()
  //








	// 
  // BEGIN METHOD blog_entry_post()
  //
  // OVERVIEW:
  //    This methods posts or edits an entry.
  //
	// INPUT:
  //    $blogentry_id               REPRESENTING THE ID OF THE BLOG ENTRY TO EDIT. IF NO ENTRY WITH THIS ID IS FOUND, A NEW ENTRY WILL BE ADDED
	//	  $blogentry_title            REPRESENTING THE TITLE OF THE BLOG ENTRY
	//	  $blogentry_body             REPRESENTING THE BODY OF THE BLOG ENTRY
	//	  $blogentry_blogentrycat_id  REPRESENTING THE ID OF THE SELECTED BLOG ENTRY CATEGORY
	//	  $blogentry_search           REPRESENTING WHETHER THE BLOG ENTRY SHOULD BE INCLUDED IN SEARCH RESULTS
	//	  $blogentry_privacy          REPRESENTING THE PRIVACY LEVEL OF THE ENTRY
	//	  $blogentry_comments         REPRESENTING WHO CAN COMMENT ON THE ENTRY
	//	  $blogentry_trackbacks       REPRESENTING THE URLS TO SEND TRACKBACK DATA TO
  //
	// OUTPUT:
  //    returns FALSE or the blog entry id
  //
  
	function blog_entry_post($blogentry_id=NULL, $blogentry_title, $blogentry_body, $blogentry_blogentrycat_id=NULL, $blogentry_search=NULL, $blogentry_privacy=NULL, $blogentry_comments=NULL, $blogentry_trackbacks=NULL)
  {
	  global $database, $user;
    
    $is_error = FALSE;
    
	  // GET SETTINGS
	  $level_blog_privacy   = unserialize($user->level_info['level_blog_privacy']);
	  $level_blog_comments  = unserialize($user->level_info['level_blog_comments']);
    
    // PREPARE VARS
	  $blogentry_user_id    = $this->user_id;
	  $blogentry_date       = time();
    $blogentry_title      = censor(trim($blogentry_title));
    
    // Input filter class seems to be doing the decoding, don't decode it twice (not good for posting a blog about HTML)
    //$blogentry_body         = censor(htmlspecialchars_decode($blogentry_body, ENT_QUOTES));
    
    $blogentry_body         = cleanHTML($blogentry_body, $user->level_info['level_blog_html']);
    $blogentry_body         = censor($blogentry_body);
    $blogentry_body         = htmlspecialchars($blogentry_body, ENT_QUOTES, 'UTF-8');
    
    // OLD HTML ALLOWED: strong,b,em,i,u,strike,sub,sup,p,div,pre,address,h1,h2,h3,h4,h5,h6,span,ol,li,ul,a,img,embed
    
	  if( !$blogentry_blogentrycat_id )
      $blogentry_blogentrycat_id = 0;
    
    if( is_string($blogentry_trackbacks) )
      $blogentry_trackbacks = preg_split('/[\s\r\n]/', $blogentry_trackbacks);
    
    if( !is_array($blogentry_trackbacks) || empty($blogentry_trackbacks) )
      $blogentry_trackbacks = array();
    
	  if( !in_array($blogentry_privacy, $level_blog_privacy) )
      $blogentry_privacy = $level_blog_privacy[0];
    
	  if( !in_array($blogentry_comments, $level_blog_comments) ) 
      $blogentry_comments = $level_blog_comments[0];
    
    
    // VALIDATE
    if( empty($blogentry_user_id) )
      $is_error = TRUE;
    
    if( empty($blogentry_title) )
      $is_error = TRUE;
    
    
    // VALIDATE ID
    if( !empty($blogentry_id) )
    {
      $sql = "SELECT NULL FROM se_blogentries WHERE blogentry_id='{$blogentry_id}' AND blogentry_user_id='{$this->user_id}'";
      $resource = $database->database_query($sql);
      
      if( !$database->database_num_rows($resource) )
        $is_error = TRUE;
    }
    
	  // UPDATE
    if( !$is_error && !empty($blogentry_id) )
    {
      $sql = "
        UPDATE
          se_blogentries
        SET
          blogentry_title='$blogentry_title',
          blogentry_body='$blogentry_body',
          blogentry_blogentrycat_id='$blogentry_blogentrycat_id',
          blogentry_search='$blogentry_search',
          blogentry_privacy='$blogentry_privacy',
          blogentry_comments='$blogentry_comments'
        WHERE
          blogentry_id='$blogentry_id'
      ";
      
      $resource = $database->database_query($sql);
	  }
    
    // INSERT
    elseif( !$is_error )
    {
      $sql = "
        INSERT INTO se_blogentries
        (
          blogentry_user_id,
          blogentry_blogentrycat_id,
          blogentry_date,
          blogentry_title,
          blogentry_body,
          blogentry_search,
          blogentry_privacy,
          blogentry_comments,
          blogentry_trackbacks
        )
        VALUES
        (
          '$blogentry_user_id',
          '$blogentry_blogentrycat_id',
          '$blogentry_date',
          '$blogentry_title',
          '$blogentry_body',
          '$blogentry_search',
          '$blogentry_privacy',
          '$blogentry_comments',
          '".join("\n", $blogentry_trackbacks)."'
        )
      ";
      
      $resource = $database->database_query($sql);
      
      if( $database->database_affected_rows($resource) )
        $blogentry_id = $database->database_insert_id();
      else
        $is_error = TRUE;
	  }
    
    
    // TRACKBACKS
    $trackback_results = array();
    if( !$is_error && is_array($blogentry_trackbacks) && !empty($blogentry_trackbacks) )
    {
      $this->blog_trackback_send($blogentry_trackbacks, $blogentry_id, $blogentry_title, $blogentry_body);
    }
    
    
    // SUBSCRIPTION NOTIFICATION
    if( !$is_error )
    {
      $this->blog_subscription_notification($blogentry_id, $blogentry_title, $blogentry_privacy);
    }
    
    
    return array(
      'result' => !$is_error,
      'error' => $is_error,
      'blogentry_id' => $blogentry_id,
      'trackback_results' => $trackback_results
    );
	}
  
  //
  // END METHOD blog_entry_post()
  //







  //
  // BEGIN METHOD blog_entry_delete()
  //
  // OVERVIEW:
	//    Deletes entries
  //
	// INPUT:
  //    $blogentry_id       REPRESENTING THE ID OF THE BLOG ENTRY TO DELETE, OR AN ARRAY OF IDS
  //
	// OUTPUT:
  //    Return whether or not the entrie(s) was(were) deleted
  //
  
	function blog_entry_delete($blogentry_id)
  {
	  global $database;
    
    if( !is_array($blogentry_id) )
      $blogentry_id = array($blogentry_id);
    
    $blogentry_id = array_unique(array_filter($blogentry_id));
    
	  // CREATE DELETE QUERY
	  $sql = "
      DELETE FROM
        se_blogentries,
        se_blogcomments
      USING
        se_blogentries
      LEFT JOIN
        se_blogcomments
        ON se_blogentries.blogentry_id=se_blogcomments.blogcomment_blogentry_id
      WHERE
        se_blogentries.blogentry_id IN('".join("','", $blogentry_id)."')
    ";
    
	  // IF USER ID IS NOT EMPTY, ADD USER ID CLAUSE
	  if( $this->user_id ) 
      $sql .= " AND se_blogentries.blogentry_user_id='{$this->user_id}'";
    
	  // RUN QUERY
    $resource = $database->database_query($sql);
    
    return (bool) ($database->database_affected_rows($resource)==count($blogentry_id) );
	}
  
  //
  // END METHOD blog_entry_delete()
  //
  
  
  
  
  
  
  
  //
  // BEGIN METHOD blog_archive_generate()
  //
  // OVERVIEW:
	//    Generates a list of dates using the current user's blog entries for an archive list.
  //
	// INPUT:
  //    $where
  //
	// OUTPUT:
  //    An array of data about each date, including a label, start and end periods
  //
  
  function blog_archive_generate($where=NULL)
  {
    global $database;
    
    $sql = "
      SELECT
        blogentry_date
      FROM
        se_blogentries
      WHERE
        blogentry_user_id='{$this->user_id}'
    ";
    
    if( $where ) $sql .= " &&
        $where
    ";
    
    $sql .= "
      ORDER BY
        blogentry_date DESC
    ";
    
    $resource = $database->database_query($sql);
    
    // GET DATES
    $blog_dates = array();
    while( $result=$database->database_fetch_assoc($resource) )
      $blog_dates[] = $result['blogentry_date'];
    
    // GEN ARCHIVE LIST
    $time = time();
    $archive_list = array();
    
    foreach( $blog_dates as $blog_date )
    {
      $ltime = localtime($blog_date, TRUE);
      $ltime["tm_mon"] = $ltime["tm_mon"] + 1;
      $ltime["tm_year"] = $ltime["tm_year"] + 1900;
      
      // LESS THAN A YEAR AGO - MONTHS
      if( $blog_date+31536000>$time )
      {
        $date_start = mktime(0, 0, 0, $ltime["tm_mon"], 1, $ltime["tm_year"]);
        $date_end = mktime(0, 0, 0, $ltime["tm_mon"]+1, 1, $ltime["tm_year"]);
        $label = date('F Y', $blog_date);
        $type = 'month';
      }
      
      // MORE THAN A YEAR AGO - YEARS
      else
      {
        $date_start = mktime(0, 0, 0, 1, 1, $ltime["tm_year"]);
        $date_end = mktime(0, 0, 0, 1, 1, $ltime["tm_year"]+1);
        $label = date('Y', $blog_date);
        $type = 'year';
      }
      
      if( !isset($archive_list[$date_start]) )
      {
        $archive_list[$date_start] = array(
          'type' => $type,
          'label' => $label,
          'date_start' => $date_start,
          'date_end' => $date_end,
          'count' => 1
        );
      }
      else
      {
        $archive_list[$date_start]['count']++;
      }
    }
    
    //krsort($archive_list);
    
    return $archive_list;
  }
  
  //
  // END METHOD blog_archive_generate()
  //
  
  
  
  
  
  
  
  //
  // BEGIN METHOD blog_categories_generate()
  //
  // OVERVIEW:
	//    Generates a list of categories using the current user's blog entries for a category list.
  //
	// INPUT:
  //    $where
  //
	// OUTPUT:
  //    An array of data about each category, including a title and ID
  //
  
  function blog_categories_generate($where=NULL)
  {
    global $database;
    
    $sql = "
      SELECT
        COUNT(*) AS blogentry_count,
        se_blogentries.blogentry_blogentrycat_id,
        se_blogentrycats.*
      FROM
        se_blogentries
      LEFT JOIN
        se_blogentrycats
        ON se_blogentrycats.blogentrycat_id=se_blogentries.blogentry_blogentrycat_id
      WHERE
        se_blogentries.blogentry_user_id='{$this->user_id}' &&
        (se_blogentries.blogentry_blogentrycat_id=0 || se_blogentrycats.blogentrycat_user_id=0 || se_blogentrycats.blogentrycat_user_id='{$this->user_id}')
    ";
    
    if( $where ) $sql .= " &&
        $where
    ";
    
    $sql .= "
      GROUP BY
        se_blogentries.blogentry_blogentrycat_id
      ORDER BY
        se_blogentries.blogentry_blogentrycat_id ASC
    ";
    
    $resource = $database->database_query($sql);
    
    $blog_cats = array();
    while( $result=$database->database_fetch_assoc($resource) )
    {
      if( empty($result['blogentrycat_id']) )
      {
        $result['blogentrycat_id'] = 0;
        $result['blogentrycat_title'] = SE_Language::get(1500035);
      }
      
      if( !empty($result['blogentrycat_languagevar_id']) )
        SE_Language::_preload($result['blogentrycat_languagevar_id']);
      
      $blog_cats[] = $result;
    }
    
    return $blog_cats;
  }
  
  //
  // END METHOD blog_categories_generate()
  //
  
  
  
  
  
  
  
  //
  // BEGIN METHOD blog_subscription_create()
  //
  // OVERVIEW:
	//    Subscribes the current user to the specified user's blog
  //
	// INPUT:
  //    $blog_owner_user_id   The user id of the owner of the blog to subscribe to
  //
	// OUTPUT:
  //    TRUE if the subscription was create, otherwise FALSE
  //
  
  function blog_subscription_create($blog_owner_user_id)
  {
    global $database, $user;
    
    // MUST BE LOGGED IN
    if( !$user->user_exists || !$user->user_info['user_id'] )
      return FALSE;
    
    // CHECK FOR EXISTING SUBSCRIPTION
    $sql = "
      SELECT
        NULL
      FROM
        se_blogsubscriptions
      WHERE
        blogsubscription_user_id='{$user->user_info['user_id']}' &&
        blogsubscription_owner_id='{$blog_owner_user_id}'
      LIMIT
        1
    ";
    
    $resource = $database->database_query($sql);
    
    // Already subscribed
    if( $database->database_num_rows($resource) )
      return FALSE;
    
    // Insert
    $time = time();
    
    $sql = "
      INSERT INTO se_blogsubscriptions (
        blogsubscription_user_id,
        blogsubscription_owner_id,
        blogsubscription_date
      ) VALUES (
        '{$user->user_info['user_id']}',
        '{$blog_owner_user_id}',
        '{$time}'
      )
    ";
    
    $resource = $database->database_query($sql);
    
    return ( $database->database_affected_rows($resource) ? $database->database_insert_id() : FALSE );
  }
  
  //
  // END METHOD blog_subscription_create()
  //
  
  
  
  
  
  
  
  //
  // BEGIN METHOD blog_subscription_exists()
  //
  // OVERVIEW:
	//    Does the specified subscription exist
  //
	// INPUT:
  //    $blog_owner_user_id         The user id of the owner
  //    $blog_subscriber_user_id    The user id of the subscriber
  //
	// OUTPUT:
  //    TRUE if the subscription exists, otherwise FALSE
  //
  
  function blog_subscription_exists($blog_owner_user_id, $blog_subscriber_user_id)
  {
    global $database, $user;
    
    // MUST BE LOGGED IN AND NOT SELF
    if( !$blog_subscriber_user_id || !$blog_owner_user_id || $blog_owner_user_id==$blog_subscriber_user_id )
      return FALSE;
    
    // CHECK FOR EXISTING SUBSCRIPTION
    $sql = "
      SELECT
        NULL
      FROM
        se_blogsubscriptions
      WHERE
        blogsubscription_user_id='{$blog_subscriber_user_id}' &&
        blogsubscription_owner_id='{$blog_owner_user_id}'
      LIMIT
        1
    ";
    
    $resource = $database->database_query($sql);
    
    return (bool) $database->database_num_rows($resource);
  }
  
  //
  // END METHOD blog_subscription_exists()
  //
  
  
  
  
  
  
  
  //
  // BEGIN METHOD blog_subscription_delete()
  //
  // OVERVIEW:
	//    Unsubscribes the current user from the specified user's blog
  //
	// INPUT:
  //    $blog_owner_user_id   The user id of the owner of the blog to unsubscribe from
  //
	// OUTPUT:
  //    TRUE if the subscription was deleted, otherwise FALSE
  //
  
  function blog_subscription_delete($blog_owner_user_id)
  {
    global $database, $user;
    
    // MUST BE LOGGED IN
    if( !$user->user_exists || !$user->user_info['user_id'] )
      return FALSE;
    
    // DELETE
    $sql = "
      DELETE FROM
        se_blogsubscriptions
      WHERE
        blogsubscription_user_id='{$user->user_info['user_id']}' &&
        blogsubscription_owner_id='{$blog_owner_user_id}'
      LIMIT
        1
    ";
    
    $resource = $database->database_query($sql);
    
    return (bool) $database->database_affected_rows($resource);
  }
  
  //
  // END METHOD blog_subscription_delete()
  //
  
  
  
  
  
  
  
  //
  // BEGIN METHOD blog_subscription_total()
  //
  // OVERVIEW:
	//    Count of blog subscriptions
  //
	// INPUT:
  //    $where      SQL WHERE
  //
	// OUTPUT:
  //    Count of blog subscriptions
  //
  
  function blog_subscription_total($where=NULL)
  {
    global $database, $user;
    
    // Generate query
    $sql = "
      SELECT
        COUNT(*) AS blogsubscription_count
      FROM
        se_blogsubscriptions
      WHERE
        se_blogsubscriptions.blogsubscription_user_id='{$user->user_info['user_id']}'
    ";
    
    if( !empty($where) )
      $sql .= " && $where";
    
    $resource = $database->database_query($sql);
    $result = $database->database_fetch_assoc($resource);
    
    return ( !empty($result['blogsubscription_count']) ? $result['blogsubscription_count'] : 0 );
  }
  
  //
  // END METHOD blog_subscription_total()
  //
  
  
  
  
  
  
  
  //
  // BEGIN METHOD blog_subscription_list()
  //
  // OVERVIEW:
	//    Lists blog subscriptions
  //
	// INPUT:
  //    $start                SQL START
  //    $limit                SQL LIMIT
  //    $order_by             SQL ORDER BY
  //    $where                SQL WHERE
  //    $getmostrecententry   SQL WHERE
  //
	// OUTPUT:
  //    Array of blog subscriptions
  //
  
  function blog_subscription_list($start=NULL, $limit=NULL, $order_by=NULL, $where=NULL, $getmostrecententry=FALSE)
  {
    global $database, $user;
    
    if( !$start           ) $start = '0';
    if( !isset($limit)    ) $limit = 20;
    if( empty($order_by)  ) $order_by = "se_blogsubscriptions.blogsubscription_date DESC";
    
    // Generate query
    $sql = "
      SELECT
        se_blogsubscriptions.*,
        se_users.user_id,
        se_users.user_username,
        se_users.user_photo,
        se_users.user_fname,
        se_users.user_lname
    ";
    
    if( $getmostrecententry ) $sql .= ",
        se_blogentries.*
    ";
    
    $sql .= "
      FROM
        se_blogsubscriptions
      LEFT JOIN
        se_users
        ON se_users.user_id=se_blogsubscriptions.blogsubscription_owner_id
    ";
    
    if( $getmostrecententry ) $sql .= "
      LEFT JOIN
        se_blogentries
        ON (
          se_blogentries.blogentry_user_id=se_blogsubscriptions.blogsubscription_owner_id &&
          se_blogentries.blogentry_id=(
            SELECT
              se_blogentries.blogentry_id
            FROM
              se_blogentries
            WHERE
              (se_blogentries.blogentry_search IS NULL || se_blogentries.blogentry_search=1) && 
              se_blogentries.blogentry_user_id=se_blogsubscriptions.blogsubscription_owner_id
            ORDER BY
              se_blogentries.blogentry_date DESC
            LIMIT
              1
          )
        )
    ";
    
    $sql .= "
      WHERE
        se_blogsubscriptions.blogsubscription_user_id='{$user->user_info['user_id']}'
    ";
    
    if( !empty($where) )
      $sql .= " && $where";
    
    if( $getmostrecententry ) $sql .= "
      GROUP BY
        se_blogsubscriptions.blogsubscription_owner_id
    ";
    
    $sql .= "
      ORDER BY
        $order_by
    ";
    
    $sql .= "
      LIMIT
        {$start}, {$limit}
    ";
    
    $resource = $database->database_query($sql);
    
    // Get results
    $blogsubscription_list = array();
    
    while( $result=$database->database_fetch_assoc($resource) )
    {
      // Create blog entry author object
      $author = new se_user();
      $author->user_info['user_id'] = $result['user_id'];
      $author->user_info['user_username'] = $result['user_username'];
      $author->user_info['user_photo'] = $result['user_photo'];
      $author->user_info['user_fname'] = $result['user_fname'];
      $author->user_info['user_lname'] = $result['user_lname'];
      $author->user_displayname();
      $result['blog_author'] =& $author;
      unset($author);
      
      $blogsubscription_list[] = $result;
    }
    
    return $blogsubscription_list;
  }
  
  //
  // END METHOD blog_subscription_list()
  //
  
  
  
  
  
  
  
  //
  // BEGIN METHOD blog_subscription_entry_list()
  //
  // OVERVIEW:
	//    Lists recent blogs from the specified user's subscriptions
  //
	// INPUT:
  //    $start      SQL START
  //    $limit      SQL LIMIT
  //    $order_by   SQL ORDER BY
  //    $where      SQL WHERE
  //
	// OUTPUT:
  //    Array of blogentries
  //
  
  function blog_subscription_entry_list($start=NULL, $limit=NULL, $order_by=NULL, $where=NULL)
  {
    global $database, $user;
    
    if( !$start         ) $start = '0';
    if( !isset($limit)  ) $limit = 20;
    
    // Generate query
    $sql = "
      SELECT
        se_blogentries.*,
        se_blogentrycats.*,
        se_users.user_id,
        se_users.user_username,
        se_users.user_photo,
        se_users.user_fname,
        se_users.user_lname
      FROM
        se_blogsubscriptions
      LEFT JOIN
        se_blogentries
        ON se_blogentries.blogentry_user_id=se_blogsubscriptions.blogsubscription_owner_id
      LEFT JOIN
        se_blogentrycats
        ON se_blogentrycats.blogentrycat_id=se_blogentries.blogentry_blogentrycat_id
      LEFT JOIN
        se_users
        ON se_user.user_id=se_blogsubscriptions.blogsubscription_owner_id
    ";
    
    $sql .= "
      WHERE
        se_blogsubscriptions.blogsubscription_user_id='{$user->user_info['user_id']}' &&
        CASE
          WHEN se_blogentries.blogentry_user_id='{$user->user_info['user_id']}'
            THEN TRUE
          WHEN ((se_blogentries.blogentry_privacy & @SE_PRIVACY_REGISTERED) AND '{$user->user_exists}'<>0)
            THEN TRUE
          WHEN ((se_blogentries.blogentry_privacy & @SE_PRIVACY_ANONYMOUS) AND '{$user->user_exists}'=0)
            THEN TRUE
          WHEN ((se_blogentries.blogentry_privacy & @SE_PRIVACY_FRIEND) AND (SELECT TRUE FROM se_friends WHERE friend_user_id1=se_blogentries.blogentry_user_id AND friend_user_id2='{$user->user_info['user_id']}' AND friend_status='1' LIMIT 1))
            THEN TRUE
          WHEN ((se_blogentries.blogentry_privacy & @SE_PRIVACY_SUBNET) AND '{$user->user_exists}'<>0 AND (SELECT TRUE FROM se_users WHERE user_id=se_blogentries.blogentry_user_id AND user_subnet_id='{$user->user_info['user_subnet_id']}' LIMIT 1))
            THEN TRUE
          WHEN ((se_blogentries.blogentry_privacy & @SE_PRIVACY_FRIEND2) AND (SELECT TRUE FROM se_friends AS friends_primary LEFT JOIN se_users ON friends_primary.friend_user_id1=se_users.user_id LEFT JOIN se_friends AS friends_secondary ON friends_primary.friend_user_id2=friends_secondary.friend_user_id1 WHERE friends_primary.friend_user_id1=se_blogentries.blogentry_user_id AND friends_secondary.friend_user_id2='{$user->user_info['user_id']}' AND se_users.user_subnet_id='{$user->user_info['user_subnet_id']}' LIMIT 1))
            THEN TRUE
          ELSE FALSE
        END
    ";
    
    if( !empty($where) )
      $sql .= " && $where";
    
    if( !empty($order_by) ) $sql .= "
      ORDER BY
        $order_by
    ";
    
    $sql .= "
      LIMIT
        {$start}, {$limit}
    ";
    
    $resource = $database->database_query($sql);
    
    // Get results
    $blogentry_list = array();
    
    while( $result=$database->database_fetch_assoc($resource) )
    {
      // Create blog entry author object
      $author = new se_user();
      $author->user_info['user_id'] = $result['user_id'];
      $author->user_info['user_username'] = $result['user_username'];
      $author->user_info['user_photo'] = $result['user_photo'];
      $author->user_info['user_fname'] = $result['user_fname'];
      $author->user_info['user_lname'] = $result['user_lname'];
      $result['blogentry_author'] =& $author;
      
      $blogentry_list[] = $result;
      unset($author);
    }
    
    return $blogentry_list;
  }
  
  //
  // END METHOD blog_subscription_entry_list()
  //
  
  
  
  
  
  
  
  //
  // BEGIN METHOD blog_subscription_notification()
  //
  // OVERVIEW:
	//    Lists recent blogs from the specified user's subscriptions
  //
	// INPUT:
  //
	// OUTPUT:
  //
  
  function blog_subscription_notification($newblogentry_id, $newblogentry_title, $newblogentry_privacy=1)
  {
    global $database, $user, $url, $notify;
    
    // Quick fix for self
    if( !$newblogentry_privacy || $newblogentry_privacy==1 )
      return;
    
    // Generate query
    $sql = "
      SELECT
        se_blogsubscriptions.*,
        subscriber.user_id,
        subscriber.user_username,
        subscriber.user_fname,
        subscriber.user_lname,
        subscriber.user_email,
        subscriber_settings.usersetting_notify_newblogsubscriptionentry
      FROM
        se_blogsubscriptions
      LEFT JOIN
        se_users AS subscriber
        ON subscriber.user_id=se_blogsubscriptions.blogsubscription_user_id
      LEFT JOIN
        se_usersettings AS subscriber_settings
        ON subscriber_settings.usersetting_user_id=subscriber.user_id
      WHERE
        se_blogsubscriptions.blogsubscription_owner_id='{$user->user_info['user_id']}' &&
        CASE
          /* DO NOT SEND AN EMAIL TO SELF, BESIDES THEY SHOULDNT BE SUBSCRIBED TO THEIR OWN BLOG... */
          WHEN subscriber.user_id='{$user->user_info['user_id']}'
            THEN FALSE
          /* IGNORE MISSING USERS */
          WHEN (({$newblogentry_privacy} & @SE_PRIVACY_ANONYMOUS) AND subscriber.user_id IS NULL)
            THEN FALSE
          /* NORMAL */
          WHEN (({$newblogentry_privacy} & @SE_PRIVACY_REGISTERED) AND subscriber.user_id IS NOT NULL)
            THEN TRUE
          WHEN (({$newblogentry_privacy} & @SE_PRIVACY_FRIEND) AND (SELECT TRUE FROM se_friends WHERE friend_user_id1='{$user->user_info['user_id']}' AND friend_user_id2=subscriber.user_id AND friend_status='1' LIMIT 1))
            THEN TRUE
          WHEN (({$newblogentry_privacy} & @SE_PRIVACY_SUBNET) AND (SELECT TRUE FROM se_users WHERE user_id='{$user->user_info['user_id']}' AND user_subnet_id=subscriber.user_subnet_id LIMIT 1))
            THEN TRUE
          WHEN (({$newblogentry_privacy} & @SE_PRIVACY_FRIEND2) AND (
              SELECT TRUE FROM se_friends AS friends_primary
              LEFT JOIN se_users ON friends_primary.friend_user_id1=se_users.user_id
              LEFT JOIN se_friends AS friends_secondary ON friends_primary.friend_user_id2=friends_secondary.friend_user_id1
              WHERE friends_primary.friend_user_id1='{$user->user_info['user_id']}' AND friends_secondary.friend_user_id2=subscriber.user_id AND se_users.user_subnet_id=subscriber.user_subnet_id LIMIT 1)
              )
            THEN TRUE
          ELSE FALSE
        END
    ";
    
    $resource = $database->database_query($sql);
    
    // Get all recipients and send emails
    // TODO: large numbers of subscribers
    
    $blogentry_url = $url->url_create('blog_entry', $user->user_info['user_username'], $newblogentry_id);
    
    while( $result=$database->database_fetch_assoc($resource) )
    {
      // Create user object for displayname
      $recipient_object = new se_user();
      $recipient_object->user_info['user_id'] = $result['user_id'];
      $recipient_object->user_info['user_username'] = $result['user_username'];
      $recipient_object->user_info['user_fname'] = $result['user_fname'];
      $recipient_object->user_info['user_lname'] = $result['user_lname'];
      $recipient_object->user_displayname();
      
      // NOTIFICATION
      $notifytype = $notify->notify_add(
        $recipient_object->user_info['user_id'],
        "newblogsubscriptionentry",
        $newblogentry_id,
        Array(
          $user->user_info['user_username'],
          $newblogentry_id
        ),
        Array(
          $newblogentry_title
        )
      );
      
      // EMAIL NOTIFICATION
      if( !empty($result['user_email']) && $result['usersetting_notify_newblogsubscriptionentry'] )
      {
        
        send_systememail('newblogsubscriptionentry', $result['user_email'], Array(
          $recipient_object->user_displayname,
          $user->user_displayname,
          "<a href=\"$blogentry_url\">$blogentry_url</a>"
        ));
      }
      
      unset($recipient_object);
    }
  }
  
  //
  // END METHOD blog_subscription_notification()
  //
  
  
  
  
  
  
  
  //
  // BEGIN METHOD blog_trackback_generate()
  //
  // OVERVIEW:
	//    Creates the RDF signature used for trackback auto discovery
  //
	// INPUT:
  //    &$blogentry_info   An array of info about the blog
  //
	// OUTPUT:
  //    The RDF signature
  //
  
  function blog_trackback_generate(&$blogentry_info)
  {
    global $url, $owner;
    
    $trackback = new Trackback(NULL, $owner->user_displayname, "UTF-8");
    
    return $trackback->rdf_autodiscover(
      date("r", $blogentry_info['blogentry_date']),
      $blogentry_info['blogentry_title'],
      $blogentry_info['blogentry_body'],
      $url->url_create('blog_entry', $owner->user_info['user_username'], $blogentry_info['blogentry_id']),
      $url->url_create('blog_trackback', $owner->user_info['user_username'], $blogentry_info['blogentry_id']),
      $owner->user_displayname
    );
  }
  
  //
  // END METHOD blog_trackback_generate()
  //
  
  
  
  
  
  
  
  //
  // BEGIN METHOD blog_trackback_receive()
  //
  // OVERVIEW:
	//    Used to receive a trackback send request from a remote server
  //
	// INPUT:
  //    void
  //
	// OUTPUT:
  //    A trackback XML response
  //
  
  function blog_trackback_receive()
  {
    global $database, $user, $setting;
    
    $is_error = FALSE;
    
    // Create trackback class instance
    $trackback = new Trackback(NULL, NULL, "UTF-8");
    
    // Prepare data
    $trackback_eid          = $trackback->e_id;
    $trackback_url          = trim($trackback->url);
    $trackback_title        = trim($trackback->title);
    $trackback_excerpt      = trim($trackback->excerpt);
    $trackback_bname        = trim($trackback->bname);
    $trackback_ip           = $_SERVER['REMOTE_ADDR'];
    $trackback_time         = time();
    $trackback_excerpthash  = md5($trackback_excerpt);
    
    // Clean body
	  $trackback_excerpt = str_replace("\r\n", "<br />", cleanHTML(censor(htmlspecialchars_decode($trackback_excerpt)), $setting['setting_comment_html']));
    
    // Trackbacks not allowed
    if( !$user->level_info['level_blog_trackbacks_allow'] )
      $is_error = 1500013;
    
    // No ID specified
    if( !$trackback_eid )
      $is_error = 1500008;
      
    // Trackback URL is empty
    if( !$trackback_url )
      $is_error = 1500009;
    
    // Get entry info. TODO: switch to SELECT NULL?
    if( !$is_error )
    {
      $sql = "
        SELECT
          NULL
        FROM
          se_blogentries
        WHERE
          se_blogentries.blogentry_id='{$trackback_eid}'
        LIMIT
          1
      ";
      
      $resource = $database->database_query($sql);
      
      // Entry not found
      if( !$database->database_num_rows($resource) )
        $is_error = 1500010;
    }
    
    
    // See if trackback has already been received
    if( !$is_error )
    {
      $sql = "
        SELECT
          NULL
        FROM
          se_blogtrackbacks
        WHERE
          blogtrackback_blogentry_id='{$trackback_eid}' &&
          blogtrackback_name='{$trackback_bname}' &&
          blogtrackback_excerpthash='{$trackback_excerpthash}'
        LIMIT
          1
      ";
      
      $resource = $database->database_query($sql);
      
      // Already tracked
      if( $database->database_num_rows($resource) )
        $is_error = 1500011;
    }
    
    
    // Only 1/15 seconds
    if( !$is_error )
    {
      $trackback_timeout = 15;
      
      $sql = "
        SELECT
          NULL
        FROM
          se_blogtrackbacks
        WHERE
          blogtrackback_ip='{$trackback_ip}' &&
          blogtrackback_date>".($trackback_time - $trackback_timeout)."
        LIMIT
          1
      ";
      
      $resource = $database->database_query($sql);
      
      if( $database->database_num_rows($resource) )
        $is_error = 1500012;
    }
    
    
    // TODO: antispam
    
    
    // INSERT
    if( !$is_error )
    {
      $sql = "
        INSERT INTO se_blogtrackbacks
        (
          blogtrackback_blogentry_id,
          blogtrackback_name,
          blogtrackback_title,
          blogtrackback_excerpt,
          blogtrackback_excerpthash,
          blogtrackback_url,
          blogtrackback_ip,
          blogtrackback_date
        ) VALUES (
          '{$trackback_eid}',
          '{$trackback_bname}',
          '{$trackback_title}',
          '{$trackback_excerpt}',
          '{$trackback_excerpthash}',
          '{$trackback_url}',
          '{$trackback_ip}',
          '{$trackback_time}'
        )
      ";
      
      $resource = $database->database_query($sql);
      
      if( !$database->database_affected_rows($resource) )
        $is_error = 1500013;
      
      
      // UPDATE TRACKBACK COUNT
      $sql = "UPDATE se_blogentries SET blogentry_totaltrackbacks=blogentry_totaltrackbacks+1 WHERE blogentry_id='{$trackback_eid}' LIMIT 1";
      $database->database_query($sql);
    }
    
    
    
    // LOG
    if( empty($blogentry_url) && !empty($_SERVER['HTTP_REFERER']) )
      $blogentry_url = $_SERVER['HTTP_REFERER'];
    if( empty($blogentry_url) && !empty($_SERVER['REMOTE_ADDR']) )
      $blogentry_url = $_SERVER['REMOTE_ADDR'];
    
    $sql = "
      INSERT INTO se_blogpings
      (
        blogping_blogentry_id,
        blogping_target_url,
        blogping_source_url,
        blogping_status,
        blogping_type,
        blogping_ip
      ) VALUES (
        '{$trackback_eid}',
        '".$database->database_real_escape_string($_SERVER['REQUEST_URI'])."',
        '".$database->database_real_escape_string($blogentry_url)."',
        '1',
        '2',
        '{$_SERVER['REMOTE_ADDR']}'
      )
    ";
    
    $resource = $database->database_query($sql);
    
    
    // GET ERROR MESSAGE
    SE_Language::_preload( $is_error ? $is_error : 1500014 );
    SE_Language::load();
    $message = SE_Language::_get( $is_error ? $is_error : 1500014 );
    
    return $trackback->recieve(!$is_error, $message);
  }
  
  //
  // END METHOD blog_trackback_receive()
  //
  
  
  
  
  
  
  
  //
  // BEGIN METHOD blog_trackback_send()
  //
  // OVERVIEW:
	//    Sends a trackback to a remote server
  //
	// INPUT:
  //    $ping_urls              An array of trackback URLs
  //    $blogentry_id           The id of the local blogentry
  //    $blogentry_title        The title of the local blogentry
  //    &$blogentry_body        The body of the local blogentry
  //
	// OUTPUT:
  //    'result'                Whether or not it was successful
  //    'trackback_results'     An array of results for each trackback url
  //
  
  function blog_trackback_send($ping_urls, $blogentry_id, $blogentry_title, &$blogentry_body)
  {
    global $database, $user, $url, $setting;
    
    // Trackback class
    $trackback = new Trackback($user->user_displayname."'s Blog", $user->user_displayname, "UTF-8");
    
    // Prepare data
    $blogentry_excerpt = ( strlen($blogentry_body)>255 ? substr($blogentry_body, 0, 254) : $blogentry_body );
    $blogentry_url = $url->url_create('blog_entry', $user->user_info['user_username'], $blogentry_id);
    
    // Allow multiple trackbacks
    if( !is_array($ping_urls) )
      $ping_urls = array($ping_urls);
    
    // Detect trackbacks
    if( $user->level_info['level_blog_trackbacks_detect'] )
    {
      $detected_trackback_urls = $trackback->auto_discovery($blogentry_body);
      $ping_urls = array_merge($ping_urls, $detected_trackback_urls);
    }
    
    $ping_urls = array_unique(array_filter($ping_urls));
    
    // Ping the trackback urls (and generate ping log query)
    $sql = "INSERT INTO se_blogpings (blogping_blogentry_id, blogping_target_url, blogping_source_url, blogping_status, blogping_type, blogping_ip) VALUES ";
    $isFirst = TRUE;
    
    $trackback_results = array();
    foreach( $ping_urls as $ping_url )
    {
      $tb_result = $trackback->ping($ping_url, $blogentry_url, $blogentry_title, $blogentry_excerpt);
      
      if( $tb_result=="1" )
        $trackback_results[$ping_url] = "Could not connect";
      elseif( $tb_result=="2" )
        $trackback_results[$ping_url] = "Success";
      elseif( $tb_result=="3" )
        $trackback_results[$ping_url] = "An error occurred";
      else
        $trackback_results[$ping_url] = "An unknown error has occurred";
      
      if( !$isFirst ) $sql .= ',';
      $sql .= "('$blogentry_id', '".$database->database_real_escape_string($ping_url)."', '".$database->database_real_escape_string($_SERVER['REQUEST_URI'])."', 1, 1, '{$_SERVER['REMOTE_ADDR']}')";
      
      $isFirst = FALSE;
    }
    
    if( !$isFirst )
      $resource = $database->database_query($sql);
    
    return array(
      'result' => TRUE,
      'trackback_results' => $results
    );
  }
  
  //
  // END METHOD blog_trackback_send()
  //
  
  
  
  
  
  
  
  //
  // BEGIN METHOD blog_trackback_total()
  //
  // OVERVIEW:
	//    Gets a count of trackbacks stored on the local server
  //
	// INPUT:
  //    $where                ---
  //
	// OUTPUT:
  //    The number of trackbacks
  //
  
  function blog_trackback_total($where=NULL)
  {
    global $database;
    
    $sql = "
      SELECT
        COUNT(*) AS trackback_count
      FROM
        se_blogtrackbacks
    ";
    
    if( $where ) $sql .= "
      WHERE
        $where
    ";
    
    $resource = $database->database_query($sql);
    $result = $database->database_fetch_assoc($resource);
    
    return ( !empty($result['trackback_count']) ? $result['trackback_count'] : 0 );
  }
  
  //
  // END METHOD blog_trackback_total()
  //
  
  
  
  
  
  
  
  //
  // BEGIN METHOD blog_trackback_list()
  //
  // OVERVIEW:
	//    Gets a list of trackbacks stored on the local server
  //
	// INPUT:
  //    $where                ---
  //
	// OUTPUT:
  //    A list of trackbacks
  //
  
  function blog_trackback_list($start=NULL, $limit=NULL, $order='blogtrackback_date ASC', $where=NULL)
  {
    global $database;
    
    if( !$limit ) $limit = 20;
    
    $sql = "
      SELECT
        *
      FROM
        se_blogtrackbacks
    ";
    
    if( $where ) $sql .= "
      WHERE
        $where
    ";
    
    if( $order ) $sql .= "
      ORDER BY
        $order
    ";
    
    if( $start || $limit )
      $sql .= " LIMIT";
      
    if( $start )
      $sql .= " {$start},";
      
    if( $limit )
      $sql .= " {$limit}";
    
    $resource = $database->database_query($sql);
    
    $trackbacks = array();
    while( $result=$database->database_fetch_assoc($resource) )
      $trackbacks[] = $result;
    
    return $trackbacks;
  }
  
  //
  // END METHOD blog_trackback_list()
  //
  
  
  
  
  
  function blog_category_create($category_title, $parent_category_id=NULL)
  {
    global $database, $admin, $user;
    
    if( !trim($category_title) ) return FALSE;
    if( !$parent_category_id ) $parent_category_id = '0';
    
    // Truncate and escape
    if( strlen($category_title)>64 ) $category_title = substr($category_title, 0, 64);
    
    $user_id = $user->user_info['user_id'];
    $lvar_id = 0;
    
    // If admin, create language variable
    if( is_object($admin) && $admin->admin_exists )
    {
      $lvar_id = SE_Language::edit(0, $category_title, NULL, LANGUAGE_INDEX_SUBNETS);
      $user_id = 0;
    }
    
    // If not user or not allowed, return
    elseif( !is_object($user) || !$user->user_exists || !$user->level_info['level_blog_category_create'] )
    {
      return FALSE;
    }
    
    // INSERT
    $category_title = addslashes($category_title);
    
    $sql = "
      INSERT INTO se_blogentrycats (
        blogentrycat_title,
        blogentrycat_user_id,
        blogentrycat_languagevar_id,
        blogentrycat_parentcat_id
      ) VALUES (
        '{$category_title}',
        '{$user_id}',
        '{$lvar_id}',
        '{$parent_category_id}'
      )
    ";
    
    $resource = $database->database_query($sql);
    
    return ( $database->database_affected_rows($resource) ? $database->database_insert_id() : FALSE );
  }
  
  
  
  
  
  function blog_category_list($user_id=FALSE)
  {
    global $database;
    
    $sql = "SELECT * FROM se_blogentrycats ";
    
    if( $user_id!==TRUE && $user_id>0 )
      $sql .= "WHERE blogentrycat_user_id=0 || blogentrycat_user_id='{$user_id}' ";
    elseif( $user_id===FALSE )
      $sql .= "WHERE blogentrycat_user_id=0 ";
    
    $sql .= "ORDER BY blogentrycat_id ASC";
    
    $resource = $database->database_query($sql);
    
    $blogentrycats_array = array();
    while( $result=$database->database_fetch_assoc($resource) )
    {
      // PRELOAD
      if( !empty($result['blogentrycat_languagevar_id']) )
        SE_Language::_preload($result['blogentrycat_languagevar_id']);
      
      $blogentrycats_array[] = $result;
    }
    
    return $blogentrycats_array;
  }
  
}

?>