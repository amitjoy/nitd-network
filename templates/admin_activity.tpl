{include file='admin_header.tpl'}

{* $Id: admin_activity.tpl 59 2009-02-13 03:25:54Z john $ *}

<h2>{lang_print id=14}</h2>
{lang_print id=547}

<br><br>

{if $result != 0}
  <div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=191}</div>
{/if}

<form action='admin_activity.php' method='post'>
<table cellpadding='0' cellspacing='0' width='650'>
  <tr>
    <td class='header'>{lang_print id=548}</td>
  </tr>
  <tr>
    <td class='setting1'>{lang_print id=549}</td>
  </tr>
  <tr>
    <td class='setting2'>
      <table cellpadding='3' cellspacing='0' width='100%'>
      {section name=actiontype_loop loop=$actiontypes}
        <tr>
        <td valign='top'>
          <b>{lang_print id=550}</b> - {lang_print id=578} {$actiontypes[actiontype_loop].actiontype_vars}<br />
          <textarea name='actiontype_text[{$actiontypes[actiontype_loop].actiontype_id}]' rows='3' style='width: 100%;' class='text'>{lang_sprintf id=$actiontypes[actiontype_loop].actiontype_text args=$actiontypes[actiontype_loop].actiontype_vars_array}</textarea>
          {if $actiontypes[actiontype_loop].actiontype_media == 1}<br />{lang_print id=1027}{/if}
        </td>
        <td valign='top' width='1' style='padding-left: 7px;'>
          <b>{lang_print id=551}</b><br />
          {$actiontypes[actiontype_loop].actiontype_name}
        </td>
        </tr>
        <tr>
        <td colspan='2'{if $smarty.section.actiontype_loop.last != true} style='padding-bottom: 50px;'{/if}>
          <input name='actiontype_enabled[{$actiontypes[actiontype_loop].actiontype_id}]' id='actiontype_enabled{$actiontypes[actiontype_loop].actiontype_id}' type='checkbox' value='1' {if $actiontypes[actiontype_loop].actiontype_enabled == 1} checked='checked'{/if}> <label for='actiontype_enabled{$actiontypes[actiontype_loop].actiontype_id}'>{lang_print id=576}</label><br>
          <input name='actiontype_setting[{$actiontypes[actiontype_loop].actiontype_id}]' id='actiontype_setting{$actiontypes[actiontype_loop].actiontype_id}' type='checkbox' value='1' {if $actiontypes[actiontype_loop].actiontype_setting == 1} checked='checked'{/if}> <label for='actiontype_setting{$actiontypes[actiontype_loop].actiontype_id}'>{lang_print id=577}</label><br>
        </td>
        </tr>
      {/section}
      </table>
    </td>
  </tr>
</table>
<br />


<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=552}</td>
</tr>
<td class='setting1'>
  {lang_print id=553}
</td></tr><tr><td class='setting2'>
  <select class='text' name='setting_actions_actionsonprofile'>
  <option{if $setting.setting_actions_actionsonprofile == "0"} selected='selected'{/if}>0</option>
  <option{if $setting.setting_actions_actionsonprofile == "1"} selected='selected'{/if}>1</option>
  <option{if $setting.setting_actions_actionsonprofile == "2"} selected='selected'{/if}>2</option>
  <option{if $setting.setting_actions_actionsonprofile == "3"} selected='selected'{/if}>3</option>
  <option{if $setting.setting_actions_actionsonprofile == "4"} selected='selected'{/if}>4</option>
  <option{if $setting.setting_actions_actionsonprofile == "5"} selected='selected'{/if}>5</option>
  <option{if $setting.setting_actions_actionsonprofile == "6"} selected='selected'{/if}>6</option>
  <option{if $setting.setting_actions_actionsonprofile == "7"} selected='selected'{/if}>7</option>
  <option{if $setting.setting_actions_actionsonprofile == "8"} selected='selected'{/if}>8</option>
  <option{if $setting.setting_actions_actionsonprofile == "9"} selected='selected'{/if}>9</option>
  <option{if $setting.setting_actions_actionsonprofile == "10"} selected='selected'{/if}>10</option>
  </select> <b>{lang_print id=554}</b>
</td></tr></table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=555}</td>
</tr>
<tr>
<td class='setting1'>
  {lang_print id=556}
