{include file='admin_header.tpl'}

{* $Id: admin_levels_messagesettings.tpl 8 2009-01-11 06:02:53Z john $ *}

<h2>{lang_sprintf id=288 1=$level_info.level_name}</h2>
{lang_print id=282}

<table cellspacing='0' cellpadding='0' width='100%' style='margin-top: 20px;'>
<tr>
<td class='vert_tab0'>&nbsp;</td>
<td valign='top' class='pagecell' rowspan='{math equation="x+5" x=$level_menu|@count}'>

  <h2>{lang_print id=287}</h2>
  {lang_print id=330}

  <br><br>

  {if $result != 0}
  <div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=191}</div>
  {/if}

  <form action='admin_levels_messagesettings.php' method='POST'>
  <table cellpadding='0' cellspacing='0' width='100%'>
  <tr><td class='header'>{lang_print id=331}</td></tr>
  <tr><td class='setting1'>
  {lang_print id=332}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='radio' name='level_message_allow' id='message_allow_0' value='0'{if $level_info.level_message_allow == 0} CHECKED{/if}>&nbsp;</td><td><label for='message_allow_0'>{lang_print id=333}</label></td></tr>
    <tr><td><input type='radio' name='level_message_allow' id='message_allow_1' value='1'{if $level_info.level_message_allow == 1} CHECKED{/if}>&nbsp;</td><td><label for='message_allow_1'>{lang_print id=334}</label></td></tr>
    <tr><td><input type='radio' name='level_message_allow' id='message_allow_2' value='2'{if $level_info.level_message_allow == 2} CHECKED{/if}>&nbsp;</td><td><label for='message_allow_2'>{lang_print id=335}</label></td></tr>
    </table>
  </td></tr></table>

  <br>

  <table cellpadding='0' cellspacing='0' width='100%'>
  <tr><td class='header'>{lang_print id=336}</td></tr>
  <tr><td class='setting1'>
  {lang_print id=337}
  </td></tr><tr><td class='setting2'>
    <table cellpadding='0' cellspacing='0'>
    <tr>
    <td>
    <select name='level_message_inbox' class='text'>
    <option value='5'{if $level_info.level_message_inbox == 5} SELECTED{/if}>5</option>
    <option value='10'{if $level_info.level_message_inbox == 10} SELECTED{/if}>10</option>
    <option value='20'{if $level_info.level_message_inbox == 20} SELECTED{/if}>20</option>
    <option value='30'{if $level_info.level_message_inbox == 30} SELECTED{/if}>30</option>
    <option value='40'{if $level_info.level_message_inbox == 40} SELECTED{/if}>40</option>
    <option value='50'{if $level_info.level_message_inbox == 50} SELECTED{/if}>50</option>
    <option value='100'{if $level_info.level_message_inbox == 100} SELECTED{/if}>100</option>
    <option value='200'{if $level_info.level_message_inbox == 200} SELECTED{/if}>200</option>
    <option value='500'{if $level_info.level_message_inbox == 500} SELECTED{/if}>500</option>
    </select>
    </td>
    <td>&nbsp; {lang_print id=338}</td>
    </tr>
    <tr>
    <td>
    <select name='level_message_outbox' class='text'>
    <option value='5'{if $level_info.level_message_outbox == 5} SELECTED{/if}>5</option>
    <option value='10'{if $level_info.level_message_outbox == 10} SELECTED{/if}>10</option>
    <option value='20'{if $level_info.level_message_outbox == 20} SELECTED{/if}>20</option>
    <option value='30'{if $level_info.level_message_outbox == 30} SELECTED{/if}>30</option>
    <option value='40'{if $level_info.level_message_outbox == 40} SELECTED{/if}>40</option>
    <option value='50'{if $level_info.level_message_outbox == 50} SELECTED{/if}>50</option>
    <option value='100'{if $level_info.level_message_outbox == 100} SELECTED{/if}>100</option>
    <option value='200'{if $level_info.level_message_outbox == 200} SELECTED{/if}>200</option>
    <option value='500'{if $level_info.level_message_outbox == 500} SELECTED{/if}>500</option>
    </select>
    </td>
    <td>&nbsp; {lang_print id=339}</td>
    </tr>
    </table>
  </td></tr></table>

  <br>

  <table cellpadding='0' cellspacing='0' width='100%'>
  <tr><td class='header'>{lang_print id=792}</td></tr>
  <tr><td class='setting1'>
  {lang_print id=793}
  </td></tr><tr><td class='setting2'>
    <select name='level_message_recipients' class='text'>
    <option value='1'{if $level_info.level_message_recipients == 1} SELECTED{/if}>1</option>
    <option value='5'{if $level_info.level_message_recipients == 5} SELECTED{/if}>5</option>
    <option value='10'{if $level_info.level_message_recipients == 10} SELECTED{/if}>10</option>
    <option value='20'{if $level_info.level_message_recipients == 20} SELECTED{/if}>20</option>
    </select>
    &nbsp;{lang_print id=794}
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
<tr><td width='100' nowrap='nowrap' class='vert_tab' style='border-right: none; border-top: none;'><div style='width: 100px;'><a href='admin_levels_messagesettings.php?level_id={$level_info.level_id}'>{lang_print id=287}</a></div></td></tr>
{foreach from=$global_plugins key=plugin_k item=plugin_v}
{section name=level_page_loop loop=$plugin_v.plugin_pages_level}
  <tr><td width='100' nowrap='nowrap' class='vert_tab' style='border-top: none;{if $plugin_v.plugin_pages_level[level_page_loop].page == $page} border-right: none;{/if}'><div style='width: 100px;'><a href='{$plugin_v.plugin_pages_level[level_page_loop].link}?level_id={$level_info.level_id}'>{lang_print id=$plugin_v.plugin_pages_level[level_page_loop].title}</a></div></td></tr>
{/section}
{/foreach}

<tr>
<td class='vert_tab0'>
  <div style='height: 350px;'>&nbsp;</div>
</td>
</tr>
</table>





{include file='admin_footer.tpl'}