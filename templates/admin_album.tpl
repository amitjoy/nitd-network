{include file='admin_header.tpl'}

{* $Id: admin_album.tpl 2 2009-01-10 20:53:09Z john $ *}

<h2>{lang_print id=1000005}</h2>
{lang_print id=1000008}

<br><br>

{if $result != 0}
  <div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=191}</div>
{/if}

<form action='admin_album.php' method='POST'>


<table cellpadding='0' cellspacing='0' width='600'>
<td class='header'>{lang_print id=192}</td>
</tr>
<td class='setting1'>
  {lang_print id=1000009}
</td>
</tr>
<tr>
<td class='setting2'>
  <table cellpadding='2' cellspacing='0'>
  <tr>
  <td><input type='radio' name='setting_permission_album' id='permission_album_1' value='1'{if $setting.setting_permission_album == 1} checked='checked'{/if}></td>
  <td><label for='permission_album_1'>{lang_print id=1000010}</label></td>
  </tr>
  <tr>
  <td><input type='radio' name='setting_permission_album' id='permission_album_0' value='0'{if $setting.setting_permission_album == 0} checked='checked'{/if}></td>
  <td><label for='permission_album_0'>{lang_print id=1000011}</label></td>
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