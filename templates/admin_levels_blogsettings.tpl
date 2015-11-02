{include file='admin_header.tpl'}

{* $Id: admin_levels_blogsettings.tpl 16 2009-01-13 04:01:31Z john $ *}

<h2>{lang_sprintf id=288 1=$level_info.level_name}</h2>
{lang_print id=282}

<table cellspacing='0' cellpadding='0' width='100%' style='margin-top: 20px;'>
<tr>
<td class='vert_tab0'>&nbsp;</td>
<td valign='top' class='pagecell' rowspan='{math equation="x+5" x=$level_menu|@count}'>

  <h2>{lang_print id=1500001}</h2>
  {lang_print id=1500092}
  <br />
  <br />
  
  {* SHOW SUCCESS MESSAGE *}
  {if $result != 0}
    <div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=191}</div>
  {/if}
  
  {* SHOW ERROR MESSAGE *}
  {if $is_error != 0}
    <div class='error'><img src='../images/error.gif' class='icon' border='0'> {lang_print id=$is_error}</div>
  {/if}
  
  
  <form action='admin_levels_blogsettings.php' name='info' method='POST'>
  
  
  <table cellpadding='0' cellspacing='0' width='600'>
    <tr>
      <td class='header'>{lang_print id=1500143}</td>
    </tr>
    <tr>
      <td class='setting1'>{lang_print id=1500144}</td>
    </tr>
    <tr>
      <td class='setting2'>
        <table cellpadding='0' cellspacing='0'>
          <tr>
            <td><input type='radio' name='level_blog_view' id='level_blog_view_1' value='1'{if  $level_info.level_blog_view} checked{/if} />&nbsp;</td>
            <td><label for='level_blog_view_1'>{lang_print id=1500145}</label></td>
          </tr>
          <tr>
            <td><input type='radio' name='level_blog_view' id='level_blog_view_0' value='0'{if !$level_info.level_blog_view} checked{/if} />&nbsp;</td>
            <td><label for='level_blog_view_0'>{lang_print id=1500146}</label></td>
          </tr>
        </table>
      </td>
    </tr>
    
    <tr>
      <td class='setting1'>{lang_print id=1500147}</td>
    </tr>
    <tr>
      <td class='setting2'>
        <table cellpadding='0' cellspacing='0'>
          <tr>
            <td><input type='radio' name='level_blog_create' id='level_blog_create_1' value='1'{if  $level_info.level_blog_create} checked{/if} />&nbsp;</td>
            <td><label for='level_blog_create_1'>{lang_print id=1500148}</label></td>
          </tr>
          <tr>
            <td><input type='radio' name='level_blog_create' id='level_blog_create_0' value='0'{if !$level_info.level_blog_create} checked{/if} />&nbsp;</td>
            <td><label for='level_blog_create_0'>{lang_print id=1500149}</label></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <br />
  
  
  <table cellpadding='0' cellspacing='0' width='600'>
    <tr>
      <td class='header'>{lang_print id=1500166}</td>
    </tr>
    <tr>
      <td class='setting1'>{lang_print id=1500167}</td>
    </tr>
    <tr>
      <td class='setting2'>
        <table cellpadding='0' cellspacing='0'>
          <tr>
            <td><input type='radio' name='level_blog_category_create' id='level_blog_category_create_1' value='1'{if  $level_info.level_blog_category_create} checked{/if} />&nbsp;</td>
            <td><label for='level_blog_category_create_1'>{lang_print id=1500168}</label></td>
          </tr>
          <tr>
            <td><input type='radio' name='level_blog_category_create' id='level_blog_category_create_0' value='0'{if !$level_info.level_blog_category_create} checked{/if} />&nbsp;</td>
            <td><label for='level_blog_category_create_0'>{lang_print id=1500169}</label></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <br />
  
  
  <table cellpadding='0' cellspacing='0' width='600'>
    <tr>
      <td class='header'>{lang_print id=1500097}</td>
    </tr>
    <tr>
      <td class='setting1'>{lang_print id=1500098}</td>
    </tr>
    <tr>
    <td class='setting2'>
      <table cellpadding='0' cellspacing='0'>
        <tr>
          <td><input type='text' class='text' size='2' name='level_blog_entries' maxlength='3' value='{$level_info.level_blog_entries}' /></td>
          <td>&nbsp; {lang_sprintf id=1500099 1=''}</td>
        </tr>
      </table>
    </td>
    </tr>
  </table>
  <br />
  
  
  <table cellpadding='0' cellspacing='0' width='600'>
    <tr>
      <td class='header'>{lang_print id=1500100}</td>
    </tr>
    <tr>
      <td class='setting1'>{lang_print id=1500101}</td>
    </tr>
    <tr>
      <td class='setting2'>
        <table cellpadding='0' cellspacing='0'>
          <tr>
            <td><input type='radio' name='level_blog_search' id='level_blog_search_1' value='1'{if  $level_info.level_blog_search} checked{/if} /></td>
            <td><label for='level_blog_search_1'>{lang_print id=1500102}</label>&nbsp;&nbsp;</td>
          </tr>
          <tr>
            <td><input type='radio' name='level_blog_search' id='level_blog_search_0' value='0'{if !$level_info.level_blog_search} checked{/if} /></td>
            <td><label for='level_blog_search_0'>{lang_print id=1500103}</label>&nbsp;&nbsp;</td>
          </tr>
        </table>
      </td>
    </tr>
    
    <tr>
      <td class='setting1'>{lang_print id=1500104}</td>
    </tr>
    <tr>
      <td class='setting2'>
        <table cellpadding='0' cellspacing='0'>
        {foreach from=$blog_privacy key=k item=v}
          <tr>
            <td><input type='checkbox' name='level_blog_privacy[]' id='privacy_{$k}' value='{$k}'{if $k|in_array:$level_blog_privacy} checked{/if} /></td>
            <td><label for='privacy_{$k}'>{lang_print id=$v}</label>&nbsp;&nbsp;</td>
          </tr>
        {/foreach}
        </table>
      </td>
    </tr>
    
    <tr>
      <td class='setting1'>{lang_print id=1500105}</td>
    </tr>
    <tr>
      <td class='setting2'>
        <table cellpadding='0' cellspacing='0'>
        {foreach from=$blog_comments key=k item=v}
          <tr>
            <td><input type='checkbox' name='level_blog_comments[]' id='comments_{$k}' value='{$k}'{if $k|in_array:$level_blog_comments} checked{/if} /></td>
            <td><label for='comments_{$k}'>{lang_print id=$v}</label>&nbsp;&nbsp;</td>
          </tr>
        {/foreach}
        </table>
      </td>
    </tr>
  </table>
  <br />
  
  
  <table cellpadding='0' cellspacing='0' width='600'>
    <tr>
      <td class='header'>{lang_print id=1500106}</td>
    </tr>
    <tr>
      <td class='setting1'>{lang_print id=1500107}</td>
    </tr>
    <tr>
      <td class='setting2'>
        <table cellpadding='0' cellspacing='0'>
        <tr>
          <td><input type='radio' name='level_blog_style' id='level_blog_style_1' value='1'{if  $level_info.level_blog_style} checked{/if} />&nbsp;</td>
          <td><label for='level_blog_style_1'>{lang_print id=1500108}</label></td>
        </tr>
        <tr>
          <td><input type='radio' name='level_blog_style' id='level_blog_style_0' value='0'{if !$level_info.level_blog_style} checked{/if} />&nbsp;</td>
          <td><label for='level_blog_style_0'>{lang_print id=1500109}</label></td>
        </tr>
        </table>
      </td>
    </tr>
  </table>
  <br />
  
  
  <table cellpadding='0' cellspacing='0' width='600'>
    <tr>
      <td class='header'>{lang_print id=1500136}</td>
    </tr>
    <tr>
      <td class='setting1'>{lang_print id=1500137}</td>
    </tr>
    <tr>
      <td class='setting2'>
        <table cellpadding='0' cellspacing='0'>
        <tr>
          <td><input type='radio' name='level_blog_trackbacks_allow' id='level_blog_trackbacks_allow_1' value='1'{if  $level_info.level_blog_trackbacks_allow} checked{/if} />&nbsp;</td>
          <td><label for='level_blog_trackbacks_allow_1'>{lang_print id=1500138}</label></td>
        </tr>
        <tr>
          <td><input type='radio' name='level_blog_trackbacks_allow' id='level_blog_trackbacks_allow_0' value='0'{if !$level_info.level_blog_trackbacks_allow} checked{/if} />&nbsp;</td>
          <td><label for='level_blog_trackbacks_allow_0'>{lang_print id=1500139}</label></td>
        </tr>
        </table>
      </td>
    </tr>
    
    <tr>
      <td class='setting1'>{lang_print id=1500140}</td>
    </tr>
    <tr>
      <td class='setting2'>
        <table cellpadding='0' cellspacing='0'>
        <tr>
          <td><input type='radio' name='level_blog_trackbacks_detect' id='level_blog_trackbacks_detect_1' value='1'{if  $level_info.level_blog_trackbacks_detect} checked{/if} />&nbsp;</td>
          <td><label for='level_blog_trackbacks_detect_1'>{lang_print id=1500141}</label></td>
        </tr>
        <tr>
          <td><input type='radio' name='level_blog_trackbacks_detect' id='level_blog_trackbacks_detect_0' value='0'{if !$level_info.level_blog_trackbacks_detect} checked{/if} />&nbsp;</td>
          <td><label for='level_blog_trackbacks_detect_0'>{lang_print id=1500142}</label></td>
        </tr>
        </table>
      </td>
    </tr>
  </table>
  <br />
  
  
  <table cellpadding='0' cellspacing='0' width='600'>
    <tr>
      <td class='header'>{lang_print id=1500134}</td>
    </tr>
    <tr>
      <td class='setting1'>{lang_print id=1500135}</td>
    </tr>
    <tr>
      <td class='setting2'>
        <input type='text' class='text' name='level_blog_html' value='{$level_blog_html}' size='60' />
      </td>
    </tr>
  </table>
  <br />
  
  
  {lang_block id=173 var=langBlockTemp}<input type='submit' class='button' value='{$langBlockTemp}' />{/lang_block}
  <input type='hidden' name='task' value='dosave' />
  <input type='hidden' name='level_id' value='{$level_info.level_id}' />
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
  <div style='height: 1200px;'>&nbsp;</div>
</td>
</tr>
</table>


{include file='admin_footer.tpl'}