</td></tr><tr><td class='setting2'>
  <select class='text' name='setting_actions_actionsinlist'>
  <option{if $setting.setting_actions_actionsinlist == "0"} selected='selected'{/if}>0</option>
  <option{if $setting.setting_actions_actionsinlist == "1"} selected='selected'{/if}>1</option>
  <option{if $setting.setting_actions_actionsinlist == "2"} selected='selected'{/if}>2</option>
  <option{if $setting.setting_actions_actionsinlist == "3"} selected='selected'{/if}>3</option>
  <option{if $setting.setting_actions_actionsinlist == "4"} selected='selected'{/if}>4</option>
  <option{if $setting.setting_actions_actionsinlist == "5"} selected='selected'{/if}>5</option>
  <option{if $setting.setting_actions_actionsinlist == "6"} selected='selected'{/if}>6</option>
  <option{if $setting.setting_actions_actionsinlist == "7"} selected='selected'{/if}>7</option>
  <option{if $setting.setting_actions_actionsinlist == "8"} selected='selected'{/if}>8</option>
  <option{if $setting.setting_actions_actionsinlist == "9"} selected='selected'{/if}>9</option>
  <option{if $setting.setting_actions_actionsinlist == "10"} selected='selected'{/if}>10</option>
  <option{if $setting.setting_actions_actionsinlist == "15"} selected='selected'{/if}>15</option>
  <option{if $setting.setting_actions_actionsinlist == "20"} selected='selected'{/if}>20</option>
  <option{if $setting.setting_actions_actionsinlist == "25"} selected='selected'{/if}>25</option>
  <option{if $setting.setting_actions_actionsinlist == "30"} selected='selected'{/if}>30</option>
  <option{if $setting.setting_actions_actionsinlist == "35"} selected='selected'{/if}>35</option>
  <option{if $setting.setting_actions_actionsinlist == "40"} selected='selected'{/if}>40</option>
  <option{if $setting.setting_actions_actionsinlist == "45"} selected='selected'{/if}>45</option>
  <option{if $setting.setting_actions_actionsinlist == "50"} selected='selected'{/if}>50</option>
  </select> <b>{lang_print id=557}</b>
</td></tr>
<tr>
<td class='setting1'>
  {lang_print id=558}
</td></tr><tr><td class='setting2'>
  <select class='text' name='setting_actions_showlength'>
  <option value='60'{if $setting.setting_actions_showlength == "60"} selected='selected'{/if}>1 {lang_print id=559}</option>
  <option value='300'{if $setting.setting_actions_showlength == "300"} selected='selected'{/if}>5 {lang_print id=559}</option>
  <option value='600'{if $setting.setting_actions_showlength == "600"} selected='selected'{/if}>10 {lang_print id=559}</option>
  <option value='1200'{if $setting.setting_actions_showlength == "1200"} selected='selected'{/if}>20 {lang_print id=559}</option>
  <option value='1800'{if $setting.setting_actions_showlength == "1800"} selected='selected'{/if}>30 {lang_print id=559}</option>
  <option value='3600'{if $setting.setting_actions_showlength == "3600"} selected='selected'{/if}>1 {lang_print id=560}</option>
  <option value='10800'{if $setting.setting_actions_showlength == "10800"} selected='selected'{/if}>3 {lang_print id=560}</option>
  <option value='21600'{if $setting.setting_actions_showlength == "21600"} selected='selected'{/if}>6 {lang_print id=560}</option>
  <option value='43200'{if $setting.setting_actions_showlength == "43200"} selected='selected'{/if}>12 {lang_print id=560}</option>
  <option value='86400'{if $setting.setting_actions_showlength == "86400"} selected='selected'{/if}>1 {lang_print id=561}</option>
  <option value='172800'{if $setting.setting_actions_showlength == "172800"} selected='selected'{/if}>2 {lang_print id=561}</option>
  <option value='259200'{if $setting.setting_actions_showlength == "259200"} selected='selected'{/if}>3 {lang_print id=561}</option>
  <option value='604800'{if $setting.setting_actions_showlength == "604800"} selected='selected'{/if}>1 {lang_print id=562}</option>
  <option value='1209600'{if $setting.setting_actions_showlength == "1209600"} selected='selected'{/if}>2 {lang_print id=562}</option>
  <option value='2629743'{if $setting.setting_actions_showlength == "2629743"} selected='selected'{/if}>1 {lang_print id=563}</option>
  </select>
</td></tr>
<tr>
<td class='setting1'>
  {lang_print id=564}
