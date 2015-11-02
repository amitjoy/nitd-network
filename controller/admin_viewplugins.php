<?php

$page = "admin_viewplugins";
include "admin_header.php";

$task = ( isset($_POST['task']) ? $_POST['task'] : ( isset($_GET['task']) ? $_GET['task'] : NULL ) );
$order = ( isset($_POST['order']) ? $_POST['order'] : ( isset($_GET['order']) ? $_GET['order'] : NULL ) );

// UPDATE PLUGIN ORDER
if( $task=="doorder" )
{
  $i = 1;
  $result = TRUE;
  
  foreach( $order as $plugin_type )
  {
    $sql = "UPDATE se_plugins SET plugin_order='{$i}' WHERE plugin_type='{$plugin_type}' LIMIT 1";
    $result = $result && (bool) $database->database_query($sql);
    $i++;
  }
  
  // Flush cached stuff
  if( is_object($cache_object) )
  {
    $cache_object->remove('site_plugins');
  }
  
  echo json_encode(array('result'=>$result));
  exit();
}


// INSTALL RELEVANT PLUGIN
if(isset($_GET['install']))
{
  // Flush cached stuff
  if( is_object($cache_object) )
  {
    $cache_object->remove('site_plugins');
  }
  
  // Install
  $install = $_GET['install'];
  if(file_exists("./install_$install.php")) { 
    include "./install_$install.php"; 
    header("Location: admin_viewplugins.php");
    exit();  
  }
}


if( isset($_GET['disable']) )
{
  $disable = $_GET['disable'];
  $database->database_query("UPDATE se_plugins SET plugin_disabled=1 WHERE plugin_type='$disable'");
  
  // Flush cached stuff
  if( $database->database_affected_rows() && is_object($cache_object) )
  {
    $cache_object->remove('site_plugins');
  }
  
  header("Location: admin_viewplugins.php");
  exit();  
}

if( isset($_GET['enable']) )
{
  $enable = $_GET['enable'];
  $database->database_query("UPDATE se_plugins SET plugin_disabled=0 WHERE plugin_type='$enable'");
  
  // Flush cached stuff
  if( $database->database_affected_rows() && is_object($cache_object) )
  {
    $cache_object->remove('site_plugins');
  }
  
  header("Location: admin_viewplugins.php");
  exit();  
}



// TRY TO READ VERSION FILE FROM SOCIALENGINE SERVER
/*include_once "../include/xmlrpc/xmlrpc.inc";
$f = new xmlrpcmsg('versions', array(php_xmlrpc_encode($setting[setting_key])));
$c = new xmlrpc_client("/remote_versions.php", "www.socialengine.net", 80);
$r = &$c->send($f);
$v = $r->value();
if($v != 0) {
  $max = $v->arraysize();
  $versions = Array();
  for($i=0;$i<$max;$i++) {
    $rec = $v->arraymem($i);
    $download_type = $rec->structmem("type");
    $download_version = $rec->structmem("version");
    $versions[$download_type->scalarval()] = $download_version->scalarval();
  }
}
*/

// GET INSTALLED PLUGINS
$plugins_installed = Array();
$plugin_types = Array();
$plugins = $database->database_query("SELECT * FROM se_plugins ORDER BY plugin_order ASC");
while($plugin_info = $database->database_fetch_assoc($plugins)) {

  // CHECK FOR INSTALL FILE
  $plugin_version_ready = "";
  if(file_exists("./install_$plugin_info[plugin_type].php")) {
    include "./install_$plugin_info[plugin_type].php";
    $plugin_version_ready = $plugin_version;
  }

  // SET PLUGIN ARRAYS
  $plugin_types[] = "install_$plugin_info[plugin_type].php";
  $plugins_installed[] = Array('plugin_name' => $plugin_info[plugin_name],
				'plugin_version' => $plugin_info[plugin_version],
				'plugin_type' => $plugin_info[plugin_type],
				'plugin_desc' => $plugin_info[plugin_desc],
				'plugin_icon' => $plugin_info[plugin_icon],
				'plugin_disabled' => $plugin_info[plugin_disabled],
				'plugin_version_avail' => $versions[$plugin_info[plugin_type]],
				'plugin_version_ready' => $plugin_version_ready);
}


// BEGIN READY-TO-INSTALL PLUGIN ARRAY
$plugins_ready = Array();

// FIND INSTALL FILES
if($dh = opendir("./")) {
  while(($file = readdir($dh)) !== false) {
    if($file != "." && $file != "..") {
      if(strpos($file, "install_") === 0 && !in_array($file, $plugin_types)) {
        include "./$file";
	$plugins_ready[] = Array('plugin_name' => $plugin_name,
				'plugin_version' => $plugin_version,
				'plugin_type' => $plugin_type,
				'plugin_desc' => $plugin_desc,
				'plugin_icon' => $plugin_icon);
      }
    }
  }
  closedir($dh);
}


// ASSIGN VARIABLES AND SHOW ADMIN VIEW PLUGINS PAGE
$smarty->assign('plugins_ready', $plugins_ready);
$smarty->assign('plugins_installed', $plugins_installed);
$smarty->assign('versions', $versions);
include "admin_footer.php";
?>