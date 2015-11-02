{include file='header.tpl'}

{* $Id: user_album_update.tpl 293 2010-01-21 01:16:21Z phil $ *}

<img src='./images/icons/album_image48.gif' border='0' class='icon_big'>
<div class='page_header'>{lang_print id=1000095} <a href='{$url->url_create('album', $user->user_info.user_username, $album_info.album_id)}'>{$album_info.album_title}</a></div>
<div>
  {lang_sprintf id=1000096 1=$files_total 2=$album_info.album_views}</b>
</div>

<br>

{* SHOW RESULT MESSAGE *}
{if $result != 0 && $files_total > 0}
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='success'><img src='./images/success.gif' border='0' class='icon'> {lang_print id=191}</td></tr>
  </table>
  <br>
{/if}

{* SHOW NO FILES MESSAGE IF NECESSARY, OTHERWISE SHOW ALBUM STATS *}
{if $files_total == 0}
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='result'>
    <img src='./images/icons/bulb16.gif' border='0' class='icon'>{lang_print id=1000100} <a href='user_album_upload.php?album_id={$album_info.album_id}'>{lang_print id=1000101}</a>
  </td></tr></table>
  <br>
{/if}

<div>
  <div class='button' style='float: left;'>
    <img src='./images/icons/back16.gif' border='0' class='button'><a href='user_album.php'>{lang_print id=1000097}</a>
  </div>
  <div class='button' style='float: left; padding-left: 20px;'>
    <img src='./images/icons/album_addimages16.gif' border='0' class='button'><a href='user_album_upload.php?album_id={$album_info.album_id}'>{lang_print id=1000098}</a>
  </div>
  <div class='button' style='float: left; padding-left: 20px;'>
    <img src='./images/icons/album_edit16.gif' border='0' class='button'><a href='user_album_edit.php?album_id={$album_info.album_id}'>{lang_print id=1000099}</a>
  </div>
  <div style='clear: both; height: 0px;'></div>
</div>

