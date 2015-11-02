{include file="header_global.tpl"}

{literal}
<style type="text/css">
 @import url("templates/styles.css");
 .button{text-transform: capitalize;}
 .feedback_error{color: red;}
 .feedback_success{font-size: 16px; color: #777777;}
</style>
<script type="text/javascript" src="./include/js/feedback.js"></script>
{/literal}





{if $user->user_exists}
<table align="center">
 <tr>
  <td class="feedback_success" style="height: 250px; text-align: center; padding-top: 50px; width: 300px;">
   {lang_print id=17001034}<br>
  </td>
 </tr>
</table>
<script type="text/javascript">
 setTimeout(closeWindow2,17600);
</script>

{else}

<table align="center">
 <tr>
  <td class="feedback_success" style="height: 250px; text-align: center; padding-top: 50px; width: 300px;">
   {lang_print id=17001034}<br>
  </td>
 </tr>
</table>
<script type="text/javascript">
 setTimeout(closeWindow2,17600);
</script>


{/if}
</body>
</html>

