<?php

//
//  THIS CLASS CONTAINS USER-RELATED METHODS.
//  IT IS USED DURING THE CREATION, MODIFICATION AND DELETION OF A USER.
//
//  METHODS IN THIS CLASS:
//    SEUser()
//
//    getLevelSettings()
//    getUserSettings()
//    getProfileCategoryInfo()
//    getProfileValues()
//
//    user_displayname()
//    user_displayname_update()
//    user_settings()
//    user_checkCookies()
//    user_login()
//    user_setcookies()
//    user_clear()
//    user_logout()
//    user_account()
//    user_password()
//    user_subnet_select()
//    user_lastupdate()
//    user_photo()
//    user_photo_upload()
//    user_photo_delete()
//    user_friend_total()
//    user_friend_list()
//    user_friend_add()
//    user_friend_remove()
//    user_friend_of_friend()
//    user_friended()
//    user_blocked()
//    user_privacy_max()
//    user_create()
//    user_delete()
//    user_message_total()
//    user_message_list()
//    user_message_send()
//    user_message_delete_selected()
//    user_message_cleanup()
//    user_message_validate()
//    user_message_view()
//


class SEUser
{
	// INITIALIZE VARIABLES
	var $is_error;			// DETERMINES WHETHER THERE IS AN ERROR OR NOT, CONTAINS RELEVANT ERROR CODE
	var $user_exists;		// DETERMINES WHETHER WE ARE EDITING AN EXISTING USER OR NOT

	var $user_info;			// CONTAINS USER'S INFORMATION FROM SE_USERS TABLE
	var $profile_info;		// CONTAINS USER'S INFORMATION FROM SE_PROFILEVALUES TABLE
	var $level_info;		// CONTAINS USER'S INFORMATION FROM SE_LEVELS TABLE
	var $subnet_info;		// CONTAINS USER'S INFORMATION FROM SE_SUBNETS TABLE
	var $usersetting_info;		// CONTAINS USER'S INFORMATION FROM SE_USERSETTINGS TABLE
	
	var $user_salt;			// CONTAINS THE SALT USED TO ENCRYPT USER'S PASSWORD

	var $moderation_privacy;	// CONTAINS THE PRIVACY LEVEL THAT IS ALLOWED TO MODERATE FOR THIS USER

	var $session_info;	// CONTAINS THE PRIVACY LEVEL THAT IS ALLOWED TO MODERATE FOR THIS USER









  //
	// THIS METHOD SETS INITIAL VARS SUCH AS USER INFO AND LEVEL INFO
  //
	// INPUT:
  //    $user_unique (OPTIONAL) REPRESENTING AN ARRAY:
	//		$user_unique[0] REPRESENTS THE USER'S ID (user_id)
	//		$user_unique[1] REPRESENTS THE USER'S USERNAME (user_username)
	//		$user_unique[2] REPRESENTS THE USER'S EMAIL (user_email)
	//	  $select_fields (OPTIONAL) REPRESENTING AN ARRAY:
	//		$select_fields[0] REPRESENTS THE FIELDS TO SELECT FROM THE SE_USERS TABLE
	//		$select_fields[1] REPRESENTS THE FIELDS TO SELECT FROM THE SE_PROFILEVALUES TABLE (QUERY WILL NOT RUN AT ALL IF VALUE IS LEFT BLANK)
	//		$select_fields[2] REPRESENTS THE FIELDS TO SELECT FROM THE SE_LEVELS TABLE (QUERY WILL NOT RUN AT ALL IF VALUE IS LEFT BLANK)
	//		$select_fields[3] REPRESENTS THE FIELDS TO SELECT FROM THE SE_SUBNETS TABLE (QUERY WILL NOT RUN AT ALL IF VALUE IS LEFT BLANK)
	//	  
	// OUTPUT: 
  //    void
  //
  
	function SEUser($user_unique = Array('0', '', ''), $select_fields = Array('*', '*', '*', '*'))
  {
	  global $database;
    
	  // SET VARS
	  $this->is_error = 0;
	  $this->user_exists = 0;
	  $this->user_info['user_id'] = 0;
	  $this->user_info['user_subnet_id'] = 0;
	  $this->moderation_privacy = 1;
    
    $user_unique_id = ( !empty($user_unique[0]) ? $user_unique[0] : NULL );
    $user_unique_username = ( !empty($user_unique[1]) ? $user_unique[1] : NULL );
    $user_unique_email = ( !empty($user_unique[2]) ? $user_unique[2] : NULL );
	  
	  // VERIFY USER_ID/USER_USERNAME/USER_EMAIL IS VALID AND SET APPROPRIATE OBJECT VARIABLES
	  if( $user_unique_id || $user_unique_username || $user_unique_email )
    {
	    // SET USERNAME AND EMAIL TO LOWERCASE
	    $user_username = strtolower($user_unique_username);
	    $user_email = strtolower($user_unique_email);
      
	    // SELECT USER USING SPECIFIED SELECTION PARAMETER
	    $sql_array = array();
	    if( !empty($user_unique[0]) )
	      $sql_array[] = "SELECT {$select_fields[0]} FROM se_users WHERE user_id='{$user_unique_id}' LIMIT 1";
      
	    if( !empty($user_unique[1]) )
	      $sql_array[] = "SELECT {$select_fields[0]} FROM se_users WHERE LOWER(user_username)='{$user_username}' LIMIT 1";
      
	    if( !empty($user_unique[2]) )
	      $sql_array[] = "SELECT {$select_fields[0]} FROM se_users WHERE LOWER(user_email)='{$user_email}' LIMIT 1";
      
	    if( count($sql_array)>1 )
	      $sql = '('.join(') UNION (', $sql_array).')';
	    else
	      $sql = $sql_array[0];
      
	    $user = $database->database_query($sql);
	    if($database->database_num_rows($user) == 1)
      {
	      $this->user_exists = 1;
	      $this->user_info = $database->database_fetch_assoc($user);
        
	      // SET USER SALT
	      $this->user_salt = $this->user_info['user_code'];
        
	      // SET DISPLAY NAME (BACKWARDS COMPAT)
        //$this->user_displayname = $this->user_info['user_displayname'];
	      $this->user_displayname();
        
	      // SELECT PROFILE CATEGORY INFO
	      if( !empty($this->user_info['user_profilecat_id']) )
          $this->profilecat_info =& SEUser::getProfileCategoryInfo($this->user_info['user_profilecat_id']);
	      //if(isset($this->user_info[user_profilecat_id])) { $this->profilecat_info = $database->database_fetch_assoc($database->database_query("SELECT profilecat_id, profilecat_title FROM se_profilecats WHERE profilecat_id=".$this->user_info[user_profilecat_id]." LIMIT 1")); }
        
	      // SELECT PROFILE INFO
	      if( !empty($select_fields[1]) )
          $this->profile_info =& SEUser::getProfileValues($this->user_info['user_id']);
	      //if($select_fields[1] != "") { $this->profile_info = $database->database_fetch_assoc($database->database_query("SELECT $select_fields[1] FROM se_profilevalues WHERE profilevalue_user_id='".$this->user_info[user_id]."'")); }
        
	      // SELECT LEVEL INFO
	      if( !empty($select_fields[2]) )
          $this->level_info =& SEUser::getLevelSettings($this->user_info['user_level_id']);
	      //if($select_fields[2] != "") { $this->level_info = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_levels WHERE level_id='".$this->user_info[user_level_id]."'")); }
        
        // GET USER SETTINGS
        $this->usersetting_info =& SEUser::getUserSettings($this->user_info['user_id']);
        
        
	      // SELECT SUBNET INFO
	      if( $this->user_info['user_subnet_id'] )
        {
	        if( !empty($select_fields[3]) )
            $this->subnet_info =& SECore::getSubnetworkInfo($this->user_info['user_subnet_id']);
	        //if($select_fields[3] != "") { $this->subnet_info = $database->database_fetch_assoc($database->database_query("SELECT subnet_id, subnet_name FROM se_subnets WHERE subnet_id='".$this->user_info[user_subnet_id]."'")); }
	      }
        else
        {
	        $this->subnet_info['subnet_id'] = 0;
          $this->subnet_info['subnet_name'] = 152;
	      }
	      SE_Language::_preload($this->subnet_info['subnet_name']);
	    }
	  }
	}
  
  // END SEUser() METHOD







  function &getLevelSettings($level_id)
  {
    static $level_settings;
    
    if( !is_array($level_settings) ) $level_settings = array();
    
    if( !isset($level_settings[$level_id]) )
    {
      $cache = SECache::getInstance('serial', array('lifetime' => 3600));
      
      // Get from cache
      if( is_object($cache) )
      {
        $level_settings[$level_id] = $cache->get('site_level_settings_'.$level_id);
      }
      
      // Get from database
      if( !is_array($level_settings[$level_id]) )
      {
        $database = SEDatabase::getInstance();
        $resource = $database->database_query("SELECT * FROM se_levels WHERE level_id='{$level_id}' LIMIT 1");
        $level_settings[$level_id] = $database->database_fetch_assoc($resource);
        
        // Store in cache
        if( is_object($cache) )
        {
          $cache->store($level_settings[$level_id], 'site_level_settings_'.$level_id);
        }
      }
    }
    
    return $level_settings[$level_id];
  }







  function &getUserSettings($user_id)
  {
    static $user_settings;
    
    if( !is_array($user_settings) ) $user_settings = array();
    
    if( !isset($user_settings[$user_id]) )
    {
      $cache = SECache::getInstance('serial', array('lifetime' => 3600));
      
      // Get from cache
      if( is_object($cache) )
      {
        $user_settings[$user_id] = $cache->get('site_user_settings_'.$user_id);
      }
      
      // Get from database
      if( !is_array($user_settings[$user_id]) )
      {
        $database = SEDatabase::getInstance();
        $resource = $database->database_query("SELECT * FROM se_usersettings WHERE usersetting_user_id='{$user_id}' LIMIT 1");
        $user_settings[$user_id] = $database->database_fetch_assoc($resource);
        
        // Store in cache
        if( is_object($cache) )
        {
          $cache->store($user_settings[$user_id], 'site_user_settings_'.$user_id);
        }
      }
    }
    
    return $user_settings[$user_id];
  }







  function &getProfileCategoryInfo($profilecat_id)
  {
    static $profile_cats;
    
    if( !is_array($profile_cats) ) $profile_cats = array();
    
    if( !isset($profile_cats[$profilecat_id]) )
    {
      $cache = SECache::getInstance('serial', array('lifetime' => 3600));
      
      // Get from cache
      if( is_object($cache) )
      {
        $profile_cats[$profilecat_id] = $cache->get('site_profile_categories_'.$profilecat_id);
      }
      
      // Get from database
      if( !is_array($profile_cats[$profilecat_id]) )
      {
        $database = SEDatabase::getInstance();
        $resource = $database->database_query("SELECT profilecat_id, profilecat_title FROM se_profilecats WHERE profilecat_id='{$profilecat_id}' LIMIT 1");
        $profile_cats[$profilecat_id] = $database->database_fetch_assoc($resource);
        
        // Store in cache
        if( is_object($cache) )
        {
          $cache->store($profile_cats[$profilecat_id], 'site_profile_categories_'.$profilecat_id);
        }
      }
    }
    
    return $profile_cats[$profilecat_id];
  }







  function &getProfileValues($user_id)
  {
    static $user_profiles;
    
    if( !is_array($user_profiles) ) $user_profiles = array();
    
    if( !isset($user_profiles[$user_id]) )
    {
      $cache = SECache::getInstance('serial', array('lifetime' => 3600));
      
      // Get from cache
      if( is_object($cache) )
      {
        $user_profiles[$user_id] = $cache->get('site_user_profiles_'.$user_id);
      }
      
      // Get from database
      if( !is_array($user_profiles[$user_id]) )
      {
        $database = SEDatabase::getInstance();
        $resource = $database->database_query("SELECT * FROM se_profilevalues WHERE profilevalue_user_id='{$user_id}' LIMIT 1");
        $user_profiles[$user_id] = $database->database_fetch_assoc($resource);
        
        // Store in cache
        if( is_object($cache) )
        {
          $cache->store($user_profiles[$user_id], 'site_user_profiles_'.$user_id);
        }
      }
    }
    
    return $user_profiles[$user_id];
  }







  
  //
	// THIS METHOD SETS A USER'S DISPLAY NAME
  //
	// INPUT:
  //    void
  //
	// OUTPUT: 
  //    void
  //
  
	function user_displayname()
  {
	  // SET DISPLAY NAME
    if( !empty($this->user_info['user_displayname']) && trim($this->user_info['user_displayname']) )
      $this->user_displayname = $this->user_info['user_displayname'];
    elseif( !empty($this->user_info['user_fname']) && !empty($this->user_info['user_lname']) && trim($this->user_info['user_fname']) && trim($this->user_info['user_lname']) )
      $this->user_info['user_displayname'] = $this->user_displayname = $this->user_info['user_fname'].' '.$this->user_info['user_lname'];
    elseif( !empty($this->user_info['user_fname']) && trim($this->user_info['user_fname']) )
      $this->user_info['user_displayname'] = $this->user_displayname = $this->user_info['user_fname'];
    elseif( !empty($this->user_info['user_lname']) && trim($this->user_info['user_lname']) )
      $this->user_info['user_displayname'] = $this->user_displayname = $this->user_info['user_lname'];
    elseif( !empty($this->user_info['user_username']) && trim($this->user_info['user_username']) )
      $this->user_info['user_displayname'] = $this->user_displayname = $this->user_info['user_username'];
    else
      $this->user_info['user_displayname'] = $this->user_displayname = $this->user_info['user_id'];
    
    $this->user_displayname_short = ( !empty($this->user_info['user_fname']) && trim($this->user_info['user_fname']) ? $this->user_info['user_fname'] : $this->user_info['user_username'] );
	}
  
