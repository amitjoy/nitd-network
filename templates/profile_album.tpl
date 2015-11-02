
{* $Id: profile_album.tpl 2 2009-01-10 20:53:09Z john $ *}

{* BEGIN ALBUMS *}
{if $owner->level_info.level_album_allow != 0 AND $total_albums > 0}

  <table cellpadding='0' cellspacing='0' width='100%' style='margin-bottom: 10px;'>
  <tr><td class='header'>
    {$header_album2} ({$total_albums})
    {* IF MORE THAN 3 ALBUMS, SHOW VIEW MORE LINKS *}
    {if $total_albums > 3}&nbsp;[ <a href='{$url->url_create('albums', $owner->user_info.user_username)}'>{$header_album3}</a> ]{/if}
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
        {if $albums[album_loop].album_cover_ext == "jpeg" OR $albums[album_loop].album_cover_ext == "jpg" OR $albums[album_loop].album_cover_ext == "gif" OR $albums[album_loop].album_cover_ext == "png" OR $albums[album_loop].album_cover_ext == "bmp"}
          {assign var='album_cover_dir' value=$url->url_userdir($owner->user_info.user_id)}
          {assign var='album_cover_src' value="`$album_cover_dir``$albums[album_loop].album_cover_id`_thumb.jpg"}
        {* SET THUMB PATH FOR AUDIO *}
        {elseif $albums[album_loop].album_cover_ext == "mp3" OR $albums[album_loop].album_cover_ext == "mp4" OR $albums[album_loop].album_cover_ext == "wav"}
          {assign var='album_cover_src' value='./images/icons/audio_big.gif'}
        {* SET THUMB PATH FOR VIDEO *}
        {elseif $albums[album_loop].album_cover_ext == "mpeg" OR $albums[album_loop].album_cover_ext == "mpg" OR $albums[album_loop].album_cover_ext == "mpa" OR $albums[album_loop].album_cover_ext == "avi" OR $albums[album_loop].album_cover_ext == "swf" OR $albums[album_loop].album_cover_ext == "mov" OR $albums[album_loop].album_cover_ext == "ram" OR $albums[album_loop].album_cover_ext == "rm"}
          {assign var='album_cover_src' value='./images/icons/video_big.gif'}
        {* SET THUMB PATH FOR UNKNOWN *}
        {else}
          {assign var='album_cover_src' value='./images/icons/file_big.gif'}
        {/if}
      {/if}

      {* SET ALBUM TITLE *}
      {if $albums[album_loop].album_title != ""}
        {assign var="album_title" value=$albums[album_loop].album_title}
      {else}
        {assign var="album_title" value=$header_album4}
      {/if}

      <table cellpadding='0' cellspacing='0'>
      <tr>
      <td width='1' style='padding: 5px 5px 5px 0px;' valign='top'><a href='{$url->url_create('album', $owner->user_info.user_username, $albums[album_loop].album_id)}'><img src='{$album_cover_src}' border='0' class='photo' width='{$misc->photo_size($album_cover_src,'75','75','w')}'></a></td>
      <td valign='top' style='padding: 2px 0px 0px 0px;'>
        <b><a href='{$url->url_create('album', $owner->user_info.user_username, $albums[album_loop].album_id)}'>{$album_title|truncate:17:"...":true}</a></b>
        {if $albums[album_loop].album_dateupdated > 0}<br>{$header_album5} {assign var="album_dateupdated" value=$datetime->time_since($albums[album_loop].album_dateupdated)}{lang_sprintf id=$album_dateupdated[0] 1=$album_dateupdated[1]}{/if}
      </td>
      </tr>
      </table>

    {/section}
  </td>
  </tr>
  </table>

{/if}