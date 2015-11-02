
/* $Id: class_tags.js 130 2009-03-21 23:36:57Z john $ */

// This file will no longer be included by default. A minified version is included as part of core-min.js

// Required language variables: 39,1212,1213,1214,1215,1228

SocialEngineAPI.Tags = new Class({
  
  // Class
	Implements: [Options],
  
  
  
  
  // Properties
  Base: {},
  
  isTagging: false,

  newtag : false,

  tags : [],
  
  
  options: {
    // Controls ajax request options
    'ajaxURL' : 'misc_js.php',
    'ajaxMethod' : 'post',
    'ajaxSecure' : false,
    
    // Can the viewer media
    'canTag' : false,
    
    // The type of media ex. 'group'
    'type' : false,
    
    // The actual ID of the object being tagged
    'media_id' : false,

    // Path to the image
    'media_dir' : false,
    
    // Some other stuff to identify the object
    'object_owner' : false,
    'object_owner_id' : false
  },
  
  
  
  
  // Methods
  initialize: function(options)
  {
    this.setOptions(options);
    
    if( this.options.initialTotal ) this.total = this.options.initialTotal;

    var bind = this;
    window.addEvent('domready', function()
    {
      bind.showTagForm();
    });
  },
  
  
  
  showTagForm : function()
  {
    var innerHTML = "";
    var bind = this;

    if( this.options.canTag ) 
    {
      innerHTML +=
        "<div class='media_photo_tagform_titlebar'><img src='images/icons/photos16.gif' class='icon' />" + this.Base.Language.Translate(1212) + "</div>" +
        "<div style='padding: 7px;'>" +
        "<div style='text-align: left;'>" +
        "<div style='font-size: 7pt; margin-bottom: 5px;'>" + this.Base.Language.Translate(1213) + "</div>" +
        "<div style='text-align: center;'><input type='text' style='width: 130px; padding-left: 4px; font-size: 8pt;' name='media_photo_tag' id='media_photo_tag' class='text' maxlength='40'/></div>" +
        "</div>" +
        "<div id='media_photo_friendlist' class='media_photo_friendlist'>";
      
      if( bind.Base.Viewer.user_exists ) 
      {
        innerHTML += "<div><a href='javascript:void(0)' id='tag_me'>" + bind.Base.Viewer.user_displayname + bind.Base.Language.Translate(1214) + "</a></div>";
      }
      
      innerHTML += "</div>" +
        "<div>" +
        "<input type='button' class='button' name='save' id='tag_save' value='" + bind.Base.Language.Translate(1215) + "' />&nbsp;&nbsp;" +
        "<input type='button' class='button' id='tag_cancel' name='cancel' value='" + bind.Base.Language.Translate(39) + "' />" +
        "</div>" +
        "</div>";
      
      var newTagForm = new Element('div', {
        'id' : 'media_photo_tagform',
        'class' : 'media_photo_tagform',
        'html' : innerHTML
      });
      
      var mediaContainer = $('media_photo_div');
      newTagForm.inject(mediaContainer);
      
      // ADD EVENTS
      if( newTagForm.getElement('input[id=tag_cancel]') ) newTagForm.getElement('input[id=tag_cancel]').addEvent('click', function()
      {
        bind.cancelTag();
      });
      if( newTagForm.getElement('input[id=tag_save]') ) newTagForm.getElement('input[id=tag_save]').addEvent('click', function()
      {
        bind.saveTag(0);
      });
      if( newTagForm.getElement('a[id=tag_me]') ) newTagForm.getElement('a[id=tag_me]').addEvent('click', function()
      {
        bind.saveTag(bind.Base.Viewer.user_info.user_id);
      });
      
      var request = new Request.JSON({
        url: 'misc_js.php?task=friends_all',
        secure: this.options.ajaxSecure,
        onComplete: function(jsonObj)
        { 
          bind.addFriendToList(jsonObj.friends);
        }
      }).send();
    }
  },
  
  
  
  addFriendToList : function(friends)
  {
    var bind = this;
    
    friends.each(function(friend)
    {
      for(var x in friend)
      {
        var newDiv = new Element("div", {'id' : 'friend_div_'+x});
        var newAnchor = new Element("a", {'href' : 'javascript:void(0)', 
          'id' : 'friend_link_'+x,
          'html' : friend[x]
        }).inject(newDiv);
        
        newDiv.inject($('media_photo_friendlist'));
        
        $('friend_link_'+x).addEvent('click', function() { bind.saveTag(x); });
      }
    });
  },
  
  
  
  insertTag : function(tag_id, tag_link, tag_text, tag_x, tag_y, tag_width, tag_height, tagged_user)
  {
    var newHTML = '';
    var bind = this;
    
    if($('media_tags').style.display == 'none') 
    {
      $('media_tags').style.display = 'block';
    } else if(bind.tags.length != 0) 
    {
      newHTML = '<span id="tag_comma_'+tag_id+'">, </span>';
    }
    
    var newSpan = new Element("span", {'id' : 'full_tag_'+tag_id, 'html' : newHTML});
    if(tag_link != '') 
    {
      var newAnchor = new Element("a", {'href' : tag_link, 
        'id' : 'tag_link_'+tag_id,
        'html' : tag_text
      }).inject(newSpan);
    } 
    else
    {
      var newAnchor = new Element("span", {
        'id' : 'tag_link_'+tag_id,
				'html' : tag_text,
				'styles' : {'cursor' : 'pointer'}
			}).inject(newSpan);
    } 
    
    if( bind.Base.Viewer.user_exists && (bind.Base.Viewer.user_info.user_username == tagged_user || bind.Base.Viewer.user_info.user_username == bind.Base.Owner.user_info.user_username)) 
    {
      var media_tags_text = $(newSpan).get('html');
      $(newSpan).set('html', media_tags_text, ' (<a href=\'javascript:void(0);\' id=\'new_removetag_link\'>' + bind.Base.Language.Translate(1228) + '</a>)');
    }
    
    newSpan.inject($('media_tags'));
    
    bind.createTag(tag_id, tag_text, tag_x, tag_y, tag_width, tag_height);
    
    $('tag_link_'+tag_id).addEvent('mouseover', function() { bind.showTag(tag_id); });
    $('tag_link_'+tag_id).addEvent('mouseout', function() { bind.hideTag(tag_id); });
    
    if($('new_removetag_link'))
    {
      $('new_removetag_link').addEvent('click', function() { bind.removeTag(tag_id); });
      $('new_removetag_link').set('id', 'removetag_link_'+tag_id);
    }
    
    bind.tags.push(tag_id);
  },
  
  
  
  createTag : function(tag_id, label_text, tag_x, tag_y, tag_width, tag_height)
  {
    var bind = this;
      
    // CREATE TAG AND LABEL
    new Element("div", {'id' : 'tag_'+tag_id, 'html' : '<img src="./images/trans.gif" width="100%" height="100%" />', 'class' : 'tag_div_hidden', 'styles' : {'width' : (parseInt(tag_width)-4)+'px', 'height' : (parseInt(tag_height)-4)+'px', 'top' : tag_x+'px', 'left' : tag_y+'px'}}).inject($('media_photo_div'));
    new Element("div", {'id' : 'tag_label_'+tag_id, 'html' : label_text, 'class' : 'tag_label', 'styles' : {'display' : 'none', 'top' : (parseInt(tag_x)+parseInt(tag_height)+10)+'px', 'left' : tag_y+'px'}}).inject($('media_photo_div'));
    
    // ADD MOUSEOVER/MOUSEOUT EVENTS
    $('tag_'+tag_id).addEvent('mouseover', function() { bind.showTag(tag_id); });
    $('tag_'+tag_id).addEvent('mouseout', function() { bind.hideTag(tag_id); });
  },
  
  
  
  showTag : function(tag_id)
  {
    $('tag_'+tag_id).className = 'tag_div';
    $('tag_label_'+tag_id).style.display = 'block';
  },
  
  
  
  hideTag : function(tag_id)
  {
    $('tag_'+tag_id).className = 'tag_div_hidden';
    $('tag_label_'+tag_id).style.display = 'none';
  },
  
  
  
  addTag : function()
  {
    if( !this.isTagging )
    {
      this.isTagging = true;
      this.newtag = new MooCrop('media_photo');
      
      var bind = this;
      
      var indicator = $('media_photo_tagform').inject(bind.newtag.wrapper);
      indicator.setStyles({'top' : this.newtag.crop.bottom+10, 'left' : this.newtag.crop.right+10, 'display' : 'block'});
      
      this.newtag.addEvent('onBegin', function(imgsrc, crop, bound, hanlde) { indicator.setStyle('display', 'none'); });
      this.newtag.addEvent('onCrop', function(imgsrc, crop, bound, hanlde) { indicator.setStyles({'top' : crop.bottom+10, 'left' : crop.right+10, 'display' : 'none'}); });
      this.newtag.addEvent('onComplete', function(imgsrc, crop, bound, hanlde) { indicator.setStyle('display', 'block'); });
    }
  },
  
  
  
  cancelTag : function()
  {
    if( this.isTagging )
    {
      $('media_photo_tag').value = '';
      $('media_photo_tagform').inject('media_photo_div').setStyle('display', 'none');
      var stopTagging = this.newtag.removeOverlay.bind(this.newtag);
      stopTagging();
      this.isTagging = false;
    }
  },
  
  
  
  saveTag : function(mediatag_user_id)
  {
    if( this.isTagging )
    {
      if( this.options.object_owner && this.options.object_owner_id )
      {
        var object_owner = this.options.object_owner;
        var object_owner_id = this.options.object_owner_id;
        var user = '';
      }
      else
      {
        var object_owner = '';
        var object_owner_id = '';
        var user = this.Base.Owner.user_info.user_username;      
      }
      
      // Use ajax only
      var bind = this;
      var request = new Request.JSON({
        url: this.options.ajaxURL,
        method: this.options.ajaxMethod,
        secure: this.options.ajaxSecure,
        data: {
          'task' : 'tag_do',
          'ajax' : true,
          'mediatag_user_id' : mediatag_user_id,
          'mediatag_text' : $('media_photo_tag').value,
          'mediatag_x' : this.newtag.crop.top,
          'mediatag_y' : this.newtag.crop.left,
          'mediatag_height' : this.newtag.crop.height,
          'mediatag_width' : this.newtag.crop.width,
          'user' : user,
          'object_owner' : object_owner,
          'object_owner_id' : object_owner_id,
          'type' : this.options.type,
          'media_id' : this.options.media_id,
          'media_dir' : this.options.media_dir
        },
        onComplete: function(jsonObj)
        {
          if( $type(jsonObj.mediatag_id) )
          {
            bind.insertTag(
              jsonObj.mediatag_id,
              jsonObj.mediatag_link,
              jsonObj.mediatag_text,
              jsonObj.mediatag_x,
              jsonObj.mediatag_y,
              jsonObj.mediatag_width,
              jsonObj.mediatag_height,
              jsonObj.mediatag_user_username
            );
          }
        }
      }).send();
      
      // Stuff
      $('media_photo_tag').value = '';
      $('media_photo_tagform').inject('media_photo_div').setStyle('display', 'none');
      var stopTagging = this.newtag.removeOverlay.bind(this.newtag);
      stopTagging();
      this.isTagging = false;
    }
  },
  
  
  
  removeTag : function(tag_id)
  {
    if( this.options.object_owner && this.options.object_owner_id )
    {
      var object_owner = this.options.object_owner;
      var object_owner_id = this.options.object_owner_id;
      var user = '';
    }
    else
    {
      var object_owner = '';
      var object_owner_id = '';
      var user = this.Base.Owner.user_info.user_username;      
    }
    
    // Use ajax only
    var bind = this;
    var request = new Request.JSON({
      url: this.options.ajaxURL,
      method: this.options.ajaxMethod,
      secure: this.options.ajaxSecure,
      data: {
        'task' : 'tag_remove',
        'ajax' : true,
        'mediatag_id' : tag_id,
        'user' : user,
        'object_owner' : object_owner,
        'object_owner_id' : object_owner_id,
        'type' : this.options.type,
        'media_id' : this.options.media_id
      }
    }).send();
    
    // Destroy stuff
    $('tag_'+tag_id).destroy();
    $('tag_label_'+tag_id).destroy();
    
    $('full_tag_'+tag_id).destroy();
    
    if(this.tags.indexOf(tag_id) == 0 && $('tag_comma_'+this.tags[1]))
    {
      $('tag_comma_'+this.tags[1]).destroy();
    }
    
    this.tags.splice(this.tags.indexOf(tag_id), 1);
    
    if(this.tags.length == 0)
    {
      $('media_tags').style.display = 'none';
    }
  }
});








