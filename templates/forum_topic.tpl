{include file='header.tpl'}

<div class='page_header'><a href='forum.php'>{lang_print id=6000061}</a> &#187; <a href='forum_view.php?forum_id={$forum_info.forum_id}'>{lang_print id=$forum_info.forum_title}</a></div>
<div>{lang_print id=$forum_info.forum_desc}</div>

{* SHOW MAINTENANCE DIV *}
{if $setting.setting_forum_status == 2}
  <br>
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='result' style='text-align: left;'>{lang_print id=6000113}</td>
  </table>
{/if}

<br />

<table cellpadding='0' cellspacing='0' width='100%'>
<tr>
<td style='vertical-align: bottom;'>
  <div class='forum_topic_title'>{$topic_info.forumtopic_subject}</div>
  {if $topic_info.forumtopic_closed}<div style='margin-bottom: 10px;'><img src='./images/icons/forum_topic_locked16.gif' border='0' class='icon'>{lang_print id=6000118}</div>{/if}
</td>
<td class='forum_topic_options'>
  <table cellpadding='0' cellspacing='0' align='right'>
  <tr>
  <td>
  {if $maxpage > 1}
    {lang_print id=1005}
    {if $maxpage > 6}
      {section name=page_loop start=1 loop=4}
        <a href='forum_topic.php?forum_id={$forum_info.forum_id}&topic_id={$topic_info.forumtopic_id}&p={$smarty.section.page_loop.index}' {if $p == $smarty.section.page_loop.index}style='font-weight: bold;'{/if}>
          {$smarty.section.page_loop.index}
        </a>
      {/section}
      {if $p > 2 && $p < $maxpage-1}
        {if $p-2 > 3}...{/if}
        {section name=page_loop start=$p-1 loop=$p+2}
	  {if $smarty.section.page_loop.index > 3 && $smarty.section.page_loop.index < $maxpage-2}
	    <a href='forum_topic.php?forum_id={$forum_info.forum_id}&topic_id={$topic_info.forumtopic_id}&p={$smarty.section.page_loop.index}' {if $p == $smarty.section.page_loop.index}style='font-weight: bold;'{/if}>{$smarty.section.page_loop.index}</a>
	  {/if}
	{/section}
        {if $p+2 < $maxpage-2}...{/if}
      {else}...{/if}
      {section name=page_loop start=$maxpage-2 loop=$maxpage+1}
        <a href='forum_topic.php?forum_id={$forum_info.forum_id}&topic_id={$topic_info.forumtopic_id}&p={$smarty.section.page_loop.index}' {if $p == $smarty.section.page_loop.index}style='font-weight: bold;'{/if}>
          {$smarty.section.page_loop.index}
        </a>
      {/section}
    {else}
      {section name=page_loop start=1 loop=$maxpage+1}
        <a href='forum_topic.php?forum_id={$forum_info.forum_id}&topic_id={$topic_info.forumtopic_id}&p={$smarty.section.page_loop.index}' {if $p == $smarty.section.page_loop.index}style='font-weight: bold;'{/if}>
          {$smarty.section.page_loop.index}
        </a>
      {/section}
    {/if}
  {/if}
  </td>
  {if $forum_permission.allowed_to_post && !$topic_info.forumtopic_closed}
    <td class='forum_topic_replylink' style='font-weight: bold; padding-left: 20px;' nowrap='nowrap'>
      <a href='forum_new.php?forum_id={$forum_info.forum_id}&topic_id={$topic_info.forumtopic_id}'><img src='./images/icons/plus16.gif' border='0' style='float: left; margin-right: 2px;'>{lang_print id=6000071}</a>
    </td>
  {/if}
  </tr>
  </table>
</td>
</tr>
</table>

<div class='forum_topics'>
  
