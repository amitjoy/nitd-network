<?php

//define('APP_DIR', 'app');
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', SE_ROOT);

define('CAKE_CORE_INCLUDE_PATH', ROOT . DS . 'include');

//define('APP_PATH', ROOT . DS . APP_DIR . DS);
define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);


define('CAKE', CORE_PATH . 'cake' . DS);
//define('APP', ROOT.DS.APP_DIR.DS);
define('CONFIGS', CAKE.'configs'.DS);
define('LIBS', CAKE.'libs'.DS);

include CAKE . DS . 'basics.php';
include LIBS . DS . 'object.php';
include LIBS . DS . 'configure.php';
include LIBS . DS . 'inflector.php';
include LIBS . DS . 'cache.php';
include LIBS . DS . 'session.php';
include LIBS . DS . 'sanitize.php';

include CAKE . DS . 'compat.php';

$cConfigure =& Configure::getInstance(false);

// Config
Configure::write('Session.save', 'php');
Configure::write('Session.cookie', 'SSESSID');
Configure::write('Session.timeout', '120');
Configure::write('Session.start', true);
Configure::write('Session.checkAgent', true);
Configure::write('Security.level', 'high');
Configure::write('Security.salt', 'DYhG93b0qyJfIxfs2guVoUubWwvniR2G0FgaC9mi');

/*
Cache::config('default', array(
  'engine' => 'File', //[required]
  'duration' => 3600, //[optional]
  'probability' => 100, //[optional]
  'path' => SE_ROOT . DS . 'cache', //[optional] use system tmp directory - remember to use absolute path
  'prefix' => 'se_', //[optional]  prefix every cache file with this string
  'lock' => false, //[optional]  use file locking
  'serialize' => true,
));

Cache::config('default', array(
      'engine' => 'Memcache', //[required]
      'duration' => 3600, //[optional]
      'probability' => 100, //[optional]
      'prefix' => 'se_', //[optional]  prefix every cache file with this string
      'servers' => array(
        '127.0.0.1:11211' // localhost, default port 11211
      ), //[optional]
      'compress' => false, // [optional] compress data in Memcache (slower, but uses less memory)
));
*/