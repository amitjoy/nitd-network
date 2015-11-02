<?php

class SESanityTestCustom extends SESanityTest
{
  var $value_formatted;
  
  
  
  function SESanityTestCustom($options)
  {
    parent::SESanityTest($options);
    
    $this->name = 'custom_'.$options['name'];
  }
  
  
  
  function execute()
  {
    if( $this->preset() )
      return;
  }
  
  
  
  function getValue()
  {
    return $this->value_formatted;
  }
}

?>