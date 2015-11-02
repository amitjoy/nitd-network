{* INCLUDE HEADER CODE *}
{include file="header_global.tpl"}

{* $Id: user_report.tpl 8 2009-01-11 06:02:53Z john $ *}

{* SHOW CONFIRMATION OF REPORT *}
{if $submitted == 1}

  {* JAVASCRIPT FOR CLOSING BOX *}
  {literal}
  <script type="text/javascript">
  <!-- 
  setTimeout("window.parent.TB_remove();", "1000");
  //-->
  </script>
  {/literal}

  {lang_print id=866}

{* SHOW SEND REPORT FORM *}
{else}

  <div style='text-align:left; padding-left: 10px;'>
  {lang_print id=858}
  <br />
  <br />

  <form action='user_report.php' method='POST'>

  <div><b>{lang_print id=859}</b></div>

  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td>&nbsp;<input type='radio' name='report_reason' id='report_type1' value='1' checked='checked'></td>
  <td><label for='report_type1'>{lang_print id=860}</td>
  </tr>
  <tr>
  <td>&nbsp;<input type='radio' name='report_reason' id='report_type2' value='2'></td>
  <td><label for='report_type2'>{lang_print id=861}</td>
  </tr>
  <tr>
  <td>&nbsp;<input type='radio' name='report_reason' id='report_type3' value='3'></td>
  <td><label for='report_type3'>{lang_print id=862}</td>
  </tr>
  <tr>
  <td>&nbsp;<input type='radio' name='report_reason' id='report_type0' value='0'></td>
  <td><label for='report_type0'>{lang_print id=863}</td>
  </tr>
  </table>
  
  <br>

  <div><b>{lang_print id=864}</b></div>
  <textarea name='report_details' rows='5' cols='50'></textarea>

  <br><br>

  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td>
    <input type='submit' class='button' value='{lang_print id=865}'>&nbsp;
    <input type='hidden' name='task' value='dosend'>
    <input type='hidden' name='return_url' value='{$return_url}'>
    </form>
  </td>
  <td>
    <input type='button' class='button' value='{lang_print id=39}' onClick="window.parent.TB_remove();">
  </td>
  </tr>
  </table>

  </div>

{/if}

</body>
</html>