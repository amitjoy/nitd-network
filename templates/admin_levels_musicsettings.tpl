{include file='admin_header.tpl'}

<h2>{lang_sprintf id=288 1=$level_name}</h2>
{lang_print id=282}

<table cellspacing='0' cellpadding='0' width='100%' style='margin-top: 20px;'>
<tr>
<td class='vert_tab0'>&nbsp;</td>
<td valign='top' class='pagecell' rowspan='{math equation="x+5" x=$level_menu|@count}'>

  <h2>{lang_print id=4000001}</h2>
  {lang_print id=4000005}
  <br />
  <br />

  {if !empty($result)}
    <div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=191}</div>
  {/if}

  {if !empty($is_error)}
    <div class='error'><img src='../images/error.gif' class='icon' border='0'> {if is_numeric($is_error)}{lang_print id=$is_error}{else}{$is_error}{/if}</div>
  {/if}

  <form action='admin_levels_musicsettings.php' method='POST'>
  
  <table cellpadding='0' cellspacing='0' width='600'>
    <tr>
      <td class='header'>{lang_print id=4000006}</td>
    </tr>
    <tr>
      <td class='setting1'>{lang_print id=4000007}</td>
    </tr>
    <tr>
      <td class='setting2'>
        <table cellpadding='0' cellspacing='0'>
          <tr>
            <td><input type='radio' name='level_music_allow' id='music_allow_1' value='1'{if  $music_allow} CHECKED{/if}>&nbsp;</td>
            <td><label for='music_music_1'>{lang_print id=4000008}</label></td>
          </tr>
          <tr>
            <td><input type='radio' name='level_music_allow' id='music_allow_0' value='0'{if !$music_allow} CHECKED{/if}>&nbsp;</td>
            <td><label for='music_music_0'>{lang_print id=4000009}</label></td>
          </tr>
        </table>
    </td>
    </tr>
  </table>
  <br />
  
  
  <table cellpadding='0' cellspacing='0' width='600'>
    <tr>
      <td class='header'>{lang_print id=4000010}</td>
    </tr>
    <tr>
      <td class='setting1'>
      {lang_print id=4000011}
      </td>
    </tr>
    <tr>
      <td class='setting2'>
        <table cellpadding='0' cellspacing='0'>
          <tr>
            <td><input type='text' name='level_music_maxnum' value='{$music_maxnum}' maxlength='3' size='5'>
            &nbsp;{lang_print id=4000012}
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <br />
  
  
  <table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=4000013}</td>
  </tr>
  <tr>
    <td class='setting1'>{lang_print id=4000014}</td>
  </tr>
  <tr>
    <td class='setting2'>
      <textarea name='level_music_exts' rows='2' cols='40' class='text' style='width: 100%;'>{$music_exts_value}</textarea>
    </td>
  </tr>
  </table>
  <br />
  
  
  <table cellpadding='0' cellspacing='0' width='600'>
    <tr>
      <td class='header'>{lang_print id=4000015}</td>
    </tr>
    <tr>
      <td class='setting1'>{lang_print id=4000016}</td>
    </tr>
    <tr>
      <td class='setting2'>
        <textarea name='level_music_mimes' rows='2' cols='40' class='text' style='width: 100%;'>{$music_mimes_value}</textarea>
      </td>
    </tr>
  </table>
  <br />
  

  <table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=4000017}</td>
  </tr>
  <tr>
    <td class='setting1'>{lang_print id=4000018}</td>
  </tr>
  <tr>
    <td class='setting2'>
      <select name='level_music_storage' class='text'>
        <option value='102400'{if $music_storage == 102400} SELECTED{/if}>{lang_sprintf id=4000030 1=100}</option>
        <option value='204800'{if $music_storage == 204800} SELECTED{/if}>{lang_sprintf id=4000030 1=200}</option>
        <option value='512000'{if $music_storage == 512000} SELECTED{/if}>{lang_sprintf id=4000030 1=500}</option>
        <option value='1048576'{if $music_storage == 1048576} SELECTED{/if}>{lang_sprintf id=4000049 1=1}</option>
        <option value='2097152'{if $music_storage == 2097152} SELECTED{/if}>{lang_sprintf id=4000049 1=2}</option>
        <option value='3145728'{if $music_storage == 3145728} SELECTED{/if}>{lang_sprintf id=4000049 1=3}</option>
        <option value='4194304'{if $music_storage == 4194304} SELECTED{/if}>{lang_sprintf id=4000049 1=4}</option>
        <option value='5242880'{if $music_storage == 5242880} SELECTED{/if}>{lang_sprintf id=4000049 1=5}</option>
        <option value='6291456'{if $music_storage == 6291456} SELECTED{/if}>{lang_sprintf id=4000049 1=6}</option>
        <option value='7340032'{if $music_storage == 7340032} SELECTED{/if}>{lang_sprintf id=4000049 1=7}</option>
        <option value='8388608'{if $music_storage == 8388608} SELECTED{/if}>{lang_sprintf id=4000049 1=8}</option>
        <option value='9437184'{if $music_storage == 9437184} SELECTED{/if}>{lang_sprintf id=4000049 1=9}</option>
        <option value='10485760'{if $music_storage == 10485760} SELECTED{/if}>{lang_sprintf id=4000049 1=10}</option>
        <option value='15728640'{if $music_storage == 15728640} SELECTED{/if}>{lang_sprintf id=4000049 1=15}</option>
        <option value='20971520'{if $music_storage == 20971520} SELECTED{/if}>{lang_sprintf id=4000049 1=20}</option>
        <option value='26214400'{if $music_storage == 26214400} SELECTED{/if}>{lang_sprintf id=4000049 1=25}</option>
        <option value='52428800'{if $music_storage == 52428800} SELECTED{/if}>{lang_sprintf id=4000049 1=50}</option>
        <option value='78643200'{if $music_storage == 78643200} SELECTED{/if}>{lang_sprintf id=4000049 1=75}</option>
        <option value='104857600'{if $music_storage == 104857600} SELECTED{/if}>{lang_sprintf id=4000049 1=100}</option>
        <option value='209715200'{if $music_storage == 209715200} SELECTED{/if}>{lang_sprintf id=4000049 1=200}</option>
        <option value='314572800'{if $music_storage == 314572800} SELECTED{/if}>{lang_sprintf id=4000049 1=300}</option>
        <option value='419430400'{if $music_storage == 419430400} SELECTED{/if}>{lang_sprintf id=4000049 1=400}</option>
        <option value='524288000'{if $music_storage == 524288000} SELECTED{/if}>{lang_sprintf id=4000049 1=500}</option>
        <option value='629145600'{if $music_storage == 629145600} SELECTED{/if}>{lang_sprintf id=4000049 1=600}</option>
        <option value='734003200'{if $music_storage == 734003200} SELECTED{/if}>{lang_sprintf id=4000049 1=700}</option>
        <option value='838860800'{if $music_storage == 838860800} SELECTED{/if}>{lang_sprintf id=4000049 1=800}</option>
        <option value='943718400'{if $music_storage == 943718400} SELECTED{/if}>{lang_sprintf id=4000049 1=900}</option>
        <option value='1073741824'{if $music_storage == 1073741824} SELECTED{/if}>{lang_sprintf id=4000050 1=1}</option>
        <option value='2147483648'{if $music_storage == 2147483648} SELECTED{/if}>{lang_sprintf id=4000050 1=2}</option>
        <option value='5368709120'{if $music_storage == 5368709120} SELECTED{/if}>{lang_sprintf id=4000050 1=5}</option>
        <option value='10737418240'{if $music_storage == 10737418240} SELECTED{/if}>{lang_sprintf id=4000050 1=10}</option>
        <option value='0'{if $music_storage == 0} SELECTED{/if}> </option>
      </select>
    </td>
  </tr>
  </table>
  <br />


  <table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=4000019}</td>
  </tr>
  <tr>
    <td class='setting1'>{lang_print id=4000020}</td>
  </tr>
  <tr>
    <td class='setting2'>
      <input type='text' class='text' size='5' name='level_music_maxsize' maxlength='6' value='{$music_maxsize}'> {lang_sprintf id=4000030 1=''}
    </td>
  </tr>
  </table>
  <br />
  
  
  <table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=4000024}</td>
  </tr>
  <tr>
    <td class='setting1'>{lang_print id=4000025}</td>
  </tr>
  <tr>
    <td class='setting2'>
      <table cellpadding='0' cellspacing='0'>
        <tr>
          <td><input type='radio' name='level_music_allow_skins' id='music_allow_1_skins' value='1'{if  $music_allow_skins} CHECKED{/if}>&nbsp;</td>
          <td><label for='music_allow_1_skins'>{lang_print id=4000026}</label></td>
        </tr>
        <tr>
          <td><input type='radio' name='level_music_allow_skins' id='music_allow_0_skins' value='0'{if !$music_allow_skins} CHECKED{/if}>&nbsp;</td>
          <td><label for='music_allow_0_skins'>{lang_print id=4000027}</label></td>
        </tr>
      </table>  
    </td>
  </tr>
  <tr>
    <td class='setting1'>{lang_print id=4000028}</td>
  </tr>
  <tr>
    <td class='setting2'>
      <table cellpadding='0' cellspacing='0'>
        <tr>
          <td>
            <select name='level_music_skin_default' class='text'>
            {section name=skin_loop loop=$music_skins}
            <option value='{$music_skins[skin_loop].xspfskin_id}'{if $music_skins[skin_loop].xspfskin_id == $music_skin_default} SELECTED{/if}>{$music_skins[skin_loop].xspfskin_title}</option>
            {/section}
            </select>
          </td>
        </tr>
      </table>
    </td>
  </tr>  
  </table>
  <br />
  
  
  <table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=4000091}</td>
  </tr>
  <tr>
    <td class='setting1'>{lang_print id=4000092}</td>
  </tr>
  <tr>
    <td class='setting2'>
      <table cellpadding='0' cellspacing='0'>
        <tr>
          <td><input type='radio' name='level_music_allow_downloads' id='music_allow_1_downloads' value='1'{if  $music_allow_downloads} CHECKED{/if}>&nbsp;</td>
          <td><label for='music_allow_1_downloads'>{lang_print id=4000093}</label></td>
        </tr>
        <tr>
          <td><input type='radio' name='level_music_allow_downloads' id='music_allow_0_downloads' value='0'{if !$music_allow_downloads} CHECKED{/if}>&nbsp;</td>
          <td><label for='music_allow_0_downloads'>{lang_print id=4000094}</label></td>
        </tr>
      </table>  
    </td>
  </tr>
  </table>
  <br />
  
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
{section name=level_plugin_loop loop=$global_plugins}
{section name=level_page_loop loop=$global_plugins[level_plugin_loop].plugin_pages_level}
  <tr><td width='100' nowrap='nowrap' class='vert_tab' style='border-top: none;{if $global_plugins[level_plugin_loop].plugin_pages_level[level_page_loop].page == $page} border-right: none;{/if}'><div style='width: 100px;'><a href='{$global_plugins[level_plugin_loop].plugin_pages_level[level_page_loop].link}?level_id={$level_id}'>{lang_print id=$global_plugins[level_plugin_loop].plugin_pages_level[level_page_loop].title}</a></div></td></tr>
{/section}
{/section}

<tr>
<td class='vert_tab0'>
  <div style='height: 800px;'>&nbsp;</div>
</td>
</tr>
</table>

{include file='admin_footer.tpl'}