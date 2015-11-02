{include file='header.tpl'}

{* $Id: group_discussion_view.tpl 247 2009-11-14 03:30:43Z phil $ *}

<div class='page_header'>
  <a href='{$url->url_create("group", $smarty.const.NULL, $group->group_info.group_id)}&v=discussions'>
    {$group->group_info.group_title}
  </a>
  &#187; {$topic_info.grouptopic_subject}
</div>

<br />

{* JAVASCRIPT FOR GOING TO SPECIFIED POST *}
{literal}
<script type="text/javascript">
<!-- 
window.addEvent('domready', function(){
  if($('post_{/literal}{$grouppost_id}{literal}')) {
    location.hash = 'post_{/literal}{$grouppost_id}{literal}';
  }
});
//-->
</script>
{/literal}


{* JAVASCRIPT FOR CONFIRMING TOPIC EDITING/DELETION *}
{if $group->user_rank == 2 || $group->user_rank == 1}
  {literal}
  <script type="text/javascript">
  <!--   
    function deleteTopic() {
      window.location = '{/literal}{$url->url_create("group", $smarty.const.NULL, $group->group_info.group_id)}{literal}&v=discussions&task=topic_delete&grouptopic_id={/literal}{$topic_info.grouptopic_id}{literal}';
    }
  //-->
  </script>
  {/literal}

  {* HIDDEN DIV TO DISPLAY CONFIRMATION MESSAGE *}
  <div style='display: none;' id='confirmedit'>
    <form action='{$url->url_create("group_discussion", $smarty.const.NULL, $group->group_info.group_id, $topic_info.grouptopic_id)}' method='post' target='_parent' onSubmit="{literal}if(this.topic_subject.value == '') { alert('{/literal}{lang_print id=2000299}{literal}'); return false; } else { return true; }{/literal}">
    <div style='margin-top: 10px; margin-bottom: 10px;'>{lang_print id=2000319}</div>
    {lang_print id=2000300}<br>
    <input type='text' name='topic_subject' id='topic_subject' value='{$topic_info.grouptopic_subject|escape:quotes}' maxlength='50' size='40'>
    <br><br>
    <input type='submit' class='button' value='{lang_print id=2000317}' />
    <input type='button' class='button' value='{lang_print id=2000266}' onClick='parent.TB_remove();parent.deleteTopic();' />
    <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();' />
    <input type='hidden' name='task' value='topic_edit' />
    </form>
  </div>
{/if}

<table cellpadding='0' cellspacing='0' width='100%' style='margin-bottom: 10px;'>
<tr>
<td>
  <div>
    <div style='float: left;'>
      <a href='{$url->url_create("group", $smarty.const.NULL, $group->group_info.group_id)}&v=discussions'>
        <img src='./images/icons/back16.gif' class='button' style='float: left;' border='0' />
        {lang_print id=2000303}
      </a>
    </div>
    {if $allowed_to_discuss}
    <div style='float: left; padding-left: 15px;'>
      <a href='javascript:void(0);' onClick="location.hash = 'reply'; $('group_discussion_reply').focus();">
        <img src='./images/icons/group_discussion_post16.gif' class='button' style='float: left;' border='0' />
        {lang_print id=2000304}
      </a>
    </div>
    {/if}
    {if $group->user_rank == 2 || $group->user_rank == 1}
      {if $topic_info.grouptopic_sticky}
        <div style='float: left; padding-left: 15px;'>
          <a href='{$url->url_create("group_discussion", $smarty.const.NULL, $group->group_info.group_id, $topic_info.grouptopic_id)}&p={$p}&task=unsticky'>
            <img src='./images/icons/group_discussion_unsticky16.gif' class='button' style='float: left;' border='0' />{lang_print id=2000315}
          </a>
        </div>
      {else}
        <div style='float: left; padding-left: 15px;'>
          <a href='{$url->url_create("group_discussion", $smarty.const.NULL, $group->group_info.group_id, $topic_info.grouptopic_id)}&p={$p}&task=sticky'>
            <img src='./images/icons/group_discussion_sticky16.gif' class='button' style='float: left;' border='0' />
            {lang_print id=2000305}
          </a>
        </div>
      {/if}
      {if $topic_info.grouptopic_closed}
        <div style='float: left; padding-left: 15px;'>
          <a href='{$url->url_create("group_discussion", $smarty.const.NULL, $group->group_info.group_id, $topic_info.grouptopic_id)}&p={$p}&task=open'>
            <img src='./images/icons/group_discussion_open16.gif' class='button' style='float: left;' border='0' />
            {lang_print id=2000316}
          </a>
        </div>
      {else}
        <div style='float: left; padding-left: 15px;'>
          <a href='{$url->url_create("group_discussion", $smarty.const.NULL, $group->group_info.group_id, $topic_info.grouptopic_id)}&p={$p}&task=close'>
            <img src='./images/icons/group_discussion_closed16.gif' class='button' style='float: left;' border='0' />
            {lang_print id=2000306}
          </a>
        </div>
      {/if}
      <div style='float: left; padding-left: 15px;'>
        <a href='javascript:void(0);' onClick="TB_show('{lang_print id=2000318}', '#TB_inline?height=150&width=300&inlineId=confirmedit', '', '../images/trans.gif');">
          <img src='./images/icons/group_discussion_edit16.gif' class='button' style='float: left;' border='0' />
          {lang_print id=2000318}
        </a>
      </div>
    {/if}
    <div style='clear: both; height: 0px;'></div>
  </div>
