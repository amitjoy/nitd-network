<?php

//
//  THIS CLASS CONTAINS DATABASE-RELATED METHODS
//  IT IS USED TO CONNECT TO THE DATABASE, RUN QUERIES, AND REPORT ERRORS
//  CURRENTLY MySQL IS THE DATABASE TYPE, EVENTUALLY ADD SUPPORT FOR OTHER DATABASE TYPES
//
//  METHODS IN THIS CLASS:
//
//    se_database()
//    database_connect()
//    database_select()
//    database_query()
//    database_fetch_array()
//    database_fetch_assoc()
//    database_num_rows()
//    database_affected_rows()
//    database_set_charset()
//    database_real_escape_string()
//    database_insert_id()
//    database_error()
//    database_close()
//


define('SE_DATABASE_LOG_SUCCESS',   1);
define('SE_DATABASE_LOG_FAIL',      2);
define('SE_DATABASE_LOG_SLOW',      4);
define('SE_DATABASE_LOG_FAST',      8);
define('SE_DATABASE_LOG_ALL',       15);

define('SE_DATABASE_LOGOPTS_QUERY',     1);
define('SE_DATABASE_LOGOPTS_TIME',      2);
define('SE_DATABASE_LOGOPTS_BACKTRACE', 4);
define('SE_DATABASE_LOGOPTS_RESULT',    8);
define('SE_DATABASE_LOGOPTS_COUNT',     16);
define('SE_DATABASE_LOGOPTS_ERROR',     32);
define('SE_DATABASE_LOGOPTS_ALL',       63);




class SEDatabase
{
	// INITIALIZE VARIABLES
	var $database_connection;		                // VARIABLE REPRESENTING DATABASE LINK IDENTIFIER
  
  var $_last_query;
  
  var $_last_resource;
  
  // DEBUG VARIABLES
	var $log_stats = NULL;				              // VARIABLE DETERMINING WHETHER QUERY INFO SHOULD BE LOGGED
  var $log_trigger = SE_DATABASE_LOG_ALL;
  var $log_options = SE_DATABASE_LOGOPTS_ALL;
  
	var $log_data = array();			              // ARRAY CONTAINING RELEVANT INFORMATION ABOUT QUERIES RUN
	var $log_data_totals = array(               // ARRAY CONTAINING RELEVANT INFORMATION ABOUT QUERIES RUN
    'total' => 0,
    'time' => 0,
    'success' => 0,
    'failed' => 0,
    'count' => 0
  );
  var $log_slow_threshold = '0.05';
  
  // 1 - Index by query hash, 2 - index by backtrace
  var $log_stats_index_mode = 2;
  
  var $root_folder;
  
  var $query_thresholds = array(
    '0.0001'  => '#00ff00',
    '0.001'   => '#55ff00',
    '0.005'   => '#aaff00',
    '0.05'    => '#ffff00',
    '0.5'     => '#ffaa00',
    '1'       => '#ff5500',
    '99999'   => '#ff0000'
  );





  
  //
	// THIS METHOD CONNECTS TO THE SERVER AND SELECTS THE DATABASE
  //
	// INPUT:
  //    $database_host REPRESENTING THE DATABASE HOST
	//	  $database_username REPRESENTING THE DATABASE USERNAME
	//	  $database_password REPRESENTING THE DATABASE PASSWORD
	//	  $database_name REPRESENTING THE DATABASE NAME
  //
	// OUTPUT:
  //    void
  //
  
	function SEDatabase($database_host, $database_username, $database_password, $database_name)
  {
    global $user, $global_plugins;
    
    // GET THE SOCIALENGINE ROOT
    $this->root_folder = dirname(dirname(realpath(__FILE__)));
    
    // FORCE TIME AND QUERY LOGGING (IF ENABLED)
    if( is_null($this->log_stats) ) $this->log_stats = ( defined('SE_DEBUG') ? SE_DEBUG : FALSE );
    $this->log_options = ( $this->log_options | SE_DATABASE_LOGOPTS_TIME | SE_DATABASE_LOGOPTS_QUERY );
    
	  $this->database_connection = $this->database_connect($database_host, $database_username, $database_password) or die($this->database_error());
	  $this->database_select($database_name) or die($this->database_error());
    
    // This will prevent some problems on MySQL5+ servers
    mysql_query("SET sql_mode='MYSQL40'", $this->database_connection);
	}
  
  // END se_database() METHOD





  
  //
	// THIS METHOD CONNECTS TO THE SERVER AND SELECTS THE DATABASE
  //
	// INPUT:
  //    void
  //
	// OUTPUT:
  //    instance of this class
  //
  
