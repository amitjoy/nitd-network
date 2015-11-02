<?php

class SESanityTestConfiguration extends SESanityTest
{
  var $directive;
  
  var $value;
  
  var $comparision;
  
  var $is_bytes = FALSE;
  
  var $value_actual;
  
  
  
  function SESanityTestConfiguration($options)
  {
    parent::SESanityTest($options);
    
    $this->name = 'configuration_'.strtolower(preg_replace('/[^A-Z0-9_\.-]/i', '', $this->directive));
  }
  
  
  
  function setOptions($options)
  {
    if( isset($options['directive']) ) $this->directive = $options['directive'];
    if( isset($options['value']) ) $this->value = $options['value'];
    if( isset($options['cmp']) ) $this->comparision = $options['cmp'];
    if( isset($options['is_bytes']) ) $this->is_bytes = $options['is_bytes'];
    
    parent::setOptions($options);
  }
  
  
  
  function execute()
  {
    if( $this->preset() )
      return;
    
    $ini_value = ini_get($this->directive);
    
    if( $this->is_bytes )
      $ini_value = $this->getByteValue($ini_value);
    
    $this->value_actual = $ini_value;
    
    switch( $this->comparision )
    {
      case 'on':
        $this->result = ( $ini_value == 'on' || $ini_value == '1' );
        break;
      case 'off':
        $this->result = ( $ini_value == 'off' || $ini_value == '0' || $ini_value == '' );
        break;
        
      default:
      case '==':
        $this->result = ( $ini_value == $this->value );
        break;
      case '!=':
        $this->result = ( $ini_value != $this->value );
        break;
      case '>=':
        $this->result = ( $ini_value >= $this->value );
        break;
      case '<=':
        $this->result = ( $ini_value <= $this->value );
        break;
      case '>':
        $this->result = ( $ini_value >  $this->value );
        break;
      case '<':
        $this->result = ( $ini_value <  $this->value );
        break;
      case '&':
        $this->result = ( $ini_value &  $this->value );
        break;
      case '|':
        $this->result = ( $ini_value |  $this->value );
        break;
    }
  }
  
  
  
  function merge($new_object)
  {
    switch( TRUE )
    {
      // When checking for less than, take the lower value
      case ( strpos($this->comparision, "<")!==FALSE && strpos($new_object->comparision, "<")!==FALSE ):
        $this->value = min($this->value, $new_object->value);
        break;
      // When checking for greater than, take the higher value
      case ( strpos($this->comparision, ">")!==FALSE && strpos($new_object->comparision, ">")!==FALSE ):
        $this->value = max($this->value, $new_object->value);
        break;
      
      // TODO: Probably should throw an error for conflicting on, off, and == comparisions
      default:
        die("Conflicting test rules added");
        break;
    }
  }
  
  
  
  function getValue()
  {
    if( isset($this->value_formatted) )
      return $this->value_formatted;
    
    // On/Off
    if( $this->comparision=="on" || $this->comparision=="off" )
      return ( $this->value_actual ? 'On' : 'Off' );
    
    // Bytes
    if( $this->is_bytes )
      return $this->makeByteValue($this->value_actual);
    
    return $this->value_actual;
  }
  
  
  
  
  
  
  // Helper functions
  function getByteValue($byte_string)
  {
    $val = trim($byte_string);
    $last = strtolower($val[strlen($val)-1]);
    switch($last)
    {
      // The 'G' modifier is available since PHP 5.1.0
      case 'g':
        $val *= 1024;
      case 'm':
        $val *= 1024;
      case 'k':
        $val *= 1024;
    }
    
    return $val;
  }
  
  
  function makeByteValue($byte_int, $round=2)
  {
    $byte_names = array('', 'K', 'M', 'G', 'T');
    $byte_power = 0;
    while( $byte_int>1024 )
    {
      $byte_int = $byte_int / 1024;
      $byte_power++;
    }
    
    if( $round!==FALSE ) $byte_string = round($byte_int, $round);
    $byte_string .= $byte_names[$byte_power];
    $byte_string .= 'B';
    return $byte_string;
  }
}

?>