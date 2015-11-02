
/* $Id: InstantMessengerConversations.js 6 2009-01-11 06:01:29Z john $ */


var InstantMessengerConversations = new Class({

  /* ----------------------------------------------------------------------- *\
  | Class                                                                     |
  \* ----------------------------------------------------------------------- */
  
	Implements: [],
  
  
  
  
  
  /* ----------------------------------------------------------------------- *\
  | Methods - Class                                                           |
  \* ----------------------------------------------------------------------- */
  
  initialize: function()
  {
    this.conversationIndex    = 1;
    
    this.conversationsByIndex = new Hash;
    this.conversationsByUser  = new Hash;
    this.conversationsByMask  = new Hash;
    
    this.messageIndex         = 1;
    this.messagesByIndex      = new Hash;
    this.messagesByMessage    = new Hash;
    
    this.lastUserUpdateId = false;
  },
  
  
  
  onModuleRegister: function()
  {
    var bind = this;
    
    // Events - GUI
    this.core.addEvent('openConversation', function(params)
    {
      var conversationObject = bind.scrapeConversation(params.userID, params.maskID, params.conversationIndex);
      bind.core.modules.gui.menuToggle('conversation_' + conversationObject.conversationIndex, true);
    });
    
    // Events - Core Processor - Request
    this.core.addEvent('maskCreateRequestComplete', function(params)
    {
      var conversationObject = bind.scrapeConversation(params.userID, params.maskID, params.conversationIndex);
      bind.modifyConversationProfile(params.conversationIndex);
    });
    
    this.core.addEvent('messageSendRequestComplete', function(params)
    {
      var conversationObject = bind.scrapeConversation(undefined, params.maskID, params.conversationIndex);
      var messageObject = bind.scrapeMessage(params.messageID, params.messageIndex);
    });
    
    // Events - Core Processor - User
    this.core.addEvent('userUpdate', function(params)
    {
      var maskID, conversationIndex;
      bind.conversationsByIndex.each(function(conversationObject)
      {
        if( conversationObject.userID!=params.object.userID ) return;
        maskID = conversationObject.maskID;
        conversationIndex = conversationObject.conversationIndex;
      });
      if( conversationIndex && bind.lastUserUpdateId!=conversationIndex+'_'+params.data.userStatus )
      {
        bind.modifyConversationProfile(conversationIndex);
        
        bind.lastUserUpdateId = conversationIndex+'_'+params.data.userStatus;
        
        // System message
        var userDisplayName = bind.core.userDisplayName(params.data);
        var systemMessage;
        if( params.data.userStatus==bind.core.constants.USER_STATUS.ONLINE )
          systemMessage = SELanguage.TranslateFormatted(3510036, [userDisplayName])
        else if( params.data.userStatus==bind.core.constants.USER_STATUS.AWAY )
          systemMessage = SELanguage.TranslateFormatted(3510037, [userDisplayName])
        else if( params.data.userStatus==bind.core.constants.USER_STATUS.AWAY )
          systemMessage = SELanguage.TranslateFormatted(3510037, [userDisplayName])
        
        if( systemMessage )
        {
          var messageObject = bind.scrapeMessage();
          bind.messageAdd({
            'messageIndex' : messageObject.messageIndex,
            'conversationIndex' : conversationIndex,
            'messageContent' : systemMessage,
            'isError' : false
          });
        }
      }
    });
    
    this.core.addEvent('userDestruction', function(params)
    {
      var maskID, conversationIndex;
      bind.conversationsByIndex.each(function(conversationObject)
      {
        if( conversationObject.userID!=params.data.userID ) return;
        maskID = conversationObject.maskID;
        conversationIndex = conversationObject.conversationIndex;
      });
      if( conversationIndex )
      {
        bind.modifyConversationProfile(conversationIndex);
        
        // System message
        var messageObject = bind.scrapeMessage();
        var userDisplayName = bind.core.userDisplayName(params.data);
        bind.messageAdd({
          'messageIndex' : messageObject.messageIndex,
          'conversationIndex' : conversationIndex,
          'messageContent' : SELanguage.TranslateFormatted(3510023, [userDisplayName]),
          'isError' : false
        });
      }
    });
    
    // Events - Core Processor - Masks
    this.core.addEvent('maskCreation', function(params)
    {
      //var conversationObject = bind.scrapeConversation(undefined, params.object.maskID, undefined);
    });
    
    this.core.addEvent('maskUserAdded', function(params)
    {
      if( params.object.userID==bind.core.activeUser.userID ) return;
      var conversationObject = bind.scrapeConversation(params.object.userID, params.object.maskID, undefined);
    });
    
    this.core.addEvent('maskUserRemoved', function(params)
    {
      if( params.object.userID==bind.core.activeUser.userID ) return;
      var conversationObjectClone = bind.unscrapeConversation(params.object.userID, params.object.maskID, undefined);
    });
    
    this.core.addEvent('maskDestruction', function(params)
    {
      var conversationObjectClone = bind.unscrapeConversation(undefined, params.object.maskID, undefined);
    });
    
    // Events - Core Processor - Messages
    this.core.addEvent('messageCreation', function(params)
    {
      bind.messageAdd(params.object);
      
      // Flash and sound effects
      var nonSelfUserID = ( params.object.userID!=bind.core.activeUser.userID ? params.object.userID : undefined );
      var conversationObject = bind.scrapeConversation(nonSelfUserID, params.object.maskID);
      if( params.isNew && nonSelfUserID )
      {
        var userObject = bind.core.users.get(nonSelfUserID);
        
        bind.core.modules.gui.playAudio();
        if( bind.core.modules.gui.menus.get('conversation_' + conversationObject.conversationIndex).hasClass('seIMHide') )
          $('seIM_conversation_trayItem_' + conversationObject.conversationIndex).flash({'backgroundDefault' : '#F0F0F0'});
        
        // Browser title effect
        if( !bind.documentTitleDefault )
          bind.documentTitleDefault = document.title;
        
        var username = ( userObject ? bind.core.userDisplayName(userObject) : '[unknown]' );
        
        document.title = SELanguage.TranslateFormatted(3510035, [username]);
        (function() { document.title = bind.documentTitleDefault; }).delay(20000);
      }
    });
    
    // Events - GUI
    this.core.addEvent('menuToggle', function(menuName)
    {
      var menuElement = bind.core.modules.gui.menus.get(menuName);
      var bodyListWrapper = menuElement.getElement('.seIM_trayMenu_bodyListWrapper');
      if( menuElement.hasClass('seIMHide') ) return;
      
      var textInputField = menuElement.getElement('.seIM_conversation_trayMenu_textInput');
      if( bodyListWrapper )
        bodyListWrapper.scrollTo(0, bodyListWrapper.getScrollSize().y);
      if( textInputField )
        textInputField.focus();
    });
  },
  
  
  
  
  onBoot: function()
  {
    // TODO
  },
  
  
  
  
  
  
  /* ----------------------------------------------------------------------- *\
  | Methods - Conversation                                                    |
  \* ----------------------------------------------------------------------- */
  
  
  scrapeConversation: function(userID, maskID, conversationIndex, noCreate)
  {
    
    // Special
    if( !userID && maskID && this.core.sessionData.get('conversationPrimaryTarget_' + maskID) )
      userID = this.core.sessionData.get('conversationPrimaryTarget_' + maskID);
    
    // Get conversationObject
    var conversationObject;
    if( conversationIndex && this.conversationsByIndex.get(conversationIndex) )
      conversationObject = this.conversationsByIndex.get(conversationIndex);
    else if( userID && this.conversationsByUser.get(userID) )
      conversationObject = this.conversationsByUser.get(userID);
    else if( maskID && this.conversationsByMask.get(maskID) )
      conversationObject = this.conversationsByMask.get(maskID);
    
    // Scrape info
    if( conversationObject )
    {
      if( !userID && conversationObject.userID )
        userID = conversationObject.userID;
      if( !maskID && conversationObject.maskID )
        maskID = conversationObject.maskID;
      if( !conversationIndex && conversationObject.conversationIndex )
        conversationIndex = conversationObject.conversationIndex;
    }
    
    // Create Part 1
    if( !noCreate && !conversationObject )
    {
      conversationObject = new Hash;
      conversationIndex = this.conversationIndex++;
    }
    
    // Update/Create Part 2
    if( userID==this.core.activeUser.userID ) userID = undefined;
    
    if( conversationObject )
    {
      // Set info on object
      if( conversationIndex && !conversationObject.get('conversationIndex') )
        conversationObject.set('conversationIndex', conversationIndex);
      if( userID && !conversationObject.get('userID') )
        conversationObject.set('userID', userID);
      if( maskID && !conversationObject.get('maskID') )
        conversationObject.set('maskID', maskID);
      
      // Special
      if( userID && maskID && !this.core.sessionData.get('conversationPrimaryTarget_' + maskID) )
        this.core.sessionData.set('conversationPrimaryTarget_' + maskID, userID);
      
      // Indexing
      if( !this.conversationsByIndex.get(conversationIndex) )
        this.conversationsByIndex.set(conversationIndex, conversationObject);
      if( !this.conversationsByUser.get(userID) )
        this.conversationsByUser.set(userID, conversationObject);
      if( !this.conversationsByMask.get(maskID) )
        this.conversationsByMask.set(maskID, conversationObject);
    }
    
    // Create Part 3
    if( !noCreate && conversationObject )
    {
      if( !this.core.modules.gui.items.get('conversation_' + conversationIndex) );
        this.spawnConversation(conversationIndex);
        
      this.modifyConversationProfile(conversationIndex);
    }
    
    return conversationObject;
  },
  
  
  
  
  unscrapeConversation: function(userID, maskID, conversationIndex)
  {
    var conversationObject = this.scrapeConversation(userID, maskID, conversationIndex, true);
    var clone = $H(conversationObject);
    
    // Destroy
    if( conversationObject )
    {
      // Special
      if( userID && maskID && this.core.sessionData.get('conversationPrimaryTarget_' + maskID) )
        this.core.sessionData.erase('conversationPrimaryTarget_' + maskID, userID);
      
      // Unlink
      if( conversationIndex ) this.conversationsByIndex.erase(conversationIndex);
      if( userID ) this.conversationsByUser.erase(userID);
      if( maskID ) this.conversationsByMask.erase(maskID);
      delete conversationObject;
    }
    
    return clone;
  },
  
  
  
  
  spawnConversation: function(conversationIndex)
  {
    var bind = this;
    
    if( !$('seIM_conversation_trayItem_' + conversationIndex) )
    {
      // Limit conversations
      if( $('seIM_tray').getElement('.seIM_traySpacer').getSize().x<175 )
      {
        alert(SELanguage.Translate(3510038));
        return false;
      }
      
      
      // Load Clone Assign
      var conversationTrayItemTemplate = $('seIM_conversation_trayItem_template').getElement('.seIM_conversation_trayItem').clone();
      conversationTrayItemTemplate.setProperty('id', 'seIM_conversation_trayItem_' + conversationIndex);
      
      // Attach
      conversationTrayItemTemplate.getElement('.seIM_trayItem_menuActivator').addEvent('click', function()
      {
        bind.core.modules.gui.menuToggle('conversation_' + conversationIndex);
      });
      
      // Inject Show Register
      conversationTrayItemTemplate.inject($('seIM_tray').getElement('.seIM_traySpacer'), 'after');
      //this.core.modules.gui.items.set('conversation_' + conversationIndex, conversationTrayItemTemplate);
      this.core.modules.gui.registerItem('conversation_' + conversationIndex, conversationTrayItemTemplate);
    }
    
    if( !$('seIM_conversation_trayMenu_' + conversationIndex) )
    {
      // Load Clone Assign
      var conversationTrayMenuTemplate = $('seIM_conversation_trayMenu_template').getElement('.seIM_conversation_trayMenu').clone();
      conversationTrayMenuTemplate.setProperty('id', 'seIM_conversation_trayMenu_' + conversationIndex);
      
      // Attach
      conversationTrayMenuTemplate.getElement('.seIM_trayItem_menuDeactivator').addEvent('click', function()
      {
        bind.core.modules.gui.menuToggle('conversation_' + conversationIndex, false);
      });
      
      conversationTrayMenuTemplate.getElement('.seIM_trayItem_menuDestroyer').addEvent('click', function()
      {
        bind.core.modules.gui.menuToggle('conversation_' + conversationIndex, false);
        // Remove data structures
        //alert(conversationIndex);
        //var conversationObject = bind.scrapeConversation(undefined, undefined, conversationIndex);
        //alert(conversationIndex && conversationObject.toQueryString());
        
        var conversationObjectClone = bind.unscrapeConversation(undefined, undefined, conversationIndex);
        if( conversationObjectClone && conversationObjectClone.maskID )
        {
          bind.core.Request({
            'task' : 'maskUserLeave',
            'mask_id' : conversationObjectClone.maskID,
            'user_id' : bind.core.activeUser.userID
          });
        }
        
        // Remove element
        conversationTrayMenuTemplate.destroy();
        $('seIM_conversation_trayItem_' + conversationIndex).destroy();
        
        // Unregister
        bind.core.modules.gui.unregisterItem('seIM_conversation_trayItem_' + conversationIndex);
        bind.core.modules.gui.unregisterMenu('seIM_conversation_trayItem_' + conversationIndex);
        
      });
      
      var messageSubmitFunction = function()
      {
        var messageContent = conversationTrayMenuTemplate.getElement('.seIM_conversation_trayMenu_textInput').value;
        if( !messageContent || messageContent=='' ) return;
        
        bind.messageSend(conversationIndex, messageContent);
        
        conversationTrayMenuTemplate.getElement('.seIM_conversation_trayMenu_textInput').value = '';
        conversationTrayMenuTemplate.getElement('.seIM_conversation_trayMenu_textInput').focus();
      }
      
      conversationTrayMenuTemplate.getElement('.seIM_conversation_trayMenu_textInput').addEvent('keyenter', messageSubmitFunction);
      //conversationTrayMenuTemplate.getElement('.seIM_conversation_trayMenu_textSubmit').addEvent('click', messageSubmitFunction);
      
      // Inject Show Register
      conversationTrayMenuTemplate.inject(document.body);
      //this.core.modules.gui.menus.set('conversation_' + conversationIndex, conversationTrayMenuTemplate);
      this.core.modules.gui.registerMenu('conversation_' + conversationIndex, conversationTrayMenuTemplate);
      
      // Position
      window.addEvent('resize', function() { bind.core.modules.gui.menuAdjustPosition('conversation_' + conversationIndex); });
      if( Browser.Engine.trident )
      {
        conversationTrayMenuTemplate.style.position = "absolute";
        window.addEvent('scroll', function() { bind.core.modules.gui.menuAdjustPosition('conversation_' + conversationIndex); });
      }
    }
    
    
    
    
    this.modifyConversationProfile(conversationIndex);
  },
  
  
  
  
  modifyConversationProfile: function(conversationIndex)
  {
    var bind = this;
    
    var conversationObject = this.scrapeConversation(undefined, undefined, conversationIndex, true);
    var conversationItemElement = $('seIM_conversation_trayItem_' + conversationIndex);
    var conversationMenuElement = $('seIM_conversation_trayMenu_' + conversationIndex);
    
    if( !conversationObject || !conversationItemElement || !conversationMenuElement )
    {
      alert('Trying to modify non-existent convo');
      return;
    }
    
    // Get user info
    if( conversationObject.userID && this.core.users.get(conversationObject.userID) )
    {
      var userRegistryObject = this.core.users.get(conversationObject.userID);
      
      // User name
      if( userRegistryObject )
      {
        var userDisplayName = this.core.userDisplayName(userRegistryObject);
        var userNameTruncated = userDisplayName || '[unknown]';
        if( userNameTruncated.length>15 ) userNameTruncated = userNameTruncated.substr(0,12) + '...';
        
        conversationItemElement.getElement('.seIM_trayItem_title').setProperty('html', userNameTruncated);
        conversationMenuElement.getElement('.seIM_trayMenu_userName').setProperty('html', userNameTruncated);
      }
      
      // User Status
      //alert(userRegistryObject.toQueryString());
      var userStatusIcon = '<img class="seIM_userStatusIcon" src="' + this.core.userStatusIcon(userRegistryObject.userStatus) + '" />';
      conversationItemElement.getElement('.seIM_trayItem_userStatus').setProperty('html', userStatusIcon);
      // REMOVED conversationMenuElement.getElement('.seIM_trayMenu_userStatus').setProperty('html', userStatusIcon);
      
      // user message
      /* REMOVED 
      if( userRegistryObject.userMessage )
      {
        conversationMenuElement.getElement('.seIM_trayMenu_title').getElement('.seIM_trayMenu_userMessage').setProperty('html', userRegistryObject.userMessage);
      }
      */
      
      // User photo
      if( userRegistryObject.userPath && userRegistryObject.userPhoto )
        var photoHtml = '<img src="' + userRegistryObject.userPath + '/' + userRegistryObject.userPhoto + '" width="25" height="25" />';
      else
        var photoHtml = '<img src="' + this.core.options.imagePath + '/icons/chat_nophoto.gif" width="25" height="25" />';
      
      conversationItemElement.getElement('.seIM_trayItem_icon').setProperty('html', photoHtml);
      // REMOVED conversationMenuElement.getElement('.seIM_trayMenu_icon').setProperty('html', photoHtml);
    }
    
    else
    {
      // User Status
      var userStatusIcon = '<img class="seIM_userStatusIcon" src="' + this.core.userStatusIcon(0) + '" />';
      conversationItemElement.getElement('.seIM_trayItem_userStatus').setProperty('html', userStatusIcon);
      // REMOVED conversationMenuElement.getElement('.seIM_trayMenu_userStatus').setProperty('html', userStatusIcon);
    }
  },
  
  
  
  
  
  
  
  /* ----------------------------------------------------------------------- *\
  | Methods - Messages                                                        |
  \* ----------------------------------------------------------------------- */
  
  scrapeMessage: function(messageID, messageIndex, noCreate)
  {
    // Get
    var messageObject;
    if( messageID && this.messagesByMessage.get(messageID) )
      messageObject = this.messagesByMessage.get(messageID);
    if( messageIndex && this.messagesByIndex.get(messageIndex) )
      messageObject = this.messagesByIndex.get(messageIndex);
    
    // Create
    var isCreate = false;
    if( !messageObject /*&& !noCreate*/ )
    {
      messageObject = new Hash;
      if( !messageIndex )
        messageIndex = this.messageIndex++;
    }
    
    // Update
    if( messageID && !messageObject.get('messageID') )
      messageObject.set('messageID', messageID);
    if( messageIndex && !messageObject.get('messageIndex') )
      messageObject.set('messageIndex', messageIndex);
    
    // Index
    if( messageID && !this.messagesByMessage.get(messageID) )
      this.messagesByMessage.set(messageID, messageObject);
    if( messageIndex && !this.messagesByIndex.get(messageIndex) )
      this.messagesByIndex.set(messageIndex, messageObject);
    
    return messageObject;
  },
  
  
  
  
  messageSend: function(conversationIndex, messageContent, userID)
  {
    var bind = this;
    var conversationObject = this.scrapeConversation(undefined, undefined, conversationIndex);
    
    var messageObject = this.scrapeMessage();
    
    // Check for user
    var doJoinAgain = false;
    if( conversationObject.maskID && conversationObject.userID && this.core.masks.get(conversationObject.maskID) )
    {
      var maskRegistryObject = this.core.masks.get(conversationObject.maskID);
      //var userRegistryObject = this.core.masks.get(conversationObject.maskID).users.get(conversationObject.userID);
      var maskuserRegistryObject = this.core.masks.get(conversationObject.maskID).maskusers.get(conversationObject.userID);
      
      if( !maskuserRegistryObject || !maskuserRegistryObject.maskUserStatus )
      {
        doJoinAgain = true;
      }
    }
    
    // Replace URLs and smilies
    messageContent = this.core.replaceUrls(messageContent);
    messageContent = this.core.replaceSmilies(messageContent);
    
    // Add message to GUI
    this.messageAdd({
      'conversationIndex' : conversationIndex,
      'messageIndex' : messageObject.messageIndex,
      'messageContent' : messageContent,
      'userID' : this.core.activeUser.userID,
      'maskID' : conversationObject.maskID
    });
    
    var sendMessageFunction = function(messageMaskID)
    {
      bind.core.Request({
        'task' : 'messageSend',
        'message_content' : messageContent,
        'mask_id' : messageMaskID,
        'conversation_index' : conversationIndex,
        'message_index' : messageObject.messageIndex
      });
    }
    
    var maskCreateFunction = function()
    {
      bind.core.Request({
        'task' : 'maskCreate',
        'user_id1' : bind.core.activeUser.userID,
        'user_id2' : conversationObject.userID,
        'conversation_index' : conversationIndex
      }, function(requestObject, responseObject)
      {
        //bind.scrapeConversation(userID, responseObject.response.maskID, conversationIndex);
        sendMessageFunction(responseObject.response.maskID);
      });
    }
    
    var userJoinFunction = function(joinMaskID, joinUserID)
    {
      bind.core.Request({
        'task' : 'maskUserJoin',
        'user_id' : joinUserID,
        'mask_id' : joinMaskID
      }, function(requestObject, responseObject)
      {
        sendMessageFunction(joinMaskID);
      });
    }
    
    // Execute
    if( doJoinAgain )
      userJoinFunction(conversationObject.maskID, conversationObject.userID);
    else if( conversationObject.maskID )
      sendMessageFunction(conversationObject.maskID);
    else
      maskCreateFunction();
    
    
    // Show error if target user is offline
    if( !this.core.users.get(conversationObject.userID) || !this.core.users.get(conversationObject.userID).userStatus )
    {
      var messageObject = this.scrapeMessage();
      this.messageAdd({
        'conversationIndex' : conversationIndex,
        'messageIndex' : messageObject.messageIndex,
        'messageContent' : SELanguage.Translate(3510034),
        'isError' : true
      });
    }
  },
  
  
  
  
  messageAdd: function(arguments)
  {
    // Scrape
    var nonSelfUserID = ( arguments.userID==this.core.activeUser.userID ? undefined : arguments.userID );
    var conversationObject = this.scrapeConversation(nonSelfUserID, arguments.maskID, arguments.conversationIndex);
    var messageObject = this.scrapeMessage(arguments.messageID, arguments.messageIndex);
    
    // Create
    var conversationIndex = conversationObject.conversationIndex;
    var messageIndex = messageObject.messageIndex;
    
    if( !$('seIM_conversation_trayMenu_' + conversationIndex + '_message_' + messageIndex) )
    {
      // Load Clone Assign
      var messageTemplate = $('seIM_conversation_trayMenu_message_template').getElement('.seIM_conversation_trayMenu_message').clone();
      messageTemplate.setProperty('id', 'seIM_conversation_trayMenu_' + conversationIndex + '_message_' + messageIndex);
      
      // Prepare
      var userID = arguments.userID;
      var messageID = arguments.messageID;
      // MOVED TO MESSAGESEND var messageContent = this.core.replaceSmilies(arguments.messageContent);
      var messageContent = arguments.messageContent;
      var messageTimestamp = this.core.createTimestamp(arguments.messageTime, true);
      
      // User message
      if( userID )
      {
        var userObject = this.core.users.get(userID);
        var userDisplayName = ( userObject ? this.core.userDisplayName(userObject, 'short') : 'unknown' );
        userDisplayName += ':'
        
        messageTemplate.getElement('.seIM_conversation_trayMenu_messageTimestamp').setProperty('html', '(' + messageTimestamp + ')');
        messageTemplate.getElement('.seIM_conversation_trayMenu_messageUserName').setProperty('html', userDisplayName);
        messageTemplate.getElement('.seIM_conversation_trayMenu_messageContent').setProperty('html', messageContent);
        
      }
      
      // System message
      else
      {
        messageTemplate.getElement('.seIM_conversation_trayMenu_messageTimestamp').setProperty('html', '(' + messageTimestamp + ')');
        messageTemplate.getElement('.seIM_conversation_trayMenu_messageUserName').setProperty('html', '');
        messageTemplate.getElement('.seIM_conversation_trayMenu_messageContent').setProperty('html', messageContent);
        
        if( !arguments.isError )
          messageTemplate.getElement('.seIM_conversation_trayMenu_messageContent').addClass('seIMSystemMessage');
        else
          messageTemplate.getElement('.seIM_conversation_trayMenu_messageContent').addClass('seIMSystemErrorMessage');
      }
      
      if( !this.core.userConfig.get('enableTimestamp') )
        messageTemplate.getElement('.seIM_conversation_trayMenu_messageTimestamp').addClass('seIMHide');
      
      messageTemplate.inject($('seIM_conversation_trayMenu_' + conversationIndex).getElement('.seIM_trayMenu_bodyList'));
      
      // Hide null message
      if( !$('seIM_conversation_trayMenu_' + conversationIndex).getElement('.seIMNullMessage').hasClass('seIMHide') )
        $('seIM_conversation_trayMenu_' + conversationIndex).getElement('.seIMNullMessage').addClass('seIMHide');
    }
    
    // Scroll
    var bodyListWrapper = $('seIM_conversation_trayMenu_' + conversationIndex).getElement('.seIM_trayMenu_bodyListWrapper');
    bodyListWrapper.scrollTo(0, bodyListWrapper.getScrollSize().y);
    
    return messageIndex;
  },
  
  
  
  
  transMessageUpdate: function(messageObject, conversationIndex)
  {
    // TODO?
  },
  
  
  
  
  transMessageRemove: function(messageObject, conversationIndex)
  {
    // TODO?
  }





});