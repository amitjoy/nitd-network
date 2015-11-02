<?php

// SET ERROR REPORTING
error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_USER_ERROR);
ini_set('display_errors', TRUE);

// CHECK FOR PAGE VARIABLE
if(!isset($page)) { $page = ""; }

// DEFINE SE PAGE CONSTANT
define('SE_PAGE', TRUE);
define('SE_ROOT', realpath(dirname(dirname(__FILE__))));
define('SE_ADMIN_SAFE_MODE', FALSE);
$se_benchmark_start = array_sum(explode(" ",microtime()));

// SET INCLUDE PATH TO ROOT OF SE
set_include_path(get_include_path() . PATH_SEPARATOR . realpath("../"));

// INITIATE SMARTY
include "include/class_smarty.php";
$smarty =& SESmarty::getInstance();

// INCLUDE VERSION
include "include/version.php";

// INCLUDE DATABASE INFORMATION
include "include/database_config.php";

// INCLUDE CLASS/FUNCTION FILES
include "include/functions_file.php";

// INCLUDE CACHE/SESSION
include "include/cake/config.php";

/*
include "include/cache/cache.php";
include "include/cache/storage.php";
include "include/session/session.php";
include "include/session/storage.php";
*/

include "include/class_core.php";

include "include/class_admin.php";
include "include/class_actions.php";
include "include/class_database.php";
include "include/class_datetime.php";
include "include/class_field.php";
include "include/class_hook.php";
include "include/class_language.php";
include "include/class_upload.php";
include "include/class_user.php";
include "include/class_url.php";
include "include/class_misc.php";
include "include/functions_general.php";
include "include/functions_email.php";
include "include/functions_stats.php";

// JS API MOD JSON FUNCTIONS
include "include/class_javascript.php";
if( !function_exists('json_encode') )
{
  include_once "include/xmlrpc/xmlrpc.inc";
  include_once "include/xmlrpc/xmlrpcs.inc";
  include_once "include/xmlrpc/xmlrpc_wrappers.inc";
  include_once "include/jsonrpc/jsonrpc.inc";
  include_once "include/jsonrpc/jsonrpcs.inc";
  include_once "include/jsonrpc/json_extension_api.inc";
}

// INITIATE DATABASE CONNECTION
$database =& SEDatabase::getInstance();
// Use this line if you changed the way database connection is loaded
//$database = new se_database($database_host, $database_username, $database_password, $database_name);

// SET LANGUAGE CHARSET
$database->database_set_charset(SE_Language::info('charset'));

// GET SETTINGS
$setting = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_settings LIMIT 1"));

// Instantiate caching object
$cache_object = SECache::getInstance();

// ENSURE NO SQL INJECTIONS THROUGH POST OR GET ARRAYS
$_POST = security($_POST);
$_GET = security($_GET);
$_COOKIE = security($_COOKIE);

// CREATE SESSION
$session_options = @unserialize($setting['setting_session_options']);
if( !empty($session_options) )
{
  if( !empty($session_options['storage']) ) Configure::write('Session.save', $session_options['storage']);
  if( !empty($session_options['name']) ) Configure::write('Session.cookie', $session_options['name']);
  if( !empty($session_options['expire']) ) Configure::write('Session.timeout', $session_options['expire']);
}

$session =& SESession::getInstance(null, true);
$session->engine(@$session_options['storage'], $session_options);
if( defined('SE_SESSION_RESUME') && SE_SESSION_RESUME && isset($session_id) )
{
  $session->_userAgent = md5(env('HTTP_USER_AGENT') . Configure::read('Security.salt'));
  $session->id($session_id);
}
$session->start();

// CREATE CACHE OBJECT
$cache =& SECache::getInstance();

if( !empty($setting['setting_cache_enabled']) && !empty($setting['setting_cache_default']) )
{
  $config = array();
  $config['engine'] = ucfirst($setting['setting_cache_default']);
  $config['duration'] = ( !empty($setting['setting_cache_lifetime']) ? $setting['setting_cache_lifetime'] : 3600 );
  $config['prefix'] = 'se_';

  $cache_options = @unserialize($setting['setting_cache_'.$setting['setting_cache_default'].'_options']);
  if( !$cache_options ) $cache_options = array();

  if( $setting['setting_cache_default'] == 'file' )
  {
    $config['path'] = ( !empty($cache_options['root']) ? $cache_options['root'] : SE_ROOT.DS.'cache' );
    $config['lock'] = ( isset($cache_options['locking']) ? $cache_options['locking'] : false );
    $config['serialize'] = true;
    if( !defined('CACHE') ) define('CACHE', $config['path']);
  }
  else if( $setting['setting_cache_default'] == 'memcache' )
  {
    $config['servers'] = array();
    foreach( $cache_options['servers'] as $server )
    {
      $config['servers'][] = $server['host'].':'.$server['port'];
    }
    $config['compress'] = false;
  }

  SECache::config('default', $config);
}



