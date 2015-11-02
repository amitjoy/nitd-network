{include file='admin_header.tpl'}

<h2>{lang_print id=11040201}</h2>
{lang_print id=11040202}

<br><br>

{if $is_error != 0}
<div class='error'><img src='../images/error.gif' border='0' class='icon'> {$error_message}</div>
{/if}

{if $result != ""}
  <div class='success'><img src='../images/success.gif' border='0' class='icon'> {lang_print id=$result}</div>
{/if}


<form action='admin_education.php' method='POST' name='info'>

<!-- Nulled by [x-MoBiLe]
<table cellpadding='0' cellspacing='0' width='600'>
  <tr><td class='header'>{lang_print id=11040205}</td></tr>
  <tr><td class='setting1'>{lang_print id=11040206}</td></tr>
  <tr><td class='setting2'><input type='text' name='setting_education_license' value='{$setting_education_license}' size='30' maxlength="200" /> {lang_print id=11040207}</td>
  </tr>
</table>

<br>
// -->

<table cellpadding='0' cellspacing='0' width='600'>
  <tr><td class='header'>{lang_print id=11040209}</td></tr>
  <tr><td class='setting1'>{lang_print id=11040210}</td></tr>
  <tr>
    <td class='setting2'>

  <table cellpadding='2' cellspacing='0'>
  <tr>
  <td><input type='radio' name='setting_permission_education' id='permission_education_1' value='1'{if $setting_permission_education == 1} CHECKED{/if}></td>
  <td><label for='permission_education_1'>{lang_print id=11040211}</label></td>
  </tr>
  <tr>
  <td><input type='radio' name='setting_permission_education' id='permission_education_0' value='0'{if $setting_permission_education == 0} CHECKED{/if}></td>
  <td><label for='permission_education_0'>{lang_print id=11040212}</label></td>
  </tr>
  </table>

    </td>
  </tr>
</table>

<br>

<input type='submit' class='button' value='{lang_print id=11040204}'>
<input type='hidden' name='task' value='dosave'>
</form>


{include file='admin_footer.tpl'}