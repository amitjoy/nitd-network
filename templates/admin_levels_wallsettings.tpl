{include file='admin_header.tpl'}

<h2>{lang_sprintf id=288 1=$level_info.level_name}</h2>
{lang_print id=282}

<table cellspacing='0' cellpadding='0' width='100%' style='margin-top: 20px;'>
  <tr><td class='vert_tab0' style="height: 1px;">&nbsp;</td>
	<td valign='top' class='pagecell' rowspan='{math equation="x+5" x=$level_menu|@count}'>
	  <h2>{lang_print id=690706035}</h2>
	  {lang_print id=690706053}
	  <br />
	  <br />
  {if $result != 0}
    <div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=191}</div>
  {/if}
  
  {if $is_error != 0}
    <div class='error'><img src='../images/error.gif' class='icon' border='0'> {lang_print id=$is_error}</div> 
  {/if}

	<form action='admin_levels_wallsettings.php' method='post' id='info' name='info'>
	  <table cellpadding='0' cellspacing='0' width='600'>
	  <tr><td class='header'>{lang_print id=690706054}</td></tr>
	  <tr><td class='setting1'>
	  {lang_print id=690706055}
	  </td></tr><tr><td class='setting2'>
	    <table cellpadding='0' cellspacing='0'>
	    <tr><td><input type='radio' name='level_wall_allowed' id='profile_block_1' value='1'{if $level_info.level_wall_allowed == 1} CHECKED{/if}>&nbsp;</td><td><label for='profile_block_1'>{lang_print id=690706056}</label></td></tr>
	    <tr><td><input type='radio' name='level_wall_allowed' id='profile_block_0' value='0'{if $level_info.level_wall_allowed == 0} CHECKED{/if}>&nbsp;</td><td><label for='profile_block_0'>{lang_print id=690706057}</label></td></tr>
	    </table>
	  </td></tr></table>
	
      <br>

      <table cellpadding='0' cellspacing='0' width='600'>
      <tr><td class='header'>{lang_print id=690706059}</td></tr>
      <tr><td class='setting1'>
      <b>{lang_print id=690706060}</b><br>{lang_print id=690706061}
      </td></tr><tr><td class='setting2'>
        <table cellpadding='0' cellspacing='0'>
        {foreach from=$privacy_options key=k item=v}
          <tr><td><input type='checkbox' name='level_wall_action_privacy[]' id='privacy_{$k}' value='{$k}'{if $k|in_array:$level_wall_action_privacy} checked='checked'{/if}></td><td><label for='privacy_{$k}'>{lang_print id=$v}</label>&nbsp;&nbsp;</td></tr>
        {/foreach}
        </table>
      </td></tr>
      </table>
    
	  <br />
	  
	  <input type='submit' class='button' value='{lang_print id=173}'>
	  <input type='hidden' name='task' value='dosave'>
	  <input type='hidden' name='level_id' value='{$level_info.level_id}'>
	  </form>
	</td>
	</tr>

{* DISPLAY MENU *}
<tr><td width='100' nowrap='nowrap' class='vert_tab'><div style='width: 100px;'><a href='admin_levels_edit.php?level_id={$level_info.level_id}'>{lang_print id=285}</a></div></td></tr>
<tr><td width='100' nowrap='nowrap' class='vert_tab' style='border-top: none;'><div style='width: 100px;'><a href='admin_levels_usersettings.php?level_id={$level_info.level_id}'>{lang_print id=286}</a></div></td></tr>
<tr><td width='100' nowrap='nowrap' class='vert_tab' style='border-top: none;'><div style='width: 100px;'><a href='admin_levels_messagesettings.php?level_id={$level_info.level_id}'>{lang_print id=287}</a></div></td></tr>
{foreach from=$global_plugins key=plugin_k item=plugin_v}
{section name=level_page_loop loop=$plugin_v.plugin_pages_level}
  <tr><td width='100' nowrap='nowrap' class='vert_tab' style='border-top: none;{if $plugin_v.plugin_pages_level[level_page_loop].page == $page} border-right: none;{/if}'><div style='width: 100px;'><a href='{$plugin_v.plugin_pages_level[level_page_loop].link}?level_id={$level_info.level_id}'>{lang_print id=$plugin_v.plugin_pages_level[level_page_loop].title}</a></div></td></tr>
{/section}
{/foreach}

<tr>
<td class='vert_tab0'>
  <div style='height: 1650px;'>&nbsp;</div>
</td>
</tr>
</table>

{include file='admin_footer.tpl'}