{include file='admin_header.tpl'}

{* $Id: admin_viewusers.tpl 8 2009-01-11 06:02:53Z john $ *}

<h2>{lang_print id=4}</h2>
{lang_print id=996}
<br />
<br />

<table cellpadding='0' cellspacing='0' align='center'>
<tr>
<td align='center'>
<div class='box'>
<table cellpadding='0' cellspacing='0' align='center'>
<tr><form action='admin_viewusers.php' method='POST'>
<td>{if $setting.setting_username}{lang_print id=28}{else}{lang_print id=1152}{/if}<br><input type='text' class='text' name='f_user' value='{$f_user}' size='15' maxlength='50'>&nbsp;</td>
<td>{lang_print id=89}<br><input type='text' class='text' name='f_email' value='{$f_email}' size='15' maxlength='70'>&nbsp;</td>
<td>{lang_print id=997}<br><select class='text' name='f_level'><option></option>{section name=level_loop loop=$levels}<option value='{$levels[level_loop].level_id}'{if $f_level == $levels[level_loop].level_id} SELECTED{/if}>{$levels[level_loop].level_name}</option>{/section}</select>&nbsp;</td>
<td>{lang_print id=998}<br><select class='text' name='f_subnet'><option></option>{section name=subnet_loop loop=$subnets}<option value='{$subnets[subnet_loop].subnet_id}'{if $f_subnet === $subnets[subnet_loop].subnet_id} SELECTED{/if}>{lang_print id=$subnets[subnet_loop].subnet_name}</option>{/section}</select>&nbsp;</td>
<td>{lang_print id=999}<br><select class='text' name='f_enabled'><option></option><option value='1'{if $f_enabled == "1"} SELECTED{/if}>{lang_print id=1000}</option><option value='0'{if $f_enabled == "0"} SELECTED{/if}>{lang_print id=1001}</option></select>&nbsp;&nbsp;&nbsp;</td>
<td valign='bottom'><input type='submit' class='button' value='{lang_print id=1002}'></td>
<input type='hidden' name='s' value='{$s}'>
</form>
</tr>
</table>
</div>
</td></tr></table>
  
<br>

{if $total_users == 0}

  <table cellpadding='0' cellspacing='0' width='400' align='center'>
  <tr>
  <td align='center'>
  <div class='box'><b>{lang_print id=1003}</b></div>
  </td></tr></table>
  <br>

