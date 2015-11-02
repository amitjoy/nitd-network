{include file='admin_header.tpl'}

{* $Id: admin_viewadmins.tpl 190 2009-07-11 01:57:50Z john $ *}

<h2>{lang_print id=5}</h2>
{lang_print id=257}
<br />
<br />

<table cellpadding='0' cellspacing='0' class='list'>
<tr>
<td class='header' width='10'>{lang_print id=87}</td>
<td class='header'>{lang_print id=28}</td>
<td class='header'>{lang_print id=258}</td>
<td class='header'>{lang_print id=89}</td>
<td class='header'>{lang_print id=259}</td>
<td class='header'>{lang_print id=153}</td>
</tr>
<!-- LOOP THROUGH ADMINS -->
{section name=admin_loop loop=$admins}
  <tr class='{cycle values="background1,background2"}'>
  <td class='item'>{$admins[admin_loop].admin_id}</td>
  <td class='item'>{$admins[admin_loop].admin_username|truncate:27:"...":true}</td>
  <td class='item'>{$admins[admin_loop].admin_name}</td>
  <td class='item'><a href='mailto:{$admins[admin_loop].admin_email}'>{$admins[admin_loop].admin_email|truncate:27:"...":true}</a></td>
  <td class='item'>{if $admins[admin_loop].admin_status == "0"}{lang_print id=260}{else}{lang_print id=261}{/if}</td>
  <td class='item'><a href="javascript:editAdmin('{$admins[admin_loop].admin_id}', '{$admins[admin_loop].admin_name}', '{$admins[admin_loop].admin_username}', '{$admins[admin_loop].admin_email}');">{lang_print id=187}</a>{if $admins[admin_loop].admin_status != "0"} | <a href="javascript:confirmDelete('{$admins[admin_loop].admin_id}');">{lang_print id=155}</a>{/if}</td>
  </tr>
{/section}
</table>

<br>

<input type='button' class='button' value='{lang_print id=262}' onclick='createAdmin();'>


{* JAVASCRIPT FOR CONFIRMING DELETION *}
{literal}
<script type="text/javascript">
<!-- 
var admin_id = 0;
function confirmDelete(id) {
  admin_id = id;
  TB_show('{/literal}{lang_print id=263}{literal}', '#TB_inline?height=100&width=300&inlineId=confirmdelete', '', '../images/trans.gif');
  document.getElementById('deletebutton').focus();
}

function deleteAdmin() {
  window.location = 'admin_viewadmins.php?task=delete&admin_id='+admin_id;
}

function createAdmin() {
  document.getElementById('createbutton').value = '{/literal}{lang_print id=262}{literal}';
  document.getElementById('error').innerHTML = '';  
  document.getElementById('old_password').style.display = 'none';  
  document.getElementById('task').value = 'create';  
  document.getElementById('admin_id').value = '0';  
  document.getElementById('admin_username').defaultValue = '';  
  document.getElementById('admin_username').value = '';  
  document.getElementById('admin_name').defaultValue = '';  
  document.getElementById('admin_name').value = '';  
  document.getElementById('admin_email').defaultValue = '';  
  document.getElementById('admin_email').value = '';  
  TB_show('{/literal}{lang_print id=262}{literal}', '#TB_inline?height=350&width=300&inlineId=createadmin', '', '../images/trans.gif');
}

function editAdmin(id, name, username, email) {
  document.getElementById('createbutton').value = '{/literal}{lang_print id=270}{literal}';
  document.getElementById('error').innerHTML = '';  
  document.getElementById('old_password').style.display = '';  
  document.getElementById('task').value = 'edit';  
  document.getElementById('admin_id').value = id;  
  document.getElementById('admin_username').defaultValue = username;  
  document.getElementById('admin_username').value = username;  
  document.getElementById('admin_name').defaultValue = name;  
  document.getElementById('admin_name').value = name;  
  document.getElementById('admin_email').defaultValue = email;  
  document.getElementById('admin_email').value = email;  
  TB_show('{/literal}{lang_print id=270}{literal}', '#TB_inline?height=350&width=350&inlineId=createadmin', '', '../images/trans.gif');
}
//-->
</script>
{/literal}

{* HIDDEN DIV TO DISPLAY CONFIRMATION MESSAGE *}
<div style='display: none;' id='confirmdelete'>
  <div style='margin-top: 10px;'>
    {lang_print id=264}
  </div>
  <br>
  <input type='button' class='button' value='{lang_print id=175}' id='deletebutton' onClick='parent.TB_remove();parent.deleteAdmin();'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
</div>



{* HIDDEN DIV TO DISPLAY CREATE/EDIT ADMIN *}
<div style='display: none;' id='createadmin'>
  <form action='admin_viewadmins.php' method='post' target='ajaxframe'>
  <div style='margin-top: 10px;'>{lang_print id=265}</div>
  <div id='error' style='width: 100%;'>test</div>
  <br>
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td align='right'>{lang_print id=28}:&nbsp;</td>
  <td><input type='text' class='text' name='admin_username' id='admin_username' maxlength='50'></td>
  </tr>
  <tr id='old_password'>
  <td align='right'>{lang_print id=269}:&nbsp;</td>
  <td><input type='password' class='text' name='admin_old_password' id='admin_old_password' maxlength='50'></td>
  </tr>
  <tr>
  <td align='right'>{lang_print id=46}:&nbsp;</td>
  <td><input type='password' class='text' name='admin_password' id='admin_password' maxlength='50'></td>
  </tr>
  <tr>
  <td align='right'>{lang_print id=47}:&nbsp;</td>
  <td><input type='password' class='text' name='admin_password_confirm' id='admin_password_confirm' maxlength='50'></td>
  </tr>
  <tr>
  <td align='right'>{lang_print id=258}:&nbsp;</td>
  <td><input type='text' class='text' name='admin_name' id='admin_name' maxlength='50'></td>
  </tr>
  <tr>
  <td align='right'>{lang_print id=89}:&nbsp;</td>
  <td><input type='text' class='text' name='admin_email' id='admin_email' maxlength='50'></td>
  </tr>
  </table>

  <br>
  <input type='submit' class='button' id='createbutton' value='{lang_print id=262}'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
  <input type='hidden' name='task' value='create' id='task'>
  <input type='hidden' name='admin_id' id='admin_id' value='0'>
  </form>

  {* JAVASCRIPT FOR CONFIRMING DELETION *}
  {literal}
  <script type="text/javascript">
  <!-- 
  function createResult(is_error, error_message) {
    if(is_error != 0) {
      $("TB_window").getElements('div[id=error]').each(function(el) { el.innerHTML = "<br><table cellpadding='0' cellspacing='0' width='100%'><tr><td class='error'>"+error_message+"</td></tr></table>" });
    } else {
      parent.window.location = 'admin_viewadmins.php';
    }
  }
  //-->
  </script>
  {/literal}

</div>


{include file='admin_footer.tpl'}