{include file='header.tpl'}

{* $Id: user_album.tpl 134 2009-03-23 00:03:51Z john $ *}

<img src='./images/icons/album_image48.gif' border='0' class='icon_big' />
<div class='page_header'>{lang_print id=1000055}</div>
<div>
  {lang_sprintf id=1000057 1=$albums_total 2=$total_files}<br>
  {lang_sprintf id=1000058 1=$space_free}
</div>


<div style='margin-top: 20px;'>
  <div class='button' style='float: left;'>
    <a href='user_album_add.php'><img src='./images/icons/plus16.gif' border='0' class='button'>{lang_print id=1000059}</a>
  </div>
  <div class='button' style='float: left; padding-left: 20px;'>
    <a href='user_album_settings.php'><img src='./images/icons/album_settings16.gif' border='0' class='button'>{lang_print id=1000056}</a>
  </div>
  <div style='clear: both; height: 0px;'></div>
</div>

{* LOOP THROUGH ALBUMS *}
{section name=album_loop loop=$albums}

  {* SET ALBUM COVER *}
  {if $albums[album_loop].album_cover_id == 0}
    {assign var='album_cover_src' value='./images/icons/folder_big.gif'}
  {else}
    {* IF IMAGE, GET THUMBNAIL *}
    {if $albums[album_loop].album_cover_ext == "jpeg" || $albums[album_loop].album_cover_ext == "jpg" || $albums[album_loop].album_cover_ext == "gif" || $albums[album_loop].album_cover_ext == "png" || $albums[album_loop].album_cover_ext == "bmp"}
      {assign var='album_cover_dir' value=$url->url_userdir($user->user_info.user_id)}
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


  <div class='album' style='width: 550px;' id='album_{$albums[album_loop].album_id}'>
    <table cellpadding='0' cellspacing='0' width='100%'>
      <tr>
        <td class='album_left' width='1'>
          <div class='album_photo' style='width: 140px; height: 140px;'>
            <table cellpadding='0' cellspacing='0' width='140' height='140'>
              <tr>
                <td>
                  <a href='user_album_update.php?album_id={$albums[album_loop].album_id}'>
                    <img src='{$album_cover_src}' border='0' width='{$misc->photo_size($album_cover_src,"140","140","w")}' />
                  </a>
                </td>
              </tr>
            </table>
          </div>
        </td>
        <td class='album_right' width='100%'>
          <div class='album_title'>
            <a href='user_album_update.php?album_id={$albums[album_loop].album_id}'>{$albums[album_loop].album_title|truncate:30:"...":true}</a>
          </div>
          {if $albums[album_loop].album_desc != ""}
            <div style='margin-bottom: 8px;'>{$albums[album_loop].album_desc|truncate:197:"...":true}</div>
          {/if}
          <div class='album_stats'>
            {lang_print id=1000061}
            {assign var='album_datecreated' value=$datetime->time_since($albums[album_loop].album_datecreated)}
            {lang_sprintf id=$album_datecreated[0] 1=$album_datecreated[1]}
            <br />
            {if $albums[album_loop].album_dateupdated != 0}
              {lang_print id=1000062}
              {assign var='album_dateupdated' value=$datetime->time_since($albums[album_loop].album_dateupdated)}
              {lang_sprintf id=$album_dateupdated[0] 1=$album_dateupdated[1]}
              <br />
            {/if}
            {lang_print id=1000063} {lang_sprintf id=1000064 1=$albums[album_loop].album_files 2=$albums[album_loop].album_space}<br />
            {lang_print id=1000065} {lang_sprintf id=1000066 1=$albums[album_loop].album_views}<br />
            {lang_print id=1000067} {lang_print id=$albums[album_loop].album_privacy}
            <div class='album_options'>
              <div style='float: left;'>
                <a href='{$url->url_create("album", $user->user_info.user_username, $albums[album_loop].album_id)}'>
                  <img src='./images/icons/album_album16.gif' border='0' class='button' />
                  {lang_print id=1000068}
                </a>
              </div>
              <div style='float: left; padding-left: 15px;'>
                <a href='user_album_update.php?album_id={$albums[album_loop].album_id}'>
                  <img src='./images/icons/album_edit16.gif' border='0' class='button' />
                  {lang_print id=1000069}
                </a>
              </div>
              <div style='float: left; padding-left: 15px;'>
                <a href='javascript:void(0);' onClick="SocialEngine.Album.deleteAlbum({$albums[album_loop].album_id});">
                  <img src='./images/icons/album_delete16.gif' border='0' class='button' />
                  {lang_print id=1000070}
                </a>
              </div>
              <div class="seAlbumActionMoveup" style='float: left; padding-left: 8px;{if $smarty.section.album_loop.first==true}display:none;{/if}'>
                <a href='javascript:void(0);' onclick="SocialEngine.Album.moveupAlbum({$albums[album_loop].album_id});">
                  <img src='./images/icons/album_moveup16.gif' border='0' class='button' />
                  {lang_print id=1000114}
                </a>
              </div>
              <div style='clear: both; height: 0px;'></div>
            </div>
          </div>
        </td>
      </tr>
    </table>
  </div>

{/section}
<div style='clear: both; height: 0px;'></div>

{* IF THERE ARE NO ALBUMS, SHOW NOTE *}
{if $albums_total == 0}
  <br />
  <table cellpadding='0' cellspacing='0'>
    <tr>
      <td class='result'>
        <img src='./images/icons/bulb16.gif' border='0' class='icon' />
        {lang_print id=1000071}
        <a href='user_album_add.php'>{lang_print id=1000072}</a>
      </td>
    </tr>
  </table>
{/if}


{* JAVASCRIPT *}
{lang_javascript ids=1000054}
<script type="text/javascript" src="./include/js/class_album.js"></script>
<script type="text/javascript">
<!-- 
  SocialEngine.Album = new SocialEngineAPI.Album();
  SocialEngine.RegisterModule(SocialEngine.Album);
//-->
</script>


{* HIDDEN DIV TO DISPLAY CONFIRMATION MESSAGE *}
<div style='display: none;' id='confirmdelete'>
  <div style='margin-top: 10px;'>
    {lang_print id=1000053}
  </div>
  <br />
  <input type='button' class='button' value='{lang_print id=175}' onClick='parent.TB_remove();parent.SocialEngine.Album.deleteAlbumConfirm();' />
  <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();' />
</div>


{include file='footer.tpl'}