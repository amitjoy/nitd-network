{include file='header.tpl'}

<table class='tabs' cellpadding='0' cellspacing='0'>
<tr>
<td class='tab0'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_article.php'>{lang_print id=11150542}</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_article_settings.php'>{lang_print id=11150543}</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab1' NOWRAP><a href='articles.php'>{lang_print id=11150544}</a></td>
<td class='tab3'>&nbsp;</td>
</tr>
</table>

{* JAVASCRIPT FOR ADDING COMMENT *}
{literal}
<script type='text/javascript'>
<!--
var comment_changed = 0;
var first_comment = 1;
var last_comment = {/literal}{$comments|@count}{literal};
var next_comment = last_comment+1;
var total_comments = {/literal}{$total_comments}{literal};

function removeText(commentBody) {
  if(comment_changed == 0) {
    commentBody.value='';
    commentBody.style.color='#000000';
    comment_changed = 1;
  }
}

function addText(commentBody) {
  if(commentBody.value == '') {
    commentBody.value = '{/literal}{lang_print id=11150531}{literal}';
    commentBody.style.color = '#888888';
    comment_changed = 0;
  }
}

function checkText() {
  if(comment_changed == 0) { 
    var commentBody = document.getElementById('comment_body');
    commentBody.value=''; 
  }
  var commentSubmit = document.getElementById('comment_submit');
  commentSubmit.value = '{/literal}{lang_print id=11150532}{literal}';
  commentSubmit.disabled = true;
  
}

function addComment(is_error, comment_body, comment_date) {
  if(is_error == 1) {
    var commentError = document.getElementById('comment_error');
    commentError.style.display = 'block';
    if(comment_body == '') {
      commentError.innerHTML = '{/literal}{lang_print id=11150533}{literal}';
    } else {
      commentError.innerHTML = '{/literal}{lang_print id=11150534}{literal}';
    }
    var commentSubmit = document.getElementById('comment_submit');
    commentSubmit.value = '{/literal}{lang_print id=11150524}{literal}';
    commentSubmit.disabled = false;
  } else {
    var commentError = document.getElementById('comment_error');
    commentError.style.display = 'none';
    commentError.innerHTML = '';

    var commentBody = document.getElementById('comment_body');
    commentBody.value = '';
    addText(commentBody);

    var commentSubmit = document.getElementById('comment_submit');
    commentSubmit.value = '{/literal}{lang_print id=11150524}{literal}';
    commentSubmit.disabled = false;

    if(document.getElementById('comment_secure')) {
      var commentSecure = document.getElementById('comment_secure');
      commentSecure.value=''
      var secureImage = document.getElementById('secure_image');
      secureImage.src = secureImage.src + '?' + (new Date()).getTime();
    }

    total_comments++;
    var totalComments = document.getElementById('total_comments');
    totalComments.innerHTML = total_comments;

    if(total_comments > 10) {
      var oldComment = document.getElementById('comment_'+first_comment);
      if(oldComment) { oldComment.style.display = 'none'; first_comment++; }
    }

    var newComment = document.createElement('div');
    var divIdName = 'comment_'+next_comment;
    newComment.setAttribute('id',divIdName);
    var newTable = "<table cellpadding='0' cellspacing='0' width='100%'><tr><td class='profile_item1' width='80'>";
    {/literal}
      {if $user->user_info.user_id != 0}
        newTable += "<a href='{$url->url_create('profile',$user->user_info.user_username)}'><img src='{$user->user_photo('./images/nophoto.gif')}' class='photo' border='0' width='{$misc->photo_size($user->user_photo('./images/nophoto.gif'),'75','75','w')}'></a></td><td class='profile_item2'><table cellpadding='0' cellspacing='0' width='100%'><tr><td class='profile_comment_author'><b><a href='{$url->url_create('profile',$user->user_info.user_username)}'>{$user->user_info.user_username}</a></b> - {$datetime->cdate("`$setting.setting_timeformat` `$article35` `$setting.setting_dateformat`", $datetime->timezone($smarty.now, $global_timezone))}</td><td class='profile_comment_author' align='right' nowrap='nowrap'><a href='{$url->url_create('profile',$user->user_info.user_username)}#comments'>{lang_print id=11150540}</a>&nbsp;|&nbsp;<a href='user_messages_new.php?to={$user->user_info.user_username}'>{lang_print id=11150527}</a></td>";
      {else}
        newTable += "<img src='./images/nophoto.gif' class='photo' border='0' width='75'></td><td class='profile_item2'><table cellpadding='0' cellspacing='0' width='100%'><tr><td class='profile_comment_author'><b>{lang_print id=11150526}</b> - {$datetime->cdate("`$setting.setting_timeformat` `$article35` `$setting.setting_dateformat`", $datetime->timezone($smarty.now, $global_timezone))}</td><td class='profile_comment_author' align='right' nowrap='nowrap'>&nbsp;</td>";
      {/if}
      newTable += "</tr><tr><td colspan='2' class='profile_comment_body'>"+comment_body+"</td></tr></table></td></tr></table>";
    {literal}
    newComment.innerHTML = newTable;
    var profileComments = document.getElementById('profile_comments');
    var prevComment = document.getElementById('comment_'+last_comment);
    profileComments.insertBefore(newComment, prevComment);
    next_comment++;
    last_comment++;
  }
}
//-->
</script>
{/literal}

