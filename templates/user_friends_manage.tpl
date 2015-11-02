{* INCLUDE HEADER CODE *}
{include file="header_global.tpl"}

{* $Id: user_friends_manage.tpl 136 2009-03-24 00:25:44Z john $ *}



{* SHOW CONFIRMATION OF FRIENDSHIP ADDITION *}
{if $result != 0}

  {* JAVASCRIPT FOR CLOSING BOX *}
  {literal}
  <script type="text/javascript">
  <!-- 
  setTimeout("window.parent.TB_remove();", "1000");
  if(window.parent.friend_update) { setTimeout("window.parent.friend_update('{/literal}{$status}{literal}', '{/literal}{$owner->user_info.user_id}{literal}');", "800"); }
  //-->
  </script>
  {/literal}

  <br />
  <div>{lang_print id=$result}</div>


{* SHOW CANCEL FRIEND REQUEST FORM *}
{elseif $subpage == "cancel"}

  <div style='text-align:left; padding-left: 10px; padding-top: 10px;'>
    {lang_sprintf id=918 1=$owner->user_displayname}

    <br />
    <br />

    <form action='user_friends_manage.php' method='POST'>

    <table cellpadding='0' cellspacing='0'>
    <tr><td colspan='2'>&nbsp;</td></tr>
    <tr>
    <td colspan='2'>
      <table cellpadding='0' cellspacing='0'>
      <tr>
      <td>
      <input type='submit' class='button' value='{lang_print id=919}' />&nbsp;
      <input type='hidden' name='task' value='cancel_do' />
      <input type='hidden' name='user' value='{$owner->user_info.user_username}' />
      </form>
      </td>
      <td>
        <input type='button' class='button' value='{lang_print id=39}' onClick='window.parent.TB_remove();' />
      </td>
      </tr>
      </table>
    </td>
    </tr>
    </table>
  </div>


{* SHOW REJECT FRIEND REQUEST FORM *}
{elseif $subpage == "reject"}

  <div style='text-align:left; padding-left: 10px; padding-top: 10px;'>
    {lang_sprintf id=912 1=$owner->user_displayname}
    <br />
    <br />
    
    <form action='user_friends_manage.php' method='POST'>
    
    <table cellpadding='0' cellspacing='0'>
    <tr><td colspan='2'>&nbsp;</td></tr>
    <tr>
    <td colspan='2'>
       <table cellpadding='0' cellspacing='0'>
       <tr>
       <td>
       <input type='submit' class='button' value='{lang_print id=913}' />&nbsp;
       <input type='hidden' name='task' value='reject_do' />
       <input type='hidden' name='user' value='{$owner->user_info.user_username}' />
       </form>
       </td>
       <td>
         <input type='button' class='button' value='{lang_print id=39}' onClick='window.parent.TB_remove();' />
       </td>
       </tr>
       </table>
    </td>
    </tr>
    </table>

  </div>


{* SHOW REMOVE FRIEND FORM *}
{elseif $subpage == "remove"}

  <div style='text-align:left; padding-left: 10px; padding-top: 10px;'>
    {lang_sprintf id=877 1=$owner->user_displayname}
    <br />
    
    <form action='user_friends_manage.php' method='POST'>
    
    <table cellpadding='0' cellspacing='0'>
    <tr><td colspan='2'>&nbsp;</td></tr>
    <tr>
    <td colspan='2'>
      <table cellpadding='0' cellspacing='0'>
      <tr>
      <td>
      <input type='submit' class='button' value='{lang_print id=889}' />&nbsp;
      <input type='hidden' name='task' value='remove_do' />
      <input type='hidden' name='user' value='{$owner->user_info.user_username}' />
      </form>
      </td>
      <td>
        <input type='button' class='button' value='{lang_print id=39}' onClick='window.parent.TB_remove();' />
      </td>
      </tr>
      </table>
    </td>
    </tr>
    </table>
  </div>



{* SHOW CONFIRM FRIEND FORM *}
{elseif $subpage == "confirm"}

  <div style='text-align:left; padding-left: 10px; padding-top: 10px;'>
    {lang_sprintf id=888 1=$owner->user_displayname}
    <br />
    <br />
    
    <form action='user_friends_manage.php' method='POST'>
    
    <table cellpadding='0' cellspacing='0'>
    <tr><td colspan='2'>&nbsp;</td></tr>
    <tr>
    <td colspan='2'>
      <table cellpadding='0' cellspacing='0'>
      <tr>
      <td>
      <input type='submit' class='button' value='{lang_print id=887}' />&nbsp;
      <input type='hidden' name='task' value='add_do' />
      <input type='hidden' name='user' value='{$owner->user_info.user_username}' />
      </form>
      </td>
      <td>
        <input type='button' class='button' value='{lang_print id=39}' onClick='window.parent.TB_remove();' />
      </td>
      </tr>
      </table>
    </td>
    </tr>
    </table>
  </div>

