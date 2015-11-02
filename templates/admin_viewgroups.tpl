{include file='admin_header.tpl'}

{* $Id: admin_viewgroups.tpl 10 2009-01-11 06:03:42Z john $ *}

<h2>{lang_print id=2000084}</h2>
{lang_print id=2000085}
<br />
<br />

<table cellpadding='0' cellspacing='0' width='400' align='center'>
<tr>
<td align='center'>
<div class='box'>
<table cellpadding='0' cellspacing='0' align='center'>
<tr><form action='admin_viewgroups.php' method='POST'>
<td>{lang_print id=2000094}<br><input type='text' class='text' name='f_title' value='{$f_title}' size='15' maxlength='100'>&nbsp;</td>
<td>{lang_print id=2000086}<br><input type='text' class='text' name='f_owner' value='{$f_owner}' size='15' maxlength='50'>&nbsp;</td>
<td><input type='submit' class='button' value='{lang_print id=1002}'></td>
<input type='hidden' name='s' value='{$s}'>
</form>
</tr>
</table>
</div>
</td></tr></table>

<br />

{if $total_groups == 0}

  <table cellpadding='0' cellspacing='0' width='400' align='center'>
  <tr>
  <td align='center'>
    <div class='box' style='width: 300px;'><b>{lang_print id=2000087}</b></div>
  </td>
  </tr>
  </table>
  <br>

{else}

  {* JAVASCRIPT FOR CHECK ALL *}
  {literal}
  <script language='JavaScript'> 
  <!---
  var checkboxcount = 1;
  function doCheckAll() {
    if(checkboxcount == 0) {
      with (document.items) {
      for (var i=0; i < elements.length; i++) {
      if (elements[i].type == 'checkbox') {
      elements[i].checked = false;
      }}
      checkboxcount = checkboxcount + 1;
      }
    } else
      with (document.items) {
      for (var i=0; i < elements.length; i++) {
      if (elements[i].type == 'checkbox') {
      elements[i].checked = true;
      }}
      checkboxcount = checkboxcount - 1;
      }
  }

  var group_id = 0;
  function confirmDelete(id) {
    group_id = id;
    TB_show('{/literal}{lang_print id=2000092}{literal}', '#TB_inline?height=150&width=300&inlineId=confirmdelete', '', '../images/trans.gif');
  }

  function deleteGroup() {
    window.location = 'admin_viewgroups.php?task=deletegroup&group_id='+group_id+'&s={/literal}{$s}&p={$p}&f_title={$f_title}&f_owner={$f_owner}{literal}';
  }
  // -->
  </script>
  {/literal}

  {* HIDDEN DIV TO DISPLAY CONFIRMATION MESSAGE *}
  <div style='display: none;' id='confirmdelete'>
    <div style='margin-top: 10px;'>
      {lang_print id=2000093}
    </div>
    <br>
    <input type='button' class='button' value='{lang_print id=175}' onClick='parent.TB_remove();parent.deleteGroup();'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
  </div>
  <div class='pages'>{lang_sprintf id=2000088 1=$total_groups} &nbsp;|&nbsp; {lang_print id=1005} {section name=page_loop loop=$pages}{if $pages[page_loop].link == '1'}{$pages[page_loop].page}{else}<a href='admin_viewgroups.php?s={$s}&p={$pages[page_loop].page}&f_title={$f_title}&f_owner={$f_owner}'>{$pages[page_loop].page}</a>{/if} {/section}</div>
  
  <form action='admin_viewgroups.php' method='post' name='items'>
  <table cellpadding='0' cellspacing='0' class='list'>
  <tr>
  <td class='header' width='10'><input type='checkbox' name='select_all' onClick='javascript:doCheckAll()'></td>
  <td class='header' width='10' style='padding-left: 0px;'><a class='header' href='admin_viewgroups.php?s={$i}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=87}</a></td>
  <td class='header'><a class='header' href='admin_viewgroups.php?s={$t}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=2000094}</a></td>
  <td class='header'><a class='header' href='admin_viewgroups.php?s={$o}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=2000086}</a></td>
  <td class='header' width='1'><a class='header' href='admin_viewgroups.php?s={$m}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=2000089}</a></td>
  <td class='header' width='100'><a class='header' href='admin_viewgroups.php?s={$d}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=2000090}</a></td>
  <td class='header' width='100'>{lang_print id=153}</td>
  </tr>
  {section name=group_loop loop=$groups}
    <tr class='{cycle values="background1,background2"}'>
    <td class='item' style='padding-right: 0px;'><input type='checkbox' name='delete_group_{$groups[group_loop].group->group_info.group_id}' value='1'></td>
    <td class='item' style='padding-left: 0px;'>{$groups[group_loop].group->group_info.group_id}</td>
    <td class='item'>{$groups[group_loop].group->group_info.group_title}</td>
    <td class='item'><a href='{$url->url_create('profile', $groups[group_loop].group_leader->user_info.user_username)}' target='_blank'>{$groups[group_loop].group_leader->user_displayname}</a></td>
    <td class='item' align='center'>{$groups[group_loop].group_members}</td>
    <td class='item'>{assign var=group_datecreated value=$groups[group_loop].group->group_info.group_datecreated}{$datetime->cdate($setting.setting_dateformat, $datetime->timezone($group_datecreated, $setting.setting_timezone))}</td>
    <td class='item'>[ <a href='admin_loginasuser.php?user_id={$groups[group_loop].group->group_info.group_user_id}&return_url={$url->url_encode("`$url->url_base`group.php?group_id=`$groups[group_loop].group->group_info.group_id`")}' target='_blank'>{lang_print id=2000091}</a> ] [ <a href='javascript:void(0);' onclick="confirmDelete('{$groups[group_loop].group->group_info.group_id}');">{lang_print id=155}</a> ]</td>
    </tr>
  {/section}
  </table>

  <table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
  <td>
    <br>
    <input type='submit' class='button' value='{lang_print id=788}'>
    <input type='hidden' name='task' value='delete'>
    <input type='hidden' name='s' value='{$s}'>
    <input type='hidden' name='p' value='{$p}'>
    <input type='hidden' name='f_title' value='{$f_title}'>
    <input type='hidden' name='f_owner' value='{$f_owner}'>
    </form>
  </td>
  <td align='right' valign='top'>
    <div class='pages2'>{lang_sprintf id=2000088 1=$total_groups} &nbsp;|&nbsp; {lang_print id=1005} {section name=page_loop loop=$pages}{if $pages[page_loop].link == '1'}{$pages[page_loop].page}{else}<a href='admin_viewgroups.php?s={$s}&p={$pages[page_loop].page}&f_title={$f_title}&f_owner={$f_owner}'>{$pages[page_loop].page}</a>{/if} {/section}</div>
  </td>
  </tr>
  </table>

{/if}

{include file='admin_footer.tpl'}