
/* $Id: smoothbox.js 62 2009-02-18 02:59:27Z john $ */

// This file will no longer be included by default. A minified version is included as part of core-min.js


/*
 * Smoothbox v20080623 by Boris Popoff (http://gueschla.com)
 * To be used with mootools 1.2
 *
 * Based on Cody Lindley's Thickbox, MIT License
 *
 * Licensed under the MIT License:
 *   http://www.opensource.org/licenses/mit-license.php
 */

TB_WIDTH = 0;
TB_HEIGHT = 0;

var TB_doneOnce = 0;
var TB_ready = false;
var TB_useReady = true;


// add smoothbox to href elements that have a class of .smoothbox
function TB_init()
{
  TB_ready = true;
	$$("a.smoothbox").each(function(el){el.onclick=TB_bind});
}

window.addEvent('domready', TB_init);
//window.addEvent('load', TB_init);



function TB_bind(event)
{
  if( TB_useReady && !TB_ready ) return;
  
	var event = new Event(event);
	// stop default behaviour
	event.preventDefault();
	// remove click border
	this.blur();
	// get caption: either title or name attribute
	var caption = this.title || this.name || "";
	// get rel attribute for image groups
	var group = this.rel || false;
	// display the box for the elements href
	TB_show(caption, this.href, group);
    this.onclick = TB_bind;
    return false;
}

// called when the user clicks on a smoothbox link
function TB_show(caption, url, rel, loading, disable_close)
{
  if( TB_useReady && !TB_ready ) return;
  
  // set default closing mechanism
  if(!disable_close) { disable_close = 0; }

  // create iframe, overlay and box if non-existent
  if (!$("TB_overlay"))
  {
    (new Element('div').setProperty('id', 'TB_overlay')).inject($('smoothbox_container') || document.body);
    $('TB_overlay').setOpacity(0.6);
    TB_overlaySize();
  }

  if (!$("TB_window"))
  {
    (new Element('div').setProperty('id', 'TB_window')).inject($('smoothbox_container') || document.body);
    $('TB_window').setOpacity(0);
  }
  else 
  {
    $('TB_window').destroy(); //$('TB_window').dispose();
    (new Element('div').setProperty('id', 'TB_window')).inject($('smoothbox_container') || document.body);
    $('TB_window').setOpacity(0);
  }

  if(disable_close == 0) { $("TB_overlay").onclick=TB_remove; } else { $("TB_overlay").onclick = ''; }
  window.onscroll = TB_position;
    
  // check if a query string is involved
  var baseURL = url.match(/(.+)?/)[1] || url;
    
  // CODE TO SHOW IFRAME
  var queryString = url.match(/\?(.+)/)[1];
  var params = TB_parseQuery(queryString);
  
  TB_WIDTH = (params['width'] * 1) + 30;
  TB_HEIGHT = (params['height'] * 1) + 40;
  
  var ajaxContentW = TB_WIDTH - 30, ajaxContentH = TB_HEIGHT - 45;
  
  if (url.indexOf('TB_iframe') != -1)
  {
    urlNoQuery = url.split('TB_');
    $("TB_window").innerHTML += "<div id='TB_title'><div id='TB_ajaxWindowTitle'>" + caption + "</div><div id='TB_closeAjaxWindow'><a href='#' id='TB_closeWindowButton' title='Close'>X</a></div></div><iframe frameborder='0' hspace='0' src='" + urlNoQuery[0] + "&in_smoothbox=true' id='TB_iframeContent' name='TB_iframeContent' scrolling='auto' style='width:" + (ajaxContentW + 29) + "px;height:" + (ajaxContentH + 17) + "px;' onload='TB_showWindow()'> </iframe>";
  }
  else
  {
    $("TB_window").innerHTML += "<div id='TB_title'><div id='TB_ajaxWindowTitle'>" + caption + "</div><div id='TB_closeAjaxWindow'><a href='#' id='TB_closeWindowButton'>X</a></div></div><div id='TB_ajaxContent' style='width:" + ajaxContentW + "px;height:" + ajaxContentH + "px;'></div>";
  }

  $("TB_closeWindowButton").onclick = TB_remove;

  if (url.indexOf('TB_inline') != -1)
  {
    $("TB_ajaxContent").innerHTML = ($(params['inlineId']).innerHTML);
    TB_position();
    TB_showWindow();
  }
  else if (url.indexOf('TB_iframe') != -1)
  {
    //alert("UHOH1");
    TB_position();
    if (frames['TB_iframeContent'] == undefined) {//be nice to safari
      $(document).keyup(function(e){
                    var key = e.keyCode;
                    if (key == 27) {
                        TB_remove()
                    }
      });
      TB_showWindow();
    }
  }
  else
  {
    //alert("UHOH2");
    var handlerFunc = function(){
          TB_position();
          TB_showWindow();
    };
    new Request.HTML({
      method: 'get',
      update: $("TB_ajaxContent"),
      onComplete: handlerFunc
    }).get(url);
  }

  window.onresize = function()
  {
    TB_position();
    TB_overlaySize();
  }

  document.onkeyup = function(event)
  {
    var event = new Event(event);
    if (event.code == 27) { // close
        TB_remove();
    }
  }


}

