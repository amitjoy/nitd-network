{include file='header_global.tpl'}

{* $Id: user_group_subscribe.tpl 10 2009-01-11 06:03:42Z john $ *}

{* SHOW CONFIRMATION OF SUBSCRIBING/UNSUBSCRIBING TO GROUP *}
{if $result != 0}

  {* JAVASCRIPT FOR CLOSING BOX *}
  {literal}
  <script type="text/javascript">
  <!-- 
  setTimeout("window.parent.TB_remove();", "1000");
  if(window.parent.subscribe_update) { setTimeout("window.parent.subscribe_update('{/literal}{$is_subscribed}{literal}');", "800"); }
  //-->
  </script>
  {/literal}

  <br><div>{lang_print id=$result}</div>


{* SHOW UNSUBSCRIBE PAGE *}
{elseif $is_subscribed}

  <div style='text-align:left; padding-left: 10px; padding-top: 10px;'>
    {lang_print id=2000236}

    <br>

    <table cellpadding='0' cellspacing='0'>
    <tr><td colspan='2'>&nbsp;</td></tr>
    <tr>
    <td colspan='2'>
      <table cellpadding='0' cellspacing='0'>
      <tr>
      <td>
      <form action='user_group_subscribe.php' method='POST'>
      <input type='submit' class='button' value='{lang_print id=2000237}'>&nbsp;
      <input type='hidden' name='task' value='unsubscribe_do'>
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


{* SHOW SUBSCRIBE PAGE *}
{elseif !$is_subscribed}


  <div style='text-align:left; padding-left: 10px; padding-top: 10px;'>
    {lang_print id=2000234}

    <br>

    <table cellpadding='0' cellspacing='0'>
    <tr><td colspan='2'>&nbsp;</td></tr>
    <tr>
    <td colspan='2'>
      <table cellpadding='0' cellspacing='0'>
      <tr>
      <td>
      <form action='user_group_subscribe.php' method='POST'>
      <input type='submit' class='button' value='{lang_print id=2000235}'>&nbsp;
      <input type='hidden' name='task' value='subscribe_do'>
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