{section name=post_loop loop=$posts}
  <div class='forum_topic_wrapper{cycle values="1,2"}'><a name='post_{$posts[post_loop].forumpost_id}'></a>


  {* POST HAS BEEN DELETED*}
  {if $posts[post_loop].forumpost_deleted}


    <div class='forum_topic_deleted'>{lang_print id=6000083}</div>


  {* POST HAS NOT BEEN DELETED *}
  {else}

    <table cellpadding='0' cellspacing='0' width='100%'>
    <tr>
    <td class='forum_topic_left'>

      {* AUTHOR EXISTS *}
      {if $posts[post_loop].author->user_exists}
        <div class='forum_topic_author'><a href='{$url->url_create("profile", $posts[post_loop].author->user_info.user_username)}'>{$posts[post_loop].author->user_displayname}</a></div>
      
      {* AUTHOR DOES NOT EXIST *}
      {else}

        {* AUTHOR HAS BEEN DELETED *}
        {if $posts[post_loop].forumpost_authoruser_id != 0}
          <div class='forum_topic_author'>{lang_print id=1071}</div>

        {* AUTHOR IS ANONYMOUS *}
        {else}
          <div class='forum_topic_author'>{lang_print id=835}</div>
        {/if}
      {/if}

      {* AUTHOR EXISTS *}
      {if $posts[post_loop].author->user_exists}
        <table cellpadding='0' cellspacing='0'>
        <tr>
        <td class='forum_topic_photo'>
	  <a href='{$url->url_create("profile", $posts[post_loop].author->user_info.user_username)}'>
	  <img src='{$posts[post_loop].author->user_photo("./images/nophoto.gif")}' border='0' class='forum_topic_photo_img' width='{$misc->photo_size($posts[post_loop].author->user_photo("./images/nophoto.gif"),"100","100","w")}'>
	  </a>
	</td>
        </tr>
        </table>
        {assign var='signup_date_basic' value=$datetime->time_since($posts[post_loop].author->user_info.user_signupdate)}
	{capture assign='signup_date'}{lang_sprintf id=$signup_date_basic[0] 1=$signup_date_basic[1]}{/capture}
        <div class='forum_topic_authorinfo'>
	  {if $posts[post_loop].author->is_moderator}<span>{lang_print id=6000082}</span><br />{/if}
	  {lang_sprintf id=6000079 1=$posts[post_loop].author->totalposts}<br />
	  {lang_sprintf id=6000080 1=$signup_date}
	</div>
      {/if}

    </td>
    <td class='forum_topic_right'>
      <div class='forum_topic_info'>
	{capture assign='post_date'}{$datetime->cdate("`$setting.setting_timeformat` `$setting.setting_dateformat`", $datetime->timezone("`$posts[post_loop].forumpost_date`", $global_timezone))}{/capture}
        <img src='./images/icons/forum_post16.gif' border='0' class='icon'>{lang_sprintf id=6000081 1=$post_date}
	{if $forum_permission.allowed_to_post && !$topic_info.forumtopic_closed}{assign var="show_quote" value=true}{/if}
	{if ($user->user_exists && $user->user_info.user_id == $posts[post_loop].author->user_info.user_id && !$topic_info.forumtopic_closed) || $forum_permission.allowed_to_editall}{assign var="show_edit" value=true}{/if}
	{if ($user->user_exists && $user->user_info.user_id == $posts[post_loop].author->user_info.user_id && !$topic_info.forumtopic_closed) || $forum_permission.allowed_to_deleteall}{assign var="show_delete" value=true}{/if}
	{if $show_quote || $show_edit || $show_delete}
	  <span>&nbsp;&nbsp;[ 
	    {if $show_quote}<a href='forum_new.php?forum_id={$forum_info.forum_id}&topic_id={$topic_info.forumtopic_id}&quote_id={$posts[post_loop].forumpost_id}'>{lang_print id=6000116}</a>{/if}
	    {if $show_quote && ($show_edit || $show_delete)} | {/if}
	    {if $show_edit}<a href='forum_new.php?forum_id={$forum_info.forum_id}&topic_id={$topic_info.forumtopic_id}&post_id={$posts[post_loop].forumpost_id}'>{lang_print id=187}</a>{/if}
	    {if $show_edit && $show_delete} | {/if}
	    {if $show_delete}<a href='javascript:void(0);' onClick="confirmDeletePost('{$posts[post_loop].forumpost_id}')";>{lang_print id=155}</a>{/if}
	  ]</span>
	{/if}
      </div>
      <div class='forum_topic_body'>
	{if $posts[post_loop].forumpost_forummedia_id != 0}
	  <div style='padding-top: 15px; padding-bottom: 15px;'><img src='{$posts[post_loop].forummedia_path}' border='0'></div>
	{/if}
        {$posts[post_loop].forumpost_body}
      </div>
    </td>
    </tr>
    </table>

  {/if}

  </div>
{/section}


