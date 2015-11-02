
/* $Id: class_url.js 62 2009-02-18 02:59:27Z john $ */

// This file will no longer be included by default. A minified version is included as part of core-min.js

SocialEngineAPI.URL = new Class({
  
  // Properties
  Base: {},
  
  url_base : null,
  
  url_info : {},
  
  
  
  // Methods
  initialize: function()
  {
    
  },
  
  
  url_create: function(name, user, id1, id2, id3)
  {
    var url_template = ( SocialEngine.Core.settings.setting_url ? this.url_info[name].url_subdirectory : this.url_info[name].url_regular );
    if( !url_template ) return false;
    
    url_template = url_template.replace('$user', user);
    url_template = url_template.replace('$id1', id1);
    url_template = url_template.replace('$id2', id2);
    url_template = url_template.replace('$id3', id3);
    
    return this.url_base + url_template;
  },
  
  
  url_userdir: function(user_id)
  {
    //alert(user_id + ' ' + ((user_id - 1) % 1000).toString());
    return 'uploads_user/' + (user_id + 999 - ((user_id - 1) % 1000)).toString() + '/' + user_id + '/';
  },
  
  
  
  
  
  // Import methods
  ImportURLBase: function(url_base)
  {
    this.url_base = url_base;
  },
  
  ImportURLInfo: function(url_info)
  {
    this.url_info = url_info;
  }
  
  
});