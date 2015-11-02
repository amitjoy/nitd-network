{include file='header.tpl'}

{* $Id: browse_albums.tpl 240 2009-11-14 02:42:57Z phil $ *}

<div class='page_header'>{lang_print id=1000127}</div>

<div style='padding: 7px 10px 7px 10px; background: #F2F2F2; border: 1px solid #BBBBBB; margin: 10px 0px 10px 0px; font-weight: bold;'>
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td>
    {lang_print id=1000128}&nbsp;
  </td>
  <td>
    <select class='small' name='v' onchange="window.location.href='browse_albums.php?s={$s}&v='+this.options[this.selectedIndex].value;">
    <option value='0'{if $v == "0"} SELECTED{/if}>{lang_print id=1000129}</option>
    {if $user->user_exists}<option value='1'{if $v == "1"} SELECTED{/if}>{lang_print id=1000130}</option>{/if}
    </select>
  </td>
  <td style='padding-left: 20px;'>
    {lang_print id=1000131}&nbsp;
  </td>
  <td>
    <select class='small' name='s' onchange="window.location.href='browse_albums.php?v={$v}&s='+this.options[this.selectedIndex].value;">
    <option value='album_dateupdated DESC'{if $s == "album_dateupdated DESC"} SELECTED{/if}>{lang_print id=1000132}</option>
    <option value='album_datecreated DESC'{if $s == "album_datecreated DESC"} SELECTED{/if}>{lang_print id=1000133}</option>
    </select>
  </td>
  </tr>
  </table>
</div>


{* DISPLAY PAGINATION MENU IF APPLICABLE *}
{if $maxpage > 1}
  <div style='text-align: center; padding-bottom: 10px;'>
  {if $p != 1}<a href='browse_albums.php?s={$s}&v={$v}&p={math equation='p-1' p=$p}'>&#171; {lang_print id=182}</a>{else}&#171; {lang_print id=182}{/if}
  &nbsp;|&nbsp;&nbsp;
  {if $p_start == $p_end}
    <b>{lang_sprintf id=184 1=$p_start 2=$total_albums}</b>
  {else}
    <b>{lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_albums}</b>
  {/if}
  &nbsp;&nbsp;|&nbsp;
  {if $p != $maxpage}<a href='browse_albums.php?s={$s}&v={$v}&p={math equation='p+1' p=$p}'>{lang_print id=183} &#187;</a>{else}{lang_print id=183} &#187;{/if}
  </div>
{/if}



<div>

  {section name=album_loop loop=$albums}

    {* SET ALBUM COVER *}
    {if $albums[album_loop].album_cover_id == 0}
      {assign var='album_cover_src' value='./images/icons/folder_big.gif'}
    {else}
      {* IF IMAGE, GET THUMBNAIL *}
      {if $albums[album_loop].album_cover_ext == "jpeg" || $albums[album_loop].album_cover_ext == "jpg" || $albums[album_loop].album_cover_ext == "gif" || $albums[album_loop].album_cover_ext == "png" || $albums[album_loop].album_cover_ext == "bmp"}
        {assign var='album_cover_dir' value=$url->url_userdir($albums[album_loop].album_author->user_info.user_id)}
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


    <div class='albums_browse_item' style='width: 415px; float: left;'>
      <table cellpadding='0' cellspacing='0'>
      <tr>
      <td style='vertical-align: top;'>
        <a href='{$url->url_create('album', $albums[album_loop].album_author->user_info.user_username, $albums[album_loop].album_id)}'><img src='{$album_cover_src}' border='0' width='100' height='100'></a>
      </td>
      <td style='vertical-align: top; padding-left: 10px;'>
        <div style='font-weight: bold; font-size: 13px;'><a href='{$url->url_create('album', $albums[album_loop].album_author->user_info.user_username, $albums[album_loop].album_id)}'>{$albums[album_loop].album_title|truncate:30:"...":true}</a></div>
        <div class='album_browse_date'>
          {assign var='album_dateupdated' value=$datetime->time_since($albums[album_loop].album_dateupdated)}{capture assign="updated"}{lang_sprintf id=$album_dateupdated[0] 1=$album_dateupdated[1]}{/capture}
          {lang_sprintf id=1000172 1=$albums[album_loop].album_files} - {lang_sprintf id=1000124 1=$updated 2=$url->url_create("profile", $albums[album_loop].album_author->user_info.user_username) 3=$albums[album_loop].album_author->user_displayname}
        </div>
        <div style='margin-top: 10px;'>{$albums[album_loop].album_desc|truncate:75:"...":true}</div>
      </td>
      </tr>
      </table>
    </div>

    {cycle values=",<div style='clear: both; height: 10px;'></div>"}
  {/section}

</div>


{include file='footer.tpl'}