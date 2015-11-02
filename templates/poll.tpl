{include file='header.tpl'}

{* $Id: poll.tpl 162 2009-04-30 01:43:11Z john $ *}

<div class='page_header'>{lang_sprintf id=2500027 1=$owner->user_displayname 2=$url->url_create("profile", $owner->user_info.user_username) 3=$url->url_create("polls", $owner->user_info.user_username)}</div>
<br />


{* JAVASCRIPT FOR POLLS *}
{lang_javascript ids=2500028,2500034,2500114,2500115}

<script type='text/javascript' src="./include/js/class_poll.js"></script>
<script type='text/javascript'>
<!--
  SocialEngine.Polls = new SocialEngineAPI.Polls();
  SocialEngine.RegisterModule(SocialEngine.Polls);
  
  {if $poll_object->poll_info.poll_viewonly}
  window.addEvent('domready', function()
  {ldelim}
    SocialEngine.Polls.getPollData({$poll_object->poll_info.poll_id});
  {rdelim});
  {/if}
//-->
</script>


{* POLL RESULTS TEMPLATE *}
<div id="pollResultTemplate" style="display:none;">
  <div class="pollResult">
    <div class="pollResultLabel"></div>
    <div class="pollResultBar"></div>
    <span class="pollResultPercentage"></span>
    <span class="pollResultVotes"></span>
  </div>
</div>


{* SHOW THIS POLL *}
<table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
    <td valign='top' class='poll_view' id="sePoll{$poll_object->poll_info.poll_id}">
      
      {* TITLE AND DESCRIPTION *}
      <div class='poll_view_title'>
        {$poll_object->poll_info.poll_title|truncate:75:"...":true}
      </div>
      <div class='poll_view_stats'>
        {capture name=totalVotesCode}<span id='poll{$poll_object->poll_info.poll_id}_totalvotes'>{$poll_object->poll_info.poll_totalvotes}</span>{/capture}
        {lang_sprintf id=2500029 1=$datetime->cdate("`$setting.setting_dateformat`", $datetime->timezone("`$poll_object->poll_info.poll_datecreated`", $global_timezone))}
        {lang_sprintf id=2500028 1=$smarty.capture.totalVotesCode},
        {lang_sprintf id=507 1=$total_comments},
        {lang_sprintf id=2500122 1=$poll_object->poll_info.poll_views}
      </div>
      <div style='padding: 5px;'>
        {$poll_object->poll_info.poll_desc|choptext:120:"<br>"}
      </div>
      
      {* RESULTS *}
      <div style='padding: 5px; font-weight: bold; display: none;' id='poll{$poll_object->poll_info.poll_id}_results'></div>
      
      {* OPTIONS *}
      <div style='padding: 5px;' id='poll{$poll_object->poll_info.poll_id}_vote'>
        {counter start=-1 print=0}
        {section name=options_loop loop=$poll_object->poll_info.poll_options}
          <div style='padding: 3px 3px 3px 0px;'>
            <table cellpadding='0' cellspacing='0'><tr>
              <td>
                <input type='radio' name="pollVoteSelect_{$poll_object->poll_info.poll_id}" class="pollVoteOption" value='{counter}'>
              </td>
              <td style='font-weight: bold;'>
                <label for='poll{$poll_object->poll_info.poll_id}_option{$smarty.section.options_loop.iteration}'>{$poll_object->poll_info.poll_options[options_loop]}</label>
              </td>
            </tr></table>
          </div>
        {/section}
      </div>
      
      {* VOTE OR VIEW *}
      <div id="poll{$poll_object->poll_info.poll_id}_vote_actions" style='padding: 5px; margin-top: 10px;'>
        {if $user->level_info.level_poll_allow & 2}<a href="javascript:void(0);" onclick="SocialEngine.Polls.sendPollVote({$poll_object->poll_info.poll_id});">{lang_print id=2500030}</a> |{/if}
        <a href="javascript:void(0);" onclick="SocialEngine.Polls.getPollData({$poll_object->poll_info.poll_id});">{lang_print id=2500032}</a>
      </div>
      
      {* VOTE OR VIEW *}
      <div id="poll{$poll_object->poll_info.poll_id}_results_actions" style='padding: 5px; margin-top: 10px; display:none;'>
        <a href="javascript:void(0);" onclick="SocialEngine.Polls.pollVoteMode({$poll_object->poll_info.poll_id});">{lang_print id=2500087}</a>
      </div>
      
    </td>
  </tr>
</table>
<br />


<div>
  <a href='{$url->url_create("polls", $owner->user_info.user_username)}'>
    <img src='./images/icons/back16.gif' border='0' class='icon'>
    {lang_sprintf id=2500033 1=$owner->user_displayname}
  </a>
  &nbsp;&nbsp;&nbsp;
  {lang_block id=861 var=langBlockTemp}<a href="javascript:TB_show('{$langBlockTemp}', 'user_report.php?return_url={$url->url_current()|escape:url}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');"><img src='./images/icons/report16.gif' border='0' class='icon'>{$langBlockTemp}</a>{/lang_block}
</div>
<br />



{* DISPLAY POST COMMENT BOX *}
<div style='margin-left: auto; margin-right: auto;'>

  <div id="poll_{$poll_object->poll_info.poll_id}_postcomment"></div>
  <div id="poll_{$poll_object->poll_info.poll_id}_comments" style='margin-left: auto; margin-right: auto;'></div>
  
  {lang_javascript ids=39,155,175,182,183,184,185,187,784,787,829,830,831,832,833,834,835,854,856,891,1025,1026,1032,1034,1071}
  
  <script type="text/javascript">
    
    SocialEngine.PollComments = new SocialEngineAPI.Comments({ldelim}
      'canComment' : {if $allowed_to_comment}true{else}false{/if},
      'commentCode' : {if $setting.setting_comment_code}true{else}false{/if},
      'commentHTML' : '{$setting.setting_comment_html}',
      
      'type' : 'poll',
      'typeIdentifier' : 'poll_id',
      'typeID' : {$poll_object->poll_info.poll_id},
      
      'typeTab' : 'polls',
      'typeCol' : 'poll',
      
      'initialTotal' : {$total_comments|default:0},
      
      'paginate' : false,
      'cpp' : 20
    {rdelim});
    
    SocialEngine.RegisterModule(SocialEngine.PollComments);
    
    // Backwards
    function addComment(is_error, comment_body, comment_date)
    {ldelim}
      SocialEngine.PollComments.addComment(is_error, comment_body, comment_date);
    {rdelim}
    
    function getComments(direction)
    {ldelim}
      SocialEngine.PollComments.getComments(direction);
    {rdelim}
    
  </script>
  
</div>


{include file='footer.tpl'}