{include file='admin_header.tpl'}

{* $Id: admin_levels_edit.tpl 8 2009-01-11 06:02:53Z john $ *}

<h2>{lang_sprintf id=288 1=$level_info.level_name}</h2>
{lang_print id=282}


{* JAVASCRIPT FOR VALIDATION *}
{literal}
<script type="text/javascript">
<!-- 
function validate(form) {
  if(form.level_name.value == "") {
    if($('levelsuccess')) $('levelsuccess').style.display = 'none';
    $('levelerror').style.display = 'block';
    $('levelerror').innerHTML = "<img src='../images/error.gif' border='0' class='icon'> {/literal}{lang_print id=284}{literal}";
    return false;
  } else {
    return true;
  }
}

//-->
</script>
{/literal}

<table cellspacing='0' cellpadding='0' width='100%' style='margin-top: 20px;'>
<tr>
<td class='vert_tab0'>&nbsp;</td>
<td valign='top' class='pagecell' rowspan='{math equation="x+5" x=$level_menu|@count}'>

  <h2>{lang_print id=281}</h2>
  {lang_print id=283}
  <br />
  <br />

  {if $result != 0}
    <div class='success' id='levelsuccess'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=191}</div>
  {/if}

  <div class='error' id='levelerror' style='display:none;'></div>

  <form action='admin_levels_edit.php' method='POST' onsubmit='return validate(this);'>
  {lang_print id=258}<br>
  <input type='text' class='text' name='level_name' value='{$level_info.level_name}' size='40' maxlength='50'>
  <br><br>
  {lang_print id=277}<br>
  <textarea name='level_desc' rows='8' cols='60' class='text'>{$level_info.level_desc}</textarea>
  <br><br>
  <input type='submit' class='button' value='{lang_print id=173}'>
  <input type='hidden' name='level_id' value='{$level_info.level_id}'>
  <input type='hidden' name='task' value='editlevel'>
  </form>

</td>
</tr>

{* DISPLAY MENU *}
<tr><td width='100' nowrap='nowrap' class='vert_tab' style='border-right: none;'><div style='width: 100px;'><a href='admin_levels_edit.php?level_id={$level_info.level_id}'>{lang_print id=285}</a></div></td></tr>
<tr><td width='100' nowrap='nowrap' class='vert_tab' style='border-top: none;'><div style='width: 100px;'><a href='admin_levels_usersettings.php?level_id={$level_info.level_id}'>{lang_print id=286}</a></div></td></tr>
<tr><td width='100' nowrap='nowrap' class='vert_tab' style='border-top: none;'><div style='width: 100px;'><a href='admin_levels_messagesettings.php?level_id={$level_info.level_id}'>{lang_print id=287}</a></div></td></tr>
{foreach from=$global_plugins key=plugin_k item=plugin_v}
{section name=level_page_loop loop=$plugin_v.plugin_pages_level}
  <tr><td width='100' nowrap='nowrap' class='vert_tab' style='border-top: none;{if $plugin_v.plugin_pages_level[level_page_loop].page == $page} border-right: none;{/if}'><div style='width: 100px;'><a href='{$plugin_v.plugin_pages_level[level_page_loop].link}?level_id={$level_info.level_id}'>{lang_print id=$plugin_v.plugin_pages_level[level_page_loop].title}</a></div></td></tr>
{/section}
{/foreach}

<tr>
<td class='vert_tab0'>
  <div style='height: 250px;'>&nbsp;</div>
</td>
</tr>
</table>


{include file='admin_footer.tpl'}