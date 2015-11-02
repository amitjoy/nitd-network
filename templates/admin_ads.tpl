{include file='admin_header.tpl'}

{* $Id: admin_ads.tpl 8 2009-01-11 06:02:53Z john $ *}

<h2>{lang_print id=10}</h2>
{lang_print id=394}
<br />
<br />

<table cellpadding='0' cellspacing='0' width='100%'>
<tr>
<td>
  <form action='admin_ads_modify.php' method='get'>
  <input type='submit' class='button' value='{lang_print id=385}'>&nbsp;
  </form>
</td>
{if $ads|@count > 0}
  <td align='right'>
    <form action='admin_ads.php' method='get'>
    <input type='submit' class='button' value='{lang_print id=395}'>
    </form>
  </td>
{/if}
</tr>
</table>

<br>

<table cellpadding='0' cellspacing='0' class='list' width='100%'>
<tr>
<td class='header' width='10'><a class='header' href='admin_ads.php?s={$i}'>{lang_print id=87}</a>&nbsp;</td>
<td class='header' width='100%'><a class='header' href='admin_ads.php?s={$n}'>{lang_print id=258}</a>&nbsp;</td>
<td class='header' align='center'>{lang_print id=259}&nbsp;</td>
<td class='header' align='center' align='center'><a class='header' href='admin_ads.php?s={$v}'>{lang_print id=396}</a>&nbsp;</td>
<td class='header' align='center' align='center'><a class='header' href='admin_ads.php?s={$c}'>{lang_print id=397}</a>&nbsp;</td>
<td class='header' align='center' align='center'>{lang_print id=398}&nbsp;</td>
<td class='header' nowrap='nowrap' width='10'>{lang_print id=153}</td>
</tr>
  {section name='ad_loop' loop=$ads}
    {if $ads[ad_loop].ad_total_views == 0}
      {assign var=ad_views value="<font style='color: #AAAAAA;'>---</font>"}
    {else}
      {assign var=ad_views value=$ads[ad_loop].ad_total_views}
    {/if}
    {if $ads[ad_loop].ad_total_clicks == 0}
      {assign var=ad_clicks value="<font style='color: #AAAAAA;'>---</font>"}
    {else}
      {assign var=ad_clicks value=$ads[ad_loop].ad_total_clicks}
    {/if}
    <tr class='{cycle values="background1,background2"}'>
    <td class='item'>{$ads[ad_loop].ad_id}&nbsp;</td>
    <td class='item'>{$ads[ad_loop].ad_name}&nbsp;</td>
    <td class='item' nowrap='nowrap' align='center'>{if $ads[ad_loop].ad_paused == 1}{lang_print id=402}{elseif $ads[ad_loop].ad_date_start > $nowdate}{lang_print id=404}{elseif $ads[ad_loop].ad_date_end < $nowdate && $ads[ad_loop].ad_date_end != 0}{lang_print id=405}{else}{lang_print id=403}{/if}&nbsp;</td>
    <td class='item' align='center'>{$ad_views}&nbsp;</td>
    <td class='item' align='center'>{$ad_clicks}&nbsp;</td>
    <td class='item' align='center'>{$ads[ad_loop].ad_ctr}&nbsp;</td>
    <td class='item' nowrap='nowrap'>
      [ <a href='admin_ads_modify.php?ad_id={$ads[ad_loop].ad_id}'>{lang_print id=187}</a> ] 
      {if $ads[ad_loop].ad_paused == 0}
        [ <a href='admin_ads.php?task=pause&ad_id={$ads[ad_loop].ad_id}'>{lang_print id=399}</a> ] 
      {elseif $ads[ad_loop].ad_paused == 1}
        [ <a href='admin_ads.php?task=unpause&ad_id={$ads[ad_loop].ad_id}'>{lang_print id=400}</a> ] 
      {/if}
      [ <a href="javascript:confirmDelete('{$ads[ad_loop].ad_id}');">{lang_print id=155}</a> ]
    </td>
  {sectionelse}
    <tr>
    <td colspan='6' class='stat2' align='center'>
      {lang_print id=401}
    </td>
    </tr>
  {/section}
</table>

</td>
</tr>
</table>


{* JAVASCRIPT FOR CONFIRMING DELETION *}
{literal}
<script type="text/javascript">
<!-- 
var ad_id = 0;
function confirmDelete(id) {
  ad_id = id;
  TB_show('{/literal}{lang_print id=406}{literal}', '#TB_inline?height=150&width=300&inlineId=confirmdelete', '', '../images/trans.gif');

}

function deleteAd() {
  window.location = 'admin_ads.php?task=delete&ad_id='+ad_id;
}


//-->
</script>
{/literal}


{* HIDDEN DIV TO DISPLAY CONFIRMATION MESSAGE *}
<div style='display: none;' id='confirmdelete'>
  <div style='margin-top: 10px;'>
    {lang_print id=407}
  </div>
  <br>
  <input type='button' class='button' value='{lang_print id=175}' onClick='parent.TB_remove();parent.deleteAd();'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
</div>

{include file='admin_footer.tpl'}