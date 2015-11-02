{include file='header.tpl'}

<table cellpadding='0' cellspacing='0' width='100%'>
<tr>
<td style='vertical-align: top;' colspan='2'>
  <div class='page_header'><a href='forum.php'>{lang_print id=6000061}</a> &#187; {lang_print id=$forum_info.forum_title}</div>
  <div>{lang_print id=$forum_info.forum_desc}</div>
</td>
</tr>
</table>

{* SHOW MAINTENANCE DIV *}
{if $setting.setting_forum_status == 2}
  <br>
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='result' style='text-align: left;'>{lang_print id=6000113}</td>
  </table>
{/if}

<br>

<table cellpadding='0' cellspacing='0' width='100%'>
<tr>
<td style='vertical-align: bottom; padding-top: 10px;'>
  {if $moderators|@count != 0}
    <div class='forum_moderators'>{lang_print id=6000093}
    {section name=mod_loop loop=$moderators}
      {if $smarty.section.mod_loop.first != TRUE}, {/if}
      <a href='{$url->url_create("profile", $moderators[mod_loop]->user_info.user_username)}'>{$moderators[mod_loop]->user_displayname}</a>
    {/section}
    </div>
  {/if}
</td>
<td style='text-align: right; vertical-align: bottom; padding-top: 10px;'>
  <table cellpadding='0' cellspacing='0' align='right'>
  <tr>
  <td>
  {if $maxpage > 1}
    {lang_print id=1005}
    {if $maxpage > 6}
      {section name=page_loop start=1 loop=4}
        <a href='forum_view.php?forum_id={$forum_info.forum_id}&p={$smarty.section.page_loop.index}' {if $p == $smarty.section.page_loop.index}style='font-weight: bold;'{/if}>
          {$smarty.section.page_loop.index}
        </a>
      {/section}
      {if $p > 2 && $p < $maxpage-1}
        {if $p-2 > 3}...{/if}
        {section name=page_loop start=$p-1 loop=$p+2}
	  {if $smarty.section.page_loop.index > 3 && $smarty.section.page_loop.index < $maxpage-2}
	    <a href='forum_view.php?forum_id={$forum_info.forum_id}&p={$smarty.section.page_loop.index}' {if $p == $smarty.section.page_loop.index}style='font-weight: bold;'{/if}>{$smarty.section.page_loop.index}</a>
	  {/if}
	{/section}
        {if $p+2 < $maxpage-2}...{/if}
      {else}...{/if}
      {section name=page_loop start=$maxpage-2 loop=$maxpage+1}
        <a href='forum_view.php?forum_id={$forum_info.forum_id}&p={$smarty.section.page_loop.index}' {if $p == $smarty.section.page_loop.index}style='font-weight: bold;'{/if}>
          {$smarty.section.page_loop.index}
        </a>
      {/section}
    {else}
      {section name=page_loop start=1 loop=$maxpage+1}
        <a href='forum_view.php?forum_id={$forum_info.forum_id}&p={$smarty.section.page_loop.index}' {if $p == $smarty.section.page_loop.index}style='font-weight: bold;'{/if}>
          {$smarty.section.page_loop.index}
        </a>
      {/section}
    {/if}
  {/if}
  </td>
  {if $forum_permission.allowed_to_post}
    <td style='font-weight: bold; padding-left: 20px;' nowrap='nowrap'>
      <a href='forum_new.php?forum_id={$forum_info.forum_id}'><img src='./images/icons/plus16.gif' border='0' style='float: left; margin-right: 2px;'>{lang_print id=6000062}</a>
    </td>
  {/if}
  </tr>
  </table>
</td>
</tr>
</table>




{* SHOW MESSAGE ABOUT NO TOPICS *}
{if $forum_info.forum_totaltopics == 0}
  <br />
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='result'><img src='./images/icons/bulb16.gif' border='0' class='icon'>{lang_print id=6000068} {if $forum_permission.allowed_to_post}{lang_sprintf id=6000069 1=$forum_info.forum_id}{/if}</td></tr>
  </table>


