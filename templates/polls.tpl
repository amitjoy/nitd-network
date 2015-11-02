{include file='header.tpl'}

{* $Id: polls.tpl 59 2009-02-13 03:25:54Z john $ *}

<div class='page_header'>{lang_sprintf id=2500027 1=$owner->user_displayname 2=$url->url_create("profile", $owner->user_info.user_username) 3=$url->url_create("polls", $owner->user_info.user_username)}</div>
<br />


{* JAVASCRIPT *}
{lang_javascript ids=2500028,2500034,2500114,2500115}

<script type='text/javascript' src="./include/js/class_poll.js"></script>
<script type='text/javascript'>
<!--
  SocialEngine.Polls = new SocialEngineAPI.Polls();
  SocialEngine.RegisterModule(SocialEngine.Polls);
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


{* DISPLAY PAGINATION MENU IF APPLICABLE *}
{if $maxpage > 1}
  <div class='center'>
    {if $p != 1}
      <a href='polls.php?user={$owner->user_info.user_username}&s={$s}&search={$search}&p={math equation="p-1" p=$p}'>&#171; {lang_print id=182}</a>
    {else}
      <font class='disabled'>&#171; {lang_print id=182}</font>
    {/if}
    &nbsp;|&nbsp;
    {if $p_start==$p_end}
      {lang_sprintf id=2500035 1=$p_start 2=$total_polls}
    {else}
      {lang_sprintf id=2500036 1=$p_start 2=$p_end 3=$total_polls}
    {/if}
    &nbsp;|&nbsp;
    {if $p != $maxpage}
      <a href='polls.php?user={$owner->user_info.user_username}&s={$s}&search={$search}&p={math equation="p+1" p=$p}'>{lang_print id=183} &#187;</a>
    {else}
      <font class='disabled'>{lang_print id=183} &#187;</font>
    {/if}
  </div>
  <br />
{/if}


{* SHOW THIS USERS POLLS *}
{section name=polls_loop loop=$polls}

  {if $polls[polls_loop]->poll_info.poll_viewonly}
  <script type='text/javascript'>
  <!--
    window.addEvent('domready', function()
    {ldelim}
      SocialEngine.Polls.getPollData({$polls[polls_loop]->poll_info.poll_id});
    {rdelim});
  //-->
  </script>
  {/if}
  
  {if $smarty.section.polls_loop.first != true}
    <div style='margin-top: 10px; font-size: 1pt; height: 1px;'>&nbsp;</div>
  {/if}

  {* SHOW THIS POLL *}
  <table cellpadding='0' cellspacing='0' width='100%'>
    <tr>
      <td valign='top' class='poll_view' id="sePoll{$polls[polls_loop]->poll_info.poll_id}">
        
        {* TITLE AND DESCRIPTION *}
        <div class='poll_view_title'>
          <a href="{$url->url_create('poll', $owner->user_info.user_username, $polls[polls_loop]->poll_info.poll_id)}">
            {$polls[polls_loop]->poll_info.poll_title|truncate:75:"...":true}
          </a>
        </div>
        <div class='poll_view_stats'>
          {capture name=totalVotesCode}<span id='poll{$polls[polls_loop]->poll_info.poll_id}_totalvotes'>{$polls[polls_loop]->poll_info.poll_totalvotes}</span>{/capture}
          {lang_sprintf id=2500029 1=$datetime->cdate("`$setting.setting_dateformat`", $datetime->timezone("`$polls[polls_loop]->poll_info.poll_datecreated`", $global_timezone))}
          {lang_sprintf id=2500028 1=$smarty.capture.totalVotesCode},
          {lang_sprintf id=507 1=$total_comments},
          {lang_sprintf id=2500122 1=$polls[polls_loop]->poll_info.poll_views}
        </div>
        <div style='padding: 5px;'>
          {$polls[polls_loop]->poll_info.poll_desc|choptext:120:"<br>"}
        </div>
        
        {* RESULTS *}
        <div style='padding: 5px; font-weight: bold; display: none;' id='poll{$polls[polls_loop]->poll_info.poll_id}_results'></div>
        
        {* OPTIONS *}
        <div style='padding: 5px;' id='poll{$polls[polls_loop]->poll_info.poll_id}_vote'>
          {counter start=-1 print=0}
          {section name=options_loop loop=$polls[polls_loop]->poll_info.poll_options}
            <div style='padding: 3px 3px 3px 0px;'>
              <table cellpadding='0' cellspacing='0'><tr>
                <td>
                  <input type='radio' name="pollVoteSelect_{$polls[polls_loop]->poll_info.poll_id}" class="pollVoteOption" value='{counter}'>
                </td>
                <td style='font-weight: bold;'>
                  <label for='poll{$polls[polls_loop]->poll_info.poll_id}_option{$smarty.section.options_loop.iteration}'>{$polls[polls_loop]->poll_info.poll_options[options_loop]}</label>
                </td>
              </tr></table>
            </div>
          {/section}
        </div>
        
        {* VOTE OR VIEW *}
        <div id="poll{$polls[polls_loop]->poll_info.poll_id}_vote_actions" style='padding: 5px; margin-top: 10px;'>
          {if $user->level_info.level_poll_allow & 2}<a href="javascript:void(0);" onclick="SocialEngine.Polls.sendPollVote({$polls[polls_loop]->poll_info.poll_id});">{lang_print id=2500030}</a> |{/if}
          <a href="javascript:void(0);" onclick="SocialEngine.Polls.getPollData({$polls[polls_loop]->poll_info.poll_id});">{lang_print id=2500032}</a>
        </div>
        
        {* GO BACK TO OPTIONS *}
        <div id="poll{$polls[polls_loop]->poll_info.poll_id}_results_actions" style='padding: 5px; margin-top: 10px; display:none;'>
          <a href="javascript:void(0);" onclick="SocialEngine.Polls.pollVoteMode({$polls[polls_loop]->poll_info.poll_id});">{lang_print id=2500087}</a>
        </div>
        
      </td>
    </tr>
  </table>
  <br />

{/section}


{* DISPLAY PAGINATION MENU IF APPLICABLE *}
{if $maxpage > 1}
  <div class='center'>
    {if $p != 1}
      <a href='polls.php?user={$owner->user_info.user_username}&s={$s}&search={$search}&p={math equation="p-1" p=$p}'>&#171; {lang_print id=182}</a>
    {else}
      <font class='disabled'>&#171; {lang_print id=182}</font>
    {/if}
    &nbsp;|&nbsp;
    {if $p_start==$p_end}
      {lang_sprintf id=2500035 1=$p_start 2=$total_polls}
    {else}
      {lang_sprintf id=2500036 1=$p_start 2=$p_end 3=$total_polls}
    {/if}
    &nbsp;|&nbsp;
    {if $p != $maxpage}
      <a href='polls.php?user={$owner->user_info.user_username}&s={$s}&search={$search}&p={math equation="p+1" p=$p}'>{lang_print id=183} &#187;</a>
    {else}
      <font class='disabled'>{lang_print id=183} &#187;</font>
    {/if}
  </div>
  <br>
{/if}


{include file='footer.tpl'}