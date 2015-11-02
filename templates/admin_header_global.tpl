<!DOCTYPE html 
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns='http://www.w3.org/1999/xhtml'>
{* $Id: admin_header_global.tpl 64 2009-02-19 03:24:11Z john $ *}
<head>
<title>{lang_print id=1}</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

{* INCLUDE STYLESHEET FROM MAIN SITE *}
<link rel="stylesheet" href="../templates/styles_global.css" title="stylesheet" type="text/css" />
<link rel="stylesheet" href="../templates/admin_styles.css" title="stylesheet" type="text/css" />

{* CODE FOR VARIOUS JAVASCRIPT-BASED FEATURES, DO NOT REMOVE *}
<script type="text/javascript" src="../include/js/mootools12-min.js"></script>
{*
<script type="text/javascript" src="../include/js/mootools12.js"></script>
<script type="text/javascript" src="../include/js/mootools12-more.js"></script>
*}

<script type="text/javascript" src="../include/js/core-min.js"></script>
{*
<script type="text/javascript" src="../include/js/autogrow.js"></script>
<script type="text/javascript" src="../include/js/smoothbox.js"></script>
<script type="text/javascript" src="../include/js/autosuggest.js"></script>
<script type="text/javascript" src="../include/js/sprintf.js"></script>
<script type="text/javascript" src="../include/js/class_base.js"></script>
<script type="text/javascript" src="../include/js/class_core.js"></script>
<script type="text/javascript" src="../include/js/class_language.js"></script>
<script type="text/javascript" src="../include/js/class_url.js"></script>
<script type="text/javascript" src="../include/js/class_comments.js"></script>
<script type="text/javascript" src="../include/js/class_tags.js"></script>
<script type="text/javascript" src="../include/js/class_user.js"></script>
*}

{* INSTALIZE API *}
<script type="text/javascript">
<!--
  var SocialEngine = new SocialEngineAPI.Base();
  
  // Core
  SocialEngine.Core = new SocialEngineAPI.Core();
  SocialEngine.Core.ImportSettings({$se_javascript->generateSettings($setting)});
  SocialEngine.Core.ImportPlugins({$se_javascript->generatePlugins($global_plugins)});
  SocialEngine.RegisterModule(SocialEngine.Core);
  
  // URL
  SocialEngine.URL = new SocialEngineAPI.URL();
  SocialEngine.URL.ImportURLBase({$se_javascript->generateURLBase($url)});
  SocialEngine.URL.ImportURLInfo({$se_javascript->generateURLInfo($url)});
  SocialEngine.RegisterModule(SocialEngine.URL);
  
  // Language
  SocialEngine.Language = new SocialEngineAPI.Language();
  SocialEngine.RegisterModule(SocialEngine.Language);
  
  // Back
  SELanguage = SocialEngine.Language;
//-->
</script>

{literal}
<script language='JavaScript'>
<!--
// ADD TIP FUNCTION
window.addEvent('load', function()
{
    var Tips1 = new Tips($$('.Tips1'));
});


// ADD ABILITY TO MINIMIZE/MAXIMIZE MENU TABLES
var menu_minimized = new Hash.Cookie('menu_cookie', {duration: 3600});


// ADD ADMIN MENU BACKGROUND ROLL OVER
Rollimage1 = new Array()
Rollimage1['1'] = new Image(216,23);
Rollimage1['1'].src = "../images/admin_menu_bg1.gif";
Rollimage1['2'] = new Image(216,23);
Rollimage1['2'].src = "../images/admin_menu_bg2.gif";
//-->
</script>
{/literal}


{* ASSIGN PLUGIN MENU ITEMS AND INCLUDE NECESSARY STYLE/JAVASCRIPT FILES *}
{hook_include name=admin_header}
{hook_foreach name=admin_styles var=hook_stylesheet}
<link rel="stylesheet" href="{$hook_stylesheet}" title="stylesheet" type="text/css" />
{/hook_foreach}
{hook_foreach name=admin_scripts var=hook_script}
<script type="text/javascript" src="{$hook_script}"></script>
{/hook_foreach}


</head>
<body>

{* GLOBAL IFRAME FOR AJAX FUNCTIONALITY *}
<iframe id='ajaxframe' name='ajaxframe' style='display: none;' src='javascript:false;'></iframe>