{* SHOW EDIT FRIENDSHIP FORM *}
{elseif $subpage == "edit"}

  <div style='text-align:left; padding-left: 10px; padding-top: 10px;'>
    {lang_sprintf id=921 1=$owner->user_displayname}
    <br />
    <br />
    
    <form action='user_friends_manage.php' method='post'>
    
    {* SHOW FRIEND TYPES IF APPLICABLE *}
    <table cellpadding='0' cellspacing='0'>
    {if $connection_types|@count != 0}
      <tr>
      <td class='form1'>{lang_print id=882}</td>
      <td class='form2'>
      <select name='friend_type' onChange="{literal}if(this.options[this.selectedIndex].value == 'other_friendtype') { $('other').style.display = 'block'; } else { $('other').style.display = 'none'; }{/literal}">
      <option></option>
      {section name=type_loop loop=$connection_types}
        <option value='{$connection_types[type_loop]}'{if $friend_type == $connection_types[type_loop]} SELECTED{/if}>{$connection_types[type_loop]}</option>
      {/section}
      {if $setting.setting_connection_other != 0}<option value='other_friendtype'{if $friend_type_other != ""} SELECTED{/if}>{lang_print id=863}:</option>{/if}
      </select>
      </td>
      {if $setting.setting_connection_other != 0}
        <td class='form2' style='display: {if $friend_type_other != ""}block{else}none{/if};' id='other'>&nbsp;<input type='text' class='text' name='friend_type_other' value='{$friend_type_other}' maxlength='50'></td>
      {/if}
      </tr>
    {else}
      {if $setting.setting_connection_other != 0}
        <tr>
        <td class='form1'>{lang_print id=882}</td>
        <td class='form2'><input type='text' name='friend_type_other' value='{$friend_type_other}' maxlength='50' /></td>
        </tr>
      {/if}
    {/if}

    </table>
    <br>

    {* SHOW FRIEND EXPLANATION IF APPLICABLE *}
    {if $setting.setting_connection_explain != 0}
      <div>
        <b>{lang_print id=883}</b><br>
        <textarea name='friend_explain' rows='5' cols='60'>{$friend_explain}</textarea>
      </div>
    {/if}  

    <br>

    <table cellpadding='0' cellspacing='0'>
    <tr>
    <td>
      <input type='submit' class='button' value='{lang_print id=922}' />&nbsp;
      <input type='hidden' name='task' value='edit_do' />
      <input type='hidden' name='user' value='{$owner->user_info.user_username}' />
      </form>
    </td>
    <td>
      <input type='button' class='button' value='{lang_print id=39}' onClick='window.parent.TB_remove();' />
    </td>
    </tr>
    </table>

  </div>

{* SHOW ADD FRIEND FORM *}
{elseif $subpage == "add"}

  <div style='text-align: left; padding-left: 10px; padding-top: 10px;'>
    {lang_sprintf id=880 1=$owner->user_displayname}
    <br><br>
    <form action='user_friends_manage.php' method='POST'>

    {* IF FRIEND TYPE OR EXPLANATION IS ENABLED *}
    {if $connection_types|@count != 0 || $setting.setting_connection_other != 0 || $setting.setting_connection_explain != 0}

      {lang_sprintf id=881 1=$owner->user_displayname}
      <br />

      <table cellpadding='0' cellspacing='0'>

      {* SHOW FRIEND TYPES IF APPLICABLE *}
      {if $connection_types|@count != 0}
        <tr>
        <td class='form1'>{lang_print id=882}</td>
        <td class='form2'>
        <select name='friend_type' onChange="{literal}if(this.options[this.selectedIndex].value == 'other_friendtype') { $('other').style.display = 'block'; } else { $('other').style.display = 'none'; }{/literal}">
        <option></option>
        {section name=type_loop loop=$connection_types}
          <option value='{$connection_types[type_loop]}'>{$connection_types[type_loop]}</option>
        {/section}
        {if $setting.setting_connection_other != 0}<option value='other_friendtype'>{lang_print id=863}:</option>{/if}
        </select>
        </td>
        {if $setting.setting_connection_other != 0}
          <td class='form2' style='display: none;' id='other'>&nbsp;<input type='text' class='text' name='friend_type_other' maxlength='50' /></td>
        {/if}
        </tr>
      {else}
        {if $setting.setting_connection_other != 0}
          <tr>
          <td class='form1'>{lang_print id=882}</td>
          <td class='form2'><input type='text' name='friend_type_other' maxlength='50' /></td>
          </tr>
        {/if}
      {/if}

      </table>
      <br>

      {* SHOW FRIEND EXPLANATION IF APPLICABLE *}
      {if $setting.setting_connection_explain != 0}
        <div>
          <b>{lang_print id=883}</b><br>
          <textarea name='friend_explain' rows='5' cols='60'></textarea>
        </div>
     {/if}  

  {/if}

  <table cellpadding='0' cellspacing='0'>
  <tr><td colspan='2'>&nbsp;</td></tr>
  <tr>
  <td colspan='2'>
     <table cellpadding='0' cellspacing='0'>
     <tr>
     <td>
     <input type='submit' class='button' value='{lang_print id=884}' />&nbsp;
     <input type='hidden' name='task' value='add_do' />
     <input type='hidden' name='user' value='{$owner->user_info.user_username}' />
     </form>
     </td>
     <td>
       <input type='button' class='button' value='{lang_print id=39}' onClick='window.parent.TB_remove();' />
     </td>
     </tr>
     </table>
  </td>
  </tr>
  </table>

  </div>

{/if}



</body>
</html>