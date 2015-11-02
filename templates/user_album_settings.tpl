{include file='header.tpl'}

{* $Id: user_album_settings.tpl 2 2009-01-10 20:53:09Z john $ *}

<table cellpadding='0' cellspacing='0' width='100%'>
<tr>
<td valign='top'>

  <img src='./images/icons/album_image48.gif' border='0' class='icon_big'>
  <div class='page_header'>{lang_print id=1000056}</div>
  <div>{lang_print id=1000111}</div>

</td>
<td valign='top' align='right'>

  <table cellpadding='0' cellspacing='0' width='130'>
  <tr><td class='button' nowrap='nowrap'><a href='user_album.php'><img src='./images/icons/back16.gif' border='0' class='button'>{lang_print id=1000097}</a></td></tr>
  </table>

</td>
</tr>
</table>

<br>

{* SHOW SUCCESS MESSAGE *}
{if $result != 0}
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td class='success'><img src='./images/success.gif' border='0' class='icon'> {lang_print id=191}</td>
  </tr>
  </table><br>
{/if}

<form action='user_album_settings.php' method='post'>

{if $user->level_info.level_album_style == 1}
  <div><b>{lang_print id=1000112}</b></div>
  <div class='form_desc'>{lang_print id=1000113}</div>
  <textarea name='style_album' rows='17' cols='50' style='width: 100%; font-family: courier, serif;'>{$style_album}</textarea>
  <br><br>
{/if}

{if $level_album_profile|@count > 1}
  <div><b>{lang_print id=1000106}</b></div>
  <div class='form_desc'>{lang_print id=1000110}</div>
  <table cellpadding='0' cellspacing='0' class='editprofile_options'>
  <tr><td><input type='radio' value='tab' id='user_profile_album_tab' name='user_profile_album'{if $user->user_info.user_profile_album == "tab"} CHECKED{/if}></td><td><label for='user_profile_album_tab'>{lang_print id=1000108}</label></td></tr>
  <tr><td><input type='radio' value='side' id='user_profile_album_side' name='user_profile_album'{if $user->user_info.user_profile_album == "side"} CHECKED{/if}></td><td><label for='user_profile_album_side'>{lang_print id=1000109}</label></td></tr>
  </table>
  <br>
{/if}

<table cellpadding='0' cellspacing='0'>
<tr>
<td>
  <input type='submit' class='button' value='{lang_print id=173}'>&nbsp;
  <input type='hidden' name='task' value='dosave'>
  </form>
</td>
<td>
  <form action='user_album.php' method='get'>
  <input type='submit' class='button' value='{lang_print id=39}'>
</td>
</tr>
</table>

{include file='footer.tpl'}