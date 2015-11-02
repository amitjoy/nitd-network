<?php

$page = "admin_home";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = ""; }

// TAKE SITE OFFLINE
if($task == "site_status")
{
  $setting['setting_online'] = $_POST['setting_online'];
  $database->database_query("UPDATE se_settings SET setting_online='{$setting['setting_online']}'");
}

// GET QUICK STATISTICS
$total_users = $database->database_fetch_assoc($database->database_query("SELECT count(*) AS total_users FROM se_users"));
$total_messages = $database->database_fetch_assoc($database->database_query("SELECT count(*) AS total_messages FROM se_pms"));
$total_user_levels = $database->database_fetch_assoc($database->database_query("SELECT count(*) AS total_user_levels FROM se_levels"));
$total_subnetworks = $database->database_fetch_assoc($database->database_query("SELECT count(*) AS total_subnetworks FROM se_subnets"));
$total_reports = $database->database_fetch_assoc($database->database_query("SELECT count(*) AS total_reports FROM se_reports"));
$total_friendships = $database->database_fetch_assoc($database->database_query("SELECT count(*) AS total_friendships FROM se_friends WHERE friend_status='1'"));
$total_announcements = $database->database_fetch_assoc($database->database_query("SELECT count(*) AS total_announcements FROM se_announcements"));
$total_admins = $database->database_fetch_assoc($database->database_query("SELECT count(*) AS total_admins FROM se_admins"));


// GET TOTAL COMMENTS
$total_comments = 0;
$comment_tables = $database->database_query("SHOW TABLES FROM `$database_name` LIKE 'se_%comments'");
while($table_info = $database->database_fetch_array($comment_tables))
{
  $comment_type = strrev(substr(strrev(substr($table_info[0], 3)), 8));
  $table_comments = $database->database_fetch_assoc($database->database_query("SELECT count(*) AS total_comments FROM `se_{$comment_type}comments`"));
  $total_comments += $table_comments[total_comments];
}

// TRY TO READ VERSION FILE FROM SOCIALENGINE SERVER
/*include_once SE_ROOT."/include/xmlrpc/xmlrpc.inc";

$f = new xmlrpcmsg('versions', array(php_xmlrpc_encode($setting['setting_key']), php_xmlrpc_encode($_SERVER['SERVER_NAME'])));
$c = new xmlrpc_client("/remote_versions.php", "www.socialengine.net", 80);
$r = &$c->send($f);
$v = $r->value();
if($v != 0)
{
  $max = $v->arraysize();
  $versions = Array();
  for($i=0;$i<$max;$i++)
  {
    $rec = $v->arraymem($i);
    $download_type = $rec->structmem("type");
    $download_version = $rec->structmem("version");
    $versions[$download_type->scalarval()] = $download_version->scalarval();
  }
}
*/
// IF USER OPTED TO, REPORT INFORMATION ABOUT ENVIRONMENT
/*if( !empty($setting['setting_stats_remote']) && (time() > $setting['setting_stats_remote_last'] + 86400) )
{
  // Gather info
  $reportDataStructure = php_xmlrpc_encode(array(
    'key' => ( !empty($setting['setting_key']) ? $setting['setting_key'] : md5(php_uname().$_SERVER['SERVER_SOFTWARE'].php_sapi_name()) ),
    'version' => $setting['setting_version'],
    'os' => php_uname(),
    'httpd' => $_SERVER['SERVER_SOFTWARE'],
    'sapi' => php_sapi_name(),
    'mysql_client' => $database->database_get_client_info(),
    'mysql_server' => $database->database_get_server_info(),
    'php' => phpversion(),
    'php_apc' => extension_loaded('apc'),
    'php_ftp' => extension_loaded('ftp'),
    'php_gd' => extension_loaded('gd'),
    'php_iconv' => extension_loaded('iconv'),
    'php_imagick' => extension_loaded('imagick'),
    'php_mbstring' => extension_loaded('mbstring'),
    'php_memcache' => extension_loaded('memcache'),
    'php_mysqli' => extension_loaded('mysqli'),
    'php_openssl' => extension_loaded('openssl'),
    'php_pdo' => extension_loaded('PDO'),
    'php_pdo_mysql' => extension_loaded('pdo_mysql')
  ));
  
  $f = new xmlrpcmsg('remotestats', array($reportDataStructure));
  $c = new xmlrpc_client("/remote_stats.php", "www.socialengine.net", 80);
  $r = &$c->send($f);
  $v = $r->value();
  
  // For now, lets just wait another day to send if it fails
  //if( $v && $v->scalarval()=='ok' )
  //{
    $database->database_query("UPDATE se_settings SET setting_stats_remote_last='".time()."'");
  //}
}*/




