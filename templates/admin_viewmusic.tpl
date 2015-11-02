{include file='admin_header.tpl'}

<h2>{lang_print id=4000031}</h2>
{lang_print id=4000032}
<br />
<br />

<form action='admin_viewmusic.php' method='POST'>
<table cellpadding='0' cellspacing='0' width='400' align='center'>
  <tr>
    <td align='center'>
      <div class='box'>
        <table cellpadding='0' cellspacing='0' align='center'>
          <tr>
            <td>
              {lang_print id=4000033}
              <br />
              <input type='text' class='text' name='f_title' value='{$f_title}' size='15' maxlength='100' />
              &nbsp;
            </td>
            <td>
              {lang_print id=4000034}
              <br>
              <input type='text' class='text' name='f_owner' value='{$f_owner}' size='15' maxlength='50' />
              &nbsp;
            </td>
            <td>
              &nbsp;
              {lang_block id=1002 var=langBlockTemp}<input type='submit' class='button' value='{$langBlockTemp}' />{/lang_block}
            </td>
          </tr>
        </table>
      </div>
    </td>
  </tr>
</table>
<input type='hidden' name='s' value='{$s}' />
</form>
<br />


{if $task=='delete'}
  
  <table cellpadding='0' cellspacing='0' width='100%'><tr><td class='page'>
    
    <img src='../images/icons/music_music48.gif' border='0' class='icon_big'>
    <div class='page_header'>{lang_print id=4000038}</div>
    <div>{lang_print id=4000039}</div>
    <br />
    
    <table cellpadding='0' cellspacing='0'>
      <tr>
        <td>
          <form action='admin_viewmusic.php' method='post'>
          {lang_block id=4000038 var=langBlockTemp}<input type='submit' class='button' value='{$langBlockTemp}' />&nbsp;{/lang_block}
          <input type='hidden' name='task' value='dodelete' />
          <input type='hidden' name='music_id' value='{$music_id}' />
          <input type='hidden' name='owner' value='{$owner}' />
          </form>
        </td>
        <td>
          <form action='admin_viewmusic.php' method='get'>
          {lang_block id=39 var=langBlockTemp}<input type='submit' class='button' value='{$langBlockTemp}' />{/lang_block}
          </form>
        </td>
      </tr>
    </table>
    
  </td>
  </tr>
  </table>


{* IF THERE ARE NO MUSIC ENTRIES *}
{elseif $total_music == 0}

  <table cellpadding='0' cellspacing='0' width='400' align='center'><tr><td align='center'>
    <div class='box' style='width: 300px;'><b>{lang_print id=4000037}</b></div>
  </td></tr></table>
  <br />


