{include file='header.tpl'}


<script type="text/javascript" src="./include/fckeditor/fckeditor.js"></script>

<table cellpadding='0' cellspacing='0' width='100%'>
<tr>
<td style='vertical-align: top;'>
  <div class='page_header'>
    <a href='forum.php'>{lang_print id=6000061}</a> &#187; 
    <a href='forum_view.php?forum_id={$forum_info.forum_id}'>{lang_print id=$forum_info.forum_title}</a> &#187; 
    {if $is_reply || $is_edit}
      <a href='forum_topic.php?forum_id={$forum_info.forum_id}&topic_id={$topic_info.forumtopic_id}'>{$topic_info.forumtopic_subject}</a> &#187;
      {if $is_edit}
	{lang_print id=6000124}
      {else}
        {lang_print id=6000089}
      {/if}
    {else}
      {lang_print id=6000065}
    {/if}
  </div>
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



{* SHOW ERROR *}
{if $is_error != 0}
  <br>
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='error'><img src='./images/error.gif' border='0' class='icon'>{lang_print id=$is_error}</td></tr>
  </table>
{/if}

<br>

<form action='forum_new.php' method='post' enctype='multipart/form-data'>
<table cellpadding='0' cellspacing='0'>
{if (!$is_reply && !$is_edit) || ($is_edit && $show_title)}
  <tr>
  <td class='form1'>{lang_print id=6000088}</td>
  <td class='form2'><input type='text' class='text' name='topic_title' value='{$topic_title}' size='50' maxlength='50'></td>
  </tr>
{/if}
<tr>
<td class='form1'>{lang_print id=6000090}</td>
<td class='form2'>

  <script type="text/javascript">
  <!--
  var sToolbar;
  var oFCKeditor = new FCKeditor('post_body');
  oFCKeditor.BasePath = "./include/fckeditor/";
  oFCKeditor.Config["ProcessHTMLEntities"] = false;
  oFCKeditor.Config["CustomConfigurationsPath"] = "../../js/forum_fckconfig.js";
  oFCKeditor.Height = "250";
  oFCKeditor.Width = "700";
  oFCKeditor.ToolbarSet = "se_forum";
  oFCKeditor.Value = '{$post_body|replace:"\r":''|replace:"\n":''}';
  oFCKeditor.Config["SocialEngineUploadCustom"] = false;
  oFCKeditor.Create() ;
  //-->
  </script>

</td>
</tr>
<tr>
<td class='form1'>{lang_print id=6000086}</td>
<td class='form2'>
  {if $is_edit && $postmedia_info.is_media}
    <div id='postmedia_img'>
      <img src='{$postmedia_info.forummedia_path}' border='0' width='{$misc->photo_size($postmedia_info.forummedia_path,"200","200","w")}'>
      <br>
      <a href='javascript:void(0);' onClick="$('postmedia_img').destroy();$('postmedia_upload').style.display='block';">{lang_print id=6000126}</a>
      <input type='hidden' name='postmedia_keep' value='1'>
    </div>
    <div id='postmedia_upload' style='display:none;'>
      <input type='file' class='text' name='post_media' size='50'>
      <div style='width:425px;'>{lang_print id=6000092}</div>
    </div>
  {else}
    <input type='file' class='text' name='post_media' size='50'>
    <div style='width:425px;'>{lang_print id=6000092}</div>
  {/if}
</td>
</tr>

{if $setting.setting_forum_code == 1}
  <tr>
  <td class='form1'>&nbsp;</td>
  <td class='form2'>
    <table cellspacing='0' cellpadding='0'>
    <tr>
      <td><a href="javascript:void(0);" onClick="javascript:$('secure_image').src = $('secure_image').src + '?' + (new Date()).getTime();"><img src='./images/secure.php' id='secure_image' border='0' height='20' width='67' class='signup_code' /></a></td>
      <td style='padding-top: 4px; padding-left: 3px;'><input type='text' name='comment_secure' id='comment_secure' class='text' size='6' maxlength='10'>&nbsp;</td>
      <td>
        {capture assign=tip}{lang_print id=691}{/capture}
        <img src='./images/icons/tip.gif' border='0' class='Tips1' title='{$tip|replace:quotes}' />
      </td>
    </tr>
    </table>
  </td>
  </tr>
{/if}


<tr>
<td class='form1'>&nbsp;</td>
<td class='form2'>
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td>
    {if $is_reply}
      <input type='submit' class='button' value='{lang_print id=6000085}'>
      <input type='hidden' name='task' value='reply'>
      <input type='hidden' name='topic_id' value='{$topic_info.forumtopic_id}'>
    {elseif $is_edit}
      <input type='submit' class='button' value='{lang_print id=6000125}'>
      <input type='hidden' name='task' value='edit'>
      <input type='hidden' name='topic_id' value='{$topic_info.forumtopic_id}'>
      <input type='hidden' name='post_id' value='{$post_id}'>
    {else}
      <input type='submit' class='button' value='{lang_print id=6000087}'>
      <input type='hidden' name='task' value='create'>
    {/if}
    <input type='hidden' name='forum_id' value='{$forum_info.forum_id}'>
    <input type='hidden' name='MAX_FILE_SIZE' value='50000000'>
  </td>
  <td>&nbsp;&nbsp;{lang_print id=6000091} <a href='{if $is_edit}forum_topic.php?forum_id={$forum_info.forum_id}&topic_id={$topic_info.forumtopic_id}&post_id={$post_id}#post_{$post_id}{elseif $is_reply}forum_topic.php?forum_id={$forum_info.forum_id}&topic_id={$topic_info.forumtopic_id}{else}forum.php?forum_id={$forum_info.forum_id}{/if}'>{lang_print id=747}</td>
  </tr>
  </table>
</td>
</tr>
</table>
</form>





{include file='footer.tpl'}