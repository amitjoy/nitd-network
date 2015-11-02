
{*
@author Eldar
@copyright Hire-Experts LLC
@version Quiz 1.02
*}

{include file='header.tpl'}

<script type="text/javascript" src="./include/js/he_quiz.js"></script>

{lang_javascript ids='690691090,690691094'}

{include file='quiz_tabs.tpl'}

<form method="post" enctype="multipart/form-data" onsubmit="return he_quiz.check_general(this);">
<table class="quiz_tbl" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th colspan="2" align="left">{lang_print id=690691089}</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><label>{lang_print id=690691090}</label></td>
			<td><input type="text" class="text" name="name" value="{$general_info.name}"/></td>
		</tr>
		<tr>
			<td><label>{lang_print id=690691194}</label></td>
			<td>
                <select name="cat_id">
                    {foreach from=$quiz_cats item=quiz_cat name=quiz_cats}
                        <option value="{$quiz_cat.id}" {if (!$general_info.cat_id && $smarty.foreach.quiz_cats.first) || $general_info.cat_id==$quiz_cat.id}selected="selected"{/if}>
                            {$quiz_cat.label}
                        </option>
                    {/foreach}
                </select>               
            </td>
		</tr>
		<tr>
			<td><label>{lang_print id=690691091}</label></td>
			<td>
				<textarea class="text" name="description">{$general_info.description}</textarea>
			</td>
		</tr>
		<tr>
			<td><label>{lang_print id=690691092}</label></td>
			<td>
				<input type="file" name="photo"/><br/>
				{lang_print id=690691093}
				{if $general_info.photo}
				<br/>
				<br/>
				<img src="{$general_info.photo}" height="100px"/>
				{/if}
			</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2">
				<input type="hidden" name="task" value="save_general"/>
				<input type="submit" class="button" value="{lang_print id=1209}"/>
			</td>
		</tr>
	</tfoot>
</table>
</form>

{include file='footer.tpl'}