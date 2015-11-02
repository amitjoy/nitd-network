
{*
@author Eldar
@copyright Hire-Experts LLC
@version Quiz 1.02
*}

{include file='header.tpl'}

<div class="he_quiz_header">
	<div class="he_quiz_thumb"><img src="{if $quiz_info.photo_src}{$quiz_info.photo_src}{else}./images/he_quiz_thumb.jpg{/if}"/></div>
	<div class="he_quiz_content">
		<div class="page_header">{$quiz_info.name}</div>
		<div>{$quiz_info.description}</div>
	</div>
	<div class="he_quiz_take_cont">
	    <div class="he_quiz_act">
	        <a href="quiz.php?quiz_id={$quiz_info.quiz_id}" class="he_quiz_take">{lang_print id=690691137}</a>
            <input type="button" class="button" onclick="he_contacts.link('{lang_print id=690691170}', 'quiz_suggest_to_friends.php?quiz_id={$quiz_info.quiz_id}', false)" value="{lang_print id=690691170}" />	    	
	    </div>
	</div>
	<div class="clr"></div>
</div>

<br/>
<br/>

<div class="he_quiz_list_tabs">
    <a href="javascript://" onclick="$$('.he_quiz_browse_comments').setStyle('display', 'none'); $$('.he_quiz_browse_results').setStyle('display', 'block'); $(this).getNext('a').removeClass('active'); $(this).addClass('active'); this.blur();" class="active">{lang_print id=690691184}</a>
    <a href="javascript://" onclick="$$('.he_quiz_browse_results').setStyle('display', 'none'); $$('.he_quiz_browse_comments').setStyle('display', 'block'); $(this).getPrevious('a').removeClass('active'); $(this).addClass('active'); this.blur();">{lang_print id=690691185}</a>
    <div class="clr"></div>
</div>

<div class="he_quiz_browse_results">
{foreach from=$quiz_results item=quiz_result}
	<div class="he_quiz_browse_result">
        <table class="quiz_tbl" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th colspan="2" align="left">
                        <div class="t">{$quiz_result.title}</div>
                        {if !$quiz_result.photo}
                        <div class="d">
                            {$quiz_result.description}
                        </div>
                        {else}
                        <div class="d" style="margin-top: 8px;">
                            <div style="float: left; 105px;">
                                <img src="{$photo_url}{$quiz_result.photo}" alt="{$quiz_result.title}" width="100px;"/>
                            </div>
                            <div style="padding-left: 105px;">{$quiz_result.description}</div>
                        </div>
                        {/if}
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        {if $quiz_takes[$quiz_result.id]}
                            {foreach from=$quiz_takes[$quiz_result.id] item=quiz_take}
                            <div class="he_quiz_user_result">
                               <a href="{$url->url_create('profile',$quiz_take.user->user_info.user_username)}"><img src='{$quiz_take.user->user_photo('./images/nophoto.gif')}' class='photo{if $user->user_exists && $user->user_info.user_id==$quiz_take.user->user_info.user_id} cur_user_result{/if}' width='{$misc->photo_size($quiz_take.user->user_photo('./images/nophoto.gif'),'90','90','w')}' border='0' alt="{lang_sprintf id=509 1=$quiz_take.user->user_displayname_short}"></a><br/>
                               <a href="{$url->url_create('profile',$quiz_take.user->user_info.user_username)}" class="{if $user->user_exists && $user->user_info.user_id==$quiz_take.user->user_info.user_id} cur_user_result{/if}">{$quiz_take.user->user_displayname_short}</a>
                            </div>
                            {/foreach}
                            <div class="clr"></div>
                        {else}
                            <div>{lang_print id=690691145}</div>
                        {/if}
                    </td>
                </tr>
            </tbody>
        </table>
	</div>
{/foreach}
</div>

<div class="he_quiz_browse_comments">
    {* COMMENTS *}
    <div id="he_quiz_{$quiz_info.quiz_id}_postcomment"></div>
    <div id="he_quiz_{$quiz_info.quiz_id}_comments" style='margin-left: auto; margin-right: auto;'></div>

    {lang_javascript ids=39,155,175,182,183,184,185,187,784,787,829,830,831,832,833,834,835,854,856,891,1025,1026,1032,1034,1071}
    
    <script type="text/javascript">
        SocialEngine.QuizComments = new SocialEngineAPI.Comments({ldelim}
            'canComment' : {if $allowed_to_comment}true{else}false{/if},
            'commentHTML' : '{$setting.setting_comment_html}',
            'commentCode' : {if $setting.setting_comment_code}true{else}false{/if},
            'type' : 'he_quiz',
            'typeIdentifier' : 'quiz_id',
            'typeID' : {$quiz_info.quiz_id},
            'typeTab' : 'he_quiz',
            'typeCol' : 'quiz',
            'initialTotal' : {$total_comments|default:0},
            'object_owner': 'quiz',
            'object_owner_id': {$quiz_info.quiz_id},
            'paginate' : true,
            'cpp' : 10
        {rdelim});

        SocialEngine.RegisterModule(SocialEngine.QuizComments);

        // Backwards
        function addComment(is_error, comment_body, comment_date)
        {ldelim}
            SocialEngine.QuizComments.addComment(is_error, comment_body, comment_date);
        {rdelim}

        function getComments(direction)
        {ldelim}
            SocialEngine.QuizComments.getComments(direction);
        {rdelim}
    </script>
</div>

{include file='footer.tpl'}