{* SHOW TOPIC LIST *}
{else}
  <div class='forum_wrapper'>
    <table cellpadding='0' cellspacing='0' width='100%'>
    <tr><td class='forum_cat' colspan='6'>{lang_print id=$forum_info.forum_title}<br></td></tr>
    <tr>
    <td class='forum_label' style='width: 1px;' nowrap='nowrap'>&nbsp;</td>
    <td class='forum_label' style='width: 70%;' nowrap='nowrap'>{lang_print id=6000063}</td>
    <td class='forum_label' style='text-align: center;' nowrap='nowrap'></td>
    <td class='forum_label' style='text-align: center; width: 50px;' nowrap='nowrap'>{lang_print id=6000059}</td>
    <td class='forum_label' style='text-align: center; width: 50px;' nowrap='nowrap'>{lang_print id=6000064}</td>
    <td class='forum_label' style='width: 30%;' nowrap='nowrap'>{lang_print id=6000060}</td>
    </tr>

    {section name=topic_loop loop=$topics}
      <tr>
      <td class='forum_list0{cycle values=",a"}' style='width: 1px; vertical-align: top; padding-top: 12px;' nowrap='nowrap'>
        {if $topics[topic_loop].forumtopic_totalreplies >= 30}
	  {if $topics[topic_loop].is_new}
	    <img src='./images/icons/forum_topic_hot_new32.gif' border='0'>
	  {else}
	    <img src='./images/icons/forum_topic_hot32.gif' border='0'>
	  {/if}
        {else}
	  {if $topics[topic_loop].is_new}
	    <img src='./images/icons/forum_topic_new32.gif' border='0'>
	  {else}
	    <img src='./images/icons/forum_topic32.gif' border='0'>
	  {/if}
        {/if}
      </td>
      <td class='forum_list1{cycle values=",a"}' style='vertical-align: top;'>
        <div class='forum_list_title'>
	  {if $topics[topic_loop].forumtopic_sticky}<img src='./images/icons/forum_topic_sticky16.gif' border='0' style='vertical-align: middle;'>{/if}
	  {if $topics[topic_loop].forumtopic_closed}<img src='./images/icons/forum_topic_locked16.gif' border='0' style='vertical-align: middle;'>{/if}
          <a href='forum_topic.php?forum_id={$forum_info.forum_id}&topic_id={$topics[topic_loop].forumtopic_id}'>{$topics[topic_loop].forumtopic_subject}</a>
	</div>
        <div class='forum_list_desc'>{$topics[topic_loop].forumtopic_excerpt|truncate:70:"..."}</div>
      </td>
      <td class='forum_list1{cycle values=",a"}' style='text-align: center;' nowrap='nowrap'></td>
      <td class='forum_list1{cycle values=",a"}' style='text-align: center;' nowrap='nowrap'>{$topics[topic_loop].forumtopic_totalreplies}</td>
      <td class='forum_list1{cycle values=",a"}' style='text-align: center;' nowrap='nowrap'>{$topics[topic_loop].forumtopic_views}</td>
      <td class='forum_list1{cycle values=",a"}' nowrap='nowrap'>

	{* THERE IS A LAST POST IN THIS FORUM *}
	{if $topics[topic_loop].lastpost}

          {* LAST POST AUTHOR EXISTS *}
          {if $topics[topic_loop].lastpost_info.author->user_exists}

            <table cellpadding='0' cellspacing='0'>
            <tr>
            <td class='forum_list_photo'><img src='{$topics[topic_loop].lastpost_info.author->user_photo("./images/nophoto.gif")}' width='{$misc->photo_size($topics[topic_loop].lastpost_info.author->user_photo("./images/nophoto.gif"),"40","40","w")}'></td>
            <td class='forum_list_lastpost'>
              <a href='forum_topic.php?forum_id={$forum_info.forum_id}&topic_id={$topics[topic_loop].forumtopic_id}&post_id={$topics[topic_loop].lastpost_info.forumpost_id}#post_{$topics[topic_loop].lastpost_info.forumpost_id}'>
		{if $topics[topic_loop].lastpost_info.forumpost_excerpt == ''}
		  <i>{lang_print id=6000123}</i>
		{else}
		  {$topics[topic_loop].lastpost_info.forumpost_excerpt|truncate:50:"..."}
		{/if}
	      </a><br />
	      {capture assign="lastpost_user"}<a href='{$url->url_create("profile", $topics[topic_loop].lastpost_info.author->user_info.user_username)}'>{$topics[topic_loop].lastpost_info.author->user_displayname}</a>{/capture}
	      <div>
	        {lang_sprintf id=6000095 1=$lastpost_user}
	        {assign var='lastpost_date_basic' value=$datetime->time_since($topics[topic_loop].lastpost_info.forumpost_date)}
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
              <a href='forum_topic.php?forum_id={$forum_info.forum_id}&topic_id={$topics[topic_loop].forumtopic_id}&post_id={$topics[topic_loop].lastpost_info.forumpost_id}#post_{$topics[topic_loop].lastpost_info.forumpost_id}'>
		{if $topics[topic_loop].lastpost_info.forumpost_excerpt == ''}
		  <i>{lang_print id=6000123}</i>
		{else}
		  {$topics[topic_loop].lastpost_info.forumpost_excerpt|truncate:50:"..."}
		{/if}
	      </a><br />
	      {capture assign="lastpost_user"}{if $topics[topic_loop].lastpost_info.forumpost_authoruser_id != 0}{lang_print id=1071}{else}{lang_print id=835}{/if}{/capture}
	      <div>
	        {lang_sprintf id=6000095 1=$lastpost_user}
	        {assign var='lastpost_date_basic' value=$datetime->time_since($topics[topic_loop].lastpost_info.forumpost_date)}
	        - {lang_sprintf id=$lastpost_date_basic[0] 1=$lastpost_date_basic[1]}
              </div>
            </td>
            </tr>
            </table>

	  {/if}

	{* NO LAST POST *}
	{else}
	  {lang_print id=6000094}
	{/if}

      </td>
      </tr>
    {/section}

    </table>
  </div>


  <div style='margin-top: 10px;'>
    <table cellpadding='0' cellspacing='0'>
    <tr>
    {if $forum_permission.allowed_to_post}
      <td style='font-weight: bold; padding-right: 20px;' nowrap='nowrap'>
        <a href='forum_new.php?forum_id={$forum_info.forum_id}'><img src='./images/icons/plus16.gif' border='0' style='float: left; margin-right: 2px;'>{lang_print id=6000062}</a>
      </td>
    {/if}
    <td>
    {if $maxpage > 1}
      {lang_print id=1005}
      {if $maxpage > 6}
        {section name=page_loop start=1 loop=4}
          <a href='forum_view.php?forum_id={$forum_info.forum_id}&p={$smarty.section.page_loop.index}' {if $p == $smarty.section.page_loop.index}style='font-weight: bold;'{/if}>
            {$smarty.section.page_loop.index}
          </a>
        {/section}
        {if $p > 2 && $p < $maxpage-1}
          {if $p-2 > 3}...{/if}
          {section name=page_loop start=$p-1 loop=$p+2}
	    {if $smarty.section.page_loop.index > 3 && $smarty.section.page_loop.index < $maxpage-2}
	      <a href='forum_view.php?forum_id={$forum_info.forum_id}&p={$smarty.section.page_loop.index}' {if $p == $smarty.section.page_loop.index}style='font-weight: bold;'{/if}>{$smarty.section.page_loop.index}</a>
	    {/if}
	  {/section}
          {if $p+2 < $maxpage-2}...{/if}
        {else}...{/if}
        {section name=page_loop start=$maxpage-2 loop=$maxpage+1}
          <a href='forum_view.php?forum_id={$forum_info.forum_id}&p={$smarty.section.page_loop.index}' {if $p == $smarty.section.page_loop.index}style='font-weight: bold;'{/if}>
            {$smarty.section.page_loop.index}
          </a>
        {/section}
      {else}
        {section name=page_loop start=1 loop=$maxpage+1}
          <a href='forum_view.php?forum_id={$forum_info.forum_id}&p={$smarty.section.page_loop.index}' {if $p == $smarty.section.page_loop.index}style='font-weight: bold;'{/if}>
            {$smarty.section.page_loop.index}
          </a>
        {/section}
      {/if}
    {/if}
    </td>
    </tr>
    </table>
  </div>

{/if}







{include file='footer.tpl'}