/***
 * MooCrop (v. rc-1 - 2007-10-24 )
 *
 * @version			rc-1
 * @license			BSD-style license
 * @author			nwhite - < nw [at] nwhite.net >
 * @infos			http://www.nwhite.net/MooCrop/
 * @copyright		Author
 * 

 */
var MooCrop = new Class({

	calculateHandles : true,
	current : {},

	options : {
		maskColor : 'black',
		maskOpacity : '.3',
		handleColor : '#FFFFFF',
		handleWidth : '5px',
		handleHeight : '5px',
		cropBorder : '1px dashed #FFFFFF',
		min : { 'width' : 50, 'height' : 50 },
		showMask : true, // false to remove, helps on slow machines
		showHandles : false // hide handles on drag
	},

	initialize: function(el, options){
		this.setOptions(options);
		this.img = $(el);
		if ( this.img.get('tag') != 'img') return false;

		this.resizeFunc = this.refresh.bindWithEvent(this);
		this.removeFunc = this.removeListener.bind(this);

		this.buildOverlay();
		this.setup();
	},

	setup: function(){
		$(this.cropArea).setStyles({
			'width': this.options.min.width, 
			'height': this.options.min.height,
			'top' : (this.img.height - this.options.min.height)/2,
			'left': (this.img.width - this.options.min.width) / 2 
		});

		this.current.crop = this.crop = this.getCropArea();
		this.handleWidthOffset = this.options.handleWidth.toInt() / 2;
		this.handleHeightOffset = this.options.handleHeight.toInt() /2;

		this.fixBoxModel();
		this.drawMasks();
		this.positionHandles();
	},

	getCropArea : function(){
		var crop = this.cropArea.getCoordinates();
		crop.left -= this.offsets.x; crop.right -= this.offsets.x; // calculate relative (horizontal)
		crop.top -= this.offsets.y; crop.bottom  -= this.offsets.y; // calculate relative (vertical)
		return crop;
	},

	fixBoxModel : function(){
		var diff = this.boxDiff = (this.crop.width - this.options.min.width)/2;

		var b = this.bounds = { 'top' : diff, 'left' : diff, 
			'right' : this.img.width+(diff*2), 'bottom' : this.img.height+(diff*2),
			'width' : this.options.min.width+(diff*2), 'height' : this.options.min.height+(diff*2) };

		this.wrapper.setStyles({
			'width' : b.right, 'height' : b.bottom,
			'background' : 'url('+this.img.src+') no-repeat '+diff+'px '+diff+'px'
		});

		this.north.setStyle('width',b.right);
		this.south.setStyle('width',b.right);
	},

	activate : function(event,handle){
		event.stop();
		this.current = { 'x' : event.page.x, 'y' : event.page.y, 'handle' : handle, 'crop' : this.current.crop };
		if(this.current.handle == 'NESW' && !this.options.showHandles) this.hideHandles();
		this.fireEvent('onBegin',[this.img.src,this.getCropInfo(),this.bounds,handle]);
		document.addEvent('mousemove', this.resizeFunc);
		document.addEvent('mouseup', this.removeFunc);
	},

	removeListener : function(){
		if( this.current.handle == 'NESW' && !this.options.showHandles) this.showHandles();
		document.removeEvent('mousemove', this.resizeFunc);
		document.removeEvent('mouseup', this.removeFunc);
		this.crop = this.current.crop;
		this.fireEvent('onComplete',[this.img.src,this.getCropInfo(),this.bounds,this.current.handle]);
	},

	refresh : function(event){
		var xdiff = this.current.x - event.page.x;
		var ydiff = this.current.y - event.page.y;

		var b = this.bounds;  var c = this.crop;  var handle = this.current.handle; var styles = {}; //saving bytes
		var dragging = (handle.length > 2) ? true : false;
		
		if( handle.contains("S") ){//SOUTH
			if(c.bottom - ydiff > b.bottom ) ydiff = c.bottom - b.bottom; // box south
			if(!dragging){
				if( (c.height - ydiff) < b.height ) ydiff = c.height - b.height; // size south
				styles['height'] = c.height - ydiff; // South handles only
			}
		}
		if( handle.contains("N") ){//NORTH
			if(c.top - ydiff < b.top ) ydiff = c.top; //box north
			if(!dragging){
				if( (c.height + ydiff ) < b.height ) ydiff = b.height - c.height; // size north
				styles['height'] = c.height + ydiff; // North handles only
			}
			styles['top'] = c.top - ydiff; // both Drag and N handles
		}
		if( handle.contains("E") ){//EAST
			if(c.right - xdiff > b.right) xdiff = c.right - b.right; //box east
			if(!dragging){
				if( (c.width - xdiff) < b.width ) xdiff = c.width - b.width; // size east
				styles['width'] = c.width - xdiff;
			}
		}
		if( handle.contains("W") ){//WEST
			if(c.left - xdiff < b.left) xdiff = c.left; //box west
			if(!dragging){
				if( (c.width + xdiff) < b.width ) xdiff = b.width - c.width; //size west
				styles['width'] = c.width + xdiff;
			}
			styles['left'] = c.left - xdiff; // both Drag and W handles
		}
		var preCssStyles = $merge(styles);
		if( $defined(styles.width)) styles.width -= this.boxDiff*2;
		if( $defined(styles.height)) styles.height -= this.boxDiff*2;

		this.cropArea.setStyles(styles);
		this.getCurrentCoords(preCssStyles);
		this.drawMasks();
		this.positionHandles();
		this.fireEvent('onCrop',[this.img.src,this.getCropInfo(),b,handle]);
	},

	getCurrentCoords : function(changed){
		var current = $merge(this.crop);
		
		if($defined(changed.left)){
			current.left = changed.left;
			if($defined(changed.width)) current.width = changed.width;
			else current.right = current.left + current.width;
		}
		if($defined(changed.top)){
			current.top = changed.top;
			if($defined(changed.height)) current.height = changed.height;
			else current.bottom = current.top + current.height;
		}
		if($defined(changed.width) && !$defined(changed.left)){
			current.width = changed.width; current.right = current.left + current.width;
		}
		if($defined(changed.height) && !$defined(changed.top)){
			current.height = changed.height; current.bottom = current.top + current.height;
		}
		this.current.crop = current;
	},

	drawMasks : function(){
		if(!this.options.showMask) return;
		var b = this.bounds;  var c = this.current.crop; var handle = this.current.handle;

		this.north.setStyle('height', c.top + 'px' );
		this.south.setStyle('height', b.bottom  - c.bottom  + 'px');
		this.east.setStyles({ height: c.height + 'px', width: b.right  - c.right + 'px',  top: c.top  + 'px', left: c.right + 'px'});
		this.west.setStyles({ height: c.height + 'px', width: c.left + 'px', top: c.top + 'px'});
	},

	positionHandles: function(){
		if(!this.calculateHandles) return;
		var c = this.current.crop; var wOffset = this.handleWidthOffset; var hOffset = this.handleHeightOffset;

		this.handles.get('N').setStyles({'left' : c.width / 2 - wOffset + 'px', 'top' : - hOffset + 'px'});
		this.handles.get('NE').setStyles({'left' : c.width - wOffset + 'px', 'top' : - hOffset + 'px'});
		this.handles.get('E').setStyles({ 'left' : c.width - wOffset + 'px', 'top' : c.height / 2 - hOffset + 'px'});
		this.handles.get('SE').setStyles({'left' : c.width - wOffset + 'px', 'top' : c.height - hOffset + 'px'});
		this.handles.get('S').setStyles({'left' : c.width / 2 - wOffset + 'px', 'top' : c.height - hOffset + 'px'});
		this.handles.get('SW').setStyles({'left' : - wOffset + 'px', 'top' : c.height - hOffset + 'px'});
		this.handles.get('W').setStyles({'left' : - wOffset + 'px', 'top' : c.height / 2 - hOffset + 'px'});
		this.handles.get('NW').setStyles({'left' : - wOffset + 'px', 'top' : - hOffset + 'px'});
	},

	hideHandles: function(){
		this.calculateHandles = false;
		this.handles.each(function(handle){
			handle.setStyle('display','none');
		});
	},

	showHandles: function(){
		this.calculateHandles = true;
		this.positionHandles();
		this.handles.each(function(handle){
			handle.setStyle('display','block');
		});
	},

	buildOverlay: function(){
		var o = this.options;

		this.wrapper = new Element("div", {
			'styles' : {'z-index' : 100, 'position' : 'relative', 'width' : this.img.width, 'height' : this.img.height, 'background' : 'url('+this.img.src+') no-repeat' , 'float' : this.img.getStyle('float') , 'margin-left' : 'auto' , 'margin-right' : 'auto'  }
		}).injectBefore(this.img);

		this.img.setStyle('display','none');

		this.offsets = { x : this.wrapper.getLeft(), y : this.wrapper.getTop() };

		// SET WRAPPER MOUSEOVER TO STOP PROPAGATION OF MOUSEOVER EVENT
		this.wrapper.addEvent('mouseover', function(event) { return false; });

		if(this.options.showMask){		// optional masks
			var maskStyles = { 'position' : 'absolute', 'overflow' : 'hidden', 'background-color' : o.maskColor, 'opacity' : o.maskOpacity};
			this.north = new Element("div", {'styles' : $merge(maskStyles,{'left':'0px'})}).injectInside(this.wrapper);
			this.south = new Element("div", {'styles' : $merge(maskStyles,{'bottom':'0px', 'left':'0px'})}).injectInside(this.wrapper);
			this.east =  new Element("div", {'styles' : maskStyles}).injectInside(this.wrapper);
			this.west =  new Element("div", {'styles' : $merge(maskStyles,{'left':'0px'})}).injectInside(this.wrapper);
		}

		this.cropArea = new Element("div", { 'styles' : { 'position' : 'absolute', 'top' : '0px', 'left' : '0px', 'border' : o.cropBorder, 'cursor' : 'move' },
		'events' : {
			'dblclick' : function(){ this.fireEvent('onDblClk',[this.img.src,this.getCropInfo(),this.bounds])}.bind(this),
			'mousedown' : this.activate.bindWithEvent(this,'NESW')}
		}).injectInside(this.wrapper);

		this.handles = new Hash();
		['N','NE','E','SE','S','SW','W','NW'].each(function(handle){
			this.handles.set(handle, new Element("div", {
			'styles' : { 'position' : 'absolute', 'background-color' : o.handleColor, 
						 'width' : o.handleWidth, 'height' : o.handleHeight, 'overflow' : 'hidden', 'cursor' : (handle.toLowerCase()+'-resize')},
			'events' : {'mousedown' : this.activate.bindWithEvent(this,handle)}
			}).injectInside(this.cropArea));
		},this);
	},

	getCropInfo : function(){
		var c = $merge(this.current.crop);
		c.width -= this.boxDiff*2; c.height -= this.boxDiff*2;
		return c;
	},

	removeOverlay: function(){
		this.wrapper.destroy();
		this.img.setStyle('display','');
	}

});
MooCrop.implement(new Events, new Options);