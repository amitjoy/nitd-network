
{* $Id: profile_video_tab.tpl 143 2009-03-26 09:48:12Z szerrade $ *}

{* BEGIN VIDEOS *}
{if ($owner->level_info.level_video_allow != 0 || $owner->level_info.level_youtube_allow != 0) && $total_videos > 0}


  <div class='profile_headline'>
    {lang_print id=5500098} ({$total_videos})
  </div>

  {* LOOP THROUGH USER VIDEOS *}
  {section name=video_loop loop=$videos}    

    {* ENSURE VIDEO TITLE ISN'T BLANK *}
    {if $videos[video_loop].video_title == ""}{capture assign="video_title"}{lang_print id=589}{/capture}{else}{assign var="video_title" value=$videos[video_loop].video_title}{/if}

    <div class='videoTab' style='width: 300px;'>
      <table cellpadding='0' cellspacing='0'>
      <tr>
      <td style='vertical-align: top;'>
        <a href='{$url->url_create("video", $owner->user_info.user_username, $videos[video_loop].video_id)}'><img src='{if $videos[video_loop].video_thumb}{$videos[video_loop].video_dir}{$videos[video_loop].video_id}_thumb.jpg{else}./images/video_placeholder.gif{/if}' border='0' width='{$setting.setting_video_thumb_width}' height='{$setting.setting_video_thumb_height}'></a>
      </td>
      <td style='vertical-align: top; padding-left: 7px;'>
        <div class='video_row_title'><a href='{$url->url_create("video", $owner->user_info.user_username, $videos[video_loop].video_id)}'>{$video_title|truncate:45:'...':true}</a></div>
        <div class='video_row_info'>{lang_sprintf id=5500070 1=$videos[video_loop].video_views}</div>
        <div>
          {section name=full_loop start=0 loop=$videos[video_loop].video_rating_full}
	    <img src='./images/icons/video_rating_full_small.gif' border='0'>
  	  {/section}
	  {if $videos[video_loop].video_rating_part}<img src='./images/icons/video_rating_part_small.gif' border='0'>{/if}
          {section name=none_loop start=0 loop=$videos[video_loop].video_rating_none}
	    <img src='./images/icons/video_rating_none_small.gif' border='0'>
	  {/section}
        </div>
      </td>
      </tr>
      </table>
    </div>

    {cycle values=",<div style='clear: both; height: 0px;'></div>"}

  {/section}
  <div style='clear: both; height: 0px;'></div>

{/if}