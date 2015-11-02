<?php

/* $Id: compiler.php 21 2009-01-15 08:46:59Z john $ */


defined('SE_PAGE') or exit();


class SELanguageCompiler
{
  // Will assign self by ref to this variable 
  var $_smarty_this_name        = "SELanguageCompiler";
  
  // All of the lang vars used by the header and footer templates (used only during compilation)
  var $_smarty_language_variables_global = array();
  
  // All of the lang vars used by the template (used only during compilation)
  var $_smarty_language_variables = array();
  
  // Enables indexing of languge variable ids in temp files
  var $_smarty_language_variables_index = FALSE;
  
  // Contains the name functions
  var $_smarty_basic_name       = "lang_print";
  var $_smarty_sprintf_name     = "lang_sprintf";
  var $_smarty_block_name       = "lang_block";
  
  // Javascript API Mod
  var $_smarty_preload_name     = "lang_preload";
  var $_smarty_javascript_name  = "lang_javascript";
  
  // Edit mode settings
  var $edit_mode = FALSE;
  var $edit_mode_open   = '<span class=\"lang_edit\" onmouseover=\"showEditor(this, \'__ID__\');this.tag=this.style.background;this.style.background=\'#888888\';\" onmouseout=\'this.style.background=this.tag;\'>';
  var $edit_mode_close  = '</span>';
  
  // File indexing
  var $use_indexing = TRUE;
  var $index_non_header_templates = FALSE;
  
  // Only compiler numeric arguments
  var $skip_non_numeric_ids = TRUE;
  
  // All of the lang vars used by the template (used only during compilation)
  var $debug = FALSE;
  
  
  
  function SELanguageCompiler()
  {
    global $smarty;
    
    // Assign self to be referenced by smarty later
    $smarty->assign_by_ref($this->_smarty_this_name, $this);
    
    // Register smarty plugin methods
    
    // BASIC
    $smarty->register_compiler_function
    (
      $this->_smarty_basic_name,
      array('SELanguageCompiler', 'smarty_compiler_basic')
    );
    
    // SPRINTF
    $smarty->register_compiler_function
    (
      $this->_smarty_sprintf_name,
      array('SELanguageCompiler', 'smarty_compiler_sprintf')
    );
    
    // BLOCK - START
    $smarty->register_compiler_function
    (
      $this->_smarty_block_name,
      array('SELanguageCompiler', 'smarty_compiler_block')
    );
    
    // BLOCK - END
    $smarty->register_compiler_function
    (
      '/'.$this->_smarty_block_name,
      array('SELanguageCompiler', 'smarty_compiler_block')
    );
    
    // BLOCK - EXEC
    /*
    $smarty->register_block
    (
      $this->_smarty_block_name,
      array('SELanguageCompiler', 'smarty_block_execute')
    );
    */
    
    // PRELOAD
    $smarty->register_compiler_function
    (
      $this->_smarty_preload_name,
      array('SELanguageCompiler', 'smarty_compiler_preload')
    );
    
    // JAVASCRIPT
    $smarty->register_compiler_function
    (
      $this->_smarty_javascript_name,
      array('SELanguageCompiler', 'smarty_compiler_javascript')
    );
    
    // POSTFILTER
    $smarty->register_postfilter
    (
      array('SELanguageCompiler', 'smarty_postfilter_prepend_load')
    );
  }
  
  
  
  function &getInstance()
  {
    static $instance;
    
    if( is_null($instance) ) $instance = new SELanguageCompiler();
    
    return $instance;
  }
  
  
  
  
  
  
  //
  // PUBLIC METHOD smarty_compiler_basic()
  //
  // Smarty Compiler Function
  //
  // Arguments: See smarty documentation for register_compiler_function()
  // Returns: See smarty documentation for register_compiler_function()
  //
  
