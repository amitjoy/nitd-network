{include file='header.tpl'}

{* $Id: user_account_pass.tpl 8 2009-01-11 06:02:53Z john $ *}

<table class='tabs' cellpadding='0' cellspacing='0'>
<tr>
<td class='tab0'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_account.php'>{lang_print id=655}</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_account_privacy.php'>{lang_print id=1055}</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab1' NOWRAP><a href='user_account_pass.php'>{lang_print id=756}</a></td>
{if $user->level_info.level_profile_delete != 0}<td class='tab'>&nbsp;</td><td class='tab2' NOWRAP><a href='user_account_delete.php'>{lang_print id=757}</a></td>{/if}
<td class='tab3'>&nbsp;</td>
</tr>
</table>

<img src='./images/icons/privacy48.gif' border='0' class='icon_big'>
<div class='page_header'>{lang_print id=756}</div>
<div>{lang_print id=758}</div>
<br />

{* SHOW SUCCESS OR ERROR MESSAGE *}
{if $result != 0}
  <div class='success'><img src='./images/success.gif' border='0' class='icon'> {lang_print id=191}</div><br>
{elseif $is_error != 0}
  <div class='error'><img src='./images/error.gif' border='0' class='icon'> {lang_print id=$is_error}</div><br>
{/if}

<form action='user_account_pass.php' method='POST'>
<table cellpadding='0' cellspacing='0'>
<tr>
<td class='form1'>{lang_print id=269}</td>
<td class='form2'><input type='password' name='password_old' class='text' size='30' maxlength='50'></td>
</tr>
<tr>
<td class='form1'>{lang_print id=46}</td>
<td class='form2'><input type='password' name='password_new' class='text' size='30' maxlength='50'></td>
</tr>
<tr>
<td class='form1'>{lang_print id=47}</td>
<td class='form2'><input type='password' name='password_new2' class='text' size='30' maxlength='50'></td>
</tr>
<tr>
<td class='form1'>&nbsp;</td>
<td class='form2'><input type='submit' class='button' value='{lang_print id=173}'></td>
</tr>
</table>
<input type='hidden' name='task' value='dosave'>
</form>

{include file='footer.tpl'}