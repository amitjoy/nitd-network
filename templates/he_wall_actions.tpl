
{*
@author Ermek
@copyright Hire-Experts LLC
@version Wall 3.1
*}

{foreach from=$wall_actions item=action}
    <div class="wall_action" id="wall_action_{$action.action_id}">
        <a class="owner_photo" href="{$url->url_create('profile', $action.owner->user_info.user_username)}">
            <img src="{$action.owner->user_photo('./images/nophoto.gif')}" class="photo" width="{$misc->photo_size($action.owner->user_photo('./images/nophoto.gif'),'50','50','w')}" alt="{lang_sprintf id=509 1=$action.owner->user_displayname_short}">
        </a>
        {if $user->user_exists}
        <div class="hide_action_block">
            {if $user->user_info.user_id==$action.action_user_id}
                <a href="javascript://" class="remove_action">{lang_print id=690706050}</a>
            {else}
                <a href="javascript://" class="hide_action">{lang_print id=690706021}</a>
            {/if}
        </div>
        {/if}
        
        <div class="media_container">
            {assign var='action_media' value=''}
            {if $action.action_media !== FALSE}
            	{capture assign='action_media'}
            		{section name=action_media_loop loop=$action.action_media}
            				<div class="photo_cont">
	            				<a href='{$action.action_media[action_media_loop].actionmedia_link}'>
	            					<img src='{$action.action_media[action_media_loop].actionmedia_path}' border='0' width='{$action.action_media[action_media_loop].actionmedia_width}' class='recentaction_media'>
	            				</a>
            				</div>
            		{/section}
            	{/capture}
            {/if}
            {lang_sprintf assign=action_text id=$action.action_text args=$action.action_vars}
            {$action_text|nl2br|replace:"[media]":$action_media|choptext:50:"<br>"}
        </div>
        
        <div class="clr"></div>
        
        <div class="action_options">
            <div class="wall_action_options">
                <img src="./images/icons/{$action.action_icon}" border="0" class="icon">
                <span class="date_time">{assign var='action_date' value=$datetime->time_since($action.action_date)}
                {lang_sprintf id=$action_date[0] 1=$action_date[1]}
                </span>
                {if $user->user_exists}
                &#183;
                <a href="javascript://" class="comment_btn">{lang_print id=690706018}</a>
                &#183;
                <a href="javascript://" class="like_btn">{if $action_likes[$action.action_id].like}{lang_print id=690706009}{else}{lang_print id=690706008}{/if}</a>
                {/if}
            </div>
        </div>
        {if $action_comments.comments[$action.action_id] || $action_comments.counts[$action.action_id]}
            {assign var=has_comment value=1}
        {else}
            {assign var=has_comment value=0}
        {/if}
        <div class="comment_box {if !$has_comment && !$action_likes[$action.action_id].value}display_none{/if}">
            <div class="like_box {if !$action_likes[$action.action_id].value}display_none{/if}">
                <div class="like_content">{$action_likes[$action.action_id].value}</div>
            </div>
            <div class="feed_comments">
            {if $action_comments.counts[$action.action_id]}
                <div class="comment">
                    <div class="comment_count">
                        <a href="wall_action.php?id={$action.action_id}">{lang_sprintf id=690706017 1=$action_comments.counts[$action.action_id]}</a>
                    </div>
                </div>
            {/if}
            {foreach from=$action_comments.comments[$action.action_id] item=comment}
                <div class="comment" id="comment_{$comment.id}">
                    <a class="comment_photo_block" href="{$url->url_create('profile', $comment.author->user_info.user_username)}">
                        <img src="{$comment.author->user_photo('./images/nophoto.gif')}" class="comment_photo" width="{$misc->photo_size($comment.author->user_photo('./images/nophoto.gif'),'32','32','w')}" alt="{lang_sprintf id=509 1=$comment.author->user_displayname_short}">
                    </a>
                    <div class="comment_body">
                        <div class="comment_text">
                            <a class="comment_author" href="{$url->url_create('profile', $comment.author->user_info.user_username)}">
                                {$comment.author->user_displayname}
                            </a>
                            <div class="comment_actual_text">
                                {$comment.text|strip_tags|he_wall_format_text:300|nl2br}
                            </div>
                            
                            <div class="comment_actions">
                                <div class="wall_delete_comment">
                                    <span class="date_time">
                                        {assign var='comment_date' value=$datetime->time_since($comment.post_stamp)}
                                        {lang_sprintf id=$comment_date[0] 1=$comment_date[1]}
                                    </span>
                                    {if $user->user_exists && $user->user_info.user_id==$comment.author_id}
                                    &#183;
                                    <a href="javascript://" class="delete_comment">{lang_print id=690706022}</a>
                                    {/if}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clr"></div>
                </div>
            {/foreach}
            </div>
            <div class="comment_add display_none">
                <a class="comment_photo_block" href="{$url->url_create('profile', $user->user_info.user_username)}">
                    <img src="{$user->user_photo('./images/nophoto.gif')}" class="comment_photo" width="{$misc->photo_size($user->user_photo('./images/nophoto.gif'),'32','32','w')}" alt="{lang_sprintf id=509 1=$user->user_displayname_short}">
                </a>
                <div class="comment_body">
                    <div class="comment_text">
                        <div class="comment_actual_text">
                           <textarea name="comment_text" class="comment_text_input">{lang_print id=690706010}</textarea>
                        </div>
                        <div class="comment_actions" style="text-align: right;">
                            <input type="button" value="{lang_print id=690706018}" class="button add_comment_btn"/>
                        </div>
                    </div>
                </div>
                <div class="clr"></div>
            </div>
        </div>                                
    </div>
{/foreach}