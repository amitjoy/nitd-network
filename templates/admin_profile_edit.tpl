{include file='admin_header.tpl'}

{* $Id: admin_profile_edit.tpl 8 2009-01-11 06:02:53Z john $ *}

<h2>{lang_print id=16}: {lang_print id=$profilecat_info.profilecat_title}</h2>
todo
<br />
<br />



{if $result}
<div class='success'>
  <img src='../images/success.gif' class='icon' border='0' />
  {lang_print id=191}
</div>
{/if}

{if $is_error}
  <div class='error'>
    <img src='../images/error.gif' border='0' class='icon' />
    {if is_numeric($is_error)}{lang_print id=$is_error}{else}{$is_error}{/if}
  </div>
{/if}

{literal}
<script type="text/javascript">
  
  function displayNameDefault(method)
  {
    $$('.SEProfileCatDisplayNameDefault').each(function(element)
    {
      var currentMethod = parseInt(element.id.replace('profilecat_displayname_method_default_', ''));
      
      if( currentMethod!=method )
      {
        element.removeClass('SEProfileCatDisplayNameDefaultTrue');
        element.innerHTML = '<a href="javascript:void(0);" onclick="displayNameDefault('+currentMethod+');">(default)</a>';
      }
      else
      {
        element.addClass('SEProfileCatDisplayNameDefaultTrue');
        element.innerHTML = '(current)';
      }
    });
    
    var request = new Request({
      'url' : 'admin_profile_edit.php',
      'method' : 'post',
      'data' : {
        'profilecat_id' : {/literal}{$profilecat_info.profilecat_id}{literal},
        'task' : 'default',
        'method_default' : method
      }
    });
    
    request.send();
  }
  
</script>

<style type="text/css">
  
  .SEProfileCatDisplayNameDefault
  {
    text-align: right;
  }
  
</style>
{/literal}


<form action='admin_profile_edit.php?profilecat_id={$profilecat_info.profilecat_id}' method='post'>

