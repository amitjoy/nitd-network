
{*
@author Eldar
@copyright Hire-Experts LLC
@version Quiz 1.02
*}

{include file='header.tpl'}

<script type="text/javascript" src="./include/js/he_quiz.js"></script>

<div class="page_header">{lang_print id=690691134}</div>
<br />

<div class="he_quiz_list_tabs">
	<a href="browse_quiz.php?mode=popular"{if $mode=='popular'} class="active"{/if}>{lang_print id=690691135}</a>
	<a href="browse_quiz.php?mode=recently"{if $mode=='recently'} class="active"{/if}>{lang_print id=690691136}</a>
	<a href="browse_quiz.php?mode=commented"{if $mode=='commented'} class="active"{/if}>{lang_print id=690691186}</a>
	<div class="clr"></div>
</div>
<br />

<div class="he_user_quiz_list">

    <div style="float: left; width: 650px;">
        {foreach from=$quiz_arr item=quiz}
        <div class="he_user_quiz">
        {if $mode!='commented'}
        	<div class="he_user_quiz_thumb"><img src="{if $quiz.photo_url}{$quiz.photo_url}{else}./images/he_quiz_thumb.jpg{/if}"/></div>
        	<div class="he_user_quiz_content">
        		<div class="he_user_quiz_name"><b><a href="browse_quiz_results.php?quiz_id={$quiz.quiz_id}">{$quiz.name}</a></b></div>
        		<div class="he_user_quiz_descr">{$quiz.description|truncate:200}</div>
        		<div class="he_user_quiz_takes">
        			{lang_print id=690691127} {$quiz.takes}<br/>
        			{lang_print id=690691187} {$quiz.comments}<br/>
        			<a href="browse_quiz_results.php?quiz_id={$quiz.quiz_id}">{lang_print id=690691128}</a>
        		</div>
        	</div>
            <div class="he_quiz_act">
                <a href="quiz.php?quiz_id={$quiz.quiz_id}" class="he_quiz_take">{lang_print id=690691137}</a>
            </div>
        	<div class="clr"></div>
        {else}
            <div class="he_user_quiz_thumb" style="height: 65px; width: 65px;">
                <a href="{$url->url_create('profile',$quiz.user->user_info.user_username)}">
                    <img src='{$quiz.user->user_photo('./images/nophoto.gif')}' class='photo' width='{$misc->photo_size($quiz.user->user_photo('./images/nophoto.gif'),'60','60','w')}' border='0' alt="{lang_sprintf id=509 1=$quiz.user->user_displayname_short}">
                </a>
            </div>
            {assign var=post_date value=$datetime->time_since($quiz.comment_date)}
            <div class='portal_action_date'>{lang_sprintf id=$post_date[0] 1=$post_date[1]}</div>
            <div class="he_user_quiz_content">
                <div class="he_user_quiz_name"><a href="{$url->url_create('profile',$quiz.user->user_info.user_username)}">{$quiz.user->user_displayname_short}</a> {lang_print id=690691192} <a href="browse_quiz_results.php?quiz_id={$quiz.quiz_id}">{$quiz.name}</a></div>
                <div class="portal_spacer"></div>
                {$quiz.he_quizcomment_body|strip_tags|truncate:200}
            </div>
            <div class="clr"></div>
    	{/if}
        </div>
    	{/foreach}

    {if !$quiz_arr}
        {lang_print id=690691190}
    {/if}
	</div>

	
	<div style="float: left; width: 250px;">
        <div class="header">{lang_print id=690691188}</div>
        <div class="portal_content">
        <ul class="he_quiz_cats">
            <li {if $cat_id==0}class="active"{/if}><a href="browse_quiz.php?mode={$mode}">{lang_print id=690691189}</a></li>
            {foreach from=$quiz_cats item=quiz_cat}
            <li {if $cat_id==$quiz_cat.id}class="active"{/if}><a href="browse_quiz.php?mode={$mode}&cat_id={$quiz_cat.id}">{$quiz_cat.label}</a></li>
            {/foreach}
        </ul>
        </div>
        <div class="portal_spacer"></div>
        
        <div class="header">{lang_print id=690691191}</div>
        <div class="portal_content">
        {foreach from=$taked_quiz_list item=quiz_take}
            <div>
                <div style="float: left; width: 50px; text-align: center;">
                    <a href="{$url->url_create('profile',$quiz_take.user->user_info.user_username)}">
                        <img src='{$quiz_take.user->user_photo('./images/nophoto.gif')}' class='photo' width='{$misc->photo_size($quiz_take.user->user_photo('./images/nophoto.gif'),'50','50','w')}' border='0' alt="{lang_sprintf id=509 1=$quiz_take.user->user_displayname_short}">
                    </a>
                </div>
                <div style="padding-left: 60px;">
                    <a href="{$url->url_create('profile',$quiz_take.user->user_info.user_username)}">{$quiz_take.user->user_displayname_short}</a> {lang_print id=690691193} <a href="browse_quiz_results.php?quiz_id={$quiz_take.quiz_id}">{$quiz_take.name}</a>
                    <br/>
                    {if $quiz_take.photo}
                        <a href="browse_quiz_results.php?quiz_id={$quiz_take.quiz_id}">
                            <img src='{$quiz_take.photo_url}' class='photo' width='{$misc->photo_size($quiz_take.photo_url,'50','50','w')}' border='0' alt="{$quiz_take.name}">
                        </a>
                    {/if}
                    {assign var=action_date value=$datetime->time_since($quiz_take.play_stamp)}
                    <div class='portal_action_date'>{lang_sprintf id=$action_date[0] 1=$action_date[1]}</div> 
                </div>
                <div class="clr"></div>
            </div>
        {/foreach}
        </div>
        <div class="portal_spacer"></div>        
    </div>
    
    <div class="clr"></div>

</div>

<br />
{he_quiz_paging total=$paging.total on_page=$paging.on_page pages=$paging.pages}

{include file='footer.tpl'}