  // END user_displayname() METHOD








  //
	// THIS METHOD UPDATES A USER'S DISPLAY NAME IN THE DATABASE
  //
	// INPUT:
  //    $mode   - Denotes the method used to generate the displayname
  //
	// OUTPUT: 
  //    void
  //
  
	function user_displayname_update($user_fname=NULL, $user_lname=NULL)
  {
    global $setting, $database;
    
    // Check user exists and allowed method
    if( !$this->user_exists || (!$user_fname && !$user_lname) ) return;
    
    if( empty($this->usersetting_info) ) $this->user_settings();
    
    $delimiter = '';
    $user_displayname = '';
    $user_fname = trim((string)$user_fname);
    $user_lname = trim((string)$user_lname);
    
    switch( (int)$this->usersetting_info['usersetting_displayname_method'] )
    {
      // {First name} {Last name}
      case 1:
      default:
        if( $user_fname && $user_lname ) $delimiter = ' ';
        $user_displayname = $user_fname.$delimiter.$user_lname;
      break;
      
      // {Last name} {First name}
      case 2:
        if( $user_fname && $user_lname ) $delimiter = ' ';
        $user_displayname = $user_lname.$delimiter.$user_fname;
      break;
      
      // {Last name}, {First name}
      case 3:
        if( $user_fname && $user_lname ) $delimiter = ', ';
        $user_displayname = $user_lname.$delimiter.$user_fname;
      break;
      
      // {Last name}
      case 4:
        $user_displayname = $user_lname;
      break;
      
      // {First name}
      case 5:
        $user_displayname = $user_fname;
      break;
      
      // Custom (TODO)
      case 6:
        $user_displayname = sprintf($setting['setting_displayname_method_custom'], $user_fname, $user_lname);
      break;
    }
    
    // Fallback to username or user id
    if( !$user_displayname && $user_username )
      $user_displayname = $this->user_info['user_username'];
    elseif( !$user_displayname )
      $user_displayname = $this->user_info['user_id'];
    
    // Update the current user object?
    $this->user_info['user_displayname'] = $this->user_displayname = $user_displayname;
    
    // Update database
    $sql = "UPDATE se_users SET user_displayname='".addslashes($user_displayname)."' WHERE user_id='{$this->user_info['user_id']}' LIMIT 1";
    $database->database_query($sql);
	}
  
  // END user_displayname_update() METHOD








  //
	// THIS METHOD POPULATES THE USERSETTING VARIABLE
  //
	// INPUT:
  //    $select_fields (OPTIONAL) REPRESENTING THE FIELDS TO SELECT FROM THE USERSETTINGS TABLE
  //
	// OUTPUT: 
  //    void
  //
  
	function user_settings($select_fields = "*")
  {
	  global $database;
    
	  $this->usersetting_info =& SEUser::getUserSettings($this->user_info['user_id']);
	  //$this->usersetting_info = $database->database_fetch_assoc($database->database_query("SELECT $select_fields FROM se_usersettings WHERE usersetting_user_id='".$this->user_info[user_id]."'"));
	}
  
  // END user_settings() METHOD








	// THIS METHOD VERIFIES LOGIN COOKIES, SETS APPROPRIATE OBJECT VARIABLES, AND UPDATES LAST ACTIVE TIME
	// INPUT: 
	// OUTPUT: 
	function user_checkCookies()
  {
	  global $database, $setting, $admin;
    
    $session_object =& SESession::getInstance();
    
    // Ignore bots
    if( strpos($_SERVER['HTTP_USER_AGENT'], 'Googlebot')!==FALSE ) return;
    if( strpos($_SERVER['HTTP_USER_AGENT'], 'msnbot')!==FALSE ) return;
    
    // Check if user exists
    $user_id    = $session_object->get('user_id');
    $user_email = $session_object->get('user_email');
    $user_pass  = $session_object->get('user_pass');
    
    // Check for auth token
    if( !$user_id )
    {
      $this->user_auth_token_check();
    }
    
    if( isset($user_id) && isset($user_email) && isset($user_pass) )
    {
      // Only create if not already exists to help with caching
      if( !$this->user_exists )
      {
        $this->SEUser(Array($user_id));
      }
      
	    // VERIFY USER EXISTS, LOGIN COOKIE VALUES ARE CORRECT, AND EMAIL HAS BEEN VERIFIED - ELSE RESET USER CLASS
	    switch( TRUE )
      {
        case ( !$this->user_exists ):
        case ( $user_email != $this->user_password_crypt($this->user_info['user_email']) ):
        case ( $user_pass != $this->user_info['user_password'] ):
        case ( !$this->user_info['user_verified'] && $setting['setting_signup_verify'] ):
        case ( !$this->user_info['user_enabled'] && (!is_object($admin) || !$admin->admin_exists) ):
          $this->user_clear();
        break;
	    }
      
      // MIGHT REMOVE THIS IN FAVOR OF SESSIONS?
      if( $this->user_exists && time()>$this->user_info['user_lastactive']+600 )
      {
        $time_current = time();
        $database->database_query("UPDATE se_users SET user_lastactive='{$time_current}', user_ip_lastactive='{$_SERVER['REMOTE_ADDR']}' WHERE user_id='{$this->user_info['user_id']}' LIMIT 1");
      }
	  }
    
    
    
    // VISITOR HANDLING (ONLY UPDATE ONCE EVERY TWO MINUTES)
    $user_lastactive = $session_object->get('user_lastactive', 0);
    
    if( empty($user_lastactive) || ($user_lastactive < time() - 120) )
    {
      $visitor_ip = ip2long($_SERVER['REMOTE_ADDR']);
      $visitor_browser = addslashes(trim(substr($_SERVER['HTTP_USER_AGENT'], 0, 255)));
      $visitor_lastactive = time();
      $visitor_invisible = (bool) ( $this->user_exists && $this->user_info['user_invisible'] );
      
      $visitor_user_id = ( $this->user_exists ? $this->user_info['user_id'] : '0' );
      $visitor_user_username = ( $this->user_exists ? "'".addslashes($this->user_info['user_username'])."'" : 'NULL' );
      $visitor_user_displayname = ( $this->user_exists ? "'".addslashes($this->user_displayname)."'" : 'NULL' );
      
      $sql = "
        INSERT INTO se_visitors (
          visitor_ip,
          visitor_browser,
          visitor_lastactive,
          visitor_invisible,
          visitor_user_id,
          visitor_user_username,
          visitor_user_displayname
        ) VALUES (
          '{$visitor_ip}',
          '{$visitor_browser}',
          '{$visitor_lastactive}',
          '{$visitor_invisible}',
          '{$visitor_user_id}',
          {$visitor_user_username}, /* PRE-QUOTED */
          {$visitor_user_displayname} /* PRE-QUOTED */
        ) ON DUPLICATE KEY UPDATE
          visitor_lastactive='{$visitor_lastactive}',
          visitor_invisible='{$visitor_invisible}' /* ,
          visitor_user_id='{$visitor_user_id}',
          visitor_user_username='{$visitor_user_username}',
          visitor_user_displayname='{$visitor_user_displayname}'
          */
      ";
      
      $database->database_query($sql);
      
      // UPDATE USER LAST ACTIVE IF LOGGED IN
      if( $this->user_exists )
      {
        $sql = "UPDATE se_users SET user_lastactive='{$visitor_lastactive}', user_ip_lastactive='{$_SERVER['REMOTE_ADDR']}' WHERE user_id='{$visitor_user_id}' LIMIT 1";
        $database->database_query($sql);
      }
      
      $session_object->set('user_lastactive', $visitor_lastactive);
      //setcookie("se_user_lastactive", , 0, "/");
    }
    
    
    // REMOVE OLD VISITORS (20% chance)
    if( rand(1,100)<20 )
    {
      $removal_limit = time() - 600;
      $sql = "DELETE FROM se_visitors WHERE visitor_lastactive<'{$removal_limit}'";
      $database->database_query($sql);
    }
	}
  
  // END user_checkCookies() METHOD









	// THIS METHOD TRIES TO LOG A USER IN IF THERE IS NO ERROR
	// INPUT: $email REPRESENTING THE LOGIN EMAIL
	//	  $password REPRESENTING THE LOGIN PASSWORD
	//	  $javascript_disabled (OPTIONAL) A BOOLEAN REPRESENTING WHETHER JAVASCRIPT IS DISABLED OR NOT
	//	  $persistent (OPTIONAL) A BOOLEAN SPECIFYING WHETHER COOKIES SHOULD BE PERSISTENT OR NOT
	// OUTPUT: 
	function user_login($email, $password, $javascript_disabled = 0, $persistent = 0)
  {
	  global $database, $setting;
    
	  $this->SEUser(Array(0, "", $email));
    
	  $current_time = time();
	  $login_result = 0;
    
	  // SHOW ERROR IF JAVASCRIPT IS DIABLED
	  if( $javascript_disabled )
    {
	    $this->is_error = 31;
    }
    
	  // SHOW ERROR IF NO USER ROW FOUND
	  elseif($this->user_exists == 0)
    {
	    $this->is_error = 676;
    }
    
	  // VALIDATE PASSWORD
	  elseif( !trim($password) || $this->user_password_crypt($password) != $this->user_info['user_password'] )
    {
	    $this->is_error = 676;
	  }
    
	  // CHECK IF USER IS ENABLED
	  elseif( !$this->user_info['user_enabled'] )
    {
	    $this->is_error = 677;
    }
    
	  // CHECK IF EMAIL IS VERIFIED
	  elseif( !$this->user_info['user_verified'] && $setting['setting_signup_verify'] )
    {
	    $this->is_error = 678;
	  }
    
	  // INITIATE LOGIN AND ENCRYPT COOKIES
	  else
    {
	    // SET LOGIN RESULT VAR
	    $login_result = TRUE;
      
	    // UPDATE USER LOGIN INFO
	    $database->database_query("UPDATE se_users SET user_lastlogindate='{$current_time}', user_logins=user_logins+1, user_lastactive='{$current_time}', user_ip_lastactive='{$_SERVER['REMOTE_ADDR']}' WHERE user_id='{$this->user_info['user_id']}' LIMIT 1");
      
	    // LOG USER IN
	    $this->user_setcookies($persistent);
      
      // FIX VISITOR TABLE
      $visitor_ip = ip2long($_SERVER['REMOTE_ADDR']);
      $visitor_browser = addslashes(trim(substr($_SERVER['HTTP_USER_AGENT'], 0, 255)));
      $database->database_query("DELETE FROM se_visitors WHERE visitor_ip='{$visitor_ip}' && visitor_browser LIKE '{$visitor_browser}' && visitor_user_id='0'");
      
	    // UPDATE LOGIN STATS
	    update_stats("logins");
	  }
    
	  // BUMP LOG
	  $database->database_query("INSERT INTO se_logins (login_email, login_date, login_ip, login_result) VALUES ('{$email}', '{$current_time}', '{$_SERVER['REMOTE_ADDR']}', '{$login_result}')");
	  bumplog();
	}
  
  // END user_login() METHOD







  
  //
	// THIS METHOD SETS USER LOGIN COOKIES
  //
	// INPUT:
  //    $persistent (OPTIONAL) REPRESENTING WHETHER THE COOKIES SHOULD BE PERSISTENT OR NOT
  //
	// OUTPUT: 
  //    void
  //
  
	function user_setcookies($persistent = false)
  {
    // TODO: PERSISTENT
    $session_object =& SESession::getInstance();
    
    $user_id = ( !empty($this->user_info['user_id']) ? $this->user_info['user_id'] : '' );
    $user_email = ( !empty($this->user_info['user_email']) ? $this->user_password_crypt($this->user_info['user_email']) : '' );
    $user_password = ( !empty($this->user_info['user_password']) ? $this->user_info['user_password'] : '' );
    
    // We don't need to do this any more because of the auth tokens
    // Set cookie parameters
    //$cookie_lifetime = ( $persistent ? (60 * 60 * 24 * 31 * 6) : 0 );
    //if( $cookie_lifetime )
    //{
    //  session_set_cookie_params(10);//$cookie_lifetime);
    //}
    
    // Get new id for security
    $session_object->copy();
    
    // Set user login info
    $session_object->set('user_id', $user_id);
    $session_object->set('user_email', $user_email);
    $session_object->set('user_pass', $user_password);
    $session_object->set('user_persist', (bool) $persistent);
    $session_object->set('user_lastactive', time() - 3600);
    
    // Create new key if logging in, delete old key if logging out
    if( $user_id )
    {
      $this->user_auth_token_create((bool)$persistent);
    }
    else
    {
      $this->user_auth_token_delete();
    }
	}
  
  // END user_setcookies() METHOD









	// THIS METHOD CLEARS ALL THE CURRENT OBJECT VARIABLES
	// INPUT:
	// OUTPUT:
  
	function user_clear()
  {
	  $this->is_error = FALSE;
	  $this->user_exists = FALSE;
    
	  $this->user_info = array();
	  $this->profile_info = array();
	  $this->level_info = array();
	  $this->subnet_info = array();
    
	  $this->new_pms_total = 0;
	  $this->friend_requests_total = 0;
	}
  
  // END user_clear() METHOD








