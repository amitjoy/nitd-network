<?php

if( !function_exists('file_put_contents') )
{
  function file_put_contents($filename, $data)
  {
    $f = @fopen($filename, 'w');
    if( !$f )
    {
      return false;
    }
    else
    {
      $bytes = fwrite($f, $data);
      fclose($f);
      return $bytes;
    }
  }
}

if( !function_exists('file_get_contents') )
{
  function file_get_contents($filename, $incpath = false, $resource_context = null)
  {
    if(false === ($fh = fopen($filename, 'rb', $incpath)) )
    {
      trigger_error('file_get_contents() failed to open stream: No such file or directory', E_USER_WARNING);
      return false;
    }
    
    clearstatcache();
    if( $fsize = @filesize($filename) )
    {
      $data = fread($fh, $fsize);
    }
    else
    {
      $data = '';
      while( !feof($fh) )
      {
        $data .= fread($fh, 8192);
      }
    }
    
    fclose($fh);
    return $data;
  }
}

?>