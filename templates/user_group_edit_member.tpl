{include file='header_global.tpl'}

{* $Id: user_group_edit_member.tpl 10 2009-01-11 06:03:42Z john $ *}

{* SHOW CONFIRMATION OF MEMBER EDITING *}
{if $result != 0}

  {* JAVASCRIPT FOR CLOSING BOX *}
  {literal}
  <script type="text/javascript">
  <!-- 
  setTimeout("window.parent.location.reload(true);", "1000");
  //-->
  </script>
  {/literal}

  <br><div>{lang_print id=$result}</div>



{* SHOW FORM FOR EDITING MEMBER INFO *}
{else}

  <div style='text-align:left; padding-left: 10px; padding-top: 10px;'>
  <form action='user_group_edit_member.php' method='post'>
  <table cellpadding='0' cellspacing='0'>

  {* SHOW MEMBER TITLE FIELD IF ALLOWED BY ADMIN *}
  {if $group->groupowner_level_info.level_group_titles == 1}
    <tr>
    <td align='right' nowrap='nowrap'>{lang_print id=2000192}&nbsp;</td>
    <td><input type='text' name='member_title' class='text' size='40' maxlength='50' value='{$groupmember_info.groupmember_title}'></td>
    </tr>
  {/if}

  {* SHOW MEMBER RANK FIELD *}
  {if $group->user_rank == 2}
    <tr>
    <td align='right' nowrap='nowrap' style='padding-top: 5px;'>{lang_print id=2000193}&nbsp;</td>
    <td style='padding-top: 5px;'>
      <select name='member_rank' id='member_rank' onChange="{literal}if($('warning')) { if(this.options[this.selectedIndex].value == 2) { $('warning').style.display = 'block'; } else { $('warning').style.display = 'none'; }}{/literal}" class='group_select'>
      {if $groupmember_info.groupmember_rank == 2}
        <option value='2'{if $groupmember_info.groupmember_rank == 2} selected='selected'{/if}>{lang_print id=2000179}</option>
      {else}
        <option value='0'{if $groupmember_info.groupmember_rank == 0} selected='selected'{/if}>{lang_print id=2000181}</option>
        {if $group->groupowner_level_info.level_group_officers == 1}<option value='1'{if $groupmember_info.groupmember_rank == 1} selected='selected'{/if}>{lang_print id=2000180}</option>{/if}
        <option value='2'{if $groupmember_info.groupmember_rank == 2} selected='selected'{/if}>{lang_print id=2000179}</option>
      {/if}
      </select>
    </td>
    </tr>
  {/if}

  {* SHOW INSTRUCTIONS IF EDITING OWNER *}
  {if $groupmember_info.groupmember_rank == 2}
    <tr>
    <td>&nbsp;</td>
    <td><div class='form_desc' style='padding: 10px 0px 10px 0px;'>{lang_sprintf id=2000194 1=$group->group_info.group_id}</div></td>
    </tr>
  {* SHOW WARNING MESSAGE IF USER IS ABOUT TO TRANSFER OWNERSHIP *}
  {else}
    <tr>
    <td>&nbsp;</td>
    <td><div id='warning' class='form_desc' style='display: none;'>{lang_print id=2000195}</div></td>
    </tr>
  {/if}

  <tr>
  <td>&nbsp;</td>
  <td>
    <table cellpadding='0' cellspacing='0'>
    <tr>
    <td>
      <input type='submit' class='button' value='{lang_print id=173}'>&nbsp;
      <input type='hidden' name='group_id' value='{$group->group_info.group_id}'>
      <input type='hidden' name='groupmember_id' value='{$groupmember_info.groupmember_id}'>
      <input type='hidden' name='task' value='save_do'>
    </td>
    <td>
      <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove()'>
    </td>
    </tr>
    </table>
  </td>
  </tr>
  </table>
  </form>

{/if}

</body>
</html>