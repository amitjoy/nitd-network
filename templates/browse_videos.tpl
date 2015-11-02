{include file='header.tpl'}

{* $Id: browse_videos.tpl 13 2009-01-11 06:04:29Z john $ *}

<div class='page_header'>{lang_print id=5500029}</div>

<div style='padding: 7px 10px 7px 10px; background: #F2F2F2; border: 1px solid #BBBBBB; margin: 10px 0px 10px 0px; font-weight: bold;'>
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td>
    {lang_print id=5500071}&nbsp;
  </td>
  <td>
    <select class='small' name='v' onchange="window.location.href='browse_videos.php?s={$s}&v='+this.options[this.selectedIndex].value;">
    <option value='0'{if $v == "0"} SELECTED{/if}>{lang_print id=5500072}</option>
    {if $user->user_exists}<option value='1'{if $v == "1"} SELECTED{/if}>{lang_print id=5500073}</option>{/if}
    </select>
  </td>
  <td style='padding-left: 20px;'>
    {lang_print id=5500074}&nbsp;
  </td>
  <td>
    <select class='small' name='s' onchange="window.location.href='browse_videos.php?v={$v}&s='+this.options[this.selectedIndex].value;">
    <option value='video_datecreated DESC'{if $s == "video_dateupdated DESC"} SELECTED{/if}>{lang_print id=5500075}</option>
    <option value='video_views DESC'{if $s == "video_views DESC"} SELECTED{/if}>{lang_print id=5500144}</option>
    <option value='video_cache_rating_weighted DESC'{if $s == "video_cache_rating_weighted DESC"} SELECTED{/if}>{lang_print id=5500156}</option>
    </select>
  </td>
  </tr>
  </table>
</div>


{* DISPLAY PAGINATION MENU IF APPLICABLE *}
{if $maxpage > 1}
  <div style='text-align: center; padding-bottom: 10px;'>
  {if $p != 1}<a href='browse_videos.php?s={$s}&v={$v}&p={math equation='p-1' p=$p}'>&#171; {lang_print id=182}</a>{else}&#171; {lang_print id=182}{/if}
  &nbsp;|&nbsp;&nbsp;
  {if $p_start == $p_end}
    <b>{lang_sprintf id=184 1=$p_start 2=$total_videos}</b>
  {else}
    <b>{lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_videos}</b>
  {/if}
  &nbsp;&nbsp;|&nbsp;
  {if $p != $maxpage}<a href='browse_videos.php?s={$s}&v={$v}&p={math equation='p+1' p=$p}'>{lang_print id=183} &#187;</a>{else}{lang_print id=183} &#187;{/if}
  </div>
{/if}


<div>

  {section name=video_loop loop=$videos}

    {* ENSURE VIDEO TITLE ISN'T BLANK *}
    {if $videos[video_loop].video_title == ""}{capture assign="video_title"}{lang_print id=589}{/capture}{else}{assign var="video_title" value=$videos[video_loop].video_title}{/if}

      <div class='videoTab' style='width: 275px;'>
	<table cellpadding='0' cellspacing='0'>
	<tr>
	<td style='vertical-align: top;'>
	  <a href='{$url->url_create("video", $videos[video_loop].video_author->user_info.user_username, $videos[video_loop].video_id)}'><img src='{if $videos[video_loop].video_thumb}{$videos[video_loop].video_dir}{$videos[video_loop].video_id}_thumb.jpg{else}./images/video_placeholder.gif{/if}' border='0' width='{$setting.setting_video_thumb_width}' height='{$setting.setting_video_thumb_height}'></a>
	</td>
	<td style='vertical-align: top; padding-left: 5px;'>
          <div class='video_row_title'><a href='{$url->url_create("video", $videos[video_loop].video_author->user_info.user_username, $videos[video_loop].video_id)}'>{$video_title|truncate:55:'...':true}</a></div>
          <div class='video_row_info'>{lang_sprintf id=5500023 1=$videos[video_loop].total_comments} - {lang_sprintf id=5500070 1=$videos[video_loop].video_views}</div>
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
      {cycle values=",,<div style='clear: both; height: 0px;'></div>"}

  {/section}
  <div style='clear: both; height: 0px;'></div>

</div>



{include file='footer.tpl'}