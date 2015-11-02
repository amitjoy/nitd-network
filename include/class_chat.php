<?php

defined('SE_PAGE') or exit();


// seIM Constants
define('SEIM_TYPE_CHAT', 1);
define('SEIM_TYPE_IM',   2);

define('SEIM_CONNECTIONSTATE_DISCONNECTED', 0);
define('SEIM_CONNECTIONSTATE_CONNECTED',    1);

define('SEIM_USERSTATUS_OFFLINE',           0);
define('SEIM_USERSTATUS_ONLINE',            1);
define('SEIM_USERSTATUS_AWAY',              2);
define('SEIM_USERSTATUS_BUSY',              3);
define('SEIM_USERSTATUS_CUSTOM',            4);

define('SEIM_MASKSTATUS_INACTIVE',          0);
define('SEIM_MASKSTATUS_ACTIVE',            1);

define('SEIM_MASKUSERSTATUS_QUIT',          0);
define('SEIM_MASKUSERSTATUS_JOINED',        1);
define('SEIM_MASKUSERSTATUS_ACTIVE',        2);







class seIM
{
  var $user_id;
  
  
  var $user_info;
  
  
  var $chat_user_info = array();
  
  
  var $chat_user_masks = array();
  
  
  var $input_filter;
  
  
  
  
  
  
  /* ----------------------------------------------------------------------- *\
  |                                                                           |
  | General Methods                                                           |
  |                                                                           |
  \* ----------------------------------------------------------------------- */
  
  
  
  function seIM()
  {
    global $database, $user, $setting;
    
    $this->user_id           =   $user->user_info['user_id'];
    $this->user_info         =&  $user->user_info;
    
    $this->input_filter      =   new InputFilter(explode(",", $setting['setting_im_html']), explode(",", 'style,href,src'), 0, 0, 1);
    
    $this->chat_user_info    =   $this->getSingleUserInfo($this->user_id);
    $this->chat_user_masks   =   $this->getSingleUserMasks($this->user_id);
  }
  
  
  
  
  function execute($task)
  {
    $this->security($_COOKIE); // Problems with messages
    $this->security($_REQUEST);
    
    $response = array();
    $last_update = ( !empty($_COOKIE['seIM_last_update']) ? $_COOKIE['seIM_last_update'] : NULL );
    
    
    // Validate existing session
    if( $task!='login' )
    {
      $session_id = $_COOKIE['seIM_session_id'];
      
      if( !$this->userValidate($this->user_id, $session_id) )
        return array(
          'task' => $task,
          'response' => array(
            'result' => FALSE,
            'loginStatus' => FALSE
          )
        );
    }
    
    
    // Cleanup
    $this->cleanup();
    
    
    
    // Execute
    switch( $task )
    {
      case 'update':
      case 'ping':
        $response = $this->ping($last_update);
      break;
      
      case 'login':
      case 'userLogin':
        $response = $this->userLogin($this->user_id);
      break;
      
      case 'logout':
      case 'userLogout':
        $response = $this->userLogout($this->user_id);
      break;
      
      case 'userUpdate':
        $response = $this->userUpdate($this->user_id, $_REQUEST['user_status'], $_REQUEST['user_message']);
      break;
      
      case 'userProfile':
        $response = $this->userProfile($_POST['user_id']);
      break;
      
      case 'maskCreate':
        $response = $this->maskCreate($this->user_id, $_REQUEST['mask_type'], $_REQUEST['mask_title'], $_REQUEST['user_id1'], $_REQUEST['user_id2']);
      break;
      
      case 'maskDestroy':
        $response = $this->maskDestroy($this->user_id, $_REQUEST['mask_id']);
      break;
      
      case 'maskUserJoin':
        $maskJoinUserID = ( !empty($_POST['user_id']) ? $_POST['user_id'] : $this->user_id );
        $response = $this->maskUserJoin($maskJoinUserID, $_REQUEST['mask_id']);
      break;
      
      case 'maskUserLeave':
        $response = $this->maskUserLeave($this->user_id, $_REQUEST['mask_id']);
      break;
      
      case 'maskUserUpdate':
        $response = $this->maskUserUpdate($this->user_id, $_REQUEST['mask_id'], $_REQUEST['mask_user_status']);
      break;
      
      case 'messageSend':
        $response = $this->messageSend($this->user_id, $_REQUEST['mask_id'], $_REQUEST['message_content']);
      break;
      
      case 'emoteMessageSend':
        $emoteContent = $this->user_info['user_username'] . ' ' . $_REQUEST['message_content'];
        $response = $this->systemMessageSend($this->user_id, $_REQUEST['mask_id'], $emoteContent);
      break;
      
      case 'systemMessageSend':
        $response = $this->systemMessageSend($this->user_id, $_REQUEST['mask_id'], $_REQUEST['message_content']);
      break;
      
      default:
        return array(
          'task' => $task,
          'response' => array(
            'result' => FALSE
          ),
          'messages' => array(
            'Unknown Request'
          )
        );
      break;
    }
    
    return array(
      'task' => $task,
      'response' => &$response
    );
  }
  
  
  
  
  
  
  
