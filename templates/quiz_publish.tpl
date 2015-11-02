
{*
@author Eldar
@copyright Hire-Experts LLC
@version Quiz 1.02
*}

{include file='header.tpl'}

<script type="text/javascript" src="./include/js/he_quiz.js"></script>

<div class="he_quiz_publish">
{include file='quiz_tabs.tpl'}
</div>

<form method="post">

<div class="he_quiz_congrats">

	<span>{lang_print id=690691122}</span>
	
	<input type="hidden" name="task" value="publish_quiz"/>
	<input type="submit" name="publish" value="{lang_print id=690691123}" class="button"/>
	<input type="submit" name="publish_later" value="{lang_print id=690691124}" class="button"/>
</div>
	
</form>
{include file='footer.tpl'}