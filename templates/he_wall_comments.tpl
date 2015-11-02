
{*
@author Ermek
@copyright Hire-Experts LLC
@version Wall 3.1
*}

{foreach from=$action_comments[$action_id] item=comment}
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