  /* ----------------------------------------------------------------------- *\
  |                                                                           |
  | Security Methods                                                              |
  |                                                                           |
  \* ----------------------------------------------------------------------- */
  
  
  function security(&$value)
  {
    if( is_array($value) || is_object($value) )
    {
      foreach( $value as $_vk=>$_vv )
      {
        $this->security($value[$_vk]);
      }
    }
    
    elseif( is_string($value) && !is_numeric($value) )
    {
      $value = $this->input_filter->process($value);
    }
    
    // Things that should not be processed: booleans, numbers
    return;
  }
  
  
  
  
  
  
  
  
  
  
  
  
  /* ----------------------------------------------------------------------- *\
  |                                                                           |
  | User Methods                                                              |
  |                                                                           |
  \* ----------------------------------------------------------------------- */
  
  
  function userLogin($user_id)
  {
    global $database, $setting;
    
    // Security
    $user_id = $database->database_real_escape_string($user_id);
    
    // Attempt to continue existing session
    $session_id = $_COOKIE['seIM_session_id'];
    
    if( empty($session_id) || !$this->userValidate($user_id, $session_id) )
    {
      // Create a new session
      $session_id = md5(uniqid(rand(), TRUE));
      $time = time();
      
      // Run query
      $sql = "
        INSERT INTO
          se_chat_users
        (
          chat_user_id,
          chat_user_session_id,
          chat_user_session_lastupdate,
          chat_user_status,
          chat_user_lastupdate
        )
        VALUES
        (
          '$user_id',
          '$session_id',
          '$time',
          '".SEIM_USERSTATUS_ONLINE."',
          '$time'
        )
        ON DUPLICATE KEY UPDATE
          chat_user_session_id='$session_id',
          chat_user_session_lastupdate='$time',
          chat_user_status='".SEIM_USERSTATUS_ONLINE."',
          chat_user_lastupdate='$time'
      ";
      
      $resource = $database->database_query($sql) or die($database->database_error()." SQL was: $sql");
      
      if( !$database->database_affected_rows($resource) )
        return array( 'result' => FALSE );
    }
    
    
    
    // Save session ID and reset lastupdate
    setcookie('seIM_session_id',  $session_id,  time() + 86400, '/');
    //setcookie('seIM_last_update', 1,            time() + 86400, '/');
    
    $allUpdates = $this->ping(NULL);
    $allUpdates['sessionID'] = $session_id;
    $allUpdates['allowedHtmlTags'] = $setting['setting_im_html'];
    
    // Settings
    $allUpdates['settingChatUpdate'] = $setting['setting_chat_update'];
    $allUpdates['settingChatShowPhotos'] = $setting['setting_chat_showphotos'];
    
    return $allUpdates;
  }
  
  
  
  
  function userLogout($user_id)
  {
    global $database;
    
    // Security
    $user_id = $database->database_real_escape_string($user_id);
    
    $time = time();
    
    // Run query
    $sql = "
      UPDATE
        se_chat_users
      SET
        chat_user_status='".SEIM_USERSTATUS_OFFLINE."',
        chat_user_lastupdate='$time',
        chat_user_session_id=NULL,
        chat_user_session_lastupdate='$time'
      WHERE
        chat_user_id='$user_id'
      LIMIT
        1
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL was: $sql");
    
    return array('result' => (bool) $database->database_affected_rows($resource));
  }
  
  
  
  
  function userValidate($user_id, $session_id)
  {
    global $database;
    
    // Security
    $user_id = $database->database_real_escape_string($user_id);
    $session_id = $database->database_real_escape_string($session_id);
    
    // Run query
    $sql = "
      SELECT
        NULL
      FROM
        se_chat_users
      WHERE
        chat_user_id='$user_id' &&
        chat_user_session_id='$session_id'
      LIMIT
        1
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL was: $sql");
    
    return (bool) $database->database_num_rows($resource);
  }
  
  
  