	// THIS METHOD LOGS A USER OUT
	// INPUT:
	// OUTPUT:
  
	function user_logout()
  {
	  global $database;
    
    $session_object =& SESession::getInstance();
    
    // REMOVE AUTH TOKEN
    $this->user_auth_token_delete();
    
	  // CLEAR LAST ACTIVITY DATE
	  $database->database_query("DELETE FROM se_visitors WHERE visitor_user_id='{$this->user_info['user_id']}'");
    $session_object->clear('user_lastactive');
    
	  // CREATE PLAINTEXT USER EMAIL COOKIE WHILE LOGGED OUT
	  setcookie("prev_email", $this->user_info['user_email'], time()+99999999, "/");
    
	  $this->user_clear();
	  $this->user_setcookies();
	}
  
  // END user_logout() METHOD









	// THIS METHOD VALIDATES USER ACCOUNT INPUT
	// INPUT: $email REPRESENTING THE DESIRED EMAIL
	//	  $username REPRESENTING THE DESIRED USERNAME
	// OUTPUT: 
  
	function user_account($email, $username)
  {
	  global $database, $setting;

	  // MAKE SURE FIELDS ARE FILLED OUT
	  if( !trim($email) || (!trim($username) && $setting['setting_username']))
      $this->is_error = 51;
    
	  // MAKE SURE USERNAME IS ALPHANUMERIC
	  if( ereg('[^A-Za-z0-9]', $username) && $setting['setting_username'] )
      $this->is_error = 694;
    
	  // MAKE SURE USERNAME IS NOT BANNED
	  $banned_usernames = explode(",", strtolower($setting['setting_banned_usernames']));
	  if( in_array(strtolower($username), $banned_usernames) && trim($username) && $setting['setting_username'] )
      $this->is_error = 695;
    
	  // MAKE SURE USERNAME IS NOT RESERVED
	  if( is_dir($username) && $setting['setting_username'] )
      $this->is_error = 696;
    
	  // MAKE SURE EMAIL IS NOT BANNED
	  $banned_emails = explode(",", strtolower($setting['setting_banned_emails']));
	  $wildcard_ban = "*".strstr(strtolower($email), "@");
    
    if( trim($email) && in_array(strtolower($email), $banned_emails) )
      $this->is_error = 697;
    
    if( trim($email) && in_array(strtolower($wildcard_ban), $banned_emails) )
      $this->is_error = 697;
    
	  // MAKE SURE EMAIL IS VALID
	  if( !is_email_address($email) )
      $this->is_error = 698;
    
	  // MAKE SURE USERNAME IS UNIQUE
	  $lowercase_username = strtolower($username);
    if( $setting['setting_username'] && strtolower($this->user_info['user_username']) != $lowercase_username )
    {
      $username_query = $database->database_query("SELECT user_username FROM se_users WHERE LOWER(user_username)='{$lowercase_username}' LIMIT 1");
      if( $database->database_num_rows($username_query) )
        $this->is_error = 699;
    }
    
	  // MAKE SURE EMAIL IS UNIQUE
	  $lowercase_email = strtolower($email);
    if( strtolower($this->user_info['user_email']) != $lowercase_email )
    {
      $email_query = $database->database_query("SELECT user_email FROM se_users WHERE LOWER(user_email)='{$lowercase_email}' LIMIT 1");  
      if( $database->database_num_rows($email_query) )
        $this->is_error = 700;
    }
	}
  
  // END user_account() METHOD









	// THIS METHOD VALIDATES USER PASSWORD INPUT
	// INPUT: $password_old REPRESENTING THE EXISTING PASSWORD
	//	  $password REPRESENTING THE DESIRED PASSWORD
	//	  $password_confirm REPRESENTING THE PASSWORD CONFIRMATION FIELD
	//	  $check_old (OPTIONAL) REPRESENTING WHETHER THE OLD PASSWORD SHOULD BE VERIFIED OR NOT
	// OUTPUT: 
  
	function user_password($password_old, $password, $password_confirm, $check_old = 1)
  {
	  // CHECK FOR EMPTY PASSWORDS
	  if( !trim($password) || !trim($password_confirm) || ($check_old && !trim($password_old)) )
      $this->is_error = 51;
    
	  // CHECK FOR OLD PASSWORD MATCH
	  if( $check_old && $this->user_password_crypt($password_old) != $this->user_info['user_password'] )
      $this->is_error = 701;
    
	  // MAKE SURE BOTH PASSWORDS ARE IDENTICAL
	  if( $password != $password_confirm )
      $this->is_error = 702;
    
	  // MAKE SURE PASSWORD IS LONGER THAN 5 CHARS
	  if( trim($password) && strlen($password) < 6 )
      $this->is_error = 703;
    
	  // MAKE SURE PASSWORD IS ALPHANUMERIC
	  if( ereg('[^A-Za-z0-9]', $password) )
      $this->is_error = 704;
	}
  
  // END user_password() METHOD









	// THIS METHOD ENCRYPTS A USERS PASsWORD
	// INPUT: UNENCRYPTED PASSWORD
	// OUTPUT: ENCRYPTED PASSWORD
  
	function user_password_crypt($user_password)
  {
    global $setting;
    
    if( !$this->user_exists )
    {
      $method = $setting['setting_password_method'];
      $this->user_salt = randomcode($setting['setting_password_code_length']);
    }
    
    else
    {
      $method = $this->user_info['user_password_method'];
    }
    
    // For new methods
    if( $method>0 )
    {
      if( !empty($this->user_salt) )
      {
        list($salt1, $salt2) = str_split($this->user_salt, ceil(strlen($this->user_salt) / 2));
        $salty_password = $salt1.$user_password.$salt2;
      }
      else
      {
        $salty_password = $user_password;
      }
    }
    
    switch( $method )
    {
      // crypt()
      default:
      case 0:
        $user_password_crypt = crypt($user_password, '$1$'.str_pad(substr($this->user_salt, 0, 8), 8, '0', STR_PAD_LEFT).'$');
      break;
      
      // md5()
      case 1:
        $user_password_crypt = md5($salty_password);
      break;
      
      // sha1()
      case 2:
        $user_password_crypt = sha1($salty_password);
      break;
      
      // crc32()
      case 3:
        $user_password_crypt = sprintf("%u", crc32($salty_password));
      break;
    }
    
    return $user_password_crypt;
  }
  
  // END user_password_crypt() METHOD








	// THIS METHOD RETURNS A SUBNETWORK ID DEPENDENT ON GIVEN INPUTS
	// INPUT: $email (OPTIONAL) REPRESENTING THE USER'S EMAIL 
	//	  $category (OPTIONAL) REPRESENTING THE USER'S PROFILE CATEGORY
	//	  $profile_info (OPTIONAL) REPRESENTING THE USER'S PROFILE INFO
	// OUTPUT: RETURNS AN ARRAY CONTAINING THE SUBNETWORK ID AND RESULT STRINGS
	function user_subnet_select($email = "", $category = "", $profile_info = "")
  {
	  global $database, $datetime, $setting;
    
	  // SET DEFAULTS
	  if( !$email ) $email = $this->user_info['user_email'];
	  if( !$category ) $category = $this->user_info['user_profilecat_id'];
	  if( !$profile_info ) $profile_info = $this->profile_info;
    
    $subnet_id = ( $this->user_info['user_subnet_id'] ? $this->user_info['user_subnet_id'] : 0 );
    
	  // DETERMINE USER'S PRIMARY SUBNETWORK FIELD VALUE
	  $field1_val = "";
	  switch($setting['setting_subnet_field1_id'])
    {
	    case -2: break;
	    case -1: $field1_val = $category; break;
	    case 0: $field1_val = $email; break;
	    default:
	      $field1 = $database->database_query("SELECT profilefield_id AS field_id, profilefield_special AS field_special FROM se_profilefields WHERE profilefield_id='{$setting['setting_subnet_field1_id']}'");
	      if( $database->database_num_rows($field1) )
        {
	        $field1_info = $database->database_fetch_assoc($field1);
	        if( $field1_info['field_special'] == 1 )
          {
	          $field1_val = $datetime->age($profile_info["profilevalue_".$field1_info['field_id']]);
	        }
          else
          {
	          $field1_val = $profile_info["profilevalue_".$field1_info['field_id']];
	        }
	      }
	  }
    
	  // DETERMINE USER'S SECONDARY SUBNETWORK FIELD VALUE
	  $field2_val = "";
	  switch($setting['setting_subnet_field2_id'])
    {
	    case -2: break;
	    case -1: $field2_val = $category; break;
	    case 0: $field2_val = $email; break;
	    default:
	      $field2 = $database->database_query("SELECT profilefield_id AS field_id, profilefield_special AS field_special FROM se_profilefields WHERE profilefield_id='{$setting['setting_subnet_field2_id']}'");
	      if( $database->database_num_rows($field2) )
        {
	        $field2_info = $database->database_fetch_assoc($field2);
	        if($field2_info['field_special'] == 1)
          {
	          $field2_val = $datetime->age($profile_info["profilevalue_".$field2_info['field_id']]);
	        }
          else
          {
	          $field2_val = $profile_info["profilevalue_".$field2_info['field_id']];
	        }
	      }
	  }
    
	  // IF FIELD VALUES NOT EMPTY, RUN QUERY
	  if( $field1_val )
    {
	    // SET NUMERICAL VALUES
	    $field1_val_num = "'{$field1_val}'";
	    $field2_val_num = "'{$field2_val}'";
	    if(is_numeric($field1_val)) { $field1_val_num = str_replace(" ", "", $field1_val); }
	    if(is_numeric($field2_val)) { $field2_val_num = str_replace(" ", "", $field2_val); }
      
	    // SET SUBNETWORK QUERY
	    $subnet_query = "SELECT subnet_id, subnet_name FROM se_subnets WHERE
	    ( 
	      (subnet_field1_qual='==' AND '{$field1_val}' LIKE REPLACE(subnet_field1_value, '*', '%')) OR
	      (subnet_field1_qual='!=' AND '{$field1_val}' NOT LIKE REPLACE(subnet_field1_value, '*', '%')) OR
	      (subnet_field1_qual='>' AND subnet_field1_value<'{$field1_val_num}') OR
	      (subnet_field1_qual='<' AND subnet_field1_value>'{$field1_val_num}') OR
	      (subnet_field1_qual='>=' AND subnet_field1_value<='{$field1_val_num}') OR
	      (subnet_field1_qual='<=' AND subnet_field1_value>='{$field1_val_num}') OR
	      (subnet_field1_qual='' AND subnet_field1_value='')
	    ) AND (
	      (subnet_field2_qual='==' AND '{$field2_val}' LIKE REPLACE(subnet_field2_value, '*', '%')) OR
	      (subnet_field2_qual='!=' AND '{$field2_val}' NOT LIKE REPLACE(subnet_field2_value, '*', '%')) OR
	      (subnet_field2_qual='>' AND subnet_field2_value<'{$field2_val_num}') OR
	      (subnet_field2_qual='<' AND subnet_field2_value>'{$field2_val_num}') OR
	      (subnet_field2_qual='>=' AND subnet_field2_value<='{$field2_val_num}') OR
	      (subnet_field2_qual='<=' AND subnet_field2_value>='{$field2_val_num}') OR
	      (subnet_field2_qual='' AND subnet_field2_value='')
	    ) LIMIT 1";
      
	    // RUN SUBNETWORK QUERY AND FIND USER'S SUBNETWORK ID
	    $subnet = $database->database_query($subnet_query);
	    if( $database->database_num_rows($subnet) )
      { 
	      $subnet_info = $database->database_fetch_assoc($subnet);
	      $subnet_id = $subnet_info['subnet_id']; 
	    }
      else
      {
	      $subnet_id = 0;
	    }
	  }
    
	  // IF SUBNETWORK CHANGED, ADD NOTE
	  if( $subnet_id != $this->user_info['user_subnet_id'] )
    {
      $new_subnet = ( $subnet_id ? $subnet_info['subnet_name'] : 152 );
	  }
    
	  return Array($subnet_id, $new_subnet, $this->subnet_info['subnet_name']);
	}
  
  // END user_subnet_select() METHOD








	// THIS METHOD UPDATES THE USER'S LAST UPDATE DATE
	// INPUT: 
	// OUTPUT: 
  
	function user_lastupdate()
  {
	  global $database;
    
	  $database->database_query("UPDATE se_users SET user_dateupdated='".time()."' WHERE user_id='{$this->user_info['user_id']}' LIMIT 1");
	}
  
  // END user_lastupdate() METHOD








	// THIS METHOD OUTPUTS THE PATH TO THE USER'S PHOTO OR THE GIVEN NOPHOTO IMAGE
	// INPUT: $nophoto_image (OPTIONAL) REPRESENTING THE PATH TO AN IMAGE TO OUTPUT IF NO PHOTO EXISTS
	//	  $thumb (OPTIONAL) REPRESENTING WHETHER TO RETRIEVE THE SQUARE THUMBNAIL OR NOT
	// OUTPUT: A STRING CONTAINING THE PATH TO THE USER'S PHOTO
  
	function user_photo($nophoto_image = "", $thumb = FALSE)
  {
	  global $url;
    
    //if( !$user->user_exists || !$this->user_info['user_photo'] )
    if( !$this->user_info['user_photo'] )
      return $nophoto_image;
    
	  $user_photo = $url->url_userdir($this->user_info['user_id']).$this->user_info['user_photo'];
	  if( $thumb )
    { 
	    $user_thumb = substr($user_photo, 0, strrpos($user_photo, "."))."_thumb".substr($user_photo, strrpos($user_photo, ".")); 
	    if( file_exists($user_thumb) )
        return $user_thumb;
	  }
    
	  if( file_exists($user_photo) )
      return $user_photo;
    
	  return $nophoto_image;
	}
  