<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>Display Name Generation</td>
  </tr>
  
  <tr>
    <td class='setting1'>
      You can allow users to select how to show their display name. If you choose to
      not allow this, users in this category will have their name generated using the
      default method.
    </td>
  </tr>
  <tr>
    <td class='setting2'>
      <table cellpadding='0' cellspacing='0' width="100%">
        <tr>
          <td><input type="checkbox" id="profilecat_displayname_method_allowed_1" name="profilecat_displayname_method_allowed[]" value="1"{if $profilecat_info.profilecat_displayname_method_allowed & 1} checked {/if} /></td>
          <td><label for="profilecat_displayname_method_allowed_1">[First Name] [Last Name]</label></td>
          
          {if $profilecat_info.profilecat_displayname_method_default==1}
            <td id="profilecat_displayname_method_default_1" class="SEProfileCatDisplayNameDefault SEProfileCatDisplayNameDefaultTrue">(current)</td>
          {else}
            <td id="profilecat_displayname_method_default_1" class="SEProfileCatDisplayNameDefault"><a href="javascript:void(0);" onclick="displayNameDefault(1);">(default)</a></td>
          {/if}
        </tr>
        <tr>
          <td><input type="checkbox" id="profilecat_displayname_method_allowed_2" name="profilecat_displayname_method_allowed[]" value="2"{if $profilecat_info.profilecat_displayname_method_allowed & 2} checked {/if} /></td>
          <td><label for="profilecat_displayname_method_allowed_2">[Last Name] [First Name]</label></td>
          
          {if $profilecat_info.profilecat_displayname_method_default==2}
            <td id="profilecat_displayname_method_default_2" class="SEProfileCatDisplayNameDefault SEProfileCatDisplayNameDefaultTrue">(current)</td>
          {else}
            <td id="profilecat_displayname_method_default_2" class="SEProfileCatDisplayNameDefault"><a href="javascript:void(0);" onclick="displayNameDefault(2);">(default)</a></td>
          {/if}
        </tr>
        <tr>
          <td><input type="checkbox" id="profilecat_displayname_method_allowed_4" name="profilecat_displayname_method_allowed[]" value="4"{if $profilecat_info.profilecat_displayname_method_allowed & 4} checked {/if} /></td>
          <td><label for="profilecat_displayname_method_allowed_4">[Last Name], [First Name]</label></td>
          
          {if $profilecat_info.profilecat_displayname_method_default==4}
            <td id="profilecat_displayname_method_default_4" class="SEProfileCatDisplayNameDefault SEProfileCatDisplayNameDefaultTrue">(current)</td>
          {else}
            <td id="profilecat_displayname_method_default_4" class="SEProfileCatDisplayNameDefault"><a href="javascript:void(0);" onclick="displayNameDefault(4);">(default)</a></td>
          {/if}
        </tr>
        <tr>
          <td><input type="checkbox" id="profilecat_displayname_method_allowed_8" name="profilecat_displayname_method_allowed[]" value="8"{if $profilecat_info.profilecat_displayname_method_allowed & 8} checked {/if} /></td>
          <td><label for="profilecat_displayname_method_allowed_8">[Last Name]</label></td>
          
          {if $profilecat_info.profilecat_displayname_method_default==8}
            <td id="profilecat_displayname_method_default_8" class="SEProfileCatDisplayNameDefault SEProfileCatDisplayNameDefaultTrue">(current)</td>
          {else}
            <td id="profilecat_displayname_method_default_8" class="SEProfileCatDisplayNameDefault"><a href="javascript:void(0);" onclick="displayNameDefault(8);">(default)</a></td>
          {/if}
        </tr>
        <tr>
          <td><input type="checkbox" id="profilecat_displayname_method_allowed_16" name="profilecat_displayname_method_allowed[]" value="16"{if $profilecat_info.profilecat_displayname_method_allowed & 16} checked {/if} /></td>
          <td><label for="profilecat_displayname_method_allowed_16">[First Name]</label></td>
          
          {if $profilecat_info.profilecat_displayname_method_default==16}
            <td id="profilecat_displayname_method_default_16" class="SEProfileCatDisplayNameDefault SEProfileCatDisplayNameDefaultTrue">(current)</td>
          {else}
            <td id="profilecat_displayname_method_default_16" class="SEProfileCatDisplayNameDefault"><a href="javascript:void(0);" onclick="displayNameDefault(16);">(default)</a></td>
          {/if}
        </tr>
        <tr>
          <td><input type="checkbox" id="profilecat_displayname_method_allowed_32" name="profilecat_displayname_method_allowed[]" value="32"{if $profilecat_info.profilecat_displayname_method_allowed & 32} checked {/if} /></td>
          <td>
            <label for="profilecat_displayname_method_allowed_32">Custom:</label>
            <input class="text" type="text" name="profilecat_displayname_method_custom" value="{$profilecat_info.profilecat_displayname_method_custom}" />
          </td>
          
          {if $profilecat_info.profilecat_displayname_method_default==32}
            <td id="profilecat_displayname_method_default_32" class="SEProfileCatDisplayNameDefault SEProfileCatDisplayNameDefaultTrue">(current)</td>
          {else}
            <td id="profilecat_displayname_method_default_32" class="SEProfileCatDisplayNameDefault"><a href="javascript:void(0);" onclick="displayNameDefault(32);">(default)</a></td>
          {/if}
        </tr>
      </table>
    </td>
  </tr>
  
</table>
<br />


<table cellpadding='0' cellspacing='0'>
  <tr>
    <td>
      <input type='submit' class='button' value='{lang_print id=173}' />
      <input type='hidden' name='task' value='dosave' />
      </form>
    </td>
    <td style="padding-left: 4px;">
      <form action="admin_profile.php">
      <input type='submit' class='button' value='{lang_print id=39}' />
      </form>
    </td>
  </tr>
</table>



{include file='admin_footer.tpl'}