  function userUpdate($user_id, $user_status=NULL, $user_message=NULL)
  {
    global $database;
    
    // Security
    $user_id = $database->database_real_escape_string($user_id);
    $user_status = $database->database_real_escape_string($user_status);
    $user_message = $database->database_real_escape_string($user_message);
    
    $time = time();
    
    // Run query
    $sql = "
      UPDATE
        se_chat_users
      LEFT JOIN
        se_users
        ON se_users.user_id=se_chat_users.chat_user_id
      SET
        se_chat_users.chat_user_session_lastupdate=$time
    ";
    
    if( isset($user_status) || isset($user_message) ) $sql .= ",
        se_chat_users.chat_user_lastupdate=$time
    ";
    
    if( isset($user_status) ) $sql .= ",
        se_chat_users.chat_user_status='$user_status'
    ";
    
    if( isset($user_message) ) $sql .= ",
        se_users.user_status='$user_message',
        se_users.user_status_date=$time
    ";
    
    $sql .= "
      WHERE
        chat_user_id='$user_id'
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL was: $sql");
    $result = (bool) $database->database_affected_rows($resource);
    
    /*
    if( !empty($this->chat_user_masks) )
    {
      $sql = "
        UPDATE
          se_chat_mask_users
        SET
          chat_mask_user_status='".SEIM_MASKUSERSTATUS_ACTIVE."',
          chat_mask_user_lastupdate='".($time-6)."'
        WHERE
          chat_mask_user_user_id={$this->user_id} &&
          chat_mask_user_status!='0' &&
          chat_mask_user_lastupdate<'".($time-6)."'
      ";
      
      $resource = $database->database_query($sql) or die($database->database_error()." SQL was: $sql");
    }
    */
    
    return array('result' => (bool) $database->database_affected_rows($resource));
  }
  
  
  
  function userProfile($user_id)
  {
    global $database;
    
    // Security
    $user_id = $database->database_real_escape_string($user_id);
    
    $sql = "
      SELECT
        se_users.user_id                            AS userID,
        se_users.user_username                      AS userName,
        se_users.user_photo                         AS userPhoto,
        se_users.user_status                        AS userMessage,
        se_chat_users.chat_user_status              AS userStatus,
        se_friends.friend_status                    AS isFriendOfActiveUser
      FROM
        se_chat_users
      LEFT JOIN
        se_users
        ON se_users.user_id=se_chat_users.chat_user_id
      WHERE
        se_chat_users.chat_user_id='$user_id'
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL was: $sql");
    $row = $database->database_fetch_assoc($resource);
    
    if( empty($row) ) return array('result' => FALSE);
    
    
    $row['userPath']      = $url->url_userdir($row['userID']);
    $row['isActiveUser']  = ( $row['userID']==$this->user_id );
    $row['isFriendOfActiveUser']  = ( $row['userID']!=$this->user_id );
    
    return array(
      'result' => TRUE,
      'userData' => $row
    );
  }
  
  
  
  function &ping($last_update=NULL)
  {
    global $database;
    
    // Security
    $last_update = $database->database_real_escape_string($last_update);
    
    $time = time();
    
    // Update user online status
    $sql = "
      UPDATE
        se_chat_users
      SET
        chat_user_session_lastupdate=$time
      WHERE
        chat_user_id={$this->user_id}
      LIMIT
        1
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL was: $sql");
    
    
    
    
    // Gett all updates
    $allUpdates = $this->allUpdates($last_update);
    
    $allUpdates['result'] = TRUE;
    $allUpdates['loginStatus'] = TRUE;
    
    setcookie('seIM_last_update', time()-1,     time() + 86400, '/');
    
    return $allUpdates;
  }
  
  
  
  
  
  
  
  
  
  
  
  
  
  /* ----------------------------------------------------------------------- *\
  |                                                                           |
  | Mask Methods                                                              |
  |                                                                           |
  \* ----------------------------------------------------------------------- */
  
  
  function maskCreate($user_id, $mask_type, $mask_title=NULL, $user_id1=NULL, $user_id2=NULL)
  {
    global $database;
    
    // Security
    $user_id = $database->database_real_escape_string($user_id);
    $mask_type = $database->database_real_escape_string($mask_type);
    $mask_title = $database->database_real_escape_string($mask_title);
    $user_id1 = $database->database_real_escape_string($user_id1);
    $user_id2 = $database->database_real_escape_string($user_id2);
    
    $mask_id = NULL;
    $looked_up = FALSE;
    if( empty($mask_type) )   $mask_type = SEIM_TYPE_IM;
    
    // Try to resume an existing conversation
    if( !empty($user_id1) && !empty($user_id2) )
    {
      $mask_id = $this->maskUserLookup($user_id1, $user_id2, $mask_type);
      
      if( $mask_id )
      {
        $looked_up = TRUE;
        $time = time();
        
        $sql = "
          UPDATE
            se_chat_masks
          SET
            chat_mask_lastupdate=$time
          WHERE
            chat_mask_id=$mask_id
          LIMIT
            1
        ";
        
        $resource = $database->database_query($sql) or die($database->database_error()." SQL was: $sql");
      }
    }
    
    
    // Create new mask
    if( empty($mask_id) )
    {
      if( empty($mask_title) )  $mask_title = 'Private Conversation';
      $mask_public  = ( TRUE  ? '1'                       : '0' );
      $mask_code    = ( FALSE ? $arguments['mask_code']   : substr(md5(time()), rand(0,23), 8) );
      
      $mask_title = censor($mask_title);
      $time = time();
      
      // Run query
      $sql = "
        INSERT INTO
          se_chat_masks
        (
          chat_mask_title,
          chat_mask_type,
          chat_mask_public,
          chat_mask_code,
          chat_mask_status,
          chat_mask_lastupdate
        )
        VALUES
        (
          '$mask_title',
          '$mask_type',
          '$mask_public',
          '$mask_code',
          '".SEIM_MASKSTATUS_ACTIVE."',
          '$time'
        )
      ";
      
      $resource = $database->database_query($sql) or die($database->database_error()." SQL was: $sql");
      
      if( !$database->database_affected_rows($resource) )
        return array( 'result' => FALSE );
      
      $mask_id = $database->database_insert_id();
    }
    
    
    if( $mask_id )
    {
      if( $user_id1 ) $this->maskUserJoin($user_id1, $mask_id);
      if( $user_id2 ) $this->maskUserJoin($user_id2, $mask_id);
    }
    
    
    return array(
      'result' => TRUE,
      'maskID' => $mask_id,
      'maskTitle' => $mask_title,
      'maskType' => $mask_type,
      'lookedUp' => $looked_up
    );
  }
  
  
  
