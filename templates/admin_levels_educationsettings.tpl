{include file='admin_header.tpl'}

<h2>{lang_print id=11040309} {$level_name}</h2>
{lang_print id=11040310}

<table cellspacing='0' cellpadding='0' width='100%' style='margin-top: 20px;'>
<tr>
<td class='vert_tab0'>&nbsp;</td>
<td valign='top' class='pagecell' rowspan='{math equation='x+5' x=$level_menu|@count}'>


  <h2>{lang_print id=11040301}</h2>
  {lang_print id=11040302}

  <br><br>

  {if $result != 0}
    <div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=11040308}</div>
  {/if}

  {if $is_error != 0}
    <div class='error'><img src='../images/error.gif' class='icon' border='0'> {$error_message}</div>
  {/if}

  <form action='admin_levels_educationsettings.php' method='POST'>
  <table cellpadding='0' cellspacing='0' width='600'>
  <tr><td class='header'>{lang_print id=11040303}</td></tr>
  <tr><td class='setting1'>
  {lang_print id=11040304}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='radio' name='level_education_allow' id='education_allow_1' value='1'{if $education_allow == 1} CHECKED{/if}>&nbsp;</td><td><label for='education_allow_1'>{lang_print id=11040305}</label></td></tr>
    <tr><td><input type='radio' name='level_education_allow' id='education_allow_0' value='0'{if $education_allow == 0} CHECKED{/if}>&nbsp;</td><td><label for='education_allow_0'>{lang_print id=11040306}</label></td></tr>
    </table>
  </td></tr></table>

  <br>
  
  <input type='submit' class='button' value='{lang_print id=11040307}'>
  <input type='hidden' name='task' value='dosave'>
  <input type='hidden' name='level_id' value='{$level_id}'>
  </form>


</td>
</tr>

{* DISPLAY MENU *}
<tr><td width='100' nowrap='nowrap' class='vert_tab'><div style='width: 100px;'><a href='admin_levels_edit.php?level_id={$level_info.level_id}'>{lang_print id=285}</a></div></td></tr>
<tr><td width='100' nowrap='nowrap' class='vert_tab' style='border-top: none;'><div style='width: 100px;'><a href='admin_levels_usersettings.php?level_id={$level_info.level_id}'>{lang_print id=286}</a></div></td></tr>
<tr><td width='100' nowrap='nowrap' class='vert_tab' style='border-top: none;'><div style='width: 100px;'><a href='admin_levels_messagesettings.php?level_id={$level_info.level_id}'>{lang_print id=287}</a></div></td></tr>
{section name=level_plugin_loop loop=$global_plugins}
{section name=level_page_loop loop=$global_plugins[level_plugin_loop].plugin_pages_level}
  <tr><td width='100' nowrap='nowrap' class='vert_tab' style='border-top: none;{if $global_plugins[level_plugin_loop].plugin_pages_level[level_page_loop].page == $page} border-right: none;{/if}'><div style='width: 100px;'><a href='{$global_plugins[level_plugin_loop].plugin_pages_level[level_page_loop].link}?level_id={$level_info.level_id}'>{lang_print id=$global_plugins[level_plugin_loop].plugin_pages_level[level_page_loop].title}</a></div></td></tr>
{/section}
{/section}

<tr>
<td class='vert_tab0'>
  <div style='height: 1650px;'>&nbsp;</div>
</td>
</tr>
</table>

{include file='admin_footer.tpl'}