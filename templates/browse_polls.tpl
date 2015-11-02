{include file='header.tpl'}

{* $Id: browse_polls.tpl 246 2009-11-14 03:30:06Z phil $ *}

<div class='page_header'>{lang_print id=2500100}</div>

<div style='padding: 7px 10px 7px 10px; background: #F2F2F2; border: 1px solid #BBBBBB; margin: 10px 0px 10px 0px; font-weight: bold;'>
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td>
    {lang_print id=2500101}&nbsp;
  </td>
  <td>
    <select class='small' name='v' onchange="window.location.href='browse_polls.php?s={$s}&v='+this.options[this.selectedIndex].value;">
      <option value='0'{if $v == "0"} SELECTED{/if}>{lang_print id=2500103}</option>
      {if $user->user_exists}<option value='1'{if $v == "1"} SELECTED{/if}>{lang_print id=2500104}</option>{/if}
    </select>
  </td>
  <td style='padding-left: 20px;'>
    {lang_print id=2500102}&nbsp;
  </td>
  <td>
    <select class='small' name='s' onchange="window.location.href='browse_polls.php?v={$v}&s='+this.options[this.selectedIndex].value;">
      <option value='poll_datecreated DESC'{if $s == "poll_datecreated DESC"} SELECTED{/if}>{lang_print id=2500105}</option>
      <option value='poll_totalvotes DESC'{if $s == "poll_totalvotes DESC"} SELECTED{/if}>{lang_print id=2500106}</option>
      <option value='poll_views DESC'{if $s == "poll_views DESC"} SELECTED{/if}>{lang_print id=2500107}</option>
    </select>
  </td>
  </tr>
  </table>
</div>


{* DISPLAY PAGINATION MENU IF APPLICABLE *}
{if $maxpage > 1}
  <div style='text-align: center; padding-bottom: 10px;'>
  {if $p != 1}<a href='browse_polls.php?s={$s}&v={$v}&p={math equation="p-1" p=$p}'>&#171; {lang_print id=182}</a>{else}&#171; {lang_print id=182}{/if}
  &nbsp;|&nbsp;&nbsp;
  {if $p_start == $p_end}
    <b>{lang_sprintf id=184 1=$p_start 2=$total_polls}</b>
  {else}
    <b>{lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_polls}</b>
  {/if}
  &nbsp;&nbsp;|&nbsp;
  {if $p != $maxpage}<a href='browse_polls.php?s={$s}&v={$v}&p={math equation="p+1" p=$p}'>{lang_print id=183} &#187;</a>{else}{lang_print id=183} &#187;{/if}
  </div>
{/if}



<div>

  {section name=poll_loop loop=$polls}

    <div class='polls_browse_item' style='width: 415px; height: 80px; float: left;'>
      <table cellpadding='0' cellspacing='0'>
      <tr>
      <td style='vertical-align: top; padding-left: 0px;'>
        <div style='font-weight: bold; font-size: 13px;'>
          <img src="./images/icons/poll_poll16.gif" class='button' style='float: left;'>
          <a href='{$url->url_create("poll", $polls[poll_loop]->poll_owner->user_info.user_username, $polls[poll_loop]->poll_info.poll_id)}'>{$polls[poll_loop]->poll_info.poll_title|truncate:30:"...":true}</a>
        </div>
        <div class='polls_browse_date'>
          {assign var='poll_datecreated' value=$datetime->time_since($polls[poll_loop]->poll_info.poll_datecreated)}{capture assign="created"}{lang_sprintf id=$poll_datecreated[0] 1=$poll_datecreated[1]}{/capture}
          {lang_sprintf id=2500108 1=$created 2=$url->url_create("profile", $polls[poll_loop]->poll_owner->user_info.user_username) 3=$polls[poll_loop]->poll_owner->user_displayname}
        </div>
        <div style="margin-top: 5px;">
          {lang_sprintf id=2500028 1=$polls[poll_loop]->poll_info.poll_totalvotes},
          {lang_sprintf id=507 1=$polls[poll_loop]->poll_info.total_comments},
          {lang_sprintf id=949 1=$polls[poll_loop]->poll_info.poll_views}
        </div>
        <div style='margin-top: 10px;'>
          {$polls[poll_loop]->poll_info.poll_desc|escape:html|truncate:75:"...":true}
        </div>
      </td>
      </tr>
      </table>
    </div>
    
    {cycle values=",<div style='clear: both; height: 10px;'></div>"}
  {/section}

</div>


{include file='footer.tpl'}