  function maskDestroy($user_id, $mask_id)
  {
    global $database;
    
    // Security
    $user_id = $database->database_real_escape_string($user_id);
    $mask_id = $database->database_real_escape_string($mask_id);
    
    // TODO: Verfiy user has this ability
    
    // Run query
    $sql = "
      DELETE
        se_chat_masks.*,
        se_chat_mask_users.*
      FROM
        se_chat_masks
      LEFT JOIN
        se_chat_mask_users
        ON se_chat_mask_users.chat_mask_user_mask_id=se_chat_masks.chat_mask_id
      WHERE
        se_chat_masks.chat_mask_id='$mask_id'
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL was: $sql");
    
    return (bool) $database->database_affected_rows($resource);
  }
  
  
  
  
  
  
  
  
  
  
  
  
  
  /* ----------------------------------------------------------------------- *\
  |                                                                           |
  | Mask Methods                                                              |
  |                                                                           |
  \* ----------------------------------------------------------------------- */
  
  
  function maskUserJoin($user_id, $mask_id)
  {
    global $database;
    
    // Security
    $user_id = $database->database_real_escape_string($user_id);
    $mask_id = $database->database_real_escape_string($mask_id);
    
    // Process arguments
    $time = time();
    $existed = FALSE;
    
    // Check for already joined
    $sql = "
      SELECT
        chat_mask_user_id
      FROM
        se_chat_mask_users
      WHERE
        chat_mask_user_mask_id='$mask_id' &&
        chat_mask_user_user_id='$user_id'
      LIMIT
        1
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL was: $sql");
    $result = $database->database_fetch_assoc($resource);
    $chat_mask_user_id = ( !empty($result['chat_mask_user_id']) ? $result['chat_mask_user_id'] : FALSE );
    
    // User does not already belong
    if( !$chat_mask_user_id )
    {
      // Run query
      $sql = "
        INSERT INTO
          se_chat_mask_users
        (
          chat_mask_user_mask_id,
          chat_mask_user_user_id,
          chat_mask_user_status,
          chat_mask_user_lastupdate
        )
        VALUES
        (
          '$mask_id',
          '$user_id',
          '".SEIM_MASKUSERSTATUS_JOINED."',
          '$time'
        )
      ";
      
      $resource = $database->database_query($sql) or die($database->database_error()." SQL was: $sql");
      $result = ( $database->database_affected_rows($resource) ? $database->database_insert_id() : FALSE );
    }
    
    // Update to active
    else
    {
      $existed = TRUE;
      
      $sql = "
        UPDATE
          se_chat_mask_users
        SET
          chat_mask_user_status='".( SEIM_MASKUSERSTATUS_JOINED ? SEIM_MASKUSERSTATUS_JOINED : 1 )."',
          chat_mask_user_lastupdate=$time
        WHERE
          chat_mask_user_id='$chat_mask_user_id'
        LIMIT
          1
      ";
      
      $resource = $database->database_query($sql) or die($database->database_error()." SQL was: $sql");
      $result = ( $database->database_affected_rows($resource) ? $chat_mask_user_id : FALSE );
    }
    
    return array(
      'result' => $result,
      'maskUserID' => $chat_mask_user_id,
      'maskUserExisted' => $existed,
      'userID' => $user_id
    );
  }
  
  
  
  function maskUserLeave($user_id, $mask_id)
  {
    global $database;
    
    // Security
    $user_id = $database->database_real_escape_string($user_id);
    $mask_id = $database->database_real_escape_string($mask_id);
    
    // Process arguments
    $time = time();
    
    // Run query
    $sql = "
      UPDATE
        se_chat_mask_users
      SET
        chat_mask_user_status='".SEIM_MASKUSERSTATUS_QUIT."',
        chat_mask_user_lastupdate='$time'
      WHERE
        chat_mask_user_mask_id='$mask_id' AND
        chat_mask_user_user_id='$user_id'
      LIMIT
        1
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL was: $sql");
    
    return array( 'result' => (bool) $database->database_affected_rows($resource) );
  }
  
  
  
