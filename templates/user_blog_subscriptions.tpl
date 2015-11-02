{include file='header.tpl'}

{* $Id: user_blog_subscriptions.tpl 5 2009-01-11 06:01:16Z john $ *}


{* JAVASCRIPT *}
<script type="text/javascript" src="./include/js/class_blog.js"></script>
<script type="text/javascript">
  
  SocialEngine.Blog = new SocialEngineAPI.Blog();
  SocialEngine.RegisterModule(SocialEngine.Blog);
  
</script>



<table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
    <td valign='top'>
      
      <img src='./images/icons/blog_blog48.gif' border='0' class='icon_big' style="margin-bottom: 15px;">
      <div class='page_header'>{lang_print id=1500077}</div>
      <div>
        {lang_print id=1500078}
      </div>
      <br />
      
    </td>
    <td valign='top' align='right'>
      
      <table cellpadding='0' cellspacing='0' width='130'>
      <tr><td class='button' nowrap='nowrap'><a href='user_blog.php'><img src='./images/icons/back16.gif' border='0' class='button'>{lang_print id=1500055}</a></td></tr>
      </table>
      
    </td>
  </tr>
</table>
<br />


{* DISPLAY PAGINATION MENU IF APPLICABLE *}
{if $maxpage > 1}
  <div class='center'>
    {if $p != 1}
      <a href='user_blog_subscriptions.php?s={$s}&p={math equation="p-1" p=$p}'>&#171; {lang_print id=182}</a>
    {else}
      <font class='disabled'>&#171; {lang_print id=182}</font>
    {/if}
    {if $p_start == $p_end}
      &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$blog_subscriptions_total} &nbsp;|&nbsp; 
    {else}
      &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$blog_subscriptions_total} &nbsp;|&nbsp; 
    {/if}
    {if $p != $maxpage}
      <a href='user_blog_subscriptions.php?s={$s}&p={math equation="p+1" p=$p}'>{lang_print id=183} &#187;</a>
    {else}
      <font class='disabled'>{lang_print id=183} &#187;</font>
    {/if}
  </div>
  <br />
{/if}


{* DISPLAY MESSAGE IF NO BLOG SUBSCRIPTIONS *}
{if !$blog_subscriptions_total}

  <table cellpadding='0' cellspacing='0' align='center'>
    <tr>
      <td class='result'>
        <img src='./images/icons/bulb16.gif' border='0' class='icon'>
        {lang_print id=1500079}
      </td>
    </tr>
  </table>


{* DISPLAY SUBSCRIPTIONS *}
{else}

  {section name=blog_subscriptions_loop loop=$blog_subscriptions_list}
    <div style='width: 500px;' id="seBlogSubscriptionRow_{$blog_subscriptions_list[blog_subscriptions_loop].blog_author->user_info.user_id}" class="blog_subscription">
      <table cellpadding='0' cellspacing='0' width='100%'>
      <tr>
      <td style='font-size: 9pt; font-weight: bold;'>
        <a href="{$url->url_create('profile', $blog_subscriptions_list[blog_subscriptions_loop].blog_author->user_info.user_username)}">{$blog_subscriptions_list[blog_subscriptions_loop].blog_author->user_displayname}</a>
      </td>
      <td align='right'>
        <a href="{$url->url_create('blog', $blog_subscriptions_list[blog_subscriptions_loop].blog_author->user_info.user_username)}">{lang_print id=1500083}</a>
        |
        <a href="javascript:void(0);" onclick="SocialEngine.Blog.unsubscribeBlog({$blog_subscriptions_list[blog_subscriptions_loop].blog_author->user_info.user_id});">{lang_print id=1500129}</a>
      </td>
      </tr>
      </table>
      {if !empty($blog_subscriptions_list[blog_subscriptions_loop].blogentry_id)}
        <div class='seBlogEntryDate'>{lang_print id=1500081} {$datetime->cdate("`$setting.setting_dateformat`", $datetime->timezone($blog_subscriptions_list[blog_subscriptions_loop].blogentry_date, $global_timezone))}</div>
        <div><a href="{$url->url_create('blog_entry', $blog_subscriptions_list[blog_subscriptions_loop].blog_author->user_info.user_username, $blog_subscriptions_list[blog_subscriptions_loop].blogentry_id)}">{$blog_subscriptions_list[blog_subscriptions_loop].blogentry_title}</a></div>
      {/if}
    </div>
  {/section}

{/if}


{include file='footer.tpl'}