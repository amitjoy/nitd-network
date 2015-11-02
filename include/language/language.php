<?php

/* $Id: language.php 62 2009-02-18 02:59:27Z john $ */


defined('SE_PAGE') or exit();


// SELANGUAGE CONSTANTS
define('LANGUAGE_INDEX_GENERAL',   1);
define('LANGUAGE_INDEX_FIELDS',    2);
define('LANGUAGE_INDEX_FRIENDS',   3);
define('LANGUAGE_INDEX_LEVELS',    4);
define('LANGUAGE_INDEX_SUBNETS',   5);
define('LANGUAGE_INDEX_ACTIONS',   6);
define('LANGUAGE_INDEX_NOTIFY',    7);
define('LANGUAGE_INDEX_FAQ',       8);
define('LANGUAGE_INDEX_EMAILS',    9);
define('LANGUAGE_INDEX_CUSTOM',   10);





class SELanguage
{
  // Contains the storage handler object
  var $_storage;
  
  // Contains the compiler handler object
  var $_compiler;
  
  // Currently selected language
  var $_language;
  
  // Charset to use
  var $_charset = "utf8";
  
  // Contains the ranges for new vars
  var $_indices = array
  (
    LANGUAGE_INDEX_GENERAL  => array(0,         500000),
    LANGUAGE_INDEX_FIELDS   => array(500001,    600000),
    LANGUAGE_INDEX_FRIENDS  => array(600001,    633000),
    LANGUAGE_INDEX_LEVELS   => array(633001,    666000),
    LANGUAGE_INDEX_SUBNETS  => array(666001,    700000),
    LANGUAGE_INDEX_ACTIONS  => array(700001,    750000),
    LANGUAGE_INDEX_NOTIFY   => array(750001,    800000),
    LANGUAGE_INDEX_FAQ      => array(800001,    850000),
    LANGUAGE_INDEX_EMAILS   => array(850001,    900000),
    LANGUAGE_INDEX_CUSTOM   => array(10000000,  19999999)
  );
  
  // Will assign self by ref to this variable 
  var $_smarty_this_name        = "se_language";
  
  // Use temp file indexing?
  var $_indexing = TRUE;
  var $_indexing_post_load = FALSE;
  var $_indexing_vars_global;
  var $_indexing_vars_page;
  
  // Contains lang vars already sent to the javascript api
  var $_language_variables_js_sent = array();
  
  
  
  
  
  
  
  
  /* -------------------- Contructor Methods -------------------- */
  
  //
  // PRIVATE METHOD _init()
  //
  
  function &_init()
  {
    global $smarty, $page;
    static $instance;
    
    // Create new singleton
    if( is_null($instance) )
    {
      $instance = new SELanguage();
      
      // Get initial storage and compiler instances
      $instance->_storage =& $instance->getStorage();
      //$instance->_compiler =& $instance->_getCompiler();
      
      $smarty->register_prefilter(array(&$instance, '_dynamicCompilerLoader'));
      
      // Assign self to be referenced by smarty later
      $smarty->assign_by_ref($instance->_smarty_this_name, $instance);
      
      // Load indexed language vars
      if( $instance->_indexing )
      {
        $indexed_languagevars = array();
        $index_path = SE_ROOT.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.'indexes'.DIRECTORY_SEPARATOR;
        
        if( file_exists($index_path.'globals.php') )
        {
          $instance->_indexing_vars_global = array_map('trim', array_filter((array)file($index_path.'globals.php')));
          $indexed_languagevars = array_merge($indexed_languagevars, $instance->_indexing_vars_global);
        }
        
        if( file_exists($index_path.$page.'.tpl.php') )
        {
          $instance->_indexing_vars_page = array_map('trim', array_filter((array)file($index_path.'globals.php')));
          $indexed_languagevars = array_merge($indexed_languagevars, $instance->_indexing_vars_page);
        }
        
        if( is_object($instance->_storage) && !empty($indexed_languagevars) )
        {
          $instance->_storage->preload($instance->_language, $indexed_languagevars);
          //$instance->_storage->load($this->_language);
        }
        
        // Post-load indexing
        if( $instance->_indexing_post_load )
          register_shutdown_function(array(&$instance, '_post_load_indexing'));
      }
    }
    
    return $instance;
  }
  
