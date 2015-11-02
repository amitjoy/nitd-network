<?php

class SessionDbEngine extends SessionEngine
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
    $db =& SEDatabase::getInstance();
    $resource = $db->database_query("SELECT * FROM se_session_data WHERE session_data_id = '{$key}' LIMIT 1");
    $result = $db->database_fetch_assoc($resource);
    //$result = @unserialize($result);
    return (string) @$result['session_data_body'];
  }

  function write($key, $value)
  {
    $db =& SEDatabase::getInstance();
		switch (Configure::read('Security.level')) {
			case 'high':
				$factor = 10;
			break;
			case 'medium':
				$factor = 100;
			break;
			case 'low':
				$factor = 300;
			break;
			default:
				$factor = 10;
			break;
		}
    $expires = time() +  Configure::read('Session.timeout') * $factor;
    $resource = $db->database_query("SELECT NULL FROM se_session_data WHERE session_data_id = '{$key}' LIMIT 1");
    if( $db->database_num_rows($resource) )
    {
      $db->database_query("UPDATE se_session_data SET session_data_body='".$db->database_real_escape_string($value)."', session_data_expires='".$expires."' WHERE session_data_id = '{$key}' LIMIT 1");
    }
    else
    {
      $db->database_query("INSERT INTO se_session_data (session_data_id, session_data_body, session_data_expires) VALUES ('{$key}', '".$db->database_real_escape_string($value)."', '".$expires."')");
    }
  }

  function destroy($key)
  {
    $db =& SEDatabase::getInstance();
    $resource = $db->database_query("DELETE FROM se_session_data WHERE session_data_id = '{$key}' LIMIT 1");
  }

  function gc()
  {
    $db =& SEDatabase::getInstance();
    $resource = $db->database_query("DELETE FROM se_session_data WHERE session_data_expires < '".time()."'");
  }
}