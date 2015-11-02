{include file='header_global.tpl'}

{* $Id: profile_comments.tpl 8 2009-01-11 06:02:53Z john $ *}

<BASE TARGET="_parent">

<div style='padding: 20px; text-align: left;'>
{section name=comment_loop loop=$comments}

  <div style='overflow: hidden; margin-top: 10px; margin-bottom: 20px;'>
    <div style='float: left; text-align: center; width: 90px;'>
    <a href='{$url->url_create('profile', $comments[comment_loop].comment_author->user_info.user_username)}'>
    <img src='{$comments[comment_loop].comment_author->user_photo('./images/nophoto.gif')}' class='photo' width='{$misc->photo_size($comments[comment_loop].comment_author->user_photo('./images/nophoto.gif'),'75','75','w')}' border='0'>
    </a>
    </div>
    <div style='overflow: hidden;'>
      <div class='profile_comment_author'><a href='{$url->url_create('profile', $comments[comment_loop].comment_author->user_info.user_username)}'><b>{$comments[comment_loop].comment_author->user_displayname}</b></a></div>
      <div class='profile_comment_date'>{$datetime->cdate("`$setting.setting_dateformat` `$setting.setting_timeformat`", $datetime->timezone($comments[comment_loop].comment_date, $global_timezone))}</div>
      <div class='profile_comment_body'><table cellspacing='0' cellpadding='0'><tr><td>{$comments[comment_loop].comment_body}</td></tr></table></div>
    </div>
  </div>

{/section}
</div>

</body>
</html>