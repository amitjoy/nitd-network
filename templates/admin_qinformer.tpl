{include file='admin_header.tpl'}

<h2>{lang_print id=9000751}</h2>
{lang_print id=9000752}

<br><br>

{if $is_success != 0}
	<div class='success'><img src='../images/success.gif' class='icon' border='0'> {$is_success_count} {lang_print id=$is_success}</div>
{/if}	
<form action='admin_qinformer.php' name='qinformer_settings' method='POST'>
<table cellpadding='0' cellspacing='0' width='400'>
  <tr>
    <td colspan="2" class='header'>
      {lang_print id=9000758}
    </td>
  </tr>
  <tr>
    <td class='setting2 form2'>
	{lang_print id=9000754}:
	<input type="checkbox" name="qinformer_enabled" value="1" {if $qinformer_enabled}checked="checked"{/if}>
</td>
	</tr>
	</table>
<br/> 		

{section name=cat_loop loop=$cats}
<table cellpadding='0' cellspacing='0' width='400'>
<tr>
    <td class='header' colspan=2>
{lang_print id=$cats[cat_loop].cat_title}
    </td>
  </tr>		
		
		
			{section name=subcat_loop loop=$cats[cat_loop].subcats}
				{section name=field_loop loop=$cats[cat_loop].subcats[subcat_loop].fields}

        <tr>
          <td class='form2' width='32%'>
            {lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_title}:
          </td>
          <td width='68%'>
	    <input type="checkbox" name="fields[]" value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}' {section name=fields_loop loop=$fields}
			{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id == $fields[fields_loop]}
			checked="checked"
	 	    {/if}
			{/section}>
          </td>
        </tr> 
  {/section}
 {/section}
</table><br/>
{/section}

<table cellpadding='0' cellspacing='0' width='400'>
       <tr>
          <td class='form1' width='32%'>&nbsp;

          </td>
          <td width='68%'>
            <input type='hidden' name='task' value='qinformer_settings'>
      		<input type='submit' class='button' value='{lang_print id=9000755}'>
          </td>			  
        </tr>
</table> </form>

{include file='admin_footer.tpl'}