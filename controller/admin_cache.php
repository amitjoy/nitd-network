<?php

$page = "admin_cache";
include "admin_header.php";

$task = ( isset($_POST['task']) ? $_POST['task'] : ( isset($_GET['task']) ? $_GET['task'] : NULL ) );

if( $task == "dosave" )
{
  $cache_enabled  = (bool) $_POST['setting_cache_enabled'];
  $cache_default  = ( isset($_POST['setting_cache_default']) ? $_POST['setting_cache_default'] : 'file' );
  $cache_lifetime  = ( isset($_POST['setting_cache_lifetime']) ? $_POST['setting_cache_lifetime'] : 120 );

  $cache_file_options  = ( is_array($_POST['setting_cache_file_options']) ? $_POST['setting_cache_file_options'] : array() );
  $cache_memcache_options  = ( is_array($_POST['setting_cache_memcache_options']) ? $_POST['setting_cache_memcache_options'] : array() );
  $cache_xcache_options  = ( is_array($_POST['setting_cache_xcache_options']) ? $_POST['setting_cache_xcache_options'] : array() );
}

else
{
  // Unserialize options for template/config generation
  $cache_enabled = $setting['setting_cache_enabled'];
  $cache_default = $setting['setting_cache_default'];
  $cache_lifetime = $setting['setting_cache_lifetime'];
  $cache_file_options = @unserialize($setting['setting_cache_file_options']);
  $cache_memcache_options = @unserialize($setting['setting_cache_memcache_options']);
  $cache_xcache_options = @unserialize($setting['setting_cache_xcache_options']);
}


// Check lifetime
if( !$cache_lifetime || !is_numeric($cache_lifetime) || $cache_lifetime<=0 )
  $cache_lifetime = 120;

// Process file options
$cache_file_options['locking'] = !empty($cache_file_options['locking']);

if( isset($cache_file_options['root']) )
  $cache_file_options['root'] = preg_replace('/^[.]/', SE_ROOT, $cache_file_options['root']);

// Process memcache options
$cache_memcache_options['compression'] = !empty($cache_memcache_options['compression']);

if( is_array($cache_memcache_options) && is_array($cache_memcache_options['server_hosts']) )
{
  $cache_memcache_options['servers'] = array();
  for( $i=0, $l=count($cache_memcache_options['server_hosts']); $i<$l; $i++ )
  {
    $cache_memcache_options['servers'][] = array(
      'host' => $cache_memcache_options['server_hosts'][$i],
      'port' => $cache_memcache_options['server_ports'][$i]
    );
  }

  unset($cache_memcache_options['server_hosts']);
  unset($cache_memcache_options['server_ports']);
}


// Check available storage
$available_storage = array();
if( is_dir($cache_file_options['root']) && is_writeable($cache_file_options['root']) )
  $available_storage[] = 'file';
if( function_exists('memcache_connect') && @memcache_connect(@$cache_memcache_options['servers'][0]['host'], @$cache_memcache_options['servers'][0]['port']) )
  $available_storage[] = 'memcache';
if( function_exists('apc_add') )
  $available_storage[] = 'apc';
if( function_exists('xcache_get') )
{
  include_once SE_ROOT.DS.'include'.DS.'cake'.DS.'libs'.DS.'cache'.DS.'xcache.php';
  $test_xcache = new XcacheEngine();
  $test_xcache->init($cache_xcache_options);

  if( $test_xcache->__auth() )
    $available_storage[] = 'xcache';
}
//if( !in_array($cache_default, $available_storage) )
//  $cache_default = '';

// SET ENABLED
$cache_file_options['enabled']      = ( in_array('file',      $available_storage  ) );
$cache_memcache_options['enabled']  = ( in_array('memcache',  $available_storage  ) );
$cache_xcache_options['enabled']    = ( in_array('xcache',    $available_storage  ) );



if( $task == 'dosave' )
{
  // Serialize
  $setting_cache_file_options  = addslashes(serialize($cache_file_options));
  $setting_cache_memcache_options  = addslashes(serialize($cache_memcache_options));
  $setting_cache_xcache_options  = addslashes(serialize($cache_xcache_options));
  
  // Assign
  $setting['setting_cache_enabled'] = $cache_enabled;
  $setting['setting_cache_default'] = $cache_default;
  $setting['setting_cache_lifetime'] = $cache_lifetime;
  $setting['setting_cache_file_options'] = $setting_cache_file_options;
  $setting['setting_cache_memcache_options'] = $setting_cache_memcache_options;
  $setting['setting_cache_xcache_options'] = $setting_cache_xcache_options;
  $smarty->assign_by_ref('setting', $setting);
  
  $sql = "
    UPDATE
      se_settings
    SET
      setting_cache_enabled='{$cache_enabled}',
      setting_cache_default='{$cache_default}',
      setting_cache_lifetime='{$cache_lifetime}',
      setting_cache_file_options='{$setting_cache_file_options}',
      setting_cache_memcache_options='{$setting_cache_memcache_options}',
      setting_cache_xcache_options='{$setting_cache_xcache_options}'
  ";
  
  $database->database_query($sql) or die($database->database_error());
}


// ASSIGN VARIABLES AND SHOW PAGE
$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);
$smarty->assign('task', $task);
$smarty->assign('available_storage', $available_storage);
$smarty->assign('cache_file_options', $cache_file_options);
$smarty->assign('cache_memcache_options', $cache_memcache_options);
$smarty->assign('cache_xcache_options', $cache_xcache_options);
include "admin_footer.php";

?>