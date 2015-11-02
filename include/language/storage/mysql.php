<?php

/* $Id: mysql.php 27 2009-01-18 20:40:50Z john $ */


defined('SE_PAGE') or exit();


class SELanguageStorageMysql extends SELanguageStorage
{
  //
  // PRIVATE METHOD _languages()
  //
  
  function &_languages($reload=FALSE)
  {
    global $database;
    
    if( $reload || empty($this->_languages) )
    {
      $resource = $database->database_query("SELECT * FROM se_languages ORDER BY language_default DESC");
      
      if( empty($resource) || !$database->database_num_rows($resource) ) return FALSE;
      
      $this->_languages = array();
      while( $row=$database->database_fetch_assoc($resource) )
      {
        $this->_languages[$row['language_id']] = $row;
      }
    }
    
    return $this->_languages;
  }
  
  //
  // END PRIVATE METHOD _languages()
  //
  
  
  
  
  
  //
  // PUBLIC METHOD edit()
  //
  
  function edit($variable_id, $value, $language_id=NULL, $variable_type=LANGUAGE_INDEX_CUSTOM)
  {
    global $database;
    
    $instance = SELanguage::_init();
    
    if( empty($language_id) ) $language_id = SELanguage::info("language_id");
    if( empty($instance->_indices[$variable_type]) ) $variable_type = LANGUAGE_INDEX_CUSTOM;
    $value = str_replace("'", "\'", str_replace("\r\n", "", $value));
    
    // VARIABLE ID IS NONZERO
    if($variable_id != 0)
    {
      // CHECK FOR EXISTING VALUE
      $resource = $database->database_query("SELECT NULL FROM se_languagevars WHERE languagevar_id='$variable_id' AND languagevar_language_id='$language_id' LIMIT 1");
      
      if( !$resource ) return FALSE;
      
      // UPDATE EXISTING
      if( $database->database_num_rows($resource) ) {
        $database->database_query("UPDATE se_languagevars SET languagevar_value='$value' WHERE languagevar_id='$variable_id' AND languagevar_language_id='$language_id' LIMIT 1");
      } else {
        $database->database_query("INSERT INTO se_languagevars (languagevar_id, languagevar_language_id, languagevar_value) VALUES ('$variable_id', '$language_id', '$value')");
      }
      
      return $variable_id;
    }
    
    // CREATE A NEW VARIABLE ENTIRELY
    else
    {
      // Insert new
      $int_min = ( isset($instance->_indices[$variable_type][0]) ? $instance->_indices[$variable_type][0] : 0 );
      $int_max = ( isset($instance->_indices[$variable_type][1]) ? $instance->_indices[$variable_type][1] : 0 );
      
      $resource = $database->database_query("SELECT MAX(languagevar_id)+1 FROM se_languagevars WHERE languagevar_id>=$int_min AND languagevar_id<=$int_max");
      $new_id   = $database->database_fetch_array($resource);
      $new_id   = $new_id[0];
      
      if( !$new_id && $int_min && $int_max )
      {
        $new_id = $int_min;
      }
      elseif( !$new_id )
      {
        return FALSE;
      }
      
      $database->database_query("INSERT INTO se_languagevars (languagevar_id, languagevar_language_id, languagevar_value) VALUES ('$new_id', '$language_id', '$value')");
      
      return $new_id;
    }
  }
  
  //
  // END PUBLIC METHOD edit()
  //





  //
  // PUBLIC METHOD get()
  //
  //  Gets or returns a language variable
  //
  
  function get($language_id, $lang_var_id, $sprintf_args=NULL)
  {
    global $database;
    
    // No ID
    if( empty($lang_var_id) )
      return FALSE;
    
    // Already loaded
    if( !empty($this->_language_variables[$lang_var_id]) )
    {
      $lang_var = $this->_language_variables[$lang_var_id];
    }
    
    // Need to get it
    else
    {
      $sql = "SELECT languagevar_value FROM se_languagevars WHERE languagevar_id={$lang_var_id} AND languagevar_language_id='{$language_id}'";
      $resource = $database->database_query($sql);
      $row = $database->database_fetch_assoc($resource);
      
      if( !$row['languagevar_value'] )
        return FALSE;
      
      $lang_var = $this->_language_variables[$lang_var_id] = $row['languagevar_value'];
    }
    
    // Do sprintf stuff
    if( is_array($sprintf_args) )
    {
      $lang_var = vsprintf($lang_var, $sprintf_args);
    }
    
    return $lang_var;
  }
  
  //
  // END PUBLIC METHOD get()
  //
  
  
  
  
  
  
  //
  // PUBLIC METHOD load()
  //
  
  function load($language_id, $overwrite=FALSE)
  {
    global $database;
    
    $load_keys = parent::load($language_id, $overwrite);
    
    if( empty($load_keys) ) return;
    
    // Make query
    $sql = "SELECT languagevar_id, languagevar_value FROM se_languagevars WHERE languagevar_id IN('".join("', '", array_filter($load_keys))."')";
    
    $sql .= " AND languagevar_language_id='{$language_id}'";
    
    if( reset(array_filter($load_keys)) !== FALSE )
    {
      $resource = $database->database_query($sql);
      
      while( $row=$database->database_fetch_assoc($resource) )
      {
        $this->_language_variables[$row['languagevar_id']] = $row['languagevar_value'];
      }
    }
  }
  
  //
  // END PUBLIC METHOD load()
  //
  
  
  
  
  
  
  //
  // PUBLIC METHOD import()
  //
  
  function import($language_id, $languagevar_array, $overwrite=FALSE)
  {
    global $database;
    
    if( empty($languagevar_array) || !is_array($languagevar_array) ) return;
    
    $sql = "SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id='{$language_id}'";
    $resource = $database->database_query($sql);
    
    $lvid_array = array();
    while( $row=$database->database_fetch_assoc($resource) )
      $lvid_array[] = $row['languagevar_id'];
    
    $skipped = array();
    $created = array();
    $updated = array();
    $failed = array();
    foreach( $languagevar_array as $languagevar_id=>$languagevar_value )
    {
      $exists = ( in_array($languagevar_id, $lvid_array) );
      
      if( $exists && $overwrite )
      {
        if( $database->database_query("UPDATE se_languagevars SET languagevar_value='".addslashes($languagevar_value)."' WHERE languagevar_language_id='{$language_id}' && languagevar_id='{$languagevar_id}'") )
          $updated[] = $languagevar_id;
        else
          $failed[] = $languagevar_id;
      }
      
      elseif( !$exists )
      {
        if( $database->database_query("INSERT INTO se_languagevars (languagevar_id, languagevar_language_id, languagevar_value) VALUES ('{$languagevar_id}', '{$language_id}', '".addslashes($languagevar_value)."')") )
          $created[] = $languagevar_id;
        else
          $failed[] = $languagevar_id;
      }
      
      else
      {
        $skipped[] = $languagevar_id;
      }
    }
    
    return array(
      'updated' => $updated,
      'created' => $created,
      'skipped' => $skipped,
      'failed'  => $failed
    );
  }
  
  //
  // END PUBLIC METHOD import()
  //
}

?>