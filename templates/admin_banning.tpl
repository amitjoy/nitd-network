{include file='admin_header.tpl'}

{* $Id: admin_banning.tpl 8 2009-01-11 06:02:53Z john $ *}

<h2>{lang_print id=17}</h2>
{lang_print id=210}

<br><br>

{if $result != 0}
<div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=191}</div>
{/if}

<form action='admin_banning.php' method='POST'>

<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=211}</td>
  </tr>
  <tr>
    <td class='setting1'>
      {lang_print id=212}
      <br />
      <br />
      <textarea name='setting_banned_ips' rows='3' cols='40' class='text' style='width: 100%;'>{$setting.setting_banned_ips}</textarea>
    </td>
  </tr>
</table>
<br />


<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=213}</td>
  </tr>
  <tr>
    <td class='setting1'>
      {lang_print id=214}
      <br />
      <br />
      <textarea name='setting_banned_emails' rows='3' cols='40' class='text' style='width: 100%;'>{$setting.setting_banned_emails}</textarea>
    </td>
  </tr>
</table>
<br />


<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=215}</td>
  </tr>
  <tr>
    <td class='setting1'>
      {lang_print id=216}
      <br />
      <br />
      <textarea name='setting_banned_usernames' rows='3' cols='40' class='text' style='width: 100%;'>{$setting.setting_banned_usernames}</textarea>
    </td>
  </tr>
</table>
<br />


<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=217}</td>
  </tr>
  <tr>
    <td class='setting1'>
      {lang_print id=218}
      <br />
      <br />
      <textarea name='setting_banned_words' rows='3' cols='40' class='text' style='width: 100%;'>{$setting.setting_banned_words}</textarea>
    </td>
  </tr>
</table>
<br />


<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=219}</td>
  </tr>
  <tr>
    <td class='setting1'>
    {lang_print id=220}
    </td>
  </tr>
  <tr>
    <td class='setting2'>
      <table cellpadding='0' cellspacing='0'>
        <tr>
          <td><input type='radio' name='setting_comment_code' id='comment_code_1' value='1'{if $setting.setting_comment_code == 1} CHECKED{/if}>&nbsp;</td>
          <td><label for='comment_code_1'>{lang_print id=221}</label></td>
        </tr>
        <tr>
          <td><input type='radio' name='setting_comment_code' id='comment_code_0' value='0'{if $setting.setting_comment_code == 0} CHECKED{/if}>&nbsp;</td>
          <td><label for='comment_code_0'>{lang_print id=222}</label></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br />


<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=223}</td>
  </tr>
  <tr>
    <td class='setting1'>{lang_print id=224}</td>
  </tr>
  <tr>
    <td class='setting2'>
      <table cellpadding='0' cellspacing='0'>
        <tr>
          <td><input type='radio' name='setting_invite_code' id='invite_code_1' value='1'{if  $setting.setting_invite_code} CHECKED{/if}>&nbsp;</td>
          <td><label for='invite_code_1'>{lang_print id=225}</label></td>
        </tr>
        <tr>
          <td><input type='radio' name='setting_invite_code' id='invite_code_0' value='0'{if !$setting.setting_invite_code} CHECKED{/if}>&nbsp;</td>
          <td><label for='invite_code_0'>{lang_print id=226}</label></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br />


<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=1233}</td>
  </tr>
  
  <tr>
    <td class='setting1'>{lang_print id=1234}</td>
  </tr>
  <tr>
    <td class='setting2'>
      <table cellpadding='0' cellspacing='0'>
        <tr>
          <td><input type='radio' name='setting_login_code' id='login_code_1' value='1'{if  $setting.setting_login_code} CHECKED{/if}>&nbsp;</td>
          <td><label for='login_code_1'>{lang_print id=1235}</label></td>
        </tr>
        <tr>
          <td><input type='radio' name='setting_login_code' id='login_code_0' value='0'{if !$setting.setting_login_code} CHECKED{/if}>&nbsp;</td>
          <td><label for='login_code_0'>{lang_print id=1236}</label></td>
        </tr>
      </table>
    </td>
  </tr>
  
  <tr>
    <td class='setting1'>{lang_print id=1237}</td>
  </tr>
  
  <tr>
    <td class='setting2'>
      <input type="text" class="text" name="setting_login_code_failedcount" id="setting_login_code_failedcount" value="{$setting.setting_login_code_failedcount|default:0}" />
      <label for="setting_login_code_failedcount">{lang_print id=1238}</label>
    </td>
  </tr>
</table>
<br />


<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=1239}</td>
  </tr>
  <tr>
    <td class='setting1'>{lang_print id=1240}</td>
  </tr>
  <tr>
    <td class='setting2'>
      <table cellpadding='0' cellspacing='0'>
        <tr>
          <td><input type='radio' name='setting_contact_code' id='contact_code_1' value='1'{if  $setting.setting_contact_code} CHECKED{/if}>&nbsp;</td>
          <td><label for='contact_code_1'>{lang_print id=1241}</label></td>
        </tr>
        <tr>
          <td><input type='radio' name='setting_contact_code' id='contact_code_0' value='0'{if !$setting.setting_contact_code} CHECKED{/if}>&nbsp;</td>
          <td><label for='contact_code_0'>{lang_print id=1242}</label></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br />


<input type='submit' class='button' value='{lang_print id=173}' />
<input type='hidden' name='task' value='dosave' />

</form>



{include file='admin_footer.tpl'}