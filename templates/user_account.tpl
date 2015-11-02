{include file='header.tpl'}

{* $Id: user_account.tpl 8 2009-01-11 06:02:53Z john $ *}

<table class='tabs' cellpadding='0' cellspacing='0'>
<tr>
<td class='tab0'>&nbsp;</td>
<td class='tab1' NOWRAP><a href='user_account.php'>{lang_print id=655}</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_account_privacy.php'>{lang_print id=1055}</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_account_pass.php'>{lang_print id=756}</a></td>
{if $user->level_info.level_profile_delete != 0}<td class='tab'>&nbsp;</td><td class='tab2' NOWRAP><a href='user_account_delete.php'>{lang_print id=757}</a></td>{/if}
<td class='tab3'>&nbsp;</td>
</tr>
</table>

<img src='./images/icons/settings48.gif' border='0' class='icon_big'>
<div class='page_header'>{lang_print id=755}</div>
<div>{lang_print id=808}</div>
<br />
<br />

{* SHOW ERROR OR SUCCESS MESSAGES *}
{if $result != 0}
  <table cellpadding='0' cellspacing='0'><tr><td class='success'>
  {capture assign="old_subnet_name"}{lang_print id=$old_subnet_name}{/capture}
  {capture assign="new_subnet_name"}{lang_print id=$new_subnet_name}{/capture}
  <img src='./images/success.gif' border='0' class='icon'>{lang_sprintf id=$result 1=$old_subnet_name 2=$new_subnet_name}
  </td></tr></table>
{elseif $is_error != 0}
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='error'><img src='./images/error.gif' border='0' class='icon'>{lang_print id=$is_error}</td></tr>
  </table>
{/if}

<form action='user_account.php' method='post' name='info'>
<table cellpadding='0' cellspacing='0'>
<tr>
<td class='form1'></td>
<td class='form2'>
  <input name='user_email' type='hidden' class='text' size='40' maxlength='70' value='{$user->user_info.user_email}'>
  {if $user->user_info.user_email != $user->user_info.user_newemail && $user->user_info.user_newemail != "" && $setting.setting_signup_verify != 0}<div class='form_desc'>{lang_sprintf id=818 1=$user->user_info.user_newemail}</div>{/if}
  {if $setting.setting_subnet_field1_id == 0 || $setting.setting_subnet_field2_id == 0}{capture assign='current_subnet'}{lang_print id=$user->subnet_info.subnet_name}{/capture}<div class='form_desc'>{lang_sprintf id=766 1=$current_subnet}</div>{/if}
</td>
</tr>

{if $user->level_info.level_profile_change != 0 && $setting.setting_username}
  <tr>
  <td class='form1'></td>
  <td class='form2'>
    <input name='user_username' type='text' class='text' size='40' maxlength='50' value='{$user->user_info.user_username}'>
    {capture assign=tip}{lang_print id=809}{/capture}
    <img src='./images/icons/tip.gif' border='0' class='Tips1' title='{$tip|replace:"'":"&#039;"}'>
    <div class='form_desc'>{lang_print id=810}</div>
  </td>
  </tr>
{/if}

{if $cats|@count > 1}
  <tr>
  <td class='form1'>{lang_print id=709}:</td>
  <td class='form2'>
    <select name='user_profilecat_id'>
    {section name=cat_loop loop=$cats}
      <option value='{$cats[cat_loop].cat_id}'{if $user->user_info.user_profilecat_id == $cats[cat_loop].cat_id} selected='selected'{/if}>{lang_print id=$cats[cat_loop].cat_title}</option>
    {/section}
    </select>
    <div class='form_desc'>{lang_print id=1014}</div>
  </td>
  </tr>
{/if}