  function smarty_compiler_basic($tag_args, &$compiler)
  {
    $instance =& SELanguageCompiler::getInstance();
    
    
    // Parse params
    $params = $compiler->_parse_attrs($tag_args);
    
    
    // Attribute: id
    if( empty($params['id']) )
    {
      $compiler->_syntax_error($instance->_smarty_sprintf_name.": missing 'id' parameter", E_USER_WARNING);
      return;
    }
    
    
    // Attribute: preload
    if( !isset($params['preload']) || $params['preload'] )
    {
      // Note: A 'hidden' feature is that if a variable used as a language id is assigned before template execution,
      // you can actually preload the value in the template as if it were a normal id. On the other hand, if you use
      // a section, it may cause execution errors when trying to read the array (that is often unassigned) in the compiled
      // template. Maybe add this line:
      //if( is_numeric($params['id']) )
      
      if( !$instance->skip_non_numeric_ids || is_numeric($params['id']) )
        $instance->_smarty_language_variables[] = $params['id'];
    }
    
    
    // Generate code
    $compiled_string = "SELanguage::_get(".$params['id'].")";
    
    
    // Attribute: assign
    if( isset($params['assign']) )
    {
      $compiled_string = "\$this->_tpl_vars[".$params['assign']."] = ".$compiled_string;
      
      // If assign isset and print is not set, disable printing
      if( !isset($params['print']) ) $params['print'] = FALSE;
    }
    
    
    // Attribute: print
    if( !isset($params['print']) || $params['print'] )
    {
      $compiled_string = "echo ".$compiled_string;
    }
    
    $compiled_string .= ";";
    
    
    // Add edit code
    if( $instance->edit_mode )
    {
      $compiled_string = $instance->_edit_code($params['id'], TRUE).$compiled_string.$instance->_edit_code($params['id'], FALSE);
    }
    
    
    // Add debug code
    if( $instance->debug )
    {
      $compiled_string .= "\n/* DEBUG: ".print_r($params, TRUE)." */";
    }
    
    return $compiled_string;
  }
  
  //
  // END PUBLIC METHOD smarty_compiler_basic()
  //
  
  
  
  
  
  
  //
  // PUBLIC METHOD smarty_compiler_sprintf()
  //
  // Smarty Compiler Function
  //
  // Arguments: See smarty documentation for register_compiler_function()
  // Returns: See smarty documentation for register_compiler_function()
  //
  
  function smarty_compiler_sprintf($tag_args, &$compiler)
  {
    $instance =& SELanguageCompiler::getInstance();
    
    
    // Parse params
    $params = $compiler->_parse_attrs($tag_args);
    
    
    // Attribute: id
    if( empty($params['id']) )
    {
      $compiler->_syntax_error($instance->_smarty_sprintf_name.": missing 'id' parameter", E_USER_WARNING);
      return;
    }
    
    
    // Attribute: preload
    if( !isset($params['preload']) || $params['preload'] )
    {
      // Note: A 'hidden' feature is that if a variable used as a language id is assigned before template execution,
      // you can actually preload the value in the template as if it were a normal id. On the other hand, if you use
      // a section, it may cause execution errors when trying to read the array (that is often unassigned) in the compiled
      // template. Maybe add this line:
      // if( is_numeric($params['id']) )
      if( !$instance->skip_non_numeric_ids || is_numeric($params['id']) )
        $instance->_smarty_language_variables[] = $params['id'];
    }
    
    
    // Attribute: 1
    if( empty($params[1]) && empty($params['args']) )
    {
      $compiler->_syntax_error($instance->_smarty_sprintf_name.": must have at least one argument", E_USER_WARNING);
      return;
    }
    
    if( !empty($params[1]) && !empty($params['args']) )
    {
      $compiler->_syntax_error($instance->_smarty_sprintf_name.": cannot use both sprintf and vsprintf mode", E_USER_WARNING);
      return;
    }
    
    
    // Generate code - sprintf mode
    if( !empty($params[1]) )
    {
      $compiled_string = "sprintf(SELanguage::_get(".$params['id'].")";
      $i = 1;
      while( !empty($params[$i]) ) $compiled_string .= ', '.$params[$i++];
      $compiled_string .= ')';
    }
    
    // Generate code - vsprintf mode
    elseif( !empty($params['args']) )
    {
      $compiled_string = "vsprintf(SELanguage::_get(".$params['id'].")";
      $compiled_string .= ", ".$params['args'].");";
    }
    
    
    // Attribute: assign
    if( isset($params['assign']) )
    {
      $compiled_string = "\$this->_tpl_vars[".$params['assign']."] = ".$compiled_string;
      
      // If assign isset and print is not set, disable printing
      if( !isset($params['print']) ) $params['print'] = FALSE;
    }
    
    
    // Attribute: print
    if( !isset($params['print']) || $params['print'] )
    {
      $compiled_string = "echo ".$compiled_string;
    }
    
    $compiled_string .= ";";
    
    
    // Add edit code
    if( $instance->edit_mode )
    {
      $compiled_string = $instance->_edit_code($params['id'], TRUE).$compiled_string.$instance->_edit_code($params['id'], FALSE);
    }
    
    
    // Add debug code
    if( $instance->debug )
    {
      $compiled_string .= "\n/* DEBUG: ".print_r($params, TRUE)." */";
    }
    
    return $compiled_string;
  }
  
  //
  // END PUBLIC METHOD smarty_compiler_sprintf()
  //
  
  
  
  
  
  
  //
  // PUBLIC METHOD smarty_compiler_block()
  //
  // Smarty Compiler Function
  //
  // Arguments: See smarty documentation for register_compiler_function()
  // Returns: See smarty documentation for register_compiler_function()
  //
  
