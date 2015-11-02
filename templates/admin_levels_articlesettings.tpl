{include file='admin_header.tpl'}

<h2>{lang_print id=11150323} {$level_name}</h2>
{lang_print id=11150324}

<table cellspacing='0' cellpadding='0' width='100%' style='margin-top: 20px;'>
<tr>
<td class='vert_tab0'>&nbsp;</td>
<td valign='top' class='pagecell' rowspan='{math equation='x+5' x=$level_menu|@count}'>


  <h2>{lang_print id=11150301}</h2>
  {lang_print id=11150302}

  <br><br>

  {if $result != 0}
    <div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=11150308}</div>
  {/if}

  {if $is_error != 0}
    <div class='error'><img src='../images/error.gif' class='icon' border='0'> {$error_message}</div>
  {/if}

  <form action='admin_levels_articlesettings.php' method='POST'>
  <table cellpadding='0' cellspacing='0' width='600'>
  <tr><td class='header'>{lang_print id=11150303}</td></tr>
  <tr><td class='setting1'>
  {lang_print id=11150304}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='radio' name='level_article_allow' id='article_allow_1' value='1'{if $article_allow == 1} CHECKED{/if}>&nbsp;</td><td><label for='article_allow_1'>{lang_print id=11150305}</label></td></tr>
    <tr><td><input type='radio' name='level_article_allow' id='article_allow_0' value='0'{if $article_allow == 0} CHECKED{/if}>&nbsp;</td><td><label for='article_allow_0'>{lang_print id=11150306}</label></td></tr>
    </table>
  </td></tr></table>

  <br>

  <table cellpadding='0' cellspacing='0' width='600'>
  <tr><td class='header'>{lang_print id=11150309}</td></tr>
  <td class='setting1'>
  {lang_print id=11150310}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='radio' name='level_article_photo' id='article_photo_1' value='1'{if $article_photo == 1} CHECKED{/if}>&nbsp;</td><td><label for='article_photo_1'>{lang_print id=11150311}</label></td></tr>
    <tr><td><input type='radio' name='level_article_photo' id='article_photo_0' value='0'{if $article_photo == 0} CHECKED{/if}>&nbsp;</td><td><label for='article_photo_0'>{lang_print id=11150312}</label></td></tr>
    </table>
  </td></tr>
  <tr>
  <td class='setting1'>
  {lang_print id=11150313}
  </td>
  </tr>
  <tr>
  <td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr>
    <td>{lang_print id=11150314} &nbsp;</td>
    <td><input type='text' class='text' name='level_article_photo_width' value='{$article_photo_width}' maxlength='3' size='3'> &nbsp;</td>
    <td>{lang_print id=11150315}</td>
    </tr>
    <tr>
    <td>{lang_print id=11150316} &nbsp;</td>
    <td><input type='text' class='text' name='level_article_photo_height' value='{$article_photo_height}' maxlength='3' size='3'> &nbsp;</td>
    <td>{lang_print id=11150315}</td>
    </tr>
    </table>
  </td>
  </tr>
  <tr>
  <td class='setting1'>
  {lang_print id=11150317}
  </td>
  </tr>
  <tr>
  <td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr>
    <td>{lang_print id=11150318} &nbsp;</td>
    <td><input type='text' class='text' name='level_article_photo_exts' value='{$article_photo_exts}' size='40' maxlength='50'></td>
    </tr>
    </table>
  </td>
  </tr>
  </table>
  
  <br>

  <table cellpadding='0' cellspacing='0' width='600'>
  <tr><td class='header'>{lang_print id=11150322}</td></tr>
  <tr><td class='setting1'>
  {lang_print id=11150319}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
      <tr><td><input type='radio' name='level_article_search' id='article_search_1' value='1'{if $article_search == 1} CHECKED{/if}></td><td><label for='article_search_1'>{lang_print id=11150320}</label>&nbsp;&nbsp;</td></tr>
      <tr><td><input type='radio' name='level_article_search' id='article_search_0' value='0'{if $article_search == 0} CHECKED{/if}></td><td><label for='article_search_0'>{lang_print id=11150321}</label>&nbsp;&nbsp;</td></tr>
    </table>
  </td></tr>
  <tr><td class='setting1'>
  {lang_print id=11150325}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    {foreach from=$article_privacy key=k item=v}
      <tr><td><input type='checkbox' name='level_article_privacy[]' id='privacy_{$k}' value='{$k}'{if $k|in_array:$level_article_privacy} checked='checked'{/if}></td><td><label for='privacy_{$k}'>{lang_print id=$v}</label>&nbsp;&nbsp;</td></tr>
    {/foreach}
    </table>
  </td></tr>
  <tr><td class='setting1'>
  {lang_print id=11150326}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    {foreach from=$article_comments key=k item=v}
      <tr><td><input type='checkbox' name='level_article_comments[]' id='comments_{$k}' value='{$k}'{if $k|in_array:$level_article_comments} checked='checked'{/if}></td><td><label for='comments_{$k}'>{lang_print id=$v}</label>&nbsp;&nbsp;</td></tr>
    {/foreach}
    </table>
  </td></tr>
  </table>
  
  {* REMOVED NO NEED FOR ARTICLE
  <br>

  <table cellpadding='0' cellspacing='0' width='600'>
  <tr>
  <td class='header'>{lang_print id=11150327}</td>
  </tr>
  <td class='setting1'>
  {lang_print id=11150328}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='radio' name='level_article_inviteonly' id='article_inviteonly_1' value='1'{if $article_inviteonly == 1} CHECKED{/if}>&nbsp;</td><td><label for='article_inviteonly_1'>{lang_print id=11150329}</label></td></tr>
    <tr><td><input type='radio' name='level_article_inviteonly' id='article_inviteonly_0' value='0'{if $article_inviteonly == 0} CHECKED{/if}>&nbsp;</td><td><label for='article_inviteonly_0'>{lang_print id=11150330}</label></td></tr>
    </table>
  </td></tr></table>
  *}
  
  
  <br>

  <table cellpadding='0' cellspacing='0' width='600'>
  <tr>
  <td class='header'>{lang_print id=11150331}</td>
  </tr>
  <td class='setting1'>
  {lang_print id=11150332}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='radio' name='level_article_approved' id='article_approved_1' value='1'{if $article_approved == 1} CHECKED{/if}>&nbsp;</td><td><label for='article_approved_1'>{lang_print id=11150333}</label></td></tr>
    <tr><td><input type='radio' name='level_article_approved' id='article_approved_0' value='0'{if $article_approved == 0} CHECKED{/if}>&nbsp;</td><td><label for='article_approved_0'>{lang_print id=11150334}</label></td></tr>
    </table>
  </td></tr></table>
  
  <br>

  <table cellpadding='0' cellspacing='0' width='600'>
  <tr><td class='header'>{lang_print id=11150335}</td></tr>
  <tr><td class='setting1'>
  {lang_print id=11150336}
  </td></tr><tr><td class='setting2'>
  <textarea name='level_article_album_exts' rows='2' cols='40' class='text' style='width: 100%;'>{$article_album_exts}</textarea>
  </td></tr>
  <tr><td class='setting1'>
  {lang_print id=11150337}
  </td></tr><tr><td class='setting2'>
  <textarea name='level_article_album_mimes' rows='2' cols='40' class='text' style='width: 100%;'>{$article_album_mimes}</textarea>
  </td></tr>
  <tr><td class='setting1'>
  {lang_print id=11150338}
  </td></tr><tr><td class='setting2'>
  <select name='level_article_album_storage' class='text'>
  <option value='102400'{if $article_album_storage == 102400} SELECTED{/if}>100 Kb</option>
  <option value='204800'{if $article_album_storage == 204800} SELECTED{/if}>200 Kb</option>
  <option value='512000'{if $article_album_storage == 512000} SELECTED{/if}>500 Kb</option>
  <option value='1048576'{if $article_album_storage == 1048576} SELECTED{/if}>1 MB</option>
  <option value='2097152'{if $article_album_storage == 2097152} SELECTED{/if}>2 MB</option>
  <option value='3145728'{if $article_album_storage == 3145728} SELECTED{/if}>3 MB</option>
  <option value='4194304'{if $article_album_storage == 4194304} SELECTED{/if}>4 MB</option>
  <option value='5242880'{if $article_album_storage == 5242880} SELECTED{/if}>5 MB</option>
  <option value='6291456'{if $article_album_storage == 6291456} SELECTED{/if}>6 MB</option>
  <option value='7340032'{if $article_album_storage == 7340032} SELECTED{/if}>7 MB</option>
  <option value='8388608'{if $article_album_storage == 8388608} SELECTED{/if}>8 MB</option>
  <option value='9437184'{if $article_album_storage == 9437184} SELECTED{/if}>9 MB</option>
  <option value='10485760'{if $article_album_storage == 10485760} SELECTED{/if}>10 MB</option>
  <option value='15728640'{if $article_album_storage == 15728640} SELECTED{/if}>15 MB</option>
  <option value='20971520'{if $article_album_storage == 20971520} SELECTED{/if}>20 MB</option>
  <option value='26214400'{if $article_album_storage == 26214400} SELECTED{/if}>25 MB</option>
  <option value='52428800'{if $article_album_storage == 52428800} SELECTED{/if}>50 MB</option>
  <option value='78643200'{if $article_album_storage == 78643200} SELECTED{/if}>75 MB</option>
  <option value='104857600'{if $article_album_storage == 104857600} SELECTED{/if}>100 MB</option>
  <option value='209715200'{if $article_album_storage == 209715200} SELECTED{/if}>200 MB</option>
  <option value='314572800'{if $article_album_storage == 314572800} SELECTED{/if}>300 MB</option>
  <option value='419430400'{if $article_album_storage == 419430400} SELECTED{/if}>400 MB</option>
  <option value='524288000'{if $article_album_storage == 524288000} SELECTED{/if}>500 MB</option>
  <option value='629145600'{if $article_album_storage == 629145600} SELECTED{/if}>600 MB</option>
  <option value='734003200'{if $article_album_storage == 734003200} SELECTED{/if}>700 MB</option>
  <option value='838860800'{if $article_album_storage == 838860800} SELECTED{/if}>800 MB</option>
  <option value='943718400'{if $article_album_storage == 943718400} SELECTED{/if}>900 MB</option>
  <option value='1073741824'{if $article_album_storage == 1073741824} SELECTED{/if}>1 GB</option>
  <option value='2147483648'{if $article_album_storage == 2147483648} SELECTED{/if}>2 GB</option>
  <option value='5368709120'{if $article_album_storage == 5368709120} SELECTED{/if}>5 GB</option>
  <option value='10737418240'{if $article_album_storage == 10737418240} SELECTED{/if}>10 GB</option>
  <option value='0'{if $article_album_storage == 0} SELECTED{/if}>{lang_print id=11150339}</option>
  </select>
  </td></tr>
  <tr><td class='setting1'>
  {lang_print id=11150340}
  </td></tr><tr><td class='setting2'>
  <input type='text' class='text' size='5' name='level_article_album_maxsize' maxlength='6' value='{$article_album_maxsize}'> KB
  </td></tr>
  <tr><td class='setting1'>
  {lang_print id=11150341}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr>
    <td>{lang_print id=11150342} &nbsp;</td>
    <td><input type='text' class='text' name='level_article_album_width' value='{$article_album_width}' maxlength='4' size='3'> &nbsp;</td>
    <td>{lang_print id=11150344}</td>
    </tr>
    <tr>
    <td>{lang_print id=11150343} &nbsp;</td>
    <td><input type='text' class='text' name='level_article_album_height' value='{$article_album_height}' maxlength='4' size='3'> &nbsp;</td>
    <td>{lang_print id=11150344}</td>
    </tr>
    </table>
  </td></tr>
  </table>
  
  <br>

  <input type='submit' class='button' value='{lang_print id=11150307}'>
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