{* IF THERE ARE MUSIC ENTRIES *}
{else}

  <script type="text/javascript" src="../include/js/class_language.js"></script>
  {lang_javascript id=4000038}
  
  {literal}
  <script type="text/javascript" src="../include/js/class_music.js"></script>
  <script type="text/javascript">
    var SEMusic = new SocialEngineMusic();
    SEMusic.options.ajaxURL = 'admin_viewmusic.php';
  </script>
  {/literal}
  
  
  {* JAVASCRIPT FOR CHECK ALL *}
  {literal}
  <script language='JavaScript'> 
  <!---
  var checkboxcount = 1;
  function doCheckAll() {
    if(checkboxcount == 0) {
      with (document.items) {
      for (var i=0; i < elements.length; i++) {
      if (elements[i].type == 'checkbox') {
      elements[i].checked = false;
      }}
      checkboxcount = checkboxcount + 1;
      }
    } else
      with (document.items) {
      for (var i=0; i < elements.length; i++) {
      if (elements[i].type == 'checkbox') {
      elements[i].checked = true;
      }}
      checkboxcount = checkboxcount - 1;
      }
  }
  // -->
  </script>
  {/literal}

  <div class='pages'>
    {lang_sprintf id=4000035 1=$total_music}
    &nbsp;|&nbsp;
    {lang_print id=4000036}
    {section name=page_loop loop=$pages}
      {if $pages[page_loop].link}
        {$pages[page_loop].page}
      {else}
        <a href='admin_viewmusic.php?s={$s}&p={$pages[page_loop].page}&f_title={$f_title}&f_owner={$f_owner}'>{$pages[page_loop].page}</a>
      {/if}
    {/section}
  </div>
  
  <form action='admin_viewmusic.php' method='post' name='items'>
  <table cellpadding='0' cellspacing='0' class='list'>
    <tr>
      <td class='header' width='10'><input type='checkbox' name='select_all' onClick='javascript:doCheckAll()' /></td>
      <td class='header' width='10' style='padding-left: 0px;'><a class='header' href='admin_viewmusic.php?s={$i}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=87}</a></td>
      <td class='header'><a class='header' href='admin_viewmusic.php?s={$t}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=4000033}</a></td>
      <td class='header'><a class='header' href='admin_viewmusic.php?s={$o}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=4000034}</a></td>
      <td class='header' width='100'><a class='header' href='admin_viewmusic.php?s={$d}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=88}</a></td>
      <td class='header' width='100'>{lang_print id=153}</td>
    </tr>
    
    {section name=music_loop loop=$entries}
    {assign var='media_dir' value=$url->url_userdir($entries[music_loop].music_user_id)}
    {assign var='media_path' value=".`$media_dir``$entries[music_loop].music_id`.`$entries[music_loop].music_ext`"}
    <tr class='{cycle values="background1,background2"} seMusicRow' id="seMusic_{$entries[music_loop].music_id}">
      <td class='item' style='padding-right: 0px;'><input type='checkbox' name='delete_entry[]' value='{$entries[music_loop].music_id}'></td>
      <td class='item' style='padding-left: 0px;'>{$entries[music_loop].music_id}</td>
      <td class='item'>
        <object width="17" height="17" data="../images/music_button.swf?song_url={$media_path}&autoload=false&" type="application/x-shockwave-flash">
        <param value="../images/music_button.swf?song_url={$media_path}&autoload=false&" name="movie"/>
        <img width="17" height="17" alt="" src="noflash.gif"/>
        </object>
        {$entries[music_loop].music_title}
      </td>
      <td class='item'><a href='{$url->url_create("profile", $entries[music_loop].music_uploader->user_info.user_username)}' target='_blank'>{$entries[music_loop].music_uploader->user_displayname}</a></td>
      <td class='item'>{$datetime->cdate($setting.setting_dateformat, $datetime->timezone($entries[music_loop].music_date, $setting.setting_timezone))}</td>
      <td class='item'>
        [ <a href="javascript:void(0);" onclick="SEMusic.deleteMusic({$entries[music_loop].music_id});">{lang_print id=155}</a> ]
        {*[ <a href='admin_viewmusic.php?task=delete&music_id={$entries[music_loop].music_id}&owner={$entries[music_loop].music_user_id}'>{lang_print id=155}</a> ]*}
      </td>
    </tr>
    {/section}
    
  </table>
  <br />

  <table cellpadding='0' cellspacing='0' width='100%'>
    <tr>
      <td>
        {lang_block id=788 var=langBlockTemp}<input type='submit' class='button' value='{$langBlockTemp}' />{/lang_block}
      </td>
      <td align='right' valign='top'>
        <div class='pages2'>
          {lang_sprintf id=4000035 1=$total_music}
          &nbsp;|&nbsp;
          {lang_print id=4000036}
          {section name=page_loop loop=$pages}
            {if $pages[page_loop].link}
              {$pages[page_loop].page}
            {else}
              <a href='admin_viewmusic.php?s={$s}&p={$pages[page_loop].page}&f_title={$f_title}&f_owner={$f_owner}'>{$pages[page_loop].page}</a>
            {/if}
          {/section}
        </div>
      </td>
    </tr>
  </table>
  
  <input type='hidden' name='task' value='delete_selected' />
  <input type='hidden' name='p' value='{$p}' />
  <input type='hidden' name='s' value='{$s}' />
  <input type='hidden' name='f_title' value='{$f_title}' />
  <input type='hidden' name='f_owner' value='{$f_owner}' />
  </form>
  
  
  {* HIDDEN DIV TO DISPLAY DELETE CONFIRMATION MESSAGE *}
  <div style='display: none;' id='confirmmusicdelete'>
    <div style='margin-top: 10px;'>
      {lang_print id=4000039}
    </div>
    <br>
    <input type='button' class='button' value='{lang_print id=175}' onClick='parent.TB_remove();parent.SEMusic.deleteMusicConfirm(parent.SEMusic.currentConfirmDeleteID);'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
  </div>

{/if}

{include file='admin_footer.tpl'}