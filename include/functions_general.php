<?php

//  THIS FILE CONTAINS GENERAL FUNCTIONS
//  FUNCTIONS IN THIS FILE:
//    cheader()
//    make_page()
//    bumplog()
//    randomcode()
//    is_email_address()
//    str_ireplace()
//    htmlspecialchars_decode()
//    str_split()
//    security()
//    select_subnet()
//    link_field_values()
//    censor()
//    dirsize()
//    user_privacy_levels()
//    search_profile()
//    getmicrotime()
//    cleanHTML()
//    chopHTML()
//    choptext()
//    chunkHTML_split()
//    strlen_utf8()
//    mb_unserialize()
//    online_users()
//    site_statistics()
//    recent_signups()
//    recent_logins()
//    popular_users()
//    site_news()
//    friends_birthdays()
//    get_simple_cookie_domain()
//    check_ip_in_banned_list()











// THIS FUNCTION CHANGES LOCATION HEADER TO REDIRECT FOR IIS PRIOR TO SETTING COOKIES
// INPUT: $url REPRESENTING THE URL TO REDIRECT TO
// OUTPUT: 

function cheader($url)
{
  if( strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'microsoft') !== false )
  {
    header("Refresh: 0; URL=".$url);
  }
  else
  {
    header("Location: $url");
  }
  exit();
}

// END cheader() FUNCTION









// THIS FUNCTION RETURNS APPROPRIATE PAGE VARIABLES
// INPUT: $total_items REPRESENTING THE TOTAL NUMBER OF ITEMS
//	  $items_per_page REPRESENTING THE NUMBER OF ITEMS PER PAGE
//	  $p REPRESENTING THE CURRENT PAGE
// OUTPUT: AN ARRAY CONTAINING THE STARTING ITEM, THE PAGE, AND THE MAX PAGE

function make_page($total_items, $items_per_page, $p)
{
	if( !$items_per_page ) $items_per_page = 1;
  $maxpage = ceil($total_items / $items_per_page);
	if( $maxpage <= 0 ) $maxpage = 1;
  $p = ( ($p > $maxpage) ? $maxpage : ( ($p < 1) ? 1 : $p ) );
	$start = ($p - 1) * $items_per_page;
	return array($start, $p, $maxpage);
}

// END make_page() FUNCTION









// THIS FUNCTION BUMPS LOGIN LOG
// INPUT:
// OUTPUT: 

function bumplog()
{
	global $database;
	$log_entries = $database->database_num_rows($database->database_query("SELECT login_id FROM se_logins"));
	if( $log_entries > 1000 )
  {
	  $oldest_log = $database->database_fetch_assoc($database->database_query("SELECT login_id FROM se_logins ORDER BY login_id ASC LIMIT 0,1"));
	  $database->database_query("DELETE FROM se_logins WHERE login_id='{$oldest_log['login_id']}'");
	  bumplog();
	}
}

// END bumplog() FUNCTION









// THIS FUNCTION RETURNS A RANDOM CODE OF DEFAULT LENGTH 8
// INPUT: $len (OPTIONAL) REPRESENTING THE LENGTH OF THE RANDOM STRING
// OUTPUT: A RANDOM ALPHANUMERIC STRING

function randomcode($len=8)
{
	$code = NULL;
	for( $i=0; $i<$len; $i++ )
  {
	  $char = chr(rand(48,122));
	  while( !ereg("[a-zA-Z0-9]", $char) )
    {
	    if( $char == $lchar ) continue;
	    $char = chr(rand(48,90));
	  }
	  $pass .= $char;
	  $lchar = $char;
	}
	return $pass;
}

// END randomcode() FUNCTION









// THIS FUNCTION CHECKS IF PROVIDED STRING IS AN EMAIL ADDRESS
// INPUT: $email REPRESENTING THE EMAIL ADDRESS TO CHECK
// OUTPUT: TRUE/FALSE DEPENDING ON WHETHER THE EMAIL ADDRESS IS VALIDLY CONSTRUCTED

function is_email_address($email)
{
	$regexp = "/^[a-z0-9]+([a-z0-9_\+\\.-]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i";
	return (bool) preg_match($regexp, $email);
}

// END is_email_address() FUNCTION









// THIS FUNCTION SETS STR_IREPLACE IF FUNCTION DOESN'T EXIST
// INPUT: $search REPRESENTING THE STRING TO SEARCH FOR
//	  $replace REPRESENTING THE STRING TO REPLACE IT WITH
//	  $subject REPRESENTING THE STRING WITHIN WHICH TO SEARCH
// OUTPUT: RETURNS A STRING IN WHICH ONE STRING HAS BEEN CASE-INSENSITIVELY REPLACED BY ANOTHER

if( !function_exists('str_ireplace') )
{
  function str_ireplace($search, $replace, $subject)
  {
    $search = preg_quote($search, "/");
    return preg_replace("/".$search."/i", $replace, $subject); 
  } 
}

// END str_ireplace() FUNCTION









// THIS FUNCTION SETS HTMLSPECIALCHARS_DECODE IF FUNCTION DOESN'T EXIST
// INPUT: $text REPRESENTING THE TEXT TO DECODE
//	  $ent_quotes (OPTIONAL) REPRESENTING WHETHER TO REPLACE DOUBLE QUOTES, ETC
// OUTPUT: A STRING WITH HTML CHARACTERS DECODED

