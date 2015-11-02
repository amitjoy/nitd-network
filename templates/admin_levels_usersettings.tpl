{include file='admin_header.tpl'}

{* $Id: admin_levels_usersettings.tpl 8 2009-01-11 06:02:53Z john $ *}

<h2>{lang_sprintf id=288 1=$level_info.level_name}</h2>
{lang_print id=282}

<table cellspacing='0' cellpadding='0' width='100%' style='margin-top: 20px;'>
<tr>
<td class='vert_tab0'>&nbsp;</td>
<td valign='top' class='pagecell' rowspan='{math equation="x+5" x=$level_menu|@count}'>
  <h2>{lang_print id=286}</h2>
  {lang_print id=289}
  <br />
  <br />

  {if $result != 0}
    <div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=191}</div>
  {/if}

  {if $is_error != 0}
    <div class='error'><img src='../images/error.gif' class='icon' border='0'> {lang_print id=$is_error}</div> 
  {/if}

  <form action='admin_levels_usersettings.php' method='post' id='info' name='info'>
  <table cellpadding='0' cellspacing='0' width='100%'>
  <tr><td class='header'>{lang_print id=292}</td></tr>
  <tr><td class='setting1'>
  {lang_print id=293}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='radio' name='level_profile_block' id='profile_block_1' value='1'{if $level_info.level_profile_block == 1} CHECKED{/if}>&nbsp;</td><td><label for='profile_block_1'>{lang_print id=294}</label></td></tr>
    <tr><td><input type='radio' name='level_profile_block' id='profile_block_0' value='0'{if $level_info.level_profile_block == 0} CHECKED{/if}>&nbsp;</td><td><label for='profile_block_0'>{lang_print id=295}</label></td></tr>
    </table>
  </td></tr></table>

  <br>

  <table cellpadding='0' cellspacing='0' width='100%'>
  <tr><td class='header'>{lang_print id=296}</td></tr>
  <tr><td class='setting1'>
  <b>{lang_print id=297}</b><br>
  {lang_print id=298}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
      <tr><td><input type='radio' name='level_profile_search' id='profile_search1' value='1' {if $level_info.level_profile_search == 1} CHECKED{/if}></td><td><label for='profile_search1'>{lang_print id=299}</label>&nbsp;&nbsp;</td></tr>
      <tr><td><input type='radio' name='level_profile_search' id='profile_search0' value='0' {if $level_info.level_profile_search == 0} CHECKED{/if}></td><td><label for='profile_search0'>{lang_print id=300}</label>&nbsp;&nbsp;</td></tr>
    </table>
  </td></tr>
  <tr><td class='setting1'>
  <b>{lang_print id=301}</b><br>
  {lang_print id=302} 
  </td></tr>
  <tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    {foreach from=$profile_privacy key=k item=v}
      <tr><td><input type='checkbox' name='level_profile_privacy[]' id='privacy_{$k}' value='{$k}'{if $k|in_array:$level_profile_privacy} CHECKED{/if}></td><td><label for='privacy_{$k}'>{lang_print id=$v}</label>&nbsp;&nbsp;</td></tr>
    {/foreach}
    </table>
  </td></tr>
  <tr><td class='setting1'>
  <b>{lang_print id=303}</b><br>
  {lang_print id=304}
  </td></tr>
  <tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    {foreach from=$profile_comments key=k item=v}
      <tr><td><input type='checkbox' name='level_profile_comments[]' id='comments_{$k}' value='{$k}'{if $k|in_array:$level_profile_comments} CHECKED{/if}></td><td><label for='comments_{$k}'>{lang_print id=$v}</label>&nbsp;&nbsp;</td></tr>
    {/foreach}
    </table>
  </td></tr>
  </table>
  
  <br>

  <table cellpadding='0' cellspacing='0' width='100%'>
  <tr><td class='header'>{lang_print id=305}</td></tr>
  <tr><td class='setting1'>
  {lang_print id=306}
  </td></tr>
  <tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='radio' name='level_photo_allow' id='photo_allow_1' value='1'{if $level_info.level_photo_allow == 1} CHECKED{/if}>&nbsp;</td><td><label for='photo_allow_1'>{lang_print id=307}</label></td></tr>
    <tr><td><input type='radio' name='level_photo_allow' id='photo_allow_0' value='0'{if $level_info.level_photo_allow == 0} CHECKED{/if}>&nbsp;</td><td><label for='photo_allow_0'>{lang_print id=308}</label></td></tr>
    </table>
  </td></tr>
  <tr><td class='setting1'>
  {lang_print id=309}
  </td></tr>
  <tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr>
    <td>{lang_print id=310} &nbsp;</td>
    <td><input type='text' class='text' name='level_photo_width' value='{$level_info.level_photo_width}' maxlength='3' size='3'> &nbsp;</td>
    <td>{lang_print id=311}</td>
    </tr>
    <tr>
    <td>{lang_print id=312} &nbsp;</td>
    <td><input type='text' class='text' name='level_photo_height' value='{$level_info.level_photo_height}' maxlength='3' size='3'> &nbsp;</td>
    <td>{lang_print id=311}</td>
    </tr>
    </table>
  </td></tr>
  <tr><td class='setting1'>
  {lang_print id=313}
  </td></tr>
  <tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr>
    <td>{lang_print id=314} &nbsp;</td>
    <td><input type='text' class='text' name='level_photo_exts' value='{$level_info.level_photo_exts}' size='40' maxlength='50'></td>
    </tr>
    </table>
  </td></tr>
  </table>
  
  <br>

  <table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
  <td class='header'>{lang_print id=315}</td>
  </tr>
  <tr><td class='setting1'>
  {lang_print id=316}
  </td></tr>
  <tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='radio' name='level_profile_style' id='profile_style_1' value='1'{if $level_info.level_profile_style == 1} CHECKED{/if}>&nbsp;</td><td><label for='profile_style_1'>{lang_print id=317}</label></td></tr>
    <tr><td><input type='radio' name='level_profile_style' id='profile_style_0' value='0'{if $level_info.level_profile_style == 0} CHECKED{/if}>&nbsp;</td><td><label for='profile_style_0'>{lang_print id=318}</label></td></tr>
    </table>
  </td></tr>
  <tr><td class='setting1'>
  {lang_print id=978}
  </td></tr>
  <tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='radio' name='level_profile_style_sample' id='profile_style_sample_1' value='1'{if $level_info.level_profile_style_sample == 1} CHECKED{/if}>&nbsp;</td><td><label for='profile_style_sample_1'>{lang_print id=982}</label></td></tr>
    <tr><td><input type='radio' name='level_profile_style_sample' id='profile_style_sample_0' value='0'{if $level_info.level_profile_style_sample == 0} CHECKED{/if}>&nbsp;</td><td><label for='profile_style_sample_0'>{lang_print id=983}</label></td></tr>
    </table>
  </td></tr>
  </table>
  
  <br>
  
  <table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
  <td class='header'>{lang_print id=319}</td>
  </tr>
  <tr><td class='setting1'>
  {lang_print id=320}
  </td></tr>
  <tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='radio' name='level_profile_status' id='profile_status_1' value='1'{if $level_info.level_profile_status == 1} CHECKED{/if}>&nbsp;</td><td><label for='profile_status_1'>{lang_print id=321}</label></td></tr>
    <tr><td><input type='radio' name='level_profile_status' id='profile_status_0' value='0'{if $level_info.level_profile_status == 0} CHECKED{/if}>&nbsp;</td><td><label for='profile_status_0'>{lang_print id=322}</label></td></tr>
    </table>
  </td></tr>
  </table>
  
  <br>
  
  <table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
  <td class='header'>{lang_print id=1047}</td>
  </tr>
  <tr><td class='setting1'>
  {lang_print id=1048}
  </td></tr>
  <tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='radio' name='level_profile_invisible' id='profile_invisible_1' value='1'{if $level_info.level_profile_invisible == 1} CHECKED{/if}>&nbsp;</td><td><label for='profile_invisible_1'>{lang_print id=1051}</label></td></tr>
    <tr><td><input type='radio' name='level_profile_invisible' id='profile_invisible_0' value='0'{if $level_info.level_profile_invisible == 0} CHECKED{/if}>&nbsp;</td><td><label for='profile_invisible_0'>{lang_print id=1052}</label></td></tr>
    </table>
  </td></tr>
  </table>
  
  <br>
  
  <table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
  <td class='header'>{lang_print id=1049}</td>
  </tr>
  <tr><td class='setting1'>
  {lang_print id=1050}
  </td></tr>
  <tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='radio' name='level_profile_views' id='profile_views_1' value='1'{if $level_info.level_profile_views == 1} CHECKED{/if}>&nbsp;</td><td><label for='profile_views_1'>{lang_print id=1053}</label></td></tr>
    <tr><td><input type='radio' name='level_profile_views' id='profile_views_0' value='0'{if $level_info.level_profile_views == 0} CHECKED{/if}>&nbsp;</td><td><label for='profile_views_0'>{lang_print id=1054}</label></td></tr>
    </table>
  </td></tr>
  </table>
  
  <br>
  
  <table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
  <td class='header'>{lang_print id=820}</td>
  </tr>
  <tr><td class='setting1'>
  {lang_print id=821}
  </td></tr>
  <tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='radio' name='level_profile_change' id='profile_change_1' value='1'{if $level_info.level_profile_change == 1} CHECKED{/if}>&nbsp;</td><td><label for='profile_change_1'>{lang_print id=822}</label></td></tr>
    <tr><td><input type='radio' name='level_profile_change' id='profile_change_0' value='0'{if $level_info.level_profile_change == 0} CHECKED{/if}>&nbsp;</td><td><label for='profile_change_0'>{lang_print id=823}</label></td></tr>
    </table>
  </td></tr>
  </table>
  
  <br>
  
  <table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
  <td class='header'>{lang_print id=824}</td>
  </tr>
  <tr><td class='setting1'>
  {lang_print id=825}
  </td></tr>
  <tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='radio' name='level_profile_delete' id='profile_delete_1' value='1'{if $level_info.level_profile_delete == 1} CHECKED{/if}>&nbsp;</td><td><label for='profile_delete_1'>{lang_print id=826}</label></td></tr>
    <tr><td><input type='radio' name='level_profile_delete' id='profile_delete_0' value='0'{if $level_info.level_profile_delete == 0} CHECKED{/if}>&nbsp;</td><td><label for='profile_delete_0'>{lang_print id=827}</label></td></tr>
    </table>
  </td></tr>
  </table>
  
  <br>

  <input type='submit' class='button' value='{lang_print id=173}'>
  <input type='hidden' name='task' value='dosave'>
  <input type='hidden' name='level_id' value='{$level_info.level_id}'>
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