	function &getInstance()
  {
    global $database, $database_host, $database_username, $database_password, $database_name;
    static $db;
    
    if( !is_a($db, 'SEDatabase') )
    {
      // Backwards compatibility
      if( is_a($database, 'SEDatabase') )
      {
        $db =& $database;
      }
      
      // Instantiate
      else
      {
        $db = new SEDatabase($database_host, $database_username, $database_password, $database_name);
        $database =& $db;
      }
    }
    
    return $db;
  }
  
  // END getInstance() METHOD







  //
	// THIS METHOD CONNECTS TO A DATABASE SERVER
  //
	// INPUT:
  //    $database_host REPRESENTING THE DATABASE HOST
	//	  $database_username REPRESENTING THE DATABASE USERNAME
	//	  $database_password REPRESENTING THE DATABASE PASSWORD
  //
	// OUTPUT:
  //    RETURNS A DATABASE LINK IDENTIFIER
  //
  
	function database_connect($database_host, $database_username, $database_password)
  {
	  return mysql_connect($database_host, $database_username, $database_password, TRUE);
	}
  
  // END database_connect() METHOD








  //
	// THIS METHOD SELECTS A DATABASE
  //
	// INPUT:
  //    $database_name REPRESENTING THE DATABASE NAME
  //
	// OUTPUT:
  //    RETURNS OUTPUT FOR DATABASE SELECTION
  //
  
	function database_select($database_name)
  {
	  return mysql_select_db($database_name, $this->database_connection);
	} 
  
  // END database_select() METHOD








	// THIS METHOD QUERIES A DATABASE
	// INPUT: $database_query REPRESENTING THE DATABASE QUERY TO RUN
	// OUTPUT: RETURNS A DATABASE QUERY RESULT RESOURCE
	function database_query($database_query)
  {
    // EXECUTE QUERY
    $query_timer_start = getmicrotime();
    $query_result = mysql_query($database_query, $this->database_connection);
    $query_timer_end = getmicrotime();
    $query_timer_total = round($query_timer_end-$query_timer_start, 7);
    
    // LAST QUERY INFO
    $this->_last_query = $database_query;
    $this->_last_resource = $query_result;
	  
    // RETURN IF NOT LOGGING STATS
    switch( TRUE )
    {
      case (!$this->log_stats):
      case ( $query_result && (SE_DATABASE_LOG_SUCCESS & ~ $this->log_trigger)):
      case (!$query_result && (SE_DATABASE_LOG_FAIL    & ~ $this->log_trigger)):
      case ($query_timer_total< $this->log_slow_threshold && (SE_DATABASE_LOG_FAST    & ~ $this->log_trigger)):
      case ($query_timer_total>=$this->log_slow_threshold && (SE_DATABASE_LOG_SLOW    & ~ $this->log_trigger)):
        return $query_result;
      break;
    }
    
    
    // STATS
    $log_data = array('index' => count($this->log_data));
    $this->log_data_totals['total']++;
    
    // QUERY
    if( $this->log_options & SE_DATABASE_LOGOPTS_QUERY )
    {
      // When making hash, remove timestamps
      $log_data['query_hash']  = md5(preg_replace('/\d{10}/', '', $database_query));
      $log_data['query']  = $database_query;
    }
    
    // TIME
    if( $this->log_options & SE_DATABASE_LOGOPTS_TIME )
    {
      $log_data['time']   = $query_timer_total;
      $this->log_data_totals['time'] += $query_timer_total;
    }
    
    // BACKTRACE
    if( $this->log_options & SE_DATABASE_LOGOPTS_BACKTRACE )
    {
      $backtrace = debug_backtrace();
      foreach( $backtrace as $backtrace_index=>$single_backtrace )
        if( !empty($backtrace[$backtrace_index]['file']) )
          $backtrace[$backtrace_index]['file_short'] = str_replace($this->root_folder, '', $backtrace[$backtrace_index]['file']);
      
      $log_data['backtrace']   = &$backtrace;
    }
    
    // RESULT
    if( $this->log_options & SE_DATABASE_LOGOPTS_RESULT )
    {
      $log_data['result']   = ( $query_result ? TRUE : FALSE );
      
      if( $query_result )
        $this->log_data_totals['success']++;
      else
        $this->log_data_totals['failed']++;
    }
    
    // COUNT
    if( $this->log_options & SE_DATABASE_LOGOPTS_COUNT )
    {
      $result_count = 0;
      
      if( $query_result && !$result_count )
        $result_count = $this->database_affected_rows();
      
      if( $query_result && !$result_count )
        $result_count = $this->database_num_rows($query_result);
      
      $log_data['count']   = $result_count;
    }
    
    // GET ERROR
    if( $this->log_options & SE_DATABASE_LOGOPTS_ERROR )
    {
      $log_data['error'] = ( $query_result ? FALSE : $this->database_error() );
    }
    
    // GET THRESHOLD COLOR
    foreach( $this->query_thresholds as $threshold_time=>$threshold_color )
    {
      if( (float)$query_timer_total>(float)$threshold_time )
        continue;
      
      $log_data['color'] = $threshold_color;
      break;
    }
    
    // ADD TO LOG
    $this->log_data[] = $log_data;
    
    // RETURN
	  return $query_result;
	}
  
