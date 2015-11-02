<?php

class InputFilter
{
  var $useDefaultLists = true;
  var $allowedTags;
  var $forbiddenTags;
  var $allowedAttributes;
  var $forbiddenAttributes;
  var $forbiddenAttributeValues;
  var $decode = 1;
  
  // Static
  function process($text, $options = array())
  {
    $instance = new InputFilter($options);
    return $instance->execute($text);
  }

  // Constructor
  function InputFilter($options = array())
  {
    foreach( $options as $key => $value )
    {
      $method = 'set'.ucfirst($key);
      if( method_exists($this, $method) )
      {
        $this->$method($value);
      }
    }

    if( $this->useDefaultLists )
    {
      $this->loadDefaultLists();
    }
  }

  function loadDefaultLists()
  {
    $this->allowedAttributes = array_unique(array_merge((array) $this->allowedAttributes,
      array('href', 'src', 'alt', 'border', 'align', 'width', 'height', 'vspace', 'hspace', 'target', 'style')
    ));
  
    $this->forbiddenTags = array_unique(array_merge((array) $this->forbiddenTags,
      array('applet', 'body', 'bgsound', 'base', 'basefont', 'frame', 'frameset', 'head', 'html', 'id', 'iframe', 'ilayer', 'layer', 'link', 'meta', 'name', 'script', 'style', 'title', 'xml')
    ));

    $this->forbiddenAttributes = array_unique(array_merge((array) $this->forbiddenAttributes,
      array('action', 'background', 'codebase', 'dynsrc', 'lowsrc', 'on*')
    ));

    $this->forbiddenAttributeValues = array_unique(array_merge((array) $this->forbiddenAttributeValues,
      array('*expression*', 'javascript:*', 'behaviour:*', 'vbscript:*', 'mocha:*', 'livescript:*')
    ));
  }

  

  // Options

  function setDecode($flag = true)
  {
    $this->decode = $flag;
  }
  
  function setUseDefaultLists($flag = true)
  {
    $this->useDefaultLists = (bool) $flag;
  }

  function setAllowedTags($allowedTags = null)
  {
    if( is_string($allowedTags) )
    {
      $this->allowedTags = explode(',', $allowedTags);
    }
    else if( is_array($allowedTags) )
    {
      $this->allowedTags = $allowedTags;
    }
    else
    {
      $this->allowedTags = null;
    }
  }

  function setForbiddenTags($forbiddenTags = null)
  {
    if( is_string($forbiddenTags) )
    {
      $this->forbiddenTags = explode(',', $forbiddenTags);
    }
    else if( is_array($forbiddenTags) )
    {
      $this->forbiddenTags = $forbiddenTags;
    }
    else
    {
      $this->forbiddenTags = null;
    }
  }

  function setAllowedAttributes($allowedAttributes = null)
  {
    if( is_string($allowedAttributes) )
    {
      $this->allowedAttributes = explode(',', $allowedAttributes);
    }
    else if( is_array($allowedAttributes) )
    {
      $this->allowedAttributes = $allowedAttributes;
    }
    else
    {
      $this->allowedAttributes = null;
    }
  }

  function setForbiddenAttributes($forbiddenAttributes = null)
  {
    if( is_string($forbiddenAttributes) )
    {
      $this->forbiddenAttributes = explode(',', $forbiddenAttributes);
    }
    else if( is_array($forbiddenAttributes) )
    {
      $this->forbiddenAttributes = $forbiddenAttributes;
    }
    else
    {
      $this->forbiddenAttributes = null;
    }
  }
  
