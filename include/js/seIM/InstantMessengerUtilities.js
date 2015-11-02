
/* $Id: InstantMessengerUtilities.js 6 2009-01-11 06:01:29Z john $ */


var Modules = new Class({
  
  RegisterModule: function(moduleName, moduleObject)
  {
    if( !$type(this.modules) ) this.modules = new Hash;
    
    this.modules.set(moduleName, moduleObject);
    moduleObject.core = this;
    
    if( $type(moduleObject.onModuleRegister)=="function" )
    {
      moduleObject.onModuleRegister();
    }
  },
  
  UnregisterModule: function(moduleName)
  {
    if( !$type(this.modules) ) this.modules = new Hash;
    
    if( $type(moduleObject.onModuleUnregister)=="function" )
    {
      moduleObject.onModuleUnregister();
    }
    
    this.modules.erase(moduleName);
    moduleObject.core = undefined;
  },
  
  Boot: function()
  {
    if( $type(this.onBoot)=="function" )
    {
      this.onBoot();
    }
    
    this.modules.each(function(moduleObject)
    {
      if( $type(moduleObject.onBoot)=="function" )
      {
        moduleObject.onBoot();
      }
    });
  },
  
  Shutdown: function()
  {
    if( $type(this.onShutdown)=="function" )
    {
      this.onShutdown();
    }
    
    this.modules.each(function(moduleObject)
    {
      if( $type(moduleObject.onShutdown)=="function" )
      {
        moduleObject.onShutdown();
      }
    });
  }
  
});
  








/* ------------------------------------------------------------------------- *\
|     Effects                                                                 |
\* ------------------------------------------------------------------------- */

Fx.Flash = new Class({

	Extends: Fx,

	options: {
		'backgroundDefault' : 'transparent',
    'backgroundActive' : '#D0D0D0',
    'duration' : 250,
    'maxFlashes' : 8,
    'activeAfterFlash' : true,
    'force' : false
	},
  
  
	initialize: function(element, options)
  {
		this.element = this.subject = $(element);
		this.parent(options);
    
    // Defaults
    this.activeState = false;
    this.flashCount = 0;
  },
  
  
  iterate: function()
  {
    // Clear
    if( this.flashCount>=this.options.maxFlashes && !this.activeState )
    {
      $clear(this.periodicalID);
      this.periodicalID = undefined;
    }
    // On->Off
    if( this.activeState )
    {
      this.element.setStyle('background', this.options.backgroundDefault);
      this.activeState = false;
    }
    // Off->On
    else if( !this.activeState )
    {
      this.element.setStyle('background', this.options.backgroundActive);
      this.activeState = true;
      this.flashCount++;
    }
  },
  
  
  start: function()
  {
    if( this.isFxFlash && !this.options.force ) return false;
    this.element.isFxFlash = true;
    
    var bind = this;
    // Flash effect
    this.periodicalID = (function(){ this.iterate(); }).periodical(this.options.duration, this);
    // Event
    var onClickFunction = this.onClick = function() { bind.stop(); }
    this.element.addEvent('click', onClickFunction);
  },
  
  
  stop: function()
  {
    // Clear
    if( this.periodicalID )
    {
      $clear(this.periodicalID);
      this.periodicalID = undefined;
    }
    this.activeState = false;
    this.flashCount = 0;
    
    // Reset background
    this.element.setStyle('background', this.options.backgroundDefault);
    
    // Remove event
    this.element.removeEvent('click', this.onClick);
    
    this.element.isFxFlash = false;
  }
});




Element.implement({

	flash: function(options){
    if( $type(options)!="object" ) options = {};
    if( this.isFxFlash && !options.force ) return false;
    
    var effect = new Fx.Flash(this, options);
    effect.start();
    return effect;
	}
  
});
  








/* ------------------------------------------------------------------------- *\
|     Prototypes                                                              |
\* ------------------------------------------------------------------------- */

Hash.prototype.fromQueryString = function( text, splitOne, splitTwo )
{
  if( !$type(text) ) return;
  var thisHash = this;
  
  // Process args
  if(  $type(text)!="string" ) text = text.toString();
  if( !$type(splitOne) ) splitOne = "&";
  if( !$type(splitTwo) ) splitTwo = "=";
  
  // SplitOne
  var pairStringList = text.split(splitOne);
  if( $type(pairStringList)!="array" ) return;
  
  keyValuePairList.each(function(pairString)
  {
    // SplitTwo
    var keyValuePair = pairString.split(splitTwo);
    if( $type(keyValuePair)!='array' ) return;
    
    // Set
    thisHash.set(keyValuePair[0], decodeURIComponent(keyValuePair[1]));
  });
}




Element.Events.keyenter = {
	base: 'keyup',
	condition: function(e)
  {
    return e.key=='enter';
	}
};



String.prototype.escapeForRegex = function()
{
  var text = this;
  var specials = [
    '/', '.', '*', '+', '?', '|',
    '(', ')', '[', ']', '{', '}', '\\'
  ];
  var escapeRegex = new RegExp(
    '(\\' + specials.join('|\\') + ')', 'g'
  );
  return text.replace(escapeRegex, '\\$1');
}
