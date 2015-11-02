<?php



//
// CLASS SE_Hook
//
//  For more information about the PHP callback type:
//    http://www.php.net/manual/en/language.pseudo-types.php#language.types.callback
//
//  Example:
//    ( ($hook::$se_hooks->exists('example')) ?
//      $se_hooks::call($hook, array(
//        'value1' => &$value1,
//        'value2' => &$value2
//    )) : NULL );
//
//

class SE_Hook
{
  /*-------------------------------------------------------------------------*\
  | Property Definitions                                                      |
  \*-------------------------------------------------------------------------*/
  
  //
  // PRIVATE PROPERTY SE_Hook->_hooks
  //
  // Contains a list of all active hooks
  // Structure:
  //  array( (str)hook_name => (int)hook_index )
  //
  
  var $_hooks = array();
  
  
  
  //
  // PRIVATE PROPERTY SE_Hook->_callback_index
  //
  
  var $_callback_index = 0;
  
  
  
  //
  // PRIVATE PROPERTY SE_Hook->_callbacks
  //
  // Contains a list of all callback functions attached to a hook
  // Structure:
  //  array( (int)hook_index => array( (int)callback_index => (callback)callback_function ) )
  //
  
  var $_callbacks = array();
  
  
  
  //
  // PRIVATE PROPERTY SE_Hook->_callback_priorities
  //
  // Callback priority
  // Structure:
  //  array( (int)callback_index => (int)callback_priority )
  //
  
  var $_callback_priorities = array();
  
  
  
  //
  // PRIVATE PROPERTY SE_Hook->_needs_prioritize
  //
  //  Flag to sort
  //
  
  var $_needs_prioritize = FALSE;
  
  
  
  //
  // PUBLIC PROPERTY SE_Hook->default_priority
  //
  //  Default callback priority
  //
  
  var $default_priority = 100;
  
  
  
  
  
  /*-------------------------------------------------------------------------*\
  | Methods - Construction                                                    |
  \*-------------------------------------------------------------------------*/
  
  //
  // PUBLIC METHOD create()
  //
  //  Creates a hook instance, or 
  //
  //  Parameters:
  //    void
  //
  //  Returns:
  //    An instance of this class
  //
  
  function &create()
  {
		static $instance;
    
		if (!$instance)
		{
			$instance = new SE_Hook();
		}
    
		return $instance;
  }
  
  //
  // END PUBLIC METHOD create()
  //
  
  
  
  
  
  /*-------------------------------------------------------------------------*\
  | Methods - Registration                                                    |
  \*-------------------------------------------------------------------------*/
  
  //
  // PUBLIC METHOD register(hook_name as string, callback as callback[, priority as integer])
  //
  //  Register a hook
  //
  //  Parameters:
  //    hook_name     - The name of the hook as string
  //    callback      - The function or method to use as a callback
  //    priority      - The priority of the callback
  //
  //  Returns:
  //    void
  //
  
  function register($hook_name, $callback, $priority=NULL)
  {
    $thiis =& SE_Hook::create();
    
    // Find or create the hook index
    $hook_index = (isset($thiis->_hooks[$hook_name]) ? $thiis->_hooks[$hook_name] : ($thiis->_hooks[$hook_name]=(int)count($thiis->_hooks)) );
    
    // Store
    $thiis->_callbacks[$hook_index][$thiis->_callback_index] = $callback;
    
    // Prioritize
    if( isset($priority) ) $thiis->_needs_prioritize = TRUE;
    $thiis->_callback_priorities[$thiis->_callback_index] = (isset($priority) ? $priority : $thiis->default_priority);
    
    $thiis->_callback_index++;
    
    return;
  }
  
  //
  // END PUBLIC METHOD register
  //
  
  
  
  //
  // PUBLIC METHOD unregister(hook_name as string[, callback as callback])
  //
  // Unregister a hook
  // If callback is set, only unregisters that callback, otherwise unregisters entire hook
  // TODO: Remove callback priorities for hook mode
  //
  //  Parameters:
  //    hook_name     - The name of the hook as string
  //    callback      - The function or method callback
  //
  //  Returns:
  //    void
  //
  
  function unregister($hook_name, $callback=NULL)
  {
    $thiis =& SE_Hook::create();
    
    // Can't unregister something that isn't there
    if( !isset($thiis->_hooks[$hook_name]) ) return;
    
    $hook_index = $thiis->_hooks[$hook_name];
    
    // Unset entire hook if no specified callback
    if( !isset($callback) )
    {
      unset($thiis->_hooks[$hook_name]);
      unset($thiis->_callbacks[$hook_index]);
      //unset($thiis->_callback_priorities[]);
    }
    
    // Other wise unset all instances of the specified callback
    else
    {
      $callback_indices = array_keys($thiis->_callbacks[$hook_index], $callback, TRUE);
      foreach( $callback_indices as $callback_index )
      {
        unset($thiis->_callbacks[$hook_index][$callback_index]);
        unset($thiis->_callback_priorities[$callback_index]);
      }
    }
    
    return;
  }
  
