{include file='header_global.tpl'}

{* $Id: user_group_manage.tpl 10 2009-01-11 06:03:42Z john $ *}

{* SHOW CONFIRMATION OF JOINING/LEAVING GROUP *}
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


{* SHOW WAITING FOR CONFIRMATION PAGE *}
{elseif $subpage == "waiting"}

  <div style='text-align:left; padding-left: 10px; padding-top: 10px;'>
    {lang_print id=2000207}

    <br>

    <table cellpadding='0' cellspacing='0'>
    <tr><td colspan='2'>&nbsp;</td></tr>
    <tr>
    <td colspan='2'>
      <input type='button' class='button' value='{lang_print id=641}' onClick='window.parent.TB_remove();'>
    </td>
    </tr>
    </table>
  </div>


{* SHOW ACCEPT/REJECT INVITE PAGE *}
{elseif $subpage == "confirm"}


  <div style='text-align:left; padding-left: 10px; padding-top: 10px;'>
    {lang_print id=2000204}

    <br>

    <table cellpadding='0' cellspacing='0'>
    <tr><td colspan='2'>&nbsp;</td></tr>
    <tr>
    <td colspan='2'>
      <table cellpadding='0' cellspacing='0'>
      <tr>
      <td>
      <form action='user_group_manage.php' method='POST'>
      <input type='submit' class='button' value='{lang_print id=2000205}'>&nbsp;
      <input type='hidden' name='task' value='accept_do'>
      <input type='hidden' name='group_id' value='{$group->group_info.group_id}'>
      </form>
      </td>
      <td>
      <form action='user_group_manage.php' method='POST'>
      <input type='submit' class='button' value='{lang_print id=2000206}'>&nbsp;
      <input type='hidden' name='task' value='reject_do'>
      <input type='hidden' name='group_id' value='{$group->group_info.group_id}'>
      </form>
      </td>
      </tr>
      </table>
    </td>
    </tr>
    </table>
  </div>


{* SHOW JOIN GROUP PAGE *}
{elseif $subpage == "join"}


  <div style='text-align:left; padding-left: 10px; padding-top: 10px;'>
    {lang_print id=2000168}

    <br>

    <form action='user_group_manage.php' method='POST'>

    <table cellpadding='0' cellspacing='0'>
    <tr><td colspan='2'>&nbsp;</td></tr>
    <tr>
    <td colspan='2'>
      <table cellpadding='0' cellspacing='0'>
      <tr>
      <td>
      <input type='submit' class='button' value='{lang_print id=2000165}'>&nbsp;
      <input type='hidden' name='task' value='join_do'>
      <input type='hidden' name='group_id' value='{$group->group_info.group_id}'>
      </form>
      </td>
      <td>
        <input type='button' class='button' value='{lang_print id=39}' onClick='window.parent.TB_remove();'>
      </td>
      </tr>
      </table>
    </td>
    </tr>
    </table>
  </div>



{* SHOW LEAVE GROUP PAGE *}
{elseif $subpage == "leave"}


  <div style='text-align:left; padding-left: 10px; padding-top: 10px;'>
    {lang_print id=2000166}
    {if $group->user_rank == 2}<br><br>{lang_sprintf id=2000167 1=$group->group_info.group_id}{/if}

    <br>

    <form action='user_group_manage.php' method='POST'>

    <table cellpadding='0' cellspacing='0'>
    <tr><td colspan='2'>&nbsp;</td></tr>
    <tr>
    <td colspan='2'>
      <table cellpadding='0' cellspacing='0'>
      <tr>
      <td>
      <input type='submit' class='button' value='{lang_print id=2000160}'>&nbsp;
      <input type='hidden' name='task' value='leave_do'>
      <input type='hidden' name='group_id' value='{$group->group_info.group_id}'>
      </form>
      </td>
      <td>
        <input type='button' class='button' value='{lang_print id=39}' onClick='window.parent.TB_remove();'>
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