<tr>
<td class='form1'>{lang_print id=206}:</td>
<td class='form2'>
  <select name='user_timezone'>
  <option value='-8'{if $user->user_info.user_timezone == "-8"} SELECTED{/if}>Pacific Time (US & Canada)</option>
  <option value='-7'{if $user->user_info.user_timezone == "-7"} SELECTED{/if}>Mountain Time (US & Canada)</option>
  <option value='-6'{if $user->user_info.user_timezone == "-6"} SELECTED{/if}>Central Time (US & Canada)</option>
  <option value='-5'{if $user->user_info.user_timezone == "-5"} SELECTED{/if}>Eastern Time (US & Canada)</option>
  <option value='-4'{if $user->user_info.user_timezone == "-4"} SELECTED{/if}>Atlantic Time (Canada)</option>
  <option value='-9'{if $user->user_info.user_timezone == "-9"} SELECTED{/if}>Alaska (US & Canada)</option>
  <option value='-10'{if $user->user_info.user_timezone == "-10"} SELECTED{/if}>Hawaii (US)</option>
  <option value='-11'{if $user->user_info.user_timezone == "-11"} SELECTED{/if}>Midway Island, Samoa</option>
  <option value='-12'{if $user->user_info.user_timezone == "-12"} SELECTED{/if}>Eniwetok, Kwajalein</option>
  <option value='-3.3'{if $user->user_info.user_timezone == "-3.3"} SELECTED{/if}>Newfoundland</option>
  <option value='-3'{if $user->user_info.user_timezone == "-3"} SELECTED{/if}>Brasilia, Buenos Aires, Georgetown</option>
  <option value='-2'{if $user->user_info.user_timezone == "-2"} SELECTED{/if}>Mid-Atlantic</option>
  <option value='-1'{if $user->user_info.user_timezone == "-1"} SELECTED{/if}>Azores, Cape Verde Is.</option>
  <option value='0'{if $user->user_info.user_timezone == "0"} SELECTED{/if}>Greenwich Mean Time (Lisbon, London)</option>
  <option value='1'{if $user->user_info.user_timezone == "1"} SELECTED{/if}>Amsterdam, Berlin, Paris, Rome, Madrid</option>
  <option value='2'{if $user->user_info.user_timezone == "2"} SELECTED{/if}>Athens, Helsinki, Istanbul, Cairo, E. Europe</option>
  <option value='3'{if $user->user_info.user_timezone == "3"} SELECTED{/if}>Baghdad, Kuwait, Nairobi, Moscow</option>
  <option value='3.3'{if $user->user_info.user_timezone == "3.3"} SELECTED{/if}>Tehran</option>
  <option value='4'{if $user->user_info.user_timezone == "4"} SELECTED{/if}>Abu Dhabi, Kazan, Muscat</option>
  <option value='4.3'{if $user->user_info.user_timezone == "4.3"} SELECTED{/if}>Kabul</option>
  <option value='5'{if $user->user_info.user_timezone == "5"} SELECTED{/if}>Islamabad, Karachi, Tashkent</option>
  <option value='5.5'{if $user->user_info.user_timezone == "5.5"} SELECTED{/if}>Bombay, Calcutta, New Delhi</option>
  <option value='6'{if $user->user_info.user_timezone == "6"} SELECTED{/if}>Almaty, Dhaka</option>
  <option value='7'{if $user->user_info.user_timezone == "7"} SELECTED{/if}>Bangkok, Jakarta, Hanoi</option>
  <option value='8'{if $user->user_info.user_timezone == "8"} SELECTED{/if}>Beijing, Hong Kong, Singapore, Taipei</option>
  <option value='9'{if $user->user_info.user_timezone == "9"} SELECTED{/if}>Tokyo, Osaka, Sapporto, Seoul, Yakutsk</option>
  <option value='9.3'{if $user->user_info.user_timezone == "9.3"} SELECTED{/if}>Adelaide, Darwin</option>
  <option value='10'{if $user->user_info.user_timezone == "10"} SELECTED{/if}>Brisbane, Melbourne, Sydney, Guam</option>
  <option value='11'{if $user->user_info.user_timezone == "11"} SELECTED{/if}>Magadan, Soloman Is., New Caledonia</option>
  <option value='12'{if $user->user_info.user_timezone == "12"} SELECTED{/if}>Fiji, Kamchatka, Marshall Is., Wellington</option>
  </select>
</td>
</tr>

{* SHOW NOTIFICATION SETTINGS *}


<tr>
<td class='form1'>&nbsp;</td>
<td class='form2'><input type='submit' class='button' value='{lang_print id=173}'></td>
</tr>
</table>
<input type='hidden' name='task' value='dosave'>
</form>

{include file='footer.tpl'}