{include file='header.tpl'}

{* $Id: user_group.tpl 34 2009-01-24 04:17:28Z john $ *}

<div>
  <img src='./images/icons/group_group48.gif' border='0' class='icon_big' />
  <div class='page_header'>{lang_print id=2000153}</div>
  {lang_print id=2000154}
</div>

<br />

{if (int)$user->level_info.level_group_allow & 4 || $total_invites > 0}
<div>
  {if (int)$user->level_info.level_group_allow & 4}
  <div class='button' style='float: left; padding-right: 20px;'>
    <a href='user_group_add.php'><img src='./images/icons/plus16.gif' border='0' class='button' />{lang_print id=2000095}</a>
  </div>
  {/if}
  {if $total_invites > 0}
  <div class='button' style='float: left; padding-right: 20px;'>
    <a href='javascript:void(0);' onClick="$('invite_groups').style.display = 'block'; if($('noGroups')) {ldelim} $('noGroups').style.display = 'none'; {rdelim} this.style.display = 'none';">
      <img src='./images/icons/group_invite16.gif' border='0' class='button' />
      {lang_sprintf id=2000155 1=$total_invites}
    </a>
  </div>
  {/if}
  <div style='clear: both; height: 0px;'></div>
</div>
{/if}

{* SHOW NO GROUPS MESSAGE *}
{if !$total_groups}
  <div id='noGroups'>
  <br>
  <table cellpadding='0' cellspacing='0'><tr>
  <td class='result'><img src='./images/icons/bulb16.gif' border='0' class='icon' />{lang_print id=2000156}</td>
  </tr></table>
  </div>
{/if}

<div id='invite_groups' style='display:none;'>
  {section name=invite_loop loop=$invites}
  <div class='group_row_invite' style='width: 600px;'>
    <table cellpadding='0' cellspacing='0'>
    <tr>
    <td style='vertical-align: top;'>
      <div class='group_row_photo'>
        <table cellpadding='0' cellspacing='0' width='140'>
          <tr>
            <td valign='top'>
              <a href='{$url->url_create("group", $smarty.const.NULL, $invites[invite_loop].group->group_info.group_id)}'>
                <img src='{$invites[invite_loop].group->group_photo("./images/nophoto.gif")}' class='photo' width='{$misc->photo_size($invites[invite_loop].group->group_photo("./images/nophoto.gif"),"128","128","w")}' border='0' />
              </a>
            </td>
          </tr>
        </table>
      </div>
    </td>
    <td class='group_row1' width='100%' style='vertical-align: top;'>
      <div class='group_row_title'>
        <a href='{$url->url_create("group", $smarty.const.NULL, $invites[invite_loop].group->group_info.group_id)}'>{$invites[invite_loop].group->group_info.group_title}</a>
      </div>
      <div class='group_row_date'>
        {capture assign='group_leader'}<a href='{$url->url_create("profile", $invites[invite_loop].group_leader->user_info.user_username)}'>{$invites[invite_loop].group_leader->user_displayname}</a>{/capture}
        {lang_sprintf id=2000157 1=$invites[invite_loop].group_members 2=$group_leader}
      </div>
      <div style='margin-top: 5px;'>
        {$invites[invite_loop].group->group_info.group_desc}
      </div>
      <div class='group_row_buttons'>
        <div class='button' style='float: left;'>
          <a href='{$url->url_create("group", $smarty.const.NULL, $invites[invite_loop].group->group_info.group_id)}'>
            <img src='./images/icons/group_group16.gif' border='0' class='button' />
            {lang_print id=2000158}
          </a>
        </div>
        <div class='button' style='float: left; padding-left: 20px;'>
          <a href="javascript:TB_show('{lang_print id=2000203}', 'user_group_manage.php?group_id={$invites[invite_loop].group->group_info.group_id}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');">
            <img src='./images/icons/group_invite16.gif' border='0' class='button' />
            {lang_print id=2000203}
          </a>
        </div>
        <div style='clear: both; height: 0px;'></div>
      </div>
    </td>
    </tr>
    </table>
  </div>
  {/section}
</div>

{section name=group_loop loop=$groups}
<div class='group_row' style='width: 600px;'>
  <table cellpadding='0' cellspacing='0'>
    <tr>
      <td style='vertical-align: top;'>
        <div class='group_row_photo'>
          <table cellpadding='0' cellspacing='0' width='140'>
            <tr>
              <td valign='top'>
                <a href='{$url->url_create("group", $smarty.const.NULL, $groups[group_loop].group->group_info.group_id)}'>
                  <img src='{$groups[group_loop].group->group_photo("./images/nophoto.gif")}' class='photo' width='{$misc->photo_size($groups[group_loop].group->group_photo("./images/nophoto.gif"),"128","128","w")}' border='0' />
                </a>
              </td>
            </tr>
          </table>
        </div>
      </td>
      <td class='group_row1' width='100%' style='vertical-align: top;'>
        <div class='group_row_title'>
          <a href='{$url->url_create("group", $smarty.const.NULL, $groups[group_loop].group->group_info.group_id)}'>{$groups[group_loop].group->group_info.group_title}</a>
        </div>
        <div class='group_row_date'>
          {capture assign='group_leader'}<a href='{$url->url_create("profile", $groups[group_loop].group_leader->user_info.user_username)}'>{$groups[group_loop].group_leader->user_displayname}</a>{/capture}
          {lang_sprintf id=2000157 1=$groups[group_loop].group_members 2=$group_leader}
        </div>
        <div style='margin-top: 5px;'>
          {$groups[group_loop].group->group_info.group_desc}
        </div>
        <div class='group_row_buttons'>
          <div class='button' style='float: left;'>
            <a href='{$url->url_create("group", $smarty.const.NULL, $groups[group_loop].group->group_info.group_id)}'>
              <img src='./images/icons/group_group16.gif' border='0' class='button' />
              {lang_print id=2000158}
            </a>
          </div>
          {if $groups[group_loop].group_rank != 0}
            <div class='button' style='float: left; padding-left: 20px;'>
              <a href='user_group_edit.php?group_id={$groups[group_loop].group->group_info.group_id}'>
                <img src='./images/icons/group_settings16.gif' border='0' class='button' />
                {lang_print id=2000159}
              </a>
            </div>
          {/if}
          <div class='button' style='float: left; padding-left: 20px;'>
            <a href="javascript:TB_show('{lang_print id=2000160}', 'user_group_manage.php?group_id={$groups[group_loop].group->group_info.group_id}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');">
              <img src='./images/icons/group_leave16.gif' border='0' class='button' />
              {lang_print id=2000160}
            </a>
          </div>
          <div style='clear: both; height: 0px;'></div>
        </div>
      </td>
    </tr>
  </table>
</div>
{/section}


{include file='footer.tpl'}