{* SHOW FILES IF THERE ARE ANY *}
{if $files_total > 0}
  <form action='user_album_update.php' method='POST'>
  {section name=files_loop loop=$files}

    {* ASSIGN FIRST ID *}
    {if $smarty.section.files_loop.first}{assign var='first_media_id' value=$files[files_loop].media_id}{/if}

    {* IF IMAGE, GET THUMBNAIL *}
    {assign var='has_thumb' value='0'}

    {if $files[files_loop].media_ext == "jpeg" || $files[files_loop].media_ext == "jpg" || $files[files_loop].media_ext == "gif" || $files[files_loop].media_ext == "png" || $files[files_loop].media_ext == "bmp"}
      {assign var='file_dir' value=$url->url_userdir($user->user_info.user_id)}
      {assign var='file_src' value="`$file_dir``$files[files_loop].media_id`_thumb.jpg"}
      {assign var='has_thumb' value='1'}
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

    <div class='album' style='width: 670px;' id='media_{$files[files_loop].media_id}'>
      <a name='{$files[files_loop].media_id}'></a>
      <table cellpadding='0' cellspacing='0'>
      <tr>
      <td class='album_left'>
        <div class='album_photo'>
          <table cellpadding='0' cellspacing='0' width='200' height='200'>
          <tr><td><a href='{$url->url_create('album_file', $user->user_info.user_username, $album_info.album_id, $files[files_loop].media_id)}'><img src='{$file_src}' id='file_{$files[files_loop].media_id}' {if $has_thumb == 1}width='200' height='200' {/if}border='0' style='vertical-align: middle;'></a></td></tr>
	  </table>
	</div>
      </td>
      <td class='album_right' width='100%'>
        <div>
          {lang_print id=1000046}<br>
          <input type='text' name='media_title_{$files[files_loop].media_id}' class='text' size='35' maxlength='50' value='{$files[files_loop].media_title}'>
        </div>
        <div style='margin-top: 10px;'>
          {lang_print id=1000102}<br>
          <textarea name='media_desc_{$files[files_loop].media_id}' rows='3' cols='30' style='width: 400px'>{$files[files_loop].media_desc}</textarea>
        </div>
        <div style='margin-top: 10px;'>
          <div style='float: left;'><input type='checkbox' name='delete[]' id='delete_media_{$files[files_loop].media_id}' value='{$files[files_loop].media_id}'></div>
          <div style='float: left; padding-top: 1px;'><label for='delete_media_{$files[files_loop].media_id}'>{lang_print id=1000103}</label></div>
          <div style='float: left; padding-left: 10px;'><input type='radio' name='album_cover' id='album_cover_{$files[files_loop].media_id}' value='{$files[files_loop].media_id}'{if $album_info.album_cover == $files[files_loop].media_id} checked='checked'{/if}></div>
          <div style='float: left; padding-top: 1px;'><label for='album_cover_{$files[files_loop].media_id}'>{lang_print id=1000104}</label></div>
	  {if $albums_total != 0}
            <div style='float: left; padding-left: 17px;'>
	      {lang_print id=1000105} 
	      <select name='media_album_id_{$files[files_loop].media_id}' class='album_moveto'>
	      <option value='{$files[files_loop].media_album_id}'></option>
	      {section name=album_loop loop=$albums}
	        <option value='{$albums[album_loop].album_id}'>{$albums[album_loop].album_title|truncate:28:"...":true}</option>
	      {/section}
	      </select>
            </div>
	  {/if}	
          <div style='clear: both; height: 0px;'></div>
          {if $smarty.section.files_loop.first != true || $files[files_loop].media_ext == "jpeg" || $files[files_loop].media_ext == "jpg" || $files[files_loop].media_ext == "gif" || $files[files_loop].media_ext == "png" || $files[files_loop].media_ext == "bmp"}
	    <div class='album_options2'>
              {if $files[files_loop].media_ext == "jpeg" || $files[files_loop].media_ext == "jpg" || $files[files_loop].media_ext == "gif" || $files[files_loop].media_ext == "png" || $files[files_loop].media_ext == "bmp"}
	        <div style='float: left; padding-right: 10px;'><a href='javascript:void(0);' onClick="$('ajaxframe').src='user_album_update.php?task=rotate&dir=cc&album_id={$album_info.album_id}&media_id={$files[files_loop].media_id}';this.blur();"><img src='./images/icons/album_rotate_left16.gif' border='0' class='button'>{lang_print id=1000115}</a></div>
	        <div style='float: left; padding-right: 10px;'><a href='javascript:void(0);' onClick="$('ajaxframe').src='user_album_update.php?task=rotate&dir=c&album_id={$album_info.album_id}&media_id={$files[files_loop].media_id}';this.blur();"><img src='./images/icons/album_rotate_right16.gif' border='0' class='button'>{lang_print id=1000116}</a></div>
	      {/if}
	      <div style='float: left; padding-right: 10px;{if $smarty.section.files_loop.first}display: none;{/if}' id='moveup_{$files[files_loop].media_id}'><a href='javascript:void(0);' onClick="$('ajaxframe').src='user_album_update.php?task=moveup&album_id={$album_info.album_id}&media_id={$files[files_loop].media_id}';"><img src='./images/icons/album_moveup16.gif' border='0' class='button'>{lang_print id=1000114}</a></div>
	      <div style='clear: both; height: 0px;'></div>
	    </div>
          {/if}
        </div>
      </td>
      </tr>
      </table>
    </div>
  {/section}

  <br>

  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td>
    <input type='submit' class='button' value='{lang_print id=173}'>&nbsp;
    <input type='hidden' name='task' value='doupdate'>
    <input type='hidden' name='album_id' value='{$album_info.album_id}'>
    </form>
  </td>
  </tr>
  </table>

  {* JAVASCRIPT FOR MOVING IMAGES *}
  {literal}
  <script type="text/javascript">
  <!-- 

    var first_id = '{/literal}{$first_media_id}{literal}';
    function reorderMedia(id, prev_id) {
      if(prev_id == first_id) {
        $('moveup_'+prev_id).style.display = 'block';
        $('moveup_'+id).style.display = 'none';
        first_id = id;
      }    
      $('media_'+id).inject('media_'+prev_id, 'before');
    }
  //-->
  </script>
  {/literal}


{/if}


{include file='footer.tpl'}