</td></tr><tr><td class='setting2'>
  <select class='text' name='setting_actions_actionsperuser'>
  <option{if $setting.setting_actions_actionsperuser == "0"} selected='selected'{/if}>0</option>
  <option{if $setting.setting_actions_actionsperuser == "1"} selected='selected'{/if}>1</option>
  <option{if $setting.setting_actions_actionsperuser == "2"} selected='selected'{/if}>2</option>
  <option{if $setting.setting_actions_actionsperuser == "3"} selected='selected'{/if}>3</option>
  <option{if $setting.setting_actions_actionsperuser == "4"} selected='selected'{/if}>4</option>
  <option{if $setting.setting_actions_actionsperuser == "5"} selected='selected'{/if}>5</option>
  <option{if $setting.setting_actions_actionsperuser == "6"} selected='selected'{/if}>6</option>
  <option{if $setting.setting_actions_actionsperuser == "7"} selected='selected'{/if}>7</option>
  <option{if $setting.setting_actions_actionsperuser == "8"} selected='selected'{/if}>8</option>
  <option{if $setting.setting_actions_actionsperuser == "9"} selected='selected'{/if}>9</option>
  <option{if $setting.setting_actions_actionsperuser == "10"} selected='selected'{/if}>10</option>
  </select> <b>{lang_print id=565}</b>
</td></tr></table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=566}</td>
</tr>
<td class='setting1'>
  {lang_print id=567}
</td></tr><tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr><td><input type='radio' name='setting_actions_selfdelete' id='actions_selfdelete_1' value='1'{if $setting.setting_actions_selfdelete == 1} CHECKED{/if}>&nbsp;</td><td><label for='actions_selfdelete_1'>{lang_print id=568}</label></td></tr>
  <tr><td><input type='radio' name='setting_actions_selfdelete' id='actions_selfdelete_0' value='0'{if $setting.setting_actions_selfdelete == 0} CHECKED{/if}>&nbsp;</td><td><label for='actions_selfdelete_0'>{lang_print id=569}</label></td></tr>
  </table>
</td></tr></table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=570}</td>
</tr>
<td class='setting1'>
  {lang_print id=571}
</td></tr><tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr><td style='padding-bottom: 3px;'><input type='radio' name='setting_actions_visibility' id='actions_visibility1' value='1'{if $setting.setting_actions_visibility == 1} checked='checked'{/if}></td><td><label for='actions_visibility1'>{lang_print id=324}</label>&nbsp;&nbsp;</td></tr>
  <tr><td style='padding-bottom: 3px;'><input type='radio' name='setting_actions_visibility' id='actions_visibility2' value='2'{if $setting.setting_actions_visibility == 2} checked='checked'{/if}></td><td><label for='actions_visibility2'>{lang_print id=325}</label>&nbsp;&nbsp;</td></tr>
  <tr><td style='padding-bottom: 3px;'><input type='radio' name='setting_actions_visibility' id='actions_visibility4' value='4'{if $setting.setting_actions_visibility == 4} checked='checked'{/if}></td><td><label for='actions_visibility4'>{lang_print id=327}</label>&nbsp;&nbsp;</td></tr>
  </table>
</td></tr>
<td class='setting1'>
  {lang_print id=1065}
</td></tr><tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr><td style='padding-bottom: 3px;'><input type='radio' name='setting_actions_preference' id='actions_preference1' value='1'{if $setting.setting_actions_preference == 1} checked='checked'{/if}></td><td><label for='actions_preference1'>{lang_print id=1066}</label>&nbsp;&nbsp;</td></tr>
  <tr><td style='padding-bottom: 3px;'><input type='radio' name='setting_actions_preference' id='actions_preference0' value='0'{if $setting.setting_actions_preference == 0} checked='checked'{/if}></td><td><label for='actions_preference0'>{lang_print id=1067}</label>&nbsp;&nbsp;</td></tr>
  </table>
</td></tr>
</table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr><td class='header'>{lang_print id=572}</td></tr>
<td class='setting1'>
  {lang_print id=573}
</td></tr><tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr><td><input type='radio' name='setting_actions_privacy' id='actions_privacy_1' value='1'{if $setting.setting_actions_privacy == 1} CHECKED{/if}>&nbsp;</td><td><label for='actions_privacy_1'>{lang_print id=574}</label></td></tr>
  <tr><td><input type='radio' name='setting_actions_privacy' id='actions_privacy_0' value='0'{if $setting.setting_actions_privacy == 0} CHECKED{/if}>&nbsp;</td><td><label for='actions_privacy_0'>{lang_print id=575}</label></td></tr>
  </table>
</td></tr>
</table>

<br>

<input type='hidden' name='task' value='dosave'>
<input type='submit' class='button' value='{lang_print id=173}'>
</form>

{include file='admin_footer.tpl'}