if( !function_exists('htmlspecialchars_decode') )
{
  function htmlspecialchars_decode($text, $ent_quotes = ENT_COMPAT)
  {
    if( $ent_quotes === ENT_QUOTES   ) $text = str_replace("&quot;", "\"", $text);
    if( $ent_quotes !== ENT_NOQUOTES ) $text = str_replace("&#039;", "'", $text);
    $text = str_replace("&lt;", "<", $text);
    $text = str_replace("&gt;", ">", $text);
    $text = str_replace("&amp;", "&", $text);
    return $text;
  }
}

// END htmlspecialchars() FUNCTION









// THIS FUNCTION SETS STR_SPLIT IF FUNCTION DOESN'T EXIST
// INPUT: $string REPRESENTING THE STRING TO SPLIT
//	  $split_length (OPTIONAL) REPRESENTING WHERE TO CUT THE STRING
// OUTPUT: AN ARRAY OF STRINGS 
if( !function_exists('str_split') )
{
  function str_split($string, $split_length = 1)
  {
    $count = strlen($string);
    if($split_length < 1)
    {
      return false;
    }
    elseif($split_length > $count)
    {
      return array($string);
    }
    else
    {
      $num = (int)ceil($count/$split_length);
      $ret = array();
      for($i=0;$i<$num;$i++)
      {
        $ret[] = substr($string,$i*$split_length,$split_length);
      }
      return $ret;
    }
  }
}

// END str_split() FUNCTION









// THIS FUNCTION STRIPSLASHES AND ENCODES HTML ENTITIES FOR SECURITY PURPOSES
// INPUT: $value REPRESENTING A STRING OR ARRAY TO CLEAN
// OUTPUT: THE ARRAY OR STRING WITH HTML CHARACTERS ENCODED

function security($value)
{
	if( is_array($value) )
  {
	  $value = array_map('security', $value);
	}
  else
  {
	  if( !get_magic_quotes_gpc() )
    {
	    $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
	  }
    else
    {
	    $value = htmlspecialchars(stripslashes($value), ENT_QUOTES, 'UTF-8');
	  }
	  $value = str_replace("\\", "\\\\", $value);
	}
	return $value;
}

// END security() FUNCTION









// THIS FUNCTION LINKS FIELD VALUES
// INPUT: $field_value REPRESENTING THE VALUE TO LINK
//	  $key (NEEDED TO USE ARRAY WALK)
//	  $additional REPRESENTING THE ADDITIONAL PARAMETERS
// OUTPUT: 
function link_field_values(&$field_value, $key, $additional)
{
	global $url;

	$field_id = $additional[0];
	$field_browse = $additional[1];
	$field_link = $additional[2];
	$field_display = $additional[3];
	$field_value = trim($field_value);
	
  if( !trim($field_link) && $field_display == 2 )
  {
	  if( !$field_browse ) $field_browse = urlencode(htmlspecialchars_decode($field_value, ENT_QUOTES));
	  $browse_url = $url->url_base."search_advanced.php?task=browse&field_id={$field_id}&field_value={$field_browse}";
	  if( $field_value ) $field_value = "<a href='{$browse_url}'>{$field_value}</a>";
	}
  elseif( trim($field_link) && $field_value )
  {
	  if( preg_match('/^www([.]?[a-zA-Z0-9_\/-])*/', $field_value) ) $field_link = "http://".$field_value;
	  $link_to = str_replace("[field_value]", $field_value, $field_link);
	  $field_value = "<a href='{$link_to}' target='_blank'>{$field_value}</a>"; 
	}
}

// END link_field_values() FUNCTION









// THIS FUNCTION CENSORS WORDS FROM A STRING
// INPUT: $field_value REPRESENTING THE VALUE TO CENSOR
// OUTPUT: THE VALUE WITH BANNED WORDS CENSORED

function censor($field_value)
{
	global $setting;

	$censored_array = explode(",", trim($setting['setting_banned_words']));
	foreach($censored_array as $key => $value)
	{
		$trimvalue = trim($value);
		if (preg_match("/\b".$trimvalue."\b/i", $field_value)){
			$replace_value = str_pad("", strlen(trim($value)), "*");
			$field_value   = preg_replace("/\b".$trimvalue."\b/i", $replace_value, $field_value);
		}
	}


	return $field_value;
} 

// END censor() FUNCTION









// THIS FUNCTION RETURNS THE SIZE OF A DIRECTORY
// INPUT: $dirname REPRESENTING THE PATH TO A DIRECTORY
// OUTPUT: THE SIZE OF ALL THE FILES WITHIN THE DIRECTORY

function dirsize($dirname)
{
	if( !is_dir($dirname) || !is_readable($dirname) )
    return false;
  
	$dirname_stack[] = $dirname;
	$size = 0;

	do {
	  $dirname = array_shift($dirname_stack);
	  $handle = opendir($dirname);
	  while(false !== ($file = readdir($handle)))
    {
	    if($file != '.' && $file != '..' && is_readable($dirname . DIRECTORY_SEPARATOR . $file))
      {
	      if(is_dir($dirname . DIRECTORY_SEPARATOR . $file))
        {
	        $dirname_stack[] = $dirname . DIRECTORY_SEPARATOR . $file;
	      }
	      $size += filesize($dirname . DIRECTORY_SEPARATOR . $file);
	    }
	  }
	  closedir($handle);
	} while( count($dirname_stack) > 0 );

	return $size;
}

// END dirsize() FUNCTION









