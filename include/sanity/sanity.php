<?php

include_once dirname(__FILE__).DIRECTORY_SEPARATOR."test.php";
include_once dirname(__FILE__).DIRECTORY_SEPARATOR."helpers.php";

class SESanity
{
  var $tests = array();
  
  var $categories = array();
  
  var $root = '.';
  
  
  
  function SESanity()
  {
    // ?
  }
  
  
  
  function setRoot($path)
  {
    $this->root = $path;
  }
  
  
  
  function &getInstance()
  {
    static $instance;
    
    if( is_null($instance) )
    {
      $instance = new SESanity();
    }
    
    return $instance;
  }
  
  
  
  function registerCategory($name, $options=array())
  {
    if( !isset($options['lang_title']) ) $options['lang_title'] = ucfirst(strtolower($name));
    $this->categories[$name] = $options;
  }
  
  
  
  function register($type, $options=array())
  {
    $test =& SESanityTest::getInstance($type, $options);
    $name = $test->getName();
    
    if( isset($this->tests[$name]) )
    {
      $this->tests[$name]->merge($test);
    }
    else
    {
      $this->tests[$name] =& $test;
    }
  }
  
  
  
  function register_multiple($rules)
  {
    if( !is_array($rules) ) return;
    foreach( $rules as $index=>$options )
    {
      $type = $options['type'];
      unset($options['type']);
      $this->register($type, $options);
    }
  }
  
  
  
  function execute()
  {
    foreach( $this->tests as $test_name=>$test_object )
      $this->tests[$test_name]->execute();
  }
  
  
  
  function getCategory($category)
  {
    $keys = array();
    foreach( $this->tests as $test_name=>$test_object )
      if( $test_object->category == $category )
        $keys[] = $test_name;
    return $keys;
  }
  
  
  
  function getCategories()
  {
    $keys = array();
    foreach( $this->tests as $test_name=>$test_object )
    {
      if( !isset($keys[$this->tests[$test_name]->category]) ) 
        $keys[$this->tests[$test_name]->category] = array();
      $keys[$this->tests[$test_name]->category][] = $test_name;
    }
    return $keys;
  }
  
  
  
  function isCritical()
  {
    foreach( $this->tests as $test_name=>$test_object )
      if( !$this->tests[$test_name]->result && $this->tests[$test_name]->is_critical )
        return TRUE;
    
    return FALSE;
  }
}

?>