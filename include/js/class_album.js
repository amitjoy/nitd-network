
/* $Id$ */

// Required language vars: 1000054 (delete)

SocialEngineAPI.Album = new Class({
  
  // Class
	Implements: [Options],
  
  
  
  // Properties
  Base: {},
  
  options: {
    'ajaxURL' : 'album_ajax.php',
    'ajaxMethod' : 'post',
    'ajaxSecure' : 'false',
    
    'albumId' : 0
  },
  
  currentConfirmDeleteID: 0,
  
  
  
  // Methods
  initialize: function(options)
  {
    this.setOptions(options);
  },
  
  
  
  // Album Delete
  deleteAlbum: function(album_id)
  {
    this.currentConfirmDeleteID = album_id;
    TB_show(this.Base.Language.Translate(1000054), '#TB_inline?height=100&width=300&inlineId=confirmdelete', '', '../images/trans.gif');
  },
  
  deleteAlbumConfirm: function()
  {
    var bind = this;
    var request = new Request.JSON({
      'url' : this.options.ajaxURL,
      'method' : this.options.ajaxMethod,
      'secure' : this.options.ajaxSecure,
      'data' : {
        'task' : 'album_delete',
        'album_id' : this.currentConfirmDeleteID
      },
      onComplete: function()
      {
        window.location = 'user_album.php';
      }
    }).send();
  },
  
  
  
  
  // Album Moveup
  moveupAlbum: function(album_id)
  {
    var request = new Request.JSON({
      'url' : this.options.ajaxURL,
      'method' : this.options.ajaxMethod,
      'secure' : this.options.ajaxSecure,
      'data' : {
        'task' : 'album_moveup',
        'album_id' : album_id
      },
      onComplete: function(responseObject)
      {
        if( $type(responseObject.result) && responseObject.result )
        {
          var cai = $('album_'+responseObject.current_album_id);
          var pai = $('album_'+responseObject.previous_album_id);
          if( cai && pai )
          {
            cai.inject(pai, 'before');
            var paia = pai.getElement('.seAlbumActionMoveup');
            var caia = cai.getElement('.seAlbumActionMoveup');
            if( paia && caia && paia.style.display=='none' )
            {
              paia.style.display = '';
              caia.style.display = 'none';
            }
          }
        }
      }
    }).send();
  },
  
  
  
  // Media Rotate
  rotateMedia: function(media_id, degrees)
  {
    var request = new Request.JSON({
      'url' : this.options.ajaxURL,
      'method' : this.options.ajaxMethod,
      'secure' : this.options.ajaxSecure,
      'data' : {
        'task' : 'media_rotate',
        'album_id' : this.options.albumId,
        'media_id' : media_id,
        'degrees' : degrees
      },
      onComplete: function(responseObject)
      {
        if( $type(responseObject.result) && responseObject.result )
        {
          $('file_'+media_id).src = responseObject.path;
        }
      }
    }).send();
  },
  
  
  
  // Media Moveup
  moveupMedia: function(media_id)
  {
    var request = new Request.JSON({
      'url' : this.options.ajaxURL,
      'method' : this.options.ajaxMethod,
      'secure' : this.options.ajaxSecure,
      'data' : {
        'task' : 'media_moveup',
        'album_id' : this.options.albumId,
        'media_id' : media_id
      },
      onComplete: function(responseObject)
      {
        if( $type(responseObject.result) && responseObject.result )
        {
          var cai = $('media_'+responseObject.current_media_id);
          var pai = $('media_'+responseObject.previous_media_id);
          if( cai && pai )
          {
            cai.inject(pai, 'before');
            var paia = pai.getElement('.seAlbumMediaActionMoveup');
            var caia = cai.getElement('.seAlbumMediaActionMoveup');
            if( paia && caia && paia.style.display=='none' )
            {
              paia.style.display = '';
              caia.style.display = 'none';
            }
          }
        }
      }
    }).send();
  },
  
  
  
  // Media Delete
  deleteMedia: function(media_id)
  {
    this.currentConfirmDeleteID = media_id;
    TB_show(this.Base.Language.Translate(1000054), '#TB_inline?height=100&width=300&inlineId=confirmdelete', '', '../images/trans.gif');
  },
  
  deleteMediaConfirm: function()
  {
    var bind = this;
    var request = new Request.JSON({
      'url' : this.options.ajaxURL,
      'method' : this.options.ajaxMethod,
      'secure' : this.options.ajaxSecure,
      'data' : {
        'task' : 'media_delete',
        'album_id' : this.options.albumId,
        'media_id' : this.currentConfirmDeleteID
      },
      onComplete: function()
      {
        window.location = 'user_album_update.php?album_id='+bind.options.albumId;
      }
    }).send();
  },
  
  
  
  // Media Cover
  coverMedia: function(media_id)
  {
    var bind = this;
    var request = new Request.JSON({
      'url' : this.options.ajaxURL,
      'method' : this.options.ajaxMethod,
      'secure' : this.options.ajaxSecure,
      'data' : {
        'task' : 'media_cover',
        'album_id' : this.options.albumId,
        'media_id' : media_id
      },
      onComplete: function(responseObject)
      {
        if( $type(responseObject.result) && responseObject.result )
        {
          $$('.seAlbumMediaActionCover').each(function(actionElement)
          {
            if( actionElement.id=='seAlbumMediaActionCover_'+media_id && actionElement.style.display!='none' )
            {
              actionElement.style.display = 'none';
            }
            else if( actionElement.style.display=='none' )
            {
              actionElement.style.display = '';
            }
          });
        }
      }
    }).send();
  },
  
  
  
  // Media move
  moveMedia: function(media_id, album_id)
  {
    var bind = this;
    var request = new Request.JSON({
      'url' : this.options.ajaxURL,
      'method' : this.options.ajaxMethod,
      'secure' : this.options.ajaxSecure,
      'data' : {
        'task' : 'media_move',
        'album_id' : album_id,
        'media_id' : media_id
      },
      onComplete: function(responseObject)
      {
        window.location = 'user_album_update.php?album_id='+bind.options.albumId;
      }
    }).send();
  }
  
  
});