// THIS FUNCTION RETURNS TEXT CORRESPONDING TO THE GIVEN USER PRIVACY LEVEL
// INPUT: $privacy_level REPRESENTING THE LEVEL OF USER PRIVACY
// OUTPUT: A STRING EXPLAINING THE GIVEN PRIVACY SETTING

function user_privacy_levels($privacy_level)
{
	global $functions_general;

	switch($privacy_level)
  {
	  case 63: $privacy = 323; break;
	  case 31: $privacy = 324; break;
	  case 15: $privacy = 325; break;
	  case 7: $privacy = 326; break;
	  case 3: $privacy = 327; break;
	  case 1: $privacy = 328; break;
	  case 0: $privacy = 329; break;
	  default: $privacy = ""; break;
	}

	return $privacy;
}

// END user_privacy_levels() FUNCTION









// THIS FUNCTION SEARCHES THROUGH PROFILE INFORMATION
// INPUT: 
// OUTPUT:

function search_profile()
{
	global $database, $url, $results_per_page, $p, $search_text, $t, $search_objects, $results, $total_results;

	// GET FIELDS
	$fields = $database->database_query("
    SELECT
      profilefield_id AS field_id,
      profilefield_type AS field_type,
      profilefield_options AS field_options
    FROM
      se_profilefields
    WHERE
      profilefield_type<>'5' &&
      (profilefield_dependency<>'0' OR (profilefield_dependency='0' AND profilefield_display<>'0'))
  ");
  
	$profile_query = "se_users.user_username LIKE '%{$search_text}%' OR CONCAT(se_users.user_fname, ' ', se_users.user_lname) LIKE '%{$search_text}%'";
  
	// LOOP OVER FIELDS
	while($field_info = $database->database_fetch_assoc($fields))
  {
	  // TEXT FIELD OR TEXTAREA
	  if( $field_info['field_type'] == 1 || $field_info['field_type'] == 2 )
    {
	    if( $profile_query ) $profile_query .= " OR ";
	    $profile_query .= "`se_profilevalues`.`profilevalue_{$field_info['field_id']}` LIKE '%{$search_text}%'";
    }
    
	  // RADIO OR SELECT BOX
	  elseif($field_info[field_type] == 3 || $field_info[field_type] == 4)
    {
	    $options = unserialize($field_info['field_options']);
 	    $langids = Array();
	    $cases = Array();
	    for($i=0,$max=count($options);$i<$max;$i++)
      { 
	      $cases[] = "WHEN languagevar_id='{$options[$i]['label']}' THEN {$options[$i]['value']}";
	      $langids[] = $options[$i][label]; 
	    }
	    if(count($cases) != 0)
      {
	      if( $profile_query ) $profile_query .= " OR ";
	      $profile_query .= "`se_profilevalues`.`profilevalue_{$field_info['field_id']}` IN (SELECT CASE ".implode(" ", $cases)." END AS value FROM se_languagevars WHERE languagevar_id IN (".implode(", ", $langids).") AND languagevar_value LIKE '%{$search_text}%')";
	    }
    }
    
	  // CHECKBOX
	  elseif($field_info[field_type] == 6)
    {
	    $options = unserialize($field_info['field_options']);
 	    $langids = Array();
	    $cases = Array();
	    for($i=0,$max=count($options);$i<$max;$i++)
      { 
	      $cases[] = "WHEN languagevar_id='{$options[$i]['label']}' THEN ".(pow(2, $i));
	      $langids[] = $options[$i][label]; 
	    }
	    if(count($cases) != 0)
      {
	      if( $profile_query ) $profile_query .= " OR ";
	      $profile_query .= "`se_profilevalues`.`profilevalue_{$field_info['field_id']}` & (SELECT sum(CASE ".implode(" ", $cases)." END) AS value FROM se_languagevars WHERE languagevar_id IN (".implode(", ", $langids).") AND languagevar_value LIKE '%{$search_text}%')";
	    }
	  }
	}

	// CONSTRUCT QUERY
	$profile_query = "
    SELECT
      se_users.user_id,
      se_users.user_username,
      se_users.user_fname,
      se_users.user_lname,
      se_users.user_photo
    FROM
      se_profilevalues
    LEFT JOIN
      se_users
      ON se_profilevalues.profilevalue_user_id=se_users.user_id
    LEFT JOIN
      se_levels
      ON se_levels.level_id=se_users.user_level_id
    WHERE
      se_users.user_verified='1' AND
      se_users.user_enabled='1' AND
      (se_users.user_search='1' OR se_levels.level_profile_search='0') AND
      ($profile_query)
  ";

	// GET TOTAL PROFILES
	$total_profiles = $database->database_num_rows($database->database_query($profile_query." LIMIT 201"));

	// IF NOT TOTAL ONLY
	if($t == "0")
  {
	  // MAKE PROFILE PAGES
	  $start = ($p - 1) * $results_per_page;
	  $limit = $results_per_page+1;
    
	  // SEARCH PROFILES
	  $online_users_array = online_users();
	  $profiles = $database->database_query($profile_query." ORDER BY se_users.user_id DESC LIMIT $start, $limit");
	  while($profile_info = $database->database_fetch_assoc($profiles))
    {
	    // CREATE AN OBJECT FOR USER
	    $profile = new se_user();
	    $profile->user_info['user_id'] = $profile_info['user_id'];
	    $profile->user_info['user_username'] = $profile_info['user_username'];
	    $profile->user_info['user_fname'] = $profile_info['user_fname'];
	    $profile->user_info['user_lname'] = $profile_info['user_lname'];
	    $profile->user_info['user_photo'] = $profile_info['user_photo'];
	    $profile->user_displayname();
      
	    // DETERMINE IF USER IS ONLINE
	    $is_online = (bool) in_array($profile_info['user_username'], $online_users_array[0]);
      
	    $results[] = Array(
        'result_url' => $url->url_create('profile', $profile_info['user_username']),
        'result_icon' => $profile->user_photo('./images/nophoto.gif', TRUE),
        'result_name' => 509,
        'result_name_1' => $profile->user_displayname,
        'result_desc' => '',
        'result_online' => $is_online
      );
	  }
    
	  // SET TOTAL RESULTS
	  $total_results = $total_profiles;
	}
  
	// SET ARRAY VALUES
	SE_Language::_preload_multi(509, 1072);
	if($total_profiles > 200) { $total_profiles = "200+"; }
	$search_objects[] = Array(
    'search_type' => '0',
    'search_lang' => 1072,
    'search_total' => $total_profiles
  );
}

// END search_profile() FUNCTION









// THIS FUNCTION RETURNS TIME IN SECONDS WITH MICROSECONDS
// INPUT:
// OUTPUT: RETURNS THE TIME IN SECONDS WITH MICROSECONDS

if( !function_exists('getmicrotime') )
{
  function getmicrotime()
  {
          list($usec, $sec) = explode(" ",microtime());
          return ((float)$usec + (float)$sec);
  }
}

// END getmicrotime() FUNCTION













// THIS FUNCTION CLEANS HTML TAGS FROM TEXT
// INPUT: $text REPRESENTING THE STRING TO CLEAN
//	  $allowable_tags REPRESENTING THE ALLOWABLE HTML TAGS (AS A COMMA-DELIMITED STRING)
//	  $forbidden_attr (OPTIONAL) REPRESENTING AND ARRAY OF ANY ADDITIONAL FORBIDDEN ATTRIBUTES (SUCH AS A STYLE TAG)
// OUTPUT: THE CLEANED TEXT

function cleanHTML($text, $allowable_tags = null, $forbidden_attr = null)
{
  // INCLUDE FILTER CLASS
  if( !class_exists("InputFilter") )
  {
    require(SE_ROOT."/include/class_inputfilter.php");
  }

  // New method
  if( !method_exists('InputFilter', 'safeSQL') )
  {
    return InputFilter::process($text, array(
      'allowedTags' => $allowable_tags,
      'forbiddenAttributes' => $forbidden_attr,
    ));
  }
  
  // Old method
  else
  {
    // INSTANTIATE INPUT FILTER CLASS WITH APPROPRIATE TAGS
    $xssFilter = new InputFilter(explode(",", str_replace(" ", "", $allowable_tags)), "", 0, 1, 1);

    // ADD NECESSARY BLACKLIST ITEMS
    for($i=0;$i<count($forbidden_attr);$i++)
    {
      $xssFilter->attrBlacklist[] = $forbidden_attr[$i];
    }

    // RETURN PROCESSED TEXT
    return $xssFilter->process($text);
  }
}

// END cleanHTML() FUNCTION













// THIS FUNCTION TRIMS A GIVEN STRING PRESERVING HTML
// INPUT: $string REPRESENTING THE STRING TO SHORTEN
//	  $start REPRESENTING THE CHARACTER TO START WITH
//	  $length REPRESENTING THE LENGTH OF THE STRING TO RETURN
// OUTPUT: THE CLEANED TEXT

function chopHTML($string, $start, $length=false)
{
  $pattern = '/(\[\w+[^\]]*?\]|\[\/\w+\]|<\w+[^>]*?>|<\/\w+>)/i';
  $clean = preg_replace($pattern, chr(1), $string);

  if(!$length)
      $str = substr($clean, $start);
  else {
      $str = substr($clean, $start, $length);
      $str = substr($clean, $start, $length + substr_count($str, chr(1)));
  }
  $pattern = str_replace(chr(1),'(.*?)',preg_quote($str));
  if(preg_match('/'.$pattern.'/is', $string, $matched))
      return $matched[0];
  return $string;
}

// END chopHTML() FUNCTION










// THIS FUNCTION CHOPS A GIVEN STRING AND INSERTS A STRING AT THE END OF EACH CHOP
// INPUT: $string REPRESENTING THE STRING TO CHOP
//        $length REPRESENTING THE LENGTH OF EACH SEGMENT
//        $insert_char REPRESENTING THE STRING TO INSERT AT THE END OF EACH SEGMENT

function choptext($string, $length=32, $insert_char=' ')
{
  return preg_replace("!(?:^|\s)([\w\!\?\.]{" . $length . ",})(?:\s|$)!e",'chunk_split("\\1",' . $length . ',"' . $insert_char. '")',$string);
}

// END choptext() FUNCTION










// THIS FUNCTION CHOPS A GIVEN STRING AND INSERTS A STRING AT THE END OF EACH CHOP (PRESERVING HTML ENTITIES)
// INPUT: $html REPRESENTING THE STRING TO CHOP
//        $size REPRESENTING THE LENGTH OF EACH SEGMENT
//        $delim REPRESENTING THE STRING TO INSERT AT THE END OF EACH SEGMENT

function chunkHTML_split($html, $size, $delim)
{
  $pos=0;
  for($i=0;$i<strlen($html);$i++)
  {
    if($pos >= $size && !$unsafe)
    {
      $out .= $delim;
      $unsafe = 0;
      $pos = 0;
    }
    $c = substr($html,$i,1);
    if($c == "&")
      $unsafe = 1;
    elseif($c == ";")
      $unsafe = 0;
    $out .= $c;
    $pos++;
  }
  return $out;
}

// END chunkHTML_split










// THIS FUNCTION RETURNS THE LENGTH OF A STRING, ACCOUNTING FOR UTF8 CHARS
// INPUT: $str REPRESENTING THE STRING
// OUTPUT: THE LENGTH OF THE STRING

function strlen_utf8($str)
{
  $i = 0;
  $count = 0;
  $len = strlen($str);
  while($i < $len)
  {
    $chr = ord ($str[$i]);
    $count++;
    $i++;
    if($i >= $len)
      break;
    
    if($chr & 0x80)
    {
      $chr <<= 1;
      while ($chr & 0x80)
      {
        $i++;
        $chr <<= 1;
      }
    }
  }
  return $count;
}

// END strlen_utf8() FUNCTION










// THIS FUNCTION MAKES UTF8 CHARS WORK IN SERIALIZE BY BASICALLY IGNORING THE STRING LENGTH PARAM
// INPUT: $str REPRESENTING THE SERIALIZED STRING
// OUTPUT: THE UNSERIALIZED DATA

function mb_unserialize($serial_str)
{
  $out = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $serial_str );
  return unserialize($out);
} 

