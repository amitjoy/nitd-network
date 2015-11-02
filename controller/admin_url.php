<?php

$page = "admin_url";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }


// SET RESULT
$result = 0;


// SAVE CHANGES
if($task == "dosave") {
  $setting[setting_url] = $_POST['setting_url'];
  if($setting_url != "1" & $setting_url != "0") { $setting_url = "0"; }
  $database->database_query("UPDATE se_settings SET setting_url='$setting[setting_url]'");

  $result = 1;
}



$url_files = $database->database_query("SELECT * FROM se_urls");
while($url_files_info = $database->database_fetch_assoc($url_files)) {
  $url_regular = 'http://www.yourdomain.com/'.str_replace(Array('$user', '$id'), Array('username', 'id'), $url_files_info[url_regular]);
  $url_subdirectory = 'http://www.yourdomain.com/'.str_replace(Array('$user', '$id'), Array('username', 'id'), $url_files_info[url_subdirectory]);
  $urls[] = Array('url_title' => $url_files_info[url_title],
		'url_regular' => $url_regular,
		'url_subdirectory' => $url_subdirectory);
}

$server_array = explode("/", $_SERVER['PHP_SELF']);
$server_array_mod = array_pop($server_array);
$server_array_mod = array_pop($server_array);
$server_info = implode("/", $server_array);


$htaccess = "RewriteEngine On
Options +Followsymlinks
RewriteBase /
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^.* - [L,QSA]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^.*/images/(.*)$ $server_info/images/$1 [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^.*/uploads_user/(.*)$ $server_info/uploads_user/$1 [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^([^/]+)/?$ $server_info/profile.php?user=$1 [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^([^/]+)/([^/]+)?$ $server_info/profile.php?user=$1&v=$2 [L]";



// GET PLUGIN HTACCESS
$plugins = $database->database_query("SELECT plugin_url_htaccess FROM se_plugins ORDER BY plugin_id DESC");
while($plugin_info = $database->database_fetch_assoc($plugins)) {
  $htaccess .= "\r\n".str_replace("\$server_info", $server_info, $plugin_info[plugin_url_htaccess]);
}



// ASSIGN VARIABLES AND SHOW URL PAGE
$smarty->assign('urls', $urls);
$smarty->assign('result', $result);
$smarty->assign('htaccess', $htaccess);
include "admin_footer.php";
?>
