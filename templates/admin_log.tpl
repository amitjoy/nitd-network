{include file='admin_header.tpl'}

{* $Id: admin_log.tpl 8 2009-01-11 06:02:53Z john $ *}

<h2>{lang_print id=25}</h2>
{lang_print id=86}
<br />
<br />

<table cellpadding='0' cellspacing='0' class='list'>
<tr>
<td class='header'>{lang_print id=87}</td>
<td class='header'>{lang_print id=88}</td>
<td class='header'>{lang_print id=89}</td>
<td class='header'>{lang_print id=90}</td>
<td class='header'>{lang_print id=91}</td>
</tr>
{section name=login_loop loop=$logins}
<tr class='{cycle values="background1,background2"}'>
<td class='item'>{$logins[login_loop].login_id}</td>
<td class='item'>{$datetime->cdate("g:i:s a, M. j", $datetime->timezone($logins[login_loop].login_date, $setting.setting_timezone))}</td>
<td class='item'><a href='mailto:{$logins[login_loop].login_email}'>{$logins[login_loop].login_email}</a>&nbsp;</td>
<td class='item'>
{if $logins[login_loop].login_result}{lang_print id=92}{else}<font color='#FF0000'>{lang_print id=93}</font>{/if}
</td>
<td class='item'>{$logins[login_loop].login_ip}&nbsp;</td>
</tr>
{/section}
</table> 


{include file='admin_footer.tpl'}