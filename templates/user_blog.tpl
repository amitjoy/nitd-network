{include file='header.tpl'}

{* $Id: user_blog.tpl 21 2009-01-15 08:46:59Z john $ *}

<img src='./images/icons/blog_blog48.gif' border='0' class='icon_big' style="margin-bottom: 15px;" />
<div class='page_header'>{lang_print id=1500044}</div>
<div>
  {lang_print id=1500045}
</div>
<br />


{* SHOW BUTTONS *}
<div style='margin-top: 20px;'>
  {if $user->level_info.level_blog_create}
  <div class='button' style='float: left; padding-right: 20px;'>
    <a href='user_blog_entry.php'><img src='./images/icons/blog_newentry16.gif' border='0' class='button' />{lang_print id=1500046}</a>
  </div>
  {/if}
  {if $user->level_info.level_blog_view}
  <div class='button' style='float: left; padding-right: 20px;'>
    <a href='user_blog_subscriptions.php'><img src='./images/icons/blog_settings16.gif' border='0' class='button' />{lang_print id=1500047}</a>
  </div>
  <div class='button' style='float: left; padding-right: 20px;'>
    <a href='user_blog_settings.php'><img src='./images/icons/blog_settings16.gif' border='0' class='button' />{lang_print id=1500001}</a>
  </div>
  {/if}
  {if $user->level_info.level_blog_create}
  <div class='button' style='float: left; padding-right: 20px;'>
    <a href="javascript:void(0);" onclick="$('blog_search').style.display = ( $('blog_search').style.display=='block' ? 'none' : 'block');"><img src='./images/icons/search16.gif' border='0' class='button' />{lang_print id=1500048}</a>
  </div>
  {/if}
  <div style='clear: both; height: 0px;'></div>
</div>
<br />


{* SEARCH FIELD *}
<div style='width: 550px;border: 1px solid #AAAAAA; background: #EEEEEE;margin-bottom:8px;{if $search == ""} display: none;{/if}' id='blog_search'>
  <div style='padding: 10px;'>
    <form action='user_blog.php' name='searchform' method='post'>
    <table cellpadding='0' cellspacing='0' align='center'>
    <tr>
    <td><b>{lang_print id=1500049}</b>&nbsp;&nbsp;</td>
    <td><input type='text' name='search' maxlength='100' size='30' value='{$search}' />&nbsp;</td>
    <td>{lang_block id=646 var=langBlockTemp}<input type='submit' class='button' value='{$langBlockTemp}' />{/lang_block}</td>
    </tr>
    </table>
    <input type='hidden' name='s' value='{$s}' />
    <input type='hidden' name='p' value='{$p}' />
    </form>
  </div>
</div>



{* JAVASCRIPT *}
{lang_javascript ids=861,1500122,1500123}
<script type="text/javascript" src="./include/js/class_blog.js"></script>
<script type="text/javascript">
  
  SocialEngine.Blog = new SocialEngineAPI.Blog();
  SocialEngine.RegisterModule(SocialEngine.Blog);
  
</script>



