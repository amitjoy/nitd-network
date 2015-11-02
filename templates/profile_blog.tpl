
{* $Id: profile_blog.tpl 36 2009-01-27 02:35:37Z john $ *}

{* BEGIN BLOG ENTRIES *}
{if $owner->level_info.level_blog_create && $total_blogentries}

  <div class='profile_headline'>{lang_print id=1500043} ({$total_blogentries})</div>
  <div>
    {* LOOP THROUGH FIRST 5 BLOG ENTRIES *}
    {section name=blogentry_loop loop=$blogentries max=5}
    <div class='profile_blogentry'>
      <table cellpadding='0' cellspacing='0'>
        <tr>
          <td valign='top'>
            <a href='{$url->url_create("blog_entry", $owner->user_info.user_username, $blogentries[blogentry_loop].blogentry_id)}'>
              <img src='./images/icons/blog_blog16.gif' border='0' class='icon' />
            </a>
          </td>
          <td valign='top'>
            <div class='profile_blogentry_title'>
              <a href='{$url->url_create("blog_entry", $owner->user_info.user_username, $blogentries[blogentry_loop].blogentry_id)}'>
                {$blogentries[blogentry_loop].blogentry_title|truncate:35:"...":true}
              </a>
            </div>
            <div class='profile_blogentry_date'>
              {lang_print id=1500016}
              {assign var="blogentry_date" value=$datetime->time_since($blogentries[blogentry_loop].blogentry_date)}
              {lang_sprintf id=$blogentry_date[0] 1=$blogentry_date[1]}
            </div>
            {if !empty($blogentries[blogentry_loop].blogentrycat_languagevar_id) || !empty($blogentries[blogentry_loop].blogentrycat_title)}
            <div class='profile_blogentry_date'>
              Category:
              <a href='{$url->url_create("blog", $owner->user_info.user_username)}&category_id={$blogentries[blogentry_loop].blogentry_blogentrycat_id}'>
                {if !empty($blogentries[blogentry_loop].blogentrycat_languagevar_id)}
                  {capture assign=blogentrycat_title}{lang_print id=$blogentries[blogentry_loop].blogentrycat_languagevar_id}{/capture}
                {else}
                  {assign var=blogentrycat_title value=$blogentries[blogentry_loop].blogentrycat_title}
                {/if}
                {$blogentrycat_title|truncate:97}
              </a>
            </div>
            {/if}
            <div class='profile_blogentry_body'>
              {$blogentries[blogentry_loop].blogentry_body|strip_tags|truncate:160:"...":true}
            </div>
          </td>
        </tr>
      </table>
    </div>
    {/section}
    {* IF MORE THAN 5 ENTRIES, SHOW VIEW MORE LINKS *}
    {if $total_blogentries > 5}
    <div style='border-top: 1px solid #DDDDDD; padding-top: 10px;'>
      <div style='float: left;'>
        <a href='{$url->url_create("blog", $owner->user_info.user_username)}'>
          <img src='./images/icons/blog_subscribe16.gif' border='0' class='button' style='float: left;' />
          {lang_print id=1500121}
        </a>
      </div>
      <div style='clear: both; height: 0px;'></div>
    </div>
    {/if}
  </div>

{/if}