// END mb_unserialize() FUNCTION








// THIS FUNCTION RETURNS AN ARRAY CONTAINING THE USERNAMES OF ONLINE USERS
// INPUT:
// OUTPUT: AN ARRAY OF USERNAMES FOR USERS CURRENTLY ACTIVE IN THE SYSTEM

function online_users()
{
	global $database;
  
  $online_array = NULL;
  
  // CACHING
  $cache_object = SECache::getInstance('serial');
  if( is_object($cache_object) )
  {
    $online_array = $cache_object->get('online_users');
  }
  
  if( !is_array($online_array) )
  {
    $total_visitors = 0;
    $onlineusers_array = array();
    $onlineusers_usernames = array();
    $online_time = time() - (10 * 60);
    
    $sql = "SELECT visitor_user_id AS user_id, visitor_user_username AS user_username, visitor_user_displayname AS user_displayname FROM se_visitors WHERE visitor_invisible=0 && visitor_lastactive>'{$online_time}' ORDER BY visitor_lastactive DESC LIMIT 2000";
    $resource = $database->database_query($sql);
    
    while( $online_user_info = $database->database_fetch_assoc($resource) )
    {
      // THIS IS A USER
      if( !empty($online_user_info['user_id']) )
      {
        if( in_array($online_user_info['user_username'], $onlineusers_usernames) ) continue;
        
        $online_user = new se_user();
        $online_user->user_info['user_id']          = $online_user_info['user_id'];
        $online_user->user_info['user_username']    = $online_user_info['user_username'];
        $online_user->user_info['user_displayname'] = $online_user_info['user_displayname'];
        $online_user->user_displayname              = $online_user_info['user_displayname'];
        
        $onlineusers_array[] = $online_user;
        $onlineusers_usernames[] = $online_user->user_info['user_username'];
      }
      
      // THIS IS A VISITOR
      else
      {
        $total_visitors++;
      }
    }
    
    $online_array = array($onlineusers_array, $total_visitors, $onlineusers_usernames);
    
    // CACHE
    if( is_object($cache_object) )
    {
      $cache_object->store($online_array, 'online_users');
    }
  }
  
	return $online_array;
}