</td>

{* DISPLAY PAGINATION MENU IF APPLICABLE *}
{if $maxpage > 1}
  <td align='right'>
  {if $p != 1}<a href='{$url->url_create("group_discussion", $smarty.const.NULL, $group->group_info.group_id, $topic_info.grouptopic_id)}&p={math equation="p-1" p=$p}'>&#171; {lang_print id=182}</a>{else}<font class='disabled'>&#171; {lang_print id=182}</font>{/if}
  {if $p_start == $p_end}
    &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$total_posts} &nbsp;|&nbsp; 
  {else}
    &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_posts} &nbsp;|&nbsp; 
  {/if}
  {if $p != $maxpage}<a href='{$url->url_create("group_discussion", $smarty.const.NULL, $group->group_info.group_id, $topic_info.grouptopic_id)}&p={math equation="p+1" p=$p}'>{lang_print id=183} &#187;</a>{else}<font class='disabled'>{lang_print id=183} &#187;</font>{/if}
  </td>
{/if}
</tr>
</table>


{* LOOP THROUGH GROUP POSTS *}
<div class='group_discussion_table'>
  {section name=post_loop loop=$posts}
    <div class='group_discussion_row{cycle values="1,2"}'>
    <a name='post_{$posts[post_loop].grouppost_id}' id='post_{$posts[post_loop].grouppost_id}'></a>
    
    {* POST HAS BEEN DELETED *}
    {if $posts[post_loop].grouppost_deleted}
    
      <div class='group_discussion_deleted'>{lang_print id=2000321}</div>
      
    {* POST HAS NOT BEEN DELETED *}
    {else}

      <table cellpadding='0' cellspacing='0' width='100%'>
      <tr>
      <td class='group_discussion_item1' width='80'>
        <div style='font-size: 13px; font-weight: bold; margin-bottom: 5px;'>
        {if $posts[post_loop].grouppost_author->user_exists}
          <a href='{$url->url_create("profile", $posts[post_loop].grouppost_author->user_info.user_username)}'>
            {$posts[post_loop].grouppost_author->user_displayname}
          </a>
        {else}
          {if $posts[post_loop].grouppost_authoruser_id != 0}
            {lang_print id=1071}
          {else}
            {lang_print id=835}
          {/if}
        {/if}
        </div>
        {if $posts[post_loop].grouppost_author->user_exists}
          <a href='{$url->url_create("profile", $posts[post_loop].grouppost_author->user_info.user_username)}'>
            <img src='{$posts[post_loop].grouppost_author->user_photo("./images/nophoto.gif")}' class='photo' border='0' width='{$misc->photo_size($posts[post_loop].grouppost_author->user_photo("./images/nophoto.gif"),"125","125","w")}' />
          </a>
        {else}
          <img src='./images/nophoto.gif' class='photo' border='0' width='75' />
        {/if}
      </td>
      <td class='group_discussion_item2' style='padding: 10px;'>
        <table cellpadding='0' cellspacing='0' width='100%'>
        <tr>
        <td style='font-weight: bold;'>
          {capture assign="post_time"}{$datetime->cdate($setting.setting_timeformat, $datetime->timezone($posts[post_loop].grouppost_date, $global_timezone))}{/capture}
          {capture assign="post_date"}{$datetime->cdate($setting.setting_dateformat, $datetime->timezone($posts[post_loop].grouppost_date, $global_timezone))}{/capture}
          {lang_sprintf id=2000307 1=$post_time 2=$post_date} 
        </td>
        <td align='right' nowrap='nowrap' width='50%'>
          <div>
            {if ($posts[post_loop].grouppost_author->user_exists && $user->user_info.user_id == $posts[post_loop].grouppost_author->user_info.user_id) || $group->user_rank == 2 || $group->user_rank == 1}
            <div style='float: right; padding-left: 15px;'>
              <a href='javascript:void(0);' onClick="confirmDelete('{$posts[post_loop].grouppost_id}');">
                <img src='./images/icons/group_delete16.gif' border='0' class='button' style='float: left;' />
                {lang_print id=2000309}
              </a>
            </div>
            {/if}
            {if $posts[post_loop].grouppost_author->user_exists && $user->user_info.user_id == $posts[post_loop].grouppost_author->user_info.user_id}
            <div style='float: right; padding-left: 15px;'>
              <a href='javascript:void(0);' onClick="editPost('{$posts[post_loop].grouppost_id}');">
                <img src='./images/icons/group_edit16.gif' border='0' class='button' style='float: left;' />
                {lang_print id=2000308}
              </a>
            </div>
            {/if}
            {if $posts[post_loop].grouppost_author->user_exists && $user->user_exists && $user->user_info.user_id != $posts[post_loop].grouppost_author->user_info.user_id}
            <div style='float: right; padding-left: 15px;'>
              <a href="javascript:TB_show('{lang_print id=784}', 'user_messages_new.php?to_user={$posts[post_loop].grouppost_author->user_displayname}&to_id={$posts[post_loop].grouppost_author->user_info.user_username}&TB_iframe=true&height=400&width=450', '', './images/trans.gif');">
                <img src='./images/icons/message_inbox16.gif' border='0' class='button' style='float: left;' />
                {lang_print id=839}
              </a>
            </div>
            {/if}
            {if $allowed_to_discuss}
            <div style='float: right; padding-left: 15px;'>
              <a href='javascript:void(0);' onClick="quote('{$posts[post_loop].grouppost_id}', '{$posts[post_loop].grouppost_author->user_displayname|escape:quotes}');">
                <img src='./images/icons/group_discussion_quote16.gif' border='0' class='button' style='float: left;' />
                {lang_print id=2000322}
              </a>
            </div>
            {/if}
            <div style='clear: both; height: 0px;'></div>
          </div>
        </td>
        </tr>
        </table>
        
        <div class='group_discussion_daterow'>
          <div class='group_discussion_daterow_i2' id='post_div_{$posts[post_loop].grouppost_id}'>
            {$posts[post_loop].grouppost_body_formatted}
          </div>
        </div>
        
        <div style='display:none;visibility:hidden;' id='post_body_{$posts[post_loop].grouppost_id}'>{$posts[post_loop].grouppost_body}</div>
        
        {if !empty($posts[post_loop].grouppost_lastedit_date)}
        <div class='group_discussion_daterow'>
          <div class='group_discussion_daterow_i2'>
            {assign var='grouppost_lastedit_date' value=$datetime->time_since($posts[post_loop].grouppost_lastedit_date)}
            {capture assign="edited"}{lang_sprintf id=$grouppost_lastedit_date[0] 1=$grouppost_lastedit_date[1]}{/capture}
            {lang_sprintf id=2000395 1=$posts[post_loop].grouppost_lastedit_user_object->user_displayname 2=$edited}
          </div>
        </div>
        {/if}
      </td>
      </tr>
      </table>
      
    {/if}
    </div>

  {/section}
