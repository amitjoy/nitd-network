<?php

//  THIS CLASS CONTAINS ADMIN-RELATED METHODS.
//  IT IS USED DURING THE CREATION, MODIFICATION AND DELETION OF AN ADMIN.
//  METHODS IN THIS CLASS:
//    se_admin()
//    admin_create()
//    admin_password_crypt()
//    admin_checkCookies()
//    admin_setCookies()
//    admin_login()
//    admin_account()
//    admin_edit()
//    admin_clear()
//    admin_logout()
//    admin_delete()



class SEAdmin
{
	// INITIALIZE VARIABLES
	var $is_error;			// DETERMINES WHETHER THERE IS AN ERROR OR NOT, CONTAINS RELEVANT ERROR CODE
	var $admin_exists;		// DETERMINES WHETHER WE ARE EDITING AN EXISTING ADMIN OR NOT
	var $admin_salt;		// CONTAINS THE SALT USED TO ENCRYPT THE ADMIN PASSWORD

	var $admin_info;		// CONTAINS ADMIN'S INFORMATION FROM SE_ADMINS TABLE
	var $admin_super;		// DETERMINES WHETHER ADMIN IS A SUPER ADMIN OR NOT









	// THIS METHOD SETS INITIAL VARS SUCH AS ADMIN INFO
	// INPUT: $admin_id (OPTIONAL) REPRESENTING A ADMIN'S ADMIN_ID
	//	  $admin_username (OPTIONAL) REPRESENTING AN ADMIN'S ADMIN_USERNAME
	// OUTPUT: 
  
	function SEAdmin($admin_id = 0, $admin_username = "")
  {
	  global $database;
    
	  // SET INITIAL VARIABLES
	  $this->is_error = FALSE;
	  $this->admin_exists = FALSE;
	  $this->admin_super = FALSE;
	  
	  // VERIFY ADMIN_ID IS VALID AND SET APPROPRIATE OBJECT VARIABLES
	  if( $admin_id || trim($admin_username) )
    {
	    $admin = $database->database_query("SELECT * FROM se_admins WHERE admin_id='{$admin_id}' OR admin_username='{$admin_username}'");
	    if( $database->database_num_rows($admin) )
      {
	      $this->admin_exists = TRUE;
	      $this->admin_info = $database->database_fetch_assoc($admin);
        $this->admin_salt = $this->admin_info['admin_code'];
        
        // Set is super
	      $super = $database->database_fetch_assoc($database->database_query("SELECT admin_id FROM se_admins ORDER BY admin_id LIMIT 1"));
	      if( $super['admin_id'] == $this->admin_info['admin_id']) $this->admin_super = TRUE;
	    }
	  }
	}
  
  // END se_admin() METHOD








	// THIS METHOD CREATES A USER ACCOUNT USING THE GIVEN INFORMATION
	// INPUT: $admin_username REPRESENTING THE DESIRED USERNAME
	//	  $admin_password REPRESENTING THE DESIRED PASSWORD
	//	  $admin_name REPRESENTING THE DESIRED NAME
	//	  $admin_email REPRESENTING THE DESIRED EMAIL
	// OUTPUT: 
  
