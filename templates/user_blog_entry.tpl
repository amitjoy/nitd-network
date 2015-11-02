{include file='header.tpl'}

{* $Id: user_blog_entry.tpl 161 2009-04-28 21:14:59Z john $ *}

<script type="text/javascript" src="./include/js/class_blog.js"></script>
<script type="text/javascript" src="./include/fckeditor/fckeditor.js"></script>

<table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
  <td valign='top'>     
    <img src='./images/icons/blog_blog48.gif' border='0' class='icon_big' style="margin-bottom: 15px;">
    <div class='page_header'>{if !empty($blogentry_info.blogentry_id)}{lang_print id=1500130}{else}{lang_print id=1500053}{/if}</div>
    <div>{lang_print id=1500054}</div>
  </td>
  <td valign='top' align='right'>
    <table cellpadding='0' cellspacing='0' width='130'>
    <tr><td class='button' nowrap='nowrap'><a href='user_blog.php'><img src='./images/icons/back16.gif' border='0' class='button'>{lang_print id=1500055}</a></td></tr>
    </table>
  </td>
  </tr>
</table>

{* JAVASCRIPT *}
{lang_javascript ids=1500067,1500122,1500123}
{literal}
<script type='text/javascript'> 
<!--
  
  SocialEngine.Blog = new SocialEngineAPI.Blog();
  SocialEngine.RegisterModule(SocialEngine.Blog);
  
// -->
</script>
{/literal}


