{include file='header.tpl'}

<table class='tabs' cellpadding='0' cellspacing='0'>
<tr>
<td class='tab0'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_article_edit.php?article_id={$article->article_info.article_id}'>{lang_print id=11151702}</a></td><td class='tab'>&nbsp;</td>
<td class='tab1' NOWRAP><a href='user_article_edit_files.php?article_id={$article->article_info.article_id}'>{lang_print id=11151705}</a></td><td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_article_edit_comments.php?article_id={$article->article_info.article_id}'>{lang_print id=11151706}</a></td><td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_article_edit_delete.php?article_id={$article->article_info.article_id}'>{lang_print id=11151708}</a></td><td class='tab'>&nbsp;</td>
<td class='tab3'><a href='user_article.php'>&#171; {lang_print id=11151709}</a></td>
</tr>
</table>

<table cellpadding='0' cellspacing='0' width='100%'><tr><td class='page'>

<table cellpadding='0' cellspacing='0'>
<tr>
<td class='album_left' width='100%'>
  <div>
    <img src='./images/icons/article48.gif' border='0' class='icon_big'>
    <div class='page_header'>{lang_print id=11151710} <a href='article.php?article_id={$article->article_info.article_id}'>{$article->article_info.article_title|truncate:30:"...":true}</a></div>
    {lang_print id=11151711}
    <b>{$files_total}</b>
  </div>
</td>
<td class='album_right'>
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='button' nowrap='nowrap'>
    <a href='user_article_edit_files_upload.php?article_id={$article->article_info.article_id}'><img src='./images/icons/addimages16.gif' border='0' class='icon'></a> 
    &nbsp;<a href='user_article_edit_files_upload.php?article_id={$article->article_info.article_id}'>{lang_print id=11151712}</a>
  </td></tr></table>
</td>
</tr>
</table>

<br>

{* SHOW RESULT MESSAGE *}
{if $result != 0 AND $files_total > 0}
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='result'>
    <img src='./images/success.gif' border='0' class='icon'> {lang_print id=11151701}
  </td></tr></table>
{/if}

{* SHOW FILES IF THERE ARE ANY *}
{if $files_total > 0}
  <form action='user_article_edit_files.php' method='POST'>
  {section name=file_loop loop=$files}

    {* IF IMAGE, GET THUMBNAIL *}
    {if $files[file_loop].articlemedia_ext == "jpeg" OR $files[file_loop].articlemedia_ext == "jpg" OR $files[file_loop].articlemedia_ext == "gif" OR $files[file_loop].articlemedia_ext == "png" OR $files[file_loop].articlemedia_ext == "bmp"}
      {assign var='file_dir' value=$article->article_dir($article->article_info.article_id)}
      {assign var='file_src' value="`$file_dir``$files[file_loop].articlemedia_id`_thumb.jpg"}
    {* SET THUMB PATH FOR AUDIO *}
    {elseif $files[file_loop].articlemedia_ext == "mp3" OR $files[file_loop].articlemedia_ext == "mp4" OR $files[file_loop].articlemedia_ext == "wav"}
      {assign var='file_src' value='./images/icons/audio_big.gif'}
    {* SET THUMB PATH FOR VIDEO *}
    {elseif $files[file_loop].articlemedia_ext == "mpeg" OR $files[file_loop].articlemedia_ext == "mpg" OR $files[file_loop].articlemedia_ext == "mpa" OR $files[file_loop].articlemedia_ext == "avi" OR $files[file_loop].articlemedia_ext == "swf" OR $files[file_loop].articlemedia_ext == "mov" OR $files[file_loop].articlemedia_ext == "ram" OR $files[file_loop].articlemedia_ext == "rm"}
      {assign var='file_src' value='./images/icons/video_big.gif'}
    {* SET THUMB PATH FOR UNKNOWN *}
    {else}
      {assign var='file_src' value='./images/icons/file_big.gif'}
    {/if}

    <div class='album_row'>
    <a name='{$files[file_loop].articlemedia_id}'></a>
    <table cellpadding='0' cellspacing='0'>
    <tr>
    <td>
      <table cellpadding='0' cellspacing='0' width='220'>
      <tr>
      <td class='album_photo'><a href='article_album_file.php?article_id={$article->article_info.article_id}&articlemedia_id={$files[file_loop].articlemedia_id}'><img src='{$file_src}' border='0'></a></td>
      </tr>
      </table>
    </td>
    <td class='album_row1' width='100%'>
      <table cellpadding='0' cellspacing='0'>
      <tr>
      <td>
        {lang_print id=11151713}<br><input type='text' name='articlemedia_title_{$files[file_loop].articlemedia_id}' class='text' size='30' maxlength='50' value='{$files[file_loop].articlemedia_title}'>
        {if $files[file_loop].articlemedia_comments_total > 0}&nbsp;&nbsp;&nbsp; <b>[ <a href='user_article_edit_files_comments.php?article_id={$article->article_info.article_id}&articlemedia_id={$files[file_loop].articlemedia_id}'>{$files[file_loop].articlemedia_comments_total} {lang_print id=11151714}</a> ]</b>{/if}
      </td>
      </tr>
      <tr><td><br>{lang_print id=11151715}<br><textarea name='articlemedia_desc_{$files[file_loop].articlemedia_id}' rows='3' cols='52'>{$files[file_loop].articlemedia_desc}</textarea></td></tr>
      </table>
      <table cellpadding='0' cellspacing='0' class='album_photooptions'>
      <tr>
      <td><input type='checkbox' name='delete_articlemedia_{$files[file_loop].articlemedia_id}' id='delete_articlemedia_{$files[file_loop].articlemedia_id}' value='1'></td><td><label for='delete_articlemedia_{$files[file_loop].articlemedia_id}'>{lang_print id=11151716}</label> &nbsp;</td>
      </tr>
      </table>
    </td>
    </tr>
    </table>
    </div>
  {/section}
  <br>
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td><input type='submit' class='button' value='{lang_print id=11151717}'>&nbsp;
  <input type='hidden' name='task' value='doupdate'>
  <input type='hidden' name='article_id' value='{$article->article_info.article_id}'>
  </form>
  </td>
  </tr>
  </table>
{/if}


</td></tr></table>

{include file='footer.tpl'}