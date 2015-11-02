{include file='header.tpl'}

{* $Id: blog.tpl 162 2009-04-30 01:43:11Z john $ *}

{* JAVASCRIPT *}
{lang_javascript id=861}
<script type="text/javascript" src="./include/js/class_blog.js"></script>
<script type="text/javascript">
  
  SocialEngine.Blog = new SocialEngineAPI.Blog();
  SocialEngine.RegisterModule(SocialEngine.Blog);
  
</script>




{* BLOG ENTRIE(S) *}
<table cellpadding='0' cellspacing='0' class="seBlogTable">
<tr>
<td class="seBlogColumnLeft" valign="top">


  {section name=entries_loop loop=$entries}
    <div class="seBlogEntry seBlogEntry{cycle values='1,2'}">
    
      {* MAKE SURE TITLE IS NOT BLANK *}
      {if $entries[entries_loop].blogentry_title != ""}
        {assign var='blogentry_title' value=$entries[entries_loop].blogentry_title}
      {else}
        {lang_block id=1500015 var=blogentry_title}{/lang_block}
      {/if}
      
      <table cellpadding='0' cellspacing='0' class="seBlogEntryTable">
        <tr>
          <td valign='top'>
            <div class='seBlogEntryTitle'>
              <a href='{$url->url_create("blog_entry", $owner->user_info.user_username, $entries[entries_loop].blogentry_id)}'>{$blogentry_title}</a>
            </div>
            <div class='seBlogEntryDate'>
              {lang_print id=1500016}
              {$datetime->cdate("`$setting.setting_dateformat`", $datetime->timezone("`$entries[entries_loop].blogentry_date`", $global_timezone))}
              -
              <a href='{$url->url_create("blog_entry", $owner->user_info.user_username, $entries[entries_loop].blogentry_id)}'>{lang_sprintf id=1500019 1=$entries[entries_loop].blogentry_totalcomments}</a>
              [ <a href='{$url->url_create("blog_entry", $owner->user_info.user_username, $entries[entries_loop].blogentry_id)}'>{lang_print id=1500021}</a> ]
              -
              <a href='{$url->url_create("blog_entry", $owner->user_info.user_username, $entries[entries_loop].blogentry_id)}'>{lang_sprintf id=1500020 1=$entries[entries_loop].blogentry_totaltrackbacks}</a>
              [ <a href='{$url->url_create("blog_trackback", $owner->user_info.user_username, $entries[entries_loop].blogentry_id)}'>{lang_print id=1500022}</a> ]
            </div>
            {* SHOW ENTRY CATEGORY *}
            {if !empty($entries[entries_loop].blogentry_blogentrycat_id)}
              <div class='seBlogEntryCategory'>
                Category:
                {if !$entries[entries_loop].blogentrycat_user_id}<a href='browse_blogs.php?c={$entries[entries_loop].blogentry_blogentrycat_id}'>{/if}
                {if !empty($entries[entries_loop].blogentrycat_languagevar_id)}
                  {capture assign=blogentrycat_title}{lang_print id=$entries[entries_loop].blogentrycat_languagevar_id}{/capture}
                {else}
                  {assign var=blogentrycat_title value=$entries[entries_loop].blogentrycat_title}
                {/if}
                {$blogentrycat_title|truncate:97}
                {if !$entries[entries_loop].blogentrycat_user_id}</a>{/if}
              </div>
            {/if}
            <div class='seBlogEntryBody' style="overflow: auto;">
              {$entries[entries_loop].blogentry_body|choptext:75:"<br>"}
            </div>
          </td>
        </tr>
      </table>
      
    </div>
    
  {sectionelse}
  
    <table cellpadding='0' cellspacing='0' style="width:100%;">
      <tr>
        <td class='result' style="text-align:left;">
          <img src='./images/icons/bulb22.gif' border='0' class='icon' />
          {lang_sprintf id=1500023 1=$owner->user_displayname 2=$url->url_create("profile", $owner->user_info.user_username)}
        </td>
      </tr>
    </table>
    
  {/section}
  
  
  {* STUFF TO SHOW IF ONLY ONE BLOG ENTRY *}
  {if $blogentry_id && $total_blogentries==1}
    
    <div class='seBlogComments'>
      
      {* RETURN AND REPORT LINKS AND SOCIAL BOOKMARKING *}
      <table cellpadding='0' cellspacing='0' border="0" width="100%"><tr><td valign="top">
        
        <div style='margin-bottom: 20px;'>
          <div class='button' style='float: left;'>
            <a href='{$url->url_create("blog", $owner->user_info.user_username)}'><img src='./images/icons/back16.gif' border='0' class='button'>{lang_sprintf id=1500024 1=$owner->user_displayname}</a>
          </div>
          <div class='button' style='float: left; padding-left: 20px;'>
            <a href="javascript:TB_show(SocialEngine.Language.Translate(861), 'user_report.php?return_url={$url->url_current()|escape:url}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');"><img src='./images/icons/report16.gif' border='0' class='button'>{lang_print id=861}</a>
          </div>
          <div style='clear: both; height: 0px;'></div>
        </div>
        
      </td><td align="right" valign="top">
        
        <div>
          <a rel="nofollow" target="_blank" href="http://delicious.com/save?v=5&noui&jump=close&url={$url->url_create('blog_entry', $owner->user_info.user_username, $blogentry_info.blogentry_id)|escape:url}&title={$blogentry_info.blogentry_title|escape:url}"><img src="./images/icons/socialbookmarking_delicious16.gif" border="0" alt="Delicious" /></a>
          <a rel="nofollow" target="_blank" href="http://digg.com/submit?phase=2&media=news&url={$url->url_create('blog_entry', $owner->user_info.user_username, $blogentry_info.blogentry_id)|escape:url}&title={$blogentry_info.blogentry_title|escape:url}"><img src="./images/icons/socialbookmarking_digg16.gif" border="0" alt="Digg" /></a>
          <a rel="nofollow" target="_blank" href="http://www.facebook.com/share.php?u={$url->url_create('blog_entry', $owner->user_info.user_username, $blogentry_info.blogentry_id)|escape:url}&t={$blogentry_info.blogentry_title|escape:url}"><img src="./images/icons/socialbookmarking_facebook16.gif" border="0" alt="Facebook" /></a>
          <a rel="nofollow" target="_blank" href="http://cgi.fark.com/cgi/fark/farkit.pl?u={$url->url_create('blog_entry', $owner->user_info.user_username, $blogentry_info.blogentry_id)|escape:url}&h={$blogentry_info.blogentry_title|escape:url}"><img src="./images/icons/socialbookmarking_fark16.gif" border="0" alt="Fark" /></a>
          <a rel="nofollow" target="_blank" href="http://www.myspace.com/Modules/PostTo/Pages/?u={$url->url_create('blog_entry', $owner->user_info.user_username, $blogentry_info.blogentry_id)|escape:url}&t={$blogentry_info.blogentry_title|escape:url}"><img src="./images/icons/socialbookmarking_myspace16.gif" border="0" alt="MySpace" /></a>
        </div>
        
      </td></tr></table>
      
      {* COMMENTS *}
      <div id="blog_{$blogentry_id}_postcomment"></div>
      <div id="blog_{$blogentry_id}_comments" style='margin-left: auto; margin-right: auto;'></div>
      
      {lang_javascript ids=39,155,175,182,183,184,185,187,784,787,829,830,831,832,833,834,835,854,856,891,1025,1026,1032,1034,1071}
      
      <script type="text/javascript">
        
        SocialEngine.BlogComments = new SocialEngineAPI.Comments({ldelim}
          'canComment' : {if $allowed_to_comment}true{else}false{/if},
          'commentHTML' : '{$setting.setting_comment_html}',
          'commentCode' : {if $setting.setting_comment_code}true{else}false{/if},
          
          'type' : 'blog',
          'typeIdentifier' : 'blogentry_id',
          'typeID' : {$blogentry_id},
          'typeTab' : 'blogentries',
          'typeCol' : 'blogentry',
          
          'initialTotal' : {$blogentry_info.blogentry_totalcomments|default:0},
          'paginate' : false,
          'cpp' : 5
        {rdelim});
        
        SocialEngine.RegisterModule(SocialEngine.BlogComments);
        
        // Backwards
        function addComment(is_error, comment_body, comment_date)
        {ldelim}
          SocialEngine.BlogComments.addComment(is_error, comment_body, comment_date);
        {rdelim}
        
        function getComments(direction)
        {ldelim}
          SocialEngine.BlogComments.getComments(direction);
        {rdelim}
        
      </script>
      
      
      
      {* TRACKBACKS *}
      {if !empty($trackback_list)}
      <h2>{lang_print id=1500025}</h2>
      <ul class="seBlogTrackbackList">
      {section name=trackback_loop loop=$trackback_list}
        
        <li style="margin-top: 10px; margin-bottom: 20px;">
          <div style="overflow: hidden;">
            <div class="profile_comment_author">
              <a href="{$trackback_list[trackback_loop].blogtrackback_url}">
                <b>{$trackback_list[trackback_loop].blogtrackback_name}</b>
              </a>
              <a href="{$trackback_list[trackback_loop].blogtrackback_url}">
                {$trackback_list[trackback_loop].blogtrackback_title}
              </a>
            </div>
            <div class="profile_comment_date">
              {$datetime->cdate("`$setting.setting_dateformat`", $datetime->timezone("`$trackback_list[trackback_loop].blogtrackback_date`", $global_timezone))}
            </div>
            <div class="profile_comment_body" id="profile_comment_body_{$trackback_list[trackback_loop].blogtrackback_id}">
              {$trackback_list[trackback_loop].blogtrackback_excerpt}
            </div>
            <div class="profile_comment_links">
              <a class="commentDeleteLink" href="javascript:void(0);">
                {lang_print id=155}
              </a>
              &nbsp;|&nbsp;
              <a class="commentReportLink" href="javascript:void(0);" onclick="javascript:TB_show(SocialEngine.Language.Translate(861), 'user_report.php?return_url={$url->url_current()}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');">
                {lang_print id=1500026}
              </a>
            </div>
          </div>
        </li>
        
      {/section}
      </ul>
      {/if}
      
    </div>
    
  {/if}


