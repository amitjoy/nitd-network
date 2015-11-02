{include file='admin_header.tpl'}

{* $Id: admin_viewalbums.tpl 2 2009-01-10 20:53:09Z john $ *}

<h2>{lang_print id=1000004}</h2>
{lang_print id=1000045}

<br><br>

<table cellpadding='0' cellspacing='0' width='400' align='center'>
<tr>
<td align='center'>
<div class='box'>
<table cellpadding='0' cellspacing='0' align='center'>
<form action='admin_viewalbums.php' method='POST'>
<tr>
<td>{lang_print id=1000046}<br><input type='text' class='text' name='f_title' value='{$f_title}' size='15' maxlength='50'>&nbsp;</td>
<td>{lang_print id=1000047}<br><input type='text' class='text' name='f_owner' value='{$f_owner}' size='15' maxlength='50'>&nbsp;&nbsp;</td>
<td><input type='submit' class='button' value='{lang_print id=1002}'></td>
<input type='hidden' name='s' value='{$s}'>
</form>
</tr>
</table>
</div>
</td></tr></table>

<br>

{if $total_albums == 0}

  <table cellpadding='0' cellspacing='0' width='400' align='center'>
  <tr>
  <td align='center'>
    <div class='box' style='width: 300px;'><b>{lang_print id=1000048}</b></div>
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

  var album_id = 0;
  function confirmDelete(id) {
    album_id = id;
    TB_show('{/literal}{lang_print id=1000054}{literal}', '#TB_inline?height=150&width=300&inlineId=confirmdelete', '', '../images/trans.gif');
  }

  function deleteAlbum() {
    window.location = 'admin_viewalbums.php?task=deletealbum&album_id='+album_id+'&s={/literal}{$s}&p={$p}&f_title={$f_title}&f_owner={$f_owner}{literal}';
  }
  // -->
  </script>
  {/literal}

  {* HIDDEN DIV TO DISPLAY CONFIRMATION MESSAGE *}
  <div style='display: none;' id='confirmdelete'>
    <div style='margin-top: 10px;'>
      {lang_print id=1000053}
    </div>
    <br>
    <input type='button' class='button' value='{lang_print id=175}' onClick='parent.TB_remove();parent.deleteAlbum();'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
  </div>

  <div class='pages'>{lang_sprintf id=1000049 1=$total_albums} &nbsp;|&nbsp; {lang_print id=1005} {section name=page_loop loop=$pages}{if $pages[page_loop].link == '1'}{$pages[page_loop].page}{else}<a href='admin_viewalbums.php?s={$s}&p={$pages[page_loop].page}&f_title={$f_title}&f_owner={$f_owner}'>{$pages[page_loop].page}</a>{/if} {/section}</div>

  <form action='admin_viewalbums.php' method='post' name='items'>
  <table cellpadding='0' cellspacing='0' class='list'>
  <tr>
  <td class='header' width='10'><input type='checkbox' name='select_all' onClick='javascript:doCheckAll()'></td>
  <td class='header' width='10' style='padding-left: 0px;'><a class='header' href='admin_viewalbums.php?s={$i}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=87}</a></td>
  <td class='header'><a class='header' href='admin_viewalbums.php?s={$t}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=1000046}</a></td>
  <td class='header'><a class='header' href='admin_viewalbums.php?s={$u}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=1000047}</a></td>
  <td class='header' width='50' align='center'><a class='header' href='admin_viewalbums.php?s={$f}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=1000050}</a></td>
  <td class='header' width='80' align='center'><a class='header' href='admin_viewalbums.php?s={$su}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=1000051}</a></td>
  <td class='header' width='100'>{lang_print id=153}</td>
  </tr>
  {section name=album_loop loop=$albums}
    {assign var='album_url' value=$url->url_create('album', $albums[album_loop].album_author->user_info.user_username, $albums[album_loop].album_id)}
    <tr class='{cycle values="background1,background2"}'>
    <td class='item' style='padding-right: 0px;'><input type='checkbox' name='delete_album_{$albums[album_loop].album_id}' value='1'></td>
    <td class='item' style='padding-left: 0px;'>{$albums[album_loop].album_id}</td>
    <td class='item'>{if $albums[album_loop].album_title == ""}<i>{lang_print id=589}</i>{else}{$albums[album_loop].album_title}{/if}&nbsp;</td>
    <td class='item'><a href='{$url->url_create('profile', $albums[album_loop].album_author->user_info.user_username)}' target='_blank'>{$albums[album_loop].album_author->user_displayname}</a></td>
    <td class='item' align='center'>{$albums[album_loop].album_files}</td>
    <td class='item' align='center'>{$albums[album_loop].album_space} MB</td>
    <td class='item'>[ <a href='admin_loginasuser.php?user_id={$albums[album_loop].album_author->user_info.user_id}&return_url={$url->url_encode($album_url)}' target='_blank'>{lang_print id=1000052}</a> ] [ <a href="javascript:void(0);" onClick="confirmDelete('{$albums[album_loop].album_id}');">{lang_print id=155}</a> ]</td>
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
    <div class='pages2'>{lang_sprintf id=1000049 1=$total_albums} &nbsp;|&nbsp; {lang_print id=1005} {section name=page_loop loop=$pages}{if $pages[page_loop].link == '1'}{$pages[page_loop].page}{else}<a href='admin_viewalbums.php?s={$s}&p={$pages[page_loop].page}&f_title={$f_title}&f_owner={$f_owner}'>{$pages[page_loop].page}</a>{/if} {/section}</div>
  </td>
  </tr>
  </table>

{/if}

{include file='admin_footer.tpl'}