  function maskUserLookup($user_id, $other_user_id, $mask_type)
  {
    global $database, $user;
    
    // Security
    $user_id = $database->database_real_escape_string($user_id);
    $other_user_id = $database->database_real_escape_string($other_user_id);
    $mask_type = $database->database_real_escape_string($mask_type);
    
    // Run query
    $sql = "
      SELECT
        local_masks.chat_mask_user_mask_id AS mask_id
      FROM
        se_chat_mask_users AS local_masks
      LEFT JOIN
        se_chat_mask_users AS remote_users
        ON remote_users.chat_mask_user_mask_id=local_masks.chat_mask_user_mask_id
      LEFT JOIN
        se_chat_masks
        ON se_chat_masks.chat_mask_id=local_masks.chat_mask_user_mask_id
      WHERE
        local_masks.chat_mask_user_user_id='$user_id' &&
        remote_users.chat_mask_user_user_id='$other_user_id' &&
        se_chat_masks.chat_mask_type='$mask_type'
      LIMIT
        1
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL was: $sql");
    
    if( !$database->database_num_rows($resource) ) return FALSE;
    
    // Exists
    $mask_data = $database->database_fetch_assoc($resource);
    $mask_id = $mask_data['mask_id'];
    
    
    // Update this mask
    $time = time();
    
    $sql = "
      UPDATE
        se_chat_masks
      SET
        chat_mask_status='".SEIM_MASKSTATUS_ACTIVE."',
        chat_mask_lastupdate=$time
      WHERE
        chat_mask_id='{$mask_id}'
      LIMIT
        1
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL was: $sql");
    
    
    $sql = "
      UPDATE
        se_chat_mask_users
      SET
        chat_mask_user_status='".SEIM_MASKUSERSTATUS_ACTIVE."',
        chat_mask_user_lastupdate=$time
      WHERE
        chat_mask_user_mask_id='{$mask_id}'
      LIMIT
        1
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL was: $sql");
    
    return $mask_id;
  }
  
  
  
  function maskUserValidate($user_id, $mask_id)
  {
    global $database;
    
    // Security
    $user_id = $database->database_real_escape_string($user_id);
    $mask_id = $database->database_real_escape_string($mask_id);
    
    // Run query
    $sql = "
      SELECT
        NULL
      FROM
        se_chat_mask_users
      WHERE
        chat_mask_user_user_id='$user_id' &&
        chat_mask_user_mask_id='$mask_id'
      LIMIT
        1
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL was: $sql");
    
    return (bool) $database->database_num_rows($resource);
  }
  
  
  
  function maskUserUpdate($user_id, $mask_id, $mask_user_status=NULL)
  {
    global $database;
    
    // Security
    $user_id = $database->database_real_escape_string($user_id);
    $mask_id = $database->database_real_escape_string($mask_id);
    $mask_user_status = $database->database_real_escape_string($mask_user_status);
    
    // Process arguments
    $time = time();
    
    // Run query
    $sql = "
      UPDATE
        se_chat_mask_users
      SET
        chat_mask_user_lastupdate=$time
    ";
    
    if( isset($mask_user_status) ) $sql .= ",
        chat_mask_user_status='$mask_user_status'
    ";
    
    $sql .= "
      WHERE
        chat_mask_user_user_id='$user_id' &&
        chat_mask_user_mask_id='$mask_id'
      LIMIT
        1
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL was: $sql");
    
    return (bool) $database->database_affected_rows($resource);
  }
  
  
  
  
  
  
  /* ----------------------------------------------------------------------- *\
  |                                                                           |
  | Message Methods                                                           |
  |                                                                           |
  \* ----------------------------------------------------------------------- */
  
  
  
