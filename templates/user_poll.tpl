{include file='header.tpl'}

{* $Id: user_poll.tpl 12 2009-01-11 06:04:12Z john $ *}

<img src='./images/icons/poll_poll48.gif' border='0' class='icon_big'>
<div class='page_header'>{lang_print id=2500037}</div>
<div>{lang_print id=2500039}</div>

<div style='margin-top: 20px;'>
  <div class='button' style='float: left;'>
    <a href='user_poll_new.php'><img src='./images/icons/poll_new16.gif' border='0' class='button'>{lang_print id=2500040}</a>
  </div>
  <div class='button' style='float: left; padding-left: 20px;'>
    <a href="javascript:void(0);" onClick="this.blur();if($('poll_search').style.display=='none') {literal}{{/literal} $('poll_search').style.display='block'; $('poll_searchtext').focus(); {literal}} else {{/literal} $('poll_search').style.display='none'; {literal}}{/literal}"><img src='./images/icons/search16.gif' border='0' class='button'>{lang_print id=2500041}</a>
  </div>
  <div style='clear: both; height: 0px;'></div>
</div>

{* SHOW SEARCH FIELD IF ANY ENTRIES EXIST *}
<div class='poll_search' id='poll_search' style='width: 550px; margin-top: 10px; text-align: center;{if $search == ""} display: none;{/if}'>
  <form action='user_poll.php' name='searchform' method='post'>
  <table cellpadding='0' cellspacing='0' align='center'>
    <tr>
      <td>{lang_print id=2500042}&nbsp;</td>
      <td><input type='text' name='search' maxlength='100' size='30' value='{$search}' class='text' id='poll_searchtext'>&nbsp;</td>
      <td>{lang_block id=646 var=langBlockTemp}<input type='submit' class='button' value='{$langBlockTemp}'>{/lang_block}</td>
    </tr>
  </table>
  <input type='hidden' name='s' value='{$s}'>
  <input type='hidden' name='p' value='{$p}'>
  </form>
</div>

{* DISPLAY PAGINATION MENU IF APPLICABLE *}
{if $maxpage > 1}
  <div class='center'>
    {if $p != 1}
      <a href='user_poll.php?s={$s}&search={$search}&p={math equation="p-1" p=$p}'>&#171; {lang_print id=182}</a>
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
      <a href='user_poll.php?s={$s}&search={$search}&p={math equation="p+1" p=$p}'>{lang_print id=183} &#187;</a>
    {else}
      <font class='disabled'>{lang_print id=183} &#187;</font>
    {/if}
  </div>
  <br />
{/if}

{* JAVASCRIPT *}
{lang_javascript ids=2500046,2500047,2500055,2500114,2500115}

<script type='text/javascript' src="./include/js/class_poll.js"></script>
<script type='text/javascript'>
<!--
  SocialEngine.Polls = new SocialEngineAPI.Polls();
  SocialEngine.RegisterModule(SocialEngine.Polls);
