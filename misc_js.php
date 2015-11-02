<?php

define('SE_PAGE_AJAX', TRUE);
$page = "misc_js";
include "header.php";

// THIS FILE CONTAINS RANDOM JAVASCRIPT-Y FEATURES SUCH AS POSTING COMMENTS AND DELETING ACTIONS
$task = ( isset($_POST['task']) ? $_POST['task'] : ( isset($_GET['task']) ? $_GET['task'] : NULL ) );




// RETRIEVE FULL FRIEND LIST
if( $task == "friends_all" )
{
  // MUST BE LOGGED IN TO USE THIS TASK
  if( !$user->user_exists )
  {
    echo json_encode(array('friends'=>array()));
    exit();
  }
  
  // RETRIEVE ALL FRIENDS
  $results = array();
  $sql = "SELECT user_id, user_username, user_fname, user_lname FROM se_friends LEFT JOIN se_users ON se_friends.friend_user_id2=se_users.user_id WHERE se_friends.friend_status=1 AND se_friends.friend_user_id1='{$user->user_info['user_id']}' ORDER BY user_fname, user_lname, user_username";
  $resource = $database->database_query($sql);
  
  while( $friend_info = $database->database_fetch_assoc($resource) )
  {
    $friend = new se_user();
    $friend->user_info['user_id'] = $friend_info['user_id'];
    $friend->user_info['user_username'] = $friend_info['user_username'];
    $friend->user_info['user_fname'] = $friend_info['user_fname'];
    $friend->user_info['user_lname'] = $friend_info['user_lname'];
    $friend->user_displayname();
    
    $results[] = array($friend_info['user_id'] => $friend->user_displayname);
  }
  
  // CONSTRUCT AND OUTPUT JSON
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
  header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
  header("Pragma: no-cache"); // HTTP/1.0
  header("Content-Type: application/json");
  echo json_encode(array('friends' => &$results));
  exit();
}


// AUTOSUGGEST FRIEND
elseif( $task == "suggest_friend" )
{
  // MUST BE LOGGED IN TO USE THIS TASK
  if( !$user->user_exists )
  {
    echo json_encode(array('results'=>array()));
    exit();
  }
  
  // GET USER INPUT AND LIMIT
  $input = strtolower( $_GET['input'] );
  $len = strlen_utf8($input);
  $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 20;
	
  // RETRIEVE FITTING FRIENDS
  $results = array();
  $sql = "SELECT user_id, user_username, user_fname, user_lname, user_photo FROM se_friends LEFT JOIN se_users ON se_friends.friend_user_id1='{$user->user_info['user_id']}' AND se_users.user_id=se_friends.friend_user_id2 WHERE se_friends.friend_status=1 AND (SUBSTRING(user_username, 1, $len)='$input' OR SUBSTRING(user_fname, 1, $len)='$input' OR SUBSTRING(user_lname, 1, $len)='$input') LIMIT $limit";
  $resource = $database->database_query($sql);
  
  while( $friend_info = $database->database_fetch_assoc($resource) )
  {
    $friend = new se_user();
    $friend->user_info['user_id'] = $friend_info['user_id'];
    $friend->user_info['user_username'] = $friend_info['user_username'];
    $friend->user_info['user_fname'] = $friend_info['user_fname'];
    $friend->user_info['user_lname'] = $friend_info['user_lname'];
    $friend->user_info['user_photo'] = $friend_info['user_photo'];
    $friend->user_displayname();
    
    if( !$setting['setting_username'] ) { $friend_info['user_username'] = $friend->user_displayname; }
    
    $results[] = array(
      "id"          => $friend_info['user_id'],
      "value"       => $friend_info['user_username'],
      "info"        => $friend->user_displayname,
      "photo"       => $friend->user_photo("./images/nophoto.gif"),
      "photo_width" => $misc->photo_size($friend->user_photo("./images/nophoto.gif"),'50','50','w')
    );
  }
	
  // OUTPUT JSON
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
  header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
  header("Pragma: no-cache"); // HTTP/1.0
  header("Content-Type: application/json");
  echo json_encode(array('results' => &$results));
  exit();
}


// AUTOSUGGEST USER
elseif( $task == "suggest_user" )
{
  // GET USER INPUT AND LIMIT
  $input = strtolower( $_GET['input'] );
  $len = strlen_utf8($input);
  $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 20;
	
  // RETRIEVE FITTING FRIENDS
  $results = array();
  $sql = "SELECT user_id, user_username, user_fname, user_lname, user_photo FROM se_users WHERE user_id<>'{$user->user_info['user_id']}' AND (SUBSTRING(user_username, 1, $len)='$input' OR SUBSTRING(user_fname, 1, $len)='$input' OR SUBSTRING(user_lname, 1, $len)='$input') LIMIT $limit";
  $resource = $database->database_query($sql);
  
  while( $user_info = $database->database_fetch_assoc($resource) )
  {
    $sugg_user = new se_user();
    $sugg_user->user_info['user_id'] = $user_info['user_id'];
    $sugg_user->user_info['user_username'] = $user_info['user_username'];
    $sugg_user->user_info['user_fname'] = $user_info['user_fname'];
    $sugg_user->user_info['user_lname'] = $user_info['user_lname'];
    $sugg_user->user_info['user_photo'] = $user_info['user_photo'];
    $sugg_user->user_displayname();
    
    if( !$setting['setting_username'] ) { $user_info['user_username'] = $sugg_user->user_displayname; }
    
    $results[] = array(
      "id"          => $user_info['user_id'],
      "value"       => $user_info['user_username'],
      "info"        => $sugg_user->user_displayname,
      "photo"       => $sugg_user->user_photo("./images/nophoto.gif"),
      "photo_width" => $misc->photo_size($sugg_user->user_photo("./images/nophoto.gif"),'50','50','w')
    );
  }
	
  // OUTPUT JSON
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
  header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
  header("Pragma: no-cache"); // HTTP/1.0
  header("Content-Type: application/json");
  echo json_encode(array('results' => &$results));
  exit();
}


