{include file='admin_header.tpl'}

{* $Id: admin_levels_videosettings.tpl 94 2009-03-11 23:08:52Z szerrade $ *}

<h2>{lang_sprintf id=288 1=$level_info.level_name}</h2>
{lang_print id=282}

<table cellspacing='0' cellpadding='0' width='100%' style='margin-top: 20px;'>
<tr>
<td class='vert_tab0'>&nbsp;</td>
<td valign='top' class='pagecell' rowspan='{math equation="x+5" x=$level_menu|@count}'>

  <h2>{lang_print id=5500046}</h2>
  {lang_print id=5500066}

  <br><br>

  {* SHOW SUCCESS MESSAGE *}
  {if $result != 0}
    <div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=191}</div>
  {/if}

  {* SHOW ERROR MESSAGE *}
  {if $is_error != 0}
    <div class='error'><img src='../images/error.gif' class='icon' border='0'> {lang_print id=$is_error}</div>
  {/if}

  <table cellpadding='0' cellspacing='0' width='600'>
  <form action='admin_levels_videosettings.php' method='POST'>
  <tr><td class='header'>{lang_print id=5500047}</td></tr>
  {if $ffmpeg_path!=""}
  <tr><td class='setting1'>
  {lang_print id=5500048}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='radio' name='level_video_allow' id='video_allow_1' value='1'{if $level_info.level_video_allow == 1} checked='checked'{/if}>&nbsp;</td><td><label for='video_allow_1'>{lang_print id=5500049}</label></td></tr>
    <tr><td><input type='radio' name='level_video_allow' id='video_allow_0' value='0'{if $level_info.level_video_allow == 0} checked='checked'{/if}>&nbsp;</td><td><label for='video_allow_0'>{lang_print id=5500050}</label></td></tr>
    </table>
  {/if}
  <tr><td class='setting1'>
  {lang_print id=5500193}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='radio' name='level_youtube_allow' id='youtube_allow_1' value='1'{if $level_info.level_youtube_allow == 1} checked='checked'{/if}>&nbsp;</td><td><label for='youtube_allow_1'>{lang_print id=5500194}</label></td></tr>
    <tr><td><input type='radio' name='level_youtube_allow' id='youtube_allow_0' value='0'{if $level_info.level_youtube_allow == 0} checked='checked'{/if}>&nbsp;</td><td><label for='youtube_allow_0'>{lang_print id=5500195}</label></td></tr>
    </table>




  </td></tr></table>


  <br>

  <table cellpadding='0' cellspacing='0' width='600'>
  <tr><td class='header'>{lang_print id=5500051}</td></tr>
  <tr><td class='setting1'>
  <b>{lang_print id=5500052}</b><br>{lang_print id=5500053}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
      <tr><td><input type='radio' name='level_video_search' id='video_search_1' value='1'{if $level_info.level_video_search == 1} checked='checked'{/if}></td><td><label for='video_search_1'>{lang_print id=5500054}</label>&nbsp;&nbsp;</td></tr>
      <tr><td><input type='radio' name='level_video_search' id='video_search_0' value='0'{if $level_info.level_video_search == 0} checked='checked'{/if}></td><td><label for='video_search_0'>{lang_print id=5500055}</label>&nbsp;&nbsp;</td></tr>
    </table>
  </td></tr>
  <tr><td class='setting1'>
  <b>{lang_print id=5500056}</b><br>{lang_print id=5500057}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    {foreach from=$video_privacy key=k item=v}
      <tr><td><input type='checkbox' name='level_video_privacy[]' id='privacy_{$k}' value='{$k}'{if $k|in_array:$level_video_privacy} checked='checked'{/if}></td><td><label for='privacy_{$k}'>{lang_print id=$v}</label>&nbsp;&nbsp;</td></tr>
    {/foreach}
    </table>
  </td></tr>
  <tr><td class='setting1'>
  <b>{lang_print id=5500058}</b><br>{lang_print id=5500059}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    {foreach from=$video_comments key=k item=v}
      <tr><td><input type='checkbox' name='level_video_comments[]' id='comments_{$k}' value='{$k}'{if $k|in_array:$level_video_comments} checked='checked'{/if}></td><td><label for='comments_{$k}'>{lang_print id=$v}</label>&nbsp;&nbsp;</td></tr>
    {/foreach}
    </table>
  </td></tr>
  </table>
  
  <br>

  <table cellpadding='0' cellspacing='0' width='600'>
  <tr><td class='header'>{lang_print id=5500060}</td></tr>
  <tr><td class='setting1'>
  {lang_print id=5500061}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='text' name='level_video_maxnum' value='{$level_info.level_video_maxnum}' maxlength='3' size='5'>&nbsp;{lang_print id=5500062}</tr>
    </table>
  </td></tr></table>

  <br>

  <table cellpadding='0' cellspacing='0' width='600'>
  <tr><td class='header'>{lang_print id=5500063}</td></tr>
  <tr><td class='setting1'>
  {lang_print id=5500064}
  </td></tr><tr><td class='setting2'>
  <input type='text' class='text' size='5' name='level_video_maxsize' maxlength='6' value='{$level_info.level_video_maxsize}'> KB
  </td></tr>
  </table>

  <br>

  
  <input type='submit' class='button' value='{lang_print id=5500065}'>
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