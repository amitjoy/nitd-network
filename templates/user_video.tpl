{include file='header.tpl'}

{* $Id: user_video.tpl 148 2009-03-30 23:58:01Z john $ *}

<img src='./images/icons/video_video48.gif' border='0' class='icon_big' />
<div class='page_header'>{lang_print id=5500025}</div>
<div>{lang_sprintf id=5500103 1=$videos_total}</div>

{* UPLOAD NEW VIDEO BUTTON *}
{if $videos_total < $user->level_info.level_video_maxnum}
  <div style='margin-top: 20px;'>
    {if $user->level_info.level_video_allow && !empty($setting.setting_video_ffmpeg_path)}
    <div class='button' style='float: left; margin-right:20px;'>
      <a href='user_video_upload.php?task=create'><img src='./images/icons/plus16.gif' border='0' class='button' />{lang_print id=5500106}</a>
    </div>
    {/if}
    {if $user->level_info.level_youtube_allow}
    <div class='button' style='float:left;'>
      <a href='user_video_upload.php?task=youtube'><img src='./images/icons/youtube.gif' border='0' class='button' />{lang_print id=5500186}</a>
    </div>
    {/if}
    <div style='clear: both; height: 0px;'></div>
  </div>
{/if}

<br />

{* LOOP THROUGH VIDEOS *}
{section name=video_loop loop=$videos}    

  {* ENSURE VIDEO TITLE ISN'T BLANK *}
  {if $videos[video_loop].video_title == ""}{capture assign="video_title"}{lang_print id=589}{/capture}{else}{assign var="video_title" value=$videos[video_loop].video_title}{/if}
  
  <div class='videoTab'>
    <table cellpadding='0' cellspacing='0'>
    <tr>
    <td valign='top'>
      <div class='video_photo'>
        <a href='{$url->url_create("video", $user->user_info.user_username, $videos[video_loop].video_id)}'>
          <img src='{if $videos[video_loop].video_thumb}{$videos[video_loop].video_dir}{$videos[video_loop].video_id}_thumb.jpg{else}./images/video_placeholder.gif{/if}' border='0' width='{$setting.setting_video_thumb_width}' height='{$setting.setting_video_thumb_height}' />
        </a>
      </div>
    </td>
    <td valign='top' style='padding-left: 7px; width: 300px;'>
      <div class='video_title'><a href='{$url->url_create("video", $user->user_info.user_username, $videos[video_loop].video_id)}'>{$video_title|truncate:30:'...':true}</a></div>
      
      {* VIDEO HAD A PROBLEM ENCODING *}
      {if $videos[video_loop].video_is_converted == -1}
        <div style="padding-top:2px;padding-left:15px;font-weight:bold;color:#ee3333;">{lang_print id=5500203}</div>
      
      {* VIDEO HAD A PROBLEM UPLOADING *}
      {elseif !$videos[video_loop].video_uploaded}
        <div style="padding-top:2px;padding-left:15px;font-weight:bold;color:#ee3333;">{lang_print id=5500204}</div>
      
      {* VIDEO IS WAITING TO BE ENCODED *}
      {elseif !$videos[video_loop].video_is_converted}
        <div style="padding-top:2px;padding-left:15px;font-weight:bold;">{lang_print id=5500202}</div>
      
      {* VIDEO IS PROPERLY ENCODED *}
      {else}
        <div style="padding-top:2px;">
          {lang_sprintf id=5500043 1=$videos[video_loop].video_views 2=$videos[video_loop].total_comments} - 
          {section name=full_loop start=0 loop=$videos[video_loop].video_rating_full}<img src='./images/icons/video_rating_full_small.gif' border='0' />{/section}
          {if $videos[video_loop].video_rating_part}<img src='./images/icons/video_rating_part_small.gif' border='0' />{/if}
          {section name=none_loop start=0 loop=$videos[video_loop].video_rating_none}<img src='./images/icons/video_rating_none_small.gif' border='0' />{/section}
        </div>
      {/if}
      
      <div class='video_options'>
        {* EDIT VIDEO *}
        {if $videos[video_loop].video_is_converted == 1 && $videos[video_loop].video_uploaded}
        <div style='float: left; padding-right: 15px;'>
          <a href='javascript:void(0);' onClick="editVideo('{$videos[video_loop].video_id}', '{$videos[video_loop].video_title|replace:"&#039;":"\&#039;"}', '{$videos[video_loop].video_desc|replace:"&#039;":"\&#039;"}', '{$videos[video_loop].video_search}', '{$videos[video_loop].video_privacy}', '{$videos[video_loop].video_comments}');"><img src='./images/icons/video_edit16.gif' border='0' class='button' />{lang_print id=5500107}</a>
        </div>
        {/if}
        
        {* RETRY UPLOAD *}
        {if $videos[video_loop].video_is_converted == -1 || !$videos[video_loop].video_uploaded}
        <div style='float: left; padding-right: 15px;'>
          <a href='user_video_upload.php?task=upload&video_id={$videos[video_loop].video_id}'><img src='./images/icons/back16.gif' border='0' class='button' />Retry Upload</a>
        </div>
        {/if}
        
        {* DELETE VIDEO *}
        {if !$videos[video_loop].video_uploaded || $videos[video_loop].video_is_converted!=0}
        <div style='float: left; padding-right: 15px;'>
          <a href='javascript:void(0);' onClick="confirmDelete('{$videos[video_loop].video_id}');"><img src='./images/icons/video_delete16.gif' border='0' class='button' />{lang_print id=5500108}</a>
        </div>
        {/if}
        
        <div style='clear: both; height: 0px;'></div>
      </div>
    </td>
    </tr>
    </table>
  </div>    
  
  {cycle values=",<div style='clear: both; height: 0px;'></div>"}

{/section}
<div style='clear: both; height: 0px;'></div>


{* IF NO VIDEOS, SHOW NOTE *}
{if $videos_total == 0}
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='result'>
    <img src='./images/icons/bulb16.gif' border='0' class='icon' />{lang_print id=5500109}</div>
  </td></tr>
  </table>
{/if}


{* JAVASCRIPT FOR CONFIRMING DELETION *}
{literal}
<script type="text/javascript">
<!-- 
var video_id = 0;
function confirmDelete(id)
{
  video_id = id;
  TB_show('{/literal}{lang_print id=5500145}{literal}', '#TB_inline?height=100&width=300&inlineId=confirmdelete', '', '../images/trans.gif');
}

function deleteVideo()
{
  var request = new Request({
    'url' : 'user_video.php',
    'method' : 'post',
    'data' : {
      'task' : 'delete',
      'video_id' : video_id
    },
    onComplete : function()
    {
      window.location = 'user_video.php';
    }
  }).send();
}


function editVideo(id, title, desc, search, privacy, comments)
{
  $('video_id').value = id;  
  $('video_title').defaultValue = title;  
  $('video_title').value = title;  
  $('video_desc').defaultValue = desc;  
  $('video_desc').value = desc;
  if( $('video_search_'+search) )
  {
    $('video_search_'+search).checked = true;
    $('video_search_'+search).defaultChecked = true;
  }
  if( $('privacy_'+privacy) )
  {
    $('privacy_'+privacy).checked = true;
    $('privacy_'+privacy).defaultChecked = true;
  }
  if( $('comments_'+comments) )
  {
    $('comments_'+comments).checked = true;
    $('comments_'+comments).defaultChecked = true;
  }
  TB_show('{/literal}{lang_print id=5500083}{literal}', '#TB_inline?height=450&width=450&inlineId=editvideo', '', '../images/trans.gif');
}

//-->
</script>
{/literal}

{* HIDDEN DIV TO DISPLAY CONFIRMATION MESSAGE *}
<div style='display: none;' id='confirmdelete'>
  <div style='margin-top: 10px;'>
    {lang_print id=5500146}
  </div>
  <br>
  <input type='button' class='button' value='{lang_print id=175}' onClick='parent.TB_remove();parent.deleteVideo();'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
</div>


{* HIDDEN DIV TO DISPLAY EDIT VIDEO *}
<div style='display: none;' id='editvideo'>
  <form action='user_video.php' name='editForm' method='post' target='_parent'>
  <div style='margin-top: 10px;'>{lang_print id=5500082}</div>
  <br />

  <b>{lang_print id=5500078}</b><br>
  <input name='video_title' id='video_title' type='text' class='text' maxlength='50' size='30' value=''>
  <br />
  <br />

  <b>{lang_print id=5500079}</b><br>
  <textarea name='video_desc' id='video_desc' rows='6' cols='50'></textarea>
  <br />

  {* SHOW SEARCH PRIVACY OPTIONS IF ALLOWED BY ADMIN *}
  {if $user->level_info.level_video_search == 1}
    <br>
    <b>{lang_print id=5500014}</b><br>
    <table cellpadding='0' cellspacing='0'>
      <tr><td><label><input type='radio' name='video_search' id='video_search_1' value='1' /> {lang_print id=5500015}</label></td></tr>
      <tr><td><label><input type='radio' name='video_search' id='video_search_0' value='0' /> {lang_print id=5500016}</label></td></tr>
    </table>
  {/if}

  {* SHOW PRIVACY OPTIONS IF ALLOWED BY ADMIN *}
  {if $privacy_options|@count > 1}
    <br />
    <b>{lang_print id=5500017}</b><br>
    <table cellpadding='0' cellspacing='0'>
    {foreach from=$privacy_options name=privacy_loop key=k item=v}
      <tr>
      <td><label><input type='radio' name='video_privacy' id='privacy_{$k}' value='{$k}' /> {lang_print id=$v}</label></td>
      </tr>
    {/foreach}
    </table>
  {/if}

  {* SHOW COMMENT OPTIONS IF ALLOWED BY ADMIN *}
  {if $comment_options|@count > 1}
    <br />
    <b>{lang_print id=5500018}</b><br>
    <table cellpadding='0' cellspacing='0'>
    {foreach from=$comment_options name=comment_loop key=k item=v}
      <tr>
      <td><label><input type='radio' name='video_comments' id='comments_{$k}' value='{$k}' /> {lang_print id=$v}</label></td>
      </tr>
    {/foreach}
    </table>
  {/if}

  <br>
  <input type='submit' class='button' value='{lang_print id=173}' />
  <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();' />
  <input type='hidden' name='task' value='edit' />
  <input type='hidden' name='video_id' id='video_id' value='0' />
  </form>
  <br />
  <br />
</div>

{include file='footer.tpl'}