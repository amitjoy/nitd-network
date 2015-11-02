{include file='header.tpl'}

{* $Id: user_album_add.tpl 2 2009-01-10 20:53:09Z john $ *}

<table cellpadding='0' cellspacing='0' width='100%'>
<tr>
<td valign='top'>

  <img src='./images/icons/album_image48.gif' border='0' class='icon_big'>
  <div class='page_header'>{lang_print id=1000059}</div>
  <div>{lang_print id=1000074}</div>

</td>
<td valign='top' align='right'>

  <table cellpadding='0' cellspacing='0' width='130'>
  <tr><td class='button' nowrap='nowrap'><a href='user_album.php'><img src='./images/icons/back16.gif' border='0' class='button'>{lang_print id=1000097}</a></td></tr>
  </table>

</td>
</tr>
</table>

<br>

{* SHOW ERROR MESSAGE *}
{if $is_error != 0}
  <div class='error'><img src='./images/error.gif' class='icon' border='0'> {lang_print id=$is_error}</div><br>
{/if}

{* SHOW ERROR IF MAX ALBUMS REACHED *}
{if $total_albums >= $user->level_info.level_album_maxnum}
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td class='result'>
    <img src='./images/error.gif' class='icon' border='0'> {lang_sprintf id=1000075 1=$user->level_info.level_album_maxnum}
  </td></tr></table>
  <br>
  <form action='user_album.php' method='get'>
  <input type='submit' class='button' value='{lang_print id=1000076}'>
  </form>

{* DISPLAY ALBUM CREATION PAGE *}
{else}

  <form action='user_album_add.php' method='POST'>
  <b>{lang_print id=1000077}</b><br>
  <input name='album_title' type='text' class='text' maxlength='50' size='30' value='{$album_title}'>

  <br><br>

  <b>{lang_print id=1000078}</b><br>
  <textarea name='album_desc' rows='6' cols='50'>{$album_desc}</textarea>

  <br>

  {* SHOW SEARCH PRIVACY OPTIONS IF ALLOWED BY ADMIN *}
  {if $user->level_info.level_album_search == 1}
    <br>
    <b>{lang_print id=1000079}</b><br>
    <table cellpadding='0' cellspacing='0'>
      <tr><td><input type='radio' name='album_search' id='album_search_1' value='1'{if $album_search == 1} checked='checked'{/if}></td><td><label for='album_search_1'>{lang_print id=1000080}</label></td></tr>
      <tr><td><input type='radio' name='album_search' id='album_search_0' value='0'{if $album_search == 0} checked='checked'{/if}></td><td><label for='album_search_0'>{lang_print id=1000081}</label></td></tr>
    </table>
  {/if}

  {* SHOW PRIVACY OPTIONS IF ALLOWED BY ADMIN *}
  {if $privacy_options|@count > 1}
    <br>
    <b>{lang_print id=1000082}</b><br>
    <table cellpadding='0' cellspacing='0'>
    {foreach from=$privacy_options name=privacy_loop key=k item=v}
      <tr>
      <td><input type='radio' name='album_privacy' id='privacy_{$k}' value='{$k}'{if $album_privacy == $k} checked='checked'{/if}></td>
      <td><label for='privacy_{$k}'>{lang_print id=$v}</label></td>
      </tr>
    {/foreach}
    </table>
  {/if}

  {* SHOW COMMENT OPTIONS IF ALLOWED BY ADMIN *}
  {if $comment_options|@count > 1}
    <br>
    <b>{lang_print id=1000083}</b><br>
    <table cellpadding='0' cellspacing='0'>
    {foreach from=$comment_options name=comment_loop key=k item=v}
      <tr>
      <td><input type='radio' name='album_comments' id='comments_{$k}' value='{$k}'{if $album_comments == $k} checked='checked'{/if}></td>
      <td><label for='comments_{$k}'>{lang_print id=$v}</label></td>
      </tr>
    {/foreach}
    </table>
  {/if}

  {* SHOW TAG OPTIONS IF ALLOWED BY ADMIN *}
  {if $tag_options|@count > 1}
    <br>
    <b>{lang_print id=1000136}</b><br>
    <table cellpadding='0' cellspacing='0'>
    {foreach from=$tag_options name=tag_loop key=k item=v}
      <tr>
      <td><input type='radio' name='album_tag' id='tag_{$k}' value='{$k}'{if $album_tag == $k} checked='checked'{/if}></td>
      <td><label for='tag_{$k}'>{lang_print id=$v}</label></td>
      </tr>
    {/foreach}
    </table>
  {/if}

  <br>
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td>
    <input type='submit' class='button' value='{lang_print id=1000084}'>&nbsp;
    <input type='hidden' name='task' value='doadd'>
    </form>
  </td>
  <td>
    <form action='user_album.php' method='GET'>
    <input type='submit' class='button' value='{lang_print id=39}'>
    </form>
  </td>
  </tr>
  </table>
{/if}
  
{include file='footer.tpl'}