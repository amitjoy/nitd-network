<?php

class SESanityCommon
{
  function &load()
  {
    $sanity =& SESanity::getInstance();
    
    $sanity->registerCategory('environment', array('lang_title' => 'Environment'));
    $sanity->registerCategory('php', array('lang_title' => 'PHP'));
    $sanity->registerCategory('permissions', array('lang_title' => 'File Permissions'));
    
    // Custom
    $sanity->register('custom', array(
      'name' => 'os',
      'category' => 'environment',
      'recommendation' => TRUE,
      'lang_title' => 'Operating System',
      'lang_message' => 'For optimal performance we recommend using a Unix-based OS, however everything should run properly under Windows.'
    ));
    
    $sanity->register('custom', array(
      'name' => 'httpd',
      'category' => 'environment',
      'recommendation' => TRUE,
      'lang_title' => 'Web Server',
      'lang_message' => 'For optimal performance, use the Apache HTTPD server.'
    ));
    
    $sanity->register('custom', array(
      'name' => 'php',
      'category' => 'environment',
      'critical' => TRUE,
      'lang_title' => 'PHP',
      'lang_message' => 'At least PHP 4.3.0 is required.'
    ));
    
    $sanity->register('custom', array(
      'name' => 'php_sapi',
      'category' => 'environment',
      'recommendation' => TRUE,
      'lang_title' => 'PHP Server API',
      'lang_message' => 'It is recommended to run PHP as an Apache module.'
    ));
    
    // Extensions
    $sanity->register('extension', array(
      'category' => 'php',
      'extension' => 'gd',
      'lang_title' => 'GD Graphics Library',
      'lang_message' => 'Without the GD library, your site will not be able to have captchas or perform image manipulation such as resizing of uploaded photos.'
    ));
    
    $sanity->register('extension', array(
      'category' => 'php',
      'extension' => 'mbstring',
      'recommendation' => TRUE,
      'lang_title' => 'Multi-byte String Library',
      'lang_message' => 'Required for non-english sites, however recommended for all sites.'
    ));
    
    $sanity->register('extension', array(
      'category' => 'php',
      'extension' => 'memcache',
      'recommendation' => TRUE,
      'lang_title' => 'Memcache Client Library',
      'lang_message' => 'Not required, only for memory-based caching using memcached.'
    ));
    
    $sanity->register('extension', array(
      'category' => 'php',
      'extension' => 'mysql',
      'version' => '4.1',
      'critical' => TRUE,
      'lang_title' => 'MySQL Client Library',
      'lang_message' => 'At least version 4.1 of the MySQL Client Library is required.'
    ));
    
    $sanity->register('extension', array(
      'category' => 'php',
      'extension' => 'pcre',
      'lang_title' => 'Perl-Compatible Regular Expressions',
      'lang_message' => 'This extension is required.'
    ));
    
    $sanity->register('extension', array(
      'category' => 'php',
      'extension' => 'session',
      'critical' => TRUE,
      'lang_title' => 'Sessions',
      'lang_message' => 'Session support must be enabled.'
    ));
    
    // Configuration
    $sanity->register('configuration', array(
      'category' => 'php',
      'directive' => 'file_uploads',
      'cmp' => 'on',
      'lang_title' => 'file_uploads',
      'lang_message' => 'This php.ini directive must be set to on if you want to be able to upload files.'
    ));
    
    /*
    $sanity->register('configuration', array(
      'category' => 'php',
      'directive' => 'magic_quotes_gpc',
      'cmp' => 'off',
      'lang_title' => 'magic_quotes_gpc',
      'lang_message' => 'This php.ini directive should be disabled, however is not critical.'
    ));
    */
    
    $sanity->register('configuration', array(
      'category' => 'php',
      'directive' => 'magic_quotes_runtime',
      'cmp' => 'off',
      'lang_title' => 'magic_quotes_runtime',
      'lang_message' => 'This php.ini directive must be disabled.'
    ));
    
    $sanity->register('configuration', array(
      'category' => 'php',
      'directive' => 'memory_limit',
      'cmp' => '>=',
      'value' => (16 * 1024 * 1024),
      'is_bytes' => TRUE,
      'recommendation' => TRUE,
      'lang_title' => 'memory_limit',
      'lang_message' => 'This php.ini directive limits the max size in pixels of uploaded images. 16MB will allow for up to approx. 5 megapixel images.'
    ));
    
    $sanity->register('configuration', array(
      'category' => 'php',
      'directive' => 'open_basedir',
      'cmp' => '==', 'value' => '',
      'lang_title' => 'open_basedir',
      'lang_message' => 'If set, this php.ini directive may cause problems with file uploads. If you run into problems later, try turning it off.'
    ));
    
    $sanity->register('configuration', array(
      'category' => 'php',
      'directive' => 'post_max_size',
      'cmp' => '>=',
      'value' => (16 * 1024 * 1024),
      'is_bytes' => TRUE,
      'recommendation' => TRUE,
      'lang_title' => 'post_max_size',
      'lang_message' => 'This php.ini directive directly limits the max size uploads.'
    ));
    
    $sanity->register('configuration', array(
      'category' => 'php',
      'directive' => 'register_globals',
      'cmp' => 'off',
      'lang_title' => 'register_globals',
      'lang_message' => 'This php.ini directive is not secure and may cause unpredictable behavior and leave your server open to attacks.'
    ));
    
    $sanity->register('configuration', array(
      'category' => 'php',
      'directive' => 'safe_mode',
      'cmp' => 'off',
      'lang_title' => 'safe_mode',
      'lang_message' => 'This php.ini directive may cause problems with file uploads and has been deprecated and removed as of PHP 6.'
    ));
    
    $sanity->register('configuration', array(
      'category' => 'php',
      'directive' => 'upload_max_filesize',
      'cmp' => '>=',
      'value' => (16 * 1024 * 1024),
      'is_bytes' => TRUE,
      'recommendation' => TRUE,
      'lang_title' => 'upload_max_filesize',
      'lang_message' => 'This php.ini directive directly limits the max size uploads.'
    ));
    
    // Permissions
    $sanity->register('permission', array(
      'category' => 'permissions',
      'path' => './cache',
      'perms' => 7,
      'lang_title' => './cache',
      'lang_message' => 'This is only required for file-based caching. Log in over FTP and set chmod 0777 (Linux), or give Full Control to the user PHP runs as (Windows) on the "cache" directory.'
    ));
    
    $sanity->register('permission', array(
      'category' => 'permissions',
      'path' => './include',
      'perms' => 7,
      'critical' => TRUE,
      'lang_title' => './include',
      'lang_message' => 'This is only required for the initial install and may be set back after the installer is run. Log in over FTP and set chmod 0777 (Linux), or give Full Control to the user PHP runs as (Windows) on the "include" directory.'
    ));
    
    $sanity->register('permission', array(
      'category' => 'permissions',
      'path' => './include/smarty/templates_c',
      'perms' => 7,
      'critical' => TRUE,
      'lang_title' => './include/smarty/templates_c',
      'lang_message' => 'Log in over FTP and set chmod 0777 (Linux), or give Full Control to the user PHP runs as (Windows) on the "include/smarty/templates_c" directory.'
    ));
    
    $sanity->register('permission', array(
      'category' => 'permissions',
      'path' => './language',
      'perms' => 7,
      'lang_title' => './language',
      'lang_message' => 'Log in over FTP and set chmod 0777 (Linux), or give Full Control to the user PHP runs as (Windows) on the "language" directory.'
    ));
    
    $sanity->register('permission', array(
      'category' => 'permissions',
      'path' => './language/indexes',
      'perms' => 7,
      'lang_title' => './language/indexes',
      'lang_message' => 'Log in over FTP and set chmod 0777 (Linux), or give Full Control to the user PHP runs as (Windows) on the "language/indexes" directory.'
    ));
    
    $sanity->register('permission', array(
      'category' => 'permissions',
      'path' => './templates',
      'perms' => 7,
      'lang_title' => './templates',
      'lang_message' => 'This is only required if you want to be able to edit the templates through the admin panel. Log in over FTP and set chmod 0777 (Linux), or give Full Control to the user PHP runs as (Windows) on the "templates" directory, recursively.'
    ));
    
    $sanity->register('permission', array(
      'category' => 'permissions',
      'path' => './uploads_admin',
      'perms' => 7,
      'lang_title' => './uploads_admin',
      'lang_message' => 'Log in over FTP and set chmod 0777 (Linux), or give Full Control to the user PHP runs as (Windows) on the "uploads_admin" directory.'
    ));
    
    $sanity->register('permission', array(
      'category' => 'permissions',
      'path' => './uploads_user',
      'perms' => 7,
      'critical' => TRUE,
      'lang_title' => './uploads_user',
      'lang_message' => 'Log in over FTP and set chmod 0777 (Linux), or give Full Control to the user PHP runs as (Windows) on the "uploads_user" directory.'
    ));
    
    return $sanity;
  }
}

?>