//-->
</script>

  
{section name=poll_loop loop=$polls}
<div style='width: 550px;' id='sePoll_{$polls[poll_loop]->poll_info.poll_id}' class="sePollRow poll">
  <table cellpadding='0' cellspacing='0' width='100%'>
    <tr>
      <td class='poll_left' width='100%'>
        <div class='poll_title'>
          {$polls[poll_loop]->poll_info.poll_title|truncate:30:"...":true}
        </div>
        <div class='poll_stats'>
          {assign var='poll_datecreated' value=$datetime->time_since($polls[poll_loop]->poll_info.poll_datecreated)}
          {lang_sprintf id=2500028 1=$polls[poll_loop]->poll_info.poll_totalvotes}
          - {lang_sprintf id=2500122 1=$polls[poll_loop]->poll_info.poll_views}
          - {lang_sprintf id=507 1=$polls[poll_loop]->poll_info.total_comments}
          - {lang_sprintf id=$poll_datecreated[0] 1=$poll_datecreated[1]}
        </div>
        {if $polls[poll_loop]->poll_info.poll_desc != ""}
          <div style='margin-top: 8px; margin-bottom: 8px;'>{$polls[poll_loop]->poll_info.poll_desc|escape:html|truncate:197:"...":true}</div>
        {/if}
        <div class='poll_options'>
          {* VIEW *}
          <div style='float: left;'><a href='{$url->url_create("poll", $user->user_info.user_username, $polls[poll_loop]->poll_info.poll_id)}'><img src='./images/icons/poll_poll16.gif' border='0' class='button'>{lang_print id=2500121}</a></div>
            
          {* EDIT *}
          <div style='float: left; padding-left: 15px;'><a href='user_poll_edit.php?poll_id={$polls[poll_loop]->poll_info.poll_id}'><img src='./images/icons/poll_edit16.gif' border='0' class='button'>{lang_print id=2500057}</a></div>
            
          {* OPEN/CLOSE *}
          <div class="sePollsClose" style='float: left; padding-left: 15px;{if  $polls[poll_loop]->poll_info.poll_closed} display: none;{/if}'><a href='javascript:void(0);' onclick="SocialEngine.Polls.togglePoll({$polls[poll_loop]->poll_info.poll_id}, true );"><img src='./images/icons/poll_close16.gif' border='0' class='button'>{lang_print id=2500047}</a></div>
          <div class="sePollsOpen"  style='float: left; padding-left: 15px;{if !$polls[poll_loop]->poll_info.poll_closed} display: none;{/if}'><a href='javascript:void(0);' onclick="SocialEngine.Polls.togglePoll({$polls[poll_loop]->poll_info.poll_id}, false);"><img src='./images/icons/poll_open16.gif' border='0' class='button'>{lang_print id=2500046}</a></div>
            
          {* DELETE *}
          <div class="sePollsDelete" style='float: left; padding-left: 15px;'><a href='javascript:void(0);' onclick="SocialEngine.Polls.deletePoll({$polls[poll_loop]->poll_info.poll_id});"><img src='./images/icons/poll_delete16.gif' border='0' class='button'>{lang_print id=2500048}</a></div>
          <div style='clear: both; height: 0px;'></div>
        </div>
      </td>
    </tr>
  </table>
</div>
{/section}

<div style='clear: both; height: 0px;'></div>

{* HIDDEN DIV TO DISPLAY DELETE CONFIRMATION MESSAGE *}
<div style='display: none;' id='confirmpolldelete'>
  <div style='margin-top: 10px;'>
    {lang_print id=2500056}
  </div>
  <br>
  <input type='button' class='button' value='{lang_print id=175}' onClick='parent.TB_remove();parent.SocialEngine.Polls.deletePollConfirm(parent.SocialEngine.Polls.currentConfirmDeleteID);'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
</div>

{* DISPLAY PAGINATION MENU IF APPLICABLE *}
{if $maxpage > 1}
  <div class='center'>
    {if $p != 1}
      <a href='user_poll.php?s={$s}&search={$search}&p={math equation="p-1" p=$p}'>&#171; {lang_print id=182}</a>
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
      <a href='user_poll.php?s={$s}&search={$search}&p={math equation="p+1" p=$p}'>{lang_print id=183} &#187;</a>
    {else}
      <font class='disabled'>{lang_print id=183} &#187;</font>
    {/if}
  </div>
  <br />
{/if}



{* SHOW NULL MESSAGE *}
{if $total_polls == 0 && !empty($search)}

  <br>
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='result'><img src='./images/icons/bulb16.gif' border='0' class='icon'>{lang_print id=2500043}</td></tr>
  </table>
  
{/if}



<div{if $total_polls>0} style='display: none;'{/if} id='pollnullmessage'>
  <br>    
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='result'><img src='./images/icons/bulb16.gif' border='0' class='icon'>{lang_print id=2500044}</td></tr>
  </table>
</div>


{include file='footer.tpl'}