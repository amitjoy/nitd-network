<?php

class SessionMemcacheEngine extends SessionEngine
{
  function open()
  {
    return true;
  }

  function close()
  {
    return true;
  }

  function read($key)
  {

  }

  function write($key, $value)
  {

  }

  function destroy($key)
  {

  }

  function gc()
  {

  }
}