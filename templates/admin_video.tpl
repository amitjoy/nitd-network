{include file='admin_header.tpl'}

{* $Id: admin_video.tpl 94 2009-03-11 23:08:52Z szerrade $ *}





<h2>{lang_print id=5500117}</h2>
{lang_print id=5500118}
<br />
<br />

{if $result != 0}
  <div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=191}</div>
{/if}

<form action='admin_video.php' method='POST'>


<table cellpadding='0' cellspacing='0' width='600'>
<td class='header'>{lang_print id=192}</td>
</tr>
<td class='setting1'>
  {lang_print id=5500119}
</td>
</tr>
<tr>
<td class='setting2'>
  <table cellpadding='2' cellspacing='0'>
  <tr>
  <td><input type='radio' name='setting_permission_video' id='permission_video_1' value='1'{if $setting.setting_permission_video == 1} checked='checked'{/if}></td>
  <td><label for='permission_video_1'>{lang_print id=5500120}</label></td>
  </tr>
  <tr>
  <td><input type='radio' name='setting_permission_video' id='permission_video_0' value='0'{if $setting.setting_permission_video == 0} checked='checked'{/if}></td>
  <td><label for='permission_video_0'>{lang_print id=5500121}</label></td>
  </tr>
  </table>
</td>
</tr>
</table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=5500122}</td>
</tr>
<tr>
<td class='setting1'>
{lang_print id=5500123}
</td>
</tr>
<tr>
<td class='setting2'>
<input type='text' class='text' name='setting_video_ffmpeg_path' value='{$setting.setting_video_ffmpeg_path}' maxlength='255' size='60'>
</td>
</tr>
</table>


<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=5500124}</td>
</tr>
<tr>
<td class='setting1'>
{lang_print id=5500125} <br>
{lang_print id=5500126}
</td>
</tr>
<tr>
<td class='setting2'>
<textarea name='setting_video_mimes' rows='3' cols='40' class='text' style='width: 100%;'>{$setting.setting_video_mimes}</textarea>
</td>
</tr>
<tr>
<td class='setting1'>
{lang_print id=5500127}<br>
{lang_print id=5500126}
</td>
</tr>
<tr>
<td class='setting2'>
<textarea name='setting_video_exts' rows='3' cols='40' class='text' style='width: 100%;'>{$setting.setting_video_exts}</textarea>
</td>
</tr>
</table>


<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=5500128}</td>
</tr>
<tr>
<td class='setting1'>
{lang_print id=5500129}
</td>
</tr>
<tr>
<td class='setting2'>
{lang_print id=5500132}: <input type='text' class='text' name='setting_video_width' value='{$setting.setting_video_width}' maxlength='4' size='5'>px &nbsp; {lang_print id=5500131}: <input type='text' class='text' name='setting_video_height' value='{$setting.setting_video_height}' maxlength='4' size='5'>px 
</td>
</tr>
<tr>
<td class='setting1'>
{lang_print id=5500130}
</td>
</tr>
<tr>
<td class='setting2'>
{lang_print id=5500132}: <input type='text' class='text' name='setting_video_thumb_width' value='{$setting.setting_video_thumb_width}' maxlength='4' size='5'>px &nbsp; {lang_print id=5500131}: <input type='text' class='text' name='setting_video_thumb_height' value='{$setting.setting_video_thumb_height}' maxlength='4' size='5'>px 
</td>
</tr>
</table>


<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=5500133}</td>
</tr>
<tr>
<td class='setting1'>
{lang_print id=5500134}
</td>
</tr>
<tr>
<td class='setting2'>
<input type='text' class='text' name='setting_video_max_jobs' value='{$setting.setting_video_max_jobs}' maxlength='5' size='4'> {lang_print id=5500135}
</td>
</tr>
</table>

<br>


<table cellpadding='0' cellspacing='0' width='600'>
<td class='header'>{lang_print id=5500136}</td>
</tr>
<td class='setting1'>
  {lang_print id=5500137}
</td>
</tr>
<tr>
<td class='setting2'>
  <table cellpadding='2' cellspacing='0'>
  <tr>
  <td><input type='radio' name='setting_video_cronjob' id='setting_video_cronjob_1' value='1'{if $setting.setting_video_cronjob == 1} checked='checked'{/if}></td>
  <td><label for='setting_video_cronjob_1'>{lang_print id=5500138}</label></td>
  </tr>
  <tr>
  <td><input type='radio' name='setting_video_cronjob' id='setting_video_cronjob_0' value='0'{if $setting.setting_video_cronjob == 0} checked='checked'{/if}></td>
  <td><label for='setting_video_cronjob_0'>{lang_print id=5500139}</label></td>
  </tr>
  </table>
</td>
</tr>
</table>

<br>


<input type='submit' class='button' value='{lang_print id=173}'>
<input type='hidden' name='task' value='dosave'>
</form>


{include file='admin_footer.tpl'}