// CHECK IF USER EXISTS
elseif( $task == "check_user" )
{
  // GET USER INPUT AND LIMIT
  $input = strtolower( $_GET['input'] );
  
  // CHECK IF USER EXISTS
  $sql = "SELECT NULL FROM se_users WHERE user_username='{$input}' LIMIT 1";
  $resource = $database->database_query($sql);
  $user_exists = ( $resource && $database->database_num_rows($resource) );
	
  // OUTPUT JSON
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
  header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
  header("Pragma: no-cache"); // HTTP/1.0
  header("Content-Type: application/json");
  echo json_encode(array('user_exists' => (bool)$user_exists));
  exit();
}


// CHECK IF USER'S FRIEND EXISTS
elseif( $task == "check_friend" )
{
  // MUST BE LOGGED IN TO USE THIS TASK
  if( !$user->user_exists )
  {
    echo json_encode(array('result'=>array()));
    exit();
  }
  
  // GET USER INPUT AND LIMIT
  $input = strtolower( $_GET['input'] );
  
  $sql = "SELECT NULL FROM se_friends LEFT JOIN se_users ON se_friends.friend_user_id1='{$user->user_info['user_id']}' AND se_users.user_id=se_friends.friend_user_id2 WHERE se_users.user_username='{$input}' AND se_friends.friend_status=1 LIMIT 1";
  $resource = $database->database_query($sql);
  $user_exists = ( $resource && $database->database_num_rows($resource) );
	
  // OUTPUT JSON
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
  header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
  header("Pragma: no-cache"); // HTTP/1.0
  header("Content-Type: application/json");
  echo json_encode(array('user_exists' => (bool)$user_exists));
  exit();
}


