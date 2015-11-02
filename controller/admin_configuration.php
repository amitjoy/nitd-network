<?php

$page = "admin_configuration";
include "admin_header.php";

$task = ( isset($_POST['task']) ? $_POST['task'] : ( isset($_GET['task']) ? $_GET['task'] : NULL    ) );
$type = ( isset($_POST['type']) ? $_POST['type'] : ( isset($_GET['type']) ? $_GET['type'] : 'main'  ) );
$iframe = ( isset($_POST['iframe']) ? $_POST['iframe'] : ( isset($_GET['iframe']) ? $_GET['iframe'] : 'main'  ) );

$result = NULL;
$is_error = FALSE;
$config_contents = '';
$newline = "\n";

// Validate types
if( $type!="main" && $type!="htaccess" )
{
  header('Location: admin_home.php');
  exit();
}


// Main config file
if( $type=="main" )
{
  $output_filename = 'database_config.php';
  
  $config_contents .= '<?php' . $newline;
  $config_contents .= $newline;
  
  $config_contents .= '$setting = array();' . $newline;
  $config_contents .= $newline;
  
  // Database
  $config_contents .= '// Database configuration' . $newline;
  $config_contents .= '$database_host = \''.$database_host.'\';' . $newline;
  $config_contents .= '$database_username = \''.$database_username.'\';' . $newline;
  $config_contents .= '$database_password = \''.$database_password.'\';' . $newline;
  $config_contents .= '$database_name = \''.$database_name.'\';' . $newline;
  $config_contents .= $newline;
  
  // Cache
  if( $setting['setting_cache_enabled'] && ($cache_options = unserialize($setting['setting_cache_options'])) )
  {
    $config_contents .= '// Caching configuration' . $newline;
    $config_contents .= '$setting["setting_cache_enabled"] = '.( $setting['setting_cache_enabled'] ? 'TRUE' : 'FALSE' ).';' . $newline;
    $config_contents .= '$setting["setting_cache_options"] = array();' . $newline;
    
    if( $cache_options['storage']=="file" )
    {
      $config_contents .= '$setting["setting_cache_options"]["storage"] = "file";' . $newline;
      $config_contents .= '$setting["setting_cache_options"]["cacheroot"] = \''.addslashes($cache_options['cacheroot']).'\';' . $newline;
      $config_contents .= '$setting["setting_cache_options"]["locking"] = '.( $cache_options['locking'] ? 'TRUE' : 'FALSE' ).';' . $newline;
      $config_contents .= $newline;
    }
    
    elseif( $cache_options['storage']=="memcache" && !empty($cache_options['servers']) )
    {
      $config_contents .= '$setting["setting_cache_options"]["storage"] = "memcache";' . $newline;
      $config_contents .= '$i = 0;' . $newline;
      $config_contents .= '$setting["setting_cache_options"]["servers"] = array();' . $newline;
      foreach( $cache_options['servers'] as $cache_server )
      {
        $config_contents .= '$setting["setting_cache_options"]["servers"][$i][\'host\'] = \''.$cache_server['host'].'\';' . $newline;
        $config_contents .= '$setting["setting_cache_options"]["servers"][$i][\'port\'] = \''.$cache_server['port'].'\';' . $newline;
        $config_contents .= '$i++;' . $newline;
        $config_contents .= $newline;
      }
    }
  }
  
  // Session
  if( ($session_options = unserialize($setting["setting_session_options"])) )
  {
    $config_contents .= '// Session configuration' . $newline;
    $config_contents .= '$setting["setting_session_options"] = array();' . $newline;
    $config_contents .= '$setting["setting_session_options"]["storage"] = "'.$session_options['storage'].'";' . $newline;
    $config_contents .= '$setting["setting_session_options"]["expires"] = "'.$session_options['expires'].'";' . $newline;
    $config_contents .= $newline;
  }
  
  $config_contents .= '?>';
}

// htaccess file
elseif( $type="htaccess" )
{
  $output_filename = '.htaccess';
}


if( $task=="download" && !empty($config_contents) )
{
  header("Content-type: application/x-download");
  header("Content-disposition: attachment; filename={$output_filename}");
  echo $config_contents;
  exit();
}

elseif( $task=="show" && !empty($config_contents) )
{
  
}


// ASSIGN VARIABLES AND SHOW PAGE
$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);
$smarty->assign('task', $task);
include "admin_footer.php";
?>