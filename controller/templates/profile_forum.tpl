{* BEGIN FORUM POSTS *}
{if $setting.setting_forum_status && $total_posts}

  <div class='profile_headline'>{lang_sprintf id=6000115 1=$total_posts}</div>
  <div>

  {* DISPLAY PAGINATION MENU IF APPLICABLE *}
  {if $maxpage_forum > 1}
    <div style='text-align: center;'>
      {if $p_forum != 1}<a href='profile.php?user={$owner->user_info.user_username}&v=forum&p_forum={math equation='p-1' p=$p_forum}'>&#171; {lang_print id=182}</a>{else}<font class='disabled'>&#171; {lang_print id=182}</font>{/if}
      {if $p_start_forum == $p_end_forum}
        &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start_forum 2=$total_posts} &nbsp;|&nbsp; 
      {else}
        &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start_forum 2=$p_end_forum 3=$total_posts} &nbsp;|&nbsp; 
      {/if}
      {if $p_forum != $maxpage_forum}<a href='profile.php?user={$owner->user_info.user_username}&v=forum&p_forum={math equation='p+1' p=$p_forum}'>{lang_print id=183} &#187;</a>{else}<font class='disabled'>{lang_print id=183} &#187;</font>{/if}
    </div>
  {/if}

  {section name=post_loop loop=$forum_posts}
    <div style='border-top: 1px solid #DDDDDD; padding-top: 7px; margin-top: 7px;'>
      <table cellpadding='0' cellspacing='0' width='100%'>
      <tr>
      <td width='1' valign='top'><img src='./images/icons/forum_topic16.gif' border='0' class='icon'></td>
      <td width='100%' valign='top'>
        <div style='float: right;'>{$datetime->cdate("`$setting.setting_dateformat` `$setting.setting_timeformat`", $datetime->timezone("`$forum_posts[post_loop].forumpost_date`", $global_timezone))}</div>
        <div style='font-weight: bold;'><a href='forum_topic.php?forum_id={$forum_posts[post_loop].forumtopic_forum_id}&topic_id={$forum_posts[post_loop].forumpost_forumtopic_id}&post_id={$forum_posts[post_loop].forumpost_id}#post_{$forum_posts[post_loop].forumpost_id}'>{$forum_posts[post_loop].forumtopic_subject}</a></div>
        <div>{$forum_posts[post_loop].forumpost_excerpt}</div>
      </td>
      </tr>
      </table>
    </div>
  {/section}

  <br>

  {* DISPLAY PAGINATION MENU IF APPLICABLE *}
  {if $maxpage_forum > 1}
    <div style='text-align: center;'>
      {if $p_forum != 1}<a href='profile.php?user={$owner->user_info.user_username}&v=forum&p_forum={math equation='p-1' p=$p_forum}'>&#171; {lang_print id=182}</a>{else}<font class='disabled'>&#171; {lang_print id=182}</font>{/if}
      {if $p_start_forum == $p_end_forum}
        &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start_forum 2=$total_posts} &nbsp;|&nbsp; 
      {else}
        &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start_forum 2=$p_end_forum 3=$total_posts} &nbsp;|&nbsp; 
      {/if}
      {if $p_forum != $maxpage_forum}<a href='profile.php?user={$owner->user_info.user_username}&v=forum&p_forum={math equation='p+1' p=$p_forum}'>{lang_print id=183} &#187;</a>{else}<font class='disabled'>{lang_print id=183} &#187;</font>{/if}
    </div>
  {/if}


  </div>

{/if}