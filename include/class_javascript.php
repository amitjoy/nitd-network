<?php

class SE_Javascript
{
  function json_encode(&$data)
  {
    if( !function_exists('json_encode') ) return '';
    return json_encode($data);
  }
  
  
  
  function generateSettings(&$settings)
  {
    return json_encode(array(
      'setting_url'       => (bool) $settings['setting_url'],
      'setting_username'  => (bool) $settings['setting_username']
    ));
  }
  
  
  
  function generatePlugins(&$plugin_list)
  {
    // Fix those darn error messages in the admin panel
    if( !empty($plugin_list[0]) || !is_array($plugin_list) )
      return '[]';
    
    return json_encode(array_keys($plugin_list));
  }
  
  
  
  function generateUserInfo(&$user_object)
  {
    if( !$user_object || !$user_object->user_exists)
      return json_encode(array('user_exists' => FALSE));
    
    return json_encode(array(
      'user_exists'     => ( $user_object->user_exists ? TRUE : FALSE ),
      'user_id'         => (int) $user_object->user_info['user_id'],
      'user_username'   => $user_object->user_info['user_username'],
      'user_fname'      => $user_object->user_info['user_fname'],
      'user_lname'      => $user_object->user_info['user_lname'],
      'user_subnet_id'  => (int) $user_object->user_info['user_subnet_id'],
      'user_status'     => $user_object->user_info['user_status'],
      'user_photo'      => $user_object->user_info['user_photo']
    ));
  }
  
  
  
  function generateURLBase(&$url_object)
  {
    return "'".addslashes($url_object->url_base)."'";
  }
  
  
  
  function generateURLInfo(&$url_object)
  {
    return json_encode($url_object->convert_urls);
  }
  
  
  
  function generateNotifys(&$notify_data)
  {
    $data =& $notify_data['notifys'];
    
    foreach( $data as $index=>$notify_info )
    {
      $data[$index]['notify_text_output'] = sprintf(SELanguage::_get($notify_info['notify_desc']), $notify_info['notify_total'], $notify_info['notify_text'][0]);
    }
    
    return json_encode($notify_data);
  }
}

?>