// END online_users() FUNCTION









// THIS FUNCTION RETURNS AN ARRAY CONTAINING SITE STATISTICS
// INPUT:
// OUTPUT: AN ARRAY OF STATISTICS
function site_statistics()
{
  global $setting, $database, $database_name;
  
  $statistics = NULL;
  
  // CACHING
  $cache_object = SECache::getInstance('serial');
  if( is_object($cache_object) )
  {
    $statistics = $cache_object->get('site_statistics');
  }
  
  // RETRIEVAL
  //if( !is_array($statistics) || empty($statistics) )
  if( !is_array($statistics) )
  {
    $statistics = array();
    
    // Get default stats
    $total_members = $database->database_fetch_assoc($database->database_query("SELECT count(*) AS total_members FROM se_users"));
    $statistics['members'] = array(
      'title' => 661,
      'stat'  => (int) ( isset($total_members['total_members']) ? $total_members['total_members'] : 0 )
    );
    
    if( $setting['setting_connection_allow'] )
    {
      $total_friends = $database->database_fetch_assoc($database->database_query("SELECT count(*) AS total_friends FROM se_friends WHERE friend_status='1'"));
      $statistics['friends'] = array(
        'title' => 662,
        'stat'  => (int) ( isset($total_friends['total_friends']) ? $total_friends['total_friends'] : 0 )
      );
    }
    
    $total_comments = 0;
    $comment_tables = $database->database_query("SHOW TABLES FROM `{$database_name}` LIKE 'se_%comments'");
    while($table_info = $database->database_fetch_array($comment_tables))
    {
      $comment_type = strrev(substr(strrev(substr($table_info[0], 3)), 8));
      $table_comments = $database->database_fetch_assoc($database->database_query("SELECT count(*) AS total_comments FROM `se_{$comment_type}comments`"));
      $total_comments += $table_comments['total_comments'];
    }
    
    $statistics['comments'] = array(
      'title' => 663,
      'stat'  => (int) $total_comments
    );
    
    /*
    $total_media = 0;
    $media_tables = $database->database_query("SHOW TABLES FROM `{$database_name}` LIKE 'se_%media'");
    while($table_info = $database->database_fetch_array($media_tables))
    {
      $comment_type = strrev(substr(strrev(substr($table_info[0], 3)), 8));
      $table_media = $database->database_fetch_assoc($database->database_query("SELECT count(*) AS total_media FROM se_{$comment_type}media"));
      $total_media += $total_media['total_media'];
    }
    
    $statistics['media'] = array(
      'title' => 663, // TODO
      'stat'  => (int) $total_media
    );
    */
    
    /*
    $total_mediatags = 0;
    $mediatag_tables = $database->database_query("SHOW TABLES FROM `{$database_name}` LIKE 'se_%mediatags'");
    while($table_info = $database->database_fetch_array($media_tables))
    {
      $comment_type = strrev(substr(strrev(substr($table_info[0], 3)), 8));
      $table_mediatags = $database->database_fetch_assoc($database->database_query("SELECT count(*) AS total_mediatags FROM se_{$comment_type}mediatags"));
      $total_mediatags += $total_mediatags['total_mediatags'];
    }
    
    $statistics['mediatags'] = array(
      'title' => 663, // TODO
      'stat'  => (int) $total_mediatags
    );
    */
    
    // CALL HOOK
    // COMMENT OUT THIS NEXT LINE IF YOU ONLY WANT THE BASIC STATISTICS
    ($hook = SE_Hook::exists('se_site_statistics')) ? SE_Hook::call($hook, array('statistics' => &$statistics)) : NULL;
    
    // CACHE
    if( is_object($cache_object) )
    {
      $cache_object->store($statistics, 'site_statistics');
    }
  }
  
  // Load language
  foreach( $statistics as $stat )
  {
    SE_Language::_preload($stat['title']);
  }
  
  return $statistics;
}

