<?php

defined('SE_PAGE') or exit();


class SEBenchmark
{
  var $_start = 0;
  
  var $_uid = '';
  
  var $_prefix = '';
  
  var $_log = NULL;
  
  var $_counters = NULL;
  
  //var $_exclusions = NULL;
  
  
  
  function SEBenchmark($prefix='')
  {
    $this->_start = $this->getmicrotime();
    $this->_uid = str_replace('.', '_', (string)$this->_start).'_'.rand(100, 999);
		$this->_prefix = $prefix;
		$this->_log = array();
		$this->_counters = array();
		//$this->_exclusions = array();
  }
  
  
  
  function &getInstance($prefix)
  {
    static $instances;
    
    if( is_null($instances) )
      $instances = array();
    
    if( empty($instances[$prefix]) )
      $instances[$prefix] = new SEBenchmark($prefix);
    
    return $instances[$prefix];
  }
  
  
  
	function start($label, $note=NULL)
	{
    if( !isset($this->_log[$label]) ) $this->_log[$label] = array(
      'list' => array(),
      'total' => 0,
      'index' => 0
    );
    
    $counter = $this->_log[$label]['index'];
    $now = $this->getmicrotime();
    $mem = $this->getmemory();
    
    $this->_log[$label]['list'][$counter] = array();
    $this->_log[$label]['list'][$counter]['start_ttd'] = $now - $this->_start;
    $this->_log[$label]['list'][$counter]['start_time'] = $now;
    $this->_log[$label]['list'][$counter]['start_memory'] = $mem;
    $this->_log[$label]['list'][$counter]['note'] = $note;
  }
  
  
  
	function end($label)
	{
    if( !isset($this->_log[$label]) ) return;
    
    $counter = $this->_log[$label]['index']++;
    $now = $this->getmicrotime();
    $mem = $this->getmemory();
    
    $this->_log[$label]['list'][$counter]['end_ttd'] = $now - $this->_start;
    $this->_log[$label]['list'][$counter]['end_time'] = $now;
    $this->_log[$label]['list'][$counter]['end_memory'] = $mem;
    
    $this->_log[$label]['list'][$counter]['delta_time'] = $now - $this->_log[$label]['list'][$counter]['start_time'];
    $this->_log[$label]['list'][$counter]['delta_memory'] = $mem - $this->_log[$label]['list'][$counter]['start_memory'];
    
    $this->_log[$label]['total'] += $this->_log[$label]['list'][$counter]['delta_time'];
  }
  
  
  
	function getUid()
	{
    return $this->_uid;
  }
  
  
  
	function getLog()
	{
    return $this->_log;
  }
  
  
  
	function getTotalTime()
	{
    return ($this->getmicrotime() - $this->_start);
  }
  
  
  
	function getmicrotime()
	{
		list( $usec, $sec ) = explode( ' ', microtime() );
		return ((float)$usec + (float)$sec);
	}
  
  
  
	function getmemory()
	{
		if( function_exists( 'memory_get_usage' ) )
    {
      return memory_get_usage();
    }
    
    else
    {
      return NULL;
    }
  }
}

?>