  // END user_photo() METHOD



function user_photo2($photo,$user_id,$nophoto_image = "", $thumb = FALSE) {
			global $url;

			$user_photo = $url->url_userdir($user_id).$photo;

			if($thumb) {
			$user_thumb = substr($user_photo, 0, strrpos($user_photo, "."))."_thumb".substr($user_photo, strrpos($user_photo, "."));
			if(file_exists($user_thumb)) { $user_photo = $user_thumb; }
			}

			if(!file_exists($user_photo) || $photo == "") { $user_photo = ""; }
			if($user_photo == "") { $user_photo = $nophoto_image; }
			return $user_photo;
    		}





	// THIS METHOD UPLOADS A USER PHOTO ACCORDING TO SPECIFICATIONS AND RETURNS USER PHOTO
	// INPUT: $photo_name REPRESENTING THE NAME OF THE FILE INPUT
	// OUTPUT: 
  
	function user_photo_upload($photo_name)
  {
	  global $database, $url;
    
	  // ENSURE USER DIRECTORY IS ADDED
	  $user_directory = $url->url_userdir($this->user_info['user_id']);
	  $user_path_array = explode("/", $user_directory);
	  array_pop($user_path_array);
	  array_pop($user_path_array);
	  $subdir = implode("/", $user_path_array)."/";
	  if( !is_dir($subdir) )
    { 
	    mkdir($subdir, 0777); 
	    chmod($subdir, 0777); 
	    $handle = fopen($subdir."index.php", 'x+');
	    fclose($handle);
	  }
    if( !is_dir($user_directory) )
    {
      mkdir($user_directory, 0777);
      chmod($user_directory, 0777);
      $handle = fopen($user_directory."/index.php", 'x+');
      fclose($handle);
    }
    
	  // SET KEY VARIABLES
	  $file_maxsize = "4194304";
	  $file_exts = explode(",", str_replace(" ", "", strtolower($this->level_info['level_photo_exts'])));
	  $file_types = explode(",", str_replace(" ", "", strtolower("image/jpeg, image/jpg, image/jpe, image/pjpeg, image/pjpg, image/x-jpeg, x-jpg, image/gif, image/x-gif, image/png, image/x-png")));
	  $file_maxwidth = $this->level_info['level_photo_width'];
	  $file_maxheight = $this->level_info['level_photo_height'];
	  $photo_newname = "0_".rand(1000, 9999).".jpg";
	  $file_dest = $url->url_userdir($this->user_info['user_id']).$photo_newname;
	  $thumb_dest = substr($file_dest, 0, strrpos($file_dest, "."))."_thumb".substr($file_dest, strrpos($file_dest, "."));
    
	  $new_photo = new se_upload();
	  $new_photo->new_upload($photo_name, $file_maxsize, $file_exts, $file_types, $file_maxwidth, $file_maxheight);
    
	  // UPLOAD AND RESIZE PHOTO IF NO ERROR
	  if( !$new_photo->is_error )
    {
	    // DELETE OLD AVATAR IF EXISTS
	    $this->user_photo_delete();
      
	    // UPLOAD THUMB
	    $new_photo->upload_thumb($thumb_dest);
      
	    // CHECK IF IMAGE RESIZING IS AVAILABLE, OTHERWISE MOVE UPLOADED IMAGE
	    if( $new_photo->is_image )
      {
	      $new_photo->upload_photo($file_dest);
	    }
      else
      {
	      $new_photo->upload_file($file_dest);
	    }
      
	    // UPDATE USER INFO WITH IMAGE IF STILL NO ERROR
	    if( !$new_photo->is_error )
      {
	      $database->database_query("UPDATE se_users SET user_photo='{$photo_newname}' WHERE user_id='{$this->user_info['user_id']}' LIMIT 1");
	      $this->user_info['user_photo'] = $photo_newname;
	    }
	  }
    
	  $this->is_error = $new_photo->is_error;
	}
  
  // END user_photo_upload() METHOD








	// THIS METHOD DELETES A USER PHOTO
	// INPUT: 
	// OUTPUT: 
  
	function user_photo_delete()
  {
	  global $database;
	  $user_photo = $this->user_photo();
	  if( $user_photo )
    {
	    @unlink($user_photo);
	    @unlink(substr($user_photo, 0, strrpos($user_photo, "."))."_thumb".substr($user_photo, strrpos($user_photo, ".")));
	    $database->database_query("UPDATE se_users SET user_photo='' WHERE user_id='{$this->user_info['user_id']}' LIMIT 1");
	    $this->user_info['user_photo'] = NULL;
	  }
	}
  
  // END user_photo_delete() METHOD








	// THIS METHOD RETURNS THE TOTAL NUMBER OF FRIENDS
	// INPUT: $direction (OPTIONAL) REPRESENTING A "0" FOR OUTGOING CONNECTIONS AND A "1" FOR INCOMING CONNECTIONS
	//	  $friend_status (OPTIONAL) REPRESENTING THE FRIEND STATUS (1 FOR CONFIRMED, 0 FOR PENDING REQUESTS)
	//	  $user_details (OPTIONAL) REPRESENTING WHETHER THE QUERY SHOULD JOIN TO THE USER TABLE OR NOT
	//	  $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
	// OUTPUT: AN INTEGER REPRESENTING THE NUMBER OF FRIENDS
  
	function user_friend_total($direction = 0, $friend_status = 1, $user_details = 0, $where = "")
  {
	  global $database, $setting;
    
    if( !$setting['setting_connection_allow'] )
      return 0;
    
    // BEGIN FRIEND QUERY
    $friend_query = "
      SELECT
        NULL
      FROM
        se_friends
    ";
    
    // JOIN TO FRIEND TABLE IF NECESSARY
    if( $user_details ) $friend_query .= "
      LEFT JOIN
        se_users
        ON ";
    
    if( $user_details && $direction==1 )
      $friend_query .= "se_friends.friend_user_id1=se_users.user_id ";
    elseif( $user_details )
      $friend_query .= "se_friends.friend_user_id2=se_users.user_id ";
    
    // CONTINUE QUERY
    $friend_query .= "
      WHERE
        friend_status='{$friend_status}'
    ";
    
    // EITHER "LIST OF WHO USER IS A FRIEND OF" OR "LIST OF USER'S FRIENDS"
    if( $direction == 1 ) $friend_query .= " &&
        friend_user_id2='{$this->user_info['user_id']}'
    ";
    
    if( $direction != 1 ) $friend_query .= " &&
        friend_user_id1='{$this->user_info['user_id']}'
    ";
    
    // ADD ADDITIONAL WHERE CLAUSE IF EXISTS
    if( $where ) $friend_query .= " &&
        {$where}
    ";
    
	  return (int) $database->database_num_rows($database->database_query($friend_query));
	}
  
  // END user_friend_total() METHOD








	// THIS METHOD RETURNS AN ARRAY OF USER'S FRIENDS
	// INPUT: $start REPRESENTING THE FRIEND TO START WITH
	//	  $limit REPRESENTING THE NUMBER OF FRIENDS TO RETURN
	//	  $direction (OPTIONAL) REPRESENTING A "0" FOR OUTGOING CONNECTIONS AND A "1" FOR INCOMING CONNECTIONS
	//	  $friend_status (OPTIONAL) REPRESENTING THE FRIEND STATUS (1 FOR CONFIRMED, 0 FOR PENDING REQUESTS)
	//	  $sort_by (OPTIONAL) REPRESENTING THE ORDER BY CLAUSE
	//	  $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
	//	  $friend_details (OPTIONAL) REPRESENTING A BOOLEAN THAT DETERMINES WHETHER OR NOT TO RETRIEVE THE "FRIEND TYPE" AND "FRIEND EXPLANATION"
	// OUTPUT: AN ARRAY OF THE USER'S FRIENDS
  
	function user_friend_list($start, $limit, $direction = 0, $friend_status = 1, $sort_by = "se_users.user_dateupdated DESC", $where = "", $friend_details = 0, $other_user_id = 0)
  {
	  global $database, $setting, $user;
    
    if( !$other_user_id && $user->user_info['user_id'] != $this->user_info['user_id'] )
    {
      $other_user_id = $user->user_info['user_id'];
    }
    
	  // SET VARIABLE
	  $friend_array = Array();
    
	  // MAKE SURE CONNECTIONS ARE ALLOWED
	  if( $setting['setting_connection_allow'] )
    {
	    // BEGIN FRIEND QUERY
	    $friend_query = "
        SELECT 
          se_friends.friend_id, 
          se_users.user_id, 
          se_users.user_username, 
          se_users.user_fname,
          se_users.user_lname,
          se_users.user_photo, 
          se_users.user_lastlogindate, 
          se_users.user_dateupdated
      ";
      
      if( $other_user_id )
      {
        $friend_query .= ",
          CASE
            WHEN (SELECT TRUE FROM se_friends WHERE friend_user_id1='{$other_user_id}' AND friend_user_id2=se_users.user_id AND friend_status='1' LIMIT 1)
              THEN 2
            WHEN (SELECT TRUE FROM se_friends WHERE friend_user_id1='{$other_user_id}' AND friend_user_id2=se_users.user_id AND friend_status='0' LIMIT 1)
              THEN 1
            ELSE 0
          END
          AS is_viewers_friend
        ";
      }
      
        $friend_query .= ",
          CASE
            WHEN (SELECT TRUE FROM se_users AS se_users2 WHERE se_users2.user_id=se_users.user_id AND (user_blocklist LIKE '{$this->user_info['user_id']},%' OR user_blocklist LIKE '%,{$this->user_info['user_id']}' OR user_blocklist LIKE '%,{$this->user_info['user_id']},%') LIMIT 1)
              THEN TRUE
            ELSE FALSE
          END
          AS is_viewers_blocklisted
      ";
      
	    // GET FRIEND EXPLAIN, IF NECESSARY
	    if( $friend_details ) $friend_query .= ",
          se_friends.friend_type,
          se_friendexplains.friendexplain_body
      ";
      
	    // CONTINUE QUERY
	    $friend_query .= "
        FROM
          se_friends
        LEFT JOIN
          se_users ON
      ";
      
	    // MAKE SURE TO JOIN ON THE CORRECT FIELD (DEPENDENT ON DIRECTION)
	    if( $direction == 1 ) $friend_query .= "
        se_friends.friend_user_id1=se_users.user_id
      ";
      
	    if( $direction != 1 ) $friend_query .= "
        se_friends.friend_user_id2=se_users.user_id
      ";
      
	    // JOIN ON FRIEND EXPLAIN TABLE, IF NECESSARY
	    if( $friend_details ) $friend_query .= "
        LEFT JOIN
          se_friendexplains
          ON se_friends.friend_id=se_friendexplains.friendexplain_friend_id
      ";
      
	    // CONTINUE QUERY
	    $friend_query .= "
        WHERE
          friend_status='{$friend_status}'
      ";
      
	    // EITHER "LIST OF WHO USER IS A FRIEND OF" OR "LIST OF USER'S FRIENDS"
	    if( $direction == 1 ) $friend_query .= " &&
        friend_user_id2='{$this->user_info['user_id']}'
      ";
      
	    if( $direction != 1 ) $friend_query .= " &&
        friend_user_id1='{$this->user_info['user_id']}'
      ";
      
	    // ADD ADDITIONAL WHERE CLAUSE IF EXISTS
	    if( $where ) $friend_query .= " &&
          {$where}
      ";
      
	    // SET SORT	AND LIMIT
	    $friend_query .= "
        ORDER BY
          {$sort_by}
        LIMIT
          {$start}, {$limit}
      ";
      
	    // LOOP OVER FRIENDS
	    $friends = $database->database_query($friend_query);
	    while($friend_info = $database->database_fetch_assoc($friends))
      {
	      // CREATE AN OBJECT FOR FRIEND
	      $friend = new SEUser();
	      $friend->user_info['user_id'] = $friend_info['user_id'];
	      $friend->user_info['user_username'] = $friend_info['user_username'];
	      $friend->user_info['user_fname'] = $friend_info['user_fname'];
	      $friend->user_info['user_lname'] = $friend_info['user_lname'];
	      $friend->user_info['user_photo'] = $friend_info['user_photo'];
	      $friend->user_info['user_lastlogindate'] = $friend_info['user_lastlogindate'];
	      $friend->user_info['user_dateupdated'] = $friend_info['user_dateupdated'];
	      $friend->is_viewers_friend = @$friend_info['is_viewers_friend'];
	      $friend->is_viewers_blocklist = @$friend_info['is_viewers_blocklist'];
	      $friend->user_displayname();
        
	      // SET FRIEND TYPE/EXPLANATION VARS
	      if( $friend_details )
        {
	        $friend->friend_type = $friend_info['friend_type'];
	        $friend->friend_explain = $friend_info['friendexplain_body'];
	      }
        
	      // SET FRIEND ARRAY
	      $friend_array[] = $friend;
	    }
	  }
    
	  // RETURN FRIEND ARRAY
	  return $friend_array;
	}
  
  // END user_friend_list() METHOD








