
/* $Id: InstantMessengerCore.js 6 2009-01-11 06:01:29Z john $ */


var InstantMessengerCore = new Class({

  /* ----------------------------------------------------------------------- *\
  | Class                                                                     |
  \* ----------------------------------------------------------------------- */
  
	Implements: [Events, Modules, Options],
  
  
  
  
  /* ----------------------------------------------------------------------- *\
  | Properties                                                                |
  \* ----------------------------------------------------------------------- */
  
  constants: {
    CONNECTION_STATE: {
      UNINITIALIZED: -1,
      DISCONNECTED: 0,
      CONNECTED: 1
    },
    MASK_TYPE: {
      CHAT: 1,
      IM: 2
    },
    MASK_STATUS: {
      INACTIVE: 0,
      ACTIVE: 1
    },
    USER_STATUS: {
      OFFLINE: 0,
      ONLINE: 1,
      AWAY: 2,
      BUSY: 3
    }
  },
  
  connectionState : -1,
  
  options: {
    // Delay after playing a sound before another will play.
    audioPostDelay: 100,
    // Max Number of messages that shown in a conversation
    conversationMessageLimit: 15,
    // Deprecated (more precisely not re-implemented)
    debug: false,
    debugLogLength: 20,
    debugLogVerbosity: 4,
    debugPrependDomain: true,
    // Not yet implemented. Time of no input before status will be set to away
    idleAwayTime: 30000,
    // Path to different image folders
    imagePath: './images',
    imageSmileyPath: './images/smilies_chat',
    // List of smilies. It goes replacement->image
    smilies: {
      '(H)'   : 'cool',
      ':\'('  : 'cry',
      ':$'    : 'embarassed',
      '(F)'   : 'foot-in-mouth',
      ':('    : 'frown',
      '(A)'   : 'innocent',
      '(K)'   : 'kiss',
      ':D'    : 'laughing',
      ':-#'   : 'sealed',
      ':)'    : 'smile',
      ':O'    : 'surprised',
      ':P'    : 'tongue',
      ':-T'   : 'undecided',
      ';)'    : 'wink',
      ':@'    : 'yell'
    },
    // Sets the name that is display for each user. (username/full/short, default: full)
    userDisplayNameModeDefault: 'full',
    userDisplayNameForceCapitalize: true,
    
    // DO NOT CHANGE THESE UNLESS YOU KNOW WHAT YOU ARE DOING
    // Url to post data to.
    ajaxURL: 'chat_ajax.php',
    // Time offset between server and client. Calculated in header_chat.tpl.
    timeDelta: 0,
    // Loaded from settings in admin panel.
    updateDelay: 2000
  },
  
  
  
  
  
  
  /* ----------------------------------------------------------------------- *\
  | Methods - Class                                                           |
  \* ----------------------------------------------------------------------- */
  
  initialize: function(options)
  {
    var bind = this;
    this.setOptions(options);
    
    // Make smilies object a hash so we can iterate over it easily
    this.options.smilies = new Hash(this.options.smilies);
    
    // Load session data and user config
    this.sessionData = new Hash.Cookie('seIM_sessionData', {duration: false});
    this.userConfig  = new Hash.Cookie('seIM_userConfig',  {duration: 365});
    
    // Registry
    this.masks = new Hash;
    this.maskusers = new Hash;
    this.messages = new Hash;
    this.users = new Hash;
    
    // Update
    var updateFunction = function() { this.fireEvent('updateInterval'); }
    this.updateInterval = updateFunction.periodical(this.options.updateDelay, this);
    
    this.addEvent('updateInterval', function()
    {
      if( !bind.connectionState ) return;
      bind.Request({ task : 'update' });
    });
    
    // Events
    this.addEvent('maskLeave', function()
    {
      bind.Request({
        'task' : 'maskLeave',
        'maskID' : maskObject.maskID
      });
    });
    
  },
  
  
  
  onBoot: function()
  {
    var userChosenStatus = this.sessionData.get('userChosenStatus');
    
    // Login
    if( !$type(userChosenStatus) || userChosenStatus==this.constants.CONNECTION_STATE.CONNECTED )
    {
      this.Request({ task : 'login' });
    }
    
    // Stay offline
    else
    {
      this.setConnectionState(this.constants.CONNECTION_STATE.DISCONNECTED);
    }
  },
  
  
  
  onShutdown: function()
  {
    this.Request({ task : 'logout' });
  },
  
  
  
  
  
  
  /* ----------------------------------------------------------------------- *\
  | Methods - General                                                         |
  \* ----------------------------------------------------------------------- */
  
  setConnectionState: function(connectionState)
  {
    if( this.connectionState==connectionState ) return;
    var bind = this;
    this.connectionState = connectionState;
    this.fireEvent('connectionStateChanged', {
      'connectionState': connectionState
    });
  },
  
  
  
  
  
  
  
  /* ----------------------------------------------------------------------- *\
  | Methods - IO                                                              |
  \* ----------------------------------------------------------------------- */
  
  Request: function( arguments, callback )
  {
    if( $type(arguments)!="object" && $type(arguments)!="hash" ) arguments = new Hash;
    var me = this;
    var requestObject = new Request.JSON({
      url : this.options.ajaxURL + '?nocache=' + Math.floor(Math.random() * 89999 + 10000).toString(),
      method : 'post',
      rawdata : arguments,
      data : arguments,
      urlEncoded : true,
      onComplete : function(responseObject, responseText)
      {
        me.Process(requestObject, responseObject, responseText);
        if( $type(callback)=="function" ) callback(requestObject, responseObject);
      }
    });
    requestObject.rawdata = arguments;
    requestObject.send();
  },
  
  
  
  
  
  Process: function( requestObject, responseObject, responseText )
  {
    var me = this;
    
    if( $type(responseObject)!="object" || !responseObject.task ) return;
    
    // Task
    switch( responseObject.task )
    {
      case 'login':
        // Do not login if already connected (this can happen if a request is sent just before connecting)
        if( this.connectionState==this.constants.CONNECTION_STATE.CONNECTED ) return;
        
        var result = responseObject.response.result;
        var loginStatus = responseObject.response.loginStatus;
        var sessionID = responseObject.response.sessionID;
        var allowedHtmlTags = responseObject.response.allowedHtmlTags;
        
        if( loginStatus && sessionID )
        {
          this.setConnectionState(this.constants.CONNECTION_STATE.CONNECTED);
        }
        else
        {
          this.setConnectionState(this.constants.CONNECTION_STATE.DISCONNECTED);
        }
        this.fireEvent('loginRequestComplete', {
          'loginStatus' : loginStatus,
          'sessionID' : sessionID,
          'allowedHtmlTags' : allowedHtmlTags
        });
      break;
      
      case 'update':
        // Do not update if not connected (this can happen if a request is sent just before disconnecting)
        if( this.connectionState!=this.constants.CONNECTION_STATE.CONNECTED ) return;
        
        var result = responseObject.response.result;
        var loginStatus = responseObject.response.loginStatus;
        
        if( loginStatus )
        {
          this.setConnectionState(this.constants.CONNECTION_STATE.CONNECTED);
        }
        else
        {
          this.setConnectionState(this.constants.CONNECTION_STATE.DISCONNECTED);
        }
        this.fireEvent('updateRequestComplete', {
          'loginStatus' : loginStatus
        });
      break;
      
      case 'userLogout':
        // Do not logout if not connected (this can happen if a request is sent just before disconnecting)
        if( this.connectionState!=this.constants.CONNECTION_STATE.CONNECTED ) return;
        
        var result = responseObject.response.result;
        
        if( result )
          this.setConnectionState(this.constants.CONNECTION_STATE.DISCONNECTED);
        else
          this.setConnectionState(this.constants.CONNECTION_STATE.CONNECTED);
        
      break;
      
      case 'maskCreate':
        var maskID, conversationIndex, userID;
        try{ maskID = responseObject.response.maskID; } catch( e ) { messageID = undefined; }
        try{ conversationIndex = requestObject.rawdata.conversation_index; } catch( e ) { conversation_index = undefined; }
        try{ userID = requestObject.rawdata.user_id2; } catch( e ) { userID = undefined; }
        this.fireEvent('maskCreateRequestComplete', {
          'maskID' : maskID,
          'conversationIndex' : conversationIndex,
          'userID' : userID
        });
      break;
      
      case 'messageSend':
        var messageID, maskID, conversationIndex, messageIndex;
        try{ messageID = responseObject.response.messageID; } catch( e ) { messageID = undefined; }
        try{ maskID = responseObject.response.maskID; } catch( e ) { maskID = undefined; }
        try{ messageIndex = requestObject.rawdata.message_index; } catch( e ) { messageIndex = undefined; }
        try{ conversationIndex = requestObject.rawdata.conversation_index; } catch( e ) { conversationIndex = undefined; }
        this.fireEvent('messageSendRequestComplete', {
          'messageID' : messageID,
          'maskID' : maskID,
          'messageIndex' : messageIndex,
          'conversationIndex' : conversationIndex
        });
      break;
    }
    
    
    // General
    if( responseObject.response.users )
    {
      var userDataContainer = $H(responseObject.response.users);
      userDataContainer.each(function(userDataObject, userID)
      {
        // Parse
        userDataObject.userID = parseInt(userDataObject.userID);
        userDataObject.userStatus = parseInt(userDataObject.userStatus);
        
        // Retrieve
        var userRegistryObject = me.users.get(userID);
        var doDestroy = ( (!userRegistryObject || userRegistryObject.userStatus ) && !userDataObject.userStatus );
        
        // Create
        if( !userRegistryObject )
        {
          userRegistryObject = new Hash(userDataObject);
          userRegistryObject.masks = new Hash;
          me.users.set(userID, userRegistryObject);
          
          // Active user
          if( userDataObject.isActiveUser )
            me.activeUser = userRegistryObject;
          
          me.fireEvent('userCreation', {
            data: userDataObject,
            object: userRegistryObject
          });
        }
        
        // Update P1
        else
        {
          userRegistryObject.extend(userDataObject);
        }
        
        // Update P2
        if( userRegistryObject && !doDestroy )
        {
          //alert('test1 ' + doDestroy);
          me.fireEvent('userUpdate', {
            data: userDataObject,
            object: userRegistryObject
          });
        }
        
        // Destruction
        else if( userRegistryObject && doDestroy )
        {
          //alert('test2 ' + doDestroy + ' ' + userDataObject.userName);
          me.fireEvent('userDestruction', {
            data: userDataObject,
            object: userRegistryObject
          });
        }
        
      });
    }
    
    if( responseObject.response.masks )
    {
      var maskDataContainer = $H(responseObject.response.masks);
      maskDataContainer.each(function(maskDataObject, maskID)
      {
        // Parse
        maskDataObject.maskID = parseInt(maskDataObject.maskID);
        maskDataObject.maskStatus = parseInt(maskDataObject.maskStatus);
        
        // Retrieve
        var maskRegistryObject = me.masks.get(maskID);
        
        // Create
        if( !maskRegistryObject && maskDataObject.maskStatus )
        {
          maskRegistryObject = new Hash(maskDataObject);
          maskRegistryObject.maskusers = new Hash;
          maskRegistryObject.messages = new Hash;
          maskRegistryObject.users = new Hash;
          me.masks.set(maskID, maskRegistryObject);
          me.fireEvent('maskCreation', {
            data: maskDataObject,
            object: maskRegistryObject
          });
        }
        // Update
        else if( maskRegistryObject && maskDataObject.maskStatus )
        {
          me.fireEvent('maskUpdate', {
            data: maskDataObject,
            object: maskRegistryObject
          });
          maskRegistryObject.extend(maskDataObject);
        }
        // Destroy
        else if( maskRegistryObject && !maskDataObject.maskStatus )
        {
          maskRegistryObject.extend(maskDataObject);
          me.fireEvent('maskDestruction', {
            data: maskDataObject,
            object: maskRegistryObject
          });
          /* NEW FUNCTIONALITY - DO NOT ERASE REGISTRY OBJECT
          maskRegistryObject.empty();
          me.masks.erase(maskID);
          */
        }
      });
    }
    
    if( responseObject.response.maskusers )
    {
      var maskuserDataContainer = $H(responseObject.response.maskusers);
      maskuserDataContainer.each(function(maskuserDataObject, maskuserID)
      {
        // Parse
        maskuserDataObject.maskID = parseInt(maskuserDataObject.maskID);
        maskuserDataObject.userID = parseInt(maskuserDataObject.userID);
        maskuserDataObject.maskUserID = parseInt(maskuserDataObject.maskUserID);
        maskuserDataObject.maskUserStatus = parseInt(maskuserDataObject.maskUserStatus);
        
        // Retrieve
        var maskuserRegistryObject = me.maskusers.get(maskuserID);
        var maskRegistryObject = me.masks.get(maskuserDataObject.maskID);
        var userRegistryObject = me.users.get(maskuserDataObject.userID);
        
        // Create
        if( !maskuserRegistryObject && maskuserDataObject.maskUserStatus )
        {
          maskuserRegistryObject = new Hash(maskuserDataObject);
          if( maskRegistryObject ) {
            maskuserRegistryObject.mask = maskRegistryObject;
            maskRegistryObject.maskusers.set(userID, maskuserRegistryObject);
          }
          if( userRegistryObject ) {
            maskuserRegistryObject.user = userRegistryObject;
          }
          if( maskRegistryObject && userRegistryObject ) {
            maskRegistryObject.users.set(userRegistryObject.userID, userRegistryObject);
            userRegistryObject.masks.set(maskRegistryObject.maskID, maskRegistryObject);
          }
          me.maskusers.set(maskuserID, maskuserRegistryObject);
          me.fireEvent('maskUserAdded', {
            data: maskuserDataObject,
            object: maskuserRegistryObject,
            mask: maskRegistryObject,
            user: userRegistryObject
          });
        }
        // Update
        else if( maskuserRegistryObject && maskuserDataObject.maskUserStatus )
        {
          me.fireEvent('maskUserUpdated', {
            data: maskuserDataObject,
            object: maskuserRegistryObject,
            mask: maskRegistryObject,
            user: userRegistryObject
          });
          maskuserRegistryObject.extend(maskuserDataObject);
          if( maskRegistryObject ) {
            maskuserRegistryObject.mask = maskRegistryObject;
            maskRegistryObject.maskusers.set(maskuserID, maskuserRegistryObject);
          }
          if( userRegistryObject ) {
            maskuserRegistryObject.user = userRegistryObject;
          }
          if( maskRegistryObject && userRegistryObject ) {
            maskRegistryObject.users.set(userRegistryObject.userID, userRegistryObject);
            userRegistryObject.masks.set(maskRegistryObject.maskID, maskRegistryObject);
          }
        }
        // Destroy
        else if( maskuserRegistryObject && !maskuserDataObject.maskUserStatus )
        {
          maskuserRegistryObject.extend(maskuserDataObject);
          me.fireEvent('maskUserRemoved', {
            data: maskuserDataObject,
            object: maskuserRegistryObject,
            mask: maskRegistryObject,
            user: userRegistryObject
          });
          if( maskRegistryObject ) {
            maskRegistryObject.maskusers.erase(maskuserID);
          }
          if( maskRegistryObject && userRegistryObject ) {
            maskRegistryObject.users.erase(userRegistryObject.userID);
            userRegistryObject.masks.erase(maskRegistryObject.maskID);
          }
          maskuserRegistryObject.empty();
          me.maskusers.erase(maskuserID);
        }
      });
    }
    
    if( responseObject.response.messages )
    {
      var messageDataContainer = $H(responseObject.response.messages);
      messageDataContainer.each(function(messageDataObject, messageID)
      {
        // Parse
        messageDataObject.userID = parseInt(messageDataObject.userID);
        messageDataObject.maskID = parseInt(messageDataObject.maskID);
        messageDataObject.messageID = parseInt(messageDataObject.messageID);
        messageDataObject.messageTime = parseInt(messageDataObject.messageTime);
        
        // Retrieve
        var messageRegistryObject = me.messages.get(messageID);
        
        // Create
        if( !messageRegistryObject )
        {
          // lastRecvMessageTime
          var isNew = false;
          var lastRecvMessageTime = parseInt(me.sessionData.get('lastRecvMessageTime')) || 0;
          var currentMessageTime = messageDataObject.messageTime || 0;
          if( messageDataObject.messageTime && messageDataObject.messageTime>lastRecvMessageTime ) {
            me.sessionData.set('lastRecvMessageTime', currentMessageTime);
            isNew = true;
          }
          
          messageRegistryObject = new Hash(messageDataObject);
          me.messages.set(messageID, messageRegistryObject);
          me.fireEvent('messageCreation', {
            data: messageDataObject,
            object: messageRegistryObject,
            'isNew' : isNew
          });
        }
        // Update
        else if( messageRegistryObject )
        {
          me.fireEvent('messageUpdate', {
            data: messageDataObject,
            object: messageRegistryObject
          });
          messageRegistryObject.extend(messageDataObject);
        }
      });
    }
  },
  
  
  
  
  
  
  
  /* ----------------------------------------------------------------------- *\
  | Methods - Utility                                                         |
  \* ----------------------------------------------------------------------- */
  
  htmlEncode: function()
  {
    if ( typeof( text ) != "string" ) text = text.toString() ;
    text = text.replace(/&/g, "&amp;") ;
    text = text.replace(/"/g, "&quot;") ;
    text = text.replace(/</g, "&lt;") ;
    text = text.replace(/>/g, "&gt;") ;
    text = text.replace(/'/g, "&#39;") ;
    return text;
  },
  
  randomInteger: function( min, max )
  {
    if( $type(min)!="number" || $type(max)!="number" ) return Math.round(Math.random());
    if( min>max )
    {
      var tempMin = min;
      min = max;
      max = tempMin;
    }
    return Math.round((Math.random() * (max - min)) + min);
  },
  
  
  retrieve: function( varName )
  {
    try
    {
      var evalCode = 'try{ window.tempObjectStore = ' + varName + '; } catch( e ) { window.tempObjectStore = undefined; }';
      window.execScript ? window.execScript(evalCode) : eval(evalCode);
      return window.tempObjectStore;
    }
    catch( e )
    {
      return undefined;
    }
  },
  
  
  
  createTimestamp: function( timestamp, fromPHP )
  {
    if( !timestamp )
    {
      timestamp = Math.round((new Date()).getTime() / 1000);
      fromPHP = false;
    }
    timestamp = parseInt(timestamp);
    
    var effectiveTimestamp = (fromPHP ? timestamp + this.options.timeDelta : timestamp) * 1000;
    var today = new Date(effectiveTimestamp);
    
    var ampm;
    var h=today.getHours();
    var m=today.getMinutes();
    var s=today.getSeconds();
    
    if( h>12 )
    {
      h = h - 12;
      ampm = 'pm';
    }
    else
    {
      ampm = 'am';
    }
    
    if( h<10 ) h = '0' + h.toString();
    if( m<10 ) m = '0' + m.toString();
    if( s<10 ) s = '0' + s.toString();
    
    var timestamp = h + ":" + m + ":" + s + " " + ampm;
    
    return timestamp;
  },
  
  
  
  userDisplayName: function( userObject, forceMode )
  {
    var mode = ( $type(forceMode) ? forceMode : this.options.userDisplayNameModeDefault );
    
    if( mode=="full" )
    {
      if( !userObject.userNameFirst.trim() && !userObject.userNameLast.trim() )
        mode = "username";
      else if( userObject.userNameFirst.trim() && !userObject.userNameLast.trim() )
        mode = "short";
      else if( userObject.userNameFirst.trim() && !userObject.userNameLast.trim() )
        mode = "japanese";
    }
    
    else if( mode=="short" )
    {
      if( !userObject.userNameFirst.trim() && userObject.userNameLast.trim() )
        mode = "japanese";
      else if( !userObject.userNameFirst.trim() && !userObject.userNameLast.trim() )
        mode = "username";
    }
    
    
    // Generate name
    var displayName;
    switch( mode )
    {
      default: mode = 'username';
      case 'username':  displayName = userObject.userName.trim();                                               break;
      case 'full':      displayName = userObject.userNameFirst.trim() + ' ' + userObject.userNameLast.trim();  break;
      case 'short':     displayName = userObject.userNameFirst.trim();                                          break;
      case 'japanese':  displayName = userObject.userNameLast.trim() + '-san';                                  break;
      case 'userid':    displayName = userObject.userID;                                                        break;
    }
    
    // Force capitalize
    if( this.options.userDisplayNameForceCapitalize && mode!='username' )
      displayName = displayName.capitalize();
    
    return displayName;
  },
  
  
  
  userStatusString: function( userStatus, userMessage )
  {
    var userStatusString;
    switch( userStatus )
    {
      default:
      case this.constants.USER_STATUS.OFFLINE: userStatusString = SELanguage.Translate(3510027); break;
      case this.constants.USER_STATUS.ONLINE:  userStatusString = SELanguage.Translate(3510028); break;
      case this.constants.USER_STATUS.AWAY:    userStatusString = SELanguage.Translate(3510029); break;
      case this.constants.USER_STATUS.BUSY:    userStatusString = SELanguage.Translate(3510030); break;
    }
    return userStatusString;
  },
  
  
  
  userStatusIcon: function( userStatus )
  {
    var userStatusIcon = this.options.imagePath + '/status_im/';
    switch( userStatus )
    {
      default:
      case this.constants.USER_STATUS.OFFLINE: userStatusIcon += 'user_offline16.gif';  break;
      case this.constants.USER_STATUS.ONLINE:  userStatusIcon += 'user_online16.gif';   break;
      case this.constants.USER_STATUS.AWAY:    userStatusIcon += 'user_away16.gif';     break;
      case this.constants.USER_STATUS.BUSY:    userStatusIcon += 'user_away16.gif';     break;
    }
    return userStatusIcon;
  },
  
  
  
  replaceSmilies: function( messageText )
  {
    if( $type(messageText)!="string" ) messageText = messageText.toString();
    //if( $type(this.options.smilies)!="array" ) return messageText;
    
    var bind = this;
    var imageSmileyPath = this.options.imageSmileyPath;
    this.options.smilies.each(function(smileyName, smilieyReplacement)
    {
      var smilieyReplacementEscaped = smilieyReplacement.escapeForRegex();
      var smilieyRegex = new RegExp(smilieyReplacementEscaped, "g");
      var smileyCodeReplaced = "<img src='" + imageSmileyPath + '/' + smileyName + ".png' alt='" + smilieyReplacement + "' border='0' class='icon'>";
      
      //if( bind.activeUser.userID==1 ) alert(smilieyReplacementEscaped + ' ' + smilieyRegex + ' ' + smileyCodeReplaced);
      
      messageText = messageText.replace(smilieyRegex, smileyCodeReplaced);
    });
    
    return messageText;
  },
  
  
  replaceUrls: function (input)
  {
    input.replace(/(ftp|http|https|file):\/\/[\S]+(\b|$)/gim, '<a href="$&" class="my_link" target="_blank">$&</a>');
    input.replace(/([^\/])(www[\S]+(\b|$))/gim, '$1<a href="http://$2" class="my_link" target="_blank">$2</a>');
    return input;
  } 
  
  
});