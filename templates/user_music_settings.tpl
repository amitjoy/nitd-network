{include file='header.tpl'}

<table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
    <td valign='top'>
      
      <img src='./images/icons/music_music48.gif' border='0' class='icon_big'>
      <div class='page_header'>{lang_print id=4000054}</div>
      <div>{lang_print id=4000055}</div>
      
    </td>
    <td valign='top' align='right'>
      
      <table cellpadding='0' cellspacing='0' width='150'>
      <tr><td class='button' nowrap='nowrap'><a href='user_music.php'><img src='./images/icons/back16.gif' border='0' class='button'>{lang_print id=4000069}</a></td></tr>
      </table>
      
    </td>
  </tr>
</table>
<br />


{* SHOW SAVE CHANGES MESSAGE *}
{if $task == "dosave"}
  <table cellpadding='0' cellspacing='0'><tr>
    <td class='success'><img src='./images/success.gif' border='0' class='icon'>{lang_print id=191}</td>
  </tr></table>
  <br />
{/if}


<form action='user_music_settings.php' method='post'>

<div><b>{lang_print id=4000056}</b></div>
<table cellpadding='0' cellspacing='0'>
  <tr>
    <td><input type='radio' name='profile_autoplay' id='profile_autoplay1' value='1' {if  $profile_autoplay}checked{/if}></td>
    <td><label for='profile_autoplay1'>{lang_print id=4000057}</label></td>
  </tr>
  <tr>
    <td><input type='radio' name='profile_autoplay' id='profile_autoplay0' value='0' {if !$profile_autoplay}checked{/if}></td>
    <td><label for='profile_autoplay0'>{lang_print id=4000058}</label></td>
  </tr>
</table>
<br />


<div><b>{lang_print id=4000059}</b></div>
<table cellpadding='0' cellspacing='0'>
  <tr>
    <td><input type='radio' name='site_autoplay' id='site_autoplay1' value='1' {if  $site_autoplay}checked{/if}></td>
    <td><label for='site_autoplay1'>{lang_print id=4000060}</label></td>
  </tr>
  <tr>
    <td><input type='radio' name='site_autoplay' id='site_autoplay0' value='0' {if !$site_autoplay}checked{/if}></td>
    <td><label for='site_autoplay0'>{lang_print id=4000061}</label></td>
  </tr>
</table>
<br />


{* SHOW SKIN SELECTION IF ALLOWED *}
{if $skins}
  <div><b>{lang_print id=4000062}</b></div>
  <select class='text' name='select_music_skin' id='select_music_skin' onChange='showPlayerSkin()' style='width: 150px;'>
  {section name=skin_loop loop=$skins}
    <option value='{$skins[skin_loop].xspfskin_id}'{if $skins[skin_loop].xspfskin_id == $skin_id} selected='selected'{/if}>{$skins[skin_loop].xspfskin_title}</option>
  {/section}	
  </select>
  <input type='hidden' name='skin_id_cache' id='skin_id_cache' value='{$skin_id}'>
  <br />
  <br />
  {section name=skin_loop2 loop=$skins}
    <div id='skin{$skins[skin_loop2].xspfskin_id == $skin_id}'{if $skins[skin_loop2].xspfskin_id != $skin_id} style='display: none;'{/if}>
      <img src='include/music_skins/{$skins[skin_loop2].xspfskin_title}/screenshot.jpg'>
    </div>
  {/section}

  {literal}
  <script type='text/javascript'>
  <!--
  function showPlayerSkin() {
    old_skin = document.getElementById('skin_id_cache').value;
    new_skin = document.getElementById('select_music_skin').value;
    $('skin'+old_skin).style.display='none';
    $('skin'+new_skin).style.display='block';
    document.getElementById('skin_id_cache').value = new_skin; 
  }
  //-->
  </script>
  {/literal}
  <br />
  
{/if}

<table cellpadding='0' cellspacing='0'>
  <tr>
    <td>
      {lang_block id=173 var=langBlockTemp}<input type='submit' class='button' value='{$langBlockTemp}' />&nbsp;{/lang_block}
      <input type='hidden' name='task' value='dosave'>
      </form>
    </td>
    <td>
      <form action='user_music.php' method='get'>
      {lang_block id=39 var=langBlockTemp}<input type='submit' class='button' value='{$langBlockTemp}' />{/lang_block}
      </form>
    </td>
  </tr>
</table>

{include file='footer.tpl'}