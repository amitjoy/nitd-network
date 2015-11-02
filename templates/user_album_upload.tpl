{include file='header.tpl'}

{* $Id: user_album_upload.tpl 2 2009-01-10 20:53:09Z john $ *}

<table cellpadding='0' cellspacing='0'>
<tr>
<td width='100%'>

  <img src='./images/icons/album_image48.gif' border='0' class='icon_big'>
  <div class='page_header'>{lang_print id=1000087} <a href='{$url->url_create('album', $user->user_info.user_username, $album_info.album_id)}'>{$album_info.album_title}</a></div>
  <div>{lang_print id=1000088}</div>

</td>
<td align='right' valign='top'>

  <table cellpadding='0' cellspacing='0' width='130'>
  <tr><td class='button' nowrap='nowrap'><a href='user_album_update.php?album_id={$album_info.album_id}'><img src='./images/icons/back16.gif' border='0' class='button'>{lang_print id=1000089}</a></td></tr>
  </table>

</td>
</tr>
</table>

<br>

<div>{lang_sprintf id=1000058 1=$space_left}<br>{lang_sprintf id=1000090 1=$allowed_exts}<br>{lang_sprintf id=1000091 1=$max_filesize}</div>

{* SHOW MESSAGE IF NEW ALBUM *}
{if $new_album == 1}
  <br>
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='result'>
    <div class='success'><img src='./images/success.gif' border='0' class='icon'> {lang_print id=1000092}</div>
  </td>
  </tr>
  </table>
{/if}

<br>

{include file='user_upload.tpl' action='user_album_upload.php' session_id=$session_id upload_token=$upload_token show_uploader=$show_uploader inputs=$inputs file_result=$file_result}

{include file='footer.tpl'}