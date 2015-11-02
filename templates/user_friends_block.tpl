{* INCLUDE HEADER CODE *}
{include file="header_global.tpl"}

{* $Id: user_friends_block.tpl 8 2009-01-11 06:02:53Z john $ *}

{* UNBLOCK USER *}
{if $task == "unblock"}

  {* SHOW CONFIRMATION OF UNBLOCK *}
  {if $submitted == 1}

    {* JAVASCRIPT FOR CLOSING BOX *}
    {literal}
    <script type="text/javascript">
    <!-- 
    if(window.parent.document.getElementById('unblock') && window.parent.document.getElementById('block')) {
      window.parent.document.getElementById('unblock').style.display = 'none';
      window.parent.document.getElementById('block').style.display = 'block';
    }
    setTimeout("window.parent.TB_remove();", "1000");
    //-->
    </script>
    {/literal}

    {lang_sprintf id=867 1=$owner->user_displayname}


  {* DISPLAY CONFIRMATION QUESTION *}
  {else}
    <div style='text-align:left; padding-left: 10px;'>
    {lang_sprintf id=870 1=$owner->user_displayname}
    </div>

    <br><br>
    <table cellpadding='0' cellspacing='0'>
    <tr>
    <td>
      <form action='user_friends_block.php' method='POST'>
      <input type='submit' class='button' value='{lang_print id=871}'>&nbsp;
      <input type='hidden' name='task' value='unblock_do'>
      <input type='hidden' name='user' value='{$owner->user_info.user_username}'>
      </form>
    </td>
    <td>
      <input type='button' class='button' value='{lang_print id=39}' onClick='window.parent.TB_remove();'>
    </td>
    </tr>
    </table>

  {/if}



{* BLOCK USER *}
{else}

  {* SHOW CONFIRMATION OF BLOCK *}
  {if $submitted == 1}

    {* JAVASCRIPT FOR CLOSING BOX *}
    {literal}
    <script type="text/javascript">
    <!-- 
    if(window.parent.document.getElementById('unblock') && window.parent.document.getElementById('block')) {
      window.parent.document.getElementById('unblock').style.display = 'block';
      window.parent.document.getElementById('block').style.display = 'none';
    }
    setTimeout("window.parent.TB_remove();", "1000");
    //-->
    </script>
    {/literal}

    {lang_sprintf id=872 1=$owner->user_displayname}


  {* DISPLAY CONFIRMATION QUESTION *}
  {else}
    <div style='text-align:left; padding-left: 10px;'>
    {lang_sprintf id=873 1=$owner->user_displayname}
    </div>

    <br><br>
    <table cellpadding='0' cellspacing='0'>
    <tr>
    <td>
      <form action='user_friends_block.php' method='POST'>
      <input type='submit' class='button' value='{lang_print id=874}'>&nbsp;
      <input type='hidden' name='task' value='block_do'>
      <input type='hidden' name='user' value='{$owner->user_info.user_username}'>
      </form>
    </td>
    <td>
      <input type='button' class='button' value='{lang_print id=39}' onClick='window.parent.TB_remove();'>
    </td>
    </tr>
    </table>

  {/if}

{/if}

</body>
</html>