//helper functions below

function TB_showWindow()
{
  if( TB_useReady && !TB_ready ) return;
  
  if (TB_doneOnce == 0) {
      TB_doneOnce = 1;
      
      $('TB_window').set('tween', {
          duration: 250
      });
      $('TB_window').tween('opacity', 0, 1);
      
  }
  else {
      $('TB_window').setStyle('opacity', 1);
  }

//  $('TB_window').setStyle('opacity', 1);
}

function TB_remove()
{
  if( TB_useReady && !TB_ready ) return;
  
  $("TB_overlay").onclick = null;
  document.onkeyup = null;
  document.onkeydown = null;
  
  if ($('TB_closeWindowButton')) 
      $("TB_closeWindowButton").onclick = null;
  
  $('TB_window').set('tween', {
      duration: 250,
      onComplete: function(){
          $('TB_window').destroy(); //$('TB_window').dispose();
      }
  });
  $('TB_window').tween('opacity', 1, 0);
  
  
  
  $('TB_overlay').set('tween', {
      duration: 400,
      onComplete: function(){
          $('TB_overlay').destroy(); //$('TB_overlay').dispose();
      }
  });
  $('TB_overlay').tween('opacity', 0.6, 0);
  
  window.onscroll = null;
  window.onresize = null;
  
  TB_init();
  TB_doneOnce = 0;
  return false;
}

function TB_position()
{
  if( TB_useReady && !TB_ready ) return;
  
  $('TB_window').set('morph', {
    duration: 75
  });
  $('TB_window').morph({
    width: TB_WIDTH + 'px',
    left: (window.getScrollLeft() + (window.getWidth() - TB_WIDTH) / 2) + 'px',
    top: (window.getScrollTop() + (window.getHeight() - TB_HEIGHT) / 2) + 'px'
	});	
}

function TB_overlaySize()
{
  if( TB_useReady && !TB_ready ) return;
  
  // we have to set this to 0px before so we can reduce the size / width of the overflow onresize 
  $("TB_overlay").setStyles({
    "height": '0px',
    "width": '0px'
  });
  $("TB_overlay").setStyles({
    "height": window.getScrollHeight() + 'px',
    "width": window.getScrollWidth() + 'px'
  });
}


function TB_parseQuery(query)
{
  if( TB_useReady && !TB_ready ) return;
  
  // return empty object
  if (!query)  return {};
  var params = {};
  
  // parse query
  var pairs = query.split(/[;&]/);
  for (var i = 0; i < pairs.length; i++)
  {
    var pair = pairs[i].split('=');
    if (!pair || pair.length != 2) continue;
    // unescape both key and value, replace "+" with spaces in value
    params[unescape(pair[0])] = unescape(pair[1]).replace(/\+/g, ' ');
  }
  return params;
}