  // Main
  function execute($text)
  {
    // Process tags

    // Decode
    if( $this->decode == 1 )
    {
      $text = htmlspecialchars_decode($text, ENT_QUOTES);
    }

    // Nothing was specified? Just strip everything
    if( !$this->allowedTags && !$this->forbiddenTags )
    {
      return strip_tags($text);
    }
    
    // Close any open-ended tags, escape all non-html lt and gt
    $text = str_replace(array('[TO]', '[TC]'), array('', ''), $text);
    $text = preg_replace('/<(\/?[a-zA-Z][^<>]*?)((<)|>)/', '<$1>$3', $text);
    $text = preg_replace('/<(\/?[a-zA-Z][^<>]*?)>/', '[TO]$1[TC]', $text);
    $text = str_replace(array('<', '>'), array('&lt;', '&gt;'), $text);
    $text = str_replace(array('[TO]', '[TC]'), array('<', '>'), $text);

    /*
    // Close any open-ended tags and strip floating lts and gts
    $tmp = $text;
    while( 1 )
    {
      //$tmp = preg_replace('/>\s*>/', '>', $text);
      //$tmp = preg_replace('/<\s*</', '<', $tmp);
      $tmp = preg_replace('/<([^<>]*?)[<>]/', '<$1>', $tmp);
      if( $tmp == $text )
      {
        break;
      }
      $text = $tmp;
    }
    */
    
    // Strip everything but allowable tags
    if( $this->allowedTags )
    {
      $text = strip_tags($text, '<' . join('><', $this->allowedTags) . '>');
    }
    
    // Strip all forbidden tags
    if( $this->forbiddenTags )
    {
      $text = $this->stripTags($text, $this->forbiddenTags);
    }

    // Strip all but allowed attributes
    if( $this->allowedAttributes || $this->forbiddenAttributes )
    {
      // </?\w+((\s+\w+(\s*=\s*(?:".*?"|'.*?'|[^'">\s]+))?)+\s*|\s*)/? >
      preg_match_all('/(<\/?\w+)\s+([^<>]*?)>/i', $text, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
      /* preg_match_all('/<\/?(\w+)((\s+\w+(\s*=\s*(?:".*?"|\'.*?\'|[^\'">\s]+))?)+\s*|\s*)\/?>/ims', $text, $matches, PREG_SET_ORDER); */
      //var_dump($matches);

      $delta = 0;
      foreach( $matches as $match ) // Each one of these is an element with attributes
      {
        $current = $match[1][0];
        $modified = false;
        preg_match_all('/(\w+(\s*=\s*(?:".*?(?<!\\\\)"|\'.*?(?<!\\\\)\'|[^\'">\s]+))?)/', $match[0][0], $m, PREG_SET_ORDER);
        //var_dump($m);

        for( $i = 1; $i < count($m); $i++ )
        {
          $attribString = $m[$i][0];
          @list($attribName, $attribValue) = explode('=', $attribString, 2);

          if( !$this->checkAttribute($attribName, $attribValue) )
          {
            $modified = true;
            continue; // Throw it away!
          }
          
          $current .= ' ' . $attribName;
          if( !empty($attribValue) )
          {
            $current .= '=' . $attribValue;
          }
        }
        $current .= '>';

        // Here we replace the original
        $offset = $match[0][1] + $delta;
        $length = strlen($match[0][0]);
        if( $modified == true )
        {
          $currentLength = strlen($current);
          $tmp = substr($text, 0, $offset + 0)
            //. "~~~"
            . $current
            //. "~~~"
            . substr($text, $offset + $length);
          $delta -= ($length - $currentLength);
          $text = $tmp;
          //$tmp = substr($text, 0, )
          //var_dump($text);
        }
      }
    }

    return $text;
  }

  function checkAttribute($name, $value)
  {
    $name = strtolower($name);
    $value = trim($value, "\x00..\x20\x22\x27");
    $softAllow = false;
    
    // This attribute has been specifically whitelisted
    if( is_array($this->allowedAttributes) && in_array($name, $this->allowedAttributes) )
    {
       $softAllow = true;
      //return true;
    }

    // This attribute has been specifically blacklisted
    if( is_array($this->forbiddenAttributes) && in_array($name, $this->forbiddenAttributes) )
    {
      return false;
    }

    // Now check for wildcards

    // Allowed Attributes
    if( is_array($this->allowedAttributes) )
    {
      foreach( $this->allowedAttributes as $allowedAttribute )
      {
        if( strpos($allowedAttribute, '*') === false )
        {
          continue;
        }

        // Make a regex for the wildcard
        $regex = '/^' . str_replace('*', '.+?', preg_quote($allowedAttribute)) . '$/i';
        if( preg_match($regex, $name) )
        {
          $softAllow = true;
          //return true;
        }
      }
    }


    // Forbidden Attributes
    if( is_array($this->forbiddenAttributes) )
    {
      foreach( $this->forbiddenAttributes as $forbiddenAttribute )
      {
        if( strpos($forbiddenAttribute, '*') === false )
        {
          continue;
        }

        // Make a regex for the wildcard
        $regex = '/^' . str_replace('*', '.+?', preg_quote($forbiddenAttribute)) . '$/i';
        if( preg_match($regex, $name) )
        {
          return false;
        }
      }
    }
    
    // Forbidden attribute values
    if( is_array($this->forbiddenAttributeValues) )
    {
      foreach( $this->forbiddenAttributeValues as $forbiddenAttributeValue )
      {
        if( strpos($forbiddenAttributeValue, '*') === false )
        {
          continue;
        }

        // Make a regex for the wildcard
        $regex = '/^' . str_replace('\*', '.+?', preg_quote($forbiddenAttributeValue)) . '$/i';
        if( preg_match($regex, $value) )
        {
          return false;
        }
      }
    }
    
    return $softAllow;
  }

  function stripTags($str, $params)
  {
    // Taken from cake/sanitize (reference include/cake/lib/sanitize.php)
    for( $i = 0; $i < count($params); $i++ )
    {
      $str = preg_replace('/<' . $params[$i] . '\b[^>]*>/i', '', $str);
      $str = preg_replace('/<\/' . $params[$i] . '[^>]*>/i', '', $str);
    }
    return $str;
  }
}

?>