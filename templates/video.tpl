{include file='header.tpl'}

{* $Id: video.tpl 179 2009-06-16 05:07:47Z phil $ *}

<div class='page_header'>
  {capture assign="owner_url"}{$url->url_create('profile', $owner->user_info.user_username)}&v=video{/capture}
  {lang_sprintf id=5500076 1=$owner_url 2=$owner->user_displayname}
</div>


{* LEFT COLUMN - VIDEO PLAYER AND COMMENTS *}
<table cellpadding='0' cellspacing='0' border='0' style='margin-top: 10px;' width='100%'>
<tr>
<td valign='top' align='left'>
    
  {* VIDEO HEADER - TITLE AND TEXT *}
  <div style='padding:2px;border:1px solid #c0c0c0;'>
    <div class='videoHeader' style='width:{$setting.setting_video_width}px'>
      <div class='inner' style='cursor:default'>
        <h1>{$video_info.video_title}</h1>
        <h2 id='short_desc'>{$video_info.video_desc|truncate:100:"...":true}{if $video_info.video_desc|count_characters:true > 100} (<a href='javascript:void(0);' onClick="$('short_desc').style.display = 'none'; $('long_desc').style.display = 'block';">{lang_print id=5500101}</a>){/if}</h2>
        {if $video_info.video_desc|count_characters:true > 100}
          <h2 id='long_desc' style='display:none'>{$video_info.video_desc|nl2br} (<a href='javascript:void(0);' onClick="$('short_desc').style.display = 'block'; $('long_desc').style.display = 'none';">{lang_print id=5500027}</a>)</h2>
        {/if}
      </div>          
    </div>
        
    {* LOAD FLASH PLAYER *}
    {if $video_info.video_type == 1}

