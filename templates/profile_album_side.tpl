
{* $Id: profile_album_side.tpl 2 2009-01-10 20:53:09Z john $ *}

{* BEGIN ALBUMS *}
{if $owner->level_info.level_album_allow != 0 AND $total_albums > 0}

  <table cellpadding='0' cellspacing='0' width='100%' style='margin-bottom: 10px;'>
  <tr><td class='header'>
    {lang_print id=1000171} ({$total_albums})
    {* IF MORE THAN 3 ALBUMS, SHOW VIEW MORE LINKS *}
    {if $total_albums > 3}&nbsp;[ <a href='{$url->url_create('albums', $owner->user_info.user_username)}'>{lang_print id=1021}</a> ]{/if}
  </td></tr>
  <tr>
  <td class='profile'>
    {* LOOP THROUGH FIRST 3 ALBUMS *}
    {section name=album_loop loop=$albums}

      {* SET ALBUM COVER *}
      {if $albums[album_loop].album_cover_id == 0}
        {assign var='album_cover_src' value='./images/icons/folder_big.gif'}
      {else}
        {* IF IMAGE, GET THUMBNAIL *}
        {if $albums[album_loop].album_cover_ext == "jpeg" || $albums[album_loop].album_cover_ext == "jpg" || $albums[album_loop].album_cover_ext == "gif" || $albums[album_loop].album_cover_ext == "png" || $albums[album_loop].album_cover_ext == "bmp"}
          {assign var='album_cover_dir' value=$url->url_userdir($owner->user_info.user_id)}
          {assign var='album_cover_src' value="`$album_cover_dir``$albums[album_loop].album_cover_id`_thumb.jpg"}
        {* SET THUMB PATH FOR AUDIO *}
        {elseif $albums[album_loop].album_cover_ext == "mp3" || $albums[album_loop].album_cover_ext == "mp4" || $albums[album_loop].album_cover_ext == "wav"}
          {assign var='album_cover_src' value='./images/icons/audio_big.gif'}
        {* SET THUMB PATH FOR VIDEO *}
        {elseif $albums[album_loop].album_cover_ext == "mpeg" || $albums[album_loop].album_cover_ext == "mpg" || $albums[album_loop].album_cover_ext == "mpa" || $albums[album_loop].album_cover_ext == "avi" || $albums[album_loop].album_cover_ext == "swf" || $albums[album_loop].album_cover_ext == "mov" || $albums[album_loop].album_cover_ext == "ram" || $albums[album_loop].album_cover_ext == "rm"}
          {assign var='album_cover_src' value='./images/icons/video_big.gif'}
        {* SET THUMB PATH FOR UNKNOWN *}
        {else}
          {assign var='album_cover_src' value='./images/icons/file_big.gif'}
        {/if}
      {/if}

      <table cellpadding='0' cellspacing='0'>
      <tr>
      <td width='1' valign='top'><a href='{$url->url_create('album', $owner->user_info.user_username, $albums[album_loop].album_id)}'><img src='{$album_cover_src}' border='0' width='{$misc->photo_size($album_cover_src,'60','60','w')}'></a></td>
      <td valign='top' class='album_gutter_info'>
        <div class='album_gutter_title'><a href='{$url->url_create('album', $owner->user_info.user_username, $albums[album_loop].album_id)}'>{$albums[album_loop].album_title|truncate:17:"...":true}</a></div>
        {if $albums[album_loop].album_dateupdated > 0}
	  {assign var="album_dateupdated" value=$datetime->time_since($albums[album_loop].album_dateupdated)}
	  {capture assign='updateddate'}{lang_sprintf id=$album_dateupdated[0] 1=$album_dateupdated[1]}{/capture}
          <div class='album_item_date'>{lang_sprintf id=1000140 1=$updateddate}</div>
        {/if}
      </td>
      </tr>
      </table>

      {if $smarty.section.album_loop.last != true}<div style='height: 5px;'>&nbsp;</div>{/if}

    {/section}
  </td>
  </tr>
  </table>

{/if}