  function smarty_compiler_block($tag_args, &$compiler)
  {
    $instance =& SELanguageCompiler::getInstance();
    
    $is_start = !empty($tag_args);
    $tag_command = ( $is_start ? $instance->_smarty_block_name : '/'.$instance->_smarty_block_name );
    
    // Start
    if( $is_start )
    {
      $compiled_string = "";
      $params = $compiler->_parse_attrs($tag_args);
      
      // Attribute: id
      if( empty($params['id']) ) return 'echo "Missing id attribute";';
      $instance->_smarty_language_variables[] = $params['id'];
      
      // Attributes: var, id, value
      if( !empty($params['var']) && !empty($params['id']) )
      {
        $compiled_string .= "\$this->assign({$params['var']}, SE_Language::_get(".$params['id']."));\n";
        //$compiled_string .= "\$this->assign({$params['var']}, \$this->_tpl_vars['".$instance->_smarty_this_name."']->_get(".$params['id']."));\n";
      }
      elseif( !empty($params['var']) && !empty($params['value']) )
      {
        $compiled_string .= "\$this->assign({$params['var']}, {$params['value']});\n";
      }
    }
    
    
    // End
    else
    {
      // TODO
    }
    
    // Make block code
    $output = "";
    $compiler->_compile_block_tag($tag_command, $tag_args, NULL, $output);
    $compiled_string = $compiled_string."\n?>".$output."\n<?php ";
    
    return $compiled_string;
  }
  
  //
  // END PUBLIC METHOD smarty_compiler_block()
  //
  
  
  
  
  
  
  //
  // PUBLIC METHOD smarty_block_execute()
  //
  // Smarty Block Function
  //
  // Arguments: See smarty documentation for register_block_function()
  // Returns: 
  //
  
  function smarty_block_execute($params, $content, &$smarty, &$repeat)
  {
    $instance =& SELanguageCompiler::getInstance();
    
    if( is_null($content) ) return;
    
    // Callback
    if( !empty($params['callback']) && is_callable($params['callback']) )
    {
      $params['callback']($params, $content, $smarty);
    }

    // Add edit code
    if( $instance->edit_mode )
    {
      $content = $instance->_edit_code($params['id'], TRUE, TRUE).$content.$instance->_edit_code($params['id'], FALSE, TRUE);
    }
    
    return $content;
  }
  
  //
  // END PUBLIC METHOD smarty_block_execute()
  //
  
  
  
  
  
  
  //
  // PUBLIC METHOD smarty_compiler_preload()
  //
  // Smarty Compiler Function
  //
  // Arguments: See smarty documentation for register_compiler_function()
  // Returns: See smarty documentation for register_compiler_function()
  //
  
  function smarty_compiler_preload($tag_args, &$compiler)
  {
    $instance =& SELanguageCompiler::getInstance();
    
    $params = $compiler->_parse_attrs($tag_args);
    
    $preload_list = array();
    
    // Attribute: id
    if( !empty($params['id']) )
    {
      $preload_list = array($params['id']);
    }
    
    // Attribute: ids
    if( !empty($params['ids']) )
    {
      $params['ids'] = str_replace('"', '', $params['ids']);
      $params['ids'] = str_replace('\'', '', $params['ids']);
      $preload_list = array_filter(explode(',', $params['ids']));
    }
    
    // Attribute: range
    if( !empty($params['range']) )
    {
      $params['range'] = str_replace('"', '', $params['range']);
      $params['range'] = str_replace('\'', '', $params['range']);
      $range = explode('-', $params['range']);
      if( is_numeric($range[0]) && is_numeric($range[1]) && $range[0]<$range[1] )
        for( $i=$range[0]; $i<=$range[1]; $i++ )
          $preload_list[] = $i;
    }
    
    if( !empty($preload_list) )
    {
      $instance->_smarty_language_variables = array_merge($instance->_smarty_language_variables, $preload_list);
    }
  }
  
  //
  // END PUBLIC METHOD smarty_compiler_preload()
  //
  
  
  
  
  
  
  //
  // PUBLIC METHOD smarty_compiler_javascript()
  //
  // Smarty Compiler Function
  //
  // Arguments: See smarty documentation for register_compiler_function()
  // Returns: See smarty documentation for register_compiler_function()
  //
  