  //
  // END PRIVATE METHOD _init()
  //
  
  
  
  
  
  
  //
  // PRIVATE METHOD getStorage()
  //
  
  function &getStorage($type='mysql')
  {
    static $storages;
    
    if( !is_array($storages) ) $storages = array();
    if( !empty($storages[$type]) ) return $storages[$type];
    
    if( !class_exists("SELanguageStorage") )
    {
      require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."storage.php");
    }
    
    $storages[$type] =& SELanguageStorage::getInstance($type);
    
    return $storages[$type];
  }
  
  //
  // END PRIVATE METHOD getStorage()
  //
  
  
  
  
  
  
  //
  // PRIVATE METHOD _getCompiler()
  //
  
  function &_getCompiler()
  {
    static $compiler;
    
    if( !class_exists("SELanguageCompiler") )
    {
      require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."compiler.php");
    }
    
    if( is_null($compiler) ) $compiler =& SELanguageCompiler::getInstance();
    
    return $compiler;
  }
  
  //
  // END PRIVATE METHOD _getCompiler()
  //
  
  
  
  
  
  
  //
  // PRIVATE METHOD _dynamicCompilerLoader()
  //
  
  function _dynamicCompilerLoader($source, &$smarty)
  {
    static $is_loaded;
    
    if( is_null($is_loaded) )
    {
      SELanguage::_getCompiler();
      $is_loaded = TRUE;
    }
    
    return $source;
  }
  
  //
  // END PRIVATE METHOD _dynamicCompilerLoader()
  //
  
  
  
  
  
  
  /* -------------------- Language Pack Methods -------------------- */
  
  //
  // PUBLIC METHOD select()
  //
  // Use of $user_object is deprecated
  //
  
  function select(&$user_object)
  {
    global $setting, $user, $admin;
    $instance =& SELanguage::_init();
    
    // Load languages
    $languages =& $instance->_languages();
    
    // Set default (gets first key)
    reset($languages);
    $selected_language = key($languages);
    
    // ---------- 1. ADMIN USER SETTING ----------     
    if( is_a($user_object, 'SEAdmin') && $admin->admin_exists && !empty($admin->admin_info['admin_language_id']) && in_array($admin->admin_info['admin_language_id'], array_keys($languages)) )
    {
      $selected_language = $admin->admin_info['admin_language_id'];
    }
    
    // ---------- 2. USER SETTING ----------     
    elseif( is_a($user_object, 'SEUser') && $setting['setting_lang_allow'] && $user->user_exists && !empty($user->user_info['user_language_id']) && in_array($user->user_info['user_language_id'], array_keys($languages)) )
    {
      $selected_language = $user->user_info['user_language_id'];
    }
    
    // ---------- 3. COOKIE SETTING ---------- 
    elseif( $setting['setting_lang_anonymous'] && !empty($_COOKIE['se_language_anonymous']) && in_array($_COOKIE['se_language_anonymous'], array_keys($languages)) )
    {
      $selected_language = $_COOKIE['se_language_anonymous'];
    }
    
    // ---------- 4. AUTODETECT (COOKIE) ---------- 
    elseif( $setting['setting_lang_autodetect'] && !empty($_COOKIE['se_language_autodetect']) && in_array($_COOKIE['se_language_autodetect'], array_keys($languages)) )
    {
      $selected_language = $_COOKIE['se_language_autodetect'];
    }
    
    // ---------- 5. AUTODETECT (EXECUTE) ---------- 
    elseif( $setting['setting_lang_autodetect'] && !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) )
    {
      $selected_language = $instance->_autodetect($_SERVER['HTTP_ACCEPT_LANGUAGE'], $selected_language);
      $instance->set_cookie('se_language_autodetected', $selected_language);
    }
    
    return ($instance->_language = $selected_language);
  }
  
  //
  // END PUBLIC METHOD select()
  //
  
  
  
  
  
  
  //
  // PRIVATE METHOD _autodetect()
  //
  
  function _autodetect($http_accept_language, $default_lang)
  {
    $instance =& SELanguage::_init();
    
    // Split and removes those q= things
    $http_accept_language_array = array_filter(preg_split('/[;,.q=\d]+/', $http_accept_language));
    
    if( empty($http_accept_language_array) || !is_array($http_accept_language_array) )
      return $default_lang;
    
    $languages = $instance->_languages();
    
    // We want the first one (should be in order of priority)
    // Loop through http_accept_languages
    
    foreach( $http_accept_language_array as $http_accept_language_item ) 
    {
      // Loop through regexes
      foreach( $languages as $language_index=>$language_data )
      {
        if( empty($language_data['language_autodetect_regex']) || !preg_match($language_data['language_autodetect_regex'], $http_accept_language_item) ) continue;
        return $language_index;
      }
    }
    
    // Use the default language if no matches
    return $default_lang;
  }
  
  //
  // END PRIVATE METHOD _autodetect()
  //
  
  
  
  
  
  
  //
  // PUBLIC METHOD info()
  //
  
  function info($info_key=NULL)
  {
    $instance =& SELanguage::_init();
    
    // Charset
    if( !empty($info_key) && strtolower($info_key)=="charset" ) return $instance->_charset;
    
    // Indices
    if( !empty($info_key) && strtolower($info_key)=="indices" ) return $instance->_indices;
    
    $languages =& $instance->_languages();
    
    // Whole array
    if( empty($info_key) ) return $languages[$instance->_language];
    
    // A key
    return $languages[$instance->_language][$info_key];
  }
  
  //
  // END PUBLIC METHOD info()
  //
  
  
  
  
  
  
  //
  // PUBLIC METHOD _languages()
  //
  
  function &_languages()
  {
    $instance =& SELanguage::_init();
    
    $languages = NULL;
    if( is_object($instance->_storage) )
    {
      $languages = $instance->_storage->_languages();
    }
    
    return $languages;
  }
  
  //
  // END PUBLIC METHOD _languages()
  //
  
  
  
  
  
  
  //
  // PUBLIC METHOD set_cookie()
  //
  
  function set_cookie($cookie_name, $cookie_value, $cookie_time=NULL)
  {
    if( headers_sent() ) return;
    $cookie_time = ( empty($cookie_time) ? 0 : $cookie_time );
	  setcookie("$cookie_name", $cookie_value, $cookie_time, "/");
  }
  
  //
  // END PUBLIC METHOD set_cookie()
  //
  
  
  
  
  
  
  //
  // PRIVATE METHOD _post_load_indexing()
  //
  
  function _post_load_indexing()
  {
    global $page;
    
    $instance =& SELanguage::_init();
    
    $languagevar_ids = array_keys($instance->_storage->_language_variables);
    
    $non_globals = array_diff($languagevar_ids, $instance->_indexing_vars_global);
    
    echo 'test';
    
    if( !empty($non_globals) )
    {
      SELanguage::_update_index_file($non_globals, $page.'.tpl');
    }
  }
  
  //
  // END PRIVATE METHOD _post_load_indexing()
  //
  
  
  
  
  
  
  //
  // PRIVATE METHOD _update_index_file()
  //
  
  function _update_index_file($language_vars, $template_name='globals')
  {
    if( !is_array($language_vars) || empty($language_vars) )
      return;
    
    $index_file = SE_ROOT.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.'indexes'.DIRECTORY_SEPARATOR.$template_name.'.php';
    $index_file_exists = file_exists($index_file);
    $index_file_mode = ( $index_file_exists ? 'r+' : 'w');
    $newline = "\n";
    
    if( !($fh = fopen($index_file, $index_file_mode)) )
      return;
      //die('FILE ERR: '.$index_file);
    
    // Check for existing vars and remove
    if( $index_file_exists )
    {
      while( $line = fgets($fh, 256) )
      {
        $line = trim($line);
        if( empty($line) ) continue;
        
        $exists_index = array_search($line, $language_vars);
        if( $exists_index!==FALSE )
          unset($language_vars[$exists_index]);
      }
    }
    
    // Pointer should now be at end of file
    foreach( $language_vars as $languagevar_id )
    {
      if( is_numeric($languagevar_id) )
        fwrite($fh, $languagevar_id.$newline);
    }
    
    fclose($fh);
  }
  
  //
  // END PRIVATE METHOD _update_index_file()
  //
  
  
  
  
  
  
  
  
  /* ---------------------- Smarty Execution Methods ----------------------- */
  
  //
  // PRIVATE METHOD _javascript_redundancy_filter()
  //
  
  function _javascript_redundancy_filter($lang_ids=array())
  {
    $instance =& SELanguage::_init();
    
    $output = array_diff($lang_ids, $instance->_language_variables_js_sent);
    $instance->_language_variables_js_sent = array_merge($output, $instance->_language_variables_js_sent);
    
    return $output;
  }
  
  //
  // END PRIVATE METHOD _javascript_redundancy_filter()
  //
  
  
  
  
  
  
  
  
  
  /* ------------------------- Deprecated Methods -------------------------- */
  
  //
  // PRIVATE METHOD _preload_multi()
  //
  // Deprecated 3.08
  // Moved to SELanguageStorage
  //
  
  function _preload_multi()
  {
    $instance =& SELanguage::_init();
    
    if( is_object($instance->_storage) )
    {
      $args = func_get_args();
      $instance->_storage->preload($instance->_language, $args);
    }
    
    return NULL;
  }
  
  //
  // END PRIVATE METHOD _preload_multi()
  //
  
  
  
  
  
  
  //
  // PRIVATE METHOD _preload()
  //
  // Sets a lang var id to be loaded.
  // Arguments: One lang var id, sprintf arguments as an array
  // Returns: A reference to that value in _lang
  //
  
  function &_preload($lang_var_id)
  {
    $instance =& SELanguage::_init();
    
    $lvv = NULL;
    if( is_object($instance->_storage) )
    {
      $lvv =& $instance->_storage->preload_ref($instance->_language, $lang_var_id);
    }
    
    return $lvv;
  }
  
  //
  // END PRIVATE METHOD _preload()
  //





  //
  // PUBLIC METHOD get()
  //
  //  Gets or returns a language variable
  //
  
  function get($lang_var_id, $sprintf_args=NULL)
  {
    $instance =& SELanguage::_init();
    
    if( is_object($instance->_storage) )
    {
      return $instance->_storage->get($instance->_language, $lang_var_id, $sprintf_args);
    }
    
    return NULL;
  }
  
  //
  // END PUBLIC METHOD get()
  //
  
  
  
  
  
  
  //
  // PRIVATE METHOD _get()
  //
  // Deprecated 3.08
  // Moved to SELanguageStorage
  //
  
  function _get($id)
  {
    $instance =& SELanguage::_init();
    
    if( is_object($instance->_storage) )
    {
      return $instance->_storage->get($instance->_language, $id);
    }
    
    return NULL;
  }
  
  //
  // END PRIVATE METHOD _get()
  //
  
  
  
  
  
  
  //
  // PUBLIC METHOD list_packs()
  //
  // Deprecated 3.08
  //
  
  function list_packs()
  {
    return SELanguage::_languages();
  }
  
  //
  // END PUBLIC METHOD list_packs()
  //
  
  
  
  
  
  
  //
  // PUBLIC METHOD edit()
  //
  //  Adds a new language variable
  //
  
  function edit($variable_id, $value, $language_id=NULL, $variable_type=LANGUAGE_INDEX_CUSTOM)
  {
    $instance =& SELanguage::_init();
    
    if( is_object($instance->_storage) )
    {
      return $instance->_storage->edit($variable_id, $value, $language_id, $variable_type);
    }
    
    return NULL;
  }
  
  //
  // END PUBLIC METHOD edit()
  //
  
  
  
  
  
  
  //
  // PUBLIC METHOD load()
  //
  
  function load($overwrite=FALSE)
  {
    $instance =& SELanguage::_init();
    
    if( is_object($instance->_storage) )
    {
      return $instance->_storage->load($instance->_language, $overwrite);
    }
    
    return NULL;
  }
  
  //
  // END PUBLIC METHOD load()
  //
}

?>