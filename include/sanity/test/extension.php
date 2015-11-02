<?php

class SESanityTestExtension extends SESanityTest
{
  var $extension;
  
  var $version_required;
  
  
  // Info
  var $extension_detected = FALSE;
  
  var $version_actual;
  
  
  
  function SESanityTestExtension($options)
  {
    parent::SESanityTest($options);
    
    $this->name = 'extension_'.strtolower(preg_replace('/[^A-Z0-9_\.-]/i', '', $this->extension));
  }
  
  
  
  function setOptions($options)
  {
    if( isset($options['extension']) ) $this->extension = $options['extension'];
    if( isset($options['version']) ) $this->version_required = $options['version'];
    
    parent::setOptions($options);
  }
  
  
  
  function execute()
  {
    if( $this->preset() )
      return;
    
    $this->extension_detected = extension_loaded($this->extension);
    $this->version_actual = phpversion($this->extension);
    
    // If no version specified, set to detected
    if( empty($this->version_required) )
    {
      $this->result = (bool) $this->extension_detected;
      return;
    }
    
    // Check version
    $this->result = ( $this->version_actual && version_compare($this->version_actual, $this->version_required, '>=') );
  }
  
  
  
  function merge($new_object)
  {
    $this->version_required = max($this->version_required, $new_object->version_required);
  }
  
  
  
  function getValue()
  {
    if( $this->version_actual )
      return $this->version_actual;
    
    if( $this->extension_detected )
      return "Detected";
    
    return "Not detected";
  }
}

?>