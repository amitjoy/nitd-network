{include file='header.tpl'}

{* $Id: user_video_upload.tpl 131 2009-03-22 00:54:31Z john $ *}


<table cellpadding='0' cellspacing='0' style="width:100%;">
  <tr>
    <td valign='top'>
      <img src='./images/icons/video_video48.gif' border='0' class='icon_big' />
      
      <div class='page_header'>{if $task=="youtube"}{lang_print id=5500186}{else}{lang_print id=5500104}{/if}</div>
      <div>{if $task=="youtube"}{lang_print id=5500197}{else}{lang_print id=5500004}{/if}</div>
    </td>
    <td valign='top'>
      <table cellpadding='0' cellspacing='0' align="right">
        <tr>
          <td class='button' nowrap='nowrap'>
            <a href='user_video.php'>
              <img src='./images/icons/back16.gif' border='0' class='button' />
              {lang_print id=5500111}
            </a>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br />


{* SHOW SWITCHER *}
{if $task=="youtube" && $user->level_info.level_video_allow && !empty($setting.setting_video_ffmpeg_path)}
  <table cellpadding='0' cellspacing='0'>
    <tr>
      <td>
        <div><img src="./images/icons/bulb16.gif" class='button' /><a href="user_video_upload.php?task=create">{lang_print id=5500191}</a></div>
      </td>
    </tr>
  </table>
  <br />
{/if}

{if $task=="create" && $user->level_info.level_youtube_allow}
  <table cellpadding='0' cellspacing='0'>
    <tr>
      <td>
        <div><img src="./images/icons/bulb16.gif" class='button' /><a href="user_video_upload.php?task=youtube">{lang_print id=5500192}</a></div>
      </td>
    </tr>
  </table>
  <br />
{/if}


{* SHOW ERROR MESSAGE *}
{if $is_error != 0}
  <table cellpadding='0' cellspacing='0'>
    <tr>
      <td class='result'>
        <div class='error'><img src='./images/error.gif' border='0' class='icon' /> {lang_print id=$is_error}</div>
      </td>
    </tr>
  </table>
  <br />
{/if}




{if $task=="youtube" || $task=="create"}

  <form name='uploadForm' action='user_video_upload.php?task={if $task=="youtube"}doembed{else}docreate{/if}' method='post' id='uploadForm'>

  <b>{lang_print id=5500078}</b><br />
  <input name='video_title' type='text' class='text' maxlength='50' size='30' value='{$video->video_info.video_title}'>
  <br />
  <br />
  
  
  <b>{lang_print id=5500079}</b><br>
  <textarea name='video_desc' rows='6' cols='50'>{$video->video_info.video_desc}</textarea>
  <br />
  <br />
  

  {if $task=="youtube"}
  <b>{lang_print id=5500190}</b><br />
  {lang_print id=5500196}<br />
  <input name='video_url' id='video_url' type='text' class='text' value='{$video_last_url}' size='30' >
  <br />
  <br />
  {/if}
  
  
  {* SHOW SEARCH PRIVACY OPTIONS IF ALLOWED BY ADMIN *}
  {if $user->level_info.level_video_search == 1}
    <b>{lang_print id=5500014}</b><br>
    <table cellpadding='0' cellspacing='0'>
      <tr>
        <td><input type='radio' name='video_search' id='video_search_1' value='1'{if  $video->video_info.video_search} checked='checked'{/if}></td>
        <td><label for='video_search_1'>{lang_print id=5500015}</label></td>
      </tr>
      <tr>
        <td><input type='radio' name='video_search' id='video_search_0' value='0'{if !$video->video_info.video_search} checked='checked'{/if}></td>
        <td><label for='video_search_0'>{lang_print id=5500016}</label></td>
      </tr>
    </table>
    <br />
  {/if}

  {* SHOW PRIVACY OPTIONS IF ALLOWED BY ADMIN *}
  {if $privacy_options|@count > 1}
    <b>{lang_print id=5500017}</b><br>
    <table cellpadding='0' cellspacing='0'>
      {foreach from=$privacy_options name=privacy_loop key=k item=v}
      <tr>
        <td><input type='radio' name='video_privacy' id='privacy_{$k}' value='{$k}'{if $video->video_info.video_privacy == $k} checked='checked'{/if}></td>
        <td><label for='privacy_{$k}'>{lang_print id=$v}</label></td>
      </tr>
      {/foreach}
    </table>
    <br />
  {/if}

  {* SHOW COMMENT OPTIONS IF ALLOWED BY ADMIN *}
  {if $comment_options|@count > 1}
    <b>{lang_print id=5500018}</b><br>
    <table cellpadding='0' cellspacing='0'>
    {foreach from=$comment_options name=comment_loop key=k item=v}
      <tr>
      <td><input type='radio' name='video_comments' id='comments_{$k}' value='{$k}'{if $video->video_info.video_comments == $k} checked='checked'{/if}></td>
      <td><label for='comments_{$k}'>{lang_print id=$v}</label></td>
      </tr>
    {/foreach}
    </table>
    <br />
  {/if}
  
  {if $task=="youtube"}
  <input type='hidden' name='video_type' value='1' />
  <input type='submit' class='button' name='submit' value='{lang_print id=5500186}' id='fallback_submit' />&nbsp;&nbsp;
  {else}
  <input type='hidden' name='video_type' value='0' />
  <input type='submit' class='button' name='submit' value='{lang_print id=5500158}' id='fallback_submit' />&nbsp;&nbsp;
  {/if}
  
  </form>

{/if}

{if $task=="upload"}
  <div id="div_upload">
  {if !empty($allowed_exts)}{lang_sprintf id=1000090 1=$allowed_exts}<br />{/if}
  {lang_sprintf id=1000091 1=$max_filesize}
  {include file='user_upload.tpl' action='user_video_upload.php' session_id=$session_id upload_token=$upload_token show_uploader=$show_uploader inputs=$inputs file_result=$file_result max_files=1 }
  </div>
{/if}


{if $task=="complete"}
  <table cellpadding='0' cellspacing='0'>
    <tr>
      <td class='result'>
        <div class='success' style='text-align: left;'> 
          {lang_print id=5500173}
        </div>
      </td>
    </tr>
  </table>
{/if}

{include file='footer.tpl'}