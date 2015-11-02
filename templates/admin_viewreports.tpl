{include file='admin_header.tpl'}

{* $Id: admin_viewreports.tpl 8 2009-01-11 06:02:53Z john $ *}

<h2>{lang_print id=6}</h2>
{lang_print id=1100}
<br />
<br />

<table cellpadding='0' cellspacing='0' width='400' align='center'>
<tr>
<td align='center'>
<div class='box'>
  <table cellpadding='0' cellspacing='0' align='center'>
  <tr><form action='admin_viewreports.php' method='POST'>
  <td>
    {lang_print id=1101}<br>
    <select name='f_reason' class='text'>
    <option value=''{if $f_reason == ""} SELECTED{/if}></option>
    <option value='1'{if $f_reason == "1"} SELECTED{/if}>{lang_print id=860}</option>
    <option value='2'{if $f_reason == "2"} SELECTED{/if}>{lang_print id=861}</option>
    <option value='3'{if $f_reason == "3"} SELECTED{/if}>{lang_print id=862}</option>
    <option value='0'{if $f_reason == "0"} SELECTED{/if}>{lang_print id=863}</option>
    </select>&nbsp;
  </td>
  <td>{lang_print id=1102}<br><input type='text' class='text' name='f_details' value='{$f_details}' size='15' maxlength='50'>&nbsp;</td>
  <td><input type='submit' class='button' value='{lang_print id=1002}'></td>
  <input type='hidden' name='s' value='{$s}'>
  </form>
  </tr>
  </table>
</div>
</td></tr></table>

<br>

{if $total_reports == 0}

  <table cellpadding='0' cellspacing='0' width='400' align='center'>
  <tr>
  <td align='center'>
  <div class='box'><b>{lang_print id=1103}</b></div>
  </td></tr></table>
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
  // -->
  </script>
  {/literal}

  <div class='pages'>{lang_sprintf id=1104 1=$total_reports} &nbsp;|&nbsp; {lang_print id=1005} {section name=page_loop loop=$pages}{if $pages[page_loop].link == '1'}{$pages[page_loop].page}{else}<a href='admin_viewgroups.php?s={$s}&p={$pages[page_loop].page}&f_title={$f_title}&f_owner={$f_owner}'>{$pages[page_loop].page}</a>{/if} {/section}</div>

  <form action='admin_viewreports.php' method='post' name='items'>
  <table cellpadding='0' cellspacing='0' class='list' width='100%'>
  <tr>
  <td class='header' width='1'><input type='checkbox' name='select_all' onClick='javascript:doCheckAll()'></td>
  <td class='header' width='1' style='padding-left: 0px;'><a class='header' href='admin_viewreports.php?s={$i}&p={$p}&f_object={$f_object}&f_reason={$f_reason}&f_details={$f_details}'>{lang_print id=87}</a></td>
  <td class='header' width='5%'><a class='header' href='admin_viewreports.php?s={$u}&p={$p}&f_object={$f_object}&f_reason={$f_reason}&f_details={$f_details}'>{lang_print id=28}</a></td>
  <td class='header' width='75%'>{lang_print id=1102}</td>
  <td class='header' width='5%'>{lang_print id=1101}</td>
  <td class='header' width='5%'>{lang_print id=153}</td>
  </tr>
  {section name=report_loop loop=$reports}
    <tr class='{cycle values="background1,background2"}'>
    <td class='item' nowrap='nowrap' style='padding-right: 0px;'><input type='checkbox' name='delete[]' value='{$reports[report_loop].report_id}'></td>
    <td class='item' nowrap='nowrap' style='padding-left: 0px;'>{$reports[report_loop].report_id} &nbsp;</td>
    <td class='item' nowrap='nowrap'><a href='{$url->url_create("profile", $reports[report_loop].user_username)}' target='_blank'>{$reports[report_loop].user_username}</a> &nbsp;</td>
    <td class='item'>{$reports[report_loop].report_details} &nbsp;</td>
    <td class='item' nowrap='nowrap'>{if $reports[report_loop].report_reason == 1}{lang_print id=860}{elseif $reports[report_loop].report_reason == 2}{lang_print id=861}{elseif $reports[report_loop].report_reason == 3}{lang_print id=862}{else}{lang_print id=863}{/if}  &nbsp;</td>
    <td class='item' nowrap='nowrap'>
      <a href='admin_loginasuser.php?user_id={$reports[report_loop].report_user_id}&return_url={$reports[report_loop].report_url}' target='_blank'>{lang_print id=1105}</a> 
      | <a href='admin_viewreports.php?task=delete&report_id={$reports[report_loop].report_id}'>{lang_print id=155}</a>
    </td>
    </tr>
  {/section}
  </table>

  <table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
  <td>
    <br>
    <input type='submit' class='button' value='{lang_print id=788}'>
    <input type='hidden' name='task' value='delete_multi'>
    </form>
  </td>
  <td align='right' valign='top'>
    <div class='pages2'>{lang_sprintf id=1104 1=$total_reports} &nbsp;|&nbsp; {lang_print id=1005} {section name=page_loop loop=$pages}{if $pages[page_loop].link == '1'}{$pages[page_loop].page}{else}<a href='admin_viewgroups.php?s={$s}&p={$pages[page_loop].page}&f_title={$f_title}&f_owner={$f_owner}'>{$pages[page_loop].page}</a>{/if} {/section}</div>
  </td>
  </tr>
  </table>
{/if}


{include file='admin_footer.tpl'}