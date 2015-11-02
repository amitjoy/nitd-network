{include file='header.tpl'}

{* $Id: user_userconnection_setting.tpl 8 2009-09-16 06:02:53Z SocialEngineAddOns $ *}
<table class='tabs' cellpadding='0' cellspacing='0'>
<tr>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_friends.php'>{lang_print id=894}</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_userconnection_contacts_2degree.php'>{lang_print id=650002050}</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_userconnection_contacts_3degree.php'>{lang_print id=650002051}</a></td>
<td class='tab0'>&nbsp;</td>
<td class='tab1' NOWRAP><a href='user_userconnection_setting.php'>{lang_print id=650002063}</a></td>
<td class='tab3'>&nbsp;</td>
</tr>
</table>
<img src='./images/icons/userconnection-setting.gif' border='0' class='icon_big'>
<div class='page_header'>{lang_print id=650002029 }</div>
<div>{lang_print id=650002030}</div>
<br /><br />
{if !empty($success_message)}
	  <div class="success"><img src="./images/success.gif" class="icon" align="middle" border="0"> {lang_print id=$success_message}</div>
{/if}	
<form action="user_userconnection_setting.php" method="POST">
<div style="margin-left:50px;">	
	<input style="vertical-align:bottom;" type='radio' name='usersetting_userconnection' id='usersetting_userconnection_0' value='0' {if  empty($result.usersetting_userconnection)} checked{/if}><label for="usersetting_userconnection_0">{lang_print id=650002032 }</label><br />
	<input style="vertical-align:bottom;" type='radio' name='usersetting_userconnection' id='usersetting_userconnection_1' value='1' {if  !empty($result.usersetting_userconnection)} checked{/if}><label for="usersetting_userconnection_1">{lang_print id=650002031}</label><br /><br />
	<input type='submit' class='button' value='{lang_print id=173}'>&nbsp;
	<input type='hidden' name='task' value='edit'>
</div>
</form>
{include file='footer.tpl'}