</div>



<table cellpadding='0' cellspacing='0' width='100%'>
<tr>
<td class='forum_topic_options'>
  <table cellpadding='0' cellspacing='0' align='left'>
  <tr>
  {if $forum_permission.allowed_to_post && !$topic_info.forumtopic_closed}
    <td class='forum_topic_replylink' style='padding-right: 20px;' nowrap='nowrap' align='left'>
      <a href='forum_new.php?forum_id={$forum_info.forum_id}&topic_id={$topic_info.forumtopic_id}'><img src='./images/icons/plus16.gif' border='0' style='float: left; margin-right: 2px;'>{lang_print id=6000071}</a>
    </td>
  {/if}
  <td align='left'>
  {if $maxpage > 1}
    {lang_print id=1005}
    {if $maxpage > 6}
      {section name=page_loop start=1 loop=4}
        <a href='forum_topic.php?forum_id={$forum_info.forum_id}&topic_id={$topic_info.forumtopic_id}&p={$smarty.section.page_loop.index}' {if $p == $smarty.section.page_loop.index}style='font-weight: bold;'{/if}>
          {$smarty.section.page_loop.index}
        </a>
      {/section}
      {if $p > 2 && $p < $maxpage-1}
        {if $p-2 > 3}...{/if}
        {section name=page_loop start=$p-1 loop=$p+2}
	  {if $smarty.section.page_loop.index > 3 && $smarty.section.page_loop.index < $maxpage-2}
	    <a href='forum_topic.php?forum_id={$forum_info.forum_id}&topic_id={$topic_info.forumtopic_id}&p={$smarty.section.page_loop.index}' {if $p == $smarty.section.page_loop.index}style='font-weight: bold;'{/if}>{$smarty.section.page_loop.index}</a>
	  {/if}
	{/section}
        {if $p+2 < $maxpage-2}...{/if}
      {else}...{/if}
      {section name=page_loop start=$maxpage-2 loop=$maxpage+1}
        <a href='forum_topic.php?forum_id={$forum_info.forum_id}&topic_id={$topic_info.forumtopic_id}&p={$smarty.section.page_loop.index}' {if $p == $smarty.section.page_loop.index}style='font-weight: bold;'{/if}>
          {$smarty.section.page_loop.index}
        </a>
      {/section}
    {else}
      {section name=page_loop start=1 loop=$maxpage+1}
        <a href='forum_topic.php?forum_id={$forum_info.forum_id}&topic_id={$topic_info.forumtopic_id}&p={$smarty.section.page_loop.index}' {if $p == $smarty.section.page_loop.index}style='font-weight: bold;'{/if}>
          {$smarty.section.page_loop.index}
        </a>
      {/section}
    {/if}
  {/if}
  </td>
  {if $forum_permission.is_moderator}
    <td align='right'>
      {lang_print id=6000072} 
      {if $forum_permission.allowed_to_close}
	{if $topic_info.forumtopic_closed}<a href='javascript:void(0);' onClick='confirmOpen();'>{lang_print id=6000073}</a>{else}<a href='javascript:void(0);' onClick='confirmClose();'>{lang_print id=6000074}</a>{/if}
      {/if}
      {if $forum_permission.allowed_to_deleteall}
	{if $forum_permission.allowed_to_close} | {/if}
	<a href='javascript:void(0);' onClick='confirmDelete();'>{lang_print id=6000075}</a>
      {/if}
      {if $forum_permission.allowed_to_move}
	{if $forum_permission.allowed_to_deleteall || $forum_permission.allowed_to_close} | {/if}
	<a href='javascript:void(0);' onClick='confirmMove();'>{lang_print id=6000076}</a>
      {/if}
      {if $forum_permission.allowed_to_stick}
	{if $forum_permission.allowed_to_deleteall || $forum_permission.allowed_to_close || $forum_permission.allowed_to_move} | {/if}
	{if $topic_info.forumtopic_sticky}<a href='javascript:void(0);' onClick='confirmUnstick();'>{lang_print id=6000077}</a>{else}<a href='javascript:void(0);' onClick='confirmStick();'>{lang_print id=6000078}</a>{/if}
      {/if}
    </td>
  {/if}
  </tr>
  </table>
