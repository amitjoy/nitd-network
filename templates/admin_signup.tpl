{include file='admin_header.tpl'}

{* $Id: admin_signup.tpl 8 2009-01-11 06:02:53Z john $ *}

<h2>{lang_print id=13}</h2>
{lang_print id=410}
<br />
<br />

{if $result != 0}
  <div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=191}</div>
{/if}

<form action='admin_signup.php' method='POST'>

<table cellpadding='0' cellspacing='0' width='600'>
<tr><td class='header'>{lang_print id=411}</td></tr>
<tr><td class='setting1'>
{lang_print id=412}
</td></tr>

{section name=cat_loop loop=$cats}
  <tr>
  <td class='setting2'>
  <input type='checkbox' name='cat_signup[]' id='cat_signup_{$cats[cat_loop].cat_id}' value='{$cats[cat_loop].cat_id}'{if $cats[cat_loop].cat_signup == 1} CHECKED{/if}><b>{lang_print id=$cats[cat_loop].cat_title}</b>
  {assign var="num_fields" value=0}
  {section name=subcat_loop loop=$cats[cat_loop].subcats}
    {if $cats[cat_loop].subcats[subcat_loop].fields|@count != 0}{assign var="num_fields" value=1}{/if}
    {section name=field_loop loop=$cats[cat_loop].subcats[subcat_loop].fields}
      <table cellpadding='3' cellspacing='0' style='margin-left: 15px;'>
      <tr>
      <td><input type='checkbox' name='field_signup[]' id='field_signup_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}' value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}'{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_signup == 1} CHECKED{/if}></td>
      <td><label for='field_signup_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}'>{lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_title}</label></td>
      </tr>
      </table>
    {/section}
  {/section}
  {if $num_fields == 0}<br>{lang_print id=455}{/if}
  </td>
  </tr>
{/section}

</table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr><td class='header'>{lang_print id=413}</td></tr>
<tr><td class='setting1'>
{lang_print id=414}
</td></tr><tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr><td><input type='radio' name='setting_signup_photo' id='photo_1' value='1'{if $setting.setting_signup_photo == 1} CHECKED{/if}>&nbsp;</td><td><label for='photo_1'>{lang_print id=415}</label></td></tr>
  <tr><td><input type='radio' name='setting_signup_photo' id='photo_0' value='0'{if $setting.setting_signup_photo == 0} CHECKED{/if}>&nbsp;</td><td><label for='photo_0'>{lang_print id=416}</label></td></tr>
  </table>
</td></tr></table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr><td class='header'>{lang_print id=417}</td></tr>
<tr><td class='setting1'>
{lang_print id=418}
</td></tr><tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr><td><input type='radio' name='setting_signup_enable' id='enable_1' value='1'{if $setting.setting_signup_enable == 1} CHECKED{/if}>&nbsp;</td><td><label for='enable_1'>{lang_print id=419}</label></td></tr>
  <tr><td><input type='radio' name='setting_signup_enable' id='enable_0' value='0'{if $setting.setting_signup_enable == 0} CHECKED{/if}>&nbsp;</td><td><label for='enable_0'>{lang_print id=420}</label></td></tr>
  </table>
</td></tr></table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr><td class='header'>{lang_print id=421}</td></tr>
<tr><td class='setting1'>
{lang_print id=422}
</td></tr><tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr><td><input type='radio' name='setting_signup_welcome' id='welcome_1' value='1'{if $setting.setting_signup_welcome == 1} CHECKED{/if}>&nbsp;</td><td><label for='welcome_1'>{lang_print id=423}</label></td></tr>
  <tr><td><input type='radio' name='setting_signup_welcome' id='welcome_0' value='0'{if $setting.setting_signup_welcome == 0} CHECKED{/if}>&nbsp;</td><td><label for='welcome_0'>{lang_print id=424}</label></td></tr>
  </table>
</td></tr></table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr><td class='header'>{lang_print id=425}</td></tr>
<tr><td class='setting1'>
{lang_print id=426}
</td></tr><tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr><td><input type='radio' name='setting_signup_invite' id='invite_2' value='2'{if $setting.setting_signup_invite == 2} CHECKED{/if}>&nbsp;</td><td><label for='invite_2'>{lang_print id=427}</label></td></tr>
  <tr><td><input type='radio' name='setting_signup_invite' id='invite_1' value='1'{if $setting.setting_signup_invite == 1} CHECKED{/if}>&nbsp;</td><td><label for='invite_1'>{lang_print id=428}</label></td></tr>
  <tr><td><input type='radio' name='setting_signup_invite' id='invite_0' value='0'{if $setting.setting_signup_invite == 0} CHECKED{/if}>&nbsp;</td><td><label for='invite_0'>{lang_print id=429}</label></td></tr>
  </table>