  // END database_query() METHOD







  
  //
	// THIS METHOD FETCHES A ROW AS A NUMERIC ARRAY
  //
	// INPUT:
  //    $database_result REPRESENTING A DATABASE QUERY RESULT RESOURCE
  //
	// OUTPUT:
  //    RETURNS A NUMERIC ARRAY FOR A DATABASE ROW
  //
  
	function database_fetch_array($database_result)
  {
    if( !is_resource($database_result) ) return FALSE;
	  return mysql_fetch_array($database_result, MYSQL_NUM);
	}
  
  // END database_fetch_array() METHOD








  //
	// THIS METHOD FETCHES A ROW AS AN ASSOCIATIVE ARRAY
  //
	// INPUT:
  //    $database_result REPRESENTING A DATABASE QUERY RESULT RESOURCE
  //
	// OUTPUT:
  //    RETURNS AN ASSOCIATIVE ARRAY FOR A DATABASE ROW
  //
  
	function database_fetch_assoc($database_result)
  {
    if( !is_resource($database_result) ) return FALSE;
	  return mysql_fetch_assoc($database_result);
	}
  
  // END database_fetch_assoc() METHOD








  //
	// THIS METHOD RETURNS THE NUMBER OF ROWS IN A RESULT
  //
	// INPUT:
  //    $database_result REPRESENTING A DATABASE QUERY RESULT RESOURCE
  //
	// OUTPUT:
  //    RETURNS THE NUMBER OF ROWS IN A RESULT
  //
  
	function database_num_rows($database_result)
  {
    if( !is_resource($database_result) ) return FALSE;
	  return mysql_num_rows($database_result);
	}
  
  // END database_num_rows() METHOD








  //
	// THIS METHOD RETURNS THE NUMBER OF ROWS IN A RESULT
  //
	// INPUT:
  //    $database_result REPRESENTING A DATABASE QUERY RESULT RESOURCE
  //
	// OUTPUT:
  //    RETURNS THE NUMBER OF ROWS IN A RESULT
  //
  
	function database_affected_rows()
  {
	  return mysql_affected_rows($this->database_connection);
	}
  
  // END database_affected_rows() METHOD 








  //
	// THIS METHOD FREES THE RESULT
  //
	// INPUT:
  //    $database_result REPRESENTING A DATABASE QUERY RESULT RESOURCE
  //
	// OUTPUT:
  //    TRUE on success, else FALSE
  //
  
	function database_free_result($database_result)
  {
	  return mysql_free_result($database_result);
	}
  
  // END database_free_result() METHOD 








  //
	// THIS METHOD SETS THE CLIENT CHARACTER SET FOR THE CURRENT CONNECTION
  //
	// INPUT:
  //    $charset REPRESENTING A VALID CHARACTER SET NAME
  //
	// OUTPUT:
  //    RESOURCE OR FALSE
  //
  
	function database_set_charset($charset)
  {
	  if( function_exists('mysql_set_charset') === TRUE )
    {
	    return mysql_set_charset($charset, $this->database_connection);
	  }
    else
    {
	    return $this->database_query('SET NAMES "'.$charset.'"');
	  }
	}
  
  // END database_set_charset() METHOD 








  //
	// THIS METHOD ESCAPES SPECIAL CHARACTERS IN A STRING FOR USE IN AN SQL STATEMENT
  //
	// INPUT:
  //    $unescaped_string REPRESENTING THE STRING TO ESCAPE
  //
	// OUTPUT: 
  //    Escaped string
  //
  
	function database_real_escape_string($unescaped_string)
  {
	  return mysql_real_escape_string($unescaped_string, $this->database_connection);
	}
  
  // END database_real_escape_string() METHOD 