</div>


{lang_javascript ids=2000323}

{* JAVASCRIPT FOR CONFIRMING POST DELETION *}
{literal}
<script type="text/javascript">
<!-- 
  var post_id = 0;
  function confirmDelete(id)
  {
    post_id = id;
    TB_show('{/literal}{lang_print id=2000309}{literal}', '#TB_inline?height=150&width=300&inlineId=confirmdelete', '', '../images/trans.gif');
  }
  
  function deletePost()
  {
    window.location = {/literal}'{$url->url_create("group_discussion", $smarty.const.NULL, $group->group_info.group_id, $topic_info.grouptopic_id)}&p={$p}&task=post_delete&grouppost_id='+post_id{literal};
  }

  window.addEvent('domready', function()
  {
    var originalHeight = textarea_autogrow('group_discussion_reply');
  });

  var isEditing = false;
  function editPost(id)
  {
    if( isEditing ) return false;
    isEditing = true;
    
    var postElement = $('post_div_' + id);
    
    var height = postElement.offsetHeight + 10;
    var postText = $('post_body_'+id).innerHTML.replace(/<br>/gi, '\r\n').replace(/>/gi, '&gt;');
    
    var innerHTML = '';
    innerHTML += "<form action='group_discussion_view.php' method='post' target='ajaxframe' name='editPostForm' id='editPostForm'>";
    innerHTML += "<textarea name='grouppost_body' id='post_edit_" + id + "' style='height: " + height +" px; width: 100%;'>" + postText + "</textarea>";
    innerHTML += "<input type='hidden' name='task' value='post_edit'>";
    innerHTML += "<input type='hidden' name='grouppost_id' value='" + id + "'>";
    innerHTML += "<input type='hidden' name='group_id' value='{/literal}{$group->group_info.group_id}{literal}'>";
    innerHTML += "<input type='hidden' name='grouptopic_id' value='{/literal}{$topic_info.grouptopic_id}{literal}'>";
    innerHTML += "</form>";
    
    
    // Inject
    postElement.innerHTML = innerHTML;
    textarea_autogrow('post_edit_' + id);
    $('post_edit_' + id).focus();
    
    
    // Add events
    $('post_edit_' + id).addEvent('blur', function()
    {
      document.editPostForm.submit();
      isEditing = false;
    });

    $('editPostForm').addEvent('submit', function()
    {
      if($('post_edit_'+id).value == '')
      {
        alert('{/literal}{lang_print id=2000298}{literal}');
        return false;
      }
      else
      {
        return true;
      }
    });
  }


  function quote(id, user)
  {
    $('group_discussion_reply').value += '[quote='+user+']' + "\n" + $('post_body_'+id).innerHTML.replace(/<br>/g, "\n") + "\n" + '[/quote]' + "\n";
    location.hash = 'reply'; 
    $('group_discussion_reply').focus();
  }
