<?php

/* $Id: storage.php 18 2009-01-13 10:34:19Z john $ */


defined('SE_PAGE') or exit();


class SELanguageStorage
{
  // Contains all of the enabled languages for this storage method
  var $_languages = array();
  
  // Contains all of the loaded lang vars for this storage method
  // STRUCT: [language_id][languagevar_mask][languagevar_id][languagevar_value]
  var $_language_variables = array();
  
  // Indicates that a load is required
  var $_load_required = FALSE;
  
  // Number of loads run
  var $_load_count = 0;
  
  
  
  function SELanguageStorage()
  {
    //$this->_languages();
    $this->_language_variables = array();
  }
  
  
  function &getInstance($type="mysql")
  {
		$type = strtolower(preg_replace('/[^A-Z0-9_\.-]/i', '', $type));
		$class = 'SELanguageStorage'.ucfirst($type);
    
		if( !class_exists($class) )
		{
			$path = dirname(__FILE__).DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.$type.'.php';
      
			if( file_exists($path) )
      {
				require_once($path);
			}
      else
      {
				die('Unable to load language storage: '.$type);
			}
    }
    
		$instance = new $class();
    
		return $instance;
  }
  
  
  function getStorageHandlers()
  {
    
  }
  
  
  
  
  
  //
  // PUBLIC METHOD preload_ref()
  //
  
  
  function &preload_ref($language_id, $languagevar_id)
  {
    if( is_numeric($languagevar_id) && !array_key_exists($languagevar_id, $this->_language_variables) )
    {
      $this->_language_variables[$languagevar_id] = NULL;
      $this->_load_required = TRUE;
    }
    
    return $this->_language_variables[$languagevar_id];
  }
  
  //
  // END PRIVATE METHOD preload_ref()
  //
  
  
  
  
  
  //
  // PUBLIC METHOD preload()
  //
  
  
  function preload($language_id, $languagevar_ids)
  {
    if( !is_array($languagevar_ids) ) $languagevar_ids = array($languagevar_ids);
    
    foreach( $languagevar_ids as $languagevar_id )
    {
      if( is_numeric($languagevar_id) && !array_key_exists($languagevar_id, $this->_language_variables) )
      {
        $this->_language_variables[$languagevar_id] = NULL;
        $this->_load_required = TRUE;
      }
    }
  }
  
  //
  // END PUBLIC METHOD preload()
  //
  
  
  function load($language_id, $overwrite=FALSE)
  {
    if( $this->_load_count===0 )
      $this->_loadGlobalValues($language_id, FALSE);
    elseif( $this->_load_count===1 )
      $this->_storeGlobalValues($language_id);
    
    $this->_load_count++;
    
    // Skip if load list is empty, or if a load isn't required and we aren't overwriting
    if( empty($this->_language_variables) || (!$this->_load_required && !$overwrite) )
    {
      $this->_load_required = FALSE;
      return;
    }
    
    // Are we going to overwrite old values?
    if( $overwrite )
    {
      $load_keys = array_keys($this->_language_variables);
    }
    else
    {
      // This will filter out non-empty values and then return their keys
      $load_keys = array_keys(array_filter($this->_language_variables, create_function('$var', 'return is_null($var);')));
    }
    
    $this->_load_required = FALSE;
    
    return $load_keys;
  }
  
  
  function edit()
  {
    return NULL;
  }
  
  
  function get()
  {
    return NULL;
  }
  
  
  function set()
  {
    return NULL;
  }
  
  
  function _storeGlobalValues($language_id)
  {
    //echo " store.{$this->_load_count} ";
    $language_object = SELanguage::_init();
    $cache_object = SECache::getInstance('serial');
    if( !is_object($cache_object) || empty($language_object->_indexing_vars_global) ) return;
    
    // Return if already set
    $global_language_values = $cache_object->get('language_globals_'.$language_id);
    if( !empty($global_language_values) ) return;
    
    
    $global_language_values = array();
    foreach( $language_object->_indexing_vars_global as $global_languagevar_id )
    {
      if( empty($this->_language_variables[$global_languagevar_id]) ) continue;
      $global_language_values[$global_languagevar_id] = $this->_language_variables[$global_languagevar_id];
    }
    
    $cache_object->store($global_language_values, 'language_globals_'.$language_id);
  }
  
  
  function _loadGlobalValues($language_id, $overwrite=FALSE)
  {
    //echo " load.{$this->_load_count} ";
    $language_object = SELanguage::_init();
    $cache_object = SECache::getInstance('serial');
    if( !is_object($cache_object) ) return;
    
    // Return if empty
    $global_language_values = $cache_object->get('language_globals_'.$language_id);
    if( empty($global_language_values) ) return;
    
    foreach( $global_language_values as $global_languagevar_id=>$global_languagevar_value )
    {
      if( !$overwrite && !empty($this->_language_variables[$global_languagevar_id]) ) continue;
      if( empty($global_languagevar_value) ) continue;
      $this->_language_variables[$global_languagevar_id] = $global_languagevar_value;
    }
  }
}

?>