<?php

$page = "admin_session";
include "admin_header.php";

$task = ( isset($_POST['task']) ? $_POST['task'] : ( isset($_GET['task']) ? $_GET['task'] : NULL ) );


// Save
if( $task == "dosave" )
{
  $session_options  = (array)   $_POST['setting_session_options'];
}
// Load
else
{
  $session_options = @unserialize($setting['setting_session_options']);
}
if( !is_array($session_options) ) $session_options = array();



// Process options
// Process expire
if( empty($session_options['expire']) )
  $session_options['expire'] = 0;

// Process name
if( empty($session_options['name']) || !preg_match('/^[a-zA-Z0-9]$/', $session_options['name']) )
  $session_options['name'] = 'PHPSESSID';
  
// Process servers
if( is_array($session_options) && is_array($session_options['server_hosts']) )
{
  $session_options['servers'] = array();
  for( $i=0, $l=count($session_options['server_hosts']); $i<$l; $i++ )
  {
    $session_options['servers'][] = array(
      'host' => $session_options['server_hosts'][$i],
      'port' => $session_options['server_ports'][$i]
    );
  }

  unset($session_options['server_hosts']);
  unset($session_options['server_ports']);
}

// Process root
if( isset($session_options['root']) )
  $session_options['root'] = preg_replace('/^[.]/', SE_ROOT, $session_options['root']);



// Test available options
$available_storage = array();
$available_storage[] = 'none';
$available_storage[] = 'db';
if( is_dir($session_options['root']) && is_writeable($session_options['root']) )
  $available_storage[] = 'file';
if( function_exists('memcache_connect') && @memcache_connect(@$session_options['servers'][0]['host'], @$session_options['servers'][0]['port']) )
  $available_storage[] = 'memcache';

if( !in_array($session_options['storage'], $available_storage) )
  $session_options['storage'] = 'none';



// Do saving stuff
if( $task == 'dosave' )
{
  // Serialize
  $setting_session_options = serialize($session_options);
  
  // Assign
  $setting['setting_session_options'] = $setting_session_options;
  $smarty->assign_by_ref('setting', $setting);
  
  $sql = "UPDATE se_settings SET setting_session_options='{$setting_session_options}'";
  $database->database_query($sql) or die($database->database_error());
}


// ASSIGN VARIABLES AND SHOW PAGE
$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);
$smarty->assign('task', $task);
$smarty->assign('available_storage', $available_storage);
$smarty->assign('session_options', $session_options);
include "admin_footer.php";
?>