  //
  // END PUBLIC METHOD unregister()
  //
  
  
  
  
  
  /*-------------------------------------------------------------------------*\
  | Methods - Calling                                                         |
  \*-------------------------------------------------------------------------*/
  
  //
  // PUBLIC METHOD exists(hook_name as string)
  //
  //  Check if hook exists. It returns the argument so we only have to import the hook name once.
  //
  //  Parameters:
  //    hook_name   - the name of the hook to get
  //
  //  Returns:
  //    Hook name if a hook is registered, otherwise FALSE
  //
  
  function exists($hook_name)
  {
    $thiis =& SE_Hook::create();
    return (isset($thiis->_hooks[$hook_name]) ? $hook_name : FALSE);
  }
  
  //
  // END PUBLIC METHOD exists()
  //
  
  
  
  //
  // PUBLIC METHOD call(hook_name as string, arguments as array)
  //
  //  Calls a hook instance
  //  IMPORTANT: Always pass arguments as an array of references
  //  TODO: Should the array of references be passed as a reference?
  //
  //  Parameters:
  //    arguments   - An array of references. Each hook should have standardized elements.
  //
  //  Returns:
  //    void
  //
  
  function call($hook_name, $arguments=array())
  {
    $thiis =& SE_Hook::create();
    
    // Prioritize
    if( $thiis->_needs_prioritize ) $thiis->prioritize();
    
    // Iterate over each callback
    $hook_index = $thiis->_hooks[$hook_name];
    foreach( $thiis->_callbacks[$hook_index] as $callback_index=>$callback )
    {
      if( !is_callable($callback) ) continue;
      // TODO: Capture output
      call_user_func($callback, $arguments);
    }
    
    return;
  }
  
  //
  // END PUBLIC METHOD call
  //
  
  
  
  
  
  /*-------------------------------------------------------------------------*\
  | Methods - Priority                                                        |
  \*-------------------------------------------------------------------------*/
  
  //
  // PUBLIC METHOD prioritize(void)
  //
  //  Prioritizes the callback based on the priorities given
  //  TODO: Verify that arrays are correctly sorted
  //
  //  Parameters:
  //    void
  //
  //  Returns:
  //    void
  //
  
  function prioritize()
  {
    $thiis =& SE_Hook::create();
    
    foreach( $thiis->_callbacks as $hook_index=>$callback_array )
    {
      uksort($thiis->_callbacks[$hook_index], array('SE_Hook', '_priority_cmp') );
    }
    $thiis->_needs_prioritize = FALSE;
  }
  
  //
  // END PUBLIC METHOD prioritize()
  //
  
  
  
  //
  // PRIVATE METHOD _priority_cmp()
  //
  //  Comparison function for uasort() in SE_Hook->prioritize
  //
  //  Primary: Order descending by priority
  //  Secondary: Order ascending by index
  //
  
  function _priority_cmp($a, $b)
  {
    $thiis =& SE_Hook::create();
    
    if ($thiis->_callback_priorities[$a] == $thiis->_callback_priorities[$b]) {
      //return 0;
      return ($a < $b ? -1 : 1);
    }
    return ($thiis->_callback_priorities[$a] < $thiis->_callback_priorities[$b]) ? 1 : -1;
  }
  
  //
  // END PUBLIC METHOD _priority_cmp
  //
  
  
  
  
  
  /*-------------------------------------------------------------------------*\
  | Other Methods                                                             |
  \*-------------------------------------------------------------------------*/
  
  
  
  //
  // PUBLIC METHOD name(hook_index as integer)
  //
  //  Get the hook name corresponding to an index
  //
  //  Parameters:
  //    hook_index   - the index of a hook
  //
  //  Returns:
  //    The name of the corresponding hook, or FALSE
  //
  
  function name($hook_index)
  {
    $thiis =& SE_Hook::create();
    return array_search($hook_index, $thiis->_hooks);
  }
  
  //
  // END PUBLIC METHOD name
  //
  //
  
}

//
// END CLASS SE_Hook
//






//
// FUNCTION property_exists
//
// Create the 'property_exists' function for PHP4
// FIXME Right now does not work for static classes (using ::)
// Use of this function has been deprecated in SE_Hook (replaced by is_callable)
//

if(!function_exists('property_exists')) { 
  function property_exists($object, $property) {
		if( !class_exists(get_class($object)) ) return FALSE;
		if( !isset($object->{$property}) ) return FALSE;
		return TRUE;
  }  
}

//
// END FUNCTION property_exists
//

?>