  function smarty_compiler_javascript($tag_args, &$compiler)
  {
    $instance =& SELanguageCompiler::getInstance();
    
    $params = $compiler->_parse_attrs($tag_args);
    
    $preload_list = array();
    $compiled_string = '';
    
    // Attribute: id
    if( !empty($params['id']) )
    {
      $preload_list = array($params['id']);
    }
    
    // Attribute: ids
    if( !empty($params['ids']) )
    {
      $params['ids'] = str_replace('"', '', $params['ids']);
      $params['ids'] = str_replace('\'', '', $params['ids']);
      $preload_list = array_filter(explode(',', $params['ids']));
    }
    
    // Attribute: range
    if( !empty($params['range']) )
    {
      $params['range'] = str_replace('"', '', $params['range']);
      $params['range'] = str_replace('\'', '', $params['range']);
      $range = explode('-', $params['range']);
      if( is_numeric($range[0]) && is_numeric($range[1]) && $range[0]<$range[1] )
        for( $i=$range[0]; $i<=$range[1]; $i++ )
          $preload_list[] = $i;
    }
    
    
    if( !empty($preload_list) && is_array($preload_list) )
    {
      $instance->_smarty_language_variables = array_merge($instance->_smarty_language_variables, $preload_list);
      
      $compiled_string .= '
$javascript_lang_import_list = SELanguage::_javascript_redundancy_filter(array('.join(',', $preload_list).'));
$javascript_lang_import_first = TRUE;
if( is_array($javascript_lang_import_list) && !empty($javascript_lang_import_list) )
{
  echo "\n<script type=\'text/javascript\'>\n<!--\n";
  echo "SocialEngine.Language.Import({\n";
  foreach( $javascript_lang_import_list as $javascript_import_id )
  {
    if( !$javascript_lang_import_first ) echo ",\n";
    echo "  ".$javascript_import_id." : \'".addslashes(SE_Language::_get($javascript_import_id))."\'";
    $javascript_lang_import_first = FALSE;
  }
  echo "\n});\n//-->\n</script>\n";
}
';
    }
    
    return $compiled_string;
  }
  
  //
  // END PUBLIC METHOD smarty_compiler_javascript()
  //
  
  
  
  
  
  
  //
  // PUBLIC METHOD smarty_postfilter_prepend_load()
  //
  // Smarty Postfilter Function
  //
  // Arguments: See smarty documentation for register_postfilter()
  // Returns: Template with prepended lang preload code
  //
  
  function smarty_postfilter_prepend_load($compiled, &$smarty)
  {
    $instance =& SELanguageCompiler::getInstance();
    
    $prepend_compiled = '<?php';
    
    if( !empty($instance->_smarty_language_variables) )
    {
       $prepend_compiled .= '
SELanguage::_preload_multi(';
      
      // Remove duplicate values
      $instance->_smarty_language_variables = array_unique($instance->_smarty_language_variables);
      
      $count = 0;
      foreach( $instance->_smarty_language_variables as $preload_lang_id )
      {
        if( $count!=0 ) $prepend_compiled .= ',';
        $prepend_compiled .= "$preload_lang_id";
        $count++;
      }
      
      $prepend_compiled .= ');';
      
    }
    
    $prepend_compiled .= '
SELanguage::load();
?>';
    
    
    // INDEXING
    if( $instance->use_indexing )
    {
      $index_file = NULL;
      if( strpos($smarty->_current_file, 'admin_header')!==FALSE || strpos($smarty->_current_file, 'admin_footer')!==FALSE )
        $index_file = 'globals_admin';
      elseif( strpos($smarty->_current_file, 'header')!==FALSE || strpos($smarty->_current_file, 'footer')!==FALSE )
        $index_file = 'globals';
      elseif( $instance->index_non_header_templates )
        $index_file = $smarty->_current_file;
      
      if( !is_null($index_file) )
        SELanguage::_update_index_file($instance->_smarty_language_variables, $index_file);
    }
    
    
    // EMPTY FOR THE NEXT TEMPLATE
    $instance->_smarty_language_variables = array();
    
    
    return $prepend_compiled.$compiled;
  }
  
  //
  // END PUBLIC METHOD smarty_postfilter_prepend_load()
  //
  
  
  
  
  
  
  //
  // PRIVATE METHOD _edit_code()
  //
  
  function _edit_code($id, $is_start=TRUE, $compiled_code=FALSE)
  {
    $instance =& SELanguageCompiler::getInstance();
    
    $edit_data = ( $is_start ? $instance->edit_mode_open : $instance->edit_mode_close );
    $edit_data = ($compiled_code ? stripslashes($edit_data) : $edit_data );
    $edit_data = str_replace('__ID__', $id, $edit_data);
    
    // Add echo to uncompiled code
    if( !$compiled_code ) $edit_data = "echo \"".$edit_data."\";";
    
    return $edit_data;
  }
  
  //
  // END PRIVATE METHOD _edit_code()
  //
}

?>