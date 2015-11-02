{include file='admin_header.tpl'}

{* $Id: admin_invite.tpl 8 2009-01-11 06:02:53Z john $ *}

<h2>{lang_print id=22}</h2>
{lang_print id=340}

<br><br>

{if $result != 0}
<div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=341}</div>
{/if}

<table cellpadding='0' cellspacing='0' width='600'>
<form action='admin_invite.php' method='POST'>
<tr>
<td class='header'>{lang_print id=342}</td>
</tr>
<td class='setting1'>
{lang_print id=343}
<br><br>
<textarea name='invite_emails' rows='3' cols='40' class='text' style='width: 100%;'></textarea>
</td>
</tr>
</table>

<br>

<input type='submit' class='button' value='{lang_print id=22}'>
<input type='hidden' name='task' value='doinvite'>

</form>



{include file='admin_footer.tpl'}