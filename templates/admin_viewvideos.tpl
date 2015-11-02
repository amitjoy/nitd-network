{include file='admin_header.tpl'}

{* $Id: admin_viewvideos.tpl 13 2009-01-11 06:04:29Z john $ *}

<h2>{lang_print id=5500085}</h2>
{lang_print id=5500086}
<br />
<br />

<table cellpadding='0' cellspacing='0' width='400' align='center'>
<tr>
<td align='center'>
<div class='box'>
<table cellpadding='0' cellspacing='0' align='center'>
<form action='admin_viewvideos.php' method='POST'>
<tr>
<td>{lang_print id=5500087}<br><input type='text' class='text' name='f_title' value='{$f_title}' size='15' maxlength='50'>&nbsp;</td>
<td>{lang_print id=5500088}<br><input type='text' class='text' name='f_owner' value='{$f_owner}' size='15' maxlength='50'>&nbsp;&nbsp;</td>
<td><input type='submit' class='button' value='{lang_print id=1002}'></td>
<input type='hidden' name='s' value='{$s}'>
</form>
</tr>
</table>
</div>
</td></tr></table>

<br>

{if $total_videos == 0}

  <table cellpadding='0' cellspacing='0' width='400' align='center'>
  <tr>
  <td align='center'>
    <div class='box' style='width: 300px;'><b>{lang_print id=5500089}</b></div>
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

  var video_id = 0;
  function confirmDelete(id) {
    video_id = id;
    TB_show('{/literal}{lang_print id=5500145}{literal}', '#TB_inline?height=150&width=300&inlineId=confirmdelete', '', '../images/trans.gif');
  }

  function deleteVideo() {
    window.location = 'admin_viewvideos.php?task=deletevideo&video_id='+video_id+'&s={/literal}{$s}&p={$p}&f_title={$f_title}&f_owner={$f_owner}{literal}';
  }
  // -->
  </script>
  {/literal}

  {* HIDDEN DIV TO DISPLAY CONFIRMATION MESSAGE *}
  <div style='display: none;' id='confirmdelete'>
    <div style='margin-top: 10px;'>
      {lang_print id=5500146}
    </div>
    <br>
    <input type='button' class='button' value='{lang_print id=175}' onClick='parent.TB_remove();parent.deleteVideo();'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
  </div>

  <div class='pages'>{lang_sprintf id=5500090 1=$total_videos} &nbsp;|&nbsp; {lang_print id=1005} {section name=page_loop loop=$pages}{if $pages[page_loop].link == '1'}{$pages[page_loop].page}{else}<a href='admin_viewvideos.php?s={$s}&p={$pages[page_loop].page}&f_title={$f_title}&f_owner={$f_owner}'>{$pages[page_loop].page}</a>{/if} {/section}</div>

  <form action='admin_viewvideos.php' method='post' name='items'>
  <table cellpadding='0' cellspacing='0' class='list'>
  <tr>
  <td class='header' width='10'><input type='checkbox' name='select_all' onClick='javascript:doCheckAll()'></td>
  <td class='header' width='10' style='padding-left: 0px;'><a class='header' href='admin_viewvideos.php?s={$i}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=87}</a></td>
  <td class='header'><a class='header' href='admin_viewvideos.php?s={$t}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=5500087}</a></td>
  <td class='header'><a class='header' href='admin_viewvideos.php?s={$u}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=5500088}</a></td>
  <td class='header' width='100'>{lang_print id=153}</td>
  </tr>
  {section name=video_loop loop=$videos}
    {assign var='video_url' value=$url->url_create('video', $videos[video_loop].video_author->user_info.user_username, $videos[video_loop].video_id)}
    <tr class='{cycle values="background1,background2"}'>
    <td class='item' style='padding-right: 0px;'><input type='checkbox' name='delete_video_{$videos[video_loop].video_id}' value='1'></td>
    <td class='item' style='padding-left: 0px;'>{$videos[video_loop].video_id}</td>
    <td class='item'>{if $videos[video_loop].video_title == ""}<i>{lang_print id=589}</i>{else}{$videos[video_loop].video_title}{/if}&nbsp;</td>
    <td class='item'><a href='{$url->url_create('profile', $videos[video_loop].video_author->user_info.user_username)}' target='_blank'>{$videos[video_loop].video_author->user_displayname}</a></td>
    <td class='item'>[ <a href='admin_loginasuser.php?user_id={$videos[video_loop].video_author->user_info.user_id}&return_url={$url->url_encode($video_url)}' target='_blank'>{lang_print id=5500091}</a> ] [ <a href="javascript:void(0);" onClick="confirmDelete('{$videos[video_loop].video_id}');">{lang_print id=155}</a> ]</td>
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
    <div class='pages2'>{lang_sprintf id=5500090 1=$total_videos} &nbsp;|&nbsp; {lang_print id=1005} {section name=page_loop loop=$pages}{if $pages[page_loop].link == '1'}{$pages[page_loop].page}{else}<a href='admin_viewvideos.php?s={$s}&p={$pages[page_loop].page}&f_title={$f_title}&f_owner={$f_owner}'>{$pages[page_loop].page}</a>{/if} {/section}</div>
  </td>
  </tr>
  </table>

{/if}

{include file='admin_footer.tpl'}