// AUTOSUGGEST TEXT FIELD VALUE
elseif( $task == "suggest_field" )
{
  // GET USER INPUT AND LIMIT
  $input = strtolower( $_GET['input'] );
  $len = strlen_utf8($input);
  $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 20;

  // GET OPTIONS FROM URL
  $results = array();
  $options = ( is_array($_POST['options']) ? $_POST['options'] : ( is_array($_GET['options']) ? $_GET['options'] : array() ) );
	
  foreach( $options as $option_index=>$option_value )
    if( strtolower(substr($option_value, 0, $len)) == $input )
      $results[] = array("id"=>NULL, "value"=>$option_value, "info"=>$option_value, "photo"=>NULL, "photo_width"=>NULL);
  
  // OUTPUT JSON
  header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
  header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
  header ("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
  header ("Pragma: no-cache"); // HTTP/1.0
  header("Content-Type: application/json");
  echo json_encode(array('results' => &$results));
  exit();
}



// DELETE ACTION
elseif( $task == "action_delete" )
{
  $action_id = ( isset($_POST['action_id']) ? $_POST['action_id'] : FALSE );
  
  // MUST BE LOGGED IN TO USE THIS TASK, MAKE SURE USERS ARE ALLOWED TO DELETE ACTIONS
  if( !$user->user_exists || !$action_id || !$setting['setting_actions_selfdelete'] )
  {
    $result = FALSE;
  }
  
  else
  {
    // DELETE ACTION (IF OWNED BY LOGGED-IN USER)
    $result = TRUE;
    $database->database_query("DELETE FROM se_actions, se_actionmedia USING se_actions LEFT JOIN se_actionmedia ON se_actions.action_id=se_actionmedia.actionmedia_action_id WHERE (action_id='{$action_id}' AND action_user_id='{$user->user_info['user_id']}')");
  }
  
  // SEND AJAX CONFIRMATION
  header("Content-Type: application/json");
  echo json_encode(array('result' => $result, 'action_id' => (int) $action_id));
  exit();
}


// GET NOTIFICATIONS
elseif( $task == "notify_get" )
{
  // MUST BE LOGGED IN TO USE THIS TASK
  if( !$user->user_exists )
  {
    $result = FALSE;
    $data = array();
  }
  
  else
  {
    $result = TRUE;
    $data = $notify->notify_summary();
    SELanguage::load();
  }
  
  $data['result'] = $result;
  
  // SEND AJAX CONFIRMATION
  header("Content-Type: application/json");
  echo $se_javascript->generateNotifys($data);
  exit();
}


// DELETE NOTIFICATIONS
elseif( $task == "notify_delete" )
{
  $notifytype_id  = ( isset($_POST['notifytype_id'])  ? $_POST['notifytype_id']  : FALSE );
  $notify_grouped = ( isset($_POST['notify_grouped']) ? $_POST['notify_grouped'] : FALSE );
  
  // MUST BE LOGGED IN TO USE THIS TASK
  if( !$user->user_exists || !$notifytype_id )
  {
    $result = FALSE;
  }
  
  else
  {
    $result = $notify->notify_delete($notifytype_id, $notify_grouped);
  }
  
  // SEND AJAX CONFIRMATION
  header("Content-Type: application/json");
  echo json_encode(array('result' => $result, 'notifytype_id' => (int) $notifytype_id, 'notify_grouped' => $notify_grouped));
  exit();
}


// CHANGE STATUS
elseif( $task == "status_change" )
{
  // MUST BE LOGGED IN TO USE THIS TASK
  if( !$user->user_exists || !$user->level_info['level_profile_status'] )
  {
    $result = FALSE;
    $user_status = '';
  }
  
  else
  {
    $result = TRUE;
    $user_status = ( isset($_POST['status']) ? censor($_POST['status']) : "" );
    
    // ADD BREAKS TO STATUS SO IT WILL WRAP ON THE PAGE
    $user_status = chunkHTML_split(substr($user_status, 0, 100), 12, "<wbr>&shy;");
    
    // UPDATE STATUS AND LAST UPDATE DATE
    $database->database_query("UPDATE se_users SET user_status='{$user_status}', user_status_date='".time()."' WHERE user_id='{$user->user_info['user_id']}' LIMIT 1");
    $user->user_lastupdate();
    
    // INSERT ACTION IF STATUS IS NOT EMPTY
    if( trim($user_status) )
    {
      $actions->actions_add($user, "editstatus", Array($user->user_info['user_username'], $user->user_displayname, $user_status), Array(), 600, false, "user", $user->user_info['user_id'], $user->user_info['user_privacy']);
    }
  }
  
  // SEND AJAX CONFIRMATION
  header("Content-Type: application/json");
  echo json_encode(array('result' => $result, 'status' => (string) $user_status));
  exit();
}


// SAVE ACTION PREFERENCES
elseif( $task == "save_actionprefs" )
{
  // MUST BE LOGGED IN TO USE THIS TASK
  if( !$user->user_exists ) { exit(); }
  
  if($setting['setting_actions_preference'] == 1)
  {
    $actiontype = $_POST['actiontype'];
    $actiontype_query = $database->database_query("SELECT actiontype_id FROM se_actiontypes WHERE actiontype_id IN ('".implode("', '", $actiontype)."')");
    while($actiontype_info = $database->database_fetch_assoc($actiontype_query))
    {
      $actiontype_allowed[] = $actiontype_info['actiontype_id'];
    }

    // SAVE DISPLAYABLE ACTION TYPES IN THE USER SETTINGS TABLE
    $database->database_query("UPDATE se_usersettings SET usersetting_actions_display='".implode(",", $actiontype_allowed)."' WHERE usersetting_user_id='{$user->user_info['user_id']}' LIMIT 1");
  }

  // SEND AJAX CONFIRMATION
  echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><script type='text/javascript'>";
  echo "window.parent.location.href = 'user_home.php';";
  echo "</script></head><body></body></html>";
  exit();
}


// RETRIEVE COMMENTS
elseif($task == "comment_get")
{
  // GET COMMENT TYPE, ETC
  $type = ( isset($_POST['type']) ? $_POST['type'] : NULL );
  $iden = ( isset($_POST['iden']) ? $_POST['iden'] : NULL );
  $value = ( isset($_POST['value']) ? $_POST['value'] : NULL );
  $p = ( isset($_POST['p']) ? $_POST['p'] : 1 );
  $cpp = ( isset($_POST['cpp']) ? $_POST['cpp'] : 1 );
  $object_owner = ( isset($_POST['object_owner']) ? $_POST['object_owner'] : NULL );
  $object_owner_id = ( isset($_POST['object_owner_id']) ? $_POST['object_owner_id'] : NULL );
  $tab = ( isset($_POST['tab']) ? $_POST['tab'] : NULL );
  $col = ( isset($_POST['col']) ? $_POST['col'] : NULL );
  
  if( !$type || !$iden || !$value ) exit();
  
  // CHECK TO SEE IF OWNER EXISTS
  $object_exists = FALSE;
  if( $object_owner )
  {
    $object_owner = preg_replace('/[^A-Z0-9_\.-]/i', '', $object_owner);
    $classname = "se_".$object_owner;
    if( class_exists($classname) )
    { 
      $object_owner_class = new $classname($user->user_info['user_id'], $object_owner_id);
      $object_exists = $object_owner_class->{$object_owner."_exists"};
    }
  }
  
  if( !$owner->user_exists && !$object_exists ) exit();

  // START COMMENT OBJECT
  $comment = new se_comment($type, $iden, $value, $tab, $col);

  // GET TOTAL COMMENTS
  $total_comments = $comment->comment_total();

  // MAKE COMMENT PAGES AND GET COMMENT ARRAY
  $page_vars = make_page($total_comments, $cpp, $p);
  $comments = $comment->comment_list($page_vars[0], $cpp);

  // CONSTRUCT JSON RESPONSE
  $response_array = array(
    'total_comments'  => (int) $total_comments,
    'maxpage'         => (int) $page_vars[2],
    'p_start'         => (int) ($page_vars[0]+1),
    'p_end'           => (int) ($page_vars[0]+count($comments)),
    'p'               => (int) $page_vars[1],
    'comments'        => array()
  );
 
  foreach( $comments as $comment_index=>$comment_data )
  {
    // Escape trailing backslash
    if( substr($comment_data['comment_body'], -1, 1)=="\\" && substr($comment_data['comment_body'], -2, 2)!="\\\\" )
      $comment_data['comment_body'] .= "\\";
    
    $response_array['comments'][(int) $comment_data['comment_id']] = array
    (
      'comment_authoruser_id'           => (int)    $comment_data['comment_authoruser_id'],
      'comment_authoruser_exists'       => (bool)   $comment_data['comment_author']->user_exists,
      'comment_authoruser_private'      => (bool)   $comment_data['comment_author_private'],
      'comment_authoruser_url'          => (string) $url->url_create('profile', $comment_data['comment_author']->user_info['user_username']),
      'comment_authoruser_photo'        => (string) $comment_data['comment_author']->user_photo('./images/nophoto.gif'),
      'comment_authoruser_photo_width'  => (int)    $misc->photo_size($comment_data['comment_author']->user_photo('./images/nophoto.gif'),'75','75','w'),
      'comment_authoruser_username'     => (string) $comment_data['comment_author']->user_info['user_username'],
      'comment_authoruser_displayname'  => (string) $comment_data['comment_author']->user_displayname,
      'comment_date'                    => (string) $datetime->cdate("{$setting['setting_dateformat']} {$setting['setting_timeformat']}", $datetime->timezone($comment_data['comment_date'], $global_timezone)),
      'comment_body'                    => (string) $comment_data['comment_body']
    );
  }
 
  // OUTPUT JSON
  echo json_encode($response_array);
  exit();
}


// POST A COMMENT
elseif($task == "comment_post")
{
  // GET COMMENT TYPE, ETC
  $type = ( isset($_POST['type']) ? $_POST['type'] : NULL );
  $iden = ( isset($_POST['iden']) ? $_POST['iden'] : NULL );
  $value = ( isset($_POST['value']) ? $_POST['value'] : NULL );
  $tab = ( isset($_POST['tab']) ? $_POST['tab'] : NULL );
  $col = ( isset($_POST['col']) ? $_POST['col'] : NULL );
  $child = ( isset($_POST['child']) ? $_POST['child'] : FALSE );
  $tab_parent = ( isset($_POST['tab_parent']) ? $_POST['tab_parent'] : NULL );
  $col_parent = ( isset($_POST['col_parent']) ? $_POST['col_parent'] : NULL );
  $object_owner = ( isset($_POST['object_owner']) ? $_POST['object_owner'] : NULL );
  $object_owner_id = ( isset($_POST['object_owner_id']) ? $_POST['object_owner_id'] : NULL );
  
  if( !$type || !$iden || !$value || !$tab || !$col ) exit();
  
  $type = preg_replace('/[^A-Z0-9_\.-]/i', '', $type);
  $tab  = preg_replace('/[^A-Z0-9_\.-]/i', '', $tab );
  $col  = preg_replace('/[^A-Z0-9_\.-]/i', '', $col );
  
  // CHECK TO SEE IF OBJECT OWNER EXISTS
  if( $owner->user_exists )
  { 
    $object_owner = "user";
    $object_owner_id = $owner->user_info['user_id'];
    $object_owner_class =& $owner;
  }
  elseif( $object_owner )
  {
    $object_owner = preg_replace('/[^A-Z0-9_\.-]/i', '', $object_owner);
    $classname = "se_".$object_owner;
    $object_owner_class = new $classname($user->user_info['user_id'], $object_owner_id);
    if( !$object_owner_class->{$object_owner."_exists"} ) exit();
    $owner = new se_user(Array($object_owner_class->{$object_owner."_info"}[$object_owner."_user_id"]));
  }
  
  else
  {
    exit();
  }

  // RETRIEVE OBJECT
  $object = $database->database_query("SELECT * FROM `se_{$tab}` WHERE `{$iden}`='{$value}'");
  if( !$database->database_num_rows($object) ) exit();
  $object_info = $database->database_fetch_assoc($object);

  // RETRIEVE OBJECT OR PARENT OF OBJECT WE ARE COMMENTING ON
  if( $child )
  {
    $permission_query = $database->database_query("SELECT `{$col_parent}_privacy` AS object_privacy, `{$col_parent}_comments` AS object_comments FROM `se_{$tab}` LEFT JOIN `se_{$tab_parent}` ON `se_{$tab}`.`{$col}_{$col_parent}_id`=`se_{$tab_parent}`.`{$col_parent}_id` WHERE `se_{$tab}`.`{$iden}`='{$value}' AND `se_{$tab_parent}`.`{$col_parent}_{$object_owner}_id`='{$object_owner_id}'");
    if( !$database->database_num_rows($permission_query) ) exit();
    $permission = $database->database_fetch_assoc($permission_query);
  }
  else
  {
    $permission = $object_info;
    $permission['object_privacy'] = $object_info[$col."_privacy"];
    $permission['object_comments'] = $object_info[$col."_comments"];
    $ownercol = ( $object_owner == $col ? $col."_id" : $col."_".$object_owner."_id" );
    if( $object_info[$ownercol] != $object_owner_id ) exit();
  }

  // CHECK IF USER IS ALLOWED TO COMMENT
  $functionname = $object_owner."_privacy_max";
  $privacy_max = $object_owner_class->$functionname($user);
  if( !($privacy_max & $permission['object_comments']) ) exit();

  // SET OBJECT TITLE
  $object_title = $object_info[$col."_title"];
  
  if( $tab=="eventmedia" || $tab=="groupmedia" )
  {
    $object_title = $object_owner_class->{$object_owner."_info"}[$object_owner."_title"];
  }
  
  if( !$object_title )
  {
    SE_Language::_preload(589);
    SE_Language::load();
    $object_title = SE_Language::_get(589);
  }

  // START COMMENT OBJECT
  $comment = new se_comment($type, $iden, $value, $tab, $col);

  // POST COMMENT
  $comment_info = $comment->comment_post($_POST['comment_body'], $_POST['comment_secure'], $object_title, $object_owner, $object_owner_id, $permission['object_privacy']);
  
  $is_error = $comment->is_error;
  $comment_body = ( isset($comment_info['comment_body']) ? $comment_info['comment_body'] : NULL );
  $comment_date = ( isset($comment_info['comment_date']) ? $comment_info['comment_date'] : NULL );

  // RUN JAVASCRIPT FUNCTION (JSON)
  echo json_encode(array(
    'is_error' => $is_error,
    'comment_body' => $comment_body,
    'comment_date' => $comment_date
  ));
  
  exit();
}



// EDIT A COMMENT
elseif($task == "comment_edit")
{
  // MUST BE LOGGED IN TO USE THIS TASK
  if( !$user->user_exists ) { exit(); }
  
  // GET COMMENT TYPE, ETC
  $comment_id = ( isset($_POST['comment_id']) ? $_POST['comment_id'] : NULL );
  $comment_edit = ( isset($_POST['comment_edit']) ? $_POST['comment_edit'] : NULL );
  $type = ( isset($_POST['type']) ? $_POST['type'] : NULL );
  $iden = ( isset($_POST['iden']) ? $_POST['iden'] : NULL );
  $value = ( isset($_POST['value']) ? $_POST['value'] : NULL );
  
  if( !$type || !$iden || !$value || !$comment_id || !$comment_edit ) exit();
  
  // START COMMENT OBJECT
  $comment = new se_comment($type, $iden, $value);

  // EDIT COMMENT
  $comment->comment_edit($comment_id, $comment_edit);

  // RUN JAVASCRIPT FUNCTION (JSON)
  echo json_encode(array(
    'is_error' => FALSE
  ));
  
  exit();
}


// DELETE A COMMENT
elseif( $task == "comment_delete" )
{
  // MUST BE LOGGED IN TO USE THIS TASK
  if( !$user->user_exists ) { exit(); }
  
  // GET COMMENT TYPE, ETC
  $comment_id = ( isset($_POST['comment_id']) ? $_POST['comment_id'] : NULL );
  $type = ( isset($_POST['type']) ? $_POST['type'] : NULL );
  $iden = ( isset($_POST['iden']) ? $_POST['iden'] : NULL );
  $value = ( isset($_POST['value']) ? $_POST['value'] : NULL );
  $tab = ( isset($_POST['tab']) ? $_POST['tab'] : NULL );
  $col = ( isset($_POST['col']) ? $_POST['col'] : NULL );
  $child = ( isset($_POST['child']) ? $_POST['child'] : FALSE );
  $tab_parent = ( isset($_POST['tab_parent']) ? $_POST['tab_parent'] : NULL );
  $col_parent = ( isset($_POST['col_parent']) ? $_POST['col_parent'] : NULL );
  $object_owner = ( isset($_POST['object_owner']) ? $_POST['object_owner'] : NULL );
  $object_owner_id = ( isset($_POST['object_owner_id']) ? $_POST['object_owner_id'] : NULL );
  
  if( !$type || !$iden || !$value || !$tab || !$col || !$comment_id ) exit();
  
  $type = preg_replace('/[^A-Z0-9_\.-]/i', '', $type);
  $tab  = preg_replace('/[^A-Z0-9_\.-]/i', '', $tab );
  $col  = preg_replace('/[^A-Z0-9_\.-]/i', '', $col );
  
  // CHECK TO SEE IF OBJECT OWNER EXISTS
  if( $owner->user_exists )
  { 
    $object_owner = "user";
    $object_owner_id = $owner->user_info['user_id'];
    $object_owner_class =& $owner;
  }
  elseif( $object_owner )
  {
    $object_owner = preg_replace('/[^A-Z0-9_\.-]/i', '', $object_owner);
    $classname = "se_".$object_owner;
    $object_owner_class = new $classname($user->user_info['user_id'], $object_owner_id);
    if( !$object_owner_class->{$object_owner."_exists"} ) exit();
  }
  else
  {
    exit();
  }

  // RETRIEVE OBJECT
  $object = $database->database_query("SELECT * FROM `se_{$tab}` WHERE `{$iden}`='{$value}'");
  if( !$database->database_num_rows($object) ) exit();
  $object_info = $database->database_fetch_assoc($object);

  // RETRIEVE OBJECT OR PARENT OF OBJECT WE ARE COMMENTING ON
  if( $child )
  {
    $parent_query = $database->database_query("SELECT `{$col_parent}_{$object_owner}_id` AS object_owner_id FROM `se_{$tab}` LEFT JOIN `se_{$tab_parent}` ON `se_{$tab}`.`{$col}_{$col_parent}_id`=`se_{$tab_parent}`.`{$col_parent}_id` WHERE `se_{$tab}`.`{$iden}`='{$value}' AND `se_{$tab_parent}`.`{$col_parent}_{$object_owner}_id`='{$object_owner_id}'");
    if( !$database->database_num_rows($parent_query) ) exit();
    $parent = $database->database_fetch_assoc($parent_query);
    $object_info['object_owner_id'] = $parent['object_owner_id'];
  }
  else
  {
    $ownercol = ( $object_owner == $col ? $col."_id" : $col."_".$object_owner."_id" );
    if( $object_info[$ownercol] != $object_owner_id ) exit();
    $object_info['object_owner_id'] = $object_info[$owner_col];
  }

  // RETRIEVE COMMENT
  $comment_info = $database->database_fetch_assoc($database->database_query("SELECT `{$type}comment_authoruser_id` AS comment_authoruser_id FROM `se_{$type}comments` WHERE `{$type}comment_{$iden}`='{$value}' AND `{$type}comment_id`='{$comment_id}'"));

  // CHECK IF USER IS ALLOWED TO DELETE COMMENT
  $functionname = $object_owner."_privacy_max";
  $privacy_max = $object_owner_class->$functionname($user);
  if( !($privacy_max & $object_owner_class->{"moderation_privacy"}) && $user->user_info['user_id'] != $comment_info['comment_authoruser_id'] ) exit();

  // START COMMENT OBJECT
  $comment = new se_comment($type, $iden, $value, $tab, $col);

  // DELETE COMMENT
  $comment->comment_delete($comment_id);

  // RUN JAVASCRIPT FUNCTION (JSON)
  echo json_encode(array(
    'is_error' => FALSE
  ));
  
  exit();
}



// TAG A PHOTO
elseif($task == "tag_do")
{
  // GET COMMENT TYPE, ETC
  $type = ( isset($_POST['type']) ? $_POST['type'] : NULL );
  $media_id = ( isset($_POST['media_id']) ? $_POST['media_id'] : NULL );
  $media_dir = ( isset($_POST['media_dir']) ? $_POST['media_dir'] : NULL );
  $object_owner = ( isset($_POST['object_owner']) ? $_POST['object_owner'] : NULL );
  $object_owner_id = ( isset($_POST['object_owner_id']) ? $_POST['object_owner_id'] : NULL );
  
  if( !$media_id || !$media_dir ) exit();
  
  $mediatag_user_id = $_POST['mediatag_user_id'];
  $mediatag_text = $_POST['mediatag_text'];
  $mediatag_x = $_POST['mediatag_x'];
  $mediatag_y = $_POST['mediatag_y'];
  $mediatag_height = $_POST['mediatag_height'];
  $mediatag_width = $_POST['mediatag_width'];

  // CHECK TO SEE IF OBJECT OWNER EXISTS
  if( $owner->user_exists )
  { 
    $object_owner = "user";
    $object_owner_id = $owner->user_info['user_id'];
    $object_owner_class = $owner;
    $owner_user = $owner;
    $owner_iden = $owner->user_info['user_username'];
  }
  else
  {
    $classname = "se_".$object_owner;
    $object_owner_class = new $classname($user->user_info['user_id'], $object_owner_id);
    if(!$object_owner_class->{$object_owner."_exists"}) { exit(); }
    $owner_user = new se_user(Array($object_owner_class->{$object_owner."_info"}[$object_owner."_user_id"]));
    $owner_iden = $object_owner_id;
  }

  // RETRIEVE OBJECT
  $media_query = $database->database_query("SELECT * FROM `se_{$type}media` WHERE `{$type}media_id`='{$media_id}'");
  if($database->database_num_rows($media_query) == 0) { exit(); }
  $media_info = $database->database_fetch_assoc($media_query);

  // RETRIEVE ALBUM WE ARE COMMENTING ON
  $album_query = $database->database_query("SELECT `{$type}album_privacy` AS object_privacy, `{$type}album_tag` AS object_tag FROM `se_{$type}media` LEFT JOIN `se_{$type}albums` ON `se_{$type}media`.`{$type}media_{$type}album_id`=`se_{$type}albums`.`{$type}album_id` WHERE `se_{$type}media`.`{$type}media_id`='{$media_id}' AND `se_{$type}albums`.`{$type}album_{$object_owner}_id`='{$object_owner_id}'");
  if($database->database_num_rows($album_query) != 1) { exit(); }
  $album_info = $database->database_fetch_assoc($album_query);

  // CHECK IF USER IS ALLOWED TO TAG
  $functionname = $object_owner."_privacy_max";
  $privacy_max = $object_owner_class->$functionname($user);

  if(!($privacy_max & $album_info['object_tag'])) { exit(); }

  // GET TAGGED USER
  $tagged_query = $database->database_query("SELECT user_id, user_username, user_email, user_fname, user_lname, user_privacy FROM se_users WHERE user_id='{$mediatag_user_id}'");
  if($database->database_num_rows($tagged_query) == 1)
  {
    $tagged = $database->database_fetch_assoc($tagged_query);
    
    $taggeduser = new se_user();
    $taggeduser->user_exists = 1;
    $taggeduser->user_info['user_id'] = $tagged['user_id'];
    $taggeduser->user_info['user_username'] = $tagged['user_username'];
    $taggeduser->user_info['user_email'] = $tagged['user_email'];
    $taggeduser->user_info['user_fname'] = $tagged['user_fname'];
    $taggeduser->user_info['user_lname'] = $tagged['user_lname'];
    $taggeduser->user_info['user_privacy'] = $tagged['user_privacy'];
    $taggeduser->user_displayname();
    
    $mediatag_user_username = $tagged['user_username'];
    $mediatag_link = $url->url_create("profile", $tagged['user_username']);
    $mediatag_text = $taggeduser->user_displayname;
  }
  elseif(trim($mediatag_text) != "")
  {
    $mediatag_text = substr($mediatag_text, 0, 40);
    $mediatag_link = "";
    $mediatag_user_id = 0;
    $mediatag_user_username = "";
  }
  else
  {
    exit();
  }

  // GET MEDIA HEIGHT AND WIDTH
  $mediasize = @getimagesize($media_dir.$media_info[$type.'media_id'].'.'.$media_info[$type.'media_ext']);
  $media_info['media_width'] = $mediasize[0];
  $media_info['media_height'] = $mediasize[1];

  // VALIDATE TAG HEIGHT AND WIDTH BASED ON IMAGE SIZE
  if($mediatag_x+$mediatag_height > $media_info['media_height']) { $mediatag_x = $mediatag_x-(($mediatag_x+$mediatag_height)-$media_info['media_height']); }
  if($mediatag_y+$mediatag_width > $media_info['media_width']) { $mediatag_y = $mediatag_y-(($mediatag_y+$mediatag_width)-$media_info['media_width']); }

  $database->database_query("
    INSERT INTO `se_{$type}mediatags` (
      `{$type}mediatag_{$type}media_id`,
      `{$type}mediatag_user_id`,
      `{$type}mediatag_x`,
      `{$type}mediatag_y`,
      `{$type}mediatag_height`,
      `{$type}mediatag_width`,
      `{$type}mediatag_text`,
      `{$type}mediatag_date`
    ) VALUES (
      '".$media_info[$type.'media_id']."',
      '$mediatag_user_id',
      '$mediatag_x',
      '$mediatag_y',
      '$mediatag_height',
      '$mediatag_width',
      '$mediatag_text',
      '".time()."'
    )
  ");
  
  $mediatag_id = $database->database_insert_id();

  // SET OBJECT TITLE
  $object_title = $media_info[$type.'media_title'];
  if($object_title == "") { $object_title = SE_Language::get(589); }

  // SEND NOTIFICATION TO OWNER
  if($owner_user->user_info['user_id'] != $user->user_info['user_id'])
  {
    $notifytype = $notify->notify_add($owner_user->user_info['user_id'], $type.'mediatag', $media_info[$type.'media_id'], Array($owner_user->user_info['user_username'], $media_info[$type.'media_id'], $object_owner_id), Array($object_title));
    $object_url = $url->url_base.vsprintf($notifytype['notifytype_url'], Array($owner_iden, $media_info[$type.'media_id']));
    $owner_user->user_settings();
    if($owner_user->usersetting_info['usersetting_notify_'.$type.'mediatag'])
    {
      send_systememail($type.'mediatag', $owner_user->user_info['user_email'], Array($owner_user->user_displayname, $user->user_displayname, "<a href=\"".$object_url."\">".$object_url."</a>"));
    }
  }

  // INSERT ACTION AND SEND NOTIFICATION TO TAGGED USER
  if($taggeduser->user_exists == 1)
  {
    // ENSURE USER ISN'T ALREADY TAGGED IN THIS PHOTO
    if($database->database_num_rows($database->database_query("SELECT `{$type}mediatag_id` FROM `se_{$type}mediatags` WHERE `{$type}mediatag_{$type}media_id`='".$media_info[$type.'media_id']."' AND `{$type}mediatag_user_id`='{$taggeduser->user_info['user_id']}'")) == 1)
    {
      $media_path = $media_dir.$media_info[$type.'media_id']."_thumb.jpg";
      $media_width = $misc->photo_size($media_path, "100", "100", "w");
      $media_height = $misc->photo_size($media_path, "100", "100", "h");
      $action_media[] = Array(
        'media_link' => "profile_photos_file.php?user={$taggeduser->user_info['user_username']}&type={$type}media&media_id=".$media_info[$type.'media_id'],
				'media_path' => $media_path,
				'media_width' => $media_width,
				'media_height' => $media_height
      );
      $actions->actions_add($taggeduser, "new{$type}tag", Array($taggeduser->user_info['user_username'], $taggeduser->user_displayname), $action_media, 600, false, "user", $taggeduser->user_info['user_id'], $taggeduser->user_info['user_privacy']);
    }
    if($taggeduser->user_info['user_id'] != $owner_user->user_info['user_id'] && $taggeduser->user_info['user_id'] != $user->user_info['user_id'])
    {
      $notify->notify_add($taggeduser->user_info['user_id'], 'new'.$type.'tag', $media_info[$type.'media_id'], Array($taggeduser->user_info['user_username'], $type.'media', $media_info[$type.'media_id']), Array($object_title));
      $taggeduser->user_settings();
      if($taggeduser->usersetting_info['usersetting_notify_new'.$type.'tag'])
      {
        send_systememail('new'.$type.'tag', $taggeduser->user_info['user_email'], Array($taggeduser->user_displayname, "<a href=\"".$url->url_base."profile_photos_file.php?user={$taggeduser->user_info['user_username']}&type={$type}media&media_id={$media_info[$type.'media_id']}\">{$url->url_base}profile_photos_file.php?user={$taggeduser->user_info['user_username']}&type={$type}media&media_id=".$media_info[$type.'media_id']."</a>"));
      }
    }
  }
  
  // RUN JAVASCRIPT FUNCTION (JSON)
  echo json_encode(array(
    'mediatag_id' => $mediatag_id,
    'mediatag_link' => $mediatag_link,
    'mediatag_text' => $mediatag_text,
    'mediatag_x' => $mediatag_x,
    'mediatag_y' => $mediatag_y,
    'mediatag_width' => $mediatag_width,
    'mediatag_height' => $mediatag_height,
    'mediatag_user_username' => $mediatag_user_username
  ));
  
  exit();
}



// REMOVE A PHOTO TAG
elseif($task == "tag_remove")
{
  // MUST BE LOGGED IN TO USE THIS TASK
  if( !$user->user_exists ) { exit(); }
  
  // GET COMMENT TYPE, ETC
  $type = ( isset($_POST['type']) ? $_POST['type'] : NULL );
  $media_id = ( isset($_POST['media_id']) ? $_POST['media_id'] : NULL );
  $mediatag_id = ( isset($_POST['mediatag_id']) ? $_POST['mediatag_id'] : NULL );
  $object_owner = ( isset($_POST['object_owner']) ? $_POST['object_owner'] : NULL );
  $object_owner_id = ( isset($_POST['object_owner_id']) ? $_POST['object_owner_id'] : NULL );
  
  if( !$media_id || !$mediatag_id ) exit();
  
  // CHECK TO SEE IF OBJECT OWNER EXISTS
  if($owner->user_exists)
  { 
    $object_owner = "user";
    $object_owner_id = $owner->user_info['user_id'];
    $object_owner_class = $owner;
  }
  else
  {
    $classname = "se_".$object_owner;
    $object_owner_class = new $classname($user->user_info['user_id'], $object_owner_id);
    if(!$object_owner_class->{$object_owner."_exists"}) { exit(); }
  }

  // RETRIEVE OBJECT
  $media_query = $database->database_query("SELECT * FROM `se_{$type}media` WHERE `{$type}media_id`='{$media_id}'");
  if($database->database_num_rows($media_query) == 0) { exit(); }
  $media_info = $database->database_fetch_assoc($media_query);

  // RETRIEVE ALBUM WE ARE COMMENTING ON
  $album_query = $database->database_query("SELECT `{$type}album_privacy` AS object_privacy, `{$type}album_tag` AS object_tag FROM `se_{$type}media` LEFT JOIN `se_{$type}albums` ON `se_{$type}media`.`{$type}media_{$type}album_id`=`se_{$type}albums`.`{$type}album_id` WHERE `se_{$type}media`.`{$type}media_id`='{$media_id}' AND `se_{$type}albums`.`{$type}album_{$object_owner}_id`='{$object_owner_id}'");
  if($database->database_num_rows($album_query) != 1) { exit(); }
  $album_info = $database->database_fetch_assoc($album_query);

  // GET MEDIA TAG
  $mediatag = $database->database_query("SELECT `{$type}mediatag_id` AS mediatag_id, `{$type}mediatag_user_id` AS mediatag_user_id FROM `se_{$type}mediatags` LEFT JOIN `se_{$type}media` ON `se_{$type}mediatags`.`{$type}mediatag_{$type}media_id`=`se_{$type}media`.`{$type}media_id` LEFT JOIN `se_{$type}albums` ON `se_{$type}media`.`{$type}media_{$type}album_id`=`se_{$type}albums`.`{$type}album_id` WHERE `{$type}mediatag_id`='{$mediatag_id}'");
  if($database->database_num_rows($mediatag) != 1) { exit(); }
  $mediatag_info = $database->database_fetch_assoc($mediatag);

  // CHECK IF USER IS ALLOWED TO DELETE TAG
  $functionname = $object_owner."_privacy_max";
  $privacy_max = $object_owner_class->$functionname($user);
  if(!($privacy_max & $object_owner_class->{"moderation_privacy"}) && $user->user_info['user_id'] != $mediatag_info['mediatag_user_id']) { exit(); }

  // DELETE TAG
  $database->database_query("DELETE FROM `se_{$type}mediatags` WHERE `{$type}mediatag_{$type}media_id`='{$media_id}' AND `{$type}mediatag_id`='{$mediatag_id}'");
  
  // RUN JAVASCRIPT FUNCTION (JSON)
  if( $is_ajax )
  {
    echo json_encode(array(
      'result' => TRUE
    ));
  }
  
  exit();
}



// GET DEBUG INFO
elseif($task == "get_debug_info")
{
  if( !is_object($admin) || !$admin->admin_exists )
    exit();
  
  if(isset($_POST['id'])) { $id = $_POST['id']; } elseif(isset($_GET['id'])) { $id = $_GET['id']; } else { exit(); }
  $id = preg_replace('/[^a-zA-Z0-9\._]/', '', $id);
  
  echo file_get_contents(SE_ROOT.DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR.$id.'.html');
  
  // Delete logs older than an hour
  if( $dh = @opendir(SE_ROOT.DIRECTORY_SEPARATOR.'log') )
  {
    while( ($file = @readdir($dh)) !== false )
    {
      if( $file == "." || $file == ".." ) continue;
      if( filemtime(SE_ROOT.DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR.$file)>time()-3600 ) continue;
      @unlink(SE_ROOT.DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR.$file);
    }
  }
  
  exit();
}


?>