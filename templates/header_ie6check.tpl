{* $Id: header_ie6check.tpl 1 2010-04-172 09:36:11Z SocialEngineAddOns $ *}
<link rel="stylesheet" href="./templates/styles_ie6check.css" title="stylesheet" type="text/css" />

{* Only Showing the pop up box when users is using IE6 Browser *}
{if $check == 2}
	<div id="message_ie6check">
	 <div id="left">
	  <img src="./images/icons/message_ie_alert.gif" alt="" />
	 		{$message}
	 	 		<div class="icons">
	  			{foreach from = $browsers item = browser}
							
							{if $browser == 'mozila'}<img src="./images/icons/message_ie_mozilla.gif" alt="" /><a href="http://www.firefox.com/">{lang_print id = 650005005}</a>
							{elseif $browser == 'chrome'}<img src="./images/icons/message_ie_chrome.gif" alt="" /><a href="http://www.google.com/chrome">{lang_print id = 650005006}</a>
							{elseif $browser == 'safari'}<img src="./images/icons/message_ie_safari.gif" alt="" /><a href="http://www.apple.com/safari/">{lang_print id = 650005004}</a>
						
							{elseif $browser == 'opera'} <img src="./images/icons/message_ie_opera.gif" alt="" /><a href="http://www.opera.com/download/">{lang_print id = 650005007}</a>
							
						{elseif $browser == 'explorer'}<img src="./images/icons/message_ie_ie.gif" alt="" /><a href="http://www.microsoft.com/windows/internet-explorer/default.aspx">{lang_print id = 650005008}</a>
						
							{elseif $browser == 'netscape'}<img src="./images/icons/message_ie_netscape.gif" alt="" /><a href="http://www.brothersoft.com/netscape-navigator-download-60475.html">{lang_print id = 650005009}</a>
							{/if}
	    		{/foreach} 
	  		</div>
	 </div>
	 <div id="right" class="ie6check_close">
	 <a onclick="check_ajax()" title="close">[  close  ]</a>
	 </div> 
	 </div>
{/if}
 
{* Hiding the pop up box when close button is clicked *}
{literal}
<script language="javascript" type="text/javascript">
function check_ajax(){
	var postData = {
	 'task' : 'show_popup'
  };
	var request = new Request({
		'url' : './ie6_ajax.php',
		'method' : 'post',
    'data' : postData,
		'onComplete' : 
		function(responseObject){
     	document.getElementById('message_ie6check').style.display = "none";
   	}
  }).send();
} 
</script>
{/literal}


