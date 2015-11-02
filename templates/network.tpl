{include file='header.tpl'}

{* $Id: network.tpl 53 2009-02-06 04:55:08Z john $ *}


<table cellpadding='0' cellspacing='0' width='100%'>
<tr>
<td valign='top'>

  <div class='page_header' style='margin-bottom: 7px;'>{capture assign="subnet_name"}{lang_print id=$network.subnet_name}{/capture}{lang_sprintf id=1155 1=$subnet_name}</div>

  {* SHOW RECENT ACTIVITY LIST *}
  <div class='home_whatsnew'>

    {* RECENT ACTIVITY ADVERTISEMENT BANNER *}
    {if $ads->ad_feed != ""}
      <div class='home_action' style='display: block; visibility: visible; padding-bottom: 10px;'>{$ads->ad_feed}</div>
    {/if}

    {section name=actions_loop loop=$actions}
      {* DISPLAY ACTION *}
      <div id='action_{$actions[actions_loop].action_id}' class='home_action{if $smarty.section.actions_loop.first}_top{/if}'>
        <table cellpadding='0' cellspacing='0'>
          <tr>
            <td valign='top'><img src='./images/icons/{$actions[actions_loop].action_icon}' border='0' class='icon'></td>
            <td valign='top' width='100%'>
              {assign var='action_date' value=$datetime->time_since($actions[actions_loop].action_date)}
              <div class='home_action_date'>{lang_sprintf id=$action_date[0] 1=$action_date[1]}</div>
              {assign var='action_media' value=''}
              {if $actions[actions_loop].action_media !== FALSE}{capture assign='action_media'}{section name=action_media_loop loop=$actions[actions_loop].action_media}<a href='{$actions[actions_loop].action_media[action_media_loop].actionmedia_link}'><img src='{$actions[actions_loop].action_media[action_media_loop].actionmedia_path}' border='0' width='{$actions[actions_loop].action_media[action_media_loop].actionmedia_width}' class='recentaction_media'></a>{/section}{/capture}{/if}
              {lang_sprintf assign='action_text' id=$actions[actions_loop].action_text args=$actions[actions_loop].action_vars}
              {$action_text|replace:"[media]":$action_media|choptext:50:"<br>"}
            </td>
          </tr>
        </table>
      </div>
    {sectionelse}
      {lang_print id=738}
    {/section}
  </div>

</td>
<td valign='top' style='padding-left: 10px;' width='220'>

  {* SHOW LAST SIGNUPS *}
  <div class='header'>{lang_print id=666}</div>
  <div class='network_content'>
    {section name=signups_loop loop=$signups max=16}
      {* START NEW ROW *}
      {cycle name="startrow" values="<table cellpadding='0' cellspacing='0' align='center'><tr>,,,"}
      <td class='portal_member'><a href='{$url->url_create('profile',$signups[signups_loop]->user_info.user_username)}'><img src='{$signups[signups_loop]->user_photo('./images/nophoto.gif', TRUE)}' class='photo' width='35' height='35' border='0'></a></td>
      {* END ROW AFTER 2 RESULTS *}
      {if $smarty.section.signups_loop.last == true}
        </tr></table>
      {else}
        {cycle name="endrow" values=",,,</tr></table>"}
      {/if}
    {sectionelse}
      {lang_print id=1179}
    {/section}
  </div>

  {* RECENT STATUS UPDATES *}
  <div class='spacer10'></div>
  <div class='header'>{lang_print id=1177}</div>
  <div class='network_content'>
    {section name=statuses_loop loop=$statuses max=5}
      <div{if !$smarty.section.statuses_loop.first} style='padding-top: 7px;'{/if}><a href='{$url->url_create('profile', $statuses[statuses_loop].status_user_username)}'>{$statuses[statuses_loop].status_user_displayname}</a> {$statuses[statuses_loop].status_user_status}</div>
    {sectionelse}
      {lang_print id=1178}
    {/section}
  </div>

{* END RIGHT COLUMN *}
</td>
</tr>
</table>

{include file='footer.tpl'}