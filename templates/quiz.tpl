
{*
@author Eldar
@copyright Hire-Experts LLC
@version Quiz 1.02
*}

{include file='header.tpl'}

<script type="text/javascript" src="./include/js/he_quiz.js"></script>

{lang_javascript ids=690691106,690691107}

{literal}
<script type="text/javascript">
window.addEvent('domready', function() {
	he_quiz_play.construct();
});
</script>
{/literal}

<div class="page_header he_quiz_header">
	{$quiz_info.name}
</div>
<div class="he_quiz_status_border">
    <div class="he_quiz_status_bar"></div>
</div>


<div class="he_message_cont">
    <div id="he_message" {if $message}class="he_message_{$message.type}"{else}style="display: none;"{/if}>
		<span class="t">{if $message.title}{$message.title}{/if}</span>
        <span class="c">{if $message.text}{$message.text}{/if}</span>
    </div>
</div>

<div class="he_quiz">
<form method="post" onsubmit="return he_quiz_play.check_answers();" id="he_quiz_form">
	<table align="center">
		<tr>
			<td align="right">
				<div class="he_quiz_prev"><a href="javascript://" id="he_quiz_prev"></a></div>
			</td>
			<td style="width: 612px">
			<div class="he_quiz_cont">
			{assign var=question_number value=$quiz_questions|@count}
				<div class="he_quiz_questions" style="width: {$question_number*610}px;">
				{foreach from=$quiz_questions item=question name=quiz_questions}
					<div class="he_quiz_question" id="quiz_question_{$smarty.foreach.quiz_questions.iteration}">
						<div class="he_quiz_question_header">
							<span class="he_question_number">{$smarty.foreach.quiz_questions.iteration}</span>
							<span class="he_question_text">{$question.text}</span>
						</div>
                        {if $question.photo}
                        <div class="he_quiz_question_photo">
                            <img src="{$photo_url}{$question.photo}" alt="image" height="100px;"/>
                        </div>
                        {/if}
						<div class="he_quiz_answers">
							{foreach from=$question.answers item=answer}
							<div class="he_quiz_answer">
								<input type="radio" id="answer_{$answer.id}" name="answer[{$question.id}]" value="{$answer.id}"/> {$answer.label}
							</div>
							{/foreach}
						</div>
					</div>
				{/foreach}
				</div>
			</div>
			</td>
			<td align="left">
				<div class="he_quiz_next"><a href="javascript://" id="he_quiz_next"></a></div>
			</td>
		</tr>
		<tr>
			<td colspan="3" align="center">
			    <br />
				<input type="hidden" name="task" value="get_result"/>
				<input class="button" type="submit" value="{lang_print id=690691152}"/>
			</td>
		</tr>
	</table>
</form>
</div>

{include file='footer.tpl'}