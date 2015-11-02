{include file='admin_header.tpl'}

{* $Id: admin_emails.tpl 8 2009-01-11 06:02:53Z john $ *}

<h2>{lang_print id=20}</h2>
{lang_print id=513}

<br><br>

{if $result != 0}
<div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=191}</div>
{/if}

<table cellpadding='0' cellspacing='0' width='600'>
<tr><form action='admin_emails.php' method='POST'>
<td class='header'>{lang_print id=514}</td>
</tr>
<td class='setting1'>
{lang_print id=515}
</td>
</tr>
<tr>
<td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td width='80'>{lang_print id=516}</td>
  <td><input type='text' class='text' size='30' name='setting_email_fromname' value='{$setting.setting_email_fromname}' maxlength='70'></td>
  </tr>
  <tr>
  <td>{lang_print id=517}</td>
  <td><input type='text' class='text' size='30' name='setting_email_fromemail' value='{$setting.setting_email_fromemail}' maxlength='70'></td>
  </tr>
  </table>
</td>
</tr>
</table>

<br>

{section name=email_loop loop=$emails}
  <table cellpadding='0' cellspacing='0' width='600'>
  <tr>
  <td class='header'>{lang_print id=$emails[email_loop].systememail_title}</td>
  </tr>
  <td class='setting1'>
  {lang_print id=$emails[email_loop].systememail_desc}
  </td>
  </tr>
  <tr>
  <td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr>
    <td width='80'>{lang_print id=520}</td>
    <td><input type='text' class='text' size='30' name='subject[{$emails[email_loop].systememail_id}]' value='{lang_sprintf id=$emails[email_loop].systememail_subject 1=$emails[email_loop].systememail_vars_array[0] 2=$emails[email_loop].systememail_vars_array[1] 3=$emails[email_loop].systememail_vars_array[2] 4=$emails[email_loop].systememail_vars_array[3] 5=$emails[email_loop].systememail_vars_array[4]}' maxlength='200'></td>
    </tr><tr>
    <td valign='top'>{lang_print id=521}</td>
    {capture assign='body'}{lang_sprintf id=$emails[email_loop].systememail_body 1=$emails[email_loop].systememail_vars_array[0] 2=$emails[email_loop].systememail_vars_array[1] 3=$emails[email_loop].systememail_vars_array[2] 4=$emails[email_loop].systememail_vars_array[3] 5=$emails[email_loop].systememail_vars_array[4]}{/capture}
    <td><textarea rows='6' cols='80' class='text' name='message[{$emails[email_loop].systememail_id}]'>{$body|replace:"<br>":"\r\n"}</textarea><br>{lang_print id=578} {$emails[email_loop].systememail_vars}</td>
    </tr>
    </table>
  </td>
  </tr>
  </table>

  <br>
{/section}


<input type='submit' class='button' value='{lang_print id=173}'>
<input type='hidden' name='task' value='dosave'>
</form>


{include file='admin_footer.tpl'}