// END site_statistics() FUNCTION









// THIS FUNCTION RETURNS AN ARRAY CONTAINING THE USERS THAT RECENTLY SIGNED UP
// INPUT:
// OUTPUT:
function recent_signups()
{
  global $setting, $database;
  
  $signups = NULL;
  
  // CACHING
  $cache_object = SECache::getInstance('serial');
  if( is_object($cache_object) )
  {
    $signups = $cache_object->get('recent_signups');
  }
  
  // RETRIEVAL
  //if( !is_array($signups) || empty($signups) )
  if( !is_array($signups) )
  {
    $sql = "SELECT user_id, user_username, user_fname, user_lname, user_photo FROM se_users WHERE user_verified='1' AND user_enabled='1' AND user_search='1' AND user_photo<>'' ORDER BY user_signupdate DESC LIMIT 20";
    $resource = $database->database_query($sql);
    
    $signups = array();
    while( $user_info = $database->database_fetch_assoc($resource) )
    {
      $signup_user = new se_user();
      $signup_user->user_info['user_id'] = $user_info['user_id'];
      $signup_user->user_info['user_username'] = $user_info['user_username'];
      $signup_user->user_info['user_photo'] = $user_info['user_photo'];
      $signup_user->user_info['user_fname'] = $user_info['user_fname'];
      $signup_user->user_info['user_lname'] = $user_info['user_lname'];
      $signup_user->user_displayname();
      
      $signups[] =& $signup_user;
      unset($signup_user);
    }
    
    // CACHE
    if( is_object($cache_object) )
    {
      $cache_object->store($signups, 'recent_signups');
    }
  }
  
  return $signups;
}

// END recent_signups() FUNCTION









// THIS FUNCTION RETURNS AN ARRAY CONTAINING THE MOST RECENTLY LOGGED IN USERS
// INPUT:
// OUTPUT:
function recent_logins()
{
  global $setting, $database;
  
  $logins = NULL;
  
  // CACHING
  $cache_object = SECache::getInstance('serial');
  if( is_object($cache_object) )
  {
    $logins = $cache_object->get('recent_logins');
  }
  
  // RETRIEVAL
  //if( !is_array($logins) || empty($logins) )
  if( !is_array($logins) )
  {
    $sql = "SELECT user_id, user_username, user_fname, user_lname, user_photo FROM se_users WHERE user_photo<>'' AND user_search='1' ORDER BY user_lastlogindate DESC LIMIT 20";
    $resource = $database->database_query($sql);
    
    $logins = array();
    while( $user_info = $database->database_fetch_assoc($resource) )
    {
      $login_user = new se_user();
      $login_user->user_info['user_id'] = $user_info['user_id'];
      $login_user->user_info['user_username'] = $user_info['user_username'];
      $login_user->user_info['user_photo'] = $user_info['user_photo'];
      $login_user->user_info['user_fname'] = $user_info['user_fname'];
      $login_user->user_info['user_lname'] = $user_info['user_lname'];
      $login_user->user_displayname();
      
      $logins[] =& $login_user;
      unset($login_user);
    }
    
    // CACHE
    if( is_object($cache_object) )
    {
      $cache_object->store($logins, 'recent_logins');
    }
  }
  
  return $logins;
}

// END recent_logins() FUNCTION









