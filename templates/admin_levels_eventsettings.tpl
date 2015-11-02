{include file='admin_header.tpl'}

{* $Id: admin_levels_eventsettings.tpl 59 2009-02-13 03:25:54Z john $ *}

<h2>{lang_sprintf id=288 1=$level_info.level_name}</h2>
{lang_print id=282}

<table cellspacing='0' cellpadding='0' width='100%' style='margin-top: 20px;'>
<tr>
<td class='vert_tab0'>&nbsp;</td>
<td valign='top' class='pagecell' rowspan='{math equation="x+5" x=$level_menu|@count}'>

  <h2>{lang_print id=3000001}</h2>
  {lang_print id=3000030}
  <br />
  <br />
  
  
  {* SHOW SUCCESS MESSAGE *}
  {if $result != 0}
    <div class='success'><img src='../images/success.gif' class='icon' border='0' /> {lang_print id=191}</div>
  {/if}
  
  {* SHOW ERROR MESSAGE *}
  {if $is_error != 0}
    <div class='error'><img src='../images/error.gif' class='icon' border='0' /> {lang_print id=$is_error}</div>
  {/if}
  
  
  <form action='admin_levels_eventsettings.php' method='post'>
  
  <table cellpadding='0' cellspacing='0' width='600'>
    <tr>
      <td class='header'>{lang_print id=3000031}</td>
    </tr>
    
    <tr>
      <td class='setting1'>{lang_print id=3000259}</td>
    </tr>
    <tr>
      <td class='setting2'>
        <table cellpadding='0' cellspacing='0'>
          <tr>
            <td><input type='radio' name='level_event_allow' id='level_event_allow_7' value='7'{if $level_info.level_event_allow==7} checked{/if} />&nbsp;</td>
            <td><label for='level_event_allow_7'>{lang_print id=3000260}</label></td>
          </tr>
          <tr>
            <td><input type='radio' name='level_event_allow' id='level_event_allow_3' value='3'{if $level_info.level_event_allow==3} checked{/if} />&nbsp;</td>
            <td><label for='level_event_allow_3'>{lang_print id=3000261}</label></td>
          </tr>
          <tr>
            <td><input type='radio' name='level_event_allow' id='level_event_allow_1' value='1'{if $level_info.level_event_allow==1} checked{/if} />&nbsp;</td>
            <td><label for='level_event_allow_1'>{lang_print id=3000262}</label></td>
          </tr>
          <tr>
            <td><input type='radio' name='level_event_allow' id='level_event_allow_0' value='0'{if $level_info.level_event_allow==0} checked{/if} />&nbsp;</td>
            <td><label for='level_event_allow_0'>{lang_print id=3000263}</label></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <br />
  
  
  <table cellpadding='0' cellspacing='0' width='600'>
    <tr>
      <td class='header'>{lang_print id=3000038}</td>
    </tr>
    <tr>
      <td class='setting1'>{lang_print id=3000039}</td>
    </tr>
    <tr>
      <td class='setting2'>
        <table cellpadding='0' cellspacing='0'>
          <tr>
            <td><input type='radio' name='level_event_photo' id='level_event_photo_1' value='1'{if  $level_info.level_event_photo} checked{/if} />&nbsp;</td>
            <td><label for='level_event_photo_1'>{lang_print id=3000040}</label></td>
          </tr>
          <tr>
            <td><input type='radio' name='level_event_photo' id='level_event_photo_0' value='0'{if !$level_info.level_event_photo} checked{/if} />&nbsp;</td>
            <td><label for='level_event_photo_0'>{lang_print id=3000041}</label></td>
          </tr>
        </table>
      </td>
    </tr>
    
    <tr>
      <td class='setting1'>{lang_print id=3000042}</td>
    </tr>
    <tr>
      <td class='setting2'>
        <table cellpadding='0' cellspacing='0'>
          <tr>
            <td>{lang_print id=3000043} &nbsp;</td>
            <td><input type='text' class='text' name='level_event_photo_width' value='{$level_info.level_event_photo_width}' maxlength='3' size='3' /> &nbsp;</td>
            <td>{lang_sprintf id=3000045 1=1 2=999}</td>
          </tr>
          <tr>
            <td>{lang_print id=3000044} &nbsp;</td>
            <td><input type='text' class='text' name='level_event_photo_height' value='{$level_info.level_event_photo_height}' maxlength='3' size='3' /> &nbsp;</td>
            <td>{lang_sprintf id=3000045 1=1 2=999}</td>
          </tr>
        </table>
      </td>
    </tr>
    
    <tr>
      <td class='setting1'>{lang_print id=3000046}</td>
    </tr>
    <tr>
      <td class='setting2'>
        <table cellpadding='0' cellspacing='0'>
          <tr>
            <td>{lang_print id=3000047} &nbsp;</td>
            <td><input type='text' class='text' name='level_event_photo_exts' value='{$level_event_photo_exts}' size='40' maxlength='50' /></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <br />
  
  
  <table cellpadding='0' cellspacing='0' width='600'>
    <tr>
      <td class='header'>{lang_print id=3000048}</td>
    </tr>
    <tr>
      <td class='setting1'>{lang_print id=3000049}</td>
    </tr>
    <tr>
      <td class='setting2'>
        <table cellpadding='0' cellspacing='0'>
          <tr>
            <td><input type='radio' name='level_event_search' id='level_event_search_1' value='1'{if  $level_info.level_event_search} checked{/if} /></td>
            <td><label for='level_event_search_1'>{lang_print id=3000050}</label>&nbsp;&nbsp;</td>
          </tr>
          <tr>
            <td><input type='radio' name='level_event_search' id='level_event_search_0' value='0'{if !$level_info.level_event_search} checked{/if} /></td>
            <td><label for='level_event_search_0'>{lang_print id=3000051}</label>&nbsp;&nbsp;</td>
          </tr>
        </table>
      </td>
    </tr>
    
    <tr>
      <td class='setting1'>{lang_print id=3000052}</td>
    </tr>
    <tr>
      <td class='setting2'>
        <table cellpadding='0' cellspacing='0'>
          {foreach from=$privacy_options key=k item=v}
          <tr>
            <td><input type='checkbox' name='level_event_privacy[]' id='level_event_privacy_{$k}' value='{$k}'{if $k|in_array:$level_event_privacy} checked{/if} /></td>
            <td><label for='level_event_privacy_{$k}'>{lang_print id=$v}</label>&nbsp;&nbsp;</td>
          </tr>
          {/foreach}
        </table>
      </td>
    </tr>
    
    <tr>
      <td class='setting1'>{lang_print id=3000053}</td>
    </tr>
    <tr>
    <td class='setting2'>
      <table cellpadding='0' cellspacing='0'>
        {foreach from=$comment_options key=k item=v}
        <tr>
          <td><input type='checkbox' name='level_event_comments[]' id='level_event_comments_{$k}' value='{$k}'{if $k|in_array:$level_event_comments} checked{/if} /></td>
          <td><label for='level_event_comments_{$k}'>{lang_print id=$v}</label>&nbsp;&nbsp;</td>
        </tr>
        {/foreach}
      </table>
    </td>
    </tr>
    
    <tr>
      <td class='setting1'>{lang_print id=3000257}</td>
    </tr>
    <tr>
    <td class='setting2'>
      <table cellpadding='0' cellspacing='0'>
        {foreach from=$upload_options key=k item=v}
        <tr>
          <td><input type='checkbox' name='level_event_upload[]' id='level_event_upload_{$k}' value='{$k}'{if $k|in_array:$level_event_upload} checked{/if} /></td>
          <td><label for='level_event_upload_{$k}'>{lang_print id=$v}</label>&nbsp;&nbsp;</td>
        </tr>
        {/foreach}
      </table>
    </td>
    </tr>
    
    <tr>
      <td class='setting1'>{lang_print id=3000258}</td>
    </tr>
    <tr>
    <td class='setting2'>
      <table cellpadding='0' cellspacing='0'>
        {foreach from=$tag_options key=k item=v}
        <tr>
          <td><input type='checkbox' name='level_event_tag[]' id='level_event_tag_{$k}' value='{$k}'{if $k|in_array:$level_event_tag} checked{/if} /></td>
          <td><label for='level_event_tag_{$k}'>{lang_print id=$v}</label>&nbsp;&nbsp;</td>
        </tr>
        {/foreach}
      </table>
    </td>
    </tr>
  </table>
  <br />
  
  
  <table cellpadding='0' cellspacing='0' width='600'>
    <tr>
      <td class='header'>{lang_print id=3000054}</td>
    </tr>
      <td class='setting1'>{lang_print id=3000055}</td>
    </tr>
    <tr>
      <td class='setting2'>
        <table cellpadding='0' cellspacing='0'>
          <tr>
            <td><input type='radio' name='level_event_inviteonly' id='level_event_inviteonly_1' value='1'{if  $level_info.level_event_inviteonly} checked{/if} />&nbsp;</td>
            <td><label for='level_event_inviteonly_1'>{lang_print id=3000056}</label></td>
          </tr>
          <tr>
            <td><input type='radio' name='level_event_inviteonly' id='level_event_inviteonly_0' value='0'{if !$level_info.level_event_inviteonly} checked{/if} />&nbsp;</td>
            <td><label for='level_event_inviteonly_0'>{lang_print id=3000057}</label></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <br />
  
  
  <table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=3000058}</td>
  </tr>
    <td class='setting1'>{lang_print id=3000059}</td>
  </tr>
  <tr>
    <td class='setting2'>
      <table cellpadding='0' cellspacing='0'>
        <tr>
          <td><input type='radio' name='level_event_style' id='level_event_style_1' value='1'{if  $level_info.level_event_style} checked{/if} />&nbsp;</td>
          <td><label for='level_event_style_1'>{lang_print id=3000060}</label></td>
        </tr>
        <tr>
          <td><input type='radio' name='level_event_style' id='level_event_style_0' value='0'{if !$level_info.level_event_style} checked{/if} />&nbsp;</td>
          <td><label for='level_event_style_0'>{lang_print id=3000061}</label></td>
        </tr>
      </table>
    </td>
  </tr>
  </table>
  <br />
  
  
  <table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=3000336}</td>
  </tr>
    <td class='setting1'>{lang_print id=3000337}</td>
  </tr>
  <tr>
    <td class='setting2'>
      <table cellpadding='0' cellspacing='0'>
        <tr>
          <td><input type='radio' name='level_event_backdate' id='level_event_backdate_1' value='1'{if  $level_info.level_event_backdate} checked{/if} />&nbsp;</td>
          <td><label for='level_event_backdate_1'>{lang_print id=3000338}</label></td>
        </tr>
        <tr>
          <td><input type='radio' name='level_event_backdate' id='level_event_backdate_0' value='0'{if !$level_info.level_event_backdate} checked{/if} />&nbsp;</td>
          <td><label for='level_event_backdate_0'>{lang_print id=3000339}</label></td>
        </tr>
      </table>
    </td>
  </tr>
  </table>
  <br />
  
  
  <table cellpadding='0' cellspacing='0' width='600'>
    <tr>
      <td class='header'>{lang_print id=3000062}</td>
    </tr>
    
    <tr>
      <td class='setting1'>{lang_print id=3000063}</td>
    </tr>
    <tr>
      <td class='setting2'>
        <textarea name='level_event_album_exts' rows='2' cols='40' class='text' style='width: 100%;'>{$level_event_album_exts}</textarea>
      </td>
    </tr>
    
    <tr>
      <td class='setting1'>{lang_print id=3000064}</td>
    </tr>
    <tr>
      <td class='setting2'>
        <textarea name='level_event_album_mimes' rows='2' cols='40' class='text' style='width: 100%;'>{$level_event_album_mimes}</textarea>
      </td>
    </tr>
    
    <tr>
      <td class='setting1'>{lang_print id=3000065}</td>
    </tr>
    <tr>
      <td class='setting2'>
      <select name='level_event_album_storage' class='text'>
        <option value='102400'{if $level_info.level_event_album_storage == 102400} SELECTED{/if}>{lang_sprintf id=3000070 1=100}</option>
        <option value='204800'{if $level_info.level_event_album_storage == 204800} SELECTED{/if}>{lang_sprintf id=3000070 1=200}</option>
        <option value='512000'{if $level_info.level_event_album_storage == 512000} SELECTED{/if}>{lang_sprintf id=3000070 1=500}</option>
        <option value='1048576'{if $level_info.level_event_album_storage == 1048576} SELECTED{/if}>{lang_sprintf id=3000071 1=1}</option>
        <option value='2097152'{if $level_info.level_event_album_storage == 2097152} SELECTED{/if}>{lang_sprintf id=3000071 1=2}</option>
        <option value='3145728'{if $level_info.level_event_album_storage == 3145728} SELECTED{/if}>{lang_sprintf id=3000071 1=3}</option>
        <option value='4194304'{if $level_info.level_event_album_storage == 4194304} SELECTED{/if}>{lang_sprintf id=3000071 1=4}</option>
        <option value='5242880'{if $level_info.level_event_album_storage == 5242880} SELECTED{/if}>{lang_sprintf id=3000071 1=5}</option>
        <option value='6291456'{if $level_info.level_event_album_storage == 6291456} SELECTED{/if}>{lang_sprintf id=3000071 1=6}</option>
        <option value='7340032'{if $level_info.level_event_album_storage == 7340032} SELECTED{/if}>{lang_sprintf id=3000071 1=7}</option>
        <option value='8388608'{if $level_info.level_event_album_storage == 8388608} SELECTED{/if}>{lang_sprintf id=3000071 1=8}</option>
        <option value='9437184'{if $level_info.level_event_album_storage == 9437184} SELECTED{/if}>{lang_sprintf id=3000071 1=9}</option>
        <option value='10485760'{if $level_info.level_event_album_storage == 10485760} SELECTED{/if}>{lang_sprintf id=3000071 1=10}</option>
        <option value='15728640'{if $level_info.level_event_album_storage == 15728640} SELECTED{/if}>{lang_sprintf id=3000071 1=15}</option>
        <option value='20971520'{if $level_info.level_event_album_storage == 20971520} SELECTED{/if}>{lang_sprintf id=3000071 1=20}</option>
        <option value='26214400'{if $level_info.level_event_album_storage == 26214400} SELECTED{/if}>{lang_sprintf id=3000071 1=25}</option>
        <option value='52428800'{if $level_info.level_event_album_storage == 52428800} SELECTED{/if}>{lang_sprintf id=3000071 1=50}</option>
        <option value='78643200'{if $level_info.level_event_album_storage == 78643200} SELECTED{/if}>{lang_sprintf id=3000071 1=75}</option>
        <option value='104857600'{if $level_info.level_event_album_storage == 104857600} SELECTED{/if}>{lang_sprintf id=3000071 1=100}</option>
        <option value='209715200'{if $level_info.level_event_album_storage == 209715200} SELECTED{/if}>{lang_sprintf id=3000071 1=200}</option>
        <option value='314572800'{if $level_info.level_event_album_storage == 314572800} SELECTED{/if}>{lang_sprintf id=3000071 1=300}</option>
        <option value='419430400'{if $level_info.level_event_album_storage == 419430400} SELECTED{/if}>{lang_sprintf id=3000071 1=400}</option>
        <option value='524288000'{if $level_info.level_event_album_storage == 524288000} SELECTED{/if}>{lang_sprintf id=3000071 1=500}</option>
        <option value='629145600'{if $level_info.level_event_album_storage == 629145600} SELECTED{/if}>{lang_sprintf id=3000071 1=600}</option>
        <option value='734003200'{if $level_info.level_event_album_storage == 734003200} SELECTED{/if}>{lang_sprintf id=3000071 1=700}</option>
        <option value='838860800'{if $level_info.level_event_album_storage == 838860800} SELECTED{/if}>{lang_sprintf id=3000071 1=800}</option>
        <option value='943718400'{if $level_info.level_event_album_storage == 943718400} SELECTED{/if}>{lang_sprintf id=3000071 1=900}</option>
        <option value='1073741824'{if $level_info.level_event_album_storage == 1073741824} SELECTED{/if}>{lang_sprintf id=3000072 1=1}</option>
        <option value='2147483648'{if $level_info.level_event_album_storage == 2147483648} SELECTED{/if}>{lang_sprintf id=3000072 1=2}</option>
        <option value='5368709120'{if $level_info.level_event_album_storage == 5368709120} SELECTED{/if}>{lang_sprintf id=3000072 1=5}</option>
        <option value='10737418240'{if $level_info.level_event_album_storage == 10737418240} SELECTED{/if}>{lang_sprintf id=3000072 1=10}</option>
        <option value='0'{if $level_info.level_event_album_storage == 0} SELECTED{/if}>{lang_print id=3000066}</option>
      </select>
      </td>
    </tr>
    
    <tr>
      <td class='setting1'>{lang_print id=3000067}</td>
    </tr>
    <tr>
      <td class='setting2'>
        <input type='text' class='text' size='5' name='level_event_album_maxsize' maxlength='6' value='{$level_event_album_maxsize}' /> {lang_sprintf id=3000070 1=''}
      </td>
    </tr>
    
    <tr>
      <td class='setting1'>{lang_print id=3000068}</td>
    </tr>
    <tr>
      <td class='setting2'>
        <table cellpadding='0' cellspacing='0'>
          <tr>
            <td>{lang_print id=3000043} &nbsp;</td>
            <td><input type='text' class='text' name='level_event_album_width' value='{$level_info.level_event_album_width}' maxlength='4' size='3' /> &nbsp;</td>
            <td>{lang_sprintf id=3000045 1=1 2=9999}</td>
          </tr>
          <tr>
            <td>{lang_print id=3000043} &nbsp;</td>
            <td><input type='text' class='text' name='level_event_album_height' value='{$level_info.level_event_album_height}' maxlength='4' size='3' /> &nbsp;</td>
            <td>{lang_sprintf id=3000045 1=1 2=9999}</td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <br />
  
  
  <table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=3000340}</td>
  </tr>
    <td class='setting1'>{lang_print id=3000341}</td>
  </tr>
  <tr>
    <td class='setting2'>
      <input type='text' class="text" size='75' name='level_event_html' value='{$level_info.level_event_html|replace:",":", "}' />
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
  <div style='height: 2500px;'>&nbsp;</div>
</td>
</tr>
</table>

{include file='admin_footer.tpl'}