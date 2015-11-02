{include file='admin_header.tpl'}

{* $Id: admin_emails_config.tpl 8 2010-03-02 20:40:53Z ta_kun $ *}

<h2>Email Settings For NITD Network Activities</h2>
This is the page from where NITD Network Admin can choose the email delivery option methods.

<br><br>

{if $result != 0}
<div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=191}</div>
{/if}

<table cellpadding='0' cellspacing='0' width='600'>
<tr><form action='admin_emails_config.php' method='POST'>
<td class='header'>Email Settings For NITD Network</td>
</tr>
<td class='setting1'>
Choose option for sending emails for NITD Network Activities
</td>
</tr>
<tr>
<td class='setting2'>
	<div style="font-weight: bold;">Profiles</div>
      <table cellpadding='2' cellspacing='0'>
        <tr>
          <td><input type='radio' name='email_method' id='email_method_0' value='mail'{if $setting_email.email_method == "mail"} checked{/if} /></td>
          <td><label for='email_method_1'>PHP mail function</label></td>
        </tr>
        <tr>
          <td><input type='radio' name='email_method' id='email_method_1' value='smtp'{if $setting_email.email_method == "smtp"} checked{/if} /></td>
          <td><label for='email_method_1'>Send Email by SMTP</label></td>
        </tr>
      </table>
</td>
</tr>
<tr>
<td class='setting2'>
	<div style="font-weight: bold;">Enter SMTP server details:</div>
      <table cellpadding='2' cellspacing='0'>
        <tr>
          <td width="25%" valign="top">
          	SMTP Host Name:<br>
          	<input type="text" name="smtp_host" value="{$setting_email.smtp_host|htmlspecialchars}" style="width:100%;" /><br>
          	(ex: admin@amitinside.com)
          </td>
          <td width="25%" valign="top">
          	SMTP User Name:<br>
          	<input type="text" name="smtp_user" AUTOCOMPLETE="off" value="{$setting_email.smtp_user|htmlspecialchars}" style="width:100%;" /><br>
          	(ex: admin@amitinside.com)
          </td>
          <td width="25%" valign="top">
          	SMTP Password:<br>
          	<input type="password" name="smtp_pass" AUTOCOMPLETE="off" value="{$setting_email.smtp_pass|htmlspecialchars}" style="width:100%;" />
          </td>
          <td width="25%" valign="top">
          	SMTP Port:<br>
          	<input type="text" name="smtp_port" value="{$setting_email.smtp_port|htmlspecialchars}" style="width:100%;" /><br>
          	(ex: 25, 465)
          </td>
        </tr>
      </table>
</td>
</tr>
</table>

<br>

<input type='submit' class='button' value='{lang_print id=173}'>
<input type='hidden' name='task' value='dosave'>
</form>


{include file='admin_footer.tpl'}