{if $user->level_info.level_blog_create}

  {* DISPLAY MESSAGE IF NO BLOG ENTRIES *}
  {if !$total_blogentries}

    <table cellpadding='0' cellspacing='0' align='center'>
      <tr>
        <td class='result'>
          {if !empty($search)}
            <img src='./images/icons/bulb16.gif' border='0' class='icon' />
            {lang_print id=1500050}
          {else}
            <img src='./images/icons/bulb16.gif' border='0' class='icon' />
            {lang_sprintf id=1500051 1='user_blog_entry.php'}
          {/if}
        </td>
      </tr>
    </table>


  {* DISPLAY ENTRIES *}
  {else}
    
    <div style='width: 550px;'>

      {* DISPLAY PAGINATION MENU IF APPLICABLE *}
      {if $maxpage > 1}
        <div style='text-align: center; padding: 10px;'>
          {if $p != 1}
            <a href='user_blog.php?s={$s}&search={$search}&p={math equation="p-1" p=$p}'>&#171; {lang_print id=182}</a>
          {else}
            <font class='disabled'>&#171; {lang_print id=182}</font>
          {/if}
          {if $p_start == $p_end}
            &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$total_blogentries} &nbsp;|&nbsp; 
          {else}
            &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_blogentries} &nbsp;|&nbsp; 
          {/if}
          {if $p != $maxpage}
            <a href='user_blog.php?s={$s}&search={$search}&p={math equation="p+1" p=$p}'>{lang_print id=183} &#187;</a>
          {else}
            <font class='disabled'>{lang_print id=183} &#187;</font>
          {/if}
        </div>
      {/if}

      {* LIST BLOG ENTRIES *}
      <div class='blog_list'>
      <form action='user_blog.php' name='entryform' method='post'>
      {section name=blogentry_loop loop=$blogentries}
        <div id="seBlogRow_{$blogentries[blogentry_loop].blogentry_id}" class='{cycle values="blog_list1,blog_list2"}'>
          <table cellpadding='0' cellspacing='0' width='100%'>
            <tr>
              <td style='padding-top: 2px; vertical-align: top;' width='1'>
                <input type='checkbox' name='delete_blogentries[]' value='{$blogentries[blogentry_loop].blogentry_id}' />
              </td>
              <td style='padding-left: 5px;'>
                <div style='font-size: 10pt; font-weight: bold;'>
                  <a href='{$url->url_create("blog_entry", $user->user_info.user_username, $blogentries[blogentry_loop].blogentry_id)}'>
                    {$blogentries[blogentry_loop].blogentry_title|truncate:50:"...":false}
                  </a>
                </div>
                <div style='font-size: 7pt; color: #777777;'>
                  {lang_sprintf id=1500127 1=$datetime->cdate("`$setting.setting_dateformat`", $datetime->timezone($blogentries[blogentry_loop].blogentry_date, $global_timezone))}
                  - {lang_sprintf id=1500042 1=$blogentries[blogentry_loop].blogentry_totalcomments}
                </div>
                {if !empty($blogentries[blogentry_loop].blogentry_body)}
                <div style='font-size: 7pt; color: #777777;padding-top: 6px; width: 420px;'>
                  {$blogentries[blogentry_loop].blogentry_body|strip_tags|truncate:200}
                </div>
                {/if}
              </td>
              <td style='vertical-align: top; text-align: right; padding-left: 10px;'>
                <a href='user_blog_entry.php?blogentry_id={$blogentries[blogentry_loop].blogentry_id}'>{lang_print id=187}</a>
                | 
                <a href='javascript:void(0);' onclick='SocialEngine.Blog.deleteBlog({$blogentries[blogentry_loop].blogentry_id});'>{lang_print id=155}</a>
              </td>
            </tr>
          </table>
        </div>
      {/section}
      </div>
      
      <div style='margin-top: 10px;'>
        {lang_block id=788 var=langBlockTemp}<input type='submit' class='button' value='{$langBlockTemp}' />{/lang_block}
        <input type='hidden' name='task' value='delete' />
        <input type='hidden' name='s' value='{$s}' />
        <input type='hidden' name='p' value='{$p}' />
        </form>
      </div>
      
    </div>
    
    <br />
    <br />
    <br />
    
    
    {* HIDDEN DIV TO DISPLAY DELETE CONFIRMATION MESSAGE *}
    <div style='display: none;' id='confirmblogdelete'>
      <div style='margin-top: 10px;'>
        {lang_print id=1500114}
      </div>
      <br />
      {lang_block id=175 var=langBlockTemp}<input type='button' class='button' value='{$langBlockTemp}' onClick='parent.TB_remove();parent.SocialEngine.Blog.deleteBlogConfirm();' />{/lang_block}
      {lang_block id=39 var=langBlockTemp}<input type='button' class='button' value='{$langBlockTemp}' onClick='parent.TB_remove();' />{/lang_block}
    </div>
    
  {/if}
  
{/if}

{include file='footer.tpl'}