// THIS FUNCTION RETURNS AN ARRAY CONTAINING THE MOST POPULAR USERS
// INPUT:
// OUTPUT:
function popular_users()
{
  global $setting, $database;
  
  $popular_users = NULL;
  
  // CACHING
  $cache_object = SECache::getInstance('serial');
  if( is_object($cache_object) )
  {
    $popular_users = $cache_object->get('popular_users');
  }
  
  // RETRIEVAL
  //if( !is_array($popular_users) || empty($popular_users) )
  if( !is_array($popular_users) )
  {
    $sql = "SELECT count(se_friends.friend_user_id2) AS num_friends, se_users.user_id, se_users.user_username, se_users.user_fname, se_users.user_lname, se_users.user_photo FROM se_friends LEFT JOIN se_users ON se_friends.friend_user_id1=se_users.user_id WHERE se_friends.friend_status='1' AND se_users.user_search='1' GROUP BY se_users.user_id ORDER BY num_friends DESC LIMIT 20";
    $resource = $database->database_query($sql);
    
    $popular_users = array();
    while( $user_info = $database->database_fetch_assoc($resource) )
    {
      $popular_user = new se_user();
      $popular_user->user_info['user_id'] = $user_info['user_id'];
      $popular_user->user_info['user_username'] = $user_info['user_username'];
      $popular_user->user_info['user_photo'] = $user_info['user_photo'];
      $popular_user->user_info['user_fname'] = $user_info['user_fname'];
      $popular_user->user_info['user_lname'] = $user_info['user_lname'];
      $popular_user->user_displayname();
      
      $popular_users[] = array(
        'friend' => &$popular_user,
        'total_friends' => $user_info['num_friends']
      );
      
      unset($popular_user);
    }
    
    // CACHE
    if( is_object($cache_object) )
    {
      $cache_object->store($popular_users, 'popular_users');
    }
  }
  
  return $popular_users;
}

// END popular_users() FUNCTION









// THIS FUNCTION RETURNS AN ARRAY CONTAINING THE MOST POPULAR USERS
// INPUT:
// OUTPUT:
function site_news()
{
  global $setting, $database;
  
  $news = NULL;
  
  // CACHING
  $cache_object = SECache::getInstance('serial');
  if( is_object($cache_object) )
  {
    $news = $cache_object->get('site_news');
  }
  
  // RETRIEVAL
  //if( !is_array($news) || empty($news) )
  if( !is_array($news) )
  {
    $sql = "SELECT * FROM se_announcements ORDER BY announcement_order DESC LIMIT 20";
    $resource = $database->database_query($sql);
    
    $news = array();
    while( $news_info = $database->database_fetch_assoc($resource) )
    {
      // CONVERT SUBJECT/BODY BACK TO HTML
      $news_info['announcement_body'] = htmlspecialchars_decode($news_info['announcement_body'], ENT_QUOTES);
      $news_info['announcement_subject'] = htmlspecialchars_decode($news_info['announcement_subject'], ENT_QUOTES);
      $news[] = $news_info;
    }
    
    // CACHE
    if( is_object($cache_object) )
    {
      $cache_object->store($news, 'site_news');
    }
  }
  
  return $news;
}

// END site_news() FUNCTION









// THIS FUNCTION RETURNS AN ARRAY CONTAINING THE USERS FRIENDS BIRTHDAYS INFO
// INPUT:
// OUTPUT:

function friends_birthdays()
{
  global $setting, $database, $user;
  
  $birthdays = NULL;
  
  // CACHING
  $cache_object = SECache::getInstance('serial');
  if( is_object($cache_object) )
  {
    $birthdays = $cache_object->get('friends_birthdays_user_'.$user->user_info['user_id']);
  }
  
  
  // RETRIEVAL
  //if( !is_array($birthdays) || empty($birthdays) )
  if( !is_array($birthdays) )
  {
    $birthdays = array();
    
    $sql = "SELECT profilefield_id, t2.profilecat_id FROM se_profilefields LEFT JOIN se_profilecats AS t1 ON se_profilefields.profilefield_profilecat_id=t1.profilecat_id LEFT JOIN se_profilecats AS t2 ON t1.profilecat_dependency=t2.profilecat_id WHERE profilefield_special='1'";
    $resource = $database->database_query($sql);
    
    if( $database->database_num_rows($resource) > 0 )
    {
      // CONSTRUCT QUERY
      $birthdays_upcoming_query = "
        SELECT
          se_users.user_id, 
          se_users.user_username, 
          se_users.user_fname, 
          se_users.user_lname,
          CASE
      ";
      
      while( $birthday_field = $database->database_fetch_assoc($resource) )
      {
        $birthdays_upcoming_query .= " WHEN se_users.user_profilecat_id='{$birthday_field['profilecat_id']}' THEN DATE_FORMAT(CONCAT(YEAR(CURDATE()), \"-\", MONTH(se_profilevalues.`profilevalue_{$birthday_field['profilefield_id']}`), \"-\", DAY(se_profilevalues.`profilevalue_{$birthday_field['profilefield_id']}`)), '%Y-%m-%d')";
        $birthdays_upcoming_where[] = "(se_users.user_profilecat_id='{$birthday_field['profilecat_id']}' AND DAY(se_profilevalues.`profilevalue_{$birthday_field['profilefield_id']}`)<>'0' AND MONTH(se_profilevalues.`profilevalue_{$birthday_field['profilefield_id']}`)<>'0' AND CURDATE() <= DATE_FORMAT(CONCAT(YEAR(CURDATE()), \"-\", MONTH(se_profilevalues.`profilevalue_{$birthday_field['profilefield_id']}`), \"-\", DAY(se_profilevalues.`profilevalue_{$birthday_field['profilefield_id']}`)), '%Y-%m-%d') AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) >= DATE_FORMAT(CONCAT(YEAR(CURDATE()), \"-\", MONTH(se_profilevalues.`profilevalue_{$birthday_field['profilefield_id']}`), \"-\", DAY(se_profilevalues.`profilevalue_{$birthday_field['profilefield_id']}`)), '%Y-%m-%d'))";
      }
      
      $birthdays_upcoming_query .= " ELSE '0000-00-00' END AS birthday FROM se_friends LEFT JOIN se_users ON se_friends.friend_user_id2=se_users.user_id LEFT JOIN se_profilevalues ON se_users.user_id=se_profilevalues.profilevalue_user_id WHERE se_friends.friend_user_id1='{$user->user_info['user_id']}' AND (".implode(" OR ", $birthdays_upcoming_where).") ORDER BY birthday";
      
      $resource = $database->database_query($birthdays_upcoming_query);
      
      while( $birthday_info = $database->database_fetch_assoc($resource) )
      {
        $birthday_user = new se_user();
        $birthday_user->user_info['user_id'] = $birthday_info['user_id'];
        $birthday_user->user_info['user_username'] = $birthday_info['user_username'];
        $birthday_user->user_info['user_fname'] = $birthday_info['user_fname'];
        $birthday_user->user_info['user_lname'] = $birthday_info['user_lname'];
        $birthday_user->user_displayname();
        
        // SET BIRTHDAY
        $birthday_date = mktime(0, 0, 0, substr($birthday_info['birthday'], 5, 2), substr($birthday_info['birthday'], 8, 2), 1990);
        
        $birthdays[] = array(
          'birthday_user_id' => $birthday_user->user_info['user_id'],
          'birthday_user_username' => $birthday_user->user_info['user_username'],
          'birthday_user_displayname' => $birthday_user->user_displayname,
          'birthday_date' => $birthday_date,
          'birthday_user' => &$birthday_user
        );
        
        unset($birthday_user);
      }
    }
    
    // CACHE
    if( is_object($cache_object) )
    {
      $cache_object->store($birthdays, 'friends_birthdays_user_'.$user->user_info['user_id']);
    }
  }
  
  return $birthdays;
}