  //
	// THIS METHOD RETURNS THE ID GENERATED FROM THE PREVIOUS INSERT OPERATION
  //
	// INPUT: 
  //    void
  //
	// OUTPUT:
  //    RETURNS THE ID GENERATED FROM THE PREVIOUS INSERT OPERATION
  //
  
	function database_insert_id()
  {
	  return mysql_insert_id($this->database_connection);
	}
  
  // END database_insert_id() METHOD








  //
	// THIS METHOD RETURNS THE DATABASE ERROR
  //
	// INPUT: 
  //    void
  //
	// OUTPUT: 
  //    The error message for the last failed query
  //
  
	function database_error()
  {
	  return mysql_error($this->database_connection);
	}
  
  // END database_error() METHOD








  //
	// THIS METHOD RETURNS ALL RETURNED DATA FOR THE LAST QUERY
  //
	// INPUT: 
  //    void
  //
	// OUTPUT: 
  //    An array of all returned data for the last query
  //
  
	function database_load_all()
  {
    if( !is_resource($this->_last_resource) )
    {
      return FALSE;
    }
    
    $resource = $this->_last_resource;
    $return_data = array();
    while( $data = $this->database_fetch_assoc($resource) )
    {
      $return_data[] = $data;
    }
    
    return $return_data;
	}
  
  // END database_load_all() METHOD








  //
	// THIS METHOD RETURNS ALL RETURNED DATA FOR THE LAST QUERY IN AN ASSOC
  // ARRAY USING THE COLUMN SPECIFIED AS THE KEY
  //
	// INPUT: 
  //    $key_column - to use as assoc index
  //
	// OUTPUT: 
  //    The error message for the last failed query
  //
  
	function database_load_all_assoc($key_column)
  {
    if( !is_resource($this->_last_resource) )
    {
      return FALSE;
    }
    
    $resource = $this->_last_resource;
    $return_data = array();
    while( $data = $this->database_fetch_assoc($resource) )
    {
      $return_data[$data[$key_column]] = $data;
    }
    
    return $return_data;
	}
  
  // END database_load_all_assoc() METHOD








  //
	// THIS METHOD CLOSES A CONNECTION TO THE DATABASE SERVER
  //
	// INPUT: 
  //  void
  //
	// OUTPUT:
  //    Connection closure result
  //
  
	function database_close()
  {
    // DO DATABASE QUERY LOGGING
    if( $this->log_stats ) $this->database_log_stats();
    if( $this->log_stats ) $this->database_log_stats_cleanup();
    
	  return mysql_close($this->database_connection);
	}
  
  // END database_close() METHOD








  //
	// THIS METHOD SORT THE BENCHMARKS BY TIME
  //
	// INPUT: 
  //    void
  //
	// OUTPUT: 
  //    void
  //
  
	function database_benchmark_sort()
  {
    if( !function_exists('dbtimecmp') )
    {
      function dbtimecmp($a, $b)
      {
        //return ( $a['time']==$b['time'] ? 0 : ($a['time']<$b['time'] ? -1 : 1) );
        return ( (float)$a['time']==(float)$b['time'] ? 0 : ((float)$a['time']>(float)$b['time'] ? -1 : 1) );
      }
    }
    
    usort($this->log_data, 'dbtimecmp');
    
    return '';
  }








  //
	// THIS METHOD GETS MYSQL CLIENT INFO
  //
	// INPUT:
  //    void
  //
	// OUTPUT:
  //    http://us2.php.net/manual/en/function.mysql-get-client-info.php
  //
  
	function database_get_client_info()
  {
	  return ( function_exists('mysql_get_client_info') ? mysql_get_client_info() : FALSE );
	}
  
  // END database_get_client_info() METHOD 








  //
	// THIS METHOD GETS MYSQL HOST INFO
  //
	// INPUT:
  //    void
  //
	// OUTPUT:
  //    http://us2.php.net/manual/en/function.mysql-get-host-info.php
  //
  
	function database_get_host_info()
  {
	  return ( function_exists('mysql_get_host_info') ? mysql_get_host_info($this->database_connection) : FALSE );
	}
  
  // END database_get_host_info() METHOD 








  //
	// THIS METHOD GETS MYSQL PROTOCOL INFO
  //
	// INPUT:
  //    void
  //
	// OUTPUT:
  //    http://us2.php.net/manual/en/function.mysql-get-proto-info.php
  //
  
	function database_get_proto_info()
  {
	  return ( function_exists('mysql_get_proto_info') ? mysql_get_proto_info($this->database_connection) : FALSE );
	}
  
  // END database_get_proto_info() METHOD 








