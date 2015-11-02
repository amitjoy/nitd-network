
/* $Id: autogrow.js 62 2009-02-18 02:59:27Z john $ */

// This file will no longer be included by default. A minified version is included as part of core-min.js



/*
 * SocialEngineMods Javascript Library Lite v0.1
 * http://www.SocialEngineMods.Net
 *
 * Copyright SocialEngineMods.Net
 * This code is licensed GPL for use exclusively on SocialEngine sites
 *
 */





/* Extensions */


Function.prototype.bind = function(obj) {
  var method = this, temp = function() {
    return method.apply(obj, arguments)
  };
  return(temp);
}; 


/* SEMods */


SEMods = function () {};

/* SEMods TextAreaControl */


SEMods.TextAreaControl = function(object) {
    this.obj = object;
    this.obj.style['overflow'] = 'hidden';
    this.originalHeight = this.obj.getStyle('height').toInt();
    var updater = this.update.bind(this);
    object.addEvent("focus", this.onFocus.bind(this));
    object.addEvent("blur", this.onBlur.bind(this));
    this.update();
};

SEMods.TextAreaControl.prototype = {
    obj : null,
    updating : false,
    autoGrow : false,
    originalHeight : null,
    shadowElement : null,
    increment : 0,
    timer : null,
    lastLength : 0,
    
    setAutoGrow : function(autoGrow) {
        this.autoGrow = autoGrow;
        this.createShadowElement();
        this.update();
    },
    
    onUpdate : function() {
        if(this.autoGrow && this.lastLength != this.obj.value.length) {
            this.lastLength = this.obj.value.length;
            this.updateShadowElement();
            this.obj.style.height = Math.max(this.originalHeight, this.shadowElement.offsetHeight + this.increment) + 'px';
        }
    },
    
    beginUpdate : function() {
        if(this.updating)
            return false;
        this.updating = true;
        return true;
    },
    
    endUpdate : function() {
        this.updating = false;
    },
    
    update : function() {
        if(!this.beginUpdate())
            return;
        
        this.onUpdate();
        this.endUpdate();
    },
    
    createShadowElement : function() {
        if(this.shadowElement)
            return;
        
        this.shadowElement = document.createElement("DIV");
        this.shadowElement.style.position = "absolute";
        this.shadowElement.style.top = "-99999px";
        this.shadowElement.style.left = "-99999px";
        
        document.body.appendChild(this.shadowElement);
    },
    
    updateShadowElement : function () {
        if(this.shadowElement) {
	    text = this.obj.value+'<br>';
            this.shadowElement.innerHTML = text.toString().replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#039;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br />');
            var fontSize = this.getPXMetrics( this.obj.getStyle('font-size'), 10);
            var lineHeight = this.obj.getStyle('line-height');
            // Opera misses on line-height
            if((/Opera/i.test(navigator.userAgent))) 
              lineHeight = this.getPXMetrics( lineHeight, 0) + 3 + 'px';
              
            this.increment = fontSize + 10;
        
            this.shadowElement.style['width'] = this.obj.offsetWidth + 'px';
            this.shadowElement.style['lineHeight'] = lineHeight;
            this.shadowElement.style['fontSize'] = this.obj.getStyle('font-size');

            this.shadowElement.style['fontFamily'] = this.obj.getStyle('font-family');
            this.shadowElement.style['paddingLeft'] = this.obj.getStyle('padding-left');
            this.shadowElement.style['paddingRight'] = this.obj.getStyle('padding-right');
            
        } 
    },
    
    onFocus : function() {
      this.timer = setInterval(this.update.bind(this), 500);
    },
    
    onBlur : function() {
      if(this.timer) {
        clearInterval(this.timer);
        this.timer = null;
      }
    },

    // em's not supported for now
    getPXMetrics : function(metric, defvalue) {
      var metricBase = parseFloat(metric);
      if(isNaN(metricBase)) return defvalue!=null ? defvalue : metricBase;
      return /px/i.test(metric) ? metricBase : /pt/i.test(metric) ? 1.3333*metricBase  : metricBase;
    }
    
};


/* Global namespace helper functions */


function textarea_autogrow(elementid) {
    var el = $(elementid);
//    if(!el) alert("textarea_autogrow(): element not found");
    if(el && !el._controlled) {
        el._controlled = true;
        new SEMods.TextAreaControl(el).setAutoGrow(true);
	return el.getStyle('height').toInt();
    }
};
