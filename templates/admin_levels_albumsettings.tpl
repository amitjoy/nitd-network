{include file='admin_header.tpl'}

{* $Id: admin_levels_albumsettings.tpl 16 2009-01-13 04:01:31Z john $ *}

<h2>{lang_sprintf id=288 1=$level_info.level_name}</h2>
{lang_print id=282}

<table cellspacing='0' cellpadding='0' width='100%' style='margin-top: 20px;'>
<tr>
<td class='vert_tab0'>&nbsp;</td>
<td valign='top' class='pagecell' rowspan='{math equation="x+5" x=$level_menu|@count}'>

  <h2>{lang_print id=1000006}</h2>
  {lang_print id=1000015}

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
  <form action='admin_levels_albumsettings.php' method='POST'>
  <tr><td class='header'>{lang_print id=1000016}</td></tr>
  <tr><td class='setting1'>
  {lang_print id=1000017}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='radio' name='level_album_allow' id='album_allow_1' value='1'{if $level_info.level_album_allow == 1} checked='checked'{/if}>&nbsp;</td><td><label for='album_allow_1'>{lang_print id=1000018}</label></td></tr>
    <tr><td><input type='radio' name='level_album_allow' id='album_allow_0' value='0'{if $level_info.level_album_allow == 0} checked='checked'{/if}>&nbsp;</td><td><label for='album_allow_0'>{lang_print id=1000019}</label></td></tr>
    </table>
  </td></tr></table>

  <br>

  <table cellpadding='0' cellspacing='0' width='600'>
  <tr><td class='header'>{lang_print id=1000020}</td></tr>
  <tr><td class='setting1'>
  <b>{lang_print id=297}</b><br>{lang_print id=1000021}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
      <tr><td><input type='radio' name='level_album_search' id='album_search_1' value='1'{if $level_info.level_album_search == 1} checked='checked'{/if}></td><td><label for='album_search_1'>{lang_print id=1000022}</label>&nbsp;&nbsp;</td></tr>
      <tr><td><input type='radio' name='level_album_search' id='album_search_0' value='0'{if $level_info.level_album_search == 0} checked='checked'{/if}></td><td><label for='album_search_0'>{lang_print id=1000023}</label>&nbsp;&nbsp;</td></tr>
    </table>
  </td></tr>
  <tr><td class='setting1'>
  <b>{lang_print id=1000024}</b><br>{lang_print id=1000025}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    {foreach from=$album_privacy key=k item=v}
      <tr><td><input type='checkbox' name='level_album_privacy[]' id='privacy_{$k}' value='{$k}'{if $k|in_array:$level_album_privacy} checked='checked'{/if}></td><td><label for='privacy_{$k}'>{lang_print id=$v}</label>&nbsp;&nbsp;</td></tr>
    {/foreach}
    </table>
  </td></tr>
  <tr><td class='setting1'>
  <b>{lang_print id=1000026}</b><br>{lang_print id=1000027}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    {foreach from=$album_comments key=k item=v}
      <tr><td><input type='checkbox' name='level_album_comments[]' id='comments_{$k}' value='{$k}'{if $k|in_array:$level_album_comments} checked='checked'{/if}></td><td><label for='comments_{$k}'>{lang_print id=$v}</label>&nbsp;&nbsp;</td></tr>
    {/foreach}
    </table>
  </td></tr>
  <tr><td class='setting1'>
  <b>{lang_print id=1000134}</b><br>{lang_print id=1000135}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    {foreach from=$album_tag key=k item=v}
      <tr><td><input type='checkbox' name='level_album_tag[]' id='tag_{$k}' value='{$k}'{if $k|in_array:$level_album_tag} checked='checked'{/if}></td><td><label for='tag_{$k}'>{lang_print id=$v}</label>&nbsp;&nbsp;</td></tr>
    {/foreach}
    </table>
  </td></tr>
  </table>
  
  <br>

  <table cellpadding='0' cellspacing='0' width='600'>
  <tr><td class='header'>{lang_print id=1000028}</td></tr>
  <tr><td class='setting1'>
  {lang_print id=1000029}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='text' name='level_album_maxnum' value='{$level_info.level_album_maxnum}' maxlength='3' size='5'>&nbsp;{lang_print id=1000030}</tr>
    </table>
  </td></tr></table>

  <br>

  <table cellpadding='0' cellspacing='0' width='600'>
  <tr><td class='header'>{lang_print id=1000031}</td></tr>
  <tr><td class='setting1'>
  {lang_print id=1000032}
  </td></tr><tr><td class='setting2'>
  <textarea name='level_album_exts' rows='2' cols='40' class='text' style='width: 100%;'>{$level_info.level_album_exts}</textarea>
  </td></tr></table>

  <br>

  <table cellpadding='0' cellspacing='0' width='600'>
  <tr><td class='header'>{lang_print id=1000033}</td></tr>
  <tr><td class='setting1'>
  {lang_print id=1000034}
  </td></tr><tr><td class='setting2'>
  <textarea name='level_album_mimes' rows='2' cols='40' class='text' style='width: 100%;'>{$level_info.level_album_mimes}</textarea>
  </td></tr></table>

  <br>

  <table cellpadding='0' cellspacing='0' width='600'>
  <tr><td class='header'>{lang_print id=1000035}</td></tr>
  <tr><td class='setting1'>
  {lang_print id=1000036}
  </td></tr><tr><td class='setting2'>
  <select name='level_album_storage' class='text'>
  <option value='102400'{if $level_info.level_album_storage == 102400} SELECTED{/if}>100 Kb</option>
  <option value='204800'{if $level_info.level_album_storage == 204800} SELECTED{/if}>200 Kb</option>
  <option value='512000'{if $level_info.level_album_storage == 512000} SELECTED{/if}>500 Kb</option>
  <option value='1048576'{if $level_info.level_album_storage == 1048576} SELECTED{/if}>1 MB</option>
  <option value='2097152'{if $level_info.level_album_storage == 2097152} SELECTED{/if}>2 MB</option>
  <option value='3145728'{if $level_info.level_album_storage == 3145728} SELECTED{/if}>3 MB</option>
  <option value='4194304'{if $level_info.level_album_storage == 4194304} SELECTED{/if}>4 MB</option>
  <option value='5242880'{if $level_info.level_album_storage == 5242880} SELECTED{/if}>5 MB</option>
  <option value='6291456'{if $level_info.level_album_storage == 6291456} SELECTED{/if}>6 MB</option>
  <option value='7340032'{if $level_info.level_album_storage == 7340032} SELECTED{/if}>7 MB</option>
  <option value='8388608'{if $level_info.level_album_storage == 8388608} SELECTED{/if}>8 MB</option>
  <option value='9437184'{if $level_info.level_album_storage == 9437184} SELECTED{/if}>9 MB</option>
  <option value='10485760'{if $level_info.level_album_storage == 10485760} SELECTED{/if}>10 MB</option>
  <option value='15728640'{if $level_info.level_album_storage == 15728640} SELECTED{/if}>15 MB</option>
  <option value='20971520'{if $level_info.level_album_storage == 20971520} SELECTED{/if}>20 MB</option>
  <option value='26214400'{if $level_info.level_album_storage == 26214400} SELECTED{/if}>25 MB</option>
  <option value='52428800'{if $level_info.level_album_storage == 52428800} SELECTED{/if}>50 MB</option>
  <option value='78643200'{if $level_info.level_album_storage == 78643200} SELECTED{/if}>75 MB</option>
  <option value='104857600'{if $level_info.level_album_storage == 104857600} SELECTED{/if}>100 MB</option>
  <option value='209715200'{if $level_info.level_album_storage == 209715200} SELECTED{/if}>200 MB</option>
  <option value='314572800'{if $level_info.level_album_storage == 314572800} SELECTED{/if}>300 MB</option>
  <option value='419430400'{if $level_info.level_album_storage == 419430400} SELECTED{/if}>400 MB</option>
  <option value='524288000'{if $level_info.level_album_storage == 524288000} SELECTED{/if}>500 MB</option>
  <option value='629145600'{if $level_info.level_album_storage == 629145600} SELECTED{/if}>600 MB</option>
  <option value='734003200'{if $level_info.level_album_storage == 734003200} SELECTED{/if}>700 MB</option>
  <option value='838860800'{if $level_info.level_album_storage == 838860800} SELECTED{/if}>800 MB</option>
  <option value='943718400'{if $level_info.level_album_storage == 943718400} SELECTED{/if}>900 MB</option>
  <option value='1073741824'{if $level_info.level_album_storage == 1073741824} SELECTED{/if}>1 GB</option>
  <option value='2147483648'{if $level_info.level_album_storage == 2147483648} SELECTED{/if}>2 GB</option>
  <option value='5368709120'{if $level_info.level_album_storage == 5368709120} SELECTED{/if}>5 GB</option>
  <option value='10737418240'{if $level_info.level_album_storage == 10737418240} SELECTED{/if}>10 GB</option>
  <option value='0'{if $level_info.level_album_storage == 0} SELECTED{/if}>{lang_print id=1000037}</option>
  </select>
  </td></tr></table>

  <br>

  <table cellpadding='0' cellspacing='0' width='600'>
  <tr><td class='header'>{lang_print id=1000038}</td></tr>
  <tr><td class='setting1'>
  {lang_print id=1000039}
  </td></tr><tr><td class='setting2'>
  <input type='text' class='text' size='5' name='level_album_maxsize' maxlength='6' value='{$level_info.level_album_maxsize}'> KB
  </td></tr>
  <tr><td class='setting1'>
  {lang_print id=1000040}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr>
    <td>{lang_print id=310} &nbsp;</td>
    <td><input type='text' class='text' name='level_album_width' value='{$level_info.level_album_width}' maxlength='4' size='3'> &nbsp;</td>
    <td>{lang_print id=311}</td>
    </tr>
    <tr>
    <td>{lang_print id=312} &nbsp;</td>
    <td><input type='text' class='text' name='level_album_height' value='{$level_info.level_album_height}' maxlength='4' size='3'> &nbsp;</td>
    <td>{lang_print id=312}</td>
    </tr>
    </table>
  </td></tr>
  </table>

  <br>

  <table cellpadding='0' cellspacing='0' width='600'>
  <tr><td class='header'>{lang_print id=1000041}</td></tr>
  <tr><td class='setting1'>
  {lang_print id=1000042}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='radio' name='level_album_style' id='album_style_1' value='1'{if $level_info.level_album_style == 1} checked='checked'{/if}>&nbsp;</td><td><label for='album_style_1'>{lang_print id=1000043}</label></td></tr>
    <tr><td><input type='radio' name='level_album_style' id='album_style_0' value='0'{if $level_info.level_album_style == 0} checked='checked'{/if}>&nbsp;</td><td><label for='album_style_0'>{lang_print id=1000044}</label></td></tr>
    </table>
  </td></tr></table>

  <br>

  <table cellpadding='0' cellspacing='0' width='600'>
  <tr><td class='header'>{lang_print id=1000106}</td></tr>
  <tr><td class='setting1'>
  {lang_print id=1000107}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='checkbox' name='level_album_profile[]' id='profile_tab' value='tab'{if "tab"|in_array:$level_album_profile} checked='checked'{/if}></td><td><label for='profile_tab'>{lang_print id=1000108}</label>&nbsp;&nbsp;</td></tr>
    <tr><td><input type='checkbox' name='level_album_profile[]' id='profile_side' value='side'{if "side"|in_array:$level_album_profile} checked='checked'{/if}></td><td><label for='profile_side'>{lang_print id=1000109}</label>&nbsp;&nbsp;</td></tr>
    </table>
  </td></tr></table>

  <br>
  
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