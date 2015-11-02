{include file='header.tpl'}

{* $Id: album.tpl 2 2009-01-10 20:53:09Z john $ *}

<div class='page_header'>
  {lang_sprintf id=1000141 1=$url->url_create('profile', $owner->user_info.user_username) 2=$owner->user_displayname 3=$url->url_create('albums', $owner->user_info.user_username)} &#187; {$album_info.album_title}
</div>

{if $album_info.album_desc != ""}<div>{$album_info.album_desc}</div>{/if}

{* DISPLAY PAGINATION MENU IF APPLICABLE *}
{if $maxpage > 1}
  <div style='text-align: center;padding-top:10px;'>
  {if $p != 1}<a href='{$url->url_create('album', $owner->user_info.user_username, $album_info.album_id)}/&p={math equation='p-1' p=$p}'>&#171; {lang_print id=182}</a>{else}<font class='disabled'>&#171; {lang_print id=182}</font>{/if}
  {if $p_start == $p_end}
    &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$total_files} &nbsp;|&nbsp; 
  {else}
    &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_files} &nbsp;|&nbsp; 
  {/if}
  {if $p != $maxpage}<a href='{$url->url_create('album', $owner->user_info.user_username, $album_info.album_id)}/&p={math equation='p+1' p=$p}'>{lang_print id=183} &#187;</a>{else}<font class='disabled'>{lang_print id=183} &#187;</font>{/if}
  </div>
{/if}

{* SHOW FILES IN THIS ALBUM *}
{section name=files_loop loop=$files}

  {* IF IMAGE, GET THUMBNAIL *}
  {if $files[files_loop].media_ext == "jpeg" || $files[files_loop].media_ext == "jpg" || $files[files_loop].media_ext == "gif" || $files[files_loop].media_ext == "png" || $files[files_loop].media_ext == "bmp"}
    {assign var='file_dir' value=$url->url_userdir($files[files_loop].media_author->user_info.user_id)}
    {assign var='file_src' value="`$file_dir``$files[files_loop].media_id`_thumb.jpg"}
  {* SET THUMB PATH FOR AUDIO *}
  {elseif $files[files_loop].media_ext == "mp3" || $files[files_loop].media_ext == "mp4" || $files[files_loop].media_ext == "wav"}
    {assign var='file_src' value='./images/icons/audio_big.gif'}
  {* SET THUMB PATH FOR VIDEO *}
  {elseif $files[files_loop].media_ext == "mpeg" || $files[files_loop].media_ext == "mpg" || $files[files_loop].media_ext == "mpa" || $files[files_loop].media_ext == "avi" || $files[files_loop].media_ext == "swf" || $files[files_loop].media_ext == "mov" || $files[files_loop].media_ext == "ram" || $files[files_loop].media_ext == "rm"}
    {assign var='file_src' value='./images/icons/video_big.gif'}
  {* SET THUMB PATH FOR UNKNOWN *}
  {else}
    {assign var='file_src' value='./images/icons/file_big.gif'}
  {/if}

  {* START NEW ROW *}
  {cycle name="startrow" values="<table cellpadding='0' cellspacing='0' align='center'><tr>,,,,"}
  {* SHOW THUMBNAIL *}
  <td style='padding: 15px 15px 15px 0px; text-align: center; vertical-align: middle;'>
    {$files[files_loop].media_title|truncate:20:"...":true}&nbsp;
    <div class='album_thumb2' style='width: 120; text-align: center; vertical-align: middle;'>
      <a href='{$url->url_create('album_file', $owner->user_info.user_username, $album_info.album_id, $files[files_loop].media_id)}'><img src='{$file_src}' border='0' width='{$misc->photo_size($file_src,'120','120','w')}'></a>
    </div>
  </td>
  {* END ROW AFTER 5 RESULTS *}
  {if $smarty.section.files_loop.last == true}
    </tr></table>
  {else}
    {cycle name="endrow" values=",,,,</tr></table>"}
  {/if}

{/section}

{* DISPLAY PAGINATION MENU IF APPLICABLE *}
{if $maxpage > 1}
  <div style='text-align: center;padding-top:10px;'>
  {if $p != 1}<a href='{$url->url_create('album', $owner->user_info.user_username, $album_info.album_id)}/&p={math equation='p-1' p=$p}'>&#171; {lang_print id=182}</a>{else}<font class='disabled'>&#171; {lang_print id=182}</font>{/if}
  {if $p_start == $p_end}
    &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$total_files} &nbsp;|&nbsp; 
  {else}
    &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_files} &nbsp;|&nbsp; 
  {/if}
  {if $p != $maxpage}<a href='{$url->url_create('album', $owner->user_info.user_username, $album_info.album_id)}/&p={math equation='p+1' p=$p}'>{lang_print id=183} &#187;</a>{else}<font class='disabled'>{lang_print id=183} &#187;</font>{/if}
  </div>
{/if}

{include file='footer.tpl'}