
{*
@author Eldar
@copyright Hire-Experts LLC
@version Quiz 1.02
*}

<div class="he_quiz_tabs">
	<div class="he_step_tab he_tab_act">
		<a href="quiz_general.php?quiz_id={$quiz_id}">{lang_print id=690691085}</a>
	</div>
	<div class="he_step_tab {if $steps.results}he_tab_act{/if}">
		{if $steps.results}<a href="quiz_results.php?quiz_id={$quiz_id}">{lang_print id=690691086}</a>
		{else}<span>{lang_print id=690691086}</span>{/if}
	</div>
	<div class="he_step_tab {if $steps.questions}he_tab_act{/if}">
		{if $steps.questions}<a href="quiz_questions.php?quiz_id={$quiz_id}">{lang_print id=690691087}</a>
		{else}<span>{lang_print id=690691087}</span>{/if}
	</div>
	<div class="he_step_tab {if $steps.publish}he_tab_act{/if}">
		{if $steps.publish}<a href="quiz_publish.php?quiz_id={$quiz_id}">{lang_print id=690691088}</a>
		{else}<span>{lang_print id=690691088}</span>{/if}
	</div>

	<div class="clr"></div>
</div>

<div class="he_message_cont">
    <div id="he_message" {if $message}class="he_message_{$message.type}"{else}style="display: none;"{/if}>
		<span class="t">{if $message.title}{$message.title}{/if}</span>
        <span class="c">{if $message.text}{$message.text}{/if}</span>
    </div>
</div>
