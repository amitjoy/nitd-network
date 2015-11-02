{include file='admin_header_global.tpl'}

{* $Id: admin_fields.tpl 235 2009-11-13 04:30:39Z phil $ *}

{* INCLUDE JAVASCRIPT AND FIELD DIV *}
{include file='admin_fields_js.tpl'}


{* SET STYLES *}
{literal}
<style type='text/css'>

body {
	margin: 0px;
	padding: 10px;
	background-image: none;
}

body, td, div {
	color: #666666;
	font-family: "Trebuchet MS", arial, serif;
	font-size: 12px;
}

td {
	font-size: 11px;
}

.text {
	font-size: 12px;
	font-family: arial, verdana, serif;
}

textarea.text {
	font-family: arial, verdana, serif;
}

form {
	margin: 0px;
}

img.icon, input.checkbox {
	vertical-align: middle;
}

input.button {
	font-family: arial, verdana, serif;
	font-size: 11px;
	padding: 3px;
	color: #333333;
	font-weight: bold;
	background: #EEEEEE;
	vertical-align: middle;
	border-top: 1px solid #CCCCCC;
	border-left: 1px solid #CCCCCC;
	border-bottom: 1px solid #777777;
	border-right: 1px solid #777777;
	margin-right: 5px;
}

div.fielderror {
	font-weight: bold;
	color: #FF0000;
	text-align: left;
	padding: 7px 8px 7px 7px;
	background: #FFF3F3;
	margin-bottom: 7px;
}

</style>
{/literal}


{* JAVASCRIPT FOR ADDING CATEGORIES AND FIELDS *}
{literal}
<script type="text/javascript">
<!-- 

var hideSearch = {/literal}{if $hideSearch == 1}true{else}false{/if}{literal};
var hideDisplay = {/literal}{if $hideDisplay == 1}true{else}false{/if}{literal};
var hideSpecial = {/literal}{if $hideSpecial == 1}true{else}false{/if}{literal};

window.addEvent('domready', function(){
    {/literal}{$function}{literal}
});
var categories = {{/literal}{section name=cat_loop_js loop=$cats}'{$cats[cat_loop_js].cat_id}':{literal}{'title':'{/literal}{lang_print id=$cats[cat_loop_js].cat_title}{literal}', 'subcats': {{/literal}{section name=subcat_loop_js loop=$cats[cat_loop_js].subcats}'{$cats[cat_loop_js].subcats[subcat_loop_js].subcat_id}':'{lang_print id=$cats[cat_loop_js].subcats[subcat_loop_js].subcat_title}'{if $smarty.section.subcat_loop_js.last != TRUE},{/if}{/section}{literal}}}{/literal}{if $smarty.section.cat_loop_js.last != TRUE},{/if}{/section}{literal}};
var cat_type = '{/literal}{$cat_type}{literal}';
//-->
</script>
{/literal}

{* ADD/EDIT FIELD DISPLAY *}
<div id='fielderror'></div>

<form action='admin_fields.php' id='fieldForm' method='POST' target='ajaxframe'>
<div style='margin-bottom: 3px;' id='field_title_div'>
{lang_print id=106}<br>
<input type='text' class='text' name='field_title' id='field_title' autocomplete='off' size='30' maxlength='100'>
</div>

<div style='margin-bottom: 3px;' id='field_cat_id_div'>
{lang_print id=107}<br>
<select name='field_cat_id' id='field_cat_id' class='text' onChange='changefieldcat(this.options[this.selectedIndex].value);'>
</select>
</div>

<div style='margin-bottom: 3px;' id='field_subcat_id_div'>
{lang_print id=108}<br>
<select name='field_subcat_id' id='field_subcat_id' class='text'>
</select>
</div>

<div style='margin-bottom: 3px;' id='field_type_div'>
{lang_print id=109}<br>
<select name='field_type' id='field_type' class='text' onChange='changefieldtype();'>
<option value=''></option>
<option value='1'>{lang_print id=110}</option>
<option value='2'>{lang_print id=111}</option>
<option value='3'>{lang_print id=112}</option>
<option value='4'>{lang_print id=113}</option>
<option value='5'>{lang_print id=114}</option>
<option value='6'>{lang_print id=989}</option>
</select>
</div>

<div style='margin-bottom: 3px;' id='field_style_div'>
{lang_print id=115}<br>
<input type='text' class='text' name='field_style' id='field_style' size='30' maxlength='200'>
</div>

<div style='margin-bottom: 3px;' id='field_desc_div'>
{lang_print id=116}<br>
<textarea name='field_desc' id='field_desc' rows='4' cols='40' class='text'></textarea>
</div>

<div style='margin-bottom: 3px;' id='field_error_div'>
{lang_print id=117}<br>
<input type='text' class='text' name='field_error' id='field_error' size='30' maxlength='250'>
</div>

