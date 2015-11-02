{include file='header.tpl'}

<div class='page_header'><img src='./images/icons/article_album22.gif' border='0' class='icon'> <a href='article.php?article_id={$article->article_info.article_id}'>{$article->article_info.article_title}</a>{lang_print id=11150802}</div>

{* DISPLAY PAGINATION MENU IF APPLICABLE *}
{if $maxpage > 1}
  <br>
  <div class='center'>
  {if $p != 1}<a href='article_album.php?article_id={$article->article_info.article_id}&p={math equation='p-1' p=$p}'>&#171; {lang_print id=11150805}</a>{else}<font class='disabled'>&#171; {lang_print id=11150805}</font>{/if}
  {if $p_start == $p_end}
    &nbsp;|&nbsp; {lang_print id=11150806} {$p_start} {lang_print id=11150807} {$total_articlemedia} &nbsp;|&nbsp; 
  {else}
    &nbsp;|&nbsp; {lang_print id=11150808} {$p_start}-{$p_end} {lang_print id=11150807} {$total_articlemedia} &nbsp;|&nbsp; 
  {/if}
  {if $p != $maxpage}<a href='article_album.php?article_id={$article->article_info.article_id}&p={math equation='p+1' p=$p}'>{lang_print id=11150809} &#187;</a>{else}<font class='disabled'>{lang_print id=11150809} &#187;</font>{/if}
  </div>
{/if}

<br>

<table cellpadding='0' cellspacing='0' align='center'>
<tr>
<td>

{* SHOW FILES IN THIS ALBUM *}
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

  {* START NEW ROW *}
  {cycle name="startrow" values="<table cellpadding='0' cellspacing='0'><tr>,,,"}
  {* SHOW THUMBNAIL *}
  <td style='padding: 15px; text-align: center; vertical-align: middle;'>
    {$files[file_loop].articlemedia_title|truncate:20:"...":true}
    <div class='album_thumb2' style='width: 120; text-align: center; vertical-align: middle;'>
      <a href='article_album_file.php?articlemedia_id={$files[file_loop].articlemedia_id}&article_id={$article->article_info.article_id}'><img src='{$file_src}' border='0'  width='{$misc->photo_size($file_src,'90','90','w')}'></a>
    </div>
  </td>
  {* END ROW AFTER 3 RESULTS *}
  {if $smarty.section.file_loop.last == true}
    </tr></table>
  {else}
    {cycle name="endrow" values=",,,</tr></table>"}
  {/if}

{/section}

</td>
</tr>
</table>

{* DISPLAY PAGINATION MENU IF APPLICABLE *}
{if $maxpage > 1}
  <br>
  <div class='center'>
  {if $p != 1}<a href='article_album.php?article_id={$article->article_info.article_id}&p={math equation='p-1' p=$p}'>&#171; {lang_print id=11150805}</a>{else}<font class='disabled'>&#171; {lang_print id=11150805}</font>{/if}
  {if $p_start == $p_end}
    &nbsp;|&nbsp; {lang_print id=11150806} {$p_start} {lang_print id=11150807} {$total_articlemedia} &nbsp;|&nbsp; 
  {else}
    &nbsp;|&nbsp; {lang_print id=11150808} {$p_start}-{$p_end} {lang_print id=11150807} {$total_articlemedia} &nbsp;|&nbsp; 
  {/if}
  {if $p != $maxpage}<a href='article_album.php?article_id={$article->article_info.article_id}&p={math equation='p+1' p=$p}'>{lang_print id=11150809} &#187;</a>{else}<font class='disabled'>{lang_print id=11150809} &#187;</font>{/if}
  </div>
{/if}


{include file='footer.tpl'}