</td>
</tr>
</table>

<br>

{if $forum_permission.allowed_to_post && !$topic_info.forumtopic_closed}
  <form action='forum_new.php' method='post'>
  <table cellpadding='0' cellspacing='0' align='center'>
  <tr>
  <td>
    <div class='forum_topic_title'>{lang_print id=6000084}</div>
    <textarea style='width: 400px; height: 100px;' name='post_body' id='post_body'></textarea>

    {if $setting.setting_forum_code == 1}
      <table cellspacing='0' cellpadding='0'>
      <tr>
      <td valign='top'>
        <a href="javascript:void(0);" onClick="javascript:$('secure_image').src = $('secure_image').src + '?' + (new Date()).getTime();"><img src='./images/secure.php' id='secure_image' border='0' height='20' width='67' class='signup_code' /></a>
      </td>
      <td style='padding-top: 4px; padding-left: 3px;'><input type='text' name='comment_secure' id='comment_secure' class='text' size='6' maxlength='10'>&nbsp;</td>
      <td>
        {capture assign=tip}{lang_print id=691}{/capture}
        <img src='./images/icons/tip.gif' border='0' class='Tips1' title='{$tip|replace:quotes}' />
      </td>
      </tr>
      </table>
    {/if}

    <div style='margin-top: 10px;'>
      <input type='submit' class='button' value='{lang_print id=6000085}'>
      <input type='hidden' name='forum_id' value='{$forum_info.forum_id}'>
      <input type='hidden' name='topic_id' value='{$topic_info.forumtopic_id}'>
      <input type='hidden' name='task' value='reply'>
    </div>
  </td>
  </tr>
  </table>
  </form>
{/if}




{* JAVASCRIPT FOR CONFIRMING DELETION *}
{literal}
<script type="text/javascript">
<!-- 
function confirmDeletePost(post_id) {
  $('post_id').value = post_id;
  $('deletepostform').action = 'forum_topic.php#post_' + post_id;
  TB_show('{/literal}{lang_print id=6000121}{literal}', '#TB_inline?height=150&width=300&inlineId=confirmdeletepost', '', '../images/trans.gif');
}

function confirmDelete() {
  TB_show('{/literal}{lang_print id=6000096}{literal}', '#TB_inline?height=150&width=300&inlineId=confirmdelete', '', '../images/trans.gif');
}

function confirmMove() {
  TB_show('{/literal}{lang_print id=6000110}{literal}', '#TB_inline?height=150&width=300&inlineId=confirmmove', '', '../images/trans.gif');
}

function confirmClose() {
  TB_show('{/literal}{lang_print id=6000098}{literal}', '#TB_inline?height=150&width=300&inlineId=confirmclose', '', '../images/trans.gif');
}

function confirmOpen() {
  TB_show('{/literal}{lang_print id=6000099}{literal}', '#TB_inline?height=150&width=300&inlineId=confirmopen', '', '../images/trans.gif');
}

function confirmStick() {
  TB_show('{/literal}{lang_print id=6000104}{literal}', '#TB_inline?height=150&width=300&inlineId=confirmstick', '', '../images/trans.gif');
}

function confirmUnstick() {
  TB_show('{/literal}{lang_print id=6000105}{literal}', '#TB_inline?height=150&width=300&inlineId=confirmunstick', '', '../images/trans.gif');
}

//-->
</script>
{/literal}



{* HIDDEN DIV TO DISPLAY DELETE CONFIRMATION MESSAGE *}
{if $forum_permission.allowed_to_deleteall}
  <div style='display: none;' id='confirmdelete'>
    <form action='forum_topic.php' method='post'>
    <div style='margin-top: 10px;'>
      {lang_print id=6000097}
    </div>
    <br>
    <input type='submit' class='button' value='{lang_print id=175}' onClick='parent.TB_remove();'> 
    <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
    <input type='hidden' name='task' value='delete'>
    <input type='hidden' name='forum_id' value='{$forum_info.forum_id}'>
    <input type='hidden' name='topic_id' value='{$topic_info.forumtopic_id}'>
    </form>
  </div>
{/if}



