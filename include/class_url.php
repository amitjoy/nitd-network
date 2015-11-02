<?php

//  THIS CLASS CONTAINS URL-RELATED METHODS.
//  IT IS USED TO RETURN THE CURRENT URL AND CREATE NEW URLS
//  METHODS IN THIS CLASS:
//    se_url()
//    url_create()
//    url_current()
//    url_userdir()
//    url_encode()





class SEUrl
{

	// INITIALIZE VARIABLES
	var $is_error;			// DETERMINES WHETHER THERE IS AN ERROR OR NOT
	var $url_base;			// CONTAINS THE BASE URL TO WHICH FILENAMES CAN BE APPENDED
	var $convert_urls;		// CONTAINS THE URL CONVERSIONS








	// THIS METHOD SETS THE BASE URL TO WHICH FILENAMES CAN BE APPENDED
	// INPUT:
	// OUTPUT: A STRING REPRESENTING A PATH TO WHICH FILENAMES CAN BE APPENDED TO CREATE URLs
  
	function SEUrl()
  {
	  global $database;
    
	  $server_array = explode("/", $_SERVER['PHP_SELF']);
	  $server_array_mod = array_pop($server_array);
	  if($server_array[count($server_array)-1] == "admin") { $server_array_mod = array_pop($server_array); }
	  $server_info = implode("/", $server_array);
	  $this->url_base = "http".(443==$_SERVER['SERVER_PORT']?'s':'')."://".$_SERVER['HTTP_HOST'].$server_info."/";
    
    $this->convert_urls =& SEUrl::getSettings();
	}
  
  // END SEUrl() METHOD
  
  
  
  


	// THIS METHOD GETS URLS SETTINGS
	// INPUT:
	// OUTPUT: THE ARRAY OF SETTINGS
  
  function &getSettings()
  {
    static $url_settings;
    
    if( !is_array($url_settings) )
    {
      $cache = SECache::getInstance();
      
      // Get from cache
      if( is_object($cache) )
      {
        $url_settings = $cache->get('site_url_settings');
      }
      
      // Get from database
      if( !is_array($url_settings) )
      {
        $database = SEDatabase::getInstance();
        $resource = $database->database_query("SELECT url_file, url_regular, url_subdirectory FROM se_urls");
        $url_settings = $database->database_load_all_assoc('url_file');
        
        // Special case -_-
        $url_settings['profile'] = array(
          'url_regular' => 'profile.php?user=$user',
          'url_subdirectory' => '$user/'
        );
        
        // Store in cache
        if( is_object($cache) )
        {
          $cache->store($url_settings, 'site_url_settings');
        }
      }
    }
    
    return $url_settings;
  }
  
  // END getSettings() METHOD








	// THIS METHOD CREATES A FULL URL TO A GIVEN PAGE
	// INPUT: $file REPRESENTING THE PAGE TO CREATE THE URL FOR
	//	  $user REPRESENTING THE USERNAME OF THE USER
	//	  THERE ARE FURTHER OPTIONAL PARAMETERS TO ALLOW FOR ADDITIONAL REPLACEMENTS
	// OUTPUT: A STRING REPRESENTING A URL
  
	function url_create($file, $user)
  {
	  global $setting;

	  $url_conversion = $this->convert_urls[$file];
	  
	  if( $setting['setting_url'] == 1 )
    {
	    $new_url = $url_conversion['url_subdirectory'];
	  }
    else
    {
	    $new_url = $url_conversion['url_regular'];
	  }

	  $num_args = func_num_args();
	  $search = Array('$user');
	  $replace = Array($user);
	  for($a=2;$a<$num_args;$a++)
    {
	    $search[] = '$id'.($a-1);
	    $replace[] = func_get_arg($a);
	  }
    
	  $new_url = str_replace($search, $replace, $new_url);
    
	  return $this->url_base.$new_url;
	}
  
  // END url_create() METHOD








	// THIS METHOD RETURNS THE URL TO THE CURRENT PAGE
	// INPUT: 
	// OUTPUT: A STRING REPRESENTING THE URL TO THE CURRENT PAGE
	function url_current()
  {
	  $current_url_domain = $_SERVER['HTTP_HOST'];
	  $current_url_path = $_SERVER['SCRIPT_NAME'];
	  $current_url_querystring = $_SERVER['QUERY_STRING'];
	  $current_url = "http://".$current_url_domain.$current_url_path;
	  if($current_url_querystring != "") {  $current_url .= "?".$current_url_querystring; }
	  $current_url = urlencode($current_url);
	  return $current_url;
	}
  
  // END url_current() METHOD








	// THIS METHOD RETURNS THE PATH TO THE GIVEN USER'S DIRECTORY
	// INPUT: $user_id REPRESENTING A USER'S USER_ID
	// OUTPUT: A STRING REPRESENTING THE RELATIVE PATH TO THE USER'S DIRECTORY
  
	function url_userdir($user_id)
  {
	  $subdir = $user_id+999-(($user_id-1)%1000);
	  $userdir = "./uploads_user/$subdir/$user_id/";
	  return $userdir;
	}
  
  // END url_userdir() METHOD








	// THIS METHOD RETURNS A URLENCODED VERSION OF THE GIVEN STRING
	// INPUT: $url REPRESENTING ANY STRING
	// OUTPUT: A STRING REPRESENTING A URLENCODED VERSION OF THE GIVEN STRING
	function url_encode($url)
  {
	  return urlencode($url);
	}
  
  // END url_encode() METHOD
}



// Backwards compatibility
class se_url extends SEUrl
{
  function se_url()
  {
    $this->SEUrl();
  }
}



?>