{assign var=article_date_start value=$datetime->timezone($article->article_info.article_date_start, $global_timezone)}
{assign var=start_dateformat value="`$setting.setting_dateformat`, `$setting.setting_timeformat`"}


<img src='./images/icons/article48.gif' border='0' class='icon_big'>
<div class='page_header'>{lang_print id=11150554}</div>
<div>{lang_print id=11150517} <a href='articles.php'>{lang_print id=11150518}</a> 
{if $article_category != ""} {if $parent_category != ""} >> <a href='articles.php?articlecat_id={$parent_category.articlecat_id}'>{$parent_category.articlecat_title}</a>{/if}  >> <a href='articles.php?articlecat_id={$article->article_info.article_articlecat_id}'>{$article_category}</a>{/if}</div>

<br />
{assign var='status_date' value=$datetime->time_since($article->article_info.article_dateupdated)}
<div class='article_entry {if $article->article_info.article_featured == 1}article_featured{/if}'>
  <div class='article_title'>{$article->article_info.article_title} {if $article->article_info.article_featured == 1}<img src="./images/icons/article_featured.gif" />{/if}</div>
  <div class='article_meta'>{lang_print id=11150520} {$datetime->cdate("`$start_dateformat`", $article_date_start)} 
   {if $article->article_info.article_dateupdated != 0}{lang_print id=11150513} {lang_sprintf id=$status_date[0] 1=$status_date[1]}{/if}
   {lang_print id=11150506} <a href='{$url->url_create('profile', $articleowner_info.user_username)}'>{$article->article_owner->user_displayname}</a></div>
  <div class='article_tag'>{lang_print id=11150555}
   {foreach from=$article_tags item=tagname}
     <a href="articles.php?tag={$tagname}">{$tagname}</a>
   {/foreach}
  </div>
  <div class='article_body'>{$article->article_info.article_body}</div>
</div>

<div class='article_tools'>

<img src='./images/icons/article_view.gif' border='0' class='icon'> {lang_print id=11150507} {$article->article_info.article_views} {lang_print id=11150508}
&nbsp;&nbsp;&nbsp;
  {* SHOW BROWSE IMAGES BUTTON *}
  {if $total_files != 0}
    <a href='article_album.php?article_id={$article->article_info.article_id}'><img src='./images/icons/article_album.gif' border='0' class='icon'>{lang_print id=11150529} ({$total_files})</a>&nbsp;&nbsp;&nbsp;
  {/if}
