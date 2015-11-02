{include file='admin_header.tpl'}

{* $Id: admin_poll.tpl 12 2009-01-11 06:04:12Z john $ *}

<h2>{lang_print id=2500001}</h2>
{lang_print id=2500026}
<br />
<br />

{if $result == 1}
  <div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=191}</div>
{/if}

<form action='admin_poll.php' method='post'>


<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=192}</td>
  </tr>
  <tr>
    <td class='setting1'>{lang_print id=2500023}</td>
  </tr>
<tr>
<td class='setting2'>
  <table cellpadding='2' cellspacing='0'>
  <tr>
    <td><input type='radio' name='setting_permission_poll' id='setting_poll_enabled_1' value='1'{if  $setting.setting_permission_poll} CHECKED{/if}></td>
    <td><label for='setting_poll_enabled_1'>{lang_print id=2500024}</label></td>
  </tr>
  <tr>
    <td><input type='radio' name='setting_permission_poll' id='setting_poll_enabled_0' value='0'{if !$setting.setting_permission_poll} CHECKED{/if}></td>
    <td><label for='setting_poll_enabled_0'>{lang_print id=2500025}</label></td>
  </tr>
  </table>
</td>
</tr>
</table>
<br />



<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=2500109}</td>
  </tr>
  <tr>
    <td class='setting1'>{lang_print id=2500110}</td>
  </tr>
  <tr>
    <td class='setting2'><input type='text' class='text' name='setting_poll_html' value='{$setting.setting_poll_html}' maxlength='250' size='60' /></td>
  </tr>
</table>
<br />



{lang_block id=173 var=langBlockTemp}<input type='submit' class='button' value='{$langBlockTemp}'>{/lang_block}
<input type='hidden' name='task' value='dosave'>


</form>


{include file='admin_footer.tpl'}