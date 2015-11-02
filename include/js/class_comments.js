
/* $Id: class_comments.js 269 2009-12-05 01:57:57Z john $ */

// This file will no longer be included by default. A minified version is included as part of core-min.js

// Required language variables: 39,155,175,182,183,184,185,187,784,787,829,830,831,832,833,834,835,854,856,891,1025,1026,1032,1034,1071

SocialEngineAPI.Comments = new Class({
  
  // Class
  Implements: [Options],
  
  
  
  
  // Properties
  Base: {},
  
  page: 1,
  
  total: 0,
  
  changed: false,
  
  isEditing: false,
  
  
  options: {
    // Controls ajax request options
    'ajaxURL' : 'misc_js.php',
    'ajaxMethod' : 'post',
    'ajaxSecure' : false,
    
    // Can the viewer comment
    'canComment' : false,
    'commentHTML' : false,
    'commentCode' : false,
    
    // Controls the height of the comment box
    'originalHeight' : 70,
    
    // The type of comment ex. 'media'
    'type' : false,
    
    // The column that identifies the type ex. 'media_id'
    'typeIdentifier' : false,
    
    // The actual ID of the object commented on
    'typeID' : false,

    // Pagination Info
    'paginate' : false,
    'cpp' : false,

    // Comment links
    'commentLinks' : {'reply' : false, 'walltowall' : false},
    
    // Some other stuff to identify the object
    'object_owner' : false,
    'object_owner_id' : false,
    'typeTab' : false,
    'typeCol' : false,
    'typeTabParent' : false,
    'typeColParent' : false,
    'typeChild' : false
  },
  
  
  
  
  // Methods
  initialize: function(options)
  {
    this.setOptions(options);
    
    if( this.options.initialTotal ) this.total = this.options.initialTotal;

    var bind = this;
    window.addEvent('domready', function()
    {
      bind.showPostComment();
      
      bind.options.originalHeight = textarea_autogrow('comment_body');
      bind.getComments(1);
    });
  },
  
  
  
  
  showPostComment: function()
  {
    var innerHTML = "";
    
    // POST COMMENT
    innerHTML += 
      "<div class='comment_headline'>" +
        this.Base.Language.Translate(854) +
        " (<span class='tc' id='" + this.options.type + '_' + this.options.typeID + '_totalcomments' + "'>" + this.total + "</span>)" +
      "</div>";
      
    if( this.options.canComment )
    {
      innerHTML += 
        "<form action='misc_js.php' method='post' target='ajaxframe' name='comment_post_form'>" +
          "<div class='profile_postcomment'>" +
            "<textarea name='comment_body' id='comment_body' cols='25' class='comment_area'>" + this.Base.Language.Translate(829) + "</textarea>";
      
      if( this.options.commentHTML ) innerHTML += 
            "<div style='margin-top: 5px;'>" +
              this.Base.Language.TranslateFormatted(1034, [this.options.commentHTML]) +
            "</div>";
      
      if( this.options.commentCode ) innerHTML += 
            "<div style='float: left; margin-top: 5px;'>" +
              "<a href='javascript:void(0);' onClick=\"this.blur();$('secure_image').src=$('secure_image').src+'?'+(new Date()).getTime();\">" +
                "<img src='./images/secure.php' id='secure_image' border='0' height='20' width='67' class='signup_code'>" + 
              "</a>" +
              " <input type='text' name='comment_secure' id='comment_secure' class='text' size='6' maxlength='10' /> " +
              "<img src='./images/icons/tip.gif' border='0' class='Tips1' style='vertical-align: middle;' title='" + this.Base.Language.Translate(856) + "' />" +
            "</div>";
      
      innerHTML += 
            "<div style='text-align: right; margin-top: 5px;'>" +
            "<input type='submit' id='comment_submit' class='button' value='" + this.Base.Language.Translate(833) + "' />" +
            "<input type='hidden' name='task' value='comment_post' />" +
            "<input type='hidden' name='type' value='" + this.options.type + "' />" +
            "<input type='hidden' name='iden' value='" + this.options.typeIdentifier + "' />" +
            "<input type='hidden' name='value' value='" + this.options.typeID + "' />";
      
      if( this.options.object_owner && this.options.object_owner_id )
      {
        innerHTML += '<input type="hidden" name="object_owner" value="' + this.options.object_owner + '">' +
		     '<input type="hidden" name="object_owner_id" value="' + this.options.object_owner_id + '">';
      } else {
        innerHTML += '<input type="hidden" name="user" value="' + this.Base.Owner.user_info.user_username + '">';
      }

      if( this.options.typeTab ) innerHTML += 
            "<input type='hidden' name='tab' value='" + this.options.typeTab + "'>";
      
      if( this.options.typeCol ) innerHTML += 
            "<input type='hidden' name='col' value='" + this.options.typeCol + "'>";
      
      if( this.options.typeTabParent ) innerHTML += 
          '<input type="hidden" name="tab_parent" value="' + this.options.typeTabParent + '">';
      
      if( this.options.typeColParent ) innerHTML += 
          '<input type="hidden" name="col_parent" value="' + this.options.typeColParent + '">';
      
      if( this.options.typeChild ) innerHTML += 
          '<input type="hidden" name="child" value="1">';
      
      innerHTML += 
            "</div>" +
            "<div id='comment_error' style='color: #FF0000; display: none;'></div>" +
          "</div>" +
        "</form>";
    }
    
    // DELETE COMMENT
    innerHTML += 
      '<div style="display: none;" id="confirmcommentdelete">' +
        '<div style="margin-top: 10px;">' +
          this.Base.Language.Translate(1026) +
        '</div>' +
        '<br />' +
        '<form action="misc_js.php" method="post" target="ajaxframe">' +
        '<input type="submit" class="button" value="' + this.Base.Language.Translate(175) + '" onClick="parent.TB_remove();"> ' +
        '<input type="button" class="button" value="' + this.Base.Language.Translate(39) + '" onClick="parent.TB_remove();">' +
        '<input type="hidden" name="task" value="comment_delete">' +
        '<input type="hidden" name="comment_id" id="del_comment_id" value="0">' +
        '<input type="hidden" name="type" value="' + this.options.type + '">' +
        '<input type="hidden" name="iden" value="' + this.options.typeIdentifier + '">' +
        '<input type="hidden" name="value" value="' + this.options.typeID + '">';

    if( this.options.object_owner && this.options.object_owner_id )
    {
      innerHTML += '<input type="hidden" name="object_owner" value="' + this.options.object_owner + '">' +
		   '<input type="hidden" name="object_owner_id" value="' + this.options.object_owner_id + '">';
    } else {
      innerHTML += '<input type="hidden" name="user" value="' + this.Base.Owner.user_info.user_username + '">';
    }

    if( this.options.typeTab ) innerHTML += 
        '<input type="hidden" name="tab" value="' + this.options.typeTab + '">';
    
    if( this.options.typeCol ) innerHTML += 
        '<input type="hidden" name="col" value="' + this.options.typeCol + '">';
    
    if( this.options.typeTabParent ) innerHTML += 
        '<input type="hidden" name="tab_parent" value="' + this.options.typeTabParent + '">';
    
    if( this.options.typeColParent ) innerHTML += 
        '<input type="hidden" name="col_parent" value="' + this.options.typeColParent + '">';
    
    if( this.options.typeChild ) innerHTML += 
        '<input type="hidden" name="child" value="1">';

  
    
    innerHTML += 
        '</form>' +
      '</div>';
    
    
    
    var postCommentContainerElement = $(this.options.type + '_' + this.options.typeID + '_postcomment');
    postCommentContainerElement.innerHTML = innerHTML;
    
    
    // Add events
    var bind = this;
    if( this.options.canComment )
    {
      postCommentContainerElement.getElement('form').addEvent('submit', function(event)   { bind.checkText(event);  });
      postCommentContainerElement.getElement('textarea').addEvent('focus', function()     { bind.removeText(this);  });
      postCommentContainerElement.getElement('textarea').addEvent('blur', function()      { bind.addText(this);     });
      
      // New ajax-only functionality
      postCommentContainerElement.getElement('form').addEvent('submit', function(event) { bind.doCommentPost(event); });
    }
    
  },
  
  
  
  
  doCommentPost: function(e)
  {
    var event = new Event(e);
    
    // Build data
    var postData = {
      'task' : 'comment_post',
      'type' : this.options.type,
      'iden' : this.options.typeIdentifier,
      'value' : this.options.typeID,
      'tab' : this.options.typeTab,
      'col' : this.options.typeCol
    };
    
    if( this.options.typeTabParent )
      postData.tab_parent = this.options.typeTabParent;
    if( this.options.typeColParent )
      postData.col_parent = this.options.typeColParent;
    if( this.options.typeChild )
      postData.child = this.options.typeChild;
    
    if( this.options.object_owner && this.options.object_owner_id )
    {
      postData.object_owner = this.options.object_owner;
      postData.object_owner_id = this.options.object_owner_id;
    }
    else
    {
      postData.user = this.Base.Owner.user_info.user_username;
    }
    
    if( $type(document.comment_post_form.comment_body) )
      postData.comment_body = document.comment_post_form.comment_body.value;
    if( $type(document.comment_post_form.comment_secure) )
      postData.comment_secure = document.comment_post_form.comment_secure.value;
    
    var bind = this;
    var request = new Request.JSON({
      'url' : this.options.ajaxURL,
      'method' : this.options.ajaxMethod,
      'secure' : this.options.ajaxSecure,
      'data' : postData,
      'onComplete' : function(responseObject, responseText)
      {
        bind.addComment(responseObject.is_error, responseObject.comment_body, responseObject.comment_date);
      }
    }).send();
    
    
    // stop form submission
    event.stop();
  },
  
  
  
  
  doCommentEdit: function()
  {
    //var event = new Event(e);
    
    // Build data
    var postData = {
      'task' : 'comment_edit',
      'type' : this.options.type,
      'iden' : this.options.typeIdentifier,
      'value' : this.options.typeID,
      'user' : this.Base.Owner.user_info.user_username
    };
    
    if( $type(document.editCommentForm.comment_id) )
      postData.comment_id = document.editCommentForm.comment_id.value;
    if( $type(document.editCommentForm.comment_edit) )
      postData.comment_edit = document.editCommentForm.comment_edit.value;
    
    
    var bind = this;
    var request = new Request.JSON({
      'url' : this.options.ajaxURL,
      'method' : this.options.ajaxMethod,
      'secure' : this.options.ajaxSecure,
      'data' : postData,
      'onComplete' : function(responseObject, responseText)
      {
        bind.getComments();
      }
    }).send();
    
    // stop form submission
    //event.stop();
  },
  
  
  
  
  doCommentDelete: function(e, commentID)
  {
    var event = new Event(e);
    
    // Build data
    var postData = {
      'task' : 'comment_delete',
      'comment_id' : commentID,
      'type' : this.options.type,
      'iden' : this.options.typeIdentifier,
      'value' : this.options.typeID,
      'tab' : this.options.typeTab,
      'col' : this.options.typeCol,
      'user' : this.Base.Owner.user_info.user_username
    };
    
    if( this.options.typeTabParent )
      postData.tab_parent = this.options.typeTabParent;
    if( this.options.typeColParent )
      postData.col_parent = this.options.typeColParent;
    if( this.options.typeChild )
      postData.child = this.options.typeChild;
    
    if( this.options.object_owner )
      postData.object_owner = this.options.object_owner;
    if( this.options.object_owner_id )
      postData.object_owner_id = this.options.object_owner_id;
    
    if( $type(document.commentDeleteForm.comment_body) )
      postData.comment_body = document.commentDeleteForm.comment_body.value;
    if( $type(document.commentDeleteForm.comment_secure) )
      postData.comment_secure = document.commentDeleteForm.comment_secure.value;
    
    // Send data
    var bind = this;
    var request = new Request.JSON({
      'url' : this.options.ajaxURL,
      'method' : this.options.ajaxMethod,
      'secure' : this.options.ajaxSecure,
      'data' : postData,
      'onComplete' : function(responseObject, responseText)
      {
        bind.getComments();
      }
    }).send();
    
    // stop form submission
    event.stop();
  },
  
  
  
  
  getComments: function(direction)
  {
    if( direction=='next' )
      this.page++;
    else if( direction=='previous' )
      this.page--;
    else if( $type(direction) )
      this.page = direction;
    
    if( this.options.paginate ) {
      window.scroll(0,0);
    } else {
      this.options.cpp = this.total;
    }

    if( this.options.object_owner && this.options.object_owner_id )
    {
      var object_owner = this.options.object_owner;
      var object_owner_id = this.options.object_owner_id;
      var user = '';
    } else {
      var object_owner = '';
      var object_owner_id = '';
      var user = this.Base.Owner.user_info.user_username;      
    }

    // AJAX
    var bind = this;
    var request = new Request.JSON({
      'url' : this.options.ajaxURL,
      'method' : this.options.ajaxMethod,
      'secure' : this.options.ajaxSecure,
      'data' : {
        'task'  : 'comment_get',
        'user'  : user,
        'object_owner' : object_owner,
        'object_owner_id' : object_owner_id,
        'type'  : this.options.type,
        'iden'  : this.options.typeIdentifier,
        'value' : this.options.typeID,
        'cpp'   : this.options.cpp,
        'p'     : this.page
      },
      'onComplete' : function(responseObject, responseText)
      {
        bind.updateComments(responseObject);
      }
    });
    
    request.send();
  },
  
  
  
  
  // THIS FUNCTION UPDATES THE COMMENTS
  updateComments: function(responseObject)
  {
    if( $type(responseObject)!="object" )
    {
      alert('There was an error processing the request.');
      return false;
    }
    
    
    // Prepare
    this.total  = parseInt(responseObject.total_comments) || 0;
    this.page   = responseObject.p;
    
    var maxpage = responseObject.maxpage;
    var p_start = responseObject.p_start;
    var p_end   = responseObject.p_end;
    
    var totalCommentElement     = $(this.options.type + '_' + this.options.typeID + '_totalcomments');
    var commentContainerElement = $(this.options.type + '_' + this.options.typeID + '_comments');
    var comments = $H(responseObject.comments);
    
    // UPDATE TOTAL COMMENTS AND PAGE VARS
    totalCommentElement.innerHTML = this.total;

    // CREATE DIV TO HOLD COMMENT BODY FOR CLEANING
    var commentBodyDiv = document.createElement('div');
    
    // EMPTY CONTAINER 
    commentContainerElement.empty();
    
    // LOOP OVER COMMENTS
    var bind = this;
    if(bind.Base.Core.settings.setting_url) { var querySeparator = '?'; } else { var querySeparator = '&'; }
    comments.each(function(commentObject, commentID)
    {
      var newComment = new Element('div', {
        'id' : 'comment_' + commentID
      });
      
      // BUILD COMMENT
      var newCommentInnerHTML = "<div style='margin-top: 10px; margin-bottom: 20px;'>";
      
      // AUTHOR PHOTO
      if( commentObject.comment_authoruser_id && commentObject.comment_authoruser_exists )
        newCommentInnerHTML += "<div style='float: left; text-align: center; width: 90px;'><a href='" + commentObject.comment_authoruser_url + "'><img src='" + commentObject.comment_authoruser_photo + "' class='photo' width='" + commentObject.comment_authoruser_photo_width + "' border='0'></a></div>";
      else
        newCommentInnerHTML += "<div style='float: left; text-align: center; width: 90px;'><img src='./images/nophoto.gif' class='photo' width='75' border='0'></div>";
      
      newCommentInnerHTML += "<div style='overflow: hidden;'>";
      
      // AUTHOR NAME/LINK
      if( !commentObject.comment_authoruser_id )
        newCommentInnerHTML += "<div class='profile_comment_author'><b>" + bind.Base.Language.Translate(835) + "</b></div>";
      else if( !commentObject.comment_authoruser_exists )
        newCommentInnerHTML += "<div class='profile_comment_author'><b>" + bind.Base.Language.Translate(1071) + "</b></div>";
      else
        newCommentInnerHTML += "<div class='profile_comment_author'><a href='" + commentObject.comment_authoruser_url + "'><b>" + commentObject.comment_authoruser_displayname + "</b></a></div>";
      
      // COMMENT DATE
      newCommentInnerHTML += "<div class='profile_comment_date'>" + commentObject.comment_date + "</div>";
      
      // COMMENT BODY
      newComment.setProperty('html', commentObject.comment_body);
      
      newCommentInnerHTML += "<div class='profile_comment_body' id='profile_comment_body_" + commentID + "'>" + commentObject.comment_body + "&nbsp;</div>";
      newCommentInnerHTML += "<div class='profile_comment_links'>";
      
      // COMMENT LINKS
      var links = new Array();
      if( bind.Base.Viewer.user_exists && commentObject.comment_authoruser_id && commentObject.comment_authoruser_exists )
      {

        // REPLY LINK (VISIBLE ONLY TO OWNER)
	if(bind.options.commentLinks.reply && bind.Base.Viewer.user_info.user_id == bind.Base.Owner.user_info.user_id && bind.Base.Viewer.user_info.user_id != commentObject.comment_authoruser_id) 
        {
          links.push("<a href='profile.php?user="+commentObject.comment_authoruser_username+"&v=comments'>" + bind.Base.Language.Translate(787) + "</a>");
          //links.push("<a href='"+commentObject.comment_authoruser_url+querySeparator+"v=comments'>" + bind.Base.Language.Translate(787) + "</a>");
        }

	// WALL-TO-WALL (VISIBLE ONLY IF USER CAN SEE AUTHOR'S PROFILE)
	if(bind.options.commentLinks.walltowall && commentObject.comment_authoruser_id != bind.Base.Owner.user_info.user_id && commentObject.comment_authoruser_private == false) 
	{ 
	  links.push("<a href=\"javascript:TB_show('" + bind.Base.Language.Translate(1032) + "', 'profile_comments.php?user=" + bind.Base.Owner.user_info.user_username +"&user2=" + commentObject.comment_authoruser_username + "&TB_iframe=true&height=450&width=550', '', './images/trans.gif');\">" + bind.Base.Language.Translate(891) + "</a>");
        }

        // MESSAGE (VISIBLE IF USER IS NOT AUTHOR)
        if( commentObject.comment_authoruser_id!=bind.Base.Viewer.user_info.user_id )
        {
          links.push("<a href=\"javascript:TB_show('" + bind.Base.Language.Translate(784) + "', 'user_messages_new.php?to_user=" + commentObject.comment_authoruser_displayname + "&to_id=" + commentObject.comment_authoruser_username + "&TB_iframe=true&height=400&width=450', '', './images/trans.gif');\">" + bind.Base.Language.Translate(834) + "</a>");
        }
        
        // EDIT (VISIBLE IF USER IS AUTHOR)
        if( commentObject.comment_authoruser_id==bind.Base.Viewer.user_info.user_id )
        {
          links.push("<a class=\"commentEditLink\" href=\"javascript:void(0);\" id='comment_edit_link_" + commentID + "'>" + bind.Base.Language.Translate(187) + "</a>");
        }
        
      }

      // DELETE (VISIBLE IF USER IS AUTHOR OR USER IS OWNER)
      if((commentObject.comment_authoruser_exists && commentObject.comment_authoruser_id == bind.Base.Viewer.user_info.user_id) || (bind.Base.Viewer.user_exists && bind.Base.Viewer.user_info.user_id == bind.Base.Owner.user_info.user_id))
      {
        links.push("<a class=\"commentDeleteLink\" href=\"javascript:void(0);\" id='comment_delete_link_" + commentID + "'>" + bind.Base.Language.Translate(155) + "</a>");
      }
      
      newCommentInnerHTML += links.join('&nbsp;-&nbsp;');
      newCommentInnerHTML += "&nbsp;</div></div></div>";
      
      // ADD NEW INNERHTML
      newComment.setProperty('html', newCommentInnerHTML);
      newComment.inject(commentContainerElement);
      
      // ADD EVENTS
      if( newComment.getElement('.commentEditLink') ) newComment.getElement('.commentEditLink').addEvent('click', function()
      {
        bind.editComment(commentID);
      });
      
      if( newComment.getElement('.commentDeleteLink') ) newComment.getElement('.commentDeleteLink').addEvent('click', function()
      {
        bind.confirmDelete(commentID);
      });
    });
    
    // CREATE PAGINATION DIV
    if(this.options.paginate && this.total > this.options.cpp) { 
      var commentPaginationTop = new Element('div', {'styles': {'text-align' : 'center'}});
      var commentPaginationBottom = new Element('div', {'styles': {'text-align' : 'center'}});
      if(this.page > 1) {
        var paginationHTMLTop = "<a href='javascript:void(0);' id='comment_last_page_top'>&#171; " + bind.Base.Language.Translate(182) + "</a>";
        var paginationHTMLBottom = "<a href='javascript:void(0);' id='comment_last_page_bottom'>&#171; " + bind.Base.Language.Translate(182) + "</a>";
      } else {
        var paginationHTMLTop = "<font class='disabled'>&#171; " + bind.Base.Language.Translate(182) + "</font>";
        var paginationHTMLBottom = "<font class='disabled'>&#171; " + bind.Base.Language.Translate(182) + "</font>";
      }
      if(p_start == p_end) {
        paginationHTMLTop += "&nbsp;|&nbsp; " + this.Base.Language.TranslateFormatted(184, [p_start, this.total]) + "&nbsp;|&nbsp;";
        paginationHTMLBottom += "&nbsp;|&nbsp; " + this.Base.Language.TranslateFormatted(184, [p_start, this.total]) + "&nbsp;|&nbsp;";
      } else {
        paginationHTMLTop += "&nbsp;|&nbsp; " + this.Base.Language.TranslateFormatted(185, [p_start, p_end, this.total]) + "&nbsp;|&nbsp;";
        paginationHTMLBottom += "&nbsp;|&nbsp; " + this.Base.Language.TranslateFormatted(185, [p_start, p_end, this.total]) + "&nbsp;|&nbsp;";
      }
      if(this.page != maxpage) {
        paginationHTMLTop += "<a href='javascript:void(0);' id='comment_next_page_top'>" + bind.Base.Language.Translate(183) + " &#187;</a>";
        paginationHTMLBottom += "<a href='javascript:void(0);' id='comment_next_page_bottom'>" + bind.Base.Language.Translate(183) + " &#187;</a>";
      } else {
        paginationHTMLTop += "<font class='disabled'>" + bind.Base.Language.Translate(183) + " &#187;</font>";
        paginationHTMLBottom += "<font class='disabled'>" + bind.Base.Language.Translate(183) + " &#187;</font>";
      }
      commentPaginationTop.setProperty('html', paginationHTMLTop);
      commentPaginationBottom.setProperty('html', paginationHTMLBottom);
      commentPaginationTop.inject(commentContainerElement, 'top');
      commentPaginationBottom.inject(commentContainerElement);

      // ADD EVENTS
      if( commentPaginationTop.getElement('a[id=comment_last_page_top]') ) commentPaginationTop.getElement('a[id=comment_last_page_top]').addEvent('click', function()
      {
        bind.getComments('previous');
      });

      if( commentPaginationBottom.getElement('a[id=comment_last_page_bottom]') ) commentPaginationBottom.getElement('a[id=comment_last_page_bottom]').addEvent('click', function()
      {
        bind.getComments('previous');
      });
      
      if( commentPaginationTop.getElement('a[id=comment_next_page_top]') ) commentPaginationTop.getElement('a[id=comment_next_page_top]').addEvent('click', function()
      {
        bind.getComments('next');
      });

      if( commentPaginationBottom.getElement('a[id=comment_next_page_bottom]') ) commentPaginationBottom.getElement('a[id=comment_next_page_bottom]').addEvent('click', function()
      {
        bind.getComments('next');
      });
    }

  },
  
  
  // Adds a comment
  addComment: function(is_error, comment_body, comment_date)
  {
    if( !this.options.canComment )
      return false;
    
    if( is_error )
    {
      $('comment_error').style.display = 'block';
      if( !comment_body.trim() )
      {
        this.addText($('comment_body'));
        $('comment_error').innerHTML = this.Base.Language.Translate(831);
      }
      else
      {
        $('comment_error').innerHTML = this.Base.Language.Translate(832);
      }
      $('comment_submit').value = this.Base.Language.Translate(833);
      $('comment_submit').disabled = false;
    }
    else
    {
      $('comment_error').style.display = 'none';
      $('comment_error').innerHTML = '';
      
      $('comment_body').value = '';
      $('comment_body').style.height = this.options.originalHeight + 'px';
      this.addText($('comment_body'));
      
      $('comment_submit').value = this.Base.Language.Translate(833);
      $('comment_submit').disabled = false;
      
      if( $('comment_secure') )
      {
        $('comment_secure').value = '';
        $('secure_image').src = $('secure_image').src + '?' + (new Date()).getTime();
      }
      
      // INPUT COMMENTS
      this.page = 1;
      this.total++;
      this.getComments();
    }
  },
  
  
  
  editComment: function(commentID)
  {
    var bind = this;
    if( this.isEditing ) return false;
    this.isEditing = true;
    
    
    //var commentContainerElement = $(this.options.type + '_' + this.options.typeID + '_comments');
    //var commentElement = commentContainerElement.getElement('profile_comment_body_' + commentID);
    var commentElement = $('profile_comment_body_' + commentID);
    
    var height = commentElement.offsetHeight + 10;
    var commentText = commentElement.innerHTML.replace(/<br>/gi, '\r\n').replace(/>/gi, '&gt;');
    
    var innerHTML = '';
    innerHTML += "<form action='misc_js.php' method='post' target='ajaxframe' name='editCommentForm'>";
    innerHTML += "<textarea name='comment_edit' id='comment_edit_" + commentID + "' style='height: " + height +" px; width: 100%;'>" + commentText + "</textarea>";
    innerHTML += "<input type='hidden' name='task' value='comment_edit'>";
    innerHTML += "<input type='hidden' name='comment_id' value='" + commentID + "'>";
    innerHTML += "<input type='hidden' name='type' value='" + this.options.type + "'>";
    innerHTML += "<input type='hidden' name='iden' value='" + this.options.typeIdentifier + "'>";
    innerHTML += "<input type='hidden' name='value' value='" + this.options.typeID + "'>";
    
    if( this.options.typeTab ) innerHTML += 
        '<input type="hidden" name="tab" value="' + this.options.typeTab + '">';
    
    if( this.options.typeCol ) innerHTML += 
        '<input type="hidden" name="col" value="' + this.options.typeCol + '">';
    
    if( this.options.typeTabParent ) innerHTML += 
        '<input type="hidden" name="tab_parent" value="' + this.options.typeTabParent + '">';
    
    if( this.options.typeColParent ) innerHTML += 
        '<input type="hidden" name="col_parent" value="' + this.options.typeColParent + '">';
    
    if( this.options.typeChild ) innerHTML += 
        '<input type="hidden" name="child" value="1">';
    
    
    innerHTML += "</form>";
    
    
    // Inject
    commentElement.innerHTML = innerHTML;
    textarea_autogrow('comment_edit_' + commentID);
    $('comment_edit_' + commentID).focus();
    
    
    // Add events
    $('comment_edit_' + commentID).addEvent('blur', function()
    {
      bind.doCommentEdit();
      bind.isEditing = false;
    });
  },
  
  
  
  confirmDelete: function(commentID)
  {
    $('del_comment_id').value = commentID;
    TB_show(this.Base.Language.Translate(1025), '#TB_inline?height=100&width=300&inlineId=confirmcommentdelete', '', '../images/trans.gif');
    
    var bind = this;
    $('TB_window').getElement('form').name = 'commentDeleteForm';
    $('TB_window').getElement('form').addEvent('submit', function(event) { bind.doCommentDelete(event, commentID); });
  },
  
  
  
  
  
  
  
  // UI Methods
  removeText: function(commentBody)
  {
    if( !this.changed )
    {
      commentBody.value = '';
      commentBody.style.color = '#000000';
      this.changed = true;
    }
  },
  
  
  
  addText: function(commentBody)
  {
    if( !commentBody.value.trim() )
    {
      commentBody.value = this.Base.Language.Translate(829);
      commentBody.style.color = '#888888';
      this.changed = false;
    }
  },
  
  
  
  checkText: function(event)
  {
    if( !this.changed )
      $('comment_body').value='';
    
    $('comment_submit').value = this.Base.Language.Translate(830);
    $('comment_submit').disabled = true;
  }
  
});
