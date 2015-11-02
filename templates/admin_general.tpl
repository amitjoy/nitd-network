{include file='admin_header.tpl'}

{* $Id: admin_general.tpl 8 2009-01-11 06:02:53Z john $ *}

<h2>{lang_print id=12}</h2>
{lang_print id=190}
<br />
<br />

{if $result != 0}
<div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=191}</div>
{/if}

<table cellpadding='0' cellspacing='0' width='600'>
<tr><form action='admin_general.php' method='POST'>
<td class='header'>{lang_print id=192}</td>
</tr>
<tr>
<td class='setting1'>
{lang_print id=193}
</td>
</tr>
<tr>
<td class='setting2'>
<b>{lang_print id=194}</b><br>
<input type='radio' name='setting_permission_profile' id='permission_profile_1' value='1'{if $setting.setting_permission_profile == 1} CHECKED{/if}><label for='permission_profile_1'>{lang_print id=195}</label><br>
<input type='radio' name='setting_permission_profile' id='permission_profile_0' value='0'{if $setting.setting_permission_profile == 0} CHECKED{/if}><label for='permission_profile_0'>{lang_print id=196}</label><br>
</td>
</tr>
<tr>
<td class='setting2'>
<b>{lang_print id=197}</b><br>
<input type='radio' name='setting_permission_invite' id='permission_invite_1' value='1'{if $setting.setting_permission_invite == 1} CHECKED{/if}><label for='permission_invite_1'>{lang_print id=198}</label><br>
<input type='radio' name='setting_permission_invite' id='permission_invite_0' value='0'{if $setting.setting_permission_invite == 0} CHECKED{/if}><label for='permission_invite_0'>{lang_print id=199}</label><br>
</td>
</tr>
<tr>
<td class='setting2'>
<b>{lang_print id=200}</b><br>
<input type='radio' name='setting_permission_search' id='permission_search_1' value='1'{if $setting.setting_permission_search == 1} CHECKED{/if}><label for='permission_search_1'>{lang_print id=201}</label><br>
<input type='radio' name='setting_permission_search' id='permission_search_0' value='0'{if $setting.setting_permission_search == 0} CHECKED{/if}><label for='permission_search_0'>{lang_print id=202}</label><br>
</td>
</tr>
<tr>
<td class='setting2'>
<b>{lang_print id=203}</b><br>
<input type='radio' name='setting_permission_portal' id='permission_portal_1' value='1'{if $setting.setting_permission_portal == 1} CHECKED{/if}><label for='permission_portal_1'>{lang_print id=204}</label><br>
<input type='radio' name='setting_permission_portal' id='permission_portal_0' value='0'{if $setting.setting_permission_portal == 0} CHECKED{/if}><label for='permission_portal_0'>{lang_print id=205}</label><br>
</td>
</tr>
</table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=206}</td>
</tr>
<tr>
<td class='setting1'>
{lang_print id=207}
</td>
</tr>
<tr>
<td class='setting2'>
<select name='setting_timezone' class='text'>
<option value='-8'{if $setting.setting_timezone == "-8"} SELECTED{/if}>Pacific Time (US & Canada)</option>
<option value='-7'{if $setting.setting_timezone == "-7"} SELECTED{/if}>Mountain Time (US & Canada)</option>
<option value='-6'{if $setting.setting_timezone == "-6"} SELECTED{/if}>Central Time (US & Canada)</option>
<option value='-5'{if $setting.setting_timezone == "-5"} SELECTED{/if}>Eastern Time (US & Canada)</option>
<option value='-4'{if $setting.setting_timezone == "-4"} SELECTED{/if}>Atlantic Time (Canada)</option>
<option value='-9'{if $setting.setting_timezone == "-9"} SELECTED{/if}>Alaska (US & Canada)</option>
<option value='-10'{if $setting.setting_timezone == "-10"} SELECTED{/if}>Hawaii (US)</option>
<option value='-11'{if $setting.setting_timezone == "-11"} SELECTED{/if}>Midway Island, Samoa</option>
<option value='-12'{if $setting.setting_timezone == "-12"} SELECTED{/if}>Eniwetok, Kwajalein</option>
<option value='-3.3'{if $setting.setting_timezone == "-3.3"} SELECTED{/if}>Newfoundland</option>
<option value='-3'{if $setting.setting_timezone == "-3"} SELECTED{/if}>Brasilia, Buenos Aires, Georgetown</option>
<option value='-2'{if $setting.setting_timezone == "-2"} SELECTED{/if}>Mid-Atlantic</option>
<option value='-1'{if $setting.setting_timezone == "-1"} SELECTED{/if}>Azores, Cape Verde Is.</option>
<option value='0'{if $setting.setting_timezone == "0"} SELECTED{/if}>Greenwich Mean Time (Lisbon, London)</option>
<option value='1'{if $setting.setting_timezone == "1"} SELECTED{/if}>Amsterdam, Berlin, Paris, Rome, Madrid</option>
<option value='2'{if $setting.setting_timezone == "2"} SELECTED{/if}>Athens, Helsinki, Istanbul, Cairo, E. Europe</option>
<option value='3'{if $setting.setting_timezone == "3"} SELECTED{/if}>Baghdad, Kuwait, Nairobi, Moscow</option>
<option value='3.3'{if $setting.setting_timezone == "3.3"} SELECTED{/if}>Tehran</option>
<option value='4'{if $setting.setting_timezone == "4"} SELECTED{/if}>Abu Dhabi, Kazan, Muscat</option>
<option value='4.3'{if $setting.setting_timezone == "4.3"} SELECTED{/if}>Kabul</option>
<option value='5'{if $setting.setting_timezone == "5"} SELECTED{/if}>Islamabad, Karachi, Tashkent</option>
<option value='5.5'{if $setting.setting_timezone == "5.5"} SELECTED{/if}>Bombay, Calcutta, New Delhi</option>
<option value='6'{if $setting.setting_timezone == "6"} SELECTED{/if}>Almaty, Dhaka</option>
<option value='7'{if $setting.setting_timezone == "7"} SELECTED{/if}>Bangkok, Jakarta, Hanoi</option>
<option value='8'{if $setting.setting_timezone == "8"} SELECTED{/if}>Beijing, Hong Kong, Singapore, Taipei</option>
<option value='9'{if $setting.setting_timezone == "9"} SELECTED{/if}>Tokyo, Osaka, Sapporto, Seoul, Yakutsk</option>
<option value='9.3'{if $setting.setting_timezone == "9.3"} SELECTED{/if}>Adelaide, Darwin</option>
<option value='10'{if $setting.setting_timezone == "10"} SELECTED{/if}>Brisbane, Melbourne, Sydney, Guam</option>
<option value='11'{if $setting.setting_timezone == "11"} SELECTED{/if}>Magadan, Soloman Is., New Caledonia</option>
<option value='12'{if $setting.setting_timezone == "12"} SELECTED{/if}>Fiji, Kamchatka, Marshall Is., Wellington</option>
</select>
</td>
</tr>
</table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=208}</td>
</tr>
<tr>
<td class='setting1'>
{lang_print id=209}
</td>
</tr>
<tr>
<td class='setting2'>
<select name='setting_dateformat' class='text'>
<option value='n/j/Y'{if $setting.setting_dateformat == "n/j/Y"} SELECTED{/if}>7/17/2006</option>
<option value='n.j.Y'{if $setting.setting_dateformat == "n.j.Y"} SELECTED{/if}>7.17.2006</option>
<option value='n-j-Y'{if $setting.setting_dateformat == "n-j-Y"} SELECTED{/if}>7-17-2006</option>
<option value='Y/n/j'{if $setting.setting_dateformat == "Y/n/j"} SELECTED{/if}>2006/7/17</option>
<option value='Y-n-j'{if $setting.setting_dateformat == "Y-n-j"} SELECTED{/if}>2006-7-17</option>
<option value='Y-m-d'{if $setting.setting_dateformat == "Y-m-d"} SELECTED{/if}>2006-07-17</option>
<option value='Ynj'{if $setting.setting_dateformat == "Ynj"} SELECTED{/if}>2006717</option>
<option value='j/n/Y'{if $setting.setting_dateformat == "j/n/Y"} SELECTED{/if}>17/7/2006</option>
<option value='j.n.Y'{if $setting.setting_dateformat == "j.n.Y"} SELECTED{/if}>17.7.2006</option>
<option value='M. j, Y'{if $setting.setting_dateformat == "M. j, Y"} SELECTED{/if}>Jul. 17, 2006</option>
<option value='F j, Y'{if $setting.setting_dateformat == "F j, Y"} SELECTED{/if}>July 17, 2006</option>
<option value='j F Y'{if $setting.setting_dateformat == "j F Y"} SELECTED{/if}>17 July 2006</option>
<option value='l, F j, Y'{if $setting.setting_dateformat == "l, F j, Y"} SELECTED{/if}>Monday, July 17, 2006</option>
<option value='D-j-M-Y'{if $setting.setting_dateformat == "D-j-M-Y"} SELECTED{/if}>Mon-17-Jul-2006</option>
<option value='D j M Y'{if $setting.setting_dateformat == "D j M Y"} SELECTED{/if}>Mon 17 Jul 2006</option>
<option value='D j F Y'{if $setting.setting_dateformat == "D j F Y"} SELECTED{/if}>Mon 17 July 2006</option>
<option value='l j F Y'{if $setting.setting_dateformat == "l j F Y"} SELECTED{/if}>Monday 17 July 2006</option>
<option value='Y-M-j'{if $setting.setting_dateformat == "Y-M-j"} SELECTED{/if}>2006-Jul-17</option>
<option value='j-M-Y'{if $setting.setting_dateformat == "j-M-Y"} SELECTED{/if}>17-Jul-2006</option>
</select>
<select name='setting_timeformat' class='text'>
<option value='g:i A'{if $setting.setting_timeformat == "g:i A"} SELECTED{/if}>9:30 PM</option>
<option value='h:i A'{if $setting.setting_timeformat == "h:i A"} SELECTED{/if}>09:30 PM</option>
<option value='g:i'{if $setting.setting_timeformat == "g:i"} SELECTED{/if}>9:30</option>
<option value='h:i'{if $setting.setting_timeformat == "h:i"} SELECTED{/if}>09:30</option>
<option value='H:i'{if $setting.setting_timeformat == "H:i"} SELECTED{/if}>21:30</option>
<option value='H\hi'{if $setting.setting_timeformat == "H\hi"} SELECTED{/if}>21h30</option>
</select>
</td>
</tr>
</table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=1148}</td>
</tr>
<tr>
<td class='setting1'>
{lang_print id=1149}
</td>
</tr>
<tr>
<td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr><td><input type='radio' name='setting_username' id='username_1' value='1'{if $setting.setting_username == 1} CHECKED{/if}>&nbsp;</td><td><label for='username_1'>{lang_print id=1150}</label></td></tr>
  <tr><td><input type='radio' name='setting_username' id='username_0' value='0'{if $setting.setting_username == 0} CHECKED{/if}>&nbsp;</td><td><label for='username_0'>{lang_print id=1151}</label></td></tr>
  </table>
</td>
</tr>
</table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=892}</td>
</tr>
<tr>
<td class='setting1'>
{lang_print id=893}
</td>
</tr>
<tr>
<td class='setting2'>
<input type='text' class='text' name='setting_comment_html' value='{$setting.setting_comment_html}' maxlength='250' size='60'>
</td>
</tr>
</table>

<br>

<input type='submit' class='button' value='{lang_print id=173}'>
<input type='hidden' name='task' value='dosave'>
</form>


{include file='admin_footer.tpl'}