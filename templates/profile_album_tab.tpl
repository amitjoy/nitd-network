
{* $Id: profile_album_tab.tpl 2 2009-01-10 20:53:09Z john $ *}

{* BEGIN ALBUMS *}
{if $owner->level_info.level_album_allow != 0 AND $total_albums > 0}

  <div class='profile_headline'>
    {lang_print id=1000171} ({$total_albums})
  </div>

  {* IF MORE THAN 6 ALBUMS, SHOW VIEW MORE LINKS *}
  {if $total_albums > 6}&nbsp;[ <a href='{$url->url_create('albums', $owner->user_info.user_username)}'>view all</a> ]{/if}

  {* LOOP THROUGH FIRST 6 ALBUMS *}
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

    <div class='album_item' style='width: 45%;{cycle name='rightspacer' values="margin-right: 10px;,"}'>
      <table cellpadding='0' cellspacing='0'>
      <tr>
      <td style='vertical-align: top;'>
        <a href='{$url->url_create('album', $owner->user_info.user_username, $albums[album_loop].album_id)}'><img src='{$album_cover_src}' border='0' width='{$misc->photo_size($album_cover_src,'75','75','w')}'></a>
      </td>
      <td class='album_item_info'>
        <div class='album_item_title'><a href='{$url->url_create('album', $owner->user_info.user_username, $albums[album_loop].album_id)}'>{$albums[album_loop].album_title|truncate:17:"...":true}</a></div>
        {if $albums[album_loop].album_dateupdated > 0}
	  {assign var="album_dateupdated" value=$datetime->time_since($albums[album_loop].album_dateupdated)}
	  {capture assign='updateddate'}{lang_sprintf id=$album_dateupdated[0] 1=$album_dateupdated[1]}{/capture}
          <div class='album_item_date'>{lang_sprintf id=1000140 1=$updateddate}</div>
        {/if}
        <div style='margin-top: 10px;'>{$albums[album_loop].album_desc|truncate:75:"...":true}</div>
      </td>
      </tr>
      </table>
    </div>
    {cycle values=",<div name='bottomspacer' style='clear: both; height: 10px;'></div>"}
    
  {/section}
  <div style='clear: both; height: 0px;'></div>


{/if}