<div style='margin-bottom: 3px;' id='field_required_div'>
{lang_print id=118}<br>
<select name='field_required' id='field_required' class='text'>
<option value='0'>{lang_print id=119}</option>
<option value='1'>{lang_print id=120}</option>
</select>
</div>

<div style='margin-bottom: 3px;' id='field_search_div'>
{lang_print id=121}<br>
<select name='field_search' id='field_search' class='text'>
<option value='0'>{lang_print id=122}</option>
<option value='1'>{lang_print id=123}</option>
<option value='2'>{lang_print id=124}</option>
</select>
{capture assign=tip}{lang_print id=125}{/capture}
<img src='../images/icons/tip.gif' border='0' class='Tips1' title='{$tip|replace:"'":"&#039;"}'>
</div>

<div style='margin-bottom: 3px;' id='field_display_div'>
{lang_print id=990}<br>
<select name='field_display' id='field_display' class='text'>
<option value='1'>{lang_print id=992}</option>
<option value='2'>{lang_print id=991}</option>
<option value='0'>{lang_print id=993}</option>
</select>
</div>

<div style='margin-bottom: 3px;' id='field_special_div'>
{lang_print id=994}<br>
<select name='field_special' id='field_special' class='text'>
<option value='0'></option>
<option value='1'>{lang_print id=995}</option>
<option value='2'>{lang_print id=1029}</option>
<option value='3'>{lang_print id=1030}</option>
</select>
{capture assign=tip}{lang_print id=1031}{/capture}
<img src='../images/icons/tip.gif' border='0' class='Tips1' title='{$tip|replace:"'":"&#039;"}'>
</div>

<div style='margin-bottom: 3px;' id='field_html_div'>
{lang_print id=126}<br>
<input type='text' name='field_html' id='field_html' maxlength='200' size='30' class='text'>
{capture assign=tip}{lang_print id=127}{/capture}
<img src='../images/icons/tip.gif' border='0' class='Tips1' title='{$tip|replace:"'":"&#039;"}'>
</div>

<div style='margin-bottom: 3px;' id='field_maxlength_div'>
{lang_print id=128}<br>
<select name='field_maxlength' id='field_maxlength' class='text'>
<option value='30'>30</option>
<option value='50'>50</option>
<option value='100'>100</option>
<option value='150'>150</option>
<option value='200'>200</option>
<option value='250'>250</option>
</select>
</div>

<div style='margin-bottom: 3px;' id='field_link_div'>
{lang_print id=129}<br>
<input type='text' class='text' name='field_link' id='field_link' size='30' maxlength='250'>
{capture assign=tip}{lang_print id=130}{/capture}
<img src='../images/icons/tip.gif' border='0' class='Tips1' title='{$tip|replace:"'":"&#039;"}'>
</div>

<div style='margin-bottom: 3px;' id='field_regex_div'>
{lang_print id=131}<br>
<input type='text' class='text' name='field_regex' id='field_regex' size='30' maxlength='250'>
{capture assign=tip}{lang_print id=132}{/capture}
<img src='../images/icons/tip.gif' border='0' class='Tips1' title='{$tip|replace:"'":"&#039;"}'>
</div>

<div style='margin-bottom: 3px;' id='field_suggestions_div'>
{capture assign=tip}{lang_print id=1099}{/capture}
{lang_print id=1097} <img src='../images/icons/tip.gif' border='0' class='Tips1' title='{$tip|replace:"'":"&#039;"}'><br>
<textarea name='field_suggestions' id='field_suggestions' rows='4' cols='40' class='text'></textarea>
</div>

<div style='margin-bottom: 3px;' id='field_options_div'>
{capture assign=tip}{lang_print id=139}{/capture}
{lang_print id=133} <img src='../images/icons/tip.gif' border='0' class='Tips1' title='{$tip|replace:"'":"&#039;"}'><br>
<table cellpadding='0' cellspacing='0'>
<tr>
<td style='width: 47px;'>{lang_print id=138}</td>
<td style='width: 135px;'>{lang_print id=134}</td>
<td style='width: 137px;' id='field_dependency_div'>{lang_print id=135}</td>
<td style='width: 150px;' id='field_dependent_label_div'>{lang_print id=136}</td>
</tr>
</table>
<p id='field_options' style='margin: 0px;'></p>
<div style='font-size: 11px;'><a href="javascript:addOptions('', '', '0', '', '')">{lang_print id=137}</a></div>
</div>

<br>

<div style='margin-bottom: 3px;' id='submitButtons'></div>

<input type='hidden' name='task' id='task' value=''>
<input type='hidden' name='field_id' id='field_id' value=''>
<input type='hidden' name='type' value='{$cat_type}'>
</form>



</body>
</html>