{* HIDDEN DIV TO DISPLAY MOVE CONFIRMATION MESSAGE *}
{if $forum_permission.allowed_to_move}
  <div style='display: none;' id='confirmmove'>
    <form action='forum_topic.php' method='post'>
    <div style='margin-top: 10px;'>
      {lang_print id=6000111}
      <br>
      <select name='new_forum_id'>
	{section name=forumcat_loop loop=$forumcats}
	  {section name=forum_loop loop=$forumcats[forumcat_loop].forums}
	    <option value='{$forumcats[forumcat_loop].forums[forum_loop].forum_id}'>{lang_print id=$forumcats[forumcat_loop].forums[forum_loop].forum_title}</option>
	  {/section}
	{/section}
      </select>
    </div>
    <br>
    <input type='submit' class='button' value='{lang_print id=6000112}' onClick='parent.TB_remove();'> 
    <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
    <input type='hidden' name='task' value='move'>
    <input type='hidden' name='forum_id' value='{$forum_info.forum_id}'>
    <input type='hidden' name='topic_id' value='{$topic_info.forumtopic_id}'>
    </form>
  </div>
{/if}




{* HIDDEN DIV TO DISPLAY CLOSE/OPEN CONFIRMATION MESSAGE *}
{if $forum_permission.allowed_to_close}
  <div style='display: none;' id='confirmclose'>
    <form action='forum_topic.php?forum_id={$forum_info.forum_id}&topic_id={$topic_info.forumtopic_id}&p={$p}' method='post'>
    <div style='margin-top: 10px;'>
      {lang_print id=6000100}
    </div>
    <br>
    <input type='submit' class='button' value='{lang_print id=6000101}' onClick='parent.TB_remove();'> 
    <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
    <input type='hidden' name='task' value='close'>
    </form>
  </div>

  <div style='display: none;' id='confirmopen'>
    <form action='forum_topic.php?forum_id={$forum_info.forum_id}&topic_id={$topic_info.forumtopic_id}&p={$p}' method='post'>
    <div style='margin-top: 10px;'>
      {lang_print id=6000102}
    </div>
    <br>
    <input type='submit' class='button' value='{lang_print id=6000103}' onClick='parent.TB_remove();'> 
    <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
    <input type='hidden' name='task' value='open'>
    </form>
  </div>
{/if}




{* HIDDEN DIV TO DISPLAY STICK/UNSTICK CONFIRMATION MESSAGE *}
{if $forum_permission.allowed_to_stick}
  <div style='display: none;' id='confirmstick'>
    <form action='forum_topic.php?forum_id={$forum_info.forum_id}&topic_id={$topic_info.forumtopic_id}&p={$p}' method='post'>
    <div style='margin-top: 10px;'>
      {lang_print id=6000106}
    </div>
    <br>
    <input type='submit' class='button' value='{lang_print id=6000107}' onClick='parent.TB_remove();'> 
    <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
    <input type='hidden' name='task' value='stick'>
    </form>
  </div>

  <div style='display: none;' id='confirmunstick'>
    <form action='forum_topic.php?forum_id={$forum_info.forum_id}&topic_id={$topic_info.forumtopic_id}&p={$p}' method='post'>
    <div style='margin-top: 10px;'>
      {lang_print id=6000108}
    </div>
    <br>
    <input type='submit' class='button' value='{lang_print id=6000109}' onClick='parent.TB_remove();'> 
    <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
    <input type='hidden' name='task' value='unstick'>
    </form>
  </div>
{/if}



{* HIDDEN DIV TO DISPLAY DELETE POST CONFIRMATION MESSAGE *}
<div style='display: none;' id='confirmdeletepost'>
  <form action='forum_topic.php' id='deletepostform' method='post'>
  <div style='margin-top: 10px;'>
    {lang_print id=6000120}
  </div>
  <br>
  <input type='submit' class='button' value='{lang_print id=175}' onClick='parent.TB_remove();'> 
  <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
  <input type='hidden' name='task' value='deletepost'>
  <input type='hidden' name='forum_id' value='{$forum_info.forum_id}'>
  <input type='hidden' name='topic_id' value='{$topic_info.forumtopic_id}'>
  <input type='hidden' name='post_id' id='post_id' value='0'>
  </form>
</div>




{include file='footer.tpl'}