  function messageSend($user_id, $mask_id, $message_content)
  {
    global $database;
    
    // Security
    $user_id = $database->database_real_escape_string($user_id);
    $mask_id = $database->database_real_escape_string($mask_id);
    $message_content = $database->database_real_escape_string($message_content);
    
    // Process arguments
    $message_content = censor($message_content);
    $time = time();
    
    // Verify user belongs to this mask
    if( !$this->maskUserValidate($user_id, $mask_id) )
      return array(
        'result' => FALSE,
        'error' => "User $user_id  does not belong to mask $mask_id"
      );
    
    // Run query
    $sql = "
      INSERT INTO
        se_chat_messages
      (
        chat_message_user_id,
        chat_message_mask_id,
        chat_message_content,
        chat_message_time
      )
      VALUES
      (
        '$user_id',
        '$mask_id',
        '$message_content',
        '$time'
      )
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL was: $sql");
    
    if( !$database->database_affected_rows($resource) )
      return array(
        'result' => FALSE,
        'error' => 'Could not insert message into database'
      );
    
    // Update this mask
    $message_id = $database->database_insert_id();
    
    $sql = "
      UPDATE
        se_chat_masks
      SET
        chat_mask_status='".SEIM_MASKSTATUS_ACTIVE."',
        chat_mask_lastupdate=$time
      WHERE
        chat_mask_id='$mask_id'
      LIMIT
        1
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL was: $sql");
    
    
    // Delete the first of more than 15 in the mask
    $sql = "
      SELECT
        COUNT(*) AS message_count
      FROM
        se_chat_messages
      WHERE
        chat_message_mask_id='$mask_id'
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL was: $sql");
    $result = $database->database_fetch_assoc($resource);
    
    if( $result['message_count']>15 )
    {
      $sql = "
        DELETE FROM
          se_chat_messages
        WHERE
          chat_message_mask_id='$mask_id'
        ORDER BY
          chat_message_id ASC
        LIMIT
          ".($result['message_count'] - 15)."
      ";
      
      $resource = $database->database_query($sql) or die($database->database_error()." SQL was: $sql");
      
      // Debug
      echo "deleted - ".$database->database_affected_rows($resource);
    }
    
    return array(
      'result' => TRUE,
      'messageID' => $message_id,
      'maskID' => $mask_id
    );
  }
  
  
  
  function systemMessageSend($user_id=NULL, $mask_id=NULL, $message_content)
  {
    if( is_array($mask_id) )
    {
      foreach( $mask_id as $single_mask_index=>$single_mask_id )
        $this->systemMessageSend($message_content, $single_mask_id);
      
      return;
    }
    
    global $database;
    
    // Security
    $user_id = $database->database_real_escape_string($user_id);
    $mask_id = $database->database_real_escape_string($mask_id);
    $message_content = $database->database_real_escape_string($message_content);
    
    // Process arguments
    if( !$mask_id ) $mask_id = '0';
    $time = time();
    
    // Run query
    $sql = "
      INSERT INTO
        se_chat_messages
      (
        chat_message_user_id,
        chat_message_mask_id,
        chat_message_content,
        chat_message_time
      )
      VALUES
      (
        '0',
        '$mask_id',
        '$message_content',
        '$time'
      )
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL was: $sql");
    
    if( !$database->database_affected_rows($resource) )
      return array( 'result' => FALSE );
    
    $message_id = $database->database_insert_id();
    
    return array(
      'result' => TRUE,
      'messageID' => $message_id
    );
  }
  
  
  
  
  /* ----------------------------------------------------------------------- *\
  |                                                                           |
  | Lookup Methods                                                            |
  |                                                                           |
  \* ----------------------------------------------------------------------- */
  
  
  
