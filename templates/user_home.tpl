{include file='header.tpl'}

{* $Id: user_home.tpl 226 2009-10-28 23:57:07Z steve $ *}

<table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
    
    {* BEGIN LEFT COLUMN *}
    <td class='home_left'>
      
      
      {* ACTIVITY FEED PREFERENCES *}
      <div style='margin-bottom: 7px;'>
        <div class='page_header' style='width: 50%; float: left;'>{lang_print id=737}</div>
        <div style='width: 50%; float: right; text-align: right; padding-top: 13px;'>{if $setting.setting_actions_preference == 1} <a href='javascript:actionprefs();'>{lang_print id=1070}</a>{/if}</div>
        <div style='clear: both;'></div>
      </div>
      
      {if $setting.setting_actions_preference == 1}
        {* DIV FOR SPECIFYING ACTION PREFERENCES *}
        <div style='display: none;' id='actionprefs'>
          <div style='margin-top: 10px;'>{lang_print id=1069}</div>
          <br />
          <form action='misc_js.php' method='post' target='ajaxframe'>
            <table cellpadding='0' cellspacing='0'>
              {section name=actiontypes_loop loop=$actiontypes}
              <tr>
                <td>
                  <input type='checkbox' name='actiontype[]' id='actiontype_id_{$actiontypes[actiontypes_loop].actiontype_id}' value='{$actiontypes[actiontypes_loop].actiontype_id}'{if $actiontypes[actiontypes_loop].actiontype_selected == 1} checked='checked'{/if} />
                  <label for='actiontype_id_{$actiontypes[actiontypes_loop].actiontype_id}'>{lang_print id=$actiontypes[actiontypes_loop].actiontype_desc}</label>
                </td>
              </tr>
              {/section}
            </table>
            <br />
            <input type='submit' class='button' value='{lang_print id=173}' />
            <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();' />
            <input type='hidden' name='task' value='save_actionprefs' />
          </form>
        </div>
        
        {* JAVACRIPT FOR SPECIFYING ACTION PREFERENCES *}
        {literal}
        <script type="text/javascript">
        <!-- 
        function actionprefs()
        {
          TB_show('{/literal}{lang_print id=1068}{literal}', '#TB_inline?height=250&width=300&inlineId=actionprefs', '', '../images/trans.gif');
        }
        //-->
        </script>
        {/literal}
        
      {/if}
      
      
      {* SHOW RECENT ACTIVITY LIST *}
      <div class='home_whatsnew'>
      
        {* RECENT ACTIVITY ADVERTISEMENT BANNER *}
        {if $ads->ad_feed != ""}
          <div class='home_action' style='display: block; visibility: visible; padding-bottom: 10px;'>{$ads->ad_feed}</div>
        {/if}
        
        {* DISPLAY ACTIONS *}
        {section name=actions_loop loop=$actions}
          <div id='action_{$actions[actions_loop].action_id}' class='home_action{if $smarty.section.actions_loop.first}_top{/if}'>
            <table cellpadding='0' cellspacing='0'>
              <tr>
                <td valign='top'>
                  <img src='./images/icons/{$actions[actions_loop].action_icon}' border='0' class='icon' />
                </td>
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
    
    {* BEGIN RIGHT COLUMN *}
    <td class='home_right' width='220'>
      
      {* SHOW STATS AND NOTIFICATIONS *}  
      <table cellpadding='0' cellspacing='0' width='100%'>
        <tr>
          <td class='header'>{lang_print id=739}</td>
        </tr>
        <tr>
          <td class='home_box'>
            {* SHOW NUMBER OF TIMES PROFILE HAS BEEN VIEWED *}
            <div>
              <img src='./images/icons/newviews16.gif' border='0' class='icon' />
              {lang_sprintf id=740 1=$profile_views}
              {if $profile_viewers != 0}[ <a href='user_home.php?task=resetviews'>{lang_print id=741}</a> ]{/if}
              <br />
              
              {* WHO VIEWED MY PROFILE LINK *}
              {if $user->user_info.user_saveviews == 1}
              {if $profile_viewers|@count != 0}
                <div style='margin-top: 10px;'>
                  <a href='javascript:void(0);' onClick="$('profile_viewers').style.display='block';this.style.display='none';">{lang_print id=1064}</a>
                  <div id='profile_viewers' style='display: none; max-height: 400px; overflow: auto;'>
                    {lang_print id=1182}<br />
                    {section name=viewer_loop loop=$profile_viewers}
                    <a href='{$url->url_create("profile", $profile_viewers[viewer_loop]->user_info.user_username)}'>{$profile_viewers[viewer_loop]->user_displayname}</a>
                    {if $smarty.section.viewer_loop.last !== TRUE}, {/if}{/section}
                  </div>
                </div>
              {else}
                {lang_print id=1063}
              {/if}
              {/if}
            </div>
          </td>
        </tr>
      </table>
      <div class='spacer10'></div>
      
      {* SHOW STATUS *}
      {if $user->level_info.level_profile_status != 0}
      
        {lang_javascript ids=773,1113 range=743-747}
        {literal}
        <script type="text/javascript">
        <!-- 
        SocialEngine.Viewer.user_status = '{/literal}{$user->user_info.user_status}{literal}';
        //-->
        </script>
        {/literal}
        
        <table cellpadding='0' cellspacing='0' width='100%'>
          <tr>
            <td class='header'>{lang_print id=742}</td>
          </tr>
          <tr>
            <td class='home_box'>
              <table cellpadding='0' cellspacing='0'>
              <tr>
                <td valign='top'><img src='./images/icons/status16.gif' border='0' class='icon2' />&nbsp;&nbsp;</td>
                <td>
                  <div id='ajax_status'>
                    {if $user->user_info.user_status != ""}
                      {assign var='status_date' value=$datetime->time_since($user->user_info.user_status_date)}
                      {$user->user_displayname_short} <span id='ajax_currentstatus_value'>{$user->user_info.user_status}</span>
                      <div style='padding-top: 5px;'>
                        <div style='float: left; padding-right: 5px;'>[ <a href="javascript:void(0);" onClick="SocialEngine.Viewer.userStatusChange(); return false;">{lang_print id=745}</a> ]</div>
                        <div class='home_updated'>
                          {lang_print id=1113}
                          <span id='ajax_currentstatus_date'>{lang_sprintf id=$status_date[0] 1=$status_date[1]}</span>
                        </div>
                        <div style='clear: both; height: 0px;'></div>
                      </div>
                    {else}
                      <div><a href="javascript:void(0);" onclick="SocialEngine.Viewer.userStatusChange(); return false;">{lang_print id=743}</a></div>
                    {/if}
                  </div>
                </td>
              </tr>
              </table>
            </td>
          </tr>
        </table>
        <div class='spacer10'></div>
      {/if}
      
      
      {* SHOW LAST 3 NEWS ANNOUNCEMENTS *}
      {if $news|@count > 0}
        <table cellpadding='0' cellspacing='0' width='100%'>
          <tr>
            <td class='header'>{lang_print id=664}</td>
          </tr>
          <tr>
            <td class='home_box'>
              {section name=news_loop loop=$news}
              <table cellpadding='0' cellspacing='0' width='100%'>
                <tr>
                  <td valign='top'>
                    <b>{$news[news_loop].announcement_subject}</b><br />
                    <i>{$news[news_loop].announcement_date}</i><br />
                    {$news[news_loop].announcement_body}
                  </td>
                </tr>
              </table>
              {if $smarty.section.news_loop.last == false}<br>{/if}
              {/section}
            </td>
          </tr>
        </table>
        <div class='spacer10'></div>
      {/if}
      
      
      {* SHOW ONLINE USERS IF MORE THAN ZERO *}
      {math assign='total_online_users' equation="x+y" x=$online_users[0]|@count y=$online_users[1]}
      {if $total_online_users > 0}
        <table cellpadding='0' cellspacing='0' class='portal_table' align='center' width='100%'>
          <tr>
            <td class='header'>{lang_print id=665} ({$total_online_users})</td>
          </tr>
          <tr>
            <td class='home_box'>
            {if $online_users[0]|@count == 0}
              {lang_sprintf id=977 1=$online_users[1]}
            {else}
              {capture assign='online_users_registered'}
              {section name=online_loop loop=$online_users[0]}
              {if $smarty.section.online_loop.rownum != 1}, {/if}
              <a href='{$url->url_create("profile", $online_users[0][online_loop]->user_info.user_username)}'>{$online_users[0][online_loop]->user_displayname}</a>
              {/section}
              {/capture}
              {lang_sprintf id=976 1=$online_users_registered 2=$online_users[1]}
            {/if}
            </td>
          </tr>
        </table>
        {* padding in portal_table class <div class='spacer10'></div> *}
      {/if}
      
      {* SHOW UPCOMING BIRTHDAYS *}
      <div class='header'>{lang_print id=1176}</div>
      <div class='network_content'>
        {section name=birthday_loop loop=$birthdays max=20}
          <div>
            <a href='{$url->url_create("profile", $birthdays[birthday_loop].birthday_user_username)}'>{$birthdays[birthday_loop].birthday_user_displayname}</a>
            - {$datetime->cdate("`$setting.setting_dateformat`", $birthdays[birthday_loop].birthday_date)}
          </div>
        {sectionelse}
          {lang_print id=1180}
        {/section}
      </div>
      <div class='spacer10'></div>
      
      {* PLUGIN RELATED USER HOME SIDEBAR *}
      {hook_foreach name=user_home var=user_home_args}
        {include file=$user_home_args.file}
      {/hook_foreach}
    </td>
  </tr>
</table>

{include file='footer.tpl'}
