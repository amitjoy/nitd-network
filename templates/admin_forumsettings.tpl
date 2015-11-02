{include file='admin_header.tpl'}

<h2>{lang_print id=6000030}</h2>
{lang_print id=6000031}
<br />
<br />







{if $result != 0}
  <div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=191}</div>
{/if}


<form action='admin_forumsettings.php' method='POST'>


<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=6000035}</td>
</tr>
<td class='setting1'>
{lang_print id=6000036}
</td></tr><tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr><td><input type='radio' name='setting_forum_status' id='status_1' value='1'{if $setting.setting_forum_status == 1} CHECKED{/if}>&nbsp;</td><td><label for='status_1'>{lang_print id=6000037}</label></td></tr>
  <tr><td><input type='radio' name='setting_forum_status' id='status_2' value='2'{if $setting.setting_forum_status == 2} CHECKED{/if}>&nbsp;</td><td><label for='status_2'>{lang_print id=6000038}</label></td></tr>
  <tr><td><input type='radio' name='setting_forum_status' id='status_0' value='0'{if $setting.setting_forum_status == 0} CHECKED{/if}>&nbsp;</td><td><label for='status_0'>{lang_print id=6000039}</label></td></tr>
  </table>
</td></tr></table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>
<td class='header'>{lang_print id=192}</td>
</tr>
<td class='setting1'>
  {lang_print id=6000032}
</td>
</tr>
<tr>
<td class='setting2'>
  <table cellpadding='2' cellspacing='0'>
  <tr>
  <td><input type='radio' name='setting_permission_forum' id='permission_forum_1' value='1'{if $setting.setting_permission_forum == 1} CHECKED{/if}></td>
  <td><label for='permission_forum_1'>{lang_print id=6000033}</label></td>
  </tr>
  <tr>
  <td><input type='radio' name='setting_permission_forum' id='permission_forum_0' value='0'{if $setting.setting_permission_forum == 0} CHECKED{/if}></td>
  <td><label for='permission_forum_0'>{lang_print id=6000034}</label></td>
  </tr>
  </table>
</td>
</tr>
</table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>
<td class='header'>{lang_print id=6000040}</td>
</tr>
<td class='setting1'>
  {lang_print id=6000041}
</td>
</tr>
<tr>
<td class='setting2'>
  {lang_print id=6000042}
  <table cellpadding='2' cellspacing='0'>
  <tr>
  <td><input type='checkbox' name='setting_forum_modprivs[0]' id='modprivs_edit' value='1'{if $setting.setting_forum_modprivs|substr:0:1 == 1} CHECKED{/if}></td>
  <td><label for='modprivs_edit'>{lang_print id=6000043}</label></td>
  </tr>
  <tr>
  <td><input type='checkbox' name='setting_forum_modprivs[1]' id='modprivs_delete' value='1'{if $setting.setting_forum_modprivs|substr:1:1 == 1} CHECKED{/if}></td>
  <td><label for='modprivs_delete'>{lang_print id=6000044}</label></td>
  </tr>
  <tr>
  <td><input type='checkbox' name='setting_forum_modprivs[2]' id='modprivs_move' value='1'{if $setting.setting_forum_modprivs|substr:2:1 == 1} CHECKED{/if}></td>
  <td><label for='modprivs_move'>{lang_print id=6000045}</label></td>
  </tr>
  <tr>
  <td><input type='checkbox' name='setting_forum_modprivs[3]' id='modprivs_close' value='1'{if $setting.setting_forum_modprivs|substr:3:1 == 1} CHECKED{/if}></td>
  <td><label for='modprivs_close'>{lang_print id=6000046}</label></td>
  </tr>
  <tr>
  <td><input type='checkbox' name='setting_forum_modprivs[4]' id='modprivs_sticky' value='1'{if $setting.setting_forum_modprivs|substr:4:1 == 1} CHECKED{/if}></td>
  <td><label for='modprivs_sticky'>{lang_print id=6000047}</label></td>
  </tr>
  </table>
</td>
</tr>
</table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=6000048}</td>
</tr>
<td class='setting1'>
{lang_print id=6000049}
</td></tr><tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr><td><input type='radio' name='setting_forum_code' id='code_1' value='1'{if $setting.setting_forum_code == 1} CHECKED{/if}>&nbsp;</td><td><label for='code_1'>{lang_print id=6000050}</label></td></tr>
  <tr><td><input type='radio' name='setting_forum_code' id='code_0' value='0'{if $setting.setting_forum_code == 0} CHECKED{/if}>&nbsp;</td><td><label for='code_0'>{lang_print id=6000051}</label></td></tr>
  </table>
</td></tr></table>
  
<br>

<input type='submit' class='button' value='{lang_print id=173}'>
<input type='hidden' name='task' value='dosave'>
</form>


{include file='admin_footer.tpl'}