//-->
</script>
{/literal}

{* HIDDEN DIV TO DISPLAY CONFIRMATION MESSAGE *}
<div style='display: none;' id='confirmdelete'>
  <div style='margin-top: 10px;'>{lang_print id=2000320}</div>
  <br />
  <input type='button' class='button' value='{lang_print id=175}' onClick='parent.TB_remove();parent.deletePost();' /> 
  <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();' />
</div>




<table cellpadding='0' cellspacing='0' width='100%' style='margin-top: 15px;'>
<tr>
<td valign='top'>
  {if $allowed_to_discuss}
    <a name='reply'></a>
    <form action='group_discussion_post.php' method='post' target='ajaxframe'>
    <div style='font-weight: bold;'>{lang_print id=2000310}</div>
    <div style='margin-top: 5px;'>
      <textarea style='width: 400px; height: 75px;' name='grouppost_body' id='group_discussion_reply'></textarea>
    </div>
    {if $setting.setting_group_discussion_html != ""}
    <div style='margin-top: 5px; margin-bottom: 5px;'>
      {lang_sprintf id=1034 1=$setting.setting_group_discussion_html|replace:",":", "}
    </div>
    {/if}
    {if $setting.setting_group_discussion_code == 1}
      <table cellspacing='0' cellpadding='0'>
      <tr>
      <td valign='top'>
        <img src='./images/secure.php' id='secure_image' border='0' height='20' width='67' class='signup_code' /><br />
        <a href="javascript:void(0);" onClick="javascript:$('secure_image').src = $('secure_image').src + '?' + (new Date()).getTime();">{lang_print id=975}</a>
      </td>
      <td style='padding-top: 4px;'><input type='text' name='comment_secure' id='comment_secure' class='text' size='6' maxlength='10'>&nbsp;</td>
      <td>
        {capture assign=tip}{lang_print id=691}{/capture}
        <img src='./images/icons/tip.gif' border='0' class='Tips1' title='{$tip|escape:quotes}' />
      </td>
      </tr>
      </table>
    {/if}
    <table cellspacing='0' cellpadding='0'>
    <tr><td>
      <div style='margin-top: 10px;'>
      <input type='submit' class='button' value='{lang_print id=2000311}'>
      <input type='hidden' name='task' value='reply_do'>
      <input type='hidden' name='group_id' value='{$group->group_info.group_id}'>
      <input type='hidden' name='grouptopic_id' value='{$topic_info.grouptopic_id}'>
      </form>
      </div>
    </td><td>
      <div id='post_error' style='color: #FF0000; padding-left: 10px; display: none;'></div>
    </td></tr>
    </table>
  {/if}
</td>
<td valign='top' align='right'>
  <table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
  <td>&nbsp;</td>
  {* DISPLAY PAGINATION MENU IF APPLICABLE *}
  {if $maxpage > 1}
    <td align='right'>
    {if $p != 1}<a href='{$url->url_create("group_discussion", $smarty.const.NULL, $group->group_info.group_id, $topic_info.grouptopic_id)}&p={math equation="p-1" p=$p}'>&#171; {lang_print id=182}</a>{else}<font class='disabled'>&#171; {lang_print id=182}</font>{/if}
    {if $p_start == $p_end}
      &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$total_posts} &nbsp;|&nbsp; 
    {else}
      &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_posts} &nbsp;|&nbsp; 
    {/if}
    {if $p != $maxpage}<a href='{$url->url_create("group_discussion", $smarty.const.NULL, $group->group_info.group_id, $topic_info.grouptopic_id)}&p={math equation="p+1" p=$p}'>{lang_print id=183} &#187;</a>{else}<font class='disabled'>{lang_print id=183} &#187;</font>{/if}
    </td>
  {/if}
  </tr>
  </table>
</td>
</tr>
</table>

<div style='clear: both; height: 0px;'></div>

{include file='footer.tpl'}