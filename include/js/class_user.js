
/* $Id: class_user.js 151 2009-04-01 06:14:22Z john $ */

// Required language vars: 743-747,773,1113 (status) 759 (delete), 1199 (notifys)

// This file will no longer be included by default. A minified version is included as part of core-min.js

SocialEngineAPI.User = new Class({
  
  // Properties
  Base: {},
  
  user_exists : false,
  
  user_displayname : false,
  
  user_displayname_short : false,
  
  user_info : {},
  
  profile_info : {},
  
  level_info : {},
  
  usersetting_info : {},
  
  options : {
    // Controls ajax request options
    'ajaxURL' : 'misc_js.php',
    'ajaxMethod' : 'post',
    'ajaxSecure' : false,
    
    // Display name order method
    'displayname_order' : 'standard'
  },
  
  user_status: '',
  
  user_notify_cookie: {},
  
  user_notify_count: 0,
  
  
  
  // Methods
  initialize: function()
  {
    
  },
  
  
  userPhotoFullPath: function()
  {
    // No URL class
    if( !this.Base.URL )
      return false;
    
    // No user photo
    if( !this.user_info.user_photo )
      return this.Base.URL.url_base + 'images/nophoto.gif';
      
    return this.Base.URL.url_base + this.Base.URL.url_userdir(this.user_info.user_id) + this.user_info.user_photo;
  },
  
  
  
  // Methods - user status
  userStatusChange: function()
  {
    if( !$('ajax_status') ) return false;
    
    var userStatus = this.user_status.replace(/<wbr>/g, '').replace(/&shy;/g, '');
    
    var statusHTML = this.user_displayname_short + 
      " <input type='text' class='text_small' name='status_new' id='status_new' maxlength='100' value='";
    
    statusHTML += ( userStatus=='' ? this.Base.Language.Translate(744) : userStatus );
    statusHTML += "' size='10' style='width: 140px; margin: 2px 0px 2px 0px;' onkeypress='return ( (new Event(event)).key==\"enter\" ? SocialEngine.Viewer.userStatusChangeSubmit() : true );'>" +
      "<br />" +
      "<a href='javascript:void(0);' onclick='SocialEngine.Viewer.userStatusChangeSubmit(); return false;'>" +
        this.Base.Language.Translate(746) +
      "</a> | <a href='javascript:void(0);' onclick='SocialEngine.Viewer.userStatusChangeReturn(); return false;'>" +
        this.Base.Language.Translate(747) +
      "</a>";
    
    $('ajax_status').innerHTML = statusHTML;
    $('status_new').focus();
    $('status_new').select();
  },
  
  
  userStatusChangeReturn: function()
  {
    if( !$('ajax_status') ) return false;
    
    if( this.user_status=='' )
    {
      $('ajax_status').innerHTML = "<a href='javascript:void(0);' onclick='SocialEngine.Viewer.userStatusChange(); return false;'>" + this.Base.Language.Translate(743) + "</a>";
    }
    else
    {
      $('ajax_status').innerHTML = 
        "<div id='ajax_status'>" + this.user_displayname_short +
          " <span id='ajax_currentstatus_value'>" + this.user_status + "</span><br />" +
          "<div style='padding-top: 5px;'>" +
            "<div style='float: left; padding-right: 5px;'>" +
              "[ <a href='javascript:void(0);' onClick='SocialEngine.Viewer.userStatusChange(); return false;'>" + this.Base.Language.Translate(745) + "</a> ]" +
            "</div>" +
            "<div class='home_updated'>" +
              "<span id='ajax_currentstatus_date'>" + this.Base.Language.Translate(1113) + " " + this.Base.Language.TranslateFormatted(773, [1]) + "</span>" +
            "</div>" +
            "<div style='clear: both; height: 0px;'></div>" +
          "</div>" +
        "</div>";
    }
  },
  
  
  userStatusChangeSubmit: function()
  {
    if( !$('ajax_status') ) return false;
    
    var bind = this;
    var request = new Request.JSON({
      'url' : this.options.ajaxURL,
      'method' : this.options.ajaxMethod,
      'secure' : this.options.ajaxSecure,
      'data' : {
        'task'  : 'status_change',
        'status'  : $('status_new').value
      },
      'onComplete' : function(responseObject, responseText)
      {
        bind.user_status = responseObject.status;
        bind.userStatusChangeReturn();
      }
    }).send();
  },
  
  
  
  // Methods - user status
  userDelete: function()
  {
    TB_show(this.Base.Language.Translate(759), '#TB_inline?height=100&width=300&inlineId=confirmdelete', '', '../images/trans.gif');
  },
  
  
  userDeleteConfirm: function(delete_token)
  {
    var bind = this;
    var request = new Request.JSON({
      'url' : 'user_account_delete.php',
      'method' : this.options.ajaxMethod,
      'secure' : this.options.ajaxSecure,
      'data' : {
        'task'  : 'dodelete',
        'token'  : delete_token
      },
      'onComplete' : function(responseObject, responseText)
      {
        window.location = 'home.php';
      }
    }).send();
  },
  
  
  
  // Methods - actions
  userActionDelete: function(action_id)
  {
    var bind = this;
    var request = new Request.JSON({
      'url' : this.options.ajaxURL,
      'method' : this.options.ajaxMethod,
      'secure' : this.options.ajaxSecure,
      'data' : {
        'task'  : 'action_delete',
        'action_id'  : action_id
      },
      'onComplete' : function(responseObject, responseText)
      {
        if( $('action_' + action_id) )
        {
          $('action_' + action_id).style.display = 'none';
          total_actions--;
          if( total_actions == 0 )
            $('actions').style.display = "none";
        }
      }
    }).send();
  },
  
  
  
  // Methods - notifications
  userNotifyShow: function()
  {
    this.user_notify_cookie = new Hash.Cookie('se_show_newupdates', {duration: 3600});
    var minimizedCount = parseInt(this.user_notify_cookie.get('total'));
    if( !$type(minimizedCount) ) minimizedCount = 0;

    if( minimizedCount < this.user_notify_count )
    {
      this.user_notify_cookie.set('total', 0);
      $('newupdates').style.display = 'block';
      $('newupdates').fade('in');
    }
  },
  
  
  userNotifyPopup: function()
  {
    TB_show(this.Base.Language.Translate(1198), '#TB_inline?height=150&width=300&inlineId=newupdates_popup', '', './images/trans.gif');
  },
  
  
  userNotifyUpdate: function()
  {
    var bind = this;
    var request = new Request.JSON({
      'url' : this.options.ajaxURL,
      'method' : this.options.ajaxMethod,
      'secure' : this.options.ajaxSecure,
      'data' : {
        'task'  : 'notify_get'
      },
      'onComplete' : function(responseObject, responseText)
      {
        bind.userNotifyGenerate(responseObject);
        bind.userNotifyShow();
      }
    }).send();
  },
  
  
  userNotifyGenerate: function(notifyData)
  {
    if( !$type(notifyData.notifys) || notifyData.notifys.length==0 || !$('newupdates_popup') )
    {
      $('notify_total').innerHTML = this.user_notify_count = 0;
      return;
    }
    
    // Update count in red box
    $('notify_total').innerHTML = this.user_notify_count = notifyData.total;
    
    // Update popup contents
    var HTML =
      "<div style='margin-top: 10px;'>" +
        this.Base.Language.TranslateFormatted(1199, ["<span id='notifyscount'>" + notifyData.total_grouped + "</span>"]) + 
      "</div>";
    
    notifyData.notifys.each(function(notify_info)
    {
      HTML +=
        "<div style='font-weight: bold; padding-top: 5px;' id='notify_" + notify_info.notifytype_id + "_" + notify_info.notify_grouped + "'>" +
          "<a href='javascript:void(0);' onClick=\"SocialEngine.Viewer.userNotifyDelete('" + notify_info.notifytype_id + "', '" + notify_info.notify_grouped + "');\">X</a>" +
          "<img src='./images/icons/" + notify_info.notify_icon + "' border='0' style='border: none; margin: 0px 5px 0px 5px; display: inline; vertical-align: middle;' class='icon' />" +
          "<a href='" + notify_info.notify_url + "'>" + notify_info.notify_text_output + "</a>" +
        "</div>";
    });
    
    $('newupdates_popup').innerHTML = HTML;
  },
  
  
  userNotifyDelete: function(notifytype_id, notify_grouped)
  {
    var bind = this;
    var request = new Request.JSON({
      'url' : this.options.ajaxURL,
      'method' : this.options.ajaxMethod,
      'secure' : this.options.ajaxSecure,
      'data' : {
        'task'  : 'notify_delete',
        'notifytype_id'  : notifytype_id,
        'notify_grouped'  : notify_grouped
      },
      'onComplete' : function(responseObject, responseText)
      {
        $("TB_window").getElements('div[id=notify_'+notifytype_id+'_'+notify_grouped+']').each(function(el)
        {
          if(el.id == 'notify_'+notifytype_id+'_'+notify_grouped)
          {
            el.style.display = 'none';
            bind.user_notify_count--;
          }
        });
        
        $('newupdates_popup').getElements('div[id=notify_'+notifytype_id+'_'+notify_grouped+']').each(function(el)
        {
          if(el.id == 'notify_'+notifytype_id+'_'+notify_grouped)
          el.style.display = 'none';
        });
        
        $('notify_total').innerHTML = bind.user_notify_count;
        
        $("TB_window").getElements('span[id=notifyscount]').each(function(el)
        {
          if(el.id == 'notifyscount') el.innerHTML = bind.user_notify_count;
        });
        
        if( bind.user_notify_count == 0 )
        {
          TB_remove();
          $('newupdates').style.display = 'none';
        }
      }
    }).send();
  },
  
  
  userNotifyHide: function()
  {
    $('newupdates').fade('out');
    this.user_notify_cookie.set('total', this.user_notify_count)
  },
  
  
  userPhotoRemove: function()
  {
    var bind = this;
    var request = new Request.JSON({
      'url' : 'user_editprofile_photo.php',
      'method' : this.options.ajaxMethod,
      'secure' : this.options.ajaxSecure,
      'data' : {
        'task'  : 'remove'
      }
    }).send();
    
    if( $('userEditRemovePhotoLink' ) && $('userEditPhotoImg') )
    {
      $('userEditRemovePhotoLink' ).destroy();
      $('userEditPhotoImg' ).src = 'images/nophoto.gif';
    }
    
    else
    {
      window.location.reload( false );
    }
  },
  
  
  
  
  
  // Import methods
  ImportUserInfo: function(user_info)
  {
    // Handle anonymous users
    if( !user_info || $type(user_info)!="object" || !user_info.user_exists )
    {
      this.user_exists = false;
      return;
    }
    
    this.user_exists = true;
    
    // Prepare data
    user_info.user_id = parseInt(user_info.user_id);
    delete user_info.user_exists;
    
    // Save user info
    this.user_info = user_info;
    
    // Generate display name
    this.user_info.user_fname = this.user_info.user_fname.trim();
    this.user_info.user_lname = this.user_info.user_lname.trim();
    
    if( this.user_info.user_fname && this.user_info.user_lname )
    {
      // Asian
      if( this.options.displayname_order=="asian" )
      {
        this.user_displayname_short = this.user_info.user_lname;
        this.user_displayname = this.user_info.user_lname + ' ' + this.user_info.user_fname;
      }
      
      // Standard
      else
      {
        this.user_displayname_short = this.user_info.user_fname;
        this.user_displayname = this.user_info.user_fname + ' ' + this.user_info.user_lname;
      }
    }
    
    else if( this.user_info.user_fname )
    {
      this.user_displayname = this.user_displayname_short = this.user_info.user_fname;
    }
    
    else if( this.user_info.user_lname )
    {
      this.user_displayname = this.user_displayname_short = this.user_info.user_lname;
    }
    
    else if( this.user_info.user_username )
    {
      this.user_displayname = this.user_displayname_short = this.user_info.user_username;
    }
  }
  
});