/*
// CREATE SESSION OBJECT
$session_options = ( defined('SE_SESSION_RESUME') && !empty($session_id) ? array('id' => $session_id, 'security' => array()) : array() );
$session =& SESession::getInstance($session_options);
if( $session->getState() == 'expired' )
{
  $session->restart();
}
 *
 */

// CREATE URL CLASS
$url = new se_url();

// CREATE DATETIME CLASS
$datetime = new se_datetime();

// CREATE ADMIN OBJECT AND ATTEMPT TO LOG ADMIN IN
$admin = new SEAdmin();
$admin->admin_checkCookies();

// INSTANTIATE JAVASCRIPT OBJECT
$se_javascript = new SE_Javascript();
$smarty->assign_by_ref('se_javascript', $se_javascript);

// ADMIN IS NOT LOGGED IN AND NOT ON LOGIN PAGE
if($page != "admin_login" && $page != "admin_lostpass" && $page != "admin_lostpass_reset" && $admin->admin_exists == 0)
{
  header("Location: admin_login.php");
  exit();
}

// SET UP LANGUAGE VARIABLES
if( !empty($_GET['lang_id']) && $admin->admin_exists )
{
  $admin->admin_info['admin_language_id'] = (int)$_GET['lang_id'];
  $database->database_query("UPDATE se_admins SET admin_language_id='{$admin->admin_info['admin_language_id']}' WHERE admin_id='{$admin->admin_info['admin_id']}' LIMIT 1");
}

// SET UP LANGUAGE VARIABLES
SE_Language::select($admin);

if( SE_Language::info('language_setlocale') )
{
  $multi_language = 1;
  setlocale(LC_TIME, SE_Language::info('language_setlocale'));

}

header("Content-Language: ".SE_Language::info('language_code'));


// GET PLUGIN USER LEVEL MENU OPTIONS AND INCLUDE PLUGIN PAGES
$global_plugins = array();
$level_menu = array();
$plugins = $database->database_query("SELECT * FROM se_plugins WHERE plugin_disabled=0 ORDER BY plugin_order ASC");
while($plugin_info = $database->database_fetch_assoc($plugins))
{
  $plugin_vars = array();
  if( file_exists("admin_header_{$plugin_info['plugin_type']}.php") )
  {
    include "admin_header_{$plugin_info['plugin_type']}.php";
  }
  
  // Set the hooks for each of the plugin templates if not using the new hooked template includes (backwards compatibility)
  if( empty($plugin_vars['uses_tpl_hooks']) )
  {
    if( file_exists(SE_ROOT."/templates/admin_header_{$plugin_info['plugin_type']}.tpl") )
      $smarty->assign_hook('admin_header', "admin_header_{$plugin_info['plugin_type']}.tpl");
  }
  
  // GET LEVEL PAGES	
  $new_level_menu = array();
  $plugin_pages_level = explode("<~!~>", $plugin_info['plugin_pages_level']);
  for($l=0;$l<count($plugin_pages_level);$l++)
  {
    $plugin_page = explode("<!>", $plugin_pages_level[$l]);
    if($plugin_page[0] != "" && $plugin_page[1] != "")
    {
      $level_page = strrev(substr(strrev($plugin_page[1]), 4));
      SE_Language::_preload($plugin_page[0]);
      $new_level_menu[] = array(
        'page' => $level_page,
				'title' => $plugin_page[0],
				'link' => $plugin_page[1]
      );
    }
  }
  $level_menu[] = $new_level_menu;
  $plugin_info['plugin_pages_level'] = $new_level_menu;

  // GET MAIN PAGES
  $plugin_pages_main = explode("<~!~>", $plugin_info['plugin_pages_main']);
  $main_pages = array();
  for($l=0;$l<count($plugin_pages_main);$l++)
  {
    $plugin_page = explode("<!>", $plugin_pages_main[$l]);
    if($plugin_page[0] != "" && $plugin_page[2] != "")
    {
      SE_Language::_preload($plugin_page[0]);
      $main_pages[] = array(
        'title' => $plugin_page[0],
				'icon' => $plugin_page[1],
				'file' => $plugin_page[2]
      );
    }
  }
  $plugin_info['plugin_pages_main'] = $main_pages;

  // SET GLOBAL PLUGIN ARRAY
  SE_Language::_preload($plugin_info['plugin_menu_title']);
  $global_plugins[$plugin_info['plugin_type']] = $plugin_info;
  unset($plugin_vars);
}

// BACKWARDS COMPATIBILITY FOR THE $global_plugin CHANGE
if( strpos($page, 'admin_level')!==FALSE )
{
  $global_plugins = array_values($global_plugins);
  
  // Flush level settings
  $level_id = ( !empty($_POST['level_id']) ? $_POST['level_id'] : ( !empty($_GET['level_id']) ? $_GET['level_id'] : NULL ) );
  if( is_object($cache_object) && $level_id && ($_GET['task']=="dosave" || $_POST['task']=="dosave") )
  {
    $cache_object->remove('site_level_settings_'.$level_id);
  }
}


// Nasty code to flush site settings
if( (!empty($_GET['task']) || !empty($_POST['task'])) && is_object($cache_object) )
{
  $cache_object->remove('site_settings');
}

?>