  function allUpdates($last_update=NULL)
  {
    global $database, $url;
    
    // Security
    $last_update = $database->database_real_escape_string($last_update);
    
    $updateMaskList     = array();
    $updateUserList     = array();
    $updateMaskUserList = array();
    $updateMessageList  = array();
    
    
    // Generate some safkofgdionmgdsgf
    
    
    
    
    // -------------------- GET ALL UPDATED MASKS --------------------
    if( !empty($this->chat_user_masks) )
    {
      $mask_list_string = join(',', $this->chat_user_masks);
      
      // $mask_list_string should already have been filtered for quit masks
      $sql = "
        SELECT
          chat_mask_id      AS maskID,
          chat_mask_title   AS maskTitle,
          chat_mask_type    AS maskType,
          chat_mask_status  AS maskStatus
        FROM
          se_chat_masks
        WHERE
          chat_mask_id IN($mask_list_string)
      ";
      
      if( !empty($last_update) ) $sql .= " &&
          chat_mask_lastupdate>=$last_update
      ";
      
      $resource = $database->database_query($sql) or die($database->database_error()." SQL was: $sql");
      
      while( $row=$database->database_fetch_assoc($resource) )
      {
        $updateMaskList[(int)$row['maskID']] = $row;
      }
    }
    
    
    
    
    // -------------------- GET ALL UPDATED MASK USERS --------------------
    if( !empty($this->chat_user_masks) )
    {
      $sql = "
        SELECT
          chat_mask_user_id       AS maskUserID,
          chat_mask_user_user_id  AS userID,
          chat_mask_user_mask_id  AS maskID,
          chat_mask_user_status   AS maskUserStatus
        FROM
          se_chat_mask_users
        WHERE
          chat_mask_user_mask_id IN($mask_list_string)
      ";
      
      if( !empty($last_update) ) $sql .= " &&
          chat_mask_user_lastupdate>=$last_update
      ";
      
      $resource = $database->database_query($sql) or die($database->database_error()." SQL was: $sql");
      
      while( $row=$database->database_fetch_assoc($resource) )
      {
        $updateMaskUserList[(int)$row['maskUserID']] = $row;
      }
    }
    
    
    
    
    
    // -------------------- GET ALL UPDATED USERS --------------------
    $sql = "
      SELECT
        se_users.user_id                            AS userID,
        se_users.user_username                      AS userName,
        se_users.user_fname                         AS userNameFirst,
        se_users.user_lname                         AS userNameLast,
        se_users.user_photo                         AS userPhoto,
        se_users.user_status                        AS userMessage,
        se_chat_users.chat_user_status              AS userStatus,
        se_friends.friend_status                    AS isFriendOfActiveUser
      FROM
        se_chat_users
      LEFT JOIN
        se_users
        ON se_users.user_id=se_chat_users.chat_user_id
      LEFT JOIN
        se_friends
        ON (
          se_friends.friend_status=1 &&
          se_friends.friend_user_id1='{$this->user_id}' &&
          se_friends.friend_user_id2=se_chat_users.chat_user_id
        )
      WHERE
    ";
    	 	 	
    if( !empty($last_update) ) $sql .= "
        se_chat_users.chat_user_lastupdate>=$last_update &&
        (
    ";
    
    $sql .= "
          /* SELF */
          se_chat_users.chat_user_id={$this->user_id} ||
          
          /* FRIENDS */
          se_friends.friend_status=1
    ";
    
    if( !empty($mask_list_string) ) $sql .= " ||
          /* MASKS */
          (
            SELECT
              TRUE
            FROM
              se_chat_mask_users
            WHERE
              chat_mask_user_mask_id IN($mask_list_string) &&
              chat_mask_user_user_id=se_chat_users.chat_user_id
          )
    ";
    
    if( !empty($last_update) ) $sql .= "
        )
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL was: $sql");
    
    while( $row=$database->database_fetch_assoc($resource) )
    {
      $row['userPath']      = $url->url_userdir($row['userID']);
      $row['isActiveUser']  = ( $row['userID']==$this->user_id );
      $row['isFriendOfActiveUser']  = ( $row['userID']!=$this->user_id && !empty($row['isFriendOfActiveUser']) );
      
      $updateUserList[(int)$row['userID']] = $row;
    }
    
    
    
    
    // -------------------- GET ALL MESSAGES IN MASKS AND SYSTEM MESSAGES --------------------
    if( !empty($this->chat_user_masks) )
    {
      $sql = "
        SELECT
          chat_message_id      AS messageID,
          chat_message_content AS messageContent,
          chat_message_time    AS messageTime,
          chat_message_user_id AS userID,
          chat_message_mask_id AS maskID
        FROM
          se_chat_messages
        WHERE
          chat_message_mask_id IN($mask_list_string)
      ";
      
      if( !empty($last_update) ) $sql .= " AND
          se_chat_messages.chat_message_time>=$last_update
      ";
      
      $sql .= "
        ORDER BY
          chat_message_id ASC
      ";
      
      $resource = $database->database_query($sql) or die($database->database_error()." SQL was: $sql");
      
      while( $row=$database->database_fetch_assoc($resource) )
      {
        $updateMessageList[(int)$row['messageID']] = $row;
      }
    }
    
    
    
    
    
    
    // Gen data structure
    $allUpdates = array();
    
    if( !empty($updateMaskList)     ) $allUpdates['masks']      =& $updateMaskList;
    if( !empty($updateUserList)     ) $allUpdates['users']      =& $updateUserList;
    if( !empty($updateMaskUserList) ) $allUpdates['maskusers']  =& $updateMaskUserList;
    if( !empty($updateMessageList)  ) $allUpdates['messages']   =& $updateMessageList;
    
    return $allUpdates;
  }
  
  
  
  
  
  
  
  
  /* ----------------------------------------------------------------------- *\
  |                                                                           |
  | Cleanup Methods                                                           |
  |                                                                           |
  \* ----------------------------------------------------------------------- */
  
  
  
  function cleanup()
  {
    global $database, $user;
    
    //if( $user->user_info['user_id']!=1 ) return;
    if( rand(1, 100)>20 ) return;
    
    $time = time();
    
    // Get inactive users
    $inactive_users = array();
    
    $sql = "
      SELECT
        chat_user_id
      FROM
        se_chat_users
      WHERE
        chat_user_session_lastupdate<".($time - 30)."
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error());
    while( $row=$database->database_fetch_assoc($resource) ) $inactive_users[] = $row['chat_user_id'];
    
    
    
    
    if( !empty($inactive_users) )
    {
      $inactive_user_list = join(',', $inactive_users);
      
      
      // Get inactive maskusers
      $possibly_inactive_masks = array();
      $inactive_maskusers = array();
      
      $sql = "
        SELECT
          chat_mask_user_id      AS id,
          chat_mask_user_mask_id AS mask_id,
          chat_mask_user_user_id AS user_id
        FROM
          se_chat_mask_users
        WHERE
          chat_mask_user_user_id IN($inactive_user_list)
      ";
      
      $resource = $database->database_query($sql) or die($database->database_error());
      while( $row=$database->database_fetch_assoc($resource) )
      {
        $inactive_maskusers[$row['id']] = $row;
        $possibly_inactive_masks[] = $row['mask_id'];
      }
      
      
      if( !empty($inactive_maskusers) )
      {
        $inactive_maskuser_list = join(',', array_keys($inactive_maskusers));
        
        // Delete from database
        $sql = "
          DELETE FROM
            se_chat_mask_users
          WHERE
            chat_mask_user_id IN($inactive_maskuser_list)
        ";
        
        $resource = $database->database_query($sql) or die($database->database_error());
      }
      
      
      // Delete from database
      $sql = "
        UPDATE
          se_chat_users
        SET
          chat_user_session_id='',
          chat_user_status='".SEIM_USERSTATUS_OFFLINE."',
          chat_user_lastupdate=$time
        WHERE
          chat_user_id IN($inactive_user_list)
      ";
      
      $resource = $database->database_query($sql) or die($database->database_error());
    }
    
    
    
