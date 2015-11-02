
/* $Id: class_base.js 62 2009-02-18 02:59:27Z john $ */

// This file will no longer be included by default. A minified version is included as part of core-min.js

var SocialEngineAPI = {

  version : '0.1.0alpha'

};



SocialEngineAPI.Base = new Class({

  // Methods
  initialize: function()
  {
    this.version = SocialEngineAPI.version;
  },
  
  
  
  RegisterModule: function(moduleObject)
  {
    moduleObject.Base = this;
  }

});