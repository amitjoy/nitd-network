<?php

function smarty_block_hook_foreach($params, $content, &$smarty, &$repeat)
{
  static $depth = 0;
  static $args_array = array();
  
  // Check depth
  if( is_null($content) )
  {
    // Check params
    if( !isset($params['name']) )
    {
      $smarty->trigger_error("hook_foreach: missing 'name' parameter", E_USER_WARNING);
      return;
    }
    
    if( !isset($params['var']) )
    {
      $smarty->trigger_error("hook_foreach: missing 'var' parameter", E_USER_WARNING);
      return;
    }
    
    if( !isset($params['start']) || $params['start']<0 || $params['start']>count($smarty->_tpl_hooks[$params['name']]) )
    {
      $params['start'] = 0;
    }
    
    if( !isset($params['max']) || $params['max']<$params['start'] || $params['max']>count($smarty->_tpl_hooks[$params['name']]) )
    {
      $params['max'] = count($smarty->_tpl_hooks[$params['name']]);
    }
    
    if( empty($smarty->_tpl_hooks[$params['name']]) )
    {
      $repeat = FALSE;
      if( isset($params['complete']) && is_string($params['complete']) )
        $smarty->assign($params['complete'], TRUE);
      return;
    }
    
    if( isset($params['complete']) && is_string($params['complete']) )
    {
      $smarty->assign($params['complete'], FALSE);
    }
    
    $depth++;
    $args_array[$depth]['index']  = $params['start'];
    $args_array[$depth]['name']   = $params['name'];
    $args_array[$depth]['var']    = $params['var'];
    $args_array[$depth]['start']  = $params['start'];
    $args_array[$depth]['max']    = $params['max'];
    $args_array[$depth]['count']  = 0;
  }
  
  // Assign depth vars
  $index =& $args_array[$depth]['index'];
  $name =& $args_array[$depth]['name'];
  $var =& $args_array[$depth]['var'];
  $max =& $args_array[$depth]['max'];
  $count =& $args_array[$depth]['count'];
  
  // Check if need to repeat, assign var
  if( isset($smarty->_tpl_hooks[$name][$index]) && $max>$count )
  {
    $repeat = TRUE;
    $smarty->assign($var, $smarty->_tpl_hooks[$name][$index]);
    $index++;
    $count++;
  }
  
  else
  {
    $repeat = FALSE;
    if( isset($params['complete']) && is_string($params['complete']) && $count>=count($smarty->_tpl_hooks[$name]) )
      $smarty->assign($params['complete'], TRUE);
  }
  
  if( !$repeat )
  {
    // Reset the static vars
    unset($args_array[$depth]);
    $depth--;
  }
  
  return $content;
}

?>