    // Remove empty masks
    if( !empty($possibly_inactive_masks) )
    {
      $possibly_inactive_mask_list = join(',', array_keys($possibly_inactive_masks));
      
      $sql = "
        DELETE FROM
          se_chat_masks
        WHERE
          chat_mask_id IN($possibly_inactive_mask_list) &&
          (SELECT TRUE FROM se_chat_mask_users WHERE chat_mask_user_mask_id=se_chat_masks.chat_mask_id LIMIT 1)!=TRUE
      ";
      
      $resource = $database->database_query($sql) or die($database->database_error());
    }
    
    
    
    
    // Delete really inactive users
    $sql = "
      DELETE FROM
        se_chat_users
      WHERE
        chat_user_session_lastupdate<".($time - 1200)."
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error());
    
    
    
    
    
    // Delete messages that don't have a mask
    $sql = "
      DELETE FROM
        se_chat_messages
      WHERE
        chat_message_time<".($time - 1200)." &&
        (
          SELECT
            TRUE
          FROM
            se_chat_masks
          WHERE
            se_chat_masks.chat_mask_id=se_chat_messages.chat_message_mask_id
        )!=TRUE
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
  }
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  /* ----------------------------------------------------------------------- *\
  |                                                                           |
  | Super Low-Level Methods                                                   |
  |                                                                           |
  \* ----------------------------------------------------------------------- */
  
  function getSingleUserInfo($user_id)
  {
    global $database;
    $resource = $database->database_query("SELECT se_chat_users.*,se_users.user_username,se_users.user_photo FROM se_chat_users LEFT JOIN se_users ON se_users.user_id=se_chat_users.chat_user_id WHERE chat_user_id='$user_id' LIMIT 1") or die($database->database_error());
    $result = $database->database_fetch_assoc($resource);
    return ( !empty($result) ? $result : array() );
  }
  
  
  function getSingleUserMasks($user_id)
  {
    global $database;
    $resource = $database->database_query("SELECT chat_mask_user_mask_id FROM se_chat_mask_users WHERE chat_mask_user_user_id='{$user_id}' && chat_mask_user_status!=0") or die($database->database_error());
    $masks = array();
    while( $row=$database->database_fetch_assoc($resource) ) $masks[] = $row['chat_mask_user_mask_id'];
    return $masks;
  }
  
  
  function onlineUserCount()
  {
    global $database;
    $sql = "SELECT NULL FROM se_chat_users WHERE chat_user_status!=0";
    $resource = $database->database_query($sql) or die($database->database_error());
    $chat_user_count = $database->database_num_rows($resource) or die($database->database_error());
    return (int)$chat_user_count;
  }
  
  
  
  
  
  function estimateMaxUserLoad()
  {
    // Generate time stats
    $localtime = localtime(time(), TRUE);
    $localtime['tm_mon']++;
    $localtime['tm_year'] += 1900;
    
    $current_time_in_day = time() - mktime(0, 0, 0, $localtime['tm_mon'], $localtime['tm_mday'], $localtime['tm_year'], $localtime['tm_isdst']);
    
    // Get data from stats table
    $sql = "
      SELECT
        stat_date,
        stat_chat_requests,
        stat_chat_cpu_time
      FROM
        se_stats
      WHERE
        stat_chat_requests>1
      ORDER BY
        stat_date DESC
      LIMIT
        1
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error());
    $stat_data = $database->database_fetch_assoc($resource);
    
    if( empty($stat_data) ) return FALSE;
    
    
    // Begin calculations
    $cpu_usage = $stat_data['stat_chat_cpu_time'] / $current_time_in_day;
    $full_cpu_usage_query_time = $current_time_in_day / $stat_data['stat_chat_requests'];
    
    return array
    (
      'cpu_time' => $stat_data['stat_chat_cpu_time'],
      'cpu_usage' => $cpu_usage,
      'absolute_max_query_time' => $full_cpu_usage_query_time,
      'total_requests' => $stat_data['stat_chat_requests']
    );
  }
}