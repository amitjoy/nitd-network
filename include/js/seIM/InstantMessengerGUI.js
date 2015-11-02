
/* $Id: InstantMessengerGUI.js 6 2009-01-11 06:01:29Z john $ */


var InstantMessengerGUI = new Class({

  /* ----------------------------------------------------------------------- *\
  | Class                                                                     |
  \* ----------------------------------------------------------------------- */
  
	Implements: [],
  
  
  
  
  
  /* ----------------------------------------------------------------------- *\
  | Methods - Class                                                           |
  \* ----------------------------------------------------------------------- */
  
  initialize: function()
  {
    // Registry
    this.items = new Hash;
    this.menus = new Hash;
    
    this.tips = new Tips($$('.seIMTips1'), {'className' : 'seIMTips1Main'});
  },
  
  
  
  onModuleRegister: function()
  {
    var bind = this;
    
    // Spawn
    this.spawnTray();
    this.spawnOptions();
    this.spawnFriends();
    
    // Attach
    this.core.addEvent('connectionStateChanged', function(params)
    {
      // Online
      if( params.connectionState==bind.core.constants.CONNECTION_STATE.CONNECTED )
      {
        $$('.seIM_trayItem').each(function(trayItemElement)
        {
          // Leave options alone
          if( trayItemElement.hasClass('seIM_options_trayItem') ) return;
          
          if( trayItemElement.hasClass('seIMInvisible') ) trayItemElement.removeClass('seIMInvisible');
        });
        
        // Options Menu
        var optionsMenu = $('seIM_options_trayMenu');
        if( optionsMenu.hasClass('seIM_options_trayMenu_isAlone') ) optionsMenu.removeClass('seIM_options_trayMenu_isAlone');
        
        // Tray spacer
        var traySpacer = $('seIM_tray').getElement('.seIM_traySpacer');
        if( traySpacer.hasClass('seIMInvisible') ) traySpacer.removeClass('seIMInvisible');
        
        bind.transOptionsUserStatus(bind.core.constants.USER_STATUS.ONLINE);
      }
      
      // Offline
      else if( params.connectionState==bind.core.constants.CONNECTION_STATE.DISCONNECTED )
      {
        $$('.seIM_trayItem').each(function(trayItemElement)
        {
          // Leave options alone
          if( trayItemElement.hasClass('seIM_options_trayItem') ) return;
          
          if( !trayItemElement.hasClass('seIMInvisible') ) trayItemElement.addClass('seIMInvisible');
        });
        
        // Options Menu
        var optionsMenu = $('seIM_options_trayMenu');
        if( !optionsMenu.hasClass('seIM_options_trayMenu_isAlone') ) optionsMenu.addClass('seIM_options_trayMenu_isAlone');
        
        // Tray spacer
        var traySpacer = $('seIM_tray').getElement('.seIM_traySpacer');
        if( !traySpacer.hasClass('seIMInvisible') ) traySpacer.addClass('seIMInvisible');
        
        bind.transOptionsUserStatus(bind.core.constants.USER_STATUS.OFFLINE);
      }
    });
    
    this.core.addEvent('userCreation', function(params)
    {
      if( params.object.isFriendOfActiveUser && !params.object.isActiveUser )
        bind.transFriendAdd(params.object);
      
      if( params.object.isActiveUser )
        bind.transOptionsUserStatus(params.object.userStatus);
    });
    
    this.core.addEvent('userUpdate', function(params)
    {
      if( params.object.isFriendOfActiveUser && !params.object.isActiveUser )
        bind.transFriendUpdate(params.data);
      
      if( params.object.isActiveUser )
        bind.transOptionsUserStatus(params.data.userStatus);
    });
    
    this.core.addEvent('userDestruction', function(params)
    {
      if( params.object.isFriendOfActiveUser )
        bind.transFriendRemove(params.object);
      
      if( params.object.isActiveUser )
        bind.transOptionsUserStatus(params.object.userStatus);
    });
  },
  
  
  
  onBoot: function()
  {
    // Defaults
    if( !$type(this.core.userConfig.get('enableAudio'))     ) this.core.userConfig.set('enableAudio', true);
    
    /* TEMPORARILY REMOVE
    if( !$type(this.core.userConfig.get('enableTimestamp')) ) this.core.userConfig.set('enableTimestamp', true);
    */
    this.core.userConfig.set('enableTimestamp', false);
    
    // Make sure menu icons are showing up right on boot
    this.handleOptionAudio(true);
    this.handleOptionTimestamp(true);
  },
  
  
  
  
  
  
  
  
  /* ----------------------------------------------------------------------- *\
  | Methods - Spawn                                                           |
  \* ----------------------------------------------------------------------- */
  
  registerItem: function(name, element)
  {
    // Register
    this.items.set(name, element);
    // Event
    this.core.fireEvent('itemRegistered', [
      name,
      element
    ]);
  },
  
  
  
  unregisterItem: function(name)
  {
    // Register
    this.items.erase(name);
    // Event
    this.core.fireEvent('itemUnregistered', [
      name
    ]);
  },
  
  
  
  registerMenu: function(name, element)
  {
    // Register
    this.menus.set(name, element);
    // Open
    if( name==this.core.sessionData.get('lastOpenMenu') )
      this.menuToggle(name, true);
    // Event
    this.core.fireEvent('menuRegistered', [
      name,
      element
    ]);
  },
  
  
  
  unregisterMenu: function(name)
  {
    // Register
    this.menus.erase(name);
    // Event
    this.core.fireEvent('menuUnregistered', [
      name
    ]);
  },
  
  
  
  
  
  
  
  
  /* ----------------------------------------------------------------------- *\
  | Methods - Spawn                                                           |
  \* ----------------------------------------------------------------------- */
  
  spawnTray: function()
  {
    var bind = this;
    if( !$('seIM_tray') )
    {
      // Load Clone
      var trayTemplateWrapper = $('seIM_tray_template').getElement('.seIM_tray_wrapper').clone();
      var trayTemplate = trayTemplateWrapper.getElement('.seIM_tray');
      
      // Inject Show
      trayTemplateWrapper.inject(document.body);
      
      // Show Assign
      trayTemplate.removeClass('seIMHide');
      trayTemplate.setProperty('id', 'seIM_tray');
      
      
      // Position (IE)
      if( Browser.Engine.trident )
      {
        trayTemplate.style.position = 'absolute';
        var newIEHeight = (trayTemplate.getCoordinates().height - 5).toString() + 'px';
        
        var trayRepositionEvent = function()
        {
          var windowCoords = window.getCoordinates();
          var trayCoords = trayTemplate.getCoordinates();
          
          trayTemplate.setStyles({
            'position' : 'absolute',
            'top' : (windowCoords.height  + window.getScroll().y - trayCoords.height + 0).toString() + 'px',
            'left' : '0px',
            'width' : (trayTemplateWrapper.getCoordinates().width - 20).toString() + 'px',
            'height' : newIEHeight
          });
        }
        
        window.addEvent('resize', trayRepositionEvent);
        window.addEvent('scroll', trayRepositionEvent);
        
        trayRepositionEvent.periodical(250);
      }
    }
  },
  
  
  
  spawnFriends: function()
  {
    var bind = this;
    if( !$('seIM_friends_trayItem') )
    {
      // Load Clone
      var friendsTrayItemTemplate = $('seIM_friends_trayItem_template').getElement('.seIM_friends_trayItem').clone();
      
      // Assign
      friendsTrayItemTemplate.setProperty('id', 'seIM_friends_trayItem');
      
      // Attach
      friendsTrayItemTemplate.getElement('.seIM_trayItem_menuActivator').addEvent('click', function()
      {
        bind.menuToggle('friends');
      });
      
      // Inject Register
      friendsTrayItemTemplate.inject($('seIM_tray').getElement('.seIM_traySpacer'), 'after');
      this.registerItem('friends', friendsTrayItemTemplate);
    }
    
    if( !$('seIM_friends_trayMenu') )
    {
      // Load Clone Assign
      var friendsTrayMenuTemplate = $('seIM_friends_trayMenu_template').getElement('.seIM_friends_trayMenu').clone();
      friendsTrayMenuTemplate.setProperty('id', 'seIM_friends_trayMenu');
      
      // Attach
      friendsTrayMenuTemplate.getElement('.seIM_trayItem_menuDeactivator').addEvent('click', function()
      {
        bind.menuToggle('friends', false);
      });
      
      // Inject Show
      friendsTrayMenuTemplate.inject(document.body);
      this.registerMenu('friends', friendsTrayMenuTemplate);
      
      // Position
      window.addEvent('resize', function() { bind.menuAdjustPosition('friends'); });
      if( Browser.Engine.trident )
      {
        friendsTrayMenuTemplate.style.position = "absolute";
        window.addEvent('scroll', function() { bind.menuAdjustPosition('friends'); });
      }
    }
  },
  
  
  
  
  spawnOptions: function()
  {
    var bind = this;
    if( !$('seIM_options_trayItem') )
    {
      // Load Clone Assign
      var optionsTrayItemTemplate = $('seIM_options_trayItem_template').getElement('.seIM_options_trayItem').clone();
      optionsTrayItemTemplate.setProperty('id', 'seIM_options_trayItem');
      
      // Attach
      optionsTrayItemTemplate.getElement('.seIM_trayItem_menuActivator').addEvent('click', function()
      {
        bind.menuToggle('options');
      });
      
      // Inject Show Register
      optionsTrayItemTemplate.inject($('seIM_tray').getElement('.seIM_traySpacer'), 'after');
      this.registerItem('options', optionsTrayItemTemplate);
    }
    
    if( !$('seIM_options_trayMenu') )
    {
      // Load Clone Assign
      var optionsTrayMenuTemplate = $('seIM_options_trayMenu_template').getElement('.seIM_options_trayMenu').clone();
      optionsTrayMenuTemplate.setProperty('id', 'seIM_options_trayMenu');
      
      // Attach
      optionsTrayMenuTemplate.getElement('.seIM_trayItem_menuDeactivator').addEvent('click', function()
      {
        bind.menuToggle('options', false);
      });
      
      optionsTrayMenuTemplate.getElement('.seIM_options_trayMenu_statusSelect').addEvent('change', function()
      {
        bind.handleUserStatus(parseInt(this.value));
      });
      
      optionsTrayMenuTemplate.getElement('.seIM_options_trayMenu_audioButton').addEvent('click', function()
      {
        bind.handleOptionAudio();
      });
      
      optionsTrayMenuTemplate.getElement('.seIM_options_trayMenu_timestampButton').addEvent('click', function()
      {
        bind.handleOptionTimestamp();
      });
      
      // Inject Show Register
      optionsTrayMenuTemplate.inject(document.body);
      this.registerMenu('options', optionsTrayMenuTemplate);
      
      // Position
      window.addEvent('resize', function() { bind.menuAdjustPosition('options'); });
      if( Browser.Engine.trident )
      {
        optionsTrayMenuTemplate.style.position = "absolute";
        window.addEvent('scroll', function() { bind.menuAdjustPosition('options'); });
      }
    }
  },
  
  
  
  
  
  
  
  
  
  
  
  
  transFriendAdd: function(userObject)
  {
    if( !userObject.userStatus )
      return this.transFriendRemove(userObject);
    
    // Create
    var bind = this;
    if( !$('seIM_friends_trayMenu_friendItem_' + userObject.userID) )
    {
      // Load Clone Assign
      var friendItemTemplate = $('seIM_friends_trayMenu_bodyListItem_template').getElement('.seIM_friends_trayMenu_bodyListItem').clone();
      friendItemTemplate.setProperty('id', 'seIM_friends_trayMenu_friendItem_' + userObject.userID);
      
      // Attach
      friendItemTemplate.getElement('.seIM_trayMenu_bodyList_activator').addEvent('click', function()
      {
        bind.core.fireEvent('openConversation', {
          'userID' : userObject.userID
        });
      });
      
      // Inject Show
      friendItemTemplate.inject($('seIM_friends_trayMenu').getElement('.seIM_trayMenu_bodyList'));
      
      // Tips
      /*
      friendItemTemplate.store('tip:title', userObject.userName + ' ' + userObject.userMessage);
      this.tips.attach(friendItemTemplate);
      */
    }
    
    
    this.transFriendUpdate(userObject);
    //this.transFriendCount();
  },
  
  
  
  
  transFriendUpdate: function(userObject)
  {
    if( userObject.userStatus && !$('seIM_friends_trayMenu_friendItem_' + userObject.userID) )
      return this.transFriendAdd(userObject);
    else if( !userObject.userStatus )
      return this.transFriendRemove(userObject);
    
    // Update
    if( $('seIM_friends_trayMenu_friendItem_' + userObject.userID) )
    {
      var friendItem = $('seIM_friends_trayMenu_friendItem_' + userObject.userID);
      
      // Prepare
      var userDisplayName = this.core.userDisplayName(userObject);
      var userNameTruncated = userDisplayName || '[unknown]';
      if( userNameTruncated.length>20 ) userNameTruncated = userNameTruncated.substr(0,20) + '...';
      
      var userMessageTruncated = userObject.userMessage || '&nbsp;';
      if( userMessageTruncated.length>35 ) userMessageTruncated = userMessageTruncated.substr(0,35) + '...';
      
      var userStatusIcon = '<img class="seIM_userStatusIcon" src="' + this.core.userStatusIcon(userObject.userStatus) + '" />';
      if( userObject.userPath && userObject.userPhoto )
        var userPhotoIcon = '<img src="' + userObject.userPath + '/' + userObject.userPhoto + '" width="25" height="25" />';
      else
        var userPhotoIcon = '<img src="' + this.core.options.imagePath + '/icons/chat_nophoto.gif" width="25" height="25" />';
      
      // Tips
      //friendItem.store('tip:title', userObject.userName + ' ' + userObject.userMessage);
      
      // Data
      friendItem.getElement('.seIM_friends_trayMenu_friendName').setProperty('html', userNameTruncated);
      friendItem.getElement('.seIM_friends_trayMenu_friendMessage').setProperty('html', userMessageTruncated);
      
      friendItem.getElement('.seIM_friends_trayMenu_friendIcon').setProperty('html', userPhotoIcon);
      friendItem.getElement('.seIM_friends_trayMenu_friendStatus').setProperty('html', userStatusIcon);
    }
    
    this.transFriendCount();
  },
  
  
  
  
  transFriendRemove: function(userObject)
  {
    var friendItem = $('seIM_friends_trayMenu_friendItem_' + userObject.userID);
    if( !friendItem ) return;
    
    // Destroy
    this.tips.detach(friendItem);
    friendItem.destroy();
    
    this.transFriendCount();
  },
  
  
  
  
  transFriendCount: function()
  {
    // Update friends count
    var friendsCount = $('seIM_friends_trayMenu').getElements('.seIM_friends_trayMenu_bodyListItem').length;
    $('seIM_friends_trayItem').getElement('.seIM_friends_trayItem_userCount').setProperty('html', friendsCount);
    
    // Show/Hide Null Message
    var nullMessageElement = $('seIM_friends_trayMenu').getElement('.seIM_trayMenu_bodyList').getElement('.seIMNullMessage');
    
    if( friendsCount==0 && nullMessageElement.hasClass('seIMHide') )
      nullMessageElement.removeClass('seIMHide');
    else if( friendsCount>0 && !nullMessageElement.hasClass('seIMHide') )
      nullMessageElement.addClass('seIMHide');
  },
  
  
  
  
  transOptionsUserStatus: function(userStatus)
  {
    // User status select
    this.menus.get('options').getElement('.seIM_options_trayMenu_status_' + userStatus).selected = true;
    
    // Options menu icon
    var optionsIconPath = this.core.options.imagePath;
    switch( userStatus )
    {
      default:
      case this.core.constants.USER_STATUS.OFFLINE:
        optionsIconPath += '/status_im/options_offline16.gif';
      break;
      case this.core.constants.USER_STATUS.ONLINE:
        optionsIconPath += '/status_im/options_online16.gif';
      break;
      case this.core.constants.USER_STATUS.BUSY:
      case this.core.constants.USER_STATUS.AWAY:
        optionsIconPath += '/status_im/options_away16.gif';
      break;
    }
    this.items.get('options').getElement('.seIM_trayItem_icon').setProperty('src', optionsIconPath);
  },
  
  
  
  
  
  
  
  /* ----------------------------------------------------------------------- *\
  | Methods - Menus                                                           |
  \* ----------------------------------------------------------------------- */
  
  menuToggle: function(moduleName, forceMode)
  {
    var bind = this;
    var menuElement = this.menus.get(moduleName);
    var itemElement = this.items.get(moduleName);
    var mode = ( $type(forceMode) ? forceMode : menuElement.hasClass('seIMHide') );
    
    // If offline, do not show menus other than options
    if( this.core.connectionState==this.core.constants.CONNECTION_STATE.DISCONNECTED && mode && moduleName!="options" )
      return;
    
    if( mode )
    {
      this.menuHideAll(moduleName);
      if( menuElement.hasClass('seIMHide') ) menuElement.removeClass('seIMHide');
      this.menuAdjustPosition(moduleName);
      (function() { bind.menuAdjustPosition(moduleName); }).delay(250);
      
      // Save open window
      this.core.sessionData.set('lastOpenMenu', moduleName);
      
      // Modify trayItem class
      if( !itemElement.hasClass('seIM_trayItem_menuIsActive') )
        itemElement.addClass('seIM_trayItem_menuIsActive');
      if( itemElement.nextSibling && !itemElement.nextSibling.hasClass('seIM_trayItem_menuIsRightOfActive') )
        itemElement.nextSibling.addClass('seIM_trayItem_menuIsRightOfActive');
    }
    else
    {
      if( !menuElement.hasClass('seIMHide') ) menuElement.addClass('seIMHide');
      
      // Remove open window
      if( this.core.sessionData.get('lastOpenMenu')==moduleName )
        this.core.sessionData.erase('lastOpenMenu');
      
      // Modify trayItem class
      if( itemElement.hasClass('seIM_trayItem_menuIsActive') )
        itemElement.removeClass('seIM_trayItem_menuIsActive');
      if( itemElement.nextSibling && itemElement.nextSibling.hasClass('seIM_trayItem_menuIsRightOfActive') )
        itemElement.nextSibling.removeClass('seIM_trayItem_menuIsRightOfActive');
      
    }
    
    this.core.fireEvent('menuToggle', moduleName);
  },
  
  
  
  
  menuHideAll: function(exceptModuleName)
  {
    var bind = this;
    this.menus.each(function(currentModuleElement, currentModuleName)
    {
      if( currentModuleName==exceptModuleName ) return;
      bind.menuToggle(currentModuleName, false);
    });
  },
  
  
  
  
  menuAdjustPosition: function(moduleName)
  {
    var menuElement = this.menus.get(moduleName);
    var itemElement = this.items.get(moduleName);
    
    var windowCoords = window.getCoordinates();
    var trayCoords = $('seIM_tray').getCoordinates();
    var menuCoords = menuElement.getCoordinates();
    var itemCoords = itemElement.getCoordinates();
    
    
    // Defaults
    var setStylesObject = {
      'bottom' : (trayCoords.height - 0).toString() + 'px',
      'right' : (windowCoords.width - itemCoords.right).toString() + 'px'
    };
    
    // IE6
    if( Browser.Engine.trident4 )
    {
      setStylesObject = {
        'position' : 'absolute',
        'top' : (windowCoords.height + window.getScroll().y - trayCoords.height - menuCoords.height - 0).toString() + 'px',
        'left' : (itemCoords.right - menuCoords.width + 0).toString() + 'px'
      };
    }
    
    // IE7
    else if( Browser.Engine.trident5 )
    {
      setStylesObject = {
        'position' : 'absolute',
        'top' : (windowCoords.height + window.getScroll().y - trayCoords.height - menuCoords.height - 0).toString() + 'px',
        'right' : (windowCoords.width - itemCoords.right + 0).toString() + 'px'
      };
    }
    
    // Safari
    else if( Browser.Engine.webkit )
    {
      setStylesObject.right = (windowCoords.width - itemCoords.right - 17).toString() + 'px';
    }
    
    // Opera
    else if( Browser.Engine.presto )
    {
      setStylesObject.right = (windowCoords.width - itemCoords.right - 17).toString() + 'px';
    }
    
    // FF2
    else if( Browser.Engine.gecko && Browser.Engine.version<19 )
    {
      setStylesObject.bottom = (trayCoords.height).toString() + 'px';
      setStylesObject.right = (windowCoords.width - itemCoords.right - 10).toString() + 'px';
    }
    
    // FF3
    else if( Browser.Engine.gecko && Browser.Engine.version>=19 )
    {
      setStylesObject.bottom = (trayCoords.height - 0).toString() + 'px';
    }
    
    //Math.round(windowCoords.width * 0.99)
    //Math.round(windowCoords.width * 0.99)
    
    menuElement.setStyles(setStylesObject);
  },
  
  
  
  
  
  
  
  /* ----------------------------------------------------------------------- *\
  | Methods - Options Handling                                                |
  \* ----------------------------------------------------------------------- */
  
  handleUserStatus: function(userStatus)
  {
    var bind = this;
    
    // Disable select box
    this.menus.get('options').getElement('.seIM_options_trayMenu_statusSelect').disabled = true;
    var reEnableFunction = function() { bind.menus.get('options').getElement('.seIM_options_trayMenu_statusSelect').disabled = false; }
    
    // Save for some reason or another?
    this.core.sessionData.set('userChosenStatus', userStatus);
    
    var userStatusFunction = function() { bind.core.Request({'task' : 'userUpdate', 'user_status' : userStatus}, reEnableFunction);}
    
    
    if( this.core.connectionState==this.core.constants.CONNECTION_STATE.DISCONNECTED )
    {
      // Offline->Online
      if( userStatus==this.core.constants.USER_STATUS.ONLINE )
      {
        this.core.Request({ task : 'login' }, reEnableFunction);
      }
      
      // Offline->Away
      else
      {
        this.core.Request({ task : 'login' }, userStatusFunction);
      }
    }
    
    else if( this.core.connectionState==this.core.constants.CONNECTION_STATE.CONNECTED )
    {
      // Online->Offline
      if( userStatus==this.core.constants.USER_STATUS.OFFLINE )
      {
        this.core.Request({ task : 'userLogout' }, reEnableFunction);
      }
      
      // Online->Away
      else
      {
        userStatusFunction();
      }
    }
  },
  
  
  
  handleOptionAudio: function(ensureOnly)
  {
    var doEnable = !this.core.userConfig.get('enableAudio');
    if( ensureOnly ) doEnable = !doEnable;
    
    // Enable
    if( doEnable )
    {
      this.core.userConfig.set('enableAudio', true);
      this.menus.get('options').getElement('.seIM_options_trayMenu_audioButton').checked = true;
      //this.menus.get('options').getElement('.seIM_options_trayMenu_audioButton').setProperty('src', './images/icons/chat_audio2.gif');
    }
    
    // Disable
    else
    {
      this.core.userConfig.set('enableAudio', false);
      this.menus.get('options').getElement('.seIM_options_trayMenu_audioButton').checked = false;
      //this.menus.get('options').getElement('.seIM_options_trayMenu_audioButton').setProperty('src', './images/icons/chat_audio1.gif');
    }
  },
  
  
  handleOptionTimestamp: function(ensureOnly)
  {
    var doEnable = !this.core.userConfig.get('enableTimestamp');
    if( ensureOnly ) doEnable = !doEnable;
    
    // Enable
    if( doEnable )
    {
      this.core.userConfig.set('enableTimestamp', true);
      this.menus.get('options').getElement('.seIM_options_trayMenu_timestampButton').setProperty('src', './images/icons/chat_clock2.gif');
      
      $$('.seIM_conversation_trayMenu_messageTimestamp').each(function(timestampElement)
      {
        if( timestampElement.hasClass('seIMHide') )
          timestampElement.removeClass('seIMHide');
      });
    }
    
    // Disable
    else
    {
      this.core.userConfig.set('enableTimestamp', false);
      this.menus.get('options').getElement('.seIM_options_trayMenu_timestampButton').setProperty('src', './images/icons/chat_clock1.gif');
      
      $$('.seIM_conversation_trayMenu_messageTimestamp').each(function(timestampElement)
      {
        if( !timestampElement.hasClass('seIMHide') )
          timestampElement.addClass('seIMHide');
      });
    }
  },
  
  
  
  
  
  
  
  /* ----------------------------------------------------------------------- *\
  | Methods - Audio                                                           |
  \* ----------------------------------------------------------------------- */
  
  playAudio: function(soundFileName)
  {
    if( this.audioDelayActive || !this.core.userConfig.get('enableAudio') ) return;
    
    if( !soundFileName ) soundFileName = this.core.options.imagePath + '/chat_sound.swf';
    
    var iframe = $('seIMAudioFrame');
    var doc;
    if(iframe.contentDocument) // Firefox, Opera
      doc = iframe.contentDocument;
    else if(iframe.contentWindow) // Internet Explorer
      doc = iframe.contentWindow.document;
    else if(iframe.document) // Others?
      doc = iframe.document;
      
    if( !doc )
    {
      this.audioDelayActive = false;
      throw "Document not initialized";
      return;
    }
    
    doc.open();
    doc.write(
      '<object data="' + soundFileName + '" type="application/x-shockwave-flash" width="1" height="1" style="visibility:hidden">' + 
        '<param name="movie" value="' + soundFileName + '" />' + 
        '<param name="menu" value="false" />' + 
        '<param name="quality" value="high" />' + 
      '</object>'
    );
    doc.close();
    
    // Delay
    (function() { this.audioDelayActive = false; }).delay(this.core.options.audioPostDelay, this);
  }
  
  
});