</td></tr>
<tr><td class='setting1'>{lang_print id=430}</td></tr>
<tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr><td><input type='radio' name='setting_signup_invite_checkemail' id='invite_checkemail_1' value='1'{if $setting.setting_signup_invite_checkemail == 1} CHECKED{/if}>&nbsp;</td><td><label for='invite_checkemail_1'>{lang_print id=431}</label></td></tr>
  <tr><td><input type='radio' name='setting_signup_invite_checkemail' id='invite_checkemail_0' value='0'{if $setting.setting_signup_invite_checkemail == 0} CHECKED{/if}>&nbsp;</td><td><label for='invite_checkemail_0'>{lang_print id=432}</label></td></tr>
  </table>
</td></tr>
<tr><td class='setting1'>{lang_print id=433}</td></tr>
<tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr><td><input type='text' name='setting_signup_invite_numgiven' size='2' maxlength='3' class='text' value='{$setting.setting_signup_invite_numgiven}'>&nbsp;</td><td>{lang_print id=434}</td></tr>
  </table>
</td></tr>
</table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr><td class='header'>{lang_print id=435}</td></tr>
<tr><td class='setting1'>
{lang_print id=436}
</td></tr><tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr><td><input type='radio' name='setting_signup_invitepage' id='invitepage_1' value='1'{if $setting.setting_signup_invitepage == 1} CHECKED{/if}>&nbsp;</td><td><label for='invitepage_1'>{lang_print id=437}</label></td></tr>
  <tr><td><input type='radio' name='setting_signup_invitepage' id='invitepage_0' value='0'{if $setting.setting_signup_invitepage == 0} CHECKED{/if}>&nbsp;</td><td><label for='invitepage_0'>{lang_print id=438}</label></td></tr>
  </table>
</td></tr></table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr><td class='header'>{lang_print id=439}</td></tr>
<tr><td class='setting1'>
{lang_print id=440}
</td></tr><tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr><td><input type='radio' name='setting_signup_verify' id='verify_1' value='1'{if $setting.setting_signup_verify == 1} CHECKED{/if}>&nbsp;</td><td><label for='verify_1'>{lang_print id=441}</label></td></tr>
  <tr><td><input type='radio' name='setting_signup_verify' id='verify_0' value='0'{if $setting.setting_signup_verify == 0} CHECKED{/if}>&nbsp;</td><td><label for='verify_0'>{lang_print id=442}</label></td></tr>
  </table>
</td></tr></table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr><td class='header'>{lang_print id=443}</td></tr>
<tr><td class='setting1'>
{lang_print id=444}
</td></tr><tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr><td><input type='radio' name='setting_signup_code' id='code_1' value='1'{if $setting.setting_signup_code == 1} CHECKED{/if}>&nbsp;</td><td><label for='code_1'>{lang_print id=445}</label></td></tr>
  <tr><td><input type='radio' name='setting_signup_code' id='code_0' value='0'{if $setting.setting_signup_code == 0} CHECKED{/if}>&nbsp;</td><td><label for='code_0'>{lang_print id=446}</label></td></tr>
  </table>
</td></tr></table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr><td class='header'>{lang_print id=447}</td></tr>
<tr><td class='setting1'>
{lang_print id=448}
</td></tr><tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr><td><input type='radio' name='setting_signup_randpass' id='randpass_1' value='1'{if $setting.setting_signup_randpass == 1} CHECKED{/if}>&nbsp;</td><td><label for='randpass_1'>{lang_print id=449}</label></td></tr>
  <tr><td><input type='radio' name='setting_signup_randpass' id='randpass_0' value='0'{if $setting.setting_signup_randpass == 0} CHECKED{/if}>&nbsp;</td><td><label for='randpass_0'>{lang_print id=450}</label></td></tr>
  </table>
</td></tr></table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr><td class='header'>{lang_print id=451}</td></tr>
<tr><td class='setting1'>
{lang_print id=452}
</td></tr><tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr><td><input type='radio' name='setting_signup_tos' id='tos_0' value='0'{if $setting.setting_signup_tos == 0} CHECKED{/if} onChange="{literal}if(this.checked == true){ $('tostext').disabled=true; $('tostext').style.backgroundColor='#DDDDDD'; }{/literal}">&nbsp;</td><td><label for='tos_0'>{lang_print id=454}</label></td></tr>
  <tr><td><input type='radio' name='setting_signup_tos' id='tos_1' value='1'{if $setting.setting_signup_tos == 1} CHECKED{/if} onChange="{literal}if(this.checked == true){ $('tostext').disabled=false; $('tostext').style.backgroundColor='#FFFFFF'; }{/literal}">&nbsp;</td><td><label for='tos_1'>{lang_print id=453}</label></td></tr>
  </table>
<br>
<textarea class='text' name='setting_signup_tostext' id='tostext' rows='5' cols='50' {if $setting.setting_signup_tos == 0}disabled='disabled' style='background: #DDDDDD; width: 100%;'{else}style='width: 100%;'{/if}>{lang_print id=1210}</textarea>
</td></tr></table>

<br>

<input type='submit' class='button' value='{lang_print id=173}'>
<input type='hidden' name='task' value='dosave'>
</form>


{include file='admin_footer.tpl'}