  //
	// THIS METHOD GETS MYSQL SERVER INFO
  //
	// INPUT:
  //    void
  //
	// OUTPUT:
  //    http://us2.php.net/manual/en/function.mysql-get-server-info.php
  //
  
	function database_get_server_info()
  {
	  return ( function_exists('mysql_get_server_info') ? mysql_get_server_info($this->database_connection) : FALSE );
	}
  
  // END database_get_server_info() METHOD 








  //
	// THIS METHOD LOGS STUFF
  //
	// INPUT:
  //    void
  //
	// OUTPUT:
  //    void
  //
  
	function database_log_stats()
  {
    $query_timer_start = getmicrotime();
    
    // DO STUFF
    $time = time();
    $do_insert = FALSE;
    $insert_query = "
      INSERT INTO se_debug_querylog
      (
        debug_querylog_query,
        debug_querylog_queryhash,
        debug_querylog_querylocation,
        debug_querylog_benchmark,
        debug_querylog_backtrace,
        debug_querylog_result,
        debug_querylog_count,
        debug_querylog_error,
        debug_querylog_time
      )
      VALUES
    ";
    
    foreach( $this->log_data as $log_index=>$log_data )
    {
      // LOG SINGLE QUERY
      if( $do_insert ) $insert_query .= ", ";
      
      $query_location = substr($log_data['backtrace'][0]['file_short']." [".$log_data['backtrace'][0]['line']."]", 0, 254);
      
      $insert_query .= "(
        '".$this->database_real_escape_string($log_data['query'])."',
        '{$log_data['query_hash']}',
        '{$query_location}',
        '{$log_data['time']}',
        '', /* TODO */
        '".( $log_data['result'] ? '1' : '0' )."',
        '{$log_data['count']}',
        '".$this->database_real_escape_string($log_data['error'])."',
        '{$time}'
      )";
      
      $do_insert = TRUE;
      
      // LOG STATS
      $sql = "
        INSERT INTO se_debug_querystats
        (
          debug_querystats_query_hash,
          debug_querystats_query_location,
          debug_querystats_query,
          debug_querystats_count,
          debug_querystats_count_failed,
          debug_querystats_count_slow,
          debug_querystats_time_total,
          debug_querystats_time_avg
        )
        VALUES
        (
          '{$log_data['query_hash']}',
          '{$query_location}',
          '".$this->database_real_escape_string($log_data['query'])."',
          '1',
          '".( !$log_data['result'] ? '1' : '0' )."',
          '".( $log_data['time']>$this->log_slow_threshold ? '1' : '0' )."',
          '{$log_data['time']}',
          '{$log_data['time']}'
        )
        ON DUPLICATE KEY UPDATE
          debug_querystats_count=debug_querystats_count+1,
          debug_querystats_count_failed=debug_querystats_count_failed+".( !$log_data['result'] ? '1' : '0' ).",
          debug_querystats_count_slow=debug_querystats_count_slow+".( $log_data['time']>$this->log_slow_threshold ? '1' : '0' ).",
          debug_querystats_time_total=debug_querystats_time_total+".( $log_data['time'] ? $log_data['time'] : '0' ).",
          debug_querystats_time_avg=(debug_querystats_count*debug_querystats_time_avg+".( is_numeric($log_data['time']) ? $log_data['time'] : '0' ).")/(debug_querystats_count+1)
      ";
      
      mysql_query($sql, $this->database_connection) or die(mysql_error($this->database_connection)." ".$sql);
    }
    
    if( $do_insert ) mysql_query($insert_query, $this->database_connection) or die(mysql_error($this->database_connection)." ".$insert_query);
    
    $query_timer_end = getmicrotime();
    $query_timer_total = round($query_timer_end-$query_timer_start, 7);
	}
  
  // END database_log_stats() METHOD








  //
	// THIS METHOD CLEANS UP LOG STUFF
  //
	// INPUT:
  //    void
  //
	// OUTPUT:
  //    void
  //
  
	function database_log_stats_cleanup($not_slow=TRUE)
  {
    $age_limit = time() - 7200;
    //$age_limit = time() - 86400;
    
    $sql = "DELETE FROM se_debug_querylog WHERE debug_querylog_time<$age_limit";
    if( $not_slow ) $sql .= " && debug_querylog_benchmark<{$this->log_slow_threshold}";
    
    $resource = mysql_query($sql, $this->database_connection) or die(mysql_error($this->database_connection)." ".$sql);
  }
  
  // END database_log_stats_cleanup() METHOD
  
}




//BACKWARDS COMPATIBILITY
class se_database extends SEDatabase { }




?>