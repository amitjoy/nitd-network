<?php


class SECore
{
  function &getInstance()
  {
    static $instance;
    
    if( !is_a($instance, 'SECore') )
    {
      $instance = new SECore();
    }
    
    return $instance;
  }
  
  
  
  function &getSettings()
  {
    static $settings;
    
    if( !is_array($settings) )
    {
      $cache = SECache::getInstance('serial', array('lifetime' => 3600));
      
      // Get from cache
      if( is_object($cache) )
      {
        $settings = $cache->get('site_settings');
      }
      
      // Get from database
      if( !is_array($settings) )
      {
        $database = SEDatabase::getInstance();
        $resource = $database->database_query("SELECT * FROM se_settings LIMIT 1");
        $settings = $database->database_fetch_assoc($resource);
        
        // Store in cache
        if( is_object($cache) )
        {
          $cache->store($settings, 'site_settings');
        }
      }
    }
    
    return $settings;
  }
  
  
  
  function &getPlugins()
  {
    static $plugins;
    
    if( !is_array($plugins) )
    {
      $cache = SECache::getInstance('serial', array('lifetime' => 3600));
      
      // Get from cache
      if( is_object($cache) )
      {
        $plugins = $cache->get('site_plugins');
      }
      
      // Get from database
      if( !is_array($plugins) )
      {
        $database = SEDatabase::getInstance();
        $resource = $database->database_query("SELECT plugin_type, plugin_icon FROM se_plugins WHERE plugin_disabled=0 ORDER BY plugin_order ASC");
        $plugins = $database->database_load_all_assoc('plugin_type');
        
        // Store in cache
        if( is_object($cache) )
        {
          $cache->store($plugins, 'site_plugins');
        }
      }
    }
    
    return $plugins;
  }
  
  
  
  function &getLanguages()
  {
    static $languages;
    
    if( !is_array($languages) )
    {
      $cache = SECache::getInstance('serial', array('lifetime' => 3600));
      
      // Get from cache
      if( is_object($cache) )
      {
        $languages = $cache->get('site_languages');
      }
      
      // Get from database
      if( !is_array($languages) )
      {
        //$database = SEDatabase::getInstance();
        //$resource = $database->database_query("SELECT * FROM se_languages ORDER BY language_default DESC");
        //$languages = $database->database_load_all_assoc('language_id');
        $languages = SELanguage::_languages();
        
        // Store in cache
        if( is_object($cache) )
        {
          $cache->store($languages, 'site_languages');
        }
      }
    }
    
    return $languages;
  }
  
  
  
  function &getSubnetworkInfo($subnet_id)
  {
    static $subnetwork_info;
    
    if( !is_array($subnetwork_info) ) $subnetwork_info = array();
    
    if( !isset($subnetwork_info[$subnet_id]) )
    {
      $cache = SECache::getInstance('serial', array('lifetime' => 3600));
      
      // Get from cache
      if( is_object($cache) )
      {
        $subnetwork_info[$subnet_id] = $cache->get('site_subnetworks_'.$subnet_id);
      }
      
      // Get from database
      if( !is_array($subnetwork_info[$subnet_id]) )
      {
        $database = SEDatabase::getInstance();
        $resource = $database->database_query("SELECT subnet_id, subnet_name FROM se_subnets WHERE subnet_id='{$subnet_id}' LIMIT 1");
        $subnetwork_info[$subnet_id] = $database->database_fetch_assoc($resource);
        
        // Store in cache
        if( is_object($cache) )
        {
          $cache->store($subnetwork_info[$subnet_id], 'site_subnetworks_'.$subnet_id);
        }
      }
    }
    
    return $subnetwork_info[$subnet_id];
  }
}


?>