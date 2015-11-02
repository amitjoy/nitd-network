{include file='admin_header.tpl'}

{* $Id: admin_url.tpl 8 2009-01-11 06:02:53Z john $ *}

<h2>{lang_print id=19}</h2>
{lang_print id=456}
<br />
<br />

{if $result != 0}
<div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=191}</div>
{/if}

<form action='admin_url.php' method='POST'>
<table cellpadding='0' cellspacing='0' width='600'>
<tr><td class='header'>{lang_print id=457}</td></tr>
<tr>
<td class='setting1'>
{lang_print id=458}
<br><br>
{lang_print id=459}
<br>
{section name=url_loop loop=$urls}
{$urls[url_loop].url_title}: {$urls[url_loop].url_regular}<br>
{/section}
<br>
{lang_print id=460}
<br>
{section name=url_loop loop=$urls}
{$urls[url_loop].url_title}: {$urls[url_loop].url_subdirectory}<br>
{/section}
</td></tr><tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr><td><input type='radio' name='setting_url' id='setting_url_0' value='0'{if $setting.setting_url == 0} CHECKED{/if}>&nbsp;</td><td><label for='setting_url_0'>{lang_print id=461}</label></td></tr>
  <tr><td><input type='radio' name='setting_url' id='setting_url_1' value='1'{if $setting.setting_url == 1} CHECKED{/if}>&nbsp;</td><td><label for='setting_url_1'>{lang_print id=462}</label>{if $setting.setting_url == 1}{lang_print id=463}{/if}</td></tr>
  </table>
</td></tr></table>
<br>

<input type='submit' class='button' value='{lang_print id=173}'>
<input type='hidden' name='task' value='dosave'>
</form>


{* JAVASCRIPT FOR CONFIRMING DELETION *}
{literal}
<script type="text/javascript">
<!-- 
function urlhelp() {
  TB_show('{/literal}{lang_print id=464}{literal}', '#TB_inline?height=550&width=600&inlineId=urlhelp', '', '../images/trans.gif');
}

{/literal}{if $result != 0 && $setting.setting_url == 1}{literal}
window.addEvent('domready', function(){
  urlhelp();
});
{/literal}{/if}{literal}

//-->
</script>
{/literal}

{* HIDDEN DIV TO DISPLAY SUBDIRECTORY HELP *}
<div style='display: none;' id='urlhelp'>
  <div style='margin-top: 10px; margin-bottom: 10px;'>{lang_print id=465}</div>
  <textarea wrap='off' rows='20' cols='60' style='font-family: "Courier New", verdana, arial; width: 95%;'>{$htaccess}</textarea>
  <br><br>
  <input type='button' class='button' value='{lang_print id=466}' onClick='parent.TB_remove();'>
</div>

{include file='admin_footer.tpl'}