{else}

  {* JAVASCRIPT FOR CHECK ALL *}
  {literal}
  <script language='JavaScript'> 
  <!---
  var checkboxcount = 1;
  function doCheckAll() {
    if(checkboxcount == 0) {
      with (document.items) {
      for (var i=0; i < elements.length; i++) {
      if (elements[i].type == 'checkbox') {
      elements[i].checked = false;
      }}
      checkboxcount = checkboxcount + 1;
      }
    } else
      with (document.items) {
      for (var i=0; i < elements.length; i++) {
      if (elements[i].type == 'checkbox') {
      elements[i].checked = true;
      }}
      checkboxcount = checkboxcount - 1;
      }
  }

  var user_id = 0;
  function confirmDelete(id) {
    user_id = id;
    TB_show('{/literal}{lang_print id=1013}{literal}', '#TB_inline?height=150&width=300&inlineId=confirmdelete', '', '../images/trans.gif');
  }

  function deleteUser() {
    window.location = 'admin_viewusers.php?task=delete&user_id='+user_id;
  }
  // -->
  </script>
  {/literal}

  {* HIDDEN DIV TO DISPLAY CONFIRMATION MESSAGE *}
  <div style='display: none;' id='confirmdelete'>
    <div style='margin-top: 10px;'>
      {lang_print id=1012}
    </div>
    <br>
    <input type='button' class='button' value='{lang_print id=175}' onClick='parent.TB_remove();parent.deleteUser();'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
  </div>

  <div class='pages'>{lang_sprintf id=1004 1=$total_users} &nbsp;|&nbsp; {lang_print id=1005} {section name=page_loop loop=$pages}{if $pages[page_loop].link == '1'}{$pages[page_loop].page}{else}<a href='admin_viewusers.php?s={$s}&p={$pages[page_loop].page}&f_user={$f_user}&f_email={$f_email}&f_level={$f_level}&f_enabled={$f_enabled}'>{$pages[page_loop].page}</a>{/if} {/section}</div>
  
  <form action='admin_viewusers.php' method='post' name='items'>
  <table cellpadding='0' cellspacing='0' class='list' width='100%'>
  <tr>
  <td class='header' width='10'><input type='checkbox' name='select_all' onClick='javascript:doCheckAll()'></td>
  <td class='header' width='10' style='padding-left: 0px;'><a class='header' href='admin_viewusers.php?s={$i}&p={$p}&f_user={$f_user}&f_email={$f_email}&f_level={$f_level}&f_subnet={$f_subnet}&f_enabled={$f_enabled}'>{lang_print id=87}</a></td>
  <td class='header'>{if $setting.setting_username}<a class='header' href='admin_viewusers.php?s={$u}&p={$p}&f_user={$f_user}&f_email={$f_email}&f_level={$f_level}&f_subnet={$f_subnet}&f_enabled={$f_enabled}'>{lang_print id=28}</a>{else}{lang_print id=1152}{/if}</td>
  <td class='header'><a class='header' href='admin_viewusers.php?s={$em}&p={$p}&f_user={$f_user}&f_email={$f_email}&f_level={$f_level}&f_subnet={$f_subnet}&f_enabled={$f_enabled}'>{lang_print id=89}</a>{if $setting.setting_signup_verify != 0} (<a class='header' href='admin_viewusers.php?s={$v}&p={$p}&f_user={$f_user}&f_email={$f_email}&f_level={$f_level}&f_enabled={$f_enabled}'>{lang_print id=1006}</a>){/if}</td>
  <td class='header'>{lang_print id=997}</td>
  <td class='header'>{lang_print id=998}</td>
  <td class='header' align='center'>{lang_print id=999}</td>
  <td class='header'><a class='header' href='admin_viewusers.php?s={$sd}&p={$p}&f_user={$f_user}&f_email={$f_email}&f_level={$f_level}&f_subnet={$f_subnet}&f_enabled={$f_enabled}'>{lang_print id=1007}</a></td>
  <td class='header'>{lang_print id=153}</td>
  </tr>
  
  <!-- LOOP THROUGH USERS -->
  {section name=user_loop loop=$users}
    <tr class='{cycle values="background1,background2"}'>
    <td class='item' style='padding-right: 0px;'><input type='checkbox' name='delete[]' value='{$users[user_loop].user_id}'></td>
    <td class='item' style='padding-left: 0px;'>{$users[user_loop].user_id}</td>
    <td class='item'><a href='{$url->url_create('profile', $users[user_loop].user_username)}'>{if $setting.setting_username}{$users[user_loop].user_username|truncate:25:"...":true}{else}{$users[user_loop].user_displayname|truncate:25:"...":true}{/if}</a></td>
    <td class='item'><a href='mailto:{$users[user_loop].user_email}'>{$users[user_loop].user_email|truncate:25:"...":true}</a>{if $setting.setting_signup_verify != 0 && $users[user_loop].user_verified == 0} ({lang_print id=1008}){/if}</td>
    <td class='item'><a href='admin_levels_edit.php?level_id={$users[user_loop].user_level_id}'>{$users[user_loop].user_level}</a></td>
    <td class='item'><a href='admin_subnetworks.php'>{lang_print id=$users[user_loop].user_subnet}</a></td>
    <td class='item' align='center'>{if $users[user_loop].user_enabled == 1}{lang_print id=1000}{else}{lang_print id=1001}{/if}</td>
    <td class='item' nowrap='nowrap'>{$datetime->cdate($setting.setting_dateformat, $datetime->timezone($users[user_loop].user_signupdate, $setting.setting_timezone))}</td>
    <td class='item' nowrap='nowrap'><a href='admin_viewusers_edit.php?user_id={$users[user_loop].user_id}&s={$s}&p={$p}&f_user={$f_user}&f_email={$f_email}&f_level={$f_level}&f_subnet={$f_subnet}&f_enabled={$f_enabled}'>{lang_print id=187}</a> | <a href="javascript: confirmDelete('{$users[user_loop].user_id}');">{lang_print id=155}</a> | <a href='admin_loginasuser.php?user_id={$users[user_loop].user_id}' target='_blank'>{lang_print id=1009}</a></td>
    </tr>
  {/section}
  </table>

  <table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
  <td>
    <br>
    <input type='submit' class='button' value='{lang_print id=788}'>
    <input type='hidden' name='task' value='dodelete'>
    </form>
  </td>
  <td align='right' valign='top'>
    <div class='pages2'>{lang_sprintf id=1004 1=$total_users} &nbsp;|&nbsp; {lang_print id=1005} {section name=page_loop loop=$pages}{if $pages[page_loop].link == '1'}{$pages[page_loop].page}{else}<a href='admin_viewusers.php?s={$s}&p={$pages[page_loop].page}&f_user={$f_user}&f_email={$f_email}&f_level={$f_level}&f_enabled={$f_enabled}'>{$pages[page_loop].page}</a>{/if} {/section}</div>
  </td>
  </tr>
  </table>

{/if}

{include file='admin_footer.tpl'}