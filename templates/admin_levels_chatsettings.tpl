{include file='admin_header.tpl'}

{* $Id: admin_levels_chatsettings.tpl 16 2009-01-13 04:01:31Z john $ *}

<h2>{lang_sprintf id=288 1=$level_info.level_name}</h2>
{lang_print id=282}

<table cellspacing='0' cellpadding='0' width='100%' style='margin-top: 20px;'>
<tr>
<td class='vert_tab0'>&nbsp;</td>
<td valign='top' class='pagecell' rowspan='{math equation="x+5" x=$level_menu|@count}'>

  <h2>{lang_print id=3501021}</h2>
  {lang_print id=3501022}

  <br><br>

  {* SHOW SUCCESS MESSAGE *}
  {if $result != 0}
    <div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=191}</div>
  {/if}

  {* SHOW ERROR MESSAGE *}
  {if $is_error != 0}
    <div class='error'><img src='../images/error.gif' class='icon' border='0'> {$error_message}</div>
  {/if}
  
  <form action='admin_levels_chatsettings.php' method='POST'>


  
  <table cellpadding='0' cellspacing='0' width='600'><td class='header'>
    {lang_print id=3501023}
  </td></tr><td class='setting1'>
    {lang_print id=3501024}
  </td></tr>
  <tr><td class='setting2'>
    {* LEVEL_CHAT_ALLOW *}
    <table cellpadding='2' cellspacing='0'><tr><td>
      <input type='radio' name='level_chat_allow' id='level_chat_allow_1' value='1'{if  $level_info.level_chat_allow} CHECKED{/if} />
    </td><td>
      <label for='setting_chat_enabled_1'>{lang_print id=3501025}</label>
    </td></tr>
    <tr><td>
      <input type='radio' name='level_chat_allow' id='level_chat_allow_0' value='0'{if !$level_info.level_chat_allow} CHECKED{/if} />
    </td><td>
      <label for='setting_chat_enabled_0'>{lang_print id=3501026}</label></td>
    </tr></table>
  </td></tr><td class='setting1'>
    {lang_print id=3501027}
  </td></tr>
  <tr><td class='setting2'>
    {* LEVEL_IM_ALLOW *}
    <table cellpadding='2' cellspacing='0'><tr><td>
      <input type='radio' name='level_im_allow' id='level_im_allow_1' value='1'{if  $level_info.level_im_allow} CHECKED{/if} />
    </td><td>
      <label for='setting_im_enabled_1'>{lang_print id=3501028}</label>
    </td></tr>
    <tr><td>
      <input type='radio' name='level_im_allow' id='level_im_allow_0' value='0'{if !$level_info.level_im_allow} CHECKED{/if} />
    </td><td>
      <label for='setting_im_enabled_0'>{lang_print id=3501029}</label></td>
    </tr></table>
  </td></tr></table>
  <br />

  <input type='submit' class='button' value='{lang_print id=173}'>
  <input type='hidden' name='task' value='dosave'>
  <input type='hidden' name='level_id' value='{$level_id}'>
  </form>

</td>
</tr>

{* DISPLAY MENU *}
<tr><td width='100' nowrap='nowrap' class='vert_tab'><div style='width: 100px;'><a href='admin_levels_edit.php?level_id={$level_info.level_id}'>{lang_print id=285}</a></div></td></tr>
<tr><td width='100' nowrap='nowrap' class='vert_tab' style='border-right: none; border-top: none;'><div style='width: 100px;'><a href='admin_levels_usersettings.php?level_id={$level_info.level_id}'>{lang_print id=286}</a></div></td></tr>
<tr><td width='100' nowrap='nowrap' class='vert_tab' style='border-top: none;'><div style='width: 100px;'><a href='admin_levels_messagesettings.php?level_id={$level_info.level_id}'>{lang_print id=287}</a></div></td></tr>
{foreach from=$global_plugins key=plugin_k item=plugin_v}
{section name=level_page_loop loop=$plugin_v.plugin_pages_level}
  <tr><td width='100' nowrap='nowrap' class='vert_tab' style='border-top: none;{if $plugin_v.plugin_pages_level[level_page_loop].page == $page} border-right: none;{/if}'><div style='width: 100px;'><a href='{$plugin_v.plugin_pages_level[level_page_loop].link}?level_id={$level_info.level_id}'>{lang_print id=$plugin_v.plugin_pages_level[level_page_loop].title}</a></div></td></tr>
{/section}
{/foreach}

<tr>
<td class='vert_tab0'>
  <div style='height: 1800px;'>&nbsp;</div>
</td>
</tr>
</table>

{include file='admin_footer.tpl'}