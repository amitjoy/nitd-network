
{* $Id: profile_poll.tpl 12 2009-01-11 06:04:12Z john $ *}

{* BEGIN POLLS *}
{if $owner->level_info.level_poll_allow != 0 && $total_polls > 0}

  <table cellpadding='0' cellspacing='0' width='100%' style='margin-bottom: 10px;'>
    <tr>
      <td class='header'>
        {lang_print id=2500005} ({$total_polls})
        {* IF MORE THAN 5 polls, SHOW VIEW MORE LINKS *}
        {if $total_polls > 5}&nbsp;[ <a href='{$url->url_create("polls", $owner->user_info.user_username)}'>{lang_print id=1021}</a> ]{/if}
      </td>
    </tr>
    <tr>
      <td class='profile'>
        {* LOOP THROUGH FIRST 5 poll LISTINGS *}
        {section name=poll_loop loop=$polls}
        <table cellpadding='0' cellspacing='0' width='100%'>
          <tr>
            <td valign='top' width='1'><img src='./images/icons/poll_poll16.gif' border='0' class='icon'></td>
            <td valign='top'>
              <div><a href='{$url->url_create("poll", $owner->user_info.user_username, $polls[poll_loop]->poll_info.poll_id)}'>{if $polls[poll_loop]->poll_info.poll_title == ""}{lang_print id=589}{else}{$polls[poll_loop]->poll_info.poll_title|truncate:30:"...":true|choptext:20:"<br>"}{/if}</a></div>
              <div style='color: #888888;'>{lang_sprintf id=2500028 1=$polls[poll_loop]->poll_info.poll_totalvotes}</div>
            </td>
          </tr>
        </table>
          {if $smarty.section.poll_loop.last != true}<div style='font-size: 1pt; margin-top: 2px; margin-bottom: 2px;'>&nbsp;</div>{/if}
        {/section}
      </td>
    </tr>
  </table>

{/if}