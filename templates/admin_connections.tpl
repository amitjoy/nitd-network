{include file='admin_header.tpl'}

{* $Id: admin_connections.tpl 8 2009-01-11 06:02:53Z john $ *}

<h2>{lang_print id=18}</h2>
{lang_print id=227}

<br><br>

{* SHOW SUCCESS MESSAGE *}
{if $result != 0}
<div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=191}</div>
{/if}

{* JAVASCRIPT FOR ADDING FRIENDSHIP TYPES *}
{literal}
<script type="text/javascript">
<!-- Begin
function addInput(fieldname) {
  var newdiv = document.createElement('div');
  newdiv.innerHTML = "<input type='text' name='setting_connection_types[]' class='text' size='30' maxlength='50'>&nbsp;<br>";
  $(fieldname).appendChild(newdiv);
}
// End -->
</script>
{/literal}


<form action='admin_connections.php' method='POST' name='info'>
<table cellpadding='0' cellspacing='0' width='600'>
<tr><td class='header'>{lang_print id=228}</td></tr>
<tr><td class='setting1'>
{lang_print id=229}
</td></tr><tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'><tr>
  <td style='vertical-align: top;'><input type='radio' name='setting_connection_allow' id='invitation_0' value='0'{if $setting.setting_connection_allow == 0} CHECKED{/if}>&nbsp;</td>
  <td><label for='invitation_0'><b>{lang_print id=230}</b><br>{lang_print id=231}</label>
  </td></tr></table>
</td></tr>
<tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'><tr>
  <td style='vertical-align: top;'><input type='radio' name='setting_connection_allow' id='invitation_3' value='3'{if $setting.setting_connection_allow == 3} CHECKED{/if}>&nbsp;</td>
  <td><label for='invitation_3'><b>{lang_print id=232}</b><br>{lang_print id=233}</label>
  </td></tr></table>
</td></tr>
<tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'><tr>
  <td style='vertical-align: top;'><input type='radio' name='setting_connection_allow' id='invitation_2' value='2'{if $setting.setting_connection_allow == 2} CHECKED{/if}>&nbsp;</td>
  <td><label for='invitation_2'><b>{lang_print id=234}</b><br>{lang_print id=235}</label>
  </td></tr></table>
</td></tr>
<tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'><tr>
  <td style='vertical-align: top;'><input type='radio' name='setting_connection_allow' id='invitation_1' value='1'{if $setting.setting_connection_allow == 1} CHECKED{/if}>&nbsp;</td>
  <td><label for='invitation_1'><b>{lang_print id=236}</b><br>{lang_print id=237}</label>
  </td></tr></table>
</td></tr>
</table>
<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr><td class='header'>{lang_print id=238}</td></tr>
<tr><td class='setting1'>
{lang_print id=239}
</td></tr><tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'><tr>
  <td style='vertical-align: top;'><input type='radio' name='setting_connection_framework' id='framework_0' value='0'{if $setting.setting_connection_framework == 0} CHECKED{/if}>&nbsp;</td>
  <td><label for='framework_0'><b>{lang_print id=240}</b><br>{lang_print id=241}</label>
  </td></tr></table>
</td></tr><tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'><tr>
  <td style='vertical-align: top;'><input type='radio' name='setting_connection_framework' id='framework_1' value='1'{if $setting.setting_connection_framework == 1} CHECKED{/if}>&nbsp;</td>
  <td><label for='framework_1'><b>{lang_print id=242}</b><br>{lang_print id=243}</label>
  </td></tr></table>
</td></tr><tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'><tr>
  <td style='vertical-align: top;'><input type='radio' name='setting_connection_framework' id='framework_2' value='2'{if $setting.setting_connection_framework == 2} CHECKED{/if}>&nbsp;</td>
  <td><label for='framework_2'><b>{lang_print id=244}</b><br>{lang_print id=245}</label>
  </td></tr></table>
</td></tr><tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'><tr>
  <td style='vertical-align: top;'><input type='radio' name='setting_connection_framework' id='framework_3' value='3'{if $setting.setting_connection_framework == 3} CHECKED{/if}>&nbsp;</td>
  <td><label for='framework_3'><b>{lang_print id=246}</b><br>{lang_print id=247}</label>
  </td></tr></table>
</td></tr></table>
<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr><td class='header'>{lang_print id=248}</td></tr>
<tr><td class='setting1'>
{lang_print id=249}
</td></tr><tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr><td>{lang_print id=248}</td></tr>
  <tr><td id='newtype'>
{section name=type_loop loop=$types}
<input type='text' class='text' name='setting_connection_types[]' value='{$types[type_loop]}' size='30' maxlength='50'>&nbsp;<br>
{/section}
  </td></tr>
  <tr><td><a href="javascript:addInput('newtype')">{lang_print id=250}</a></td></tr>
  </table>
</td></tr><tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr><td>&nbsp;</td><td><b>{lang_print id=251}</b></td></tr>
  <tr>
  <td style='vertical-align: top;'><input type='radio' name='setting_connection_other' id='other_1' value='1'{if $setting.setting_connection_other == 1} CHECKED{/if}>&nbsp;</td>
  <td><label for='other_1'>{lang_print id=252}</label></td>
  </tr><tr>
  <td style='vertical-align: top;'><input type='radio' name='setting_connection_other' id='other_0' value='0'{if $setting.setting_connection_other == 0} CHECKED{/if}>&nbsp;</td>
  <td><label for='other_0'>{lang_print id=253}</label></td>
  </tr></table>
</td></tr><tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr><td>&nbsp;</td><td><b>{lang_print id=254}</b></td></tr>
  <tr>
  <td style='vertical-align: top;'><input type='radio' name='setting_connection_explain' id='explain_1' value='1'{if $setting.setting_connection_explain == 1} CHECKED{/if}>&nbsp;</td>
  <td><label for='explain_1'>{lang_print id=255}</label></td>
  </tr><tr>
  <td style='vertical-align: top;'><input type='radio' name='setting_connection_explain' id='explain_0' value='0'{if $setting.setting_connection_explain == 0} CHECKED{/if}>&nbsp;</td>
  <td><label for='explain_0'>{lang_print id=256}</label></td>
  </tr></table>
</td></tr></table>
<br>


<input type='submit' class='button' value='{lang_print id=173}'>
<input type='hidden' name='task' value='dosave'>
</form>
{include file='admin_footer.tpl'}