{* BLOG ENTRY INPUT *}
<div id="entry_main">

  <form action='user_blog_entry.php' method='post' name='blogform' id="blogform">
  <table cellpadding='0' cellspacing='0'>
  
    <tr>
      <td class='form1'>{lang_print id=1500056}</td>
      <td class='form2'><input type='text' name='blogentry_title' class='text' size='50' maxlength='100'{if !empty($blogentry_info.blogentry_title)} value='{$blogentry_info.blogentry_title}'{/if} /></td>
    </tr>
    
    {* SHOW BLOG CATEGORIES *}
    {if $user->level_info.level_blog_category_create || $blogentrycats|@count>0}
    <tr>
      <td class='form1'>{lang_print id=1500057}</td>
      <td class='form2'>
        <table cellpadding='0' cellspacing='0'>
          <tr>
            <td>
              <select name='blogentry_blogentrycat_id' onchange="$('new_blogentrycat_title').style.display = ( this.value==-1 ? '' : 'none' );">
                <option value='0'></option>
                {section name=blogentrycat_loop loop=$blogentrycats}
                <option value='{$blogentrycats[blogentrycat_loop].blogentrycat_id}'{if !empty($blogentry_info.blogentry_blogentrycat_id) && $blogentry_info.blogentry_blogentrycat_id==$blogentrycats[blogentrycat_loop].blogentrycat_id} SELECTED{/if}>
                  {if !empty($blogentrycats[blogentrycat_loop].blogentrycat_languagevar_id)}
                    {capture assign=blogentrycat_title}{lang_print id=$blogentrycats[blogentrycat_loop].blogentrycat_languagevar_id}{/capture}
                  {else}
                    {assign var=blogentrycat_title value=$blogentrycats[blogentrycat_loop].blogentrycat_title}
                  {/if}
                  {$blogentrycat_title|truncate:61}
                </option>
                {/section}
                {if $user->level_info.level_blog_category_create}<option value='-1'>{lang_print id=1500128}</option>{/if}
              </select>
            </td>
            <td>
              &nbsp;&nbsp;<input type="text" id="new_blogentrycat_title" name="new_blogentrycat_title" style="display:none;" />
            </td>
          </tr>
        </table>
      </td>
    </tr>
    {/if}
    
    {* SHOW COMMENTS LINK IF ANY HAVE BEEN POSTED *}
    {if !empty($comments_total)}
      <tr>
        <td class='form1'>
          {lang_print id=1500017}
        </td>
        <td class='form2'>
          {lang_sprintf id=1500058 1=$comments_total 2=$url->url_create("blog_entry", $user->user_info.user_username, $blogentry_info.blogentry_id)}
        </td>
      </tr>
    {/if}
    
  </table>
  <br />


  <script type="text/javascript">
  <!--
  var sToolbar;
  var oFCKeditor = new FCKeditor('blogentry_body');
  oFCKeditor.BasePath = "./include/fckeditor/";
  oFCKeditor.Config["ProcessHTMLEntities"] = false;
  oFCKeditor.Config["CustomConfigurationsPath"] = "../../js/blog_fckconfig.js";
  oFCKeditor.Height = "300";
  oFCKeditor.ToolbarSet = "se_blog";
  oFCKeditor.Value = '{if !empty($blogentry_info.blogentry_body)}{$blogentry_info.blogentry_body}{/if}';
  oFCKeditor.Config["SocialEngineUploadCustom"] = {if !empty($global_plugins.album) && $user->level_info.level_album_allow}true{else}false{/if};
  oFCKeditor.Create() ;
  //-->
  </script>
  
  
  
  {* SHOW SETTINGS LINK IF NECESSARY *}
  <table cellpadding='0' cellspacing='0' width='100%'>
    <tr>
      <td class='blog_options'>
        <div id='settings_privacy_show'>
          <a href="javascript:void(0);" onclick="$('settings_privacy').style.display='block';$('settings_privacy_hide').style.display='block';$('settings_privacy_show').style.display='none';">{lang_print id=1500059}</a>
        </div>
        <div id='settings_privacy_hide' style='display: none;'>
          <a href="javascript:void(0);" onclick="$('settings_privacy').style.display='none';$('settings_privacy_hide').style.display='none';$('settings_privacy_show').style.display='block';">{lang_print id=1500060}</a>
        </div>
      </td>
    </tr>
  </table>
  
  
  <div id='settings_privacy' class='blog_settings' style='display: none;'>
  
    {* SHOW SEARCH PRIVACY OPTIONS IF ALLOWED BY ADMIN *}
    {if $user->level_info.level_blog_search == 1}
      <b>{lang_print id=1500061}</b>
      <table cellpadding='0' cellspacing='0'>
        <tr>
          <td><input type='radio' name='blogentry_search' id='blogentry_search_1' value='1' {if !isset($blogentry_info.blogentry_search) ||  $blogentry_info.blogentry_search}checked='checked'{/if} /></td>
          <td><label for='blogentry_search_1'>{lang_print id=1500062}</label></td>
        </tr>
        <tr>
          <td><input type='radio' name='blogentry_search' id='blogentry_search_0' value='0' {if  isset($blogentry_info.blogentry_search) && !$blogentry_info.blogentry_search}checked='checked'{/if} /></td>
          <td><label for='blogentry_search_0'>{lang_print id=1500063}</label></td>
        </tr>
      </table>
      <br>
    {/if}

    {* SHOW PRIVACY OPTIONS *}
    {if $privacy_options|@count > 1}
      <b>{lang_print id=1500132}</b>
      <table cellpadding='0' cellspacing='0'>
      {foreach from=$privacy_options key=k item=v name=privacy_loop}
        <tr>
        <td><input type='radio' name='blogentry_privacy' id='privacy_{$k}' value='{$k}'{if (isset($blogentry_info.blogentry_privacy) && $k==$blogentry_info.blogentry_privacy) || (!isset($blogentry_info.blogentry_privacy) && $smarty.foreach.privacy_loop.first)} checked='checked'{/if} /></td>
        <td><label for='privacy_{$k}'>{lang_print id=$v}</label></td>
        </tr>
      {/foreach}
      </table>
      <br />
    {/if}

    {* SHOW COMMENT OPTIONS *}
    {if $comment_options|@count > 1}
      <b>{lang_print id=1500133}</b>
      <table cellpadding='0' cellspacing='0'>
      {foreach from=$comment_options key=k item=v name=comment_loop}
        <tr>
        <td><input type='radio' name='blogentry_comments' id='comments_{$k}' value='{$k}'{if (isset($blogentry_info.blogentry_comments) && $k==$blogentry_info.blogentry_comments) || (!isset($blogentry_info.blogentry_comments) && $smarty.foreach.comment_loop.first)} checked='checked'{/if} /></td>
        <td><label for='comments_{$k}'>{lang_print id=$v}</label></td>
        </tr>
      {/foreach}
      </table>
      <br />
    {/if}
    
    {* TRACKBACKS *}
    <b>{lang_print id=1500064}</b>
    <div>{lang_print id=1500126}</div>
    <table cellpadding='0' cellspacing='0'>
      <tr>
        <td>
          <textarea name="blogentry_trackbacks" style="width: 400px;">{if !empty($blogentry_info.blogentry_trackbacks)}{$blogentry_info.blogentry_trackbacks}{/if}</textarea>
        </td>
      </tr>
    </table>

  </div>
  <br />
  
  
  
  {* SEND ID FOR EDITING *}
  {if !empty($blogentry_info.blogentry_id)}
    <input type='hidden' name='blogentry_id' value='{$blogentry_info.blogentry_id}' />
  {/if}
  
  
  
  <table cellpadding='3' cellspacing='0'>
    <tr>
      <td>
        {lang_block id=1500065 var=langBlockTemp}<input type='submit' class='button' value='{$langBlockTemp}'>{/lang_block}
        <input type='hidden' name='task' value='dosave'>
        </form>
      </td>
      <td>
        {lang_block id=1500066 var=langBlockTemp}<input type='button' class='button' value='{$langBlockTemp}' onClick="SocialEngine.Blog.previewBlog(); return false;">{/lang_block}
      </td>
      <td>
        <form action='user_blog.php' method='post'>
        {lang_block id=39 var=langBlockTemp}<input type='submit' class='button' value='{$langBlockTemp}'>{/lang_block}
        </form>
      </td>
    </tr>
  </table>
  
</div>



{* BLOG ENTRY PREVIEW *}

<div id="entry_preview" style='display: none;'>
  <h2>{lang_print id=1500067} (<a href="javascript:void(0)" onClick="parent.TB_remove();">{lang_print id=1500068}</a>)</h2>
  <div id='previewpane' style='padding:0px;border:1px dashed #AAAAAA;'></div>
</div>



{include file='footer.tpl'}