// GET TODAY'S NUMBER OF SIGNUPS AND LOGINS
$date_today = strtotime(date("Y-m-d"));
$date_now = time();
$signups_today = $database->database_num_rows($database->database_query("SELECT user_id FROM se_users WHERE user_signupdate>='$date_today' AND user_signupdate<='$date_now' LIMIT 2001"));
if($signups_today > 2000) { $signups_today = "2000+"; }
$logins_today = $database->database_num_rows($database->database_query("SELECT login_id FROM se_logins WHERE login_date>='$date_today' AND login_date<='$date_now' LIMIT 501"));
if($logins_today > 500) { $logins_today = "500+"; }

// GET TODAY'S PAGE VIEWS
$views = $database->database_query("SELECT stat_views FROM se_stats ORDER BY stat_id DESC LIMIT 1");
if($database->database_num_rows($views) == 0) { $views_info[stat_views] = 0; } else { $views_info = $database->database_fetch_assoc($views); }





// Run sanity check
include_once SE_ROOT."/include/sanity/sanity.php";
include_once SE_ROOT."/include/sanity/common.php";

$sanity =& SESanityCommon::load();
unset($sanity->tests['permission_include']);
$sanity->setRoot(SE_ROOT);

($hook = SE_Hook::exists('se_admin_sanity')) ? SE_Hook::call($hook, array()) : NULL;

$sanity->execute();



// Generate notifications
$admin_notifications = array();

if( file_exists(SE_ROOT."/install.php") || file_exists(SE_ROOT."/installsql.php") )
  $admin_notifications[] = 1314;

if( file_exists(SE_ROOT."/upgrade.php") || file_exists(SE_ROOT."/upgradesql.php") )
  $admin_notifications[] = 1315;

$file_version_arr = explode('.', $version);
$file_version = array_shift($file_version_arr).'.'.join('', $file_version_arr);
$database_version_arr = explode('.', $setting['setting_version']);
$database_version = array_shift($database_version_arr).'.'.join('', $database_version_arr);

if( $file_version != $database_version )
  $admin_notifications[] = sprintf(SELanguage::get(1320), $file_version, $database_version);

foreach( $sanity->tests as $sanity_test )
{
  if( $sanity_test->result || $sanity_test->is_recommendation ) continue;
  $admin_notifications[] = $sanity_test->getCategory().': '.$sanity_test->getTitle().': '.$sanity_test->getMessage();
}

($hook = SE_Hook::exists('se_admin_notifications')) ? SE_Hook::call($hook, array()) : NULL;


// ASSIGN VARIABLES AND SHOW ADMIN HOME PAGE
$smarty->assign('admin_notifications', $admin_notifications);

$smarty->assign('task', $task);
$smarty->assign('total_users_num', $total_users['total_users']);
$smarty->assign('total_messages_num', $total_messages['total_messages']);
$smarty->assign('total_comments_num', $total_comments);
$smarty->assign('total_user_levels', $total_user_levels['total_user_levels']);
$smarty->assign('total_subnetworks', $total_subnetworks['total_subnetworks']);
$smarty->assign('total_reports', $total_reports['total_reports']);
$smarty->assign('total_friendships', $total_friendships['total_friendships']);
$smarty->assign('total_announcements', $total_announcements['total_announcements']);
$smarty->assign('total_admins', $total_admins['total_admins']);
$smarty->assign('online_users', online_users());
$smarty->assign('signups_today', $signups_today);
$smarty->assign('logins_today', $logins_today);
$smarty->assign('views_today', $views_info['stat_views']);
$smarty->assign('space_used_num', $space_used);
$smarty->assign('version_num', $version);
$smarty->assign('versions', $versions);
$smarty->assign('install_exists', $install_exists);
include "admin_footer.php";
?>