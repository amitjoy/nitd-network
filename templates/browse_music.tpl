{include file='header.tpl'}

<div class='page_header'>{lang_print id=4000096}</div>

<div style='padding: 7px 10px 7px 10px; background: #F2F2F2; border: 1px solid #BBBBBB; margin: 10px 0px 10px 0px; font-weight: bold;'>
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td>
    {lang_print id=4000097}&nbsp;
  </td>
  <td>
    <select class='small' name='v' onchange="window.location.href='browse_music.php?s={$s}&v='+this.options[this.selectedIndex].value;">
      <option value='0'{if $v == "0"} SELECTED{/if}>{lang_print id=4000098}</option>
      {if $user->user_exists}<option value='1'{if $v == "1"} SELECTED{/if}>{lang_print id=4000099}</option>{/if}
    </select>
  </td>
  <td style='padding-left: 20px;'>
    {lang_print id=4000100}&nbsp;
  </td>
  <td>
    <select class='small' name='s' onchange="window.location.href='browse_music.php?v={$v}&s='+this.options[this.selectedIndex].value;">
      <option value='music_date DESC'{if $s == "music_date DESC"} SELECTED{/if}>{lang_print id=4000101}</option>
      <option value='music_track_num ASC'{if $s == "music_track_num ASC"} SELECTED{/if}>{lang_print id=4000102}</option>
    </select>
  </td>
  </tr>
  </table>
</div>


{* DISPLAY PAGINATION MENU IF APPLICABLE *}
{if $maxpage > 1}
  <div style='text-align: center; padding-bottom: 10px;'>
  {if $p != 1}<a href='browse_music.php?s={$s}&v={$v}&p={math equation="p-1" p=$p}'>&#171; {lang_print id=182}</a>{else}&#171; {lang_print id=182}{/if}
  &nbsp;|&nbsp;&nbsp;
  {if $p_start == $p_end}
    <b>{lang_sprintf id=184 1=$p_start 2=$browse_music_total}</b>
  {else}
    <b>{lang_sprintf id=185 1=$p_start 2=$p_end 3=$browse_music_total}</b>
  {/if}
  &nbsp;&nbsp;|&nbsp;
  {if $p != $maxpage}<a href='browse_music.php?s={$s}&v={$v}&p={math equation="p+1" p=$p}'>{lang_print id=183} &#187;</a>{else}{lang_print id=183} &#187;{/if}
  </div>
{/if}


<div>
  {section name=browse_music_list_loop loop=$browse_music_list}
    {assign var='media_dir' value=$url->url_userdir($browse_music_list[browse_music_list_loop].user_id)}
    {assign var='media_path' value="`$media_dir``$browse_music_list[browse_music_list_loop].music_id`.`$browse_music_list[browse_music_list_loop].music_ext`"}
    
    <div class='music_browse_item' style='width: 415px; float: left;'>
      <table cellpadding='0' cellspacing='0'>
      <tr>
      <td style='vertical-align: middle;padding-right: 3px;'>
        <div class='music_button'>
          <object width="17" height="17" data="images/music_button.swf?song_url={$media_path}" type="application/x-shockwave-flash">
            <param value="images/music_button.swf?song_url={$media_path}" name="movie" />
            <img width="17" height="17" alt="" src="noflash.gif" />
          </object>
        </div>
      </td>
      <td style='vertical-align: top; padding-left: 10px;'>
        <div style='font-weight: bold; font-size: 10pt;'><a href='{$url->url_create("profile", $browse_music_list[browse_music_list_loop].user_username)}'>{$browse_music_list[browse_music_list_loop].music_title|truncate:45:"...":true}</a></div>
        <div class='music_browse_date'>
          {assign var='music_date' value=$datetime->time_since($browse_music_list[browse_music_list_loop].music_date)}{capture assign="updated"}{lang_sprintf id=$music_date[0] 1=$music_date[1]}{/capture}
          {lang_sprintf id=4000103 1=$updated 2=$url->url_create("profile", $url->url_create("profile", $browse_music_list[browse_music_list_loop].user_username)) 3=$browse_music_list[browse_music_list_loop].music_uploader->user_displayname}
        </div>
        {if $user->user_exists && $user->level_info.level_music_allow_downloads}
        <div style='margin-top: 4px;'>
          <a type="application/force-download" href="{$media_path}">{lang_print id=4000095}</a>
        </div>
        {/if}
      </td>
      </tr>
      </table>
    </div>
    
    {cycle values=",<div style='clear: both; height: 10px;'></div>"}
  {/section}

</div>

{include file='footer.tpl'}