	function admin_create($admin_username, $admin_password, $admin_name, $admin_email)
  {
	  global $database, $setting;
    
    $admin_password_encrypted = $this->admin_password_crypt($admin_password);
    
	  $database->database_query("
      INSERT INTO se_admins (
        admin_username,
        admin_password,
        admin_password_method,
        admin_code,
        admin_name,
        admin_email
      ) VALUES (
        '{$admin_username}',
        '{$admin_password_encrypted}',
        '{$setting['setting_password_method']}',
        '{$this->admin_salt}',
        '{$admin_name}',
        '{$admin_email}'
      )
    ");
	}
  
  // END admin_create() METHOD








	// THIS METHOD ENCRYPTS A PASSWORD
	// INPUT: UNENCRYPTED PASSWORD
	// OUTPUT: ENCRYPTED PASSWORD
  
	function admin_password_crypt($admin_password)
  {
    global $setting;
    
    if( !$this->admin_exists )
    {
      $method = $setting['setting_password_method'];
      $this->admin_salt = randomcode($setting['setting_password_code_length']);
    }
    
    else
    {
      $method = $this->admin_info['admin_password_method'];
    }
    
    // For new methods
    if( $method>0 )
    {
      if( !empty($this->admin_salt) )
      {
        list($salt1, $salt2) = str_split($this->admin_salt, ceil(strlen($this->admin_salt) / 2));
        $salty_password = $salt1.$admin_password.$salt2;
      }
      else
      {
        $salty_password = $admin_password;
      }
    }
    
    switch( $method )
    {
      // crypt()
      default:
      case 0:
        if( empty($this->admin_salt) ) $this->admin_salt = 'admin123';
        $admin_password_crypt = crypt($admin_password, '$1$'.str_pad(substr($this->admin_salt, 0, 8), 8, '0', STR_PAD_LEFT).'$');
      break;
      
      // md5()
      case 1:
        $admin_password_crypt = md5($salty_password);
      break;
      
      // sha1()
      case 2:
        $admin_password_crypt = sha1($salty_password);
      break;
      
      // crc32()
      case 3:
        $admin_password_crypt = sprintf("%u", crc32($salty_password));
      break;
    }
    
    return $admin_password_crypt;
  }
  
  // END admin_password_crypt() METHOD
  
  
  
  
  
  
  
  
	// THIS METHOD VERIFIES LOGIN COOKIES AND SETS APPROPRIATE OBJECT VARIABLES
	// INPUT: 
	// OUTPUT: 
  
	function admin_checkCookies()
  {
    // SAFE MODE (cookies)
    if( defined('SE_ADMIN_SAFE_MODE') && SE_ADMIN_SAFE_MODE===TRUE )
    {
      $admin_id = ( isset($_COOKIE['admin_id']) ? $_COOKIE['admin_id'] : NULL );
      $admin_username = ( isset($_COOKIE['admin_username']) ? $_COOKIE['admin_username'] : NULL );
      $admin_password = ( isset($_COOKIE['admin_password']) ? $_COOKIE['admin_password'] : NULL );
    }
    
    // NORMAL (sessions)
    else
    {
      $session_object =& SESession::getInstance();
      
      $admin_id = $session_object->get('admin_id');
      $admin_username = $session_object->get('admin_username');
      $admin_password = $session_object->get('admin_password');
    }
    
	  if( isset($admin_id) && isset($admin_username) && isset($admin_password) )
    {
	    // GET ADMIN ROW IF AVAILABLE
      if( !$this->admin_exists )
      {
        $this->SEAdmin($admin_id);
      }
      
	    // VERIFY USER EXISTS, LOGIN COOKIE VALUES ARE CORRECT, AND EMAIL HAS BEEN VERIFIED - ELSE RESET USER CLASS
	    switch( TRUE )
      {
        case ( !$this->admin_exists ):
        case ( $admin_username != $this->admin_password_crypt($this->admin_info['admin_username']) ):
        case ( $admin_password != $this->admin_info['admin_password'] ):
        case ( !$this->admin_info['admin_enabled'] ):
          $this->admin_clear();
        break;
	    }
	  }
	}
  
  // END admin_checkCookies() METHOD
  
  
  
  
  
  
  
  
	// THIS METHOD SETS LOGIN COOKIES
	// INPUT: 
	// OUTPUT: 
  
	function admin_setCookies()
  {
    $admin_id = ( !empty($this->admin_info['admin_id']) ? $this->admin_info['admin_id'] : '' );
    $admin_username = ( !empty($this->admin_info['admin_username']) ? $this->admin_password_crypt($this->admin_info['admin_username']) : '' );
    $admin_password = ( !empty($this->admin_info['admin_password']) ? $this->admin_info['admin_password'] : '' );
    
    // SAFE MODE (cookies)
    if( defined('SE_ADMIN_SAFE_MODE') && SE_ADMIN_SAFE_MODE===TRUE )
    {
	    setcookie("admin_id", $admin_id, 0, "/");
	    setcookie("admin_username", $admin_username, 0, "/");
	    setcookie("admin_password", $admin_password, 0, "/");
    }
    
    // NORMAL (sessions)
    else
    {
      $session_object =& SESession::getInstance();
      
      //$session_object->restart();
      
      $session_object->set('admin_id', $admin_id);
      $session_object->set('admin_username', $admin_username);
      $session_object->set('admin_password', $admin_password);
	  }
	}
  
  // END admin_setCookies() METHOD
  
  
  
  
  
  
  
  
	// THIS METHOD TRIES TO LOG AN ADMIN IN IF THERE IS NO ERROR
	// INPUT: 
	// OUTPUT: 
  
	function admin_login()
  {
	  $this->SEAdmin(0, $_POST['username']);
    
	  // SHOW ERROR IF JAVASCRIPT IS DIABLED
	  if( isset($_POST['javascript']) && $_POST['javascript'] == "no" )
    {
	    $this->is_error = 31;
	  }
    
    elseif( !$this->admin_exists )
    {
	    $this->is_error = 32;
	  }
    
    elseif( !$this->admin_info['admin_enabled'] )
    {
	    $this->is_error = 677;
	  }
    
    elseif( $this->admin_password_crypt($_POST['password']) != $this->admin_info['admin_password'] )
    {
	    $this->is_error = 32;
	  }
    
    else
    {
      $this->admin_setCookies();
	  }
	}
  
  // END admin_setCookies() METHOD









	// THIS METHOD LOOPS AND/OR VALIDATES USER ACCOUNT INPUT
	// INPUT: $admin_username REPRESENTING THE DESIRED USERNAME
	//	  $admin_password_old REPRESENTING THE ADMIN'S OLD PASSWORD, IF ONE EXISTS
	//	  $admin_password REPRESENTING THE DESIRED PASSWORD
	//	  $admin_password_confirm REPRESENTING THE PASSWORD CONFIRMATION FIELD
	//	  $admin_name REPRESENTING THE DESIRED NAME
	//	  $admin_email REPRESENTING THE DESIRED EMAIL
	// OUTPUT: 
  
	function admin_account($admin_username, $admin_password_old, $admin_password, $admin_password_confirm, $admin_name, $admin_email)
  {
	  global $database, $class_admin;
    
	  // CHECK FOR EMPTY FIELDS
	  if( !trim($admin_username) || !trim($admin_name) || !trim($admin_email) )
    {
      $this->is_error = 51;
      return;
    }
    
	  // MUST EITHER HAVE AN EXISTING PASSWORD OR IS GETTING A PASSWORD
	  if( !trim($this->admin_info['admin_password']) && !trim($admin_password) )
    {
      $this->is_error = 51;
      return;
    }
    
	  // CHECK PASSWORDS
    if( trim($admin_password) || trim($admin_password_confirm) )
    {
      // CHECK FOR OLD PASSWORD MATCH
      if( $this->admin_info['admin_password'] )
      {
        if( !trim($admin_password_old) )
        {
          $this->is_error = 51;
          return;
        }
        if( $this->admin_password_crypt($admin_password_old) != $this->admin_info['admin_password'] )
        {
          $this->is_error = 267;
          return;
        }
      }
      
      // CHECK FOR INVALID PASSWORD
      if( preg_match("/[^a-zA-Z0-9]/", $admin_password) )
      {
        $this->is_error = 52;
        return;
      }
      
      // CHECK FOR PASSWORD LENGTH
      if( strlen($admin_password) < 6 )
      {
        $this->is_error = 53;
        return;
      }
      
      // CHECK FOR PASSWORD MATCH
      if( $admin_password!=$admin_password_confirm )
      {
        $this->is_error = 54;
        return;
      }
    }
    
	  // CHECK FOR INVALID USERNAME OR PASSWORD
	  if( preg_match("/[^a-zA-Z0-9]/", $admin_username) )
    {
      $this->is_error = 52;
      return;
    }
    
	  // CHECK FOR DUPLICATE USERNAME
	  $lowercase_username = strtolower($admin_username);
	  if( strtolower($this->admin_info['admin_username']) != $lowercase_username && $database->database_num_rows($database->database_query("SELECT admin_id FROM se_admins WHERE LOWER(admin_username)='{$lowercase_username}'")) )
    {
      $this->is_error = 268;
      return;
    }
    
	}
  
  // END admin_account() METHOD








	// THIS METHOD EDITS A USER ACCOUNT USING THE GIVEN INFORMATION
	// INPUT: $admin_username REPRESENTING THE DESIRED USERNAME
	//	  $admin_password REPRESENTING THE DESIRED PASSWORD
	//	  $admin_name REPRESENTING THE DESIRED NAME
	//	  $admin_email REPRESENTING THE DESIRED EMAIL
	// OUTPUT: 
  
	function admin_edit($admin_username, $admin_password, $admin_name, $admin_email)
  {
	  global $database;
    
	  if( trim($admin_password) )
    {
	    $admin_password_encrypted = $this->admin_password_crypt($admin_password);
	  }
    else
    {
	    $admin_password_encrypted = $this->admin_info['admin_password'];
	  }
    
	  $database->database_query("UPDATE se_admins SET admin_username='{$admin_username}', admin_password='{$admin_password_encrypted}', admin_name='{$admin_name}', admin_email='{$admin_email}' WHERE admin_id='{$this->admin_info['admin_id']}' LIMIT 1");
    
	  // RESET COOKIE IF CURRENT ADMIN IS LOGGED IN
	  global $admin;
	  if( $admin->admin_info['admin_id'] == $this->admin_info['admin_id'] )
    {
      $this->admin_info['admin_username'] = $admin_username;
      $this->admin_info['admin_password'] = $admin_password_encrypted;
      $this->admin_setCookies();
	  }
	}
  
  // END admin_edit() METHOD








	// THIS METHOD CLEARS OBJECT VARS
	// INPUT: 
	// OUTPUT: 
  
	function admin_clear()
  {
	  $this->is_error = FALSE;
	  $this->admin_exists = FALSE;
	  $this->admin_super = FALSE;
	  $this->admin_salt = NULL;
	  $this->admin_info = array();
	}
  
  // END admin_clear() METHOD








	// THIS METHOD LOGS AN ADMIN OUT
	// INPUT: 
	// OUTPUT: 
  
	function admin_logout()
  {
	  $this->admin_clear();
    $this->admin_setCookies();
	}
  
  // END admin_logout() METHOD








	// THIS METHOD DELETES THE ADMIN CURRENTLY ASSOCIATED WITH THIS OBJECT
	// INPUT: 
	// OUTPUT:
	function admin_delete()
  {
	  global $database;
    
	  $database->database_query("DELETE FROM se_admins WHERE admin_id='{$this->admin_info['admin_id']}' LIMIT 1");
	  $this->admin_clear();
	}
  // END admin_delete() METHOD
}






// Backwards compatibility
class se_admin extends SEAdmin
{
  function se_admin($admin_id = 0, $admin_username = "")
  {
    $this->SEAdmin($admin_id, $admin_username);
  }
}

?>