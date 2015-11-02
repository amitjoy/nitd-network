{include file='admin_header.tpl'}

{* $Id: admin_home.tpl 75 2009-02-28 00:51:03Z john $ *}

<h2>{lang_print id=55}</h2>
<div>{lang_print id=56}</div>
<br />

{* SHOW WARNINGS *}
{if !empty($admin_notifications)}
<a class="admin_notification_activator admin_notification_hover" id="admin_notification_activator" href="javascript:void(0);" onclick="$(this).removeClass('admin_notification_hover'); $('admin_notification_container').style.display=''; $(this).blur(); return false;">
  <table width='100%' cellpadding='0' cellspacing='0'>
    <tr>
      <td id="admin_notification_message">
        {lang_print id=1318} <span id="admin_notification_expand">{lang_print id=1319}</span>
      </td>
    </tr>
    <tr style="display:none;" id="admin_notification_container">
      <td class="error">
        {foreach from=$admin_notifications key=admin_notifications_index item=admin_notifications_item}
          -{if is_numeric($admin_notifications_item)}{lang_print id=$admin_notifications_item}{else}{$admin_notifications_item}{/if}<br />
        {/foreach}
        </div>
      </td>
    </tr>
  </table>
</a>
{/if}


{* SHOW LICENSE INFO *}
<table width='100%' cellpadding='0' cellspacing='0' class='stats'>
<tr>
<td class='stat0'>
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td><b>{lang_print id=58}</b> {$setting.setting_key}</td>
  <td style='padding-left: 20px;'><b>{lang_print id=59}</b> {$version_num}<br></td>
  {if $version_num < $versions.license}
    <td style='padding-left: 20px;'><a href='http://www.socialengine.net/login.php' target='_blank'><img src='../images/icons/admin_upgrade16.gif' border='0' class='icon2'>{lang_print id=60}</a><br></td>
  {/if}
  </tr>
  </table>
</td>
</tr>
</table>

{* SHOW QUICK STATS *}
<table width='100%' cellpadding='0' cellspacing='0' class='stats' style='margin-top: 10px;'>
<tr>
<td class='stat0'>
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td><b>{lang_print id=61}</b> {$total_users_num}</td>
  <td style='padding-left: 60px;' align='right'><b>{lang_print id=62}</b> {$total_comments_num}</td>
  <td style='padding-left: 60px;' align='right'><b>{lang_print id=63}</b> {$total_messages_num}</td>
  <td style='padding-left: 60px;' align='right'><b>{lang_print id=64}</b> {$total_user_levels}</td>
  </tr>
  <tr>
  <td><b>{lang_print id=65}</b> {$total_subnetworks}</td>
  <td style='padding-left: 60px;' align='right'><b>{lang_print id=66}</b> {$total_reports}</td>
  <td style='padding-left: 60px;' align='right'><b>{lang_print id=67}</b> {$total_friendships}</td>
  <td style='padding-left: 60px;' align='right'><b>{lang_print id=68}</b> {$total_announcements}</td>
  </tr>
  <tr>
  <td><b>{lang_print id=69}</b> {$views_today}</td>
  <td style='padding-left: 60px;' align='right'><b>{lang_print id=70}</b> {$signups_today}</td>
  <td style='padding-left: 60px;' align='right'><b>{lang_print id=71}</b> {$logins_today}</td>
  <td style='padding-left: 60px;' align='right'><b>{lang_print id=72}</b> {$total_admins}</td>
  </tr>
  </table>

  {* SHOW ONLINE USERS IF MORE THAN ZERO *}
  {math assign='total_online_users' equation="x+y" x=$online_users[0]|@count y=$online_users[1]}
  {if $total_online_users > 0}
    <br><b>{$total_online_users} {lang_print id=73}</b> 
    {if $online_users[0]|@count == 0}
      {lang_sprintf id=977 1=$online_users[1]}
    {else}
      {capture assign='online_users_registered'}{section name=online_loop loop=$online_users[0]}{if $smarty.section.online_loop.rownum != 1}, {/if}<a href='{$url->url_create("profile", $online_users[0][online_loop]->user_info.user_username)}' target='_blank'>{$online_users[0][online_loop]->user_displayname}</a>{/section}{/capture}
      {lang_sprintf id=976 1=$online_users_registered 2=$online_users[1]}
    {/if}
  {/if}

