
/* $Id: class_blog.js 5 2009-01-11 06:01:16Z john $ */

SocialEngineAPI.Blog = new Class({
  
  Base: {},
  
  
  options: {
    'ajaxURL' : 'blog_ajax.php'
  },
  
  
  currentConfirmDeleteID: 0,
  
  

  // Delete
  deleteBlog: function(blogEntryID)
  {
    // Display
    currentConfirmDeleteID = blogEntryID;
    TB_show(this.Base.Language.Translate(1500122), '#TB_inline?height=100&width=300&inlineId=confirmblogdelete', '', '../images/trans.gif');
  },
  
  deleteBlogConfirm: function()
  {
    blogEntryID = currentConfirmDeleteID;
    
    // Just refresh teh page instead
    //$('seBlogRow_'+blogEntryID).destroy();
    
    // Ajax
    var bind = this;
    var request = new Request.JSON({
      'method' : 'post',
      'url' : this.options.ajaxURL,
      'data' : {
        'task' : 'deleteblog',
        'blogentry_id' : blogEntryID
      },
      'onComplete':function(responseObject)
      {
        if( $type(responseObject)!="object" || !responseObject.result || responseObject.result=="failure" )
        {
          alert(bind.Base.Language.Translate(1500123));
        }
        
        window.location.reload( false );
      }
    });
    
    request.send();
    
    // Reset
    currentConfirmDeleteID = 0;
  },
  
  
  
  
  
  // Preview
  previewBlog: function()
  {
    var bind = this;
    var previewPane = $('entry_preview');
    var mainPane = $('entry_main');
    var previewElement = $('previewpane');
    var FCKInstance = FCKeditorAPI.GetInstance('blogentry_body');
    
    // Ajax
    var bind = this;
    var request = new Request({
      'method' : 'post',
      'url' : this.options.ajaxURL,
      'data' : {
        'task' : 'previewblog',
        'blogentry_title' : document.blogform.blogentry_title.value,
        'blogentry_body' : FCKInstance.GetHTML(),
        'blogentry_blogentrycat_id' : ( document.blogform.blogentry_blogentrycat_id ? document.blogform.blogentry_blogentrycat_id.value : 0 )
      },
      'onComplete':function(responseText)
      {
        previewElement.empty();
        previewElement.innerHTML = responseText;
        
        var tmpHTML = previewElement.getElement('.seBlogEntry').innerHTML;
        previewElement.innerHTML = tmpHTML + '<br />';
        
        TB_show(bind.Base.Language.Translate(1500067), '#TB_inline?height=400&width=600&inlineId=entry_preview', '', '../images/trans.gif');
      }
    });
    
    request.send();
  },
  
  
  
  
  // Subscriptions
  subscribeBlog: function(blogOwnerID)
  {
    // Ajax
    var bind = this;
    var request = new Request.JSON({
      'method' : 'post',
      'url' : this.options.ajaxURL,
      'data' : {
        'task' : 'subscribeblog',
        'user_id' : blogOwnerID
      },
      'onComplete':function(responseObject)
      {
        if( !responseObject || $type(responseObject)!="object" || responseObject.result=="failure" )
        {
          alert(bind.Base.Language.Translate(1500123));
        }
        else
        {
          // Blog page
          if( $(document).getElement('.seBlogSubscribe') )
          {
            $(document).getElement('.seBlogSubscribe').style.display = 'none';
            $(document).getElement('.seBlogUnsubscribe').style.display = '';
          }
        }
      }
    });
    
    request.send();
  },
  
  unsubscribeBlog: function(blogOwnerID)
  {
    // Ajax
    var bind = this;
    var request = new Request.JSON({
      'method' : 'post',
      'url' : this.options.ajaxURL,
      'data' : {
        'task' : 'unsubscribeblog',
        'user_id' : blogOwnerID
      },
      'onComplete':function(responseObject)
      {
        if( !responseObject || $type(responseObject)!="object" || responseObject.result=="failure" )
        {
          alert(bind.Base.Language.Translate(1500123));
        }
        else
        {
          // User page
          if( $('seBlogSubscriptionRow_'+blogOwnerID) )
          {
            $('seBlogSubscriptionRow_'+blogOwnerID).destroy();
          }
          
          // Blog page
          if( $(document).getElement('.seBlogSubscribe') )
          {
            $(document).getElement('.seBlogSubscribe').style.display = '';
            $(document).getElement('.seBlogUnsubscribe').style.display = 'none';
          }
        }
      }
    });
    
    request.send();
  }

});