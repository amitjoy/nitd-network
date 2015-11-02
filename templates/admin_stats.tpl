{include file='admin_header.tpl'}

{* $Id: admin_stats.tpl 235 2009-11-13 04:30:39Z phil $ *}

<h2>{lang_print id=24}</h2>
{lang_print id=477}
<br />
<br />


<table cellpadding='0' cellspacing='0'>
<tr>
<td width='120' style='text-align: right; padding: 5px; vertical-align: top; line-height: 19px;' nowrap='nowrap'>

<b>{lang_print id=483}</b><br>
{if $graph == "summary"}<b>{/if}<a href='admin_stats.php?graph=summary'>{lang_print id=478}</a></b><br>
{if $graph == "visits"}<b>{/if}<a href='admin_stats.php?graph=visits'>{lang_print id=479}</a></b><br>
{if $graph == "logins"}<b>{/if}<a href='admin_stats.php?graph=logins'>{lang_print id=480}</a></b><br>
{if $graph == "signups"}<b>{/if}<a href='admin_stats.php?graph=signups'>{lang_print id=481}</a></b><br>
{if $graph == "friends"}<b>{/if}<a href='admin_stats.php?graph=friends'>{lang_print id=482}</a></b><br>

<br>
<b>{lang_print id=484}</b><br>
{if $graph == "referrers"}<b>{/if}<a href='admin_stats.php?graph=referrers'>{lang_print id=485}</a></b><br>
{if $graph == "space"}<b>{/if}<a href='admin_stats.php?graph=space'>{lang_print id=486}</a></b><br>


</td>
<td style='padding: 5px; border: 1px dashed #CCCCCC; text-align: center;' width='550' height='420'>

  {* SHOW CHART *}
  {if $chart != ""}

    <br>
    <form action='admin_stats.php' method='get'>
    <table cellpadding='0' cellspacing='0' align='center'>
    <tr>
    <td style='padding-right: 20px;'><a href='admin_stats.php?period={$period}&graph={$graph}&start={math equation='p+1' p=$start}'><img src='../images/admin_arrowleft.gif' border='0' class='icon2'>{lang_print id=487}</a></td>
    <td>{lang_print id=488}&nbsp;</td>
    <td>
      <select name='period' class='text'>
      <option value='week'{if $period == "week"} SELECTED{/if}>{lang_print id=489}</option>
      <option value='month'{if $period == "month"} SELECTED{/if}>{lang_print id=490}</option>
      <option value='year'{if $period == "year"} SELECTED{/if}>{lang_print id=491}</option>
      </select>&nbsp;
    </td>
    <td>
      <input type='submit' class='button_small' value='{lang_print id=492}'>
    </td>
    <td style='padding-left: 20px;'><a href='admin_stats.php?period={$period}&graph={$graph}&start={math equation='p-1' p=$start}'>{lang_print id=493}<img src='../images/admin_arrowright.gif' border='0' class='icon' style='margin-left: 5px;'></a></td>
    </tr>
    </table>
    <input type='hidden' name='graph' value='{$graph}'>
    </form>
    <br>
    {$chart}

  {* SHOW REFERRING URLS *}
  {elseif $referrers != ""}

    <b>{lang_print id=485}</b><br>
    {lang_print id=495}

    {* IF THERE ARE ANY REFS *}
    {if $referrers_total > 0}
      [ <a href='admin_stats.php?graph=referrers&task=clearrefs'>{lang_print id=496}</a> ]
      <br><br>
      <table cellpadding='0' cellspacing='0' class='stats' style='border-top: none; margin: 10px;'>
      <tr>
      <td class='stat1'><b>{lang_print id=497}</b></td>
      <td class='stat2'><b>{lang_print id=494}</b></td>
      </tr>
      {section name=referrers_loop loop=$referrers}
        <tr>
        <td class='stat1' align='center'>{$referrers[referrers_loop].referrer_hits}</td>
        <td class='stat2'><a href='{$referrers[referrers_loop].referrer_url}' target='_blank'>{$referrers[referrers_loop].referrer_url|truncate:60:"...":true}</a></td>
        </tr>
      {/section}
      </table>
    {else}
    {* THERE ARE NO REFS, SHOW NOTICE *}
      <br><br><i>{lang_print id=498}</i>
    {/if}

  {* SHOW SPACE USED INFO *}
  {elseif $totalspace != ""}
    {lang_print id=499}
    <br><font class='large_gray'>{$media} MB</font>
    <br><br><font class='large_gray'>+</font>
    <br><br>
    {lang_print id=500}
    <br><font class='large_gray'>{$database} MB</font>
    <br><br><font class='large_gray'>=</font>
    <br><br>
    {lang_print id=501}
    <br><font class='large'>{$totalspace} MB</font>

  {* SHOW QUICK SUMMARY *}
  {else}

    <b>{lang_print id=502}</b><br>
    {lang_print id=503}
    <br><br>

    <table cellpadding='0' cellspacing='0' align='center'>
    <tr>
    <td align='right'>{lang_print id=504} &nbsp;</td>
    <td>{lang_sprintf id=505 1=$total_users_num}</td>
    </tr>
    <tr>
    <td align='right'>{$admin_stats36} &nbsp;</td>
    <td>{lang_sprintf id=506 1=$total_messages_num}</td>
    </tr>
    <tr>
    <td align='right'>{$admin_stats37} &nbsp;</td>
    <td>{lang_sprintf id=507 1=$total_comments_num}</td>
    </tr>
    </table>

  {/if}

</td>
</tr>
</table>

{* AUTO-ACTIVATE FLASH OBJECTS IN IE *}
<script type="text/javascript" src="../include/js/activate_flash.js"></script>

{include file='admin_footer.tpl'}