<a href='user_report.php?return_url={$url->url_current()}'><img src='./images/icons/report16.gif' class='icon' border='0'>{lang_print id=11150538}</a>
</div>

    {* SHOW PHOTOS *}
    {if $total_files != 0}
      <table cellpadding='0' cellspacing='0' width='100%' style='margin-bottom: 10px;'>
      <tr>
      <td class='header'>
        {lang_print id=11150521} ({$total_files})
        &nbsp;<font class='profile_small'>[ <a href='article_album.php?article_id={$article->article_info.article_id}'>{lang_print id=11150522}</a> ]</font>
      </td>
      </tr>
      <tr>
      <td class='profile'>
        {* LOOP THROUGH PHOTOS *}
        {section name=file_loop loop=$files max=5}

          {* IF IMAGE, GET THUMBNAIL *}
          {if $files[file_loop].articlemedia_ext == "jpeg" OR $files[file_loop].articlemedia_ext == "jpg" OR $files[file_loop].articlemedia_ext == "gif" OR $files[file_loop].articlemedia_ext == "png" OR $files[file_loop].articlemedia_ext == "bmp"}
            {assign var='file_dir' value=$article->article_dir($article->article_info.article_id)}
            {assign var='file_src' value="`$file_dir``$files[file_loop].articlemedia_id`_thumb.jpg"}
          {* SET THUMB PATH FOR AUDIO *}
          {elseif $files[file_loop].articlemedia_ext == "mp3" OR $files[file_loop].articlemedia_ext == "mp4" OR $files[file_loop].articlemedia_ext == "wav"}
            {assign var='file_src' value='./images/icons/audio_big.gif'}
          {* SET THUMB PATH FOR VIDEO *}
          {elseif $files[file_loop].articlemedia_ext == "mpeg" OR $files[file_loop].articlemedia_ext == "mpg" OR $files[file_loop].articlemedia_ext == "mpa" OR $files[file_loop].articlemedia_ext == "avi" OR $files[file_loop].articlemedia_ext == "swf" OR $files[file_loop].articlemedia_ext == "mov" OR $files[file_loop].articlemedia_ext == "ram" OR $files[file_loop].articlemedia_ext == "rm"}
            {assign var='file_src' value='./images/icons/video_big.gif'}
          {* SET THUMB PATH FOR UNKNOWN *}
          {else}
            {assign var='file_src' value='./images/icons/file_big.gif'}
          {/if}

          {* START NEW ROW *}
          {cycle name="startrow2" values="<table cellpadding='0' cellspacing='0'><tr>,,,,"}
          <td class='profile_friend'><a href='article_album_file.php?article_id={$article->article_info.article_id}&articlemedia_id={$files[file_loop].articlemedia_id}'><img src='{$file_src}' class='photo' border='0' width='{$misc->photo_size($file_src,'75','75','w')}'></a></td>
          {* END ROW AFTER 5 RESULTS *}
          {if $smarty.section.file_loop.last == true}
            </tr></table>
          {else}
            {cycle name="endrow2" values=",,,,</tr></table>"}
          {/if}
        {/section}
      </td>
      </tr>
      </table>
    {/if}
    {* END PHOTOS *}


    {* BEGIN COMMENTS *}
    <a name="comments"></a>
    <table cellpadding='0' cellspacing='0' width='100%'>
    <tr>  
    <td class='header'>
      {lang_print id=11150523} (<span id='total_comments'>{$total_comments}</span>)
      {if $total_comments != 0}&nbsp;[ <a href='article_comments.php?article_id={$article->article_info.article_id}'>{lang_print id=11150525}</a> ]{/if}
    </td>
    </tr>
      {if $allowed_to_comment != 0}
        <tr>
        <td class='profile_postcomment'>
        <form action='article_comments.php' method='post' target='AddCommentWindow' onSubmit='checkText()'>
        <textarea name='comment_body' id='comment_body' rows='2' cols='65' onfocus='removeText(this)' onblur='addText(this)' style='color: #888888; width: 100%;'>{lang_print id=11150531}</textarea>

          <table cellpadding='0' cellspacing='0' width='100%'>
          <tr>
          {if $setting.setting_comment_code == 1}
            <td width='75' valign='top'><img src='./images/secure.php' id='secure_image' border='0' height='20' width='67' class='signup_code'></td>
            <td width='68' style='padding-top: 4px;'><input type='text' name='comment_secure' id='comment_secure' class='text' size='6' maxlength='10'></td>
            <td width='10'><img src='./images/icons/tip.gif' border='0' class='icon' onMouseover="tip('{lang_print id=11150536}')"; onMouseout="hidetip()"></td>
          {/if}
          <td align='right' style='padding-top: 5px;'>
          <input type='submit' id='comment_submit' class='button' value='{lang_print id=11150524}'>
          <input type='hidden' name='article_id' value='{$article->article_info.article_id}'>
          <input type='hidden' name='task' value='dopost'>
          </form>
          </td>
          </tr>
          </table>
        <div id='comment_error' style='color: #FF0000; display: none;'></div>
        <iframe name='AddCommentWindow' style='display: none' src=''></iframe>
	</div>
	</div>
	</td>
	</tr>
      {/if}
	<tr>
	<td class='profile' id='profile_comments'>

      {* LOOP THROUGH EVENT COMMENTS *}
      {section name=comment_loop loop=$comments}
        <div id='comment_{math equation='t-c' t=$comments|@count c=$smarty.section.comment_loop.index}'>
        <table cellpadding='0' cellspacing='0' width='100%'>
        <tr>
        <td class='profile_item1' width='80'>
          {if $comments[comment_loop].comment_author->user_info.user_id != 0}
            <a href='{$url->url_create('profile',$comments[comment_loop].comment_author->user_info.user_username)}'><img src='{$comments[comment_loop].comment_author->user_photo('./images/nophoto.gif')}' class='photo' border='0' width='{$misc->photo_size($comments[comment_loop].comment_author->user_photo('./images/nophoto.gif'),'75','75','w')}'></a>
          {else}
            <img src='./images/nophoto.gif' class='photo' border='0' width='75'>
          {/if}
        </td>
        <td class='profile_item2'>
          <table cellpadding='0' cellspacing='0' width='100%'>
          <tr>
          <td class='profile_comment_author'><b>{if $comments[comment_loop].comment_author->user_info.user_id != 0}<a href='{$url->url_create('profile',$comments[comment_loop].comment_author->user_info.user_username)}'>{$comments[comment_loop].comment_author->user_info.user_username}</a>{else}{lang_print id=11150526}{/if}</b> - {$datetime->cdate("`$setting.setting_timeformat` `$article35` `$setting.setting_dateformat`", $datetime->timezone($comments[comment_loop].comment_date, $global_timezone))}</td>
          <td class='profile_comment_author' align='right' nowrap='nowrap'><a href='{$url->url_create('profile',$comments[comment_loop].comment_author->user_info.user_username)}#comments'>{lang_print id=11150540}</a>&nbsp;|&nbsp;<a href='user_messages_new.php?to={$comments[comment_loop].comment_author->user_info.user_username}'>{lang_print id=11150527}</a></td>
          </tr>
          <tr>
          <td colspan='2' class='profile_comment_body'>{$comments[comment_loop].comment_body}</td>
          </tr>
          </table>
        </td>
        </tr>
        </table>
        </div>
      {/section}


    </td>
    </tr>
    </table>
    {* END COMMENTS *}    
    


{include file='footer.tpl'}