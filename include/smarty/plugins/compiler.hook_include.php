<?php

function smarty_compiler_hook_include($tag_attrs, &$compiler)
{
  $_params = $compiler->_parse_attrs($tag_attrs);

  if( !isset($_params['name']) )
  {
    $compiler->_syntax_error("hook: missing 'name' parameter", E_USER_WARNING);
    return;
  }
  
  if( !isset($_params['type']) )
  {
    $_params['type'] = 'include';
  }
  
  $content = '';
  switch( $_params['type'] )
  {
    case 'include':
      $content .= "\$this->_smarty_include(array('smarty_include_tpl_file' => \$_tpl_hook_include, 'smarty_include_vars' => array()));";
    break;
  }
  
  if( empty($content) )
    return '';
  
  return "if( isset(\$this->_tpl_hooks[{$_params['name']}]) )
{
  foreach( \$this->_tpl_hooks[{$_params['name']}] as \$_tpl_hook_include )
  {
    {$content}
  }
}";
}

?>