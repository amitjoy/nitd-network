
/* $Id: class_language.js 62 2009-02-18 02:59:27Z john $ */

// This file will no longer be included by default. A minified version is included as part of core-min.js

if( typeof(SocialEngineAPI)=="undefined" )
  var SocialEngineAPI = {};

SocialEngineAPI.Language = new Class({
  
  // Properties
  Base: {},

  
  
  
  // Methods
  initialize: function()
  {
    this.languageVariables = new Hash();
  },
  
  
  Translate: function(id)
  {
    var rawValue = this.languageVariables.get(id) || 'Missing Language Variable #' + id;
    return rawValue;
  },
  
  
  TranslateFormatted: function(id, params)
  {
    var rawValue = this.languageVariables.get(id) || 'Missing Language Variable #' + id;
    params.unshift(rawValue);
    var formattedValue = sprintf.run(params);
    return formattedValue;
  },
  
  
  
  
  // Import Methods
  Import: function(languageVariableObject)
  {
    if( $type(languageVariableObject)=="object" )
      this.languageVariables.extend(languageVariableObject);
  }
  
});



// Backwards compatibilty
var SocialEngineLanguage = SocialEngineAPI.Language;