	// THIS METHOD ADDS A USER AS A FRIEND OF THE CURRENT USER
	// INPUT: $other_user_id REPRESENTING THE USER ID OF THE FRIEND TO BE ADDED
	//	  $friend_status REPRESENTING WHETHER THE FRIENDSHIP IS CONFIRMED OR NOT
	//	  $friend_type REPRESENTING A STRING WITH THE TYPE OF FRIEND
	//	  $friend_explain REPRESENTING A TEXTUAL EXPLANATION OF THE FRIENDSHIP
	// OUTPUT:
  
	function user_friend_add($other_user_id, $friend_status, $friend_type, $friend_explain)
  {
	  global $database;
    
	  // CHECK EXISTANCE OF FRIENDSHIP
	  if( $database->database_num_rows($database->database_query("SELECT TRUE FROM se_friends WHERE friend_user_id1='{$this->user_info['user_id']}' AND friend_user_id2='{$other_user_id}' LIMIT 1")) )
      return;

	  // ADD USER TO FRIENDS
	  $database->database_query("
      INSERT INTO se_friends
        (friend_user_id1, friend_user_id2, friend_status, friend_type)
      VALUES
        ('{$this->user_info['user_id']}', '{$other_user_id}', '{$friend_status}', '{$friend_type}'
      )
    ");
	  $friend_id = $database->database_insert_id();
    
	  $database->database_query("
      INSERT INTO se_friendexplains
        (friendexplain_friend_id, friendexplain_body)
      VALUES
        ('{$friend_id}', '{$friend_explain}')
    ");
    
	  // REMOVE FRIEND FROM BLOCKLIST
	  if( $this->user_blocked($other_user_id) )
    {
	    $blocklist = explode(",", $this->user_info['user_blocklist']);
	    $user_key = array_search($other_user_id, $blocklist);
      $blocklist[$user_key] = "";
	    $this->user_info['user_blocklist'] = implode(",", $blocklist);
	    $database->database_query("UPDATE se_users SET user_blocklist='{$this->user_info['user_blocklist']}' WHERE user_id='{$this->user_info['user_id']}' LIMIT 1");
	  }
	}
  
  // END user_friend_add() METHOD








	// THIS METHOD REMOVES A USER AS A FRIEND OF THE CURRENT USER
	// INPUT: $other_user_id REPRESENTING THE FRIEND'S USER ID
	// OUTPUT: 
  
	function user_friend_remove($other_user_id)
  {
	  global $database, $setting;
    
    // REMOVE IF FRIEND
    $friend1 = $database->database_query("SELECT friend_id FROM se_friends WHERE friend_user_id1='{$this->user_info['user_id']}' AND friend_user_id2='{$other_user_id}'");
    if( $database->database_num_rows($friend1) )
    {
      $friendship = $database->database_fetch_assoc($friend1);
      $database->database_query("DELETE FROM se_friends WHERE friend_id='{$friendship['friend_id']}' LIMIT 1");
      $database->database_query("DELETE FROM se_friendexplains WHERE friendexplain_friend_id='{$friendship['friend_id']}' LIMIT 1");
    }
    
    // REMOVE ADDITIONAL ROW IF TWO-DIRECTIONAL
    $friend2 = $database->database_query("SELECT friend_id FROM se_friends WHERE friend_user_id2='{$this->user_info['user_id']}' AND friend_user_id1='{$other_user_id}'");
    if( $database->database_num_rows($friend2) && ($setting['setting_connection_framework'] == 0 || $setting['setting_connection_framework'] == 2) )
    {
      $friendship = $database->database_fetch_assoc($friend2);
      $database->database_query("DELETE FROM se_friends WHERE friend_id='{$friendship['friend_id']}' LIMIT 1");
      $database->database_query("DELETE FROM se_friendexplains WHERE friendexplain_friend_id='{$friendship['friend_id']}' LIMIT 1");    
    }
	}
  
  // END user_friend_remove() METHOD








	// THIS METHOD RETURNS TRUE IF THE SPECIFIED USER IS A FRIEND OF A FRIEND OF THE EXISTING USER IN THIS CLASS
	// INPUT: $other_user_id REPRESENTING A USER'S USER ID
	// OUTPUT: RETURNS A BOOLEAN REPRESENTING WHETHER THE SPECIFIED USER IS A FRIEND OF A FRIEND OR NOT
  
	function user_friend_of_friend($other_user_id)
  {
	  global $database;
    
    $resource = $database->database_query("
      SELECT
        t2.friend_user_id2
      FROM
        se_friends AS t1
      LEFT JOIN
        se_friends AS t2
        ON t1.friend_user_id2=t2.friend_user_id1
      WHERE
        t1.friend_user_id1='{$this->user_info['user_id']}' &&
        t2.friend_user_id2='{$other_user_id}' &&
        t1.friend_status<>'0' &&
        t2.friend_status<>'0'
    ");
    
    return (bool) $database->database_num_rows($resource);
	}
  
  // END user_friend_of_friend() METHOD








	// THIS METHOD RETURNS TRUE IF THE SPECIFIED USER HAS BEEN FRIENDED BY THE EXISTING USER IN THIS CLASS
	// INPUT: $other_user_id REPRESENTING A USER'S USER ID
	//	  $friend_status (OPTIONAL) REPRESENTING WHETHER THE FRIENDSHIP IS CONFIRMED OR NOT
	// OUTPUT: RETURNS A BOOLEAN REPRESENTING WHETHER THE SPECIFIED USER IS FRIENDED OR NOT
  
	function user_friended($other_user_id, $friend_status = 1)
  {
	  global $database;
    
    $resource = $database->database_query("
      SELECT
        friend_id
      FROM
        se_friends
      WHERE
        friend_user_id1='{$this->user_info['user_id']}' &&
        friend_user_id2='{$other_user_id}' &&
        friend_status='{$friend_status}'
    ");
    
    return (bool) $database->database_num_rows($resource);
	}
  
  // END user_friended() METHOD








	// THIS METHOD RETURNS TRUE IF THE SPECIFIED USER HAS BEEN BLOCKED BY THE EXISTING USER IN THIS CLASS
	// INPUT: $other_user_id REPRESENTING A USER'S USER ID
	// OUTPUT: RETURNS A BOOLEAN REPRESENTING WHETHER THE SPECIFIED USER IS BLOCKED OR NOT
  
	function user_blocked($other_user_id)
  {
    if( isset($this->level_info['level_profile_block']) && !$this->level_info['level_profile_block'] )
    {
      return false;
    }
    
    if( !$this->user_info['user_blocklist'] )
    {
      return false;
    }
    
	  $blocklist = explode(",", $this->user_info['user_blocklist']);
    return in_array($other_user_id, $blocklist);
	}
  
  // END user_blocked() METHOD








	// THIS METHOD RETURNS MAXIMUM PRIVACY LEVEL VIEWABLE BY A USER WITH REGARD TO THE CURRENT USER
	// INPUT: $other_user REPRESENTING A ANOTHER USER OBJECT
	// OUTPUT: RETURNS PRIVACY LEVEL OF GIVEN USER WITH RESPECT TO CURRENT USER
  
	function user_privacy_max($other_user)
  {
	  global $database;
    
	  // UNREGISTERED USER
	  if( !$other_user->user_exists )
      return 32;
    
	  switch(TRUE)
    {
	    // OWNER
	    case( $this->user_info['user_id'] == $other_user->user_info['user_id'] ):
	      return 1;
	      break;
      
	    // FRIEND
	    case( $this->user_friended($other_user->user_info['user_id']) ):
	      return 2;
	      break;
      
	    // FRIEND OF FRIEND WITHIN SAME SUBNETWORK
	    case( $this->user_info['user_subnet_id'] == $other_user->user_info['user_subnet_id'] && $this->user_friend_of_friend($other_user->user_info['user_id']) ):
	      return 4;
	      break;
      
	    // SAME SUBNETWORK
	    case( $this->user_info['user_subnet_id'] == $other_user->user_info['user_subnet_id'] ):
	      return 8;
	      break;
      
	    // REGISTERED USER
	    case( $other_user->user_exists ):
	      return 16;
	      break;
      
	    // DEFAULT EVERYONE
	    default:
	      return 32;
	  }
	}
  
  // END user_privacy_max() METHOD








	// THIS METHOD CREATES A USER ACCOUNT USING THE GIVEN INFORMATION
	// INPUT: $signup_email REPRESENTING THE DESIRED EMAIL
	//	  $signup_username REPRESENTING THE DESIRED USERNAME
	//	  $signup_password REPRESENTING THE DESIRED PASSWORD
	//	  $signup_timezone REPRESENTING THE USER'S TIMEZONE
	//	  $signup_language REPRESENTING THE USER'S SELECTED LANGUAGE
	//	  $signup_cat REPRESENTING THE USER'S SELECTED PROFILE CATEGORY
	//	  $profile_field_query REPRESENTING THE PARTIAL QUERY TO SAVE IN THE USER'S PROFILE VALUE TABLE
	// OUTPUT: 
  
	function user_create($signup_email, $signup_username, $signup_password, $signup_timezone, $signup_language, $signup_cat, $profile_field_query)
  {
	  global $database, $setting, $url, $actions, $field;
    
	  // PRESET VARS
	  $signup_subnet_id = 0;
	  $signup_level_info = $database->database_fetch_assoc($database->database_query("SELECT level_id, level_profile_privacy, level_profile_comments FROM se_levels WHERE level_default='1' LIMIT 1"));
	  $signup_date = time();
	  $signup_dateupdated = $signup_date;
	  $signup_invitesleft = $setting['setting_signup_invite_numgiven'];
	  $signup_notify_friendrequest = 1;
	  $signup_notify_message = 1;
	  $signup_notify_profilecomment = 1;
	  $signup_profile_search = 1;
	  $signup_ip = $_SERVER['REMOTE_ADDR'];
    
	  // SET SIGNUP_USERNAME TO A PLACEHOLDER IF USERNAMES ARE NOT BEING USED
	  if( !$setting['setting_username'] ) $signup_username = randomcode(15);
    
	  // SET WHETHER USER IS ENABLED OR NOT
    $signup_enabled = (bool) $setting['setting_signup_enable'];
    
	  // SET EMAIL VERIFICATION VARIABLE
    $signup_verified = !$setting['setting_signup_verify'];
    
	  // CREATE RANDOM PASSWORD IF NECESSARY
	  if( $setting['setting_signup_randpass'] ) $signup_password = randomcode(10);
    
	  // ENCODE PASSWORD WITH MD5
	  $crypt_password = $this->user_password_crypt($signup_password);
    $signup_code = $user_salt = $this->user_salt;
    
	  // SET PRIVACY DEFAULT
	  $allowable_privacy = unserialize($signup_level_info['level_profile_privacy']);
	  rsort($allowable_privacy);
	  $profile_privacy = $allowable_privacy[0];
    
	  // SET COMMENT DEFAULT
	  $allowable_comments = unserialize($signup_level_info['level_profile_comments']);
	  rsort($allowable_comments);
	  $profile_comments = $allowable_comments[0];
    
	  // ADD USER TO USER TABLE
	  $database->database_query("
      INSERT INTO se_users (
        user_level_id,
        user_profilecat_id,
        user_email,
        user_newemail,
        user_username,
        user_password,
        user_password_method,
        user_code,
        user_enabled,
        user_verified,
        user_signupdate,
        user_invitesleft,
        user_timezone,
        user_language_id,
        user_dateupdated,
        user_search,
        user_privacy,
        user_comments,
        user_ip_signup,
        user_ip_lastactive
      ) VALUES (
        '{$signup_level_info['level_id']}',
        '{$signup_cat}',
        '{$signup_email}',
        '{$signup_email}',
        '{$signup_username}',
        '{$crypt_password}',
        '{$setting['setting_password_method']}',
        '{$signup_code}',
        '{$signup_enabled}',
        '{$signup_verified}',
        '{$signup_date}',
        '{$signup_invitesleft}',
        '{$signup_timezone}',
        '{$signup_language}',
        '{$signup_dateupdated}',
        '{$signup_profile_search}',
        '{$profile_privacy}',
        '{$profile_comments}',
        '{$signup_ip}',
        '{$signup_ip}'
      )
    ");
    
	  // RETRIEVE USER ID
	  $user_id = $database->database_insert_id();
    
    if( $user_id ) $this->user_exists = TRUE;
    
	  // UPDATE USERNAME IF NECESSARY
	  if( !$setting['setting_username'] )
      $database->database_query("UPDATE se_users SET user_username=user_id WHERE user_id='{$user_id}' LIMIT 1");
    
	  // GET USER INFO
	  $this->user_info = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_users WHERE user_id='{$user_id}' LIMIT 1"));
	  $this->level_info = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_levels WHERE level_id='{$this->user_info['user_level_id']}' LIMIT 1"));
	  $this->subnet_info = $database->database_fetch_assoc($database->database_query("SELECT subnet_id, subnet_name FROM se_subnets WHERE subnet_id='{$this->user_info['user_subnet_id']}' LIMIT 1"));
    
	  // ADD USER PROFILE
	  $database->database_query("INSERT INTO se_profilevalues (profilevalue_user_id) VALUES ('{$this->user_info['user_id']}')");
	  if( $profile_field_query )
	    $database->database_query("UPDATE se_profilevalues SET $profile_field_query WHERE profilevalue_user_id='{$this->user_info['user_id']}' LIMIT 1");
	  
	  // GET PROFILE INFO
	  $this->profile_info = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_profilevalues WHERE profilevalue_user_id='{$this->user_info['user_id']}' LIMIT 1"));
    
	  // GET SUBNET ID
	  $signup_subnet = $this->user_subnet_select($signup_email, $signup_cat, $this->profile_info); 
	  $signup_subnet_id = $signup_subnet[0];
	  $database->database_query("UPDATE se_users SET user_subnet_id='{$signup_subnet_id}' WHERE user_id='{$user_id}' LIMIT 1");
	  $this->user_info['user_subnet_id'] = $signup_subnet_id;
    
	  // ADD ROW IN STYLES TABLE
	  $database->database_query("INSERT INTO se_profilestyles (profilestyle_user_id, profilestyle_css) VALUES ('{$this->user_info['user_id']}', '')");
    
	  // ADD ROW IN SETTINGS TABLE
	  $actiontypes = $database->database_query("SELECT actiontype_id FROM se_actiontypes");
	  $action_ids = Array();
	  while( $actiontype = $database->database_fetch_assoc($actiontypes) )
      $action_ids[] = $actiontype['actiontype_id'];
    
	  $database->database_query("
      INSERT INTO se_usersettings (
        usersetting_user_id,
        usersetting_notify_friendrequest,
        usersetting_notify_message,
        usersetting_notify_profilecomment,
        usersetting_actions_display
      ) VALUES (
        '{$this->user_info['user_id']}',
        '{$signup_notify_friendrequest}',
        '{$signup_notify_message}',
        '{$signup_notify_profilecomment}',
        '".implode(",", $action_ids)."'
      )
    ") or die($database->database_error());
    
	  // ADD USER DIRECTORY
	  $user_directory = $url->url_userdir($this->user_info['user_id']);
	  $user_path_array = explode("/", $user_directory);
	  array_pop($user_path_array);
	  array_pop($user_path_array);
	  $subdir = implode("/", $user_path_array)."/";
	  if( !is_dir($subdir) )
    { 
	    mkdir($subdir, 0777); 
	    chmod($subdir, 0777); 
	    $handle = fopen($subdir."index.php", 'x+');
	    fclose($handle);
	  }
    if( !is_dir($user_directory) )
    {
      mkdir($user_directory, 0777);
      chmod($user_directory, 0777);
      $handle = fopen($user_directory."/index.php", 'x+');
      fclose($handle);
    }
    
	  // SAVE FIRST/LAST NAME, IF RELEVANT
	  if( trim($field->field_special[2]) )
    {
      $flquery[] = "user_fname='".$field->field_special[2]."'";
      $this->user_info['user_fname'] = $field->field_special[2];
    }
	  if( trim($field->field_special[3]) )
    {
      $flquery[] = "user_lname='".$field->field_special[3]."'";
      $this->user_info['user_lname'] = $field->field_special[3];
    }
	  if( !empty($flquery) )
    {
      $database->database_query("UPDATE se_users SET ".implode(", ", $flquery)." WHERE user_id='{$this->user_info['user_id']}'");
      $this->user_displayname_update($field->field_special[2], $field->field_special[3]);
    }
    
	  // SET DISPLAY NAME
	  $this->user_displayname();
    
	  // CALL SIGNUP HOOK
	  ($hook = SE_Hook::exists('se_signup_success')) ? SE_Hook::call($hook, array()) : NULL;
    
	  // SEND RANDOM PASSWORD IF NECESSARY
	  if( $setting['setting_signup_randpass'] )
    {
      send_systememail('newpassword', $this->user_info['user_email'], Array($this->user_displayname, $this->user_info['user_email'], $signup_password, "<a href=\"".$url->url_base."login.php\">".$url->url_base."login.php</a>"));
    }
    
	  // SEND VERIFICATION EMAIL IF REQUIRED
	  if( $setting['setting_signup_verify'] )
    {
	    $verify_code = md5($this->user_info['user_code']);
	    $time = time();
	    $verify_link = $url->url_base."signup_verify.php?u={$this->user_info['user_id']}&verify={$verify_code}&d={$time}";
	    send_systememail('verification', $this->user_info['user_email'], Array($this->user_displayname, $this->user_info['user_email'], "<a href=\"$verify_link\">$verify_link</a>")); 
    }
    
    // INSERT ACTION IF VERIFICATION NOT NECESSARY
    else
    {
      $actions->actions_add($this, "signup", Array($this->user_info['user_username'], $this->user_displayname), Array(), 0, false, "user", $this->user_info['user_id'], $this->user_info['user_privacy']);
    }
    
	  // SEND WELCOME EMAIL IF REQUIRED (AND IF VERIFICATION EMAIL IS NOT BEING SENT)
	  if( $setting['setting_signup_welcome'] && !$setting['setting_signup_verify'] )
    {
      send_systememail('welcome', $this->user_info['user_email'], Array($this->user_displayname, $this->user_info['user_email'], $signup_password, "<a href=\"".$url->url_base."login.php\">".$url->url_base."login.php</a>"));
    }
	}
  
  // END user_create() METHOD









	// THIS METHOD DELETES THE USER CURRENTLY ASSOCIATED WITH THIS OBJECT
	// INPUT: 
	// OUTPUT:
	function user_delete()
  {
	  global $database, $url, $global_plugins;
    
	  // CALL USER DELETE HOOK
	  ($hook = SE_Hook::exists('se_user_delete')) ? SE_Hook::call($hook, $this->user_info['user_id']) : NULL;
    
	  // DELETE USER, USERSETTING, PROFILE, STYLES TABLE ROWS
	  $database->database_query("DELETE FROM se_users WHERE user_id='{$this->user_info['user_id']}' LIMIT 1");
	  $database->database_query("DELETE FROM se_usersettings WHERE usersetting_user_id='{$this->user_info['user_id']}' LIMIT 1");
	  $database->database_query("DELETE FROM se_profilevalues WHERE profilevalue_user_id='{$this->user_info['user_id']}' LIMIT 1");
	  $database->database_query("DELETE FROM se_profilestyles WHERE profilestyle_user_id='{$this->user_info['user_id']}' LIMIT 1");
    
	  // DELETE USER-OWNED AND PROFILE COMMENTS
	  $database->database_query("DELETE FROM se_profilecomments WHERE profilecomment_user_id='{$this->user_info['user_id']}'");
    
    // DELETE NOTIFICATIONS SENT TO OTHER USERS FOR A PM THEY SENT
    $database->database_query("DELETE se_notifys.* FROM se_pmconvoops LEFT JOIN se_notifys ON se_notifys.notify_object_id=se_pmconvoops.pmconvoop_pmconvo_id WHERE se_notifys.notify_notifytype_id=2 && se_pmconvoops.pmconvoop_user_id='{$this->user_info['user_id']}'");
    
	  // DELETE PMCONVOS AND PMS WHERE THE DELETED USER AND THE OTHER USER ARE THE ONLY TWO INSIDE, OR WHERE THE DELETED USER WAS THE INITIAL SENDER
    $database->database_query("UPDATE se_pmconvos LEFT JOIN se_pmconvoops ON pmconvoop_pmconvo_id=pmconvo_id SET pmconvo_recipients=pmconvo_recipients-1 WHERE pmconvoop_user_id='{$this->user_info['user_id']}'");
    $database->database_query("UPDATE se_pmconvos LEFT JOIN se_pmconvoops ON pmconvoop_pmconvo_id=pmconvo_id SET pmconvo_recipients=0 WHERE pmconvoop_user_id='{$this->user_info['user_id']}' && pmconvoop_user_id=(SELECT pm_authoruser_id FROM se_pms WHERE pm_pmconvo_id=pmconvo_id ORDER BY pm_id ASC)");
    $database->database_query("DELETE FROM se_pmconvoops WHERE pmconvoop_user_id='{$this->user_info['user_id']}'");
    
    // THIS MAY ALSO DELETE OTHER CONVOS THAT WERE PARTIALLY REMOVED
    $database->database_query("DELETE se_pms.*, se_pmconvos.*, se_pmconvoops.* FROM se_pmconvos LEFT JOIN se_pms ON pm_pmconvo_id=pmconvo_id LEFT JOIN se_pmconvoops ON pmconvoop_pmconvo_id=pmconvo_id WHERE pmconvo_recipients<2");
    
    // DELETE CONNECTIONS TO AND FROM USER
	  $database->database_query("DELETE FROM se_friends, se_friendexplains USING se_friends LEFT JOIN se_friendexplains ON se_friends.friend_id=se_friendexplains.friendexplain_friend_id WHERE se_friends.friend_user_id1='{$this->user_info['user_id']}' OR se_friends.friend_user_id2='{$this->user_info['user_id']}'");
	  
    // DELETE ALL OF THIS USER'S REPORTS
	  $database->database_query("DELETE FROM se_reports WHERE report_user_id='{$this->user_info['user_id']}'");
	  
    // DELETE USER ACTIONS
	  $database->database_query("DELETE FROM se_actions, se_actionmedia USING se_actions LEFT JOIN se_actionmedia ON se_actions.action_id=se_actionmedia.actionmedia_action_id WHERE action_user_id='{$this->user_info['user_id']}'");
	  
    // DELETE USER NOTIFICATIONS
	  $database->database_query("DELETE FROM se_notifys WHERE notify_user_id='{$this->user_info['user_id']}'");
    
	  // DELETE NOTIFICATIONS BY USER
	  $database->database_query("DELETE FROM se_notifys WHERE notify_notifytype_id=1 AND notify_object_id='{$this->user_info['user_id']}'");

	  // DELETE USER'S FILES
	  if( is_dir($url->url_userdir($this->user_info['user_id'])) )
	    $dir = $url->url_userdir($this->user_info['user_id']);
	  else
	    $dir = ".".$url->url_userdir($this->user_info['user_id']);
    
	  if( $dh = @opendir($dir) )
    {
	    while( ($file = @readdir($dh)) !== false )
      {
	      if( $file != "." && $file != ".." )
        {
	        @unlink($dir.$file);
	      }
	    }
	    @closedir($dh);
	  }
	  @rmdir($dir);
    
	  $this->user_clear();
	}
  
  // END user_delete() METHOD







  
  //
	// THIS METHOD RETURNS THE TOTAL NUMBER OF MESSAGES
  //
	// INPUT:
  //    $direction    (OPTIONAL) REPRESENTING A "0" FOR MESSAGES SENT TO USER AND "1" FOR MESSAGES SENT BY USER
	//	  $unread_only  (OPTIONAL) REPRESENTING A "0" FOR ALL MESSAGES AND A "1" FOR UNREAD MESSAGES ONLY
  //
	// OUTPUT:
  //    AN INTEGER REPRESENTING THE NUMBER OF MESSAGES
  //
  
  function user_message_total($direction=0, $unread_only=FALSE, $where=NULL, $do_joins=FALSE)
  {
    global $database;
    
    $message_total = 0;
    
	  // MAKE SURE MESSAGES ARE ALLOWED
	  if( empty($this->level_info['level_message_allow']) )
      return FALSE;
    
    // BEGIN MESSAGE QUERY
    $sql = "
      SELECT
        COUNT(pmconvoop_id) as pm_total
      FROM
        se_pmconvoops
    ";
    
    // JOIN TO PM AND PMCONVO TABLES
    if( $do_joins ) $sql .= "
      LEFT JOIN
        se_pmconvos
        ON se_pmconvos.pmconvo_id=se_pmconvoops.pmconvoop_pmconvo_id
      LEFT JOIN
        se_pms
        ON se_pms.pm_pmconvo_id=se_pmconvoops.pmconvoop_pmconvo_id
    ";
    
    $sql .= "
      WHERE
        se_pmconvoops.pmconvoop_user_id='{$this->user_info['user_id']}'
    ";
    
    // INCOMING MESSAGES
    if( !$direction ) $sql .= " &&
        se_pmconvoops.pmconvoop_deleted_inbox=0
    ";
    
    // OUTGOING MESSAGES
    if(  $direction ) $sql .= " &&
        /*
        THIS IS REMOVED BECAUSE I AM HOPING THE deleted_outbox WILL HANDLE IT
        se_pms.pm_authoruser_id='{$this->user_info['user_id']}' &&
        */
        se_pmconvoops.pmconvoop_deleted_outbox=0
    ";
    
    // READ ONLY
    if( $unread_only ) $sql .= " &&
        se_pmconvoops.pmconvoop_read=0
    ";
    
    // ADD WHERE
    if( $where ) $sql .= " &&
        {$where}
    ";
    
    // ADD GROUP BY IF JOINING
    if( $do_joins ) $sql .= "
      GROUP BY
        se_pmconvoops.pmconvoop_pmconvo_id
    ";
    
    // RUN QUERY AND RETURN
    $resource = $database->database_query($sql);
    $result = $database->database_fetch_assoc($resource);
    
    //return (int) $database->database_num_rows($resource);
    return (int) $result['pm_total'];
  }
  
  // END user_message_total() METHOD







  
  //
	// THIS METHOD RETURNS AN ARRAY OF USER'S MESSAGES
  //
	// INPUT:
  //    $start      REPRESENTING THE MESSAGE TO START WITH
	//	  $limit      REPRESENTING THE NUMBER OF MESSAGES TO RETURN
	//	  $direction  (OPTIONAL) REPRESENTING A "0" FOR MESSAGES SENT TO USER AND "1" FOR MESSAGES SENT BY USER
	//	  $where      (OPTIONAL)
  //
	// OUTPUT:
  //    AN ARRAY OF THE USER'S MESSAGES
  //
  
  function &user_message_list($start=NULL, $limit=NULL, $direction=0, $where=NULL)
  {
    global $database;
    
	  $message_array = array();
    
	  // MAKE SURE MESSAGES ARE ALLOWED
	  if( empty($this->level_info['level_message_allow']) )
      return FALSE;
    
    // BEGIN MESSAGE QUERY
    $sql = "
      SELECT
        se_pmconvos.*,
        se_pms.*,
        se_pmconvoops_user.pmconvoop_read, 
				se_users.user_id,
				se_users.user_username,
				se_users.user_fname,
				se_users.user_lname,
				se_users.user_photo
    ";
    
    // GET MESSAGE AUTHOR, REPLIED STATUS
    if( !$direction ) $sql .= ",
				(SELECT TRUE FROM se_pms WHERE pm_pmconvo_id=se_pmconvos.pmconvo_id && pm_authoruser_id='{$this->user_info['user_id']}' ORDER BY pm_id DESC LIMIT 1)
				AS pm_replied
    ";
    
    // CONTINUE QUERY
    $sql .= "
      FROM
        se_pmconvoops AS se_pmconvoops_user
      LEFT JOIN
        se_pmconvos
        ON se_pmconvoops_user.pmconvoop_pmconvo_id=se_pmconvos.pmconvo_id
      LEFT JOIN
        se_pms
        ON se_pms.pm_pmconvo_id=se_pmconvos.pmconvo_id
    ";
    
    // INCOMING MESSAGES - JOIN TO USER TABLE TO GET AUTHOR
    if( !$direction ) $sql .= "
      LEFT JOIN
        se_users
        ON se_users.user_id=se_pms.pm_authoruser_id";
    
    // OUTGOING MESSAGES - JOIN TO PMCONVOOPS AND USER TABLE TO GET RECIPIENT
    if(  $direction ) $sql .= "
      LEFT JOIN
        se_pmconvoops AS se_pmconvoops_other
        ON (se_pmconvoops_other.pmconvoop_pmconvo_id=se_pmconvos.pmconvo_id && se_pmconvoops_other.pmconvoop_user_id!='{$this->user_info['user_id']}')
      LEFT JOIN
        se_users
        ON se_users.user_id=se_pmconvoops_other.pmconvoop_user_id
    ";
    
    
    // CONTINUE QUERY
    $sql .= "
      WHERE
        se_pmconvoops_user.pmconvoop_user_id='{$this->user_info['user_id']}'
    ";
    
    // INCOMING MESSAGES
    if( !$direction ) $sql .= " &&
        se_pmconvoops_user.pmconvoop_deleted_inbox=0
    ";
    
    // OUTGOING MESSAGES
    if(  $direction ) $sql .= " &&
        se_pmconvoops_user.pmconvoop_deleted_outbox=0
    ";
    
    // CONTINUE QUERY
    $sql .= " &&
        se_pms.pm_id=(
          SELECT
            MAX(pm_id)
          FROM
            se_pms
          WHERE
            pm_pmconvo_id=se_pmconvos.pmconvo_id
    ";
    
    // INCOMING MESSAGES
    if( !$direction ) $sql .= " &&
            se_pms.pm_authoruser_id!='{$this->user_info['user_id']}'
    ";
    
    // OUTGOING MESSAGES
    if(  $direction ) $sql .= " &&
            se_pms.pm_authoruser_id='{$this->user_info['user_id']}'
    ";
    
    // CONTINUE QUERY
    $sql .= "
        )
    ";
    
    // ADD WHERE
    if( $where ) $sql .= " && {$where}";
    
    /*
      GROUP BY
        se_pmconvoops_user.pmconvoop_pmconvo_id
    */
    
    $sql .= "
      ORDER BY
        se_pmconvoops_user.pmconvoop_pmdate DESC
        /* se_pms.pm_date DESC */
      LIMIT
        $start, $limit
    ";
    
    // EXECUTE QUERY
    $resource = $database->database_query($sql);
    
    // GET MESSAGES
	  while( $message_info=$database->database_fetch_assoc($resource) )
    {
      // CREATE AN OBJECT FOR MESSAGE AUTHOR/RECIPIENT
      $pm_user = new SEUser();
      $pm_user->user_info['user_id']        = $message_info['user_id'];
      $pm_user->user_info['user_username']  = $message_info['user_username'];
      $pm_user->user_info['user_photo']     = $message_info['user_photo'];
      $pm_user->user_info['user_fname']     = $message_info['user_fname'];
      $pm_user->user_info['user_lname']     = $message_info['user_lname'];
      $pm_user->user_displayname();
      
      // Remove breaks for preview
      $message_info['pm_body'] = str_replace("<br>", "", $message_info['pm_body']);
      
      // SET MESSAGE ARRAY
      $message_array[] = array(
        'pmconvo_id'      => $message_info['pmconvo_id'],
        'pmconvo_subject' => $message_info['pmconvo_subject'],
        'pm_date'         => $message_info['pm_date'],
        'pm_read'         => (bool) $message_info['pmconvoop_read'],
        'pm_replied'      => $message_info['pm_replied'],
        'pm_body'         => $message_info['pm_body'],
        'pm_user'         => &$pm_user,
        'pm_recipients'   => $message_info['pmconvo_recipients'] - 1
      );
      
      unset($pm_user);
    }
    
    return $message_array;
  }
  
  // END user_message_list() METHOD








  //
	// THIS METHOD SENDS A MESSAGE TO ANOTHER USER
  //
	// INPUT:
  //    $to REPRESENTING A SEMI-COLON DELIMITED STRING OF USERNAMES OF THE RECIPIENTS
	//	  $subject REPRESENTING THE SUBJECT OF THE MESSAGE
	//	  $message REPRESENTING THE MESSAGE BODY
	//	  $convo_id (OPTIONAL) REPRESENTING THE CONVERSATION ID
  //
	// OUTPUT: 
  //    void
  //
  
  function user_message_send($to, $subject, $message, $convo_id=NULL)
  {
    global $database, $notify, $url;
    
    $recipients = array();
    $recipients_full = array();
    
	  // VALIDATE CONVERSATION ID
	  if( !$convo_id || !is_numeric($convo_id) )
      $convo_id = 0;
    
	  // CHECK TO SEE IF MESSAGE IS EMPTY
	  if( !trim($message) )
      $this->is_error = 796;
    
	  // NEW MESSAGE
	  if( !$convo_id )
    {
	    // ORGANIZE RECIPIENTS
	    $tos = array_filter(preg_split('/[\s,;]+?/', $to));
	    array_splice($tos, $this->level_info['level_message_recipients']);
      
	    // LOOP OVER RECIPIENTS
      foreach( $tos as $to_username )
      {
        // CANT SEND TO SELF
        if( strtolower($to_username)==strtolower($this->user_info['user_username']) ) continue;
        
        // GET TO USER OBJECT
	      $to_user = new SEUser(array(NULL, $to_username));
        
        // CANT SEND TO NON EXISTENT USER. BLOCKED USER, OR USERS NOT ALLOWED TO USE MESSAGES
        if( !$to_user->user_exists ) continue;
        if( $to_user->user_blocked($this->user_info['user_id']) ) continue;
        if( !$this->level_info['level_message_allow'] ) continue;
        
        // CHECK MESSAGE TYPES AND ADD RECIPIENT
        if( $this->level_info['level_message_allow']==2 || ($this->level_info['level_message_allow']==1 && $this->user_friended($to_user->user_info['user_id'])) )
        {
          $recipients_full[$to_user->user_info['user_id']] =& $to_user;
          $recipients[] = $to_user->user_info['user_id'];
        }
	    }
      
      
	    // ENSURE THERE ARE RECIPIENTS
	    if( empty($recipients) )
        $this->is_error = 795;
      
      
	    // IF NO ERROR, CREATE CONVERSATION
	    if( !$this->is_error )
      {
        // CREATE CONVO
        $sql = "INSERT INTO se_pmconvos (pmconvo_subject, pmconvo_recipients) VALUES ('".addslashes($subject)."', '".(count($recipients)+1)."')";
        $resource = $database->database_query($sql);
	      $convo_id = $database->database_insert_id();
        
        // CREATE CONVOOPS
        $sql = "
          INSERT INTO se_pmconvoops
            (pmconvoop_pmconvo_id, pmconvoop_user_id, pmconvoop_deleted_outbox, pmconvoop_deleted_inbox)
          VALUES
            ('{$convo_id}', '{$this->user_info['user_id']}', 0, 1)";
        
        //$is_first = TRUE;
        foreach( $recipients as $to_user_id )
          $sql .= ", ('{$convo_id}', '{$to_user_id}', 1, 0)";
        
        // EXECUTE QUERY
        $resource = $database->database_query($sql);
	    }
    }
    
    // GET RECIPIENTS AND VERIFY USER IS PART OF CONVERSATION
    else
    {
      $sql = "SELECT pmconvoop_user_id FROM se_pmconvoops WHERE pmconvoop_pmconvo_id='{$convo_id}'";
      $resource = $database->database_query($sql);
      
      $unauthorized = TRUE;
      while( $pmconvoop_info=$database->database_fetch_assoc($resource) )
      {
        if( $pmconvoop_info['pmconvoop_user_id']!=$this->user_info['user_id'] )
          $recipients[] = $pmconvoop_info['pmconvoop_user_id'];
        else
          $unauthorized = FALSE;
      }
      
      // USER WAS NOT IN CONVERSATION
      if( $unauthorized )
        $this->is_error = 39; // FIX THIS CODE RANDOM NUMBER TEMP
    }
    
	  // IF NO ERROR, ADD MESSAGE TO CONVERSATION
	  if( !$this->is_error )
    {
	    // LINK ALL LINKS
      $message = ereg_replace("http://([.]?[a-zA-Z0-9_/-])*", "<a href=\"\\0\" target=\"_blank\">\\0</a>", $message);
      $message = ereg_replace("(^| |\n)(www([.]?[a-zA-Z0-9_/-])*)", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $message);
      
	    // RUN SECURITY ON THE MESSAGE TO ENSURE NO XSS ATTACKS WITH LINKS
	    $message = cleanHTML($message, "a");
      
	    // REPLACE NEWLINES IN BODY WITH BREAKS
	    $message = str_replace("\n", "<br>", $message);
	    $message = str_replace("'", "\'", $message);
      
	    // INSERT MESSAGE
	    $pm_date = time();
      
      $sql = "
        INSERT INTO se_pms
          (pm_authoruser_id, pm_pmconvo_id, pm_date, pm_body)
        VALUES
          ('{$this->user_info['user_id']}', '{$convo_id}', '{$pm_date}', '{$message}')
      ";
      
      $resource = $database->database_query($sql);
      
      
      // UPDATE PMCONVOOPS
      $sql = "UPDATE se_pmconvoops SET pmconvoop_deleted_outbox=0, pmconvoop_pmdate='{$pm_date}' WHERE pmconvoop_pmconvo_id='{$convo_id}' && pmconvoop_user_id='{$this->user_info['user_id']}'";
      $resource = $database->database_query($sql);
      
      $sql = "UPDATE se_pmconvoops SET pmconvoop_deleted_inbox=0, pmconvoop_read=0, pmconvoop_pmdate='{$pm_date}' WHERE pmconvoop_pmconvo_id='{$convo_id}' && pmconvoop_user_id!='{$this->user_info['user_id']}'";
      $resource = $database->database_query($sql);
      
      
	    // INSERT/SEND NOTIFICATIONS FOR RECIPIENTS
      // GET RECIPIENTS IF NOT INITIAL MESSAGE
      foreach( $recipients as $recipient_user_id )
      {
        //if( empty($recipients_full[$recipient_user_id]) )
        //{
          $recipients_full[$recipient_user_id] = new SEUser(array($recipient_user_id));
        //}
        
        $current_recipient =& $recipients_full[$recipient_user_id];
        
        // NOT A USER
        if( !is_object($current_recipient) || !$current_recipient->user_exists )
          continue;
        
        // ADD NOTIFICATION
        $notify->notify_add($current_recipient->user_info['user_id'], 'message', $convo_id, array(), array(), TRUE);
        
        // SEND EMAIL
        $current_recipient->user_settings('usersetting_notify_message');
        if( $current_recipient->usersetting_info['usersetting_notify_message'] )
        {
          send_systememail('message', $current_recipient->user_info[user_email], array(
            $current_recipient->user_displayname,
            $this->user_displayname,
            "<a href=\"{$url->url_base}login.php\">{$url->url_base}login.php</a>"
          ));
        }
        
        // CLEAN OUT THEM OLD MESSAGES
        $num_inbox = $current_recipient->user_message_total(0, 0);
        $num_outbox = $current_recipient->user_message_total(1, 0);
        $num_inbox_delete = $num_inbox - $current_recipient->level_info['level_message_inbox'];
        $num_outbox_delete = $num_outbox - $current_recipient->level_info['level_message_outbox'];
        
        // CLEAN OUT INBOX
	      if( $num_inbox_delete>0 )
        {
          $sql = "
            SELECT
              se_pmconvoops.pmconvoop_pmconvo_id AS pmconvo_id
            FROM
              se_pmconvoops
            LEFT JOIN
              se_pmconvos
              ON se_pmconvos.pmconvo_id=se_pmconvoops.pmconvoop_pmconvo_id
            LEFT JOIN
              se_pms
              ON se_pms.pm_pmconvo_id=se_pmconvos.pmconvo_id
            WHERE
              se_pmconvoops.pmconvoop_user_id='{$current_recipient->user_info['user_id']}' &&
              se_pmconvoops.pmconvoop_deleted_inbox=0 &&
              se_pms.pm_id=(SELECT MAX(pm_id) FROM se_pms WHERE pm_pmconvo_id=se_pmconvoops.pmconvoop_pmconvo_id)
            ORDER BY
              se_pms.pm_date ASC
            LIMIT
              {$num_inbox_delete}
          ";
          
          $resource = $database->database_query($sql);
          
          while( $result=$database->database_fetch_assoc($resource) )
            $delete_array[] = $result['pmconvo_id'];
          
          // DELETE
	        $current_recipient->user_message_delete_selected($delete_array, 0);
        }
        
        // CLEAN OUT OUTBOX
        if( $num_outbox_delete>0 )
        {
          $sql = "
            SELECT
              se_pmconvoops.pmconvoop_pmconvo_id AS pmconvo_id
            FROM
              se_pmconvoops
            LEFT JOIN
              se_pmconvos
              ON se_pmconvos.pmconvo_id=se_pmconvoops.pmconvoop_pmconvo_id
            LEFT JOIN
              se_pms
              ON se_pms.pm_pmconvo_id=se_pmconvos.pmconvo_id
            WHERE
              se_pmconvoops.pmconvoop_user_id='{$current_recipient->user_info['user_id']}' &&
              se_pmconvoops.pmconvoop_deleted_outbox=0 &&
              se_pms.pm_id=(SELECT MAX(pm_id) FROM se_pms WHERE pm_pmconvo_id=se_pmconvoops.pmconvoop_pmconvo_id)
            ORDER BY
              se_pms.pm_date ASC
            LIMIT
              {$num_outbox_delete}
          ";
          
          $resource = $database->database_query($sql);
          
          while( $result=$database->database_fetch_assoc($resource) )
            $delete_array[] = $result['pmconvo_id'];
          
          // DELETE
	        $current_recipient->user_message_delete_selected($delete_array, 1);
        }
        
        // CLEAR INACTIVE CONVERSATIONS
        $this->user_message_cleanup();
      }
    }
    
    return $convo_id;
  }
  
  // END user_message_send() METHOD







  
  //
	// THIS METHOD DELETES MANY MESSAGES BASED ON WHAT HAS BEEN POSTED
  //
	// INPUT:
  //    $delete_array CONTAINING THE ARRAY OF CONVERSATION IDs TO DELETE
	//	  $direction (OPTIONAL) REPRESENTING A "0" FOR MESSAGES SENT TO USER AND "1" FOR MESSAGES SENT BY USER
  //
	// OUTPUT: 
  //    void
  //
  
  function user_message_delete_selected($delete_array, $direction=0)
  {
    global $database;
    
	  // START CONSTRUCTING QUERY
    $sql = "
      UPDATE
        se_pmconvoops
      SET
    ";
    
    // INCOMING MESSAGES
    if( !$direction ) $sql .= "
        se_pmconvoops.pmconvoop_deleted_inbox=1
    ";
    
    // OUTGOING MESSAGES
    if(  $direction ) $sql .= "
        se_pmconvoops.pmconvoop_deleted_outbox=1
    ";
    
	  // CONTINUE QUERY
	  $sql .= "
      WHERE
        se_pmconvoops.pmconvoop_user_id='{$this->user_info['user_id']}' &&
        se_pmconvoops.pmconvoop_pmconvo_id IN('".implode("', '", $delete_array)."')
    ";
    
    $database->database_query($sql);
    
	  // DELETE ANY NOTIFICATIONS ASSOCIATED WITH THESE PMs
    $sql = "
      DELETE FROM
        se_notifys
      WHERE
        notify_user_id='{$this->user_info[user_id]}' &&
        notify_notifytype_id='2' &&
        notify_object_id IN('".implode("', '", $delete_array)."')
    ";
    
    $database->database_query($sql);
  }
  
  // END user_message_delete_selected() METHOD







  
  //
	// THIS METHOD CLEANS UP THE PM TABLES
  //
	// INPUT:
  //    void
  //
	// OUTPUT: 
  //    void
  //
  
  function user_message_cleanup()
  {
    global $database;
    
    // CONSTRUCT QUERY
    $sql = "
      SELECT
        SUM(se_pmconvoops.pmconvoop_deleted_inbox) AS total_deleted_inbox,
        SUM(se_pmconvoops.pmconvoop_deleted_inbox) AS total_deleted_outbox,
        se_pmconvos.pmconvo_recipients,
        se_pmconvos.pmconvo_id
      FROM
        se_pmconvos
      LEFT JOIN
        se_pmconvoops
        ON se_pmconvoops.pmconvoop_pmconvo_id=se_pmconvos.pmconvo_id
      GROUP BY
        se_pmconvos.pmconvo_id
      LIMIT
        50
    ";
    
    $resource = $database->database_query($sql);
    
    $to_delete = array();
    while( $result=$database->database_fetch_assoc($resource) && count($to_delete)<50 )
    {
      if( $result['total_deleted_inbox']!=$result['pmconvo_recipients'] ) continue;
      if( $result['total_deleted_outbox']!=$result['pmconvo_recipients'] ) continue;
      $to_delete[] = $result['pmconvo_id'];
    }
    
    $to_delete = array_filter($to_delete);
    
    if( empty($to_delete) )
      return;
    
    $sql = "
      DELETE FROM
        se_pmconvos,
        se_pms,
        se_pmconvoops
      USING
        se_pmconvos
      LEFT JOIN
        se_pms
        ON se_pms.pm_pmconvo_id=se_pmconvos.pmconvo_id
      LEFT JOIN
        se_pmconvoops
        ON se_pmconvoops.pmconvoop_pmconvo_id=se_pmconvos.pmconvo_id
      WHERE
        se_pmconvos.pmconvo_id IN('".join("','", $to_delete)."')
    ";
    
    $resource = $database->database_query($sql);
  }
  
  // END user_message_cleanup() METHOD







  
  //
	// THIS METHOD GETS CONVO INFO IF USER IS PART OF CONVO
  //
	// INPUT:
  //    $convo_id
  //    $validate_only
  //
	// OUTPUT: 
  //    void
  //
  
  function user_message_validate($convo_id, $validate_only=FALSE)
  {
    global $database;
    
    // GET PMCONVO INFO
    $sql = "
      SELECT
        se_pmconvos.*,
        se_pmconvoops.*
      FROM
        se_pmconvos
      LEFT JOIN
        se_pmconvoops
        ON se_pmconvoops.pmconvoop_pmconvo_id=se_pmconvos.pmconvo_id
      WHERE
        se_pmconvos.pmconvo_id='{$convo_id}' &&
        se_pmconvoops.pmconvoop_user_id='{$this->user_info['user_id']}'
      LIMIT
        1
    ";
    
    $resource = $database->database_query($sql);
    
    if( !$database->database_num_rows($resource) )
      return FALSE;
    
    if( $validate_only )
      return TRUE;
    
    return $database->database_fetch_assoc($resource);
  }
  
  // END user_message_validate() METHOD







  
  //
	// THIS METHOD GETS CONVO INFO
  //
	// INPUT:
  //    $convo_id
  //
	// OUTPUT: 
  //    void
  //
  
  function &user_message_view($convo_id)
  {
    global $database;
    
    if( !$this->user_message_validate($convo_id, TRUE) )
      return FALSE;
    
    // SET MESSAGE TO READ
    $sql = "UPDATE se_pmconvoops SET pmconvoop_read=1 WHERE pmconvoop_pmconvo_id='{$convo_id}' && pmconvoop_user_id='{$this->user_info['user_id']}' LIMIT 1";
    $resource = $database->database_query($sql);
    
    // DELETE NOTIFICATIONS
    $sql = "DELETE FROM se_notifys WHERE notify_user_id='{$this->user_info['user_id']}' AND notify_notifytype_id='2' AND notify_object_id='{$convo_id}'";
    $resource = $database->database_query($sql);
    
    // GET COLLABORATORS
    // added user_blocklist
    $sql = "
      SELECT
        user_id,
        user_username,
        user_fname,
        user_lname,
        user_photo,
        user_blocklist
      FROM
        se_pmconvoops
      LEFT JOIN
        se_users
        ON se_users.user_id=se_pmconvoops.pmconvoop_user_id
      WHERE
        se_pmconvoops.pmconvoop_pmconvo_id='{$convo_id}' &&
        se_pmconvoops.pmconvoop_user_id!='{$this->user_info['user_id']}'
    ";
    
    $resource = $database->database_query($sql);
    
    $collaborators = array();
    $collaborators_by_id = array();
    while( $result=$database->database_fetch_assoc($resource) )
    {
      $coll = new SEUser();
      $coll->user_info['user_id']        = $result['user_id'];
      $coll->user_info['user_username']  = $result['user_username'];
      $coll->user_info['user_photo']     = $result['user_photo'];
      $coll->user_info['user_fname']     = $result['user_fname'];
      $coll->user_info['user_lname']     = $result['user_lname'];
      $coll->user_info['user_blocklist'] = $result['user_blocklist']; // this was added to fix blocklist bug
      $coll->user_displayname();
      
      $collaborators[] =& $coll;
      $collaborators_by_id[$result['user_id']] =& $coll;
      unset($coll);
    }
    
    // GET CONVERSATION
    $sql = "
      SELECT
        se_pms.*
      FROM
        se_pms
      WHERE
        pm_pmconvo_id='{$convo_id}'
      ORDER BY
        pm_date
    ";
    
    $resource = $database->database_query($sql);
    
    $pms = array();
    while( $result=$database->database_fetch_assoc($resource) )
    {
      $pm_info = $result;
      
      if( $pm_info['pm_authoruser_id']==$this->user_info['user_id'] )
        $pm_info['author'] =& $this;
      else
        $pm_info['author'] =& $collaborators_by_id[$pm_info['pm_authoruser_id']];
      
      $pms[] =& $pm_info;
      unset($pm_info);
    }
    
    return array
    (
      'collaborators' => &$collaborators,
      'pms'           => &$pms
    );
  }
  
  // END user_message_view() METHOD
  
  
  
  function user_auth_token_create($persistent = false)
  {
    if( !$this->user_exists )
    {
      return false;
    }
    
    $db =& SEDatabase::getInstance();
    
    $id = false;
    while( !$id )
    {
      $id = sha1(uniqid(mt_rand(), true));
      $resource = $db->database_query("SELECT NULL FROM se_session_auth WHERE session_auth_key='{$id}' LIMIT 1");
      if( $db->database_num_rows($resource) >= 1 )
      {
        $id = false;
      }
    }
    
    $persistent = (bool) $persistent;
    $ua = md5($_SERVER['HTTP_USER_AGENT']);
    $ip = ip2long($_SERVER['REMOTE_ADDR']);
    $now = time();
    
    $sql = "
      INSERT INTO se_session_auth
        (session_auth_key, session_auth_user_id, session_auth_ua, session_auth_ip, session_auth_type, session_auth_time)
      VALUES
        ('{$id}', '{$this->user_info['user_id']}', '{$ua}', '{$ip}', '{$persistent}', '{$now}')
    ";
    $resource = $db->database_query($sql);
    
    
    // Success, set token
    if( $resource )
    {
      // Delete old token if necessary
      $this->user_auth_token_delete(null, false);
      
      // Set new token
      $cookie_lifetime = ( $persistent ? time() + (60 * 60 * 24 * 30 * 6) : 0 );
      $host = get_simple_cookie_domain();
      setcookie('se_auth_token', $id, $cookie_lifetime, '/', $host);
      return $id;
    }
    
    else
    {
      // Delete existing auth token on failure
      $this->user_auth_token_delete(null, true);
      return false;
    }
  }
  
  
  function user_auth_token_delete($id = null, $delete_cookie = true)
  {
    if( !$id )
    {
      $id = $_COOKIE['se_auth_token'];
      if( !$id )
      {
        return;
      }
    }
    
    // Remove cookie
    if( $delete_cookie )
    {
      $host = get_simple_cookie_domain();
      setcookie('se_auth_token', null, (int) time() / 2, '/', $host);
    }
    
    // Remove from db
    $db =& SEDatabase::getInstance();
    $db->database_query("DELETE FROM se_session_auth WHERE session_auth_key='{$id}' LIMIT 1");
    
    // Cleanup? ~6 months
    $mintime = time() - (60 * 60 * 24 * 30 * 6);
    $db->database_query("DELETE FROM se_session_auth WHERE session_auth_time<'{$mintime}'");
  }
  
  
  function user_auth_token_check()
  {
    // We are already logged in? Why are we checking this?
    if( $this->user_exists )
    {
      return true;
    }
    
    $id = @$_COOKIE['se_auth_token'];
    
    // No auth token set, fail
    if( !$id )
    {
      return false;
    }
    
    $db =& SEDatabase::getInstance();
    $ua = md5($_SERVER['HTTP_USER_AGENT']);
    $ip = ip2long($_SERVER['REMOTE_ADDR']);
    
    $resource = $db->database_query("SELECT session_auth_user_id, session_auth_type FROM se_session_auth WHERE session_auth_key='{$id}' && session_auth_ip='{$ip}' && session_auth_ua='{$ua}' LIMIT 1");
    if( !$db->database_num_rows($resource) )
    {
      // There was an invalid key, remove it
      $this->user_auth_token_delete(null, true);
      return false;
    }
    
    $info = $db->database_fetch_assoc($resource);
    $persistent = (bool) $info['session_auth_type'];
    $user_id = $info['session_auth_user_id'];
    
    // Should we populate use data here?
    $this->SEUser(array($user_id));
    $this->user_setcookies($persistent);
    
    return $user_id;
  }
}




// Backwards compat
class se_user extends SEUser
{
  function se_user($user_unique = Array('0', '', ''), $select_fields = Array('*', '*', '*', '*'))
  {
    $this->SEUser($user_unique, $select_fields);
  }
}



?>