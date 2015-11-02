
{*
@author Eldar
@copyright Hire-Experts LLC
@version Quiz 1.02
*}

{include file='header.tpl'}

<script type="text/javascript" src="./include/js/he_quiz.js"></script>

{include file='quiz_tabs.tpl'}


{lang_javascript ids=690691103,690691104,690691105}


<form method="post" enctype="multipart/form-data" onsubmit="return he_quiz.check_questions({$min_question_count});">
<div class="he_quiz_questions">
{assign var=question_key value=0}
{foreach from=$question_arr item=question name=quiz_questions}
{assign var=question_key value=$smarty.foreach.quiz_questions.iteration}
	<div class="he_quiz_question">
	<table class="quiz_tbl" cellpadding="0" cellspacing="0">
		<thead>
			<tr class="he_quiz_first">
				<th>{lang_print id=690691110}<b class="he_question_number">{$question_key}</b></th>
				<th><a href="javascript://" onclick="he_quiz.delete_question(this);">{lang_print id=690691111}</a></th>
			</tr>
			<tr class="he_quiz_second">
				<th colspan="2">
					<input type="hidden" name="question_id[]" value="{$question.id}"/>
					<input type="hidden" name="question_key[]" class="quiz_question_number" value="{$question_key}"/>
					<input type="text" name="question_text[]" class="quiz_question text" value="{$question.text}"/>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><b>{lang_print id=690691112}</b></td>
				<td><b>{lang_print id=690691113}</b></td>
			</tr>
			{foreach from=$results item=result name=quiz_answers}
			{assign var=answer value=$question.answers[$result.id]}
			<tr>
				<td>
					{$smarty.foreach.quiz_answers.iteration}. <input type="text" name="answer_label[{$question_key}][]" class="quiz_answer_label text" value="{$answer.label}"/>
					<input type="hidden" name="answer_id[{$question_key}][]" class="quiz_answer_id" value="{$answer.id}"/>
					<input type="hidden" name="answer_result_id[{$question_key}][]" class="quiz_answer_result_id" value="{$result.id}"/>
				</td>
				<td>
					-> {$result.title}
				</td>
			</tr>
			{/foreach}
            <tr>
                <td>
                    <b>{lang_print id=690691195}</b><br/>
                    <input type="file" class="question_photo" name="photo_{$question_key}"/>
                    <input type="hidden" name="photo[]" value="{$question.photo}"/>
                </td>
                <td>{if $question.photo}
                    <div style="padding-top: 8px; padding-bottom: 8px;">
                        <img src="{$photo_url}{$question.photo}" alt="{$question.title}" height="100px;"/>
                    </div>
                    {/if}
                </td>
            </tr>
		</tbody>
	</table>
	</div>
{/foreach}
</div>

<div class="he_quiz_add_question">
	<input type="button" class="button" value="{lang_print id=690691114}" onclick="he_quiz.add_question({$question_key});"/>
</div>
<div class="he_quiz_save_result">
	<input type="hidden" value="save_questions" name="task"/>
	<input type="submit" value="{lang_print id=1209}" class="button"/>
</div>
</form>

{*QUESTION TPL*}
<div style="display: none;">
	<div class="he_quiz_question" id="he_quiz_question_tpl">
	<table class="quiz_tbl" cellpadding="0" cellspacing="0">
		<thead>
			<tr class="he_quiz_first">
				<th>{lang_print id=690691110}<b class="he_question_number"></b></th>
				<th><a href="javascript://" class="delete_question_btn">{lang_print id=690691111}</a></th>
			</tr>
			<tr class="he_quiz_second">
				<th colspan="2">
					<input type="hidden" name="question_id[]" value="0"/>
					<input type="hidden" name="question_key[]" class="quiz_question_number" value="0"/>
					<input type="text" name="question_text[]" class="quiz_question text" value=""/>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><b>{lang_print id=690691112}</b></td>
				<td><b>{lang_print id=690691113}</b></td>
			</tr>
			{foreach from=$results item=result name=quiz_results}
			<tr>
				<td>
					{$smarty.foreach.quiz_results.iteration}. <input type="text" class="quiz_answer_label text"/>
					<input type="hidden" class="quiz_answer_id"/>
					<input type="hidden" class="quiz_answer_result_id" value="{$result.id}"/>
				</td>
				<td>
					-> {$result.title}
				</td>
			</tr>
			{/foreach}
			<tr>
                <td>
                    <b>{lang_print id=690691195}</b><br/>
                    <input type="file" class="question_photo" name="photo"/>
                </td>
                <td>                  
                </td>
            </tr>
		</tbody>
	</table>
	</div>
</div>

{include file='footer.tpl'}