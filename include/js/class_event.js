
/* $Id: class_event.js 9 2009-01-11 06:03:21Z john $ */

// Required Language vars: 3000080,3000081,3000082,3000083,3000084,3000085,3000093,3000097,3000153,3000154,3000170,3000219,3000223,3000225,3000229


SocialEngineAPI.Event = new Class({
  
  // Class
	Implements: [Options],
  
  
  
  // Properties
  Base: {},
  
  
  options: {
    'ajaxURL' : 'event_ajax.php',
    'ajaxMethod' : 'post',
    'ajaxSecure' : false,
    
    'defaultView' : false
  },
  
  
  eventExists: false,
  
  
  eventInfo: {},
  
  
  userRank: 0,
  
  
  IsMember: false,
  
  
  IsMemberWaiting: false,
  
  
  currentConfirmID: 0,
  
  
  currentInvites: false,
  
  
  visibleTab: 0,
  
  
  rsvpLanguageVars: {
    '-1'  : 3000080,
    '0'   : 3000081,
    '1'   : 3000082,
    '2'   : 3000083,
    '3'   : 3000084,
    '4'   : 3000085
  },
  
  
  
  
  // Initialize
  initialize: function(eventInfo, options)
  {
    var bind = this;
    
    // Options
    if( $type(options)=="object" )
    {
      if( $type(options.defaultView) && options.defaultView=='' )
        delete options.defaultView;
      
      this.setOptions(options);
    }
    
    // Event info stuff
    if( eventInfo && $type(eventInfo)=="object" )
    {
      this.eventExists = eventInfo.event_exists;
      delete eventInfo.event_exists;
      
      this.IsMember = eventInfo.is_member;
      delete eventInfo.is_member;
      
      this.IsMemberWaiting = eventInfo.is_member_waiting;
      delete eventInfo.is_member_waiting;
      
      this.UserRank = eventInfo.user_rank;
      delete eventInfo.user_rank;
      
      this.eventInfo = eventInfo;
    }
    
    // Load currently visible tab
    var currentVisibleTab = Cookie.read('eventVisibleTab');
    
    if( this.options.defaultView )
      currentVisibleTab = this.options.defaultView;
    else if( !$type(currentVisibleTab) || !currentVisibleTab )
      currentVisibleTab = 'profile';
    
    window.addEvent('domready', function()
    {
      if( $('event_tab_table') ) bind.loadProfileTab(currentVisibleTab);
    });
    
  },
  
  
  
  
  // Profile Tabs
  loadProfileTab: function(tabName)
  {
    var bind = this;
    $('event_tab_table').getElements('.event_tab').each(function(tabElement)
    {
      if( tabElement.id=='event_tabs_'+tabName )
      {
        if( !tabElement.hasClass('event_tab_active') )
          tabElement.addClass('event_tab_active');
        
        $('event_' + tabName).style.display = "block";
        Cookie.write('eventVisibleTab', bind.visibleTab = tabName);
      }
      else
      {
        if( tabElement.hasClass('event_tab_active') )
          tabElement.removeClass('event_tab_active');
        
        var otherTabName = tabElement.id.replace('_tabs_', '_');
        $(otherTabName).style.display = "none";
      }
    });
  },
  
  
  
  
  // Delete
  deleteShow: function(eventID)
  {
    this.currentConfirmID = ( eventID || this.eventInfo.event_id );
    TB_show(this.Base.Language.Translate(3000093), '#TB_inline?height=100&width=300&inlineId=confirmeventdelete', '', '../images/trans.gif');
  },
  
  
  deleteConfirm: function()
  {
    eventID = this.currentConfirmID;
    
    // Remove Smoothbox
    TB_remove();
    
    // Ajax
    var bind = this;
    var request = new Request.JSON({
      'method' : 'post',
      'url' : this.options.ajaxURL,
      'data' : {
        'task' : 'eventdelete',
        'event_id' : eventID
      },
      'onComplete':function(responseObject)
      {
        //alert($type(window.redirectOnDelete) + ' ' + $type(parent.window.redirectOnDelete) + ' ' + $type(parent.redirectOnDelete));
        
        if( $type(responseObject)!="object" || !responseObject.result || responseObject.result=="failure" )
        {
          alert(bind.Base.Language.Translate(3000153));
        }
        
        else
        {
          // event, user_event_edit - Redirect on delete
          if( $type(window.redirectOnDelete)=="function" )
          {
            window.redirectOnDelete();
          }
          
          // user_event - Remove row
          if( $('seEvent_' + eventID) )
          {
            $('seEvent_' + eventID).destroy();
            
            // Display no event message
            if( !$$('.seEvent').length && $('seEventNullMessage') )
            {
              $('seEventNullMessage').style.display = 'block';
            }
          }
          
          // user_event - Fix that calendar
          var eventMonthShow = $('seEventMonthShow_' + eventID);
          if( eventMonthShow )
          {
            var eventMonthDay = eventMonthShow.title;
            var eventCellElement = $('event_cell' + eventMonthDay);
            eventMonthShow.destroy();
            
            // Fix color
            if( eventCellElement && eventCellElement.className!='event_cell3' && !eventCellElement.getElements('a').length )
            {
              $('event_cell' + eventMonthDay).className = 'event_cell1'; 
            }
          }
        }
      }
    });
    
    request.send();
    
    // Reset
    currentConfirmDeleteID = 0;
  },
  
  
  
  
  // Accept Request
  memberAccept: function(userID)
  {
    // Ajax
    var bind = this;
    var request = new Request.JSON({
      'method' : 'post',
      'url' : this.options.ajaxURL,
      'data' : {
        'task' : 'eventmemberaccept',
        'event_id' : this.eventInfo.event_id,
        'user_id' : userID
      },
      'onComplete':function(responseObject)
      {
        if( $type(responseObject)!="object" || !responseObject.result || responseObject.result=="failure" )
        {
          alert(bind.Base.Language.Translate(3000153));
        }
        
        else
        {
          // Refresh window, at least for now
          if( document.event_members_form )
            document.event_members_form.submit();
          else
            window.location.reload( false );
        }
      }
    });
    
    request.send();
  },
  
  
  
  
  // Deny Request
  memberReject: function(userID)
  {
    // Ajax
    var bind = this;
    var request = new Request.JSON({
      'method' : 'post',
      'url' : this.options.ajaxURL,
      'data' : {
        'task' : 'eventmemberreject',
        'event_id' : this.eventInfo.event_id,
        'user_id' : userID
      },
      'onComplete':function(responseObject)
      {
        if( $type(responseObject)!="object" || !responseObject.result || responseObject.result=="failure" )
        {
          alert(bind.Base.Language.Translate(3000153));
        }
        
        else
        {
          // Refresh window, at least for now
          if( document.event_members_form )
            document.event_members_form.submit();
          else
            window.location.reload( false );
        }
      }
    });
    
    request.send();
  },
  
  
  
  
  // Deny Request
  memberDelete: function(userID)
  {
    this.currentConfirmID = userID;
    TB_show(this.Base.Language.Translate(3000154), '#TB_inline?height=100&width=300&inlineId=confirmeventmemberdelete', '', '../images/trans.gif');
  },
  
  
  memberDeleteConfirm: function()
  {
    userID = this.currentConfirmID;
    
    // Ajax
    var bind = this;
    var request = new Request.JSON({
      'method' : 'post',
      'url' : this.options.ajaxURL,
      'data' : {
        'task' : 'eventmemberdelete',
        'event_id' : this.eventInfo.event_id,
        'user_id' : userID
      },
      'onComplete':function(responseObject)
      {
        if( $type(responseObject)!="object" || !responseObject.result || responseObject.result=="failure" )
        {
          alert(bind.Base.Language.Translate(3000153));
        }
        
        else
        {
          // Refresh window, at least for now
          if( document.event_members_form )
            document.event_members_form.submit();
          else
            window.location.reload( false );
        }
      }
    });
    
    request.send();
  },
  
  
  
  
  // Cancel Invite
  memberCancel: function(userID)
  {
    this.currentConfirmID = userID;
    TB_show(this.Base.Language.Translate(3000223), '#TB_inline?height=100&width=300&inlineId=confirmeventmembercancel', '', '../images/trans.gif');
  },
  
  
  memberCancelConfirm: function()
  {
    userID = this.currentConfirmID;
    
    // Ajax
    var bind = this;
    var request = new Request.JSON({
      'method' : 'post',
      'url' : this.options.ajaxURL,
      'data' : {
        'task' : 'eventmembercancel',
        'event_id' : this.eventInfo.event_id,
        'user_id' : userID
      },
      'onComplete':function(responseObject)
      {
        if( $type(responseObject)!="object" || !responseObject.result || responseObject.result=="failure" )
        {
          alert(bind.Base.Language.Translate(3000153));
        }
        
        else
        {
          // Refresh window, at least for now
          if( document.event_members_form )
            document.event_members_form.submit();
          else
            window.location.reload( false );
        }
      }
    });
    
    request.send();
  },
  
  
  
  
  // Invite
  memberInvitePopulate: function(eventID)
  {
    // Ajax
    var bind = this;
    var request = new Request.JSON({
      'method' : 'post',
      'url' : this.options.ajaxURL,
      'data' : {
        'task' : 'getfriends',
        'event_id' : this.eventInfo.event_id
      },
      'onComplete':function(responseObject)
      {
        if( $type(responseObject)!="object" || !responseObject.result || responseObject.result=="failure" )
        {
          alert(bind.Base.Language.Translate(3000153));
        }
        
        else
        {
          bind.memberInviteShow(eventID, $H(responseObject.friends));
        }
      }
    });
    
    request.send();
  },
  
  
  memberInviteShow: function(eventID, friendsObject)
  {
    this.currentConfirmID = eventID;
    this.currentInvites = new Array();
    
    // Remove any existing friends
    $('invite_friendlist').empty();
    
    // Generate HTML
    var bind = this;
    if( $A(friendsObject.getKeys()).length>0 )
    {
      friendsObject.each(function(userDisplayName, userID)
      {
        var newDiv = new Element("div", {'id' : 'friend_div_'+userID});
        var newCheckbox = new Element("input", {'type' : 'checkbox', 
          'id' : 'friend_link_'+userID,
          'name' : 'invites[]',
          'value' : userID,
          'class' : 'checkbox',
          'onchange' : 'parent.SocialEngine.Event.memberInviteUpdate(this.value, this.checked);'
        }).inject(newDiv);
        
        var newLabel = new Element("label", {'for' : 'friend_link_'+userID,
          'html' : userDisplayName
        }).inject(newDiv);
        
        newDiv.inject($('invite_friendlist'));
      });
      
      //$('eventMemberInviteSelectAll').onclick = '';
      
      $('inviteForm').style.display = '';
      $('noFriends').style.display = 'none';
      $('inviteResults').style.display = 'none';
    }
    
    else
    {
      $('inviteForm').style.display = 'none';
      $('noFriends').style.display = '';
      $('inviteResults').style.display = 'none';
    }
    
    TB_show(this.Base.Language.Translate(3000225), '#TB_inline?height=350&width=300&inlineId=eventmemberinvite', '', '../images/trans.gif');
  },
  
  
  memberInviteUpdate: function(userID, state)
  {
    //alert(userID + ', ' + state);
    state = ( state ? true : false );
    if( state && !this.currentInvites.contains(userID) )
      this.currentInvites.include(userID);
    else if( !state && this.currentInvites.contains(userID) )
      this.currentInvites.erase(userID);
  },
  
  
  memberInviteSend: function()
  {
    // Ajax
    var bind = this;
    var request = new Request.JSON({
      'method' : 'post',
      'url' : this.options.ajaxURL,
      'data' : {
        'task' : 'eventmemberinvite',
        'event_id' : this.eventInfo.event_id,
        'invites' : this.currentInvites
      },
      'onComplete':function(responseObject)
      {
        if( $type(responseObject)!="object" || !responseObject.result || responseObject.result=="failure" )
        {
          alert(bind.Base.Language.Translate(3000153));
        }
        
        else
        {
          // Show invites sent
          $('inviteResults').innerHTML = bind.Base.Language.TranslateFormatted(3000229, [responseObject.invites_sent]);
          $('inviteForm').style.display = 'none';
          $('noFriends').style.display = 'none';
          $('inviteResults').style.display = '';
          
          TB_show(bind.Base.Language.Translate(3000225), '#TB_inline?height=350&width=300&inlineId=eventmemberinvite', '', '../images/trans.gif');
          
          // Refresh window, at least for now
          (function(){
          if( document.event_members_form )
            document.event_members_form.submit();
          else
            window.location.reload( false );
          }).delay(500);
        }
      }
    });
    
    request.send();
  },
  
  
  
  
  
  
  
  // RSVP
  rsvpShow: function(eventID)
  {
    this.currentConfirmID = ( eventID || this.eventInfo.event_id );
    TB_show(this.Base.Language.Translate(3000097), '#TB_inline?height=120&width=300&inlineId=confirmeventrsvp', '', '../images/trans.gif');
  },
  
  
  rsvpConfirm: function(eventRsvp)
  {
    eventID = ( this.currentConfirmID || this.eventInfo.event_id );
    
    // Remove Smoothbox
    if( $('TB_overlay') ) TB_remove();
    
    // Ajax
    var bind = this;
    var request = new Request.JSON({
      'method' : 'post',
      'url' : this.options.ajaxURL,
      'data' : {
        'task' : 'eventrsvp',
        'event_id' : eventID,
        'event_rsvp' : eventRsvp
      },
      'onComplete':function(responseObject)
      {
        if( $type(responseObject)!="object" || !responseObject.result || responseObject.result=="failure" )
        {
          alert(bind.Base.Language.Translate(3000153));
        }
        
        else
        {
          // event - Switch profile buttons (for join)
          if( $('eventProfileMenuLeave') )
          {
            // Show leave button for all but leader
            if( bind.UserRank<3 )
              $('eventProfileMenuLeave').style.display = '';
            // Show invite button if allowed
            if( bind.eventInfo.event_invite )
              $('eventProfileMenuInvite').style.display = '';
            //$('eventProfileMenuRSVP').style.display = '';
          }
          
          // event - Save success messages
          if( $('seEventProfileRSVPSuccess') )
          {
            $('seEventProfileRSVPSuccess').style.display = 'block';
            $('seEventProfileRSVPSuccess').slide('hide').slide('in');
            (function(){ $('seEventProfileRSVPSuccess').slide('show').slide('out'); }).delay(2000);
          }
          
          // user_event - Switch buttons (for join)
          if( $('seEvent_' + eventID) )
          {
            // User should never be able to join an invite only event through the user_event page
            $('seEvent_' + eventID).getElement('.seEventUserOptionJoin').style.display = 'none';
            $('seEvent_' + eventID).getElement('.seEventUserOptionRsvp').style.display = '';
            $('seEvent_' + eventID).getElement('.seEventUserOptionLeave').style.display = '';
            $('seEvent_' + eventID).getElement('.seEventStatusAccept').style.display = 'none';
            $('seEvent_' + eventID).getElement('.seEventStatusRSVP').style.display = '';
          }
          
          // user_event - Update response
          if( $('seEventRSVP_' + eventID) )
          {
            $('seEventRSVP_' + eventID).innerHTML = bind.Base.Language.Translate(bind.rsvpLanguageVars[eventRsvp]);
          }
        }
      }
    });
    
    request.send();
    
    // Reset
    this.currentConfirmID = 0;
  },
  
  
  
  
  // Leave
  leaveShow: function(eventID)
  {
    this.currentConfirmID = ( eventID || this.eventInfo.event_id );
    TB_show(this.Base.Language.Translate(3000219), '#TB_inline?height=100&width=300&inlineId=confirmeventleave', '', '../images/trans.gif');
  },
  
  
  leaveConfirm: function()
  {
    eventID = this.currentConfirmID;
    
    // Remove Smoothbox
    TB_remove();
    
    // Ajax
    var bind = this;
    var request = new Request.JSON({
      'method' : 'post',
      'url' : this.options.ajaxURL,
      'data' : {
        'task' : 'eventleave',
        'event_id' : eventID
      },
      'onComplete':function(responseObject)
      {
        if( $type(responseObject)!="object" || !responseObject.result || responseObject.result=="failure" )
        {
          alert(bind.Base.Language.Translate(3000153));
        }
        
        else
        {
          // event - Show/hide profile menu icons
          if( $('eventProfileMenuLeave') )
          {
            if( bind.eventInfo.event_inviteonly )
            {
              $('eventProfileMenuRequest').style.display = '';
              $('eventProfileMenuRSVP').style.display = 'none';
            }
            else
            {
              $$('.seEventProfileRSVP').each(function(radioElement) { radioElement.checked = false; });
            }
            $('eventProfileMenuInvite').style.display = 'none';
            $('eventProfileMenuLeave').style.display = 'none';
          }
          
          // user_event - Remove event row
          if( $('seEvent_' + eventID) )
          {
            $('seEvent_' + eventID).destroy();
          }
          
          // user_event - Fix that calendar
          var eventMonthShow = $('seEventMonthShow_' + eventID);
          if( eventMonthShow )
          {
            var eventMonthDay = eventMonthShow.title;
            var eventCellElement = $('event_cell' + eventMonthDay);
            eventMonthShow.destroy();
            
            // Fix color
            if( eventCellElement && eventCellElement.className!='event_cell3' && !eventCellElement.getElements('a').length )
            {
              $('event_cell' + eventMonthDay).className = 'event_cell1'; 
            }
          }
        }
      }
    });
    
    request.send();
    
    // Reset
    this.currentConfirmID = 0;
  },
  
  
  
  
  // Cancel
  cancelShow: function(eventID)
  {
    this.currentConfirmID = ( eventID || this.eventInfo.event_id );
    TB_show(this.Base.Language.Translate(3000170), '#TB_inline?height=100&width=300&inlineId=confirmeventrequestcancel', '', '../images/trans.gif');
  },
  
  
  cancelConfirm: function()
  {
    eventID = this.currentConfirmID;
    
    // Remove Smoothbox
    TB_remove();
    
    // Ajax
    var bind = this;
    var request = new Request.JSON({
      'method' : 'post',
      'url' : this.options.ajaxURL,
      'data' : {
        'task' : 'eventrequestcancel',
        'event_id' : eventID
      },
      'onComplete':function(responseObject)
      {
        if( $type(responseObject)!="object" || !responseObject.result || responseObject.result=="failure" )
        {
          alert(bind.Base.Language.Translate(3000153));
        }
        
        else
        {
          // event - Show/hide profile menu icons
          if( $('eventProfileMenuRequest') )
          {
            $('eventProfileMenuRequest').style.display = '';
            $('eventProfileMenuCancel').style.display = 'none';
          }
          
          // user_event - Remove event row
          if( $('seEvent_' + eventID) )
          {
            $('seEvent_' + eventID).destroy();
          }
          
          // user_event - Fix that calendar
          var eventMonthShow = $('seEventMonthShow_' + eventID);
          if( eventMonthShow )
          {
            var eventMonthDay = eventMonthShow.title;
            var eventCellElement = $('event_cell' + eventMonthDay);
            eventMonthShow.destroy();
            
            // Fix color
            if( eventCellElement && eventCellElement.className!='event_cell3' && !eventCellElement.getElements('a').length )
            {
              $('event_cell' + eventMonthDay).className = 'event_cell1'; 
            }
          }
        }
      }
    });
    
    request.send();
    
    // Reset
    this.currentConfirmID = 0;
  },
  
  
  
  
  // Join
  join: function(eventID, eventRSVP)
  {
    if( !eventID ) eventID = this.eventInfo.event_id;
    
    // Remove Smoothbox - for month view, might mess things up?
    if( $('TB_overlay') ) TB_remove();
    
    
    // Ajax
    var bind = this;
    var request = new Request.JSON({
      'method' : 'post',
      'url' : this.options.ajaxURL,
      'data' : {
        'task' : 'eventjoin',
        'event_id' : eventID
      },
      'onComplete':function(responseObject)
      {
        if( $type(responseObject)!="object" || !responseObject.result || responseObject.result=="failure" )
        {
          alert(bind.Base.Language.Translate(3000153));
        }
        
        else
        {
          // event - Switch profile buttons
          if( $('eventProfileMenuLeave') )
          {
            if( bind.eventInfo.event_inviteonly )
            {
              $('eventProfileMenuRequest').style.display = 'none';
              $('eventProfileMenuCancel').style.display = '';
            }
            
            else
            {
              $('eventProfileMenuLeave').style.display = '';
              $('eventProfileMenuRSVP').style.display = '';
              if( bind.eventInfo.event_invite )
                $('eventProfileMenuInvite').style.display = '';
            }
          }
          
          // user_event - Switch buttons
          else if( $('seEvent_' + eventID) )
          {
            // User should never be able to join an invite only event through the user_event page
            $('seEvent_' + eventID).getElement('.seEventUserOptionJoin').style.display = 'none';
            $('seEvent_' + eventID).getElement('.seEventUserOptionRsvp').style.display = '';
            $('seEvent_' + eventID).getElement('.seEventUserOptionLeave').style.display = '';
            $('seEvent_' + eventID).getElement('.seEventStatusAccept').style.display = 'none';
            $('seEvent_' + eventID).getElement('.seEventStatusRSVP').style.display = '';
          }
          
          // Send RSVP if set
          if( $type(eventRSVP) )
          {
            bind.rsvpConfirm(eventRSVP);
          }
        }
      }
    });
    
    request.send();
    
    // Reset
    this.currentConfirmID = 0;
  },
  
  
  
  
  // Request
  request: function(eventID)
  {
    if( !eventID ) eventID = this.eventInfo.event_id;
    
    // Ajax
    var bind = this;
    var request = new Request.JSON({
      'method' : 'post',
      'url' : this.options.ajaxURL,
      'data' : {
        'task' : 'eventrequestsend',
        'event_id' : eventID
      },
      'onComplete':function(responseObject)
      {
        if( $type(responseObject)!="object" || !responseObject.result || responseObject.result=="failure" )
        {
          alert(bind.Base.Language.Translate(3000153));
        }
        
        else
        {
          // event - Switch profile buttons
          if( $('eventProfileMenuRequest') )
          {
            $('eventProfileMenuRequest').style.display = 'none';
            $('eventProfileMenuCancel').style.display = '';
          }
        }
      }
    });
    
    request.send();
    
    // Reset
    this.currentConfirmID = 0;
  }
  
});