{* SIDEBAR *}
</td>
<td class="seBlogColumnRight" valign="top">

  <div class="seBlogColumnRightPadding">

    <div>
      {* USER PHOTO AND DISPLAY NAME *}
      <div style="display:block;width:100%;text-align:center;">
        <a href="{$url->url_create('profile', $owner->user_info.user_username)}"><img class='photo' src='{$owner->user_photo("./images/nophoto.gif")}' border='0' width="{$misc->photo_size($owner->user_photo('./images/nophoto.gif'),'240','240','w')}" /></a>
      </div>
      <div style="display:block;text-align:center;width:100%;font-weight: bold;margin-top:3px;">
        <a href="{$url->url_create('profile', $owner->user_info.user_username)}">{$owner->user_displayname}</a>
      </div>
    
      {* LINKS *}
      <div style='margin: 10px 0px 10px 0px;'>
        <div><a href="{$url->url_create('blog', $owner->user_info.user_username)}">{lang_print id=1500121}</a></div>
        {if $user->user_exists && $user->user_info.user_id!=$owner->user_info.user_id}
          <div class="seBlogSubscribe" {if $is_subscribed} style="display:none;"{/if}><a href="javascript:void(0);" onclick="SocialEngine.Blog.subscribeBlog(SocialEngine.Owner.user_info.user_id);">{lang_print id=1500027}</a></div>
          <div class="seBlogUnsubscribe"{if !$is_subscribed} style="display:none;"{/if}><a href="javascript:void(0);" onclick="SocialEngine.Blog.unsubscribeBlog(SocialEngine.Owner.user_info.user_id);">{lang_print id=1500028}</a></div>
        {/if}
        {if $user->user_info.user_id==$owner->user_info.user_id}
          <div><a href="user_blog.php">{lang_print id=1500055}</a></div>
          {if $blogentry_id && $total_blogentries==1}
            <div><a href="user_blog_entry.php?blogentry_id={$blogentry_id}">{lang_print id=1500170}</a></div>
          {/if}
        {/if}
      </div>
    
      {* SEARCH *}
      <div style='margin-bottom: 10px;'>
        <form method="post" action="{$url->url_create('blog', $owner->user_info.user_username)}">
        <table cellpadding='0' cellspacing='0'><tr><td>
          <input type="text" name="blog_search" value="{$blog_search}" />
        </td><td>
          <input class="button" type="submit" value="Search" />
        </td></tr></table>
        </form>
      </div>
    
      {* ARCHIVE *}
      {if !empty($archive_list)}
      <div class='blog_archive'>{lang_print id=1500029}</div>
      <ul class="seBlogArchiveList">
        {foreach from=$archive_list item=archive}
          <li>
            <a href="{$url->url_create('blog', $owner->user_info.user_username)}&date_start={$archive.date_start}&date_end={$archive.date_end}">
              {$archive.label}
            </a>
            ({$archive.count})
          </li>
        {/foreach}
      </ul>
      {/if}
      
      {* CATEGORIES *}
      {if !empty($category_list)}
      <div class='blog_archive'>{lang_print id=1500030}</div>
      <ul class="seBlogCategoryList">
        {foreach from=$category_list item=category}
          <li>
            <a href="{$url->url_create('blog', $owner->user_info.user_username)}&category_id={$category.blogentrycat_id}">
              {if !empty($category.blogentrycat_languagevar_id)}{capture assign=blogentrycat_title}{lang_print id=$category.blogentrycat_languagevar_id}{/capture}{else}{assign var=blogentrycat_title value=$category.blogentrycat_title}{/if}
              {$blogentrycat_title|truncate:25}
            </a>
            ({$category.blogentry_count})
          </li>
        {/foreach}
      </ul>
      {/if}
    </div>

  </div>

</td>
</tr>
</table>

<br />

{* DISPLAY PAGINATION MENU IF APPLICABLE *}
{if $maxpage > 1}
  
  <div class='center'>
    {if $p != 1}
      <a href='{$url->url_create("blog", $owner->user_info.user_username)}&p={math equation="p-1" p=$p}'>&#171; {lang_print id=182}</a>
    {else}
      <font class='disabled'>&#171; {lang_print id=182}</font>
    {/if}
    {if $p_start == $p_end}
      &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$total_blogentries} &nbsp;|&nbsp; 
    {else}
      &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_blogentries} &nbsp;|&nbsp; 
    {/if}
    {if $p != $maxpage}
      <a href='{$url->url_create("blog", $owner->user_info.user_username)}&p={math equation="p+1" p=$p}'>{lang_print id=183} &#187;</a>
    {else}
      <font class='disabled'>{lang_print id=183} &#187;</font>
    {/if}
  </div>
{/if}


{include file='footer.tpl'}
