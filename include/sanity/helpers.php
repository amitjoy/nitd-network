<?php

class SESanityHelpers
{
  function custom_os($name)
  {
    $sanity =& SESanity::getInstance();
    $test =& $sanity->tests[$name];
    
    $test->value_formatted = $uname = php_uname('s')." ".php_uname('r');
    
    // Windows
    if( strtoupper(substr($uname, 0, 3)) === 'WIN' )
      return;
    
    $test->result = TRUE;
  }
  
  
  
  function custom_httpd($name)
  {
    $sanity =& SESanity::getInstance();
    $test =& $sanity->tests[$name];
    
    $test->value_formatted = $wserv = $_SERVER['SERVER_SOFTWARE'];
    
    // Not Apache
    if( strpos(strtolower($wserv), 'apache')===FALSE )
      return;
    
    $test->result = TRUE;
  }
  
  
  
  function custom_php($name)
  {
    $sanity =& SESanity::getInstance();
    $test =& $sanity->tests[$name];
    
    $test->value_formatted = $current_version = phpversion();
    
    if( version_compare($current_version, '4.3', '<') )
      return;
    
    $test->result = TRUE;
  }
  
  
  
  function custom_php_sapi($name)
  {
    $sanity =& SESanity::getInstance();
    $test =& $sanity->tests[$name];
    
    $test->value_formatted = $current_sapi = php_sapi_name();
    $recommended_types = array('apache', 'apache2filter', 'apache2handler');
    
    if( !in_array($current_sapi, $recommended_types) )
      return;
    
    $test->result = TRUE;
  }
  
  
  
  function configuration_open_basedir($name)
  {
    $sanity =& SESanity::getInstance();
    $test =& $sanity->tests[$name];
    
    $ini_value = ini_get($test->directive);
    $test->value_actual = $ini_value;
    $test->value_formatted = ( $ini_value ? 'Set' : 'Unset' );
    
    if( $ini_value )
    {
      $test->custom_message = "Path: ".$ini_value;
      return;
    }
    
    $test->result = TRUE;
  }
  
  
  
  function extension_gd($name)
  {
    $sanity =& SESanity::getInstance();
    $test =& $sanity->tests[$name];
    
    $test->extension_detected = extension_loaded($test->extension);
    
    if( !function_exists('gd_info') )
      return;
    
    $gd_info = gd_info();
    
    $test->version_actual = $current_version = $gd_info['GD Version'];
    
    // Process version
    preg_match('/[0-9.]+/', $current_version, $version_matches);
    $current_version = $version_matches[0];
    
    if( version_compare($current_version, '2.0', '<') )
      return;
    
    $test->result = TRUE;
  }
  
  
  
  function extension_mysql($name)
  {
    $sanity =& SESanity::getInstance();
    $test =& $sanity->tests[$name];
    
    $test->extension_detected = extension_loaded('mysql');
    
    if( !function_exists('mysql_get_client_info') )
      return;
    
    $test->version_actual = $current_version = mysql_get_client_info();
    
    // Process version
    preg_match('/[0-9.]+/', $current_version, $version_matches);
    $current_version = $version_matches[0];
    
    if( $test->version_required && version_compare($current_version, $test->version_required, '<') )
      return;
    
    $test->result = TRUE;
  }
}

?>