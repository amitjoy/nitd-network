{include file='header.tpl'}

<div class='page_header'>{lang_print id=6000061}</div>


{* DOWN FOR MAINTENANCE NOTICE - FOR NON-MODERATORS *}
{if $setting.setting_forum_status == 2 && !$forum_is_moderator}

  <br>
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='result'><img src='./images/icons/bulb16.gif' class='icon'>{lang_print id=6000114}</td>
  </table>

{* SHOW FORUMS *}
{else}


  {* SHOW MAINTENANCE DIV *}
  {if $setting.setting_forum_status == 2}
    <table cellpadding='0' cellspacing='0'>
    <tr><td class='result' style='text-align: left;'>{lang_print id=6000113}</td>
    </table>
  {/if}

  {* LIST CATEGORIES *}
  {section name=forumcat_loop loop=$forumcats}
    {if $forumcats[forumcat_loop].forums|@count != 0}
      <div class='forum_wrapper'>
      <table cellpadding='0' cellspacing='0' width='100%'>
      <tr><td class='forum_cat' colspan='5'>{lang_print id=$forumcats[forumcat_loop].forumcat_title}</td></tr>
      <tr>
      <td class='forum_label' style='width: 1px;' nowrap='nowrap'>&nbsp;</td>
      <td class='forum_label' style='width: 50%;' nowrap='nowrap'>{lang_print id=6000057}</td>
      <td class='forum_label' style='text-align: center;' nowrap='nowrap'>{lang_print id=6000058}</td>
      <td class='forum_label' style='text-align: center;' nowrap='nowrap'>{lang_print id=6000059}</td>
      <td class='forum_label' style='width: 50%;' nowrap='nowrap'>{lang_print id=6000060}</td>
      </tr>

      {* LIST FORUMS *}
      {section name=forum_loop loop=$forumcats[forumcat_loop].forums}
        <tr>
        <td class='forum_list0' style='vertical-align: top;'>{if $forumcats[forumcat_loop].forums[forum_loop].is_read}<img src='./images/icons/forum_old32.gif' border='0'>{else}<img src='./images/icons/forum_new32.gif' border='0'>{/if}</td>
        <td class='forum_list1' style='vertical-align: top;'>
          <div class='forum_list_title'><a href='forum_view.php?forum_id={$forumcats[forumcat_loop].forums[forum_loop].forum_id}'>{lang_print id=$forumcats[forumcat_loop].forums[forum_loop].forum_title}</a></div>
          <div class='forum_list_desc'>{lang_print id=$forumcats[forumcat_loop].forums[forum_loop].forum_desc}</div>
          {if $forumcats[forumcat_loop].forums[forum_loop].forum_mods|@count != 0}
           <div class='forum_list_moderators'>
              {lang_print id=6000008} 
              {section name=mod_loop loop=$forumcats[forumcat_loop].forums[forum_loop].forum_mods}
                {if $smarty.section.mod_loop.first != TRUE}, {/if}
                <a href='{$url->url_create("profile", $forumcats[forumcat_loop].forums[forum_loop].forum_mods[mod_loop]->user_info.user_username)}'>{$forumcats[forumcat_loop].forums[forum_loop].forum_mods[mod_loop]->user_displayname}</a>
              {/section}
            </div>
          {/if}

        </td>
        <td class='forum_list1' style='text-align: center;'>{$forumcats[forumcat_loop].forums[forum_loop].forum_totaltopics}</td>
        <td class='forum_list1' style='text-align: center;'>{$forumcats[forumcat_loop].forums[forum_loop].forum_totalreplies}</td>
        <td class='forum_list1'>
  
	  {* THERE IS A LAST POST IN THIS FORUM *}
	  {if $forumcats[forumcat_loop].forums[forum_loop].lastpost}

            {* LAST POST AUTHOR EXISTS *}
            {if $forumcats[forumcat_loop].forums[forum_loop].lastpost_info.author->user_exists}

              <table cellpadding='0' cellspacing='0'>
              <tr>
              <td class='forum_list_photo'><img src='{$forumcats[forumcat_loop].forums[forum_loop].lastpost_info.author->user_photo("./images/nophoto.gif")}' width='{$misc->photo_size($forumcats[forumcat_loop].forums[forum_loop].lastpost_info.author->user_photo("./images/nophoto.gif"),"40","40","w")}'></td>
              <td class='forum_list_lastpost'>
                <a href='forum_topic.php?forum_id={$forumcats[forumcat_loop].forums[forum_loop].forum_id}&topic_id={$forumcats[forumcat_loop].forums[forum_loop].lastpost_info.forumtopic_id}&post_id={$forumcats[forumcat_loop].forums[forum_loop].lastpost_info.forumpost_id}#post_{$forumcats[forumcat_loop].forums[forum_loop].lastpost_info.forumpost_id}'>{$forumcats[forumcat_loop].forums[forum_loop].lastpost_info.forumtopic_subject}</a>
	        {capture assign="lastpost_user"}<a href='{$url->url_create("profile", $forumcats[forumcat_loop].forums[forum_loop].lastpost_info.author->user_info.user_username)}'>{$forumcats[forumcat_loop].forums[forum_loop].lastpost_info.author->user_displayname}</a>{/capture}
                <div>
	          {lang_sprintf id=6000095 1=$lastpost_user}
	          {assign var='lastpost_date_basic' value=$datetime->time_since($forumcats[forumcat_loop].forums[forum_loop].lastpost_info.forumpost_date)}
	          - {lang_sprintf id=$lastpost_date_basic[0] 1=$lastpost_date_basic[1]}
                </div>
              </td>
              </tr>
              </table>

	    {* AUTHOR DOES NOT EXIST *}
	    {else}

              <table cellpadding='0' cellspacing='0'>
              <tr>
              <td class='forum_list_photo'><img src='./images/nophoto.gif' width='40'></td>
              <td class='forum_list_lastpost'>
                <a href='forum_topic.php?forum_id={$forumcats[forumcat_loop].forums[forum_loop].forum_id}&topic_id={$forumcats[forumcat_loop].forums[forum_loop].lastpost_info.forumtopic_id}&post_id={$forumcats[forumcat_loop].forums[forum_loop].lastpost_info.forumpost_id}#post_{$forumcats[forumcat_loop].forums[forum_loop].lastpost_info.forumpost_id}'>{$forumcats[forumcat_loop].forums[forum_loop].lastpost_info.forumtopic_subject}</a>
	        {capture assign="lastpost_user"}{if $forumcats[forumcat_loop].forums[forum_loop].lastpost_info.forumpost_authoruser_id != 0}{lang_print id=1071}{else}{lang_print id=835}{/if}{/capture}
                <div>
	          {lang_sprintf id=6000095 1=$lastpost_user}
	          {assign var='lastpost_date_basic' value=$datetime->time_since($forumcats[forumcat_loop].forums[forum_loop].lastpost_info.forumpost_date)}
	          - {lang_sprintf id=$lastpost_date_basic[0] 1=$lastpost_date_basic[1]}
                </div>
              </td>
              </tr>
              </table>

	    {/if}

	  {* NO LAST POST *}
	  {else}

	  {/if}
        </td>
        </tr>
      {/section}

      </table>
      </div>
    {/if}
  {/section}

{/if}

{include file='footer.tpl'}