</td>
</tr>
</table>

<br>

{* FORM FOR TAKING SITE ON AND OFF LINE *}
<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=984}</td>
  </tr>
  <tr>
    <td class='setting1'>
      {lang_print id=985}
      {if $task == "site_status"}
        <br />
        <br />
        <div>
          <img src='../images/icons/bulb16.gif' border='0' class='icon'>
          <b>{if $setting.setting_online == 1}{lang_print id=1010}{else}{lang_print id=1011}{/if}</b>
        </div>
      {/if}
    </td>
  </tr>
  <tr>
    <td class='setting2'>
      <form action='admin_home.php' method='post' id='setting_online_form'>
      <table cellpadding='0' cellspacing='0'>
      <tr>
      <td><input type='radio' onChange="$('setting_online_form').submit()" name='setting_online' id='setting_online_1' value='1'{if $setting.setting_online == 1} checked='checked'{/if}></td>
      <td><label for='setting_online_1'>{lang_print id=986}</label></td>
      </tr>
      <tr>
      <td><input type='radio' onChange="$('setting_online_form').submit()" name='setting_online' id='setting_online_0' value='0'{if $setting.setting_online == 0} checked='checked'{/if}></td>
      <td><label for='setting_online_0'>{lang_print id=987}</label></td>
      </tr>
      </table>
      <input type='hidden' name='task' value='site_status'>
    </form>
    </td>
  </tr>
</table>
<br />


{* ADMIN LANGUAGE SELECTION *}
{if $lang_packlist|@count != 0}
<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=1312}</td>
  </tr>
  <tr>
    <td class='setting1'>{lang_print id=1313}</td>
  </tr>
  <tr>
    <td class='setting2'>
      {if $smarty.server.QUERY_STRING|strpos:"&lang_id=" !== FALSE}{assign var="pos" value=$smarty.server.QUERY_STRING|strstr:"&lang_id="}{assign var="query_string" value=$smarty.server.QUERY_STRING|replace:$pos:""}{else}{assign var="query_string" value=$smarty.server.QUERY_STRING}{/if}
      <select style='width: 168px;' class='small' name='admin_language_id' onchange="window.location.href='{$smarty.server.PHP_SELF}?{$query_string}&lang_id='+this.options[this.selectedIndex].value;">
        {section name=lang_loop loop=$lang_packlist}
          <option value='{$lang_packlist[lang_loop].language_id}'{if $lang_packlist[lang_loop].language_id == $global_language} selected='selected'{/if}>{$lang_packlist[lang_loop].language_name}</option>
         {/section}
      </select>
    </td>
  </tr>
</table>
<br />
{/if}



{lang_print id=74}
<br />

<table cellpadding='0' cellspacing='0' style='margin-top: 5px;'>
  <tr>
    <td class='step'>1</td>
    <td><b><a href='admin_profile.php'>{lang_print id=75}</a></b><br>{lang_print id=76}</td>
  </tr>
  <tr>
    <td class='step'>2</td>
    <td><b><a href='admin_signup.php'>{lang_print id=77}</a></b><br>{lang_print id=78}</td>
  </tr>
  <tr>
    <td class='step'>3</td>
    <td><b><a href='admin_viewplugins.php'>{lang_print id=81}</a></b><br>{lang_print id=82}</td>
  </tr>
  <tr>
    <td class='step'>4</td>
    <td><b><a href='admin_levels.php'>{lang_print id=79}</a></b><br>{lang_print id=80}</td>
  </tr>
  <tr>
    <td class='step'>5</td>
    <td><b><a href='admin_templates.php'>{lang_print id=83}</a></b><br>{lang_print id=84}</td>
  </tr>
</table>

{include file='admin_footer.tpl'}