
/* $Id: class_core.js 62 2009-02-18 02:59:27Z john $ */

// This file will no longer be included by default. A minified version is included as part of core-min.js

SocialEngineAPI.Core = new Class({
  
  // Properties
  Base: {},
  
  settings: {},
  
  plugins: {},
  
  options : {
    ajaxURL : 'js_api.php'
  },

  
  
  
  // Methods
  initialize: function()
  {
    
  },

  
  
  
  // Import Methods
  ImportSettings: function(settings)
  {
    this.settings = settings;
  },

  
  
  
  // Import Methods
  ImportPlugins: function(plugins)
  {
    this.plugins = plugins;
  }
  
});