<object width="{$setting.setting_video_width}" height="{$setting.setting_video_height}"><param name="wmode" value="transparent"></param><param name="movie" value="http://www.youtube.com/v/{$video_info.video_youtube_code}&hl=en&fs=1"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed wmode="transparent" src="http://www.youtube.com/v/{$video_info.video_youtube_code}&hl=en&fs=1" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="{$setting.setting_video_width}" height="{$setting.setting_video_height}"></embed></object>

    {/if}
    {if $video_info.video_type == 0}
    {literal}
    <script type="text/javascript" src="./include/flow_player/flashembed-1.0.1.pack.js"></script>
    <script type='text/javascript'>
    <!--
      flashembed("sevideo_player", 
      {
        src:"./include/flow_player/flowplayer-3.1.0.swf",
        width: {/literal}{$setting.setting_video_width}{literal}, 
        height: {/literal}{$setting.setting_video_height}{literal},
        wmode: 'transparent'
      },
      {
        config: {
          clip: {
            url: '{/literal}{$url->url_base}{$video_info.video_dir|replace:'./':''}{$video_info.video_id}.flv{literal}',
            autoPlay: false,
            autoBuffering: true
          },
          plugins: {
            controls: {
              background: '#000000',
              bufferColor: '#333333',
              progressColor: '#444444',
              buttonColor: '#444444',
              buttonOverColor: '#666666'
            }
          },
          canvas: { 
            backgroundColor:'#000000'
          }
        }
      });
    //-->
    </script>
    {/literal}      
    {/if}
    {* DISPLAY FLASH PLAYER *}
    <div id='sevideo_player'></div>
  </div>  
      

  {* SHOW VIDEO DETAILS *}
  <div class='videoInfo' style='width:{$setting.setting_video_width}px'>
        
    <div id='tab'>

      {* SHOW RATING (NOT ALLOWED TO RATE) *}
      {if !$allowed_to_rate}
	<div id='video_rating' style='float: left;'>
          {section name=full_loop start=0 loop=$video_info.video_rating_full}
	    <img src='./images/icons/video_rating_full.gif' border='0'>
	  {/section}
	  {if $video_info.video_rating_part}<img src='./images/icons/video_rating_part.gif' border='0'>{/if}
          {section name=none_loop start=0 loop=$video_info.video_rating_none}
	    <img src='./images/icons/video_rating_none.gif' border='0'>
	  {/section}
	</div>

      {* SHOW RATING (ALLOWED TO RATE) *}
      {else}
	<div id='video_rating' onmouseout='rating_out()' style='float: left;'>
	  {assign var="rating" value="0"}
          {section name=full_loop start=0 loop=$video_info.video_rating_full}
	    {assign var="rating" value=$rating+1}
	    <img src='./images/icons/video_rating_full.gif' border='0' onmouseover="rating_over({$rating});" onclick="rate({$rating});" id='rate_{$rating}' style='cursor:pointer;'>
	  {/section}
	  {if $video_info.video_rating_part}
	    {assign var="rating" value=$rating+1}
	    <img src='./images/icons/video_rating_part.gif' border='0' onmouseover="rating_over({$rating});" onclick="rate({$rating});" id='rate_{$rating}' style='cursor:pointer;'>
          {/if}
          {section name=none_loop start=0 loop=$video_info.video_rating_none}
	    {assign var="rating" value=$rating+1}
	    <img src='./images/icons/video_rating_none.gif' border='0' onmouseover="rating_over({$rating});" onclick="rate({$rating});" id='rate_{$rating}' style='cursor:pointer;'>
	  {/section}
	</div>

	{* RATING JAVASCRIPT *}
	{literal}
	<script type='text/javascript'>
	<!--

	  preload_full = new Image();
	  preload_full.src = "./images/icons/video_rating_full.gif";
	  preload_partial = new Image();
	  preload_partial.src = "./images/icons/video_rating_part.gif";
	  preload_empty = new Image();
	  preload_empty.src = "./images/icons/video_rating_none.gif";

	  function rating_over(rating) {
	    for(var x=1; x<=5; x++) {
	      if(x <= rating) {
	        $('rate_'+x).src = preload_full.src;
	      } else {
	        $('rate_'+x).src = preload_empty.src;
	      }
	    }
	  }

	  function rating_out() {
	    for(var x=1; x<=5; x++) {
	      if(x <= {/literal}{$video_info.video_rating_full}{literal}) {
	        $('rate_'+x).src = preload_full.src;
	      } else if({/literal}{$video_info.video_rating_part}{literal} != 0 && x == {/literal}{math equation='x+1' x=$video_info.video_rating_full}{literal}) {
	        $('rate_'+x).src = preload_partial.src;
	      } else {
	        $('rate_'+x).src = preload_empty.src;
	      }
	    }
	  }

	  function rate(rating) {
	    $('video_rating').onmouseout = null;
	    var request = new Request.JSON({
	      'url' : 'video.php',
	      'method' : 'post',
	      'secure' : false,
	      'data' : {
	        'task'  : 'rate_do',
	        'user'  : '{/literal}{$owner->user_info.user_username}{literal}',
	        'video_id'  : '{/literal}{$video_info.video_id}{literal}',
		'rating' : rating
	      },
	      'onComplete' : function(responseObject, responseText)
	      {
	        rating_result(responseObject);
	      }
	    });
	    request.send();
	  }

	  function rating_result(rating) {
	    $('rating_total').innerHTML = rating.rating_total;
	    for(var x=1; x<=5; x++) {
	      if(x <= rating.rating_full) {
	        $('rate_'+x).src = preload_full.src;
	        if(!rating.allowed_to_rate) { $('rate_'+x).onmouseover = null; $('rate_'+x).onclick = null; }
	      } else if(rating.rating_part != 0 && x == rating.rating_full+1) {
	        $('rate_'+x).src = preload_partial.src;
	        if(!rating.allowed_to_rate) { $('rate_'+x).onmouseover = null; $('rate_'+x).onclick = null; }
	      } else {
	        $('rate_'+x).src = preload_empty.src;
	        if(!rating.allowed_to_rate) { $('rate_'+x).onmouseover = null; $('rate_'+x).onclick = null; }
	      }
	    }
	    if(!rating.allowed_to_rate) { $('video_rating').onmouseout = null; }
	  }

	//-->
	</script>
	{/literal}  

      {/if}

      {* SHOW STATS *}
      <div style='float: left; padding-left: 15px; padding-top: 5px;'>
	{assign var="uploaddate" value=$datetime->time_since($video_info.video_datecreated)}
	{capture assign="uploaded"}{lang_sprintf id=$uploaddate[0] 1=$uploaddate[1]}{/capture}
	{lang_sprintf id=5500024 1=$uploaded}
      </div>
      <div style='float: left; padding-left: 15px; padding-top: 5px;'>{lang_sprintf id=5500037 1=$video_info.video_rating_total}</div>
      <div style='float: left; padding-left: 15px; padding-top: 5px;'>{lang_sprintf id=5500038 1=$video_info.video_views}</div>
      <div style='float: left; padding-left: 15px; padding-top: 5px;'><a href="javascript:TB_show('{lang_print id=5500147}', 'user_report.php?return_url={$url->url_current()|escape:url}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');">{lang_print id=5500147}</a></div>
      <div style='clear: both; height: 0px;'></div>

    </div>

  </div>

  <br>

  {* COMMENTS *}

  <div id="video_{$video_info.video_id}_postcomment"></div>
  <div id="video_{$video_info.video_id}_comments" style='margin-left: auto; margin-right: auto;'></div>
      
  {lang_javascript ids=39,155,175,182,183,184,185,187,784,787,829,830,831,832,833,834,835,854,856,891,1025,1026,1032,1034,1071}
  <script type="text/javascript">
        
    SocialEngine.VideoComments = new SocialEngineAPI.Comments({ldelim}
      'canComment' : {if $allowed_to_comment}true{else}false{/if},
      'commentHTML' : '{$setting.setting_comment_html|replace:",":", "}',
      'commentCode' : {if $setting.setting_comment_code}true{else}false{/if},

      'type' : 'video',
      'typeIdentifier' : 'video_id',
      'typeID' : {$video_info.video_id},
          
      'typeTab' : 'videos',
      'typeCol' : 'video',
          
      'initialTotal' : {$total_comments|default:0}
    {rdelim});
        
    SocialEngine.RegisterModule(SocialEngine.VideoComments);
       
    // Backwards
    function addComment(is_error, comment_body, comment_date)
    {ldelim}
      SocialEngine.VideoComments.addComment(is_error, comment_body, comment_date);
    {rdelim}
        
    function getComments(direction)
    {ldelim}
      SocialEngine.VideoComments.getComments(direction);
    {rdelim}

  </script>


{* RIGHT COLUMN - USER'S OTHER VIDEOS *}
</td>
<td valign='top' align='left' width='100%'>

  <div id='videoTabFrame'>

    <div class='header'>{lang_sprintf id=5500022 1=$owner->user_displayname_short} ({$total_videos})</div>
    <div class='video_box' style='margin-bottom: 10px; padding-bottom: 10px; height: 395px; overflow: auto;'>

      {* LOOP THROUGH USER VIDEOS *}
      {section name=video_loop loop=$videos}    

        {* ENSURE VIDEO TITLE ISN'T BLANK *}
        {if $videos[video_loop].video_title == ""}{capture assign="video_title"}{lang_print id=589}{/capture}{else}{assign var="video_title" value=$videos[video_loop].video_title}{/if}

	<div class='video_row' style='{if $smarty.section.video_loop.first}padding-top: 0px; border-top: none;{/if}'>
	  <table cellpadding='0' cellspacing='0'>
	  <tr>
	  <td style='vertical-align: top;' width='1'>
            <div class='video_row_nowplaying{if $videos[video_loop].video_id == $video_info.video_id}2{/if}'{if $videos[video_loop].video_id != $video_info.video_id}onMouseOver="this.className='video_row_nowplaying2'" onMouseOut="this.className='video_row_nowplaying'"{/if}><a href='{$url->url_create("video", $owner->user_info.user_username, $videos[video_loop].video_id)}'><img src='{if $videos[video_loop].video_thumb}{$videos[video_loop].video_dir}{$videos[video_loop].video_id}_thumb.jpg{else}./images/video_placeholder.gif{/if}' border='0' width='{$setting.setting_video_thumb_width}' height='{$setting.setting_video_thumb_height}'></a></div>
	  </td>
	  <td style='vertical-align: top; padding-left: 7px;'>
            <div class='video_row_title'><a href='{$url->url_create("video", $owner->user_info.user_username, $videos[video_loop].video_id)}'>{$video_title|truncate:45:'...':true}</a></div>
            <div class='video_row_info'>
{if $videos[video_loop].video_type==0}{$videos[video_loop].video_duration_in_min} - {/if}{lang_sprintf id=5500023 1=$videos[video_loop].total_comments} - {lang_sprintf id=5500070 1=$videos[video_loop].video_views}</div>
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
      {/section}
 
    </div>
          
  </div>


</td>
</tr>
</table>




{include file='footer.tpl'}