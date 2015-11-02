
{*
@author Eldar
@copyright Hire-Experts LLC
@version Quiz 1.02
*}

{include file='header.tpl'}

<script type="text/javascript" src="./include/js/he_quiz.js"></script>

<div class="user_quiz_page">

<div class="page_header">{lang_print id=690691125}</div>

<div class="button"><a href="quiz_general.php"><img  class="button" border="0" src="./images/icons/plus16.gif"/>{lang_print id=690691126}</a></div>

<div class="he_user_quiz_list">
{foreach from=$quiz_arr item=quiz}
<div class="he_user_quiz">
	<div class="he_user_quiz_thumb"><img src="{if $quiz.photo_url}{$quiz.photo_url}{else}./images/he_quiz_thumb.jpg{/if}"/></div>
	<div class="he_user_quiz_content">
		<div class="he_user_quiz_name">{if $quiz.can_publish}<a href="browse_quiz_results.php?quiz_id={$quiz.quiz_id}">{$quiz.name}</a>{else}{$quiz.name}{/if}</div>
		<div class="he_user_quiz_descr">{$quiz.description|truncate:200}</div>
		{if $quiz.can_publish}
		<div class="he_user_quiz_takes">
			<b>{lang_print id=690691127}</b> {$quiz.takes}<br/>
			<a href="browse_quiz_results.php?quiz_id={$quiz.quiz_id}">{lang_print id=690691128}</a>
		</div>
		{/if}
	</div>
    <div class="he_quiz_act">
        <a href="quiz_general.php?quiz_id={$quiz.quiz_id}">{lang_print id=690691129}</a>
        <a href="javascript://" onclick="if (window.confirm('{lang_print id=690691133}')) window.location.href = 'user_quiz.php?task=delete&quiz_id={$quiz.quiz_id}'">{lang_print id=690691130}</a>
        {if $quiz.can_publish}
            {if $quiz.status}
                <a href="user_quiz.php?task=unpublish&page={$current_page}&quiz_id={$quiz.quiz_id}">{lang_print id=690691131}</a>
            {else}
                <a href="user_quiz.php?task=publish&page={$current_page}&quiz_id={$quiz.quiz_id}">{lang_print id=690691132}</a>
            {/if}
        {else}
            <a href="quiz_general.php?quiz_id={$quiz.quiz_id}" style="color: red">{lang_print id=690691132}</a>
        {/if}
    </div>
	<div class="clr"></div>
</div>
{/foreach}
</div>

<div>
{he_quiz_paging total=$paging.total on_page=$paging.on_page pages=$paging.pages}
</div>

</div>

{include file='footer.tpl'}