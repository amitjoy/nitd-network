{include file='header.tpl'}

{* $Id: profile_photos.tpl 8 2009-01-11 06:02:53Z john $ *}

<div class='page_header'>
  {lang_sprintf id=1205 1=$url->url_create('profile', $owner->user_info.user_username) 2=$owner->user_displayname}
</div>

{* DISPLAY PAGINATION MENU IF APPLICABLE *}
{if $maxpage > 1}
  <div style='text-align: center;padding-top:10px;'>
  {if $p != 1}<a href='profile_photos.php?user={$owner->user_info.user_username}&p={math equation='p-1' p=$p}'>&#171; {lang_print id=182}</a>{else}<font class='disabled'>&#171; {lang_print id=182}</font>{/if}
  {if $p_start == $p_end}
    &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$total_files} &nbsp;|&nbsp; 
  {else}
    &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_files} &nbsp;|&nbsp; 
  {/if}
  {if $p != $maxpage}<a href='profile_photos.php?user={$owner->user_info.user_username}&p={math equation='p+1' p=$p}'>{lang_print id=183} &#187;</a>{else}<font class='disabled'>{lang_print id=183} &#187;</font>{/if}
  </div>
{/if}

{* SHOW FILES IN THIS ALBUM *}
{section name=files_loop loop=$files}

  {* IF IMAGE, GET THUMBNAIL *}
  {if $files[files_loop].media_ext == "jpeg" || $files[files_loop].media_ext == "jpg" || $files[files_loop].media_ext == "gif" || $files[files_loop].media_ext == "png" || $files[files_loop].media_ext == "bmp"}
    {assign var='file_src' value="`$files[files_loop].media_dir``$files[files_loop].media_id`_thumb.jpg"}
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
      <a href='profile_photos_file.php?user={$owner->user_info.user_username}&type={$files[files_loop].type}&media_id={$files[files_loop].media_id}'><img src='{$file_src}' border='0' width='{$misc->photo_size($file_src,'120','120','w')}'></a>
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
  {if $p != 1}<a href='profile_photos.php?user={$owner->user_info.user_username}&p={math equation='p-1' p=$p}'>&#171; {lang_print id=182}</a>{else}<font class='disabled'>&#171; {lang_print id=182}</font>{/if}
  {if $p_start == $p_end}
    &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$total_files} &nbsp;|&nbsp; 
  {else}
    &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_files} &nbsp;|&nbsp; 
  {/if}
  {if $p != $maxpage}<a href='profile_photos.php?user={$owner->user_info.user_username}&p={math equation='p+1' p=$p}'>{lang_print id=183} &#187;</a>{else}<font class='disabled'>{lang_print id=183} &#187;</font>{/if}
  </div>
{/if}

{include file='footer.tpl'}