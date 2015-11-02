<!DOCTYPE html 
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns='http://www.w3.org/1999/xhtml'>
{* $Id: header_global.tpl 146 2009-03-27 02:48:07Z john $ *}
<head>
<title>{lang_print id=642}{if $global_page_title != ""} - {lang_sprintf id=$global_page_title[0] 1=$global_page_title[1] 2=$global_page_title[2]}{/if}</title>
<base href='{$url->url_base}' />
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
<meta http-equiv="X-UA-Compatible" content="IE=7" /> 
<meta name='Description' content="{if $global_page_description != ""}{lang_sprintf id=$global_page_description[0] 1=$global_page_description[1] 2=$global_page_description[2]}{else}{lang_print id=1156}{/if}" />

{* INLUCDE MAIN STYLESHEET *}
<link rel="stylesheet" href="./templates/styles_global.css" title="stylesheet" type="text/css" />  
<link rel="stylesheet" href="./templates/styles.css" title="stylesheet" type="text/css" /> 

<!--[if lt IE 7]>
<script defer type="text/javascript" src="pngfix.js"></script>
<![endif]-->  

{* CODE FOR VARIOUS JAVASCRIPT-BASED FEATURES, DO NOT REMOVE *}
<script type="text/javascript" src="./include/js/mootools12-min.js"></script>
{*
<script type="text/javascript" src="./include/js/mootools12.js"></script>
<script type="text/javascript" src="./include/js/mootools12-more.js"></script>
*}

<script type="text/javascript" src="./include/js/core-min.js"></script>
{*
<script type="text/javascript" src="./include/js/autogrow.js"></script>
<script type="text/javascript" src="./include/js/smoothbox.js"></script>
<script type="text/javascript" src="./include/js/autosuggest.js"></script>
<script type="text/javascript" src="./include/js/sprintf.js"></script>
<script type="text/javascript" src="./include/js/class_base.js"></script>
<script type="text/javascript" src="./include/js/class_core.js"></script>
<script type="text/javascript" src="./include/js/class_language.js"></script>
<script type="text/javascript" src="./include/js/class_url.js"></script>
<script type="text/javascript" src="./include/js/class_comments.js"></script>
<script type="text/javascript" src="./include/js/class_tags.js"></script>
<script type="text/javascript" src="./include/js/class_user.js"></script>
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
  
  // User - Viewer
  SocialEngine.Viewer = new SocialEngineAPI.User();
  SocialEngine.Viewer.ImportUserInfo({$se_javascript->generateUserInfo($user)});
  SocialEngine.RegisterModule(SocialEngine.Viewer);
  
  // User - Owner
  SocialEngine.Owner = new SocialEngineAPI.User();
  SocialEngine.Owner.ImportUserInfo({$se_javascript->generateUserInfo($owner)});
  SocialEngine.RegisterModule(SocialEngine.Owner);
  
  // Back
  SELanguage = SocialEngine.Language;
//-->
</script>


{literal}
<script type="text/javascript">
<!--
  // ADD TIP FUNCTION
  window.addEvent('load', function()
  {
    var Tips1 = new Tips($$('.Tips1'));
  });
//-->
</script>
{/literal}


{* ASSIGN PLUGIN MENU ITEMS AND INCLUDE NECESSARY STYLE/JAVASCRIPT FILES *}
{hook_include name=header}
{hook_foreach name=styles var=hook_stylesheet}
<link rel="stylesheet" href="{$hook_stylesheet}" title="stylesheet" type="text/css" />
{/hook_foreach}
{hook_foreach name=scripts var=hook_script}
<script type="text/javascript" src="{$hook_script}"></script>
{/hook_foreach}


</head>
<body>

{* GLOBAL IFRAME FOR AJAX FUNCTIONALITY *}
<iframe id='ajaxframe' name='ajaxframe' style='display: none;' src='javascript:false;'></iframe>