// END friends_birthdays() FUNCTION





function get_simple_cookie_domain($host = null)
{
  // Quick config
  if( defined('SE_COOKIE_DOMAIN') )
  {
    return SE_COOKIE_DOMAIN;
  }
  
  if( !$host )
  {
    $host = $_SERVER["HTTP_HOST"];
  }
  
  $host = parse_url($host);
  $host = $host['path'];
  $parts = explode('.', $host);
  
  switch( TRUE )
  {
    // Do not use custom for these:
    // IP Address
    case ( preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $host) ):
    // Intranet host
    case ( count($parts) === 1 ):
      return null;
      break;
    
    // Second level ccld
    case ( strlen($parts[count($parts)-1]) == 2 && strlen($parts[count($parts)-2]) <= 3 ):
      array_splice($parts, 0, count($parts) - 3);
      return join('.', $parts);
      break;
    
    // tld or first-level ccld
    default:
      array_splice($parts, 0, count($parts) - 2);
      return join('.', $parts);
  }
  
  return null;
}




function check_ip_in_banned_list($ip, $list)
{
  // We were passed a string address
  if( !is_numeric($ip) )
  {
    $ip = ip2long($ip);
  }

  // Wth it's not an ip!
  if( !$ip )
  {
    trigger_error('That isn\'t an IP address!', E_USER_NOTICE);
    return false;
  }

  // Passed a , separated string
  if( !is_array($list) )
  {
    $list = explode(',', $list);
  }

  // No banned ips
  if( empty($list) )
  {
    return false;
  }

  // Sort into banned specific IPs and ranges
  $banned_ip_addr = array();
  $banned_ip_ranges = array();

  foreach( $list as $banned_ip )
  {
    if( strpos($banned_ip, '*') !== false )
    {
      // Decode
      $tmp_low = ip2long(str_replace('*', '0', $banned_ip));
      $tmp_high = ip2long(str_replace('*', '255', $banned_ip)); // this might not work for some bytes

      // If failed to decode, or low is larger than high, skip
      if( !$tmp_low || !$tmp_high || $tmp_low > $tmp_high )
      {
        continue;
      }
      
      // Add
      $banned_ip_ranges[] = array(
        $tmp_low,
        $tmp_high
      );
    }
    else if( strpos($banned_ip, '-') !== false )
    {
      // Decode
      list($tmp_low, $tmp_high) = explode('-', $banned_ip, 2);
      $tmp_low = ip2long($tmp_low);
      $tmp_high = ip2long($tmp_high);

      // If failed to decode, or low is larger than high, skip
      if( !$tmp_low || !$tmp_high || $tmp_low > $tmp_high )
      {
        continue;
      }

      // Add
      $banned_ip_ranges[] = array(
        $tmp_low,
        $tmp_high
      );
    }
    else
    {
      $tmp = ip2long($banned_ip);
      if( $tmp )
      {
        $banned_ip_addr[] = $tmp;
      }
    }
  }

  // Now check against ip lists
  if( in_array($ip, $banned_ip_addr) )
  {
    return true;
  }

  // Check against IP ranges
  foreach( $banned_ip_ranges as $range )
  {
    if( $ip >= $range[0] && $ip <= $range[1] )
    {
      return true;
    }
  }

  return false;
}
?>
