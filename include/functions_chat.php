<?php

//
//  THIS FILE CONTAINS CHAT-RELATED FUNCTIONS
//
//  FUNCTIONS IN THIS CLASS:
//    site_statistics_chat()
//


defined('SE_PAGE') or exit();






// THIS FUNCTION IS RUN WHEN GENERATING SITE STATISTICS
// INPUT: 
// OUTPUT: 
function site_statistics_chat(&$args)
{
  global $database;
  
  $statistics =& $args['statistics'];
  
  // NOTE: CACHING WILL BE HANDLED BY THE FUNCTION THAT CALLS THIS
  
  $total = $database->database_fetch_assoc($database->database_query("SELECT COUNT(chatuser_id) AS total FROM se_chatusers WHERE chatuser_lastupdate>".(time()-15)));
  $statistics['chat'] = array(
    'title' => 3510042,
    'stat'  => (int) ( isset($total['total']) ? $total['total'] : 0 )
  );
  
  /*
  $total = $database->database_fetch_assoc($database->database_query("SELECT COUNT(chat_user_id) AS total FROM se_chat_users WHERE chat_user_session_lastupdate>".(time()-15)));
  $statistics['im'] = array(
    'title' => 3510043,
    'stat'  => (int) ( isset($total['total']) ? $total['total'] : 0 )
  );
  */
}

// END site_statistics_chat() FUNCTION

?>