
/* $Id: class_group_files.js 34 2009-01-24 04:17:28Z john $ */

// Required language variables: 182,183,184,185

SocialEngineAPI.GroupFiles = new Class({
  
  // Class
	Implements: [Options],
  
  
  
  
  // Properties
  Base: {},
  
  page: 1,
  
  total: 0,
  
  
  options: {
    // Controls ajax request options
    'ajaxURL' : 'group.php',
    'ajaxMethod' : 'post',
    'ajaxSecure' : false,

    // Pagination Info
    'paginate' : false,
    'cpp' : false,
    
    // Some other stuff to identify the object
    'group_id' : false,
    'group_dir' : ''
  },
  
  
  
  
  // Methods
  initialize: function(options)
  {
    this.setOptions(options);
    
    if( this.options.initialTotal ) this.total = this.options.initialTotal;

    var bind = this;
    window.addEvent('domready', function()
    {
      bind.getFiles(1);
    });
  },
  
  
  
  
  getFiles: function(direction)
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

    // AJAX
    var bind = this;
    var request = new Request.JSON({
      'url' : this.options.ajaxURL,
      'method' : this.options.ajaxMethod,
      'secure' : this.options.ajaxSecure,
      'data' : {
        'task'  : 'files_get',
	'group_id' : this.options.group_id,
        'cpp'   : this.options.cpp,
        'p'     : this.page
      },
      'onComplete' : function(responseObject, responseText)
      {
        bind.updateFiles(responseObject);
      }
    });
    
    request.send();
  },
  
  
  
  
  // THIS FUNCTION UPDATES THE FILES
  updateFiles: function(responseObject)
  {
    if( $type(responseObject)!="object" )
    {
      alert('There was an error processing the request.');
      return false;
    }
    
    
    // Prepare
    this.total  = parseInt(responseObject.total_files) || 0;
    this.page   = responseObject.p;

    var maxpage = responseObject.maxpage;
    var p_start = responseObject.p_start;
    var p_end   = responseObject.p_end;
    
    var totalFilesElement     = $('group_' + this.options.group_id + '_totalfiles');
    var fileContainerElement = $('group_' + this.options.group_id + '_files');
    var files = responseObject.files;
    
    // UPDATE TOTAL FILES AND PAGE VARS
    totalFilesElement.innerHTML = this.total;

    // EMPTY CONTAINER 
    fileContainerElement.empty();

    // IF NO FILES, SHOW EMPTY MESSAGE
    if(this.total == 0) { $('group_' + this.options.group_id + '_nofiles').style.display = 'block'; } else { $('group_' + this.options.group_id + '_nofiles').style.display = 'none'; }

    // LOOP OVER FILES
    var bind = this;
    files.each(function(fileObject)
    {
      var newFile = new Element('div', {
        'id' : 'file_' + fileObject.groupmedia_id
      });
      
      // DECIDE ON EXTENSION
      if(fileObject.groupmedia_ext == 'jpeg' || fileObject.groupmedia_ext == 'jpg' || fileObject.groupmedia_ext == 'gif' || fileObject.groupmedia_ext == 'png' || fileObject.groupmedia_ext == 'bmp') {
        var file_src = bind.options.group_dir + fileObject.groupmedia_id + '_thumb.jpg';
      } else if(fileObject.groupmedia_ext == 'mp3' || fileObject.groupmedia_ext == 'mp4' || fileObject.groupmedia_ext == 'wav') {
        var file_src = './images/icons/audio_big.gif';
      } else if(fileObject.groupmedia_ext == 'mpeg' || fileObject.groupmedia_ext == 'mpg' || fileObject.groupmedia_ext == 'mpa' || fileObject.groupmedia_ext == 'avi' || fileObject.groupmedia_ext == 'swf' || fileObject.groupmedia_ext == 'mov' || fileObject.groupmedia_ext == 'ram' || fileObject.groupmedia_ext == 'rm') {
        var file_src = './images/icons/video_big.gif';
      } else {
        var file_src = './images/icons/file_big.gif';
      }
      
      // Check null title
      if( !$type(fileObject.groupmedia_title) ) fileObject.groupmedia_title = 'X'; // TODO: language
      
      // BUILD FILE
      //var newFileInnerHTML = "<a href='group_album_file.php?group_id=" + bind.options.group_id +"&groupmedia_id=" + fileObject.groupmedia_id +"'>" +
      var newFileInnerHTML = "<a href='" + bind.Base.URL.url_create('group_media', null, bind.options.group_id, fileObject.groupmedia_id) +"'>" +
			"<img src='" + file_src + "' class='photo' border='0' width='90' height='90' style='float: left; margin: 3px;' alt='" + fileObject.groupmedia_title + "' title='" + fileObject.groupmedia_title.replace(/'/g, "&#039;") + "'>" +
			"</a>";
      

      // ADD NEW INNERHTML
      newFile.setProperty('html', newFileInnerHTML);
      newFile.inject(fileContainerElement);
      
    });

    new Element('div', {'styles': {'clear' : 'both'}}).inject(fileContainerElement);
    
    // CREATE PAGINATION DIV
    if(this.options.paginate && this.total > this.options.cpp) { 
      var filePaginationTop = new Element('div', {'styles': {'text-align' : 'center', 'margin-bottom' : '10px'}});
      var filePaginationBottom = new Element('div', {'styles': {'text-align' : 'center', 'margin-top' : '10px'}});
      if(this.page > 1) {
        var paginationHTMLTop = "<a href='javascript:void(0);' id='file_last_page_top'>&#171; " + bind.Base.Language.Translate(182) + "</a>";
        var paginationHTMLBottom = "<a href='javascript:void(0);' id='file_last_page_bottom'>&#171; " + bind.Base.Language.Translate(182) + "</a>";
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
        paginationHTMLTop += "<a href='javascript:void(0);' id='file_next_page_top'>" + bind.Base.Language.Translate(183) + " &#187;</a>";
        paginationHTMLBottom += "<a href='javascript:void(0);' id='file_next_page_bottom'>" + bind.Base.Language.Translate(183) + " &#187;</a>";
      } else {
        paginationHTMLTop += "<font class='disabled'>" + bind.Base.Language.Translate(183) + " &#187;</font>";
        paginationHTMLBottom += "<font class='disabled'>" + bind.Base.Language.Translate(183) + " &#187;</font>";
      }
      filePaginationTop.setProperty('html', paginationHTMLTop);
      filePaginationBottom.setProperty('html', paginationHTMLBottom);
      filePaginationTop.inject(fileContainerElement, 'top');
      filePaginationBottom.inject(fileContainerElement);

      // ADD EVENTS
      if( filePaginationTop.getElement('a[id=file_last_page_top]') ) filePaginationTop.getElement('a[id=file_last_page_top]').addEvent('click', function()
      {
        bind.getFiles('previous');
      });

      if( filePaginationBottom.getElement('a[id=file_last_page_bottom]') ) filePaginationBottom.getElement('a[id=file_last_page_bottom]').addEvent('click', function()
      {
        bind.getFiles('previous');
      });
      
      if( filePaginationTop.getElement('a[id=file_next_page_top]') ) filePaginationTop.getElement('a[id=file_next_page_top]').addEvent('click', function()
      {
        bind.getFiles('next');
      });

      if( filePaginationBottom.getElement('a[id=file_next_page_bottom]') ) filePaginationBottom.getElement('a[id=file_next_page_bottom]').addEvent('click', function()
      {
        bind.getFiles('next');
      });
    }

  }
  
});
