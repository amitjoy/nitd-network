{include file='admin_header.tpl'}

{* $Id: admin_levels_pollsettings.tpl 59 2009-02-13 03:25:54Z john $ *}

<h2>{lang_sprintf id=288 1=$level_name}</h2>
{lang_print id=282}

<table cellspacing='0' cellpadding='0' width='100%' style='margin-top: 20px;'>
<tr>
<td class='vert_tab0'>&nbsp;</td>
<td valign='top' class='pagecell' rowspan='{math equation="x+5" x=$level_menu|@count}'>

  <h2>{lang_print id=2500001}</h2>
  {lang_print id=2500006}
  <br />
  <br />

  {if !empty($result)}
    <div class='success'><img src='../images/success.gif' class='icon' border='0' /> {lang_print id=191}</div>
  {/if}

  {if !empty($is_error)}
    <div class='error'><img src='../images/error.gif' class='icon' border='0' /> {if is_numeric($is_error)}{lang_print id=$is_error}{else}{$is_error}{/if}</div>
  {/if}
  
  
  <form action='admin_levels_pollsettings.php' name='info' method='post'>
  
  <table cellpadding='0' cellspacing='0' width='600'>
    <tr>
      <td class='header'>{lang_print id=2500007}</td>
    </tr>
    <tr>
      <td class='setting1'>{lang_print id=2500008}</td>
    </tr>
    <tr>
      <td class='setting2'>
        <table cellpadding='0' cellspacing='0'>
          <tr>
            <td><input type='radio' name='level_poll_allow' id='level_poll_allow_7' value='7'{if $level_info.level_poll_allow==7} checked{/if} />&nbsp;</td>
            <td><label for='level_poll_allow_7'>{lang_print id=2500141}</label></td>
          </tr>
          <tr>
            <td><input type='radio' name='level_poll_allow' id='level_poll_allow_3' value='3'{if $level_info.level_poll_allow==3} checked{/if} />&nbsp;</td>
            <td><label for='level_poll_allow_3'>{lang_print id=2500142}</label></td>
          </tr>
          <tr>
            <td><input type='radio' name='level_poll_allow' id='level_poll_allow_1' value='1'{if $level_info.level_poll_allow==1} checked{/if} />&nbsp;</td>
            <td><label for='level_poll_allow_1'>{lang_print id=2500143}</label></td>
          </tr>
          <tr>
            <td><input type='radio' name='level_poll_allow' id='level_poll_allow_0' value='0'{if $level_info.level_poll_allow==0} checked{/if} />&nbsp;</td>
            <td><label for='level_poll_allow_0'>{lang_print id=2500144}</label></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <br />
  
  
  <table cellpadding='0' cellspacing='0' width='600'>
    <tr>
      <td class='header'>{lang_print id=2500011}</td>
    </tr>
      <td class='setting1'>{lang_print id=2500012}</td>
    </tr>
    <tr>
      <td class='setting2'>
        <table cellpadding='0' cellspacing='0'>
          <tr>
            <td><input type='text' class='text' size='2' name='level_poll_entries' maxlength='3' value='{$entries_value}'></td>
            <td>&nbsp;{lang_sprintf id=2500013 1=''}</td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <br />
  
  
  <table cellpadding='0' cellspacing='0' width='600'>
    <tr>
      <td class='header'>{lang_print id=2500014}</td>
    </tr>
    <tr>
      <td class='setting1'><b>{lang_print id=2500015}</b><br />{lang_print id=2500016}</td>
    </tr>
    <tr>
      <td class='setting2'>
        <table cellpadding='0' cellspacing='0'>
          <tr>
            <td><input type='radio' name='level_poll_search' id='poll_search_1' value='1'{if  $poll_search} CHECKED{/if}></td>
            <td><label for='poll_search_1'>{lang_print id=2500017}</label>&nbsp;&nbsp;</td>
          </tr>
          <tr>
            <td><input type='radio' name='level_poll_search' id='poll_search_0' value='0'{if !$poll_search} CHECKED{/if}></td>
            <td><label for='poll_search_0'>{lang_print id=2500018}</label>&nbsp;&nbsp;</td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td class='setting1'><b>{lang_print id=2500019}</b><br />{lang_print id=2500020}</td>
    </tr>
    <tr>
      <td class='setting2'>
        <table cellpadding='0' cellspacing='0'>
          {foreach from=$privacy_options key=k item=v}
          <tr>
            <td><input type='checkbox' name='level_poll_privacy[]' id='privacy_{$k}' value='{$k}'{if $k|in_array:$poll_privacy} CHECKED{/if}></td>
            <td><label for='privacy_{$k}'>{lang_print id=$v}</label>&nbsp;&nbsp;</td>
          </tr>
          {/foreach}
        </table>
      </td>
    </tr>
    <tr>
      <td class='setting1'>{lang_print id=2500021}</b><br />{lang_print id=2500022}</td>
    </tr>
    <tr>
      <td class='setting2'>
        <table cellpadding='0' cellspacing='0'>
        {foreach from=$comment_options key=k item=v}
          <tr><td><input type='checkbox' name='level_poll_comments[]' id='comment_{$k}' value='{$k}'{if $k|in_array:$poll_comments} CHECKED{/if}></td><td><label for='comment_{$k}'>{lang_print id=$v}</label>&nbsp;&nbsp;</td></tr>
        {/foreach}
        </table>
      </td>
    </tr>
  </table>
  
  <br>
  
  {lang_block id=173 var=langBlockTemp}<input type='submit' class='button' value='{$langBlockTemp}'>{/lang_block}
  <input type='hidden' name='task' value='dosave'>
  <input type='hidden' name='level_id' value='{$level_id}'>
  </form>
  
</td>
</tr>

{* DISPLAY MENU *}
<tr><td width='100' nowrap='nowrap' class='vert_tab'><div style='width: 100px;'><a href='admin_levels_edit.php?level_id={$level_id}'>{lang_print id=285}</a></div></td></tr>
<tr><td width='100' nowrap='nowrap' class='vert_tab' style='border-top: none;'><div style='width: 100px;'><a href='admin_levels_usersettings.php?level_id={$level_id}'>{lang_print id=286}</a></div></td></tr>
<tr><td width='100' nowrap='nowrap' class='vert_tab' style='border-top: none;'><div style='width: 100px;'><a href='admin_levels_messagesettings.php?level_id={$level_id}'>{lang_print id=287}</a></div></td></tr>
{foreach from=$global_plugins key=plugin_k item=plugin_v}
{section name=level_page_loop loop=$plugin_v.plugin_pages_level}
  <tr><td width='100' nowrap='nowrap' class='vert_tab' style='border-top: none;{if $plugin_v.plugin_pages_level[level_page_loop].page == $page} border-right: none;{/if}'><div style='width: 100px;'><a href='{$plugin_v.plugin_pages_level[level_page_loop].link}?level_id={$level_info.level_id}'>{lang_print id=$plugin_v.plugin_pages_level[level_page_loop].title}</a></div></td></tr>
{/section}
{/foreach}

<tr>
<td class='vert_tab0'>
  <div style='height: 760px;'>&nbsp;</div>
</td>
</tr>
</table>

{include file='admin_footer.tpl'}