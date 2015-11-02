<?php

/* $Id: file.php 8 2009-01-11 06:02:53Z john $ */


defined('SE_PAGE') or exit();


class SELanguageStorageFile extends SELanguageStorage
{
  //
  // PRIVATE METHOD SELanguageStorageFile()
  //
  
  function SELanguageStorageFile()
  {
    parent::SELanguageStorage();
  }
  
  //
  // END PRIVATE METHOD SELanguageStorageFile()
  //
  
  
  
  
  
  //
  // PRIVATE METHOD _languages()
  //
  
  function &_languages($reload=FALSE)
  {
    global $database;
    
    if( $reload || empty($this->_languages) )
    {
      require_once(SE_ROOT.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.'languages.php');
      $this->_languages = $languages;
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
    // For now, we don't support indices
    if( empty($variable_id) || !is_numeric($variable_id) ) return FALSE;
    
    $this->_rewrite_language_file($language_id, array($variable_id=>$value));
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
      $filename = $this->language_file_name($language_id);
      $languagevar_array = $this->read_language_file($filename, array($lang_var_id));
      $languagevar_array = $languagevar_array['languagevars'];
      
      $lang_var = $this->_language_variables[$lang_var_id] = $languagevar_array[$lang_var_id];
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
    
    $filename = $this->language_file_name($language_id);
    $languagevar_array = $this->read_language_file($filename, $load_keys);
    $languagevar_array = $languagevar_array['languagevars'];
    
    if( is_array($languagevar_array) )
    {
      foreach( $languagevar_array as $languagevar_id=>$languagevar_value )
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
    //if( empty($languagevar_array) || !is_array($languagevar_array) ) return;
    return; // NOT YET IMPLEMENTED
  }
  
  //
  // END PUBLIC METHOD import()
  //
  
  
  
  
  
  
  //
  // PRIVATE METHOD language_file_name()
  //
  
  function language_file_name($language_id, $range_name=NULL)
  {
    $instance =& SELanguage::_init();
    if( empty($instance->_languages) ) $instance->_languages();
    
    $language_name = $instance->_languages[$language_id]['language_code'];
    $language_file = 'language_'.$language_name.( $range_name ? '_'.$range_name : '' ).'.php';
    $language_file = SE_ROOT.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$language_file;
    
    return $language_file;
  }
  
  //
  // END PUBLIC METHOD language_file_name()
  //
  
  
  
  
  
  
  //
  // PUBLIC METHOD make_language_file()
  //
  
  function make_language_file($language_id, $languagevars=array(), $language_info=array())
  {
    $instance =& SELanguage::_init();
    if( empty($instance->_storage->_languages) ) $instance->_storage->_languages();
    
    $language_info = array_merge($instance->_storage->_languages[$language_id], $language_info);
    if( empty($language_info['language_record_first']) ) $language_info['language_record_first'] = min(array_keys($languagevars));
    if( empty($language_info['language_record_last']) ) $language_info['language_record_last'] = max(array_keys($languagevars));
    
    ksort($languagevars);
    
    // Generate file data
    $newline = "\n";
    $file_contents = '';
    
    $file_contents .= '<?php die("Access Denied"); ?>' . $newline;
    $file_contents .= 'LANGUAGE_CODE:=' . $language_info['language_code'] . $newline;
    $file_contents .= 'LANGUAGE_NAME:=' . $language_info['language_name'] . $newline;
    $file_contents .= 'LANGUAGE_LOCALE:=' . $language_info['language_setlocale'] . $newline;
    $file_contents .= 'LANGUAGE_AUTODETECT:=' . $language_info['language_autodetect_regex'] . $newline;
    $file_contents .= 'LANGUAGE_RECORD_FIRST:=' . $language_info['language_record_first'] . $newline;
    $file_contents .= 'LANGUAGE_RECORD_LAST:=' . $language_info['language_record_last'] . $newline;
    //if( $range_name ) $file_contents .= 'LANGUAGE_RANGE:=' . $range_name . $newline;
    
    foreach( $languagevars as $languagevar_id=>$Languagevar_value )
    {
      if( !trim($Languagevar_value) ) continue;
      
      // Normal
      if( strpos($Languagevar_value, "\n")===FALSE )
      {
        $file_contents .= $languagevar_id . ':=' . $Languagevar_value.$newline;
      }
      
      // Contains a newline
      else
      {
        $file_contents .= $languagevar_id . ':=<<<LANG' . $newline;
        $file_contents .= $Languagevar_value . $newline;
        $file_contents .= 'LANG;' . $newline;
      }
    }
    
    /*
    if( $language_file )
    {
      // Open file
      if( !($fh = fopen($language_file, 'w')) )
      {
        echo 'FILE ERROR';
        return FALSE;
      }
      
      fwrite($fh, 'LANG;'.$newline);
      fclose($fh);
      
      return TRUE;
    }
    */
    
    return $file_contents;
  }
  
  //
  // END PUBLIC METHOD make_language_file()
  //
  
  
  
  
  
  
  
  //
  // PUBLIC METHOD read_language_file()
  //
  
  function read_language_file($language_file, $languagevar_ids=NULL)
  {
    // Open file
    if( !($fh = fopen($language_file, 'r')) )
    {
      echo 'FILE ERROR';
      return FALSE;
    }
    
    // Skip over initial php die
    fgets($fh, 1024);
    
    $mode = 0;
    $language_info = NULL;
    $language_code = NULL;
    $languagevar_id = NULL;
    $languagevar_value = NULL;
    $heredoc_id = NULL;
    $languagevar_array = array();
    
    while( $line=fgets($fh, 1024) )
    {
      if( $mode==0 )
      {
        $split = split(':=', $line, 2);
        $languagevar_id = $split[0];
        
        // Check for language code
        if( trim($split[0]) && !is_numeric($split[0]) )
        {
          $key = strtolower($split[0]);
          $language_info[] = trim($split[1]);
          
          if( $key=="language_code" )
            $language_code = trim($split[1]);
        }
        
        // Check for heredoc syntax
        elseif( $split[1] && substr($split[1], 0, 3)=="<<<" )
        {
          $mode = 1;
          $split[1] = rtrim($split[1]);
          $heredoc_id = substr($split[1], 3, strlen($split[1])-3);
          $languagevar_value = '';
        }
        
        // Normal
        elseif( trim($split[0]) )
        {
          $languagevar_value = rtrim($split[1], "\n\r");
          
          // Save to array
          if( !$languagevar_ids || in_array($languagevar_id, $languagevar_ids) )
          {
            $languagevar_array[$languagevar_id] = $languagevar_value;
            if( is_array($languagevar_ids) && count($languagevar_array)==count($languagevar_ids) )
              $mode = 2;
          }
        }
      }
      
      elseif( $mode==1 )
      {
        // Ending heredoc syntax
        if( substr($line, 0, strlen($heredoc_id)+1)==$heredoc_id.";" )
        {
          $mode = 0;
          $heredoc_id = NULL;
          
          // Trim the last line return off
          $languagevar_value = rtrim($languagevar_value, "\n\r");
          
          // Save to array
          if( !$languagevar_ids || in_array($languagevar_id, $languagevar_ids) )
          {
            $languagevar_array[$languagevar_id] = $languagevar_value;
            if( is_array($languagevar_ids) && count($languagevar_array)==count($languagevar_ids) )
              $mode = 2;
          }
        }
        
        // Continuing heredoc syntax
        else
        {
          $languagevar_value .= $line;
        }
      }
      
      // Quit
      elseif( $mode==2 )
      {
        break;
      }
    }
    
    return array(
      'language_code' => $language_code,
      'language_info' => $language_info,
      'languagevars' => $languagevar_array
    );
  }
  
  //
  // END PUBLIC METHOD read_language_file()
  //
  
  
  
  
  
  
  
  //
  // PRIVATE METHOD _rewrite_language_file()
  //
  
  function _rewrite_language_file($language_file, $languagevar_array)
  {
    // Open file
    if( !($fh = fopen($language_file, 'r')) )
    {
      echo 'FILE ERROR';
      return FALSE;
    }
    
    // Skip over initial php die
    $new_file_contents = fgets($fh, 2048);
    
    $newline = "\n";
    $mode = 0;
    $languagevar_id = NULL;
    $languagevar_value = NULL;
    $heredoc_id = NULL;
    
    while( $line=fgets($fh, 2048) )
    {
      if( $mode==0 )
      {
        $split = split(':=', $line, 2);
        $languagevar_id = $split[0];
        
        // Check for language code
        if( $split[0]=="LANGUAGE_CODE" || $split[0]=="LANGUAGE_RANGE" )
        {
          // Pass through
          $new_file_contents .= $line;
        }
        
        // Check for heredoc syntax
        elseif( $split[1] && substr($split[1], 0, 3)=="<<<" )
        {
          $mode = 1;
          $split[1] = rtrim($split[1]);
          $heredoc_id = substr($split[1], 3, strlen($split[1])-3);
          $languagevar_value = '';
        }
        
        // Normal
        elseif( trim($split[0]) )
        {
          // Check if needs to be updated
          if( isset($languagevar_array[$languagevar_id]) )
          {
            // Use normal syntax
            if( strpos($languagevar_array[$languagevar_id], "\n")===FALSE )
            {
              $new_file_contents .= $languagevar_id . ":=" . $languagevar_array[$languagevar_id] . $newline;
            }
            
            // Use heredoc syntax
            else
            {
              $new_file_contents .= $languagevar_id . ":=<<<LANG" . $newline;
              $new_file_contents .= $languagevar_array[$languagevar_id] . $newline;
              $new_file_contents .= "LANG;" . $newline;
            }
            
            // Unset after updating so we can write the rest to the end
            unset($languagevar_array[$languagevar_id]);
          }
          
          // Otherwise pass through
          else
          {
            $new_file_contents .= $line;
          }
        }
      }
      
      elseif( $mode==1 )
      {
        // Ending heredoc syntax
        if( substr($line, 0, strlen($heredoc_id)+1)==$heredoc_id.";" )
        {
          $mode = 0;
          $heredoc_id = NULL;
          
          // Check if language var needs to be updated
          if( isset($languagevar_array[$languagevar_id]) )
          {
            // Use normal syntax
            if( strpos($languagevar_array[$languagevar_id], "\n")===FALSE )
            {
              $new_file_contents .= $languagevar_id . ":=" . $languagevar_array[$languagevar_id] . $newline;
            }
            
            // Use heredoc syntax
            else
            {
              $new_file_contents .= $languagevar_id . ":=<<<LANG" . $newline;
              $new_file_contents .= $languagevar_array[$languagevar_id] . $newline;
              $new_file_contents .= "LANG;" . $newline;
            }
            
            // Unset after updating so we can write the rest to the end
            unset($languagevar_array[$languagevar_id]);
          }
          
          // Otherwise pass through
          else
          {
            // Trim the last line return off
            $languagevar_value = rtrim($languagevar_value, "\n\r");
            
            $new_file_contents .= $languagevar_id . ":=<<<LANG" . $newline;
            $new_file_contents .= $languagevar_value . $newline;
            $new_file_contents .= "LANG;" . $newline;
          }
        }
        
        // Continuing heredoc syntax
        else
        {
          $languagevar_value .= $line;
        }
      }
      
      // Quit
      elseif( $mode==2 )
      {
        break;
      }
    }
    
    // Write extra vars to the end
    if( is_array($languagevar_array) && !empty($languagevar_array) )
    {
      foreach( $languagevar_array as $languagevar_id=>$languagevar_value )
      {
        // Use normal syntax
        if( strpos($languagevar_value, "\n")===FALSE )
        {
          $new_file_contents .= $languagevar_id . ":=" . $languagevar_value . $newline;
        }
        
        // Use heredoc syntax
        else
        {
          $new_file_contents .= $languagevar_id . ":=<<<LANG" . $newline;
          $new_file_contents .= $languagevar_value . $newline;
          $new_file_contents .= "LANG;" . $newline;
        }
      }
    }
    
    fclose($fh);
    
    // Open file
    if( !($fh = fopen($language_file, 'w')) )
    {
      echo 'FILE ERROR';
      return FALSE;
    }
    
    
    fwrite($fh, $new_file_contents);
    
    fclose($fh);
  }
  
  //
  // END PUBLIC METHOD _rewrite_language_file()
  //
}

?>