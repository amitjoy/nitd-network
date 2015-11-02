
{*
@author Idris
@copyright Hire-Experts LLC
@version Wall 3.1
*}

{include file='header.tpl'}

<script type="text/javascript">
window.addEvent('domready', function(){ldelim}
    he_wall.owner_mode = false;
    he_wall.action_ids = {$js_action_ids};
    he_wall.wall_object = '{$wall_object}';
    he_wall.wall_object_id = {$wall_object_id};
    he_wall.action_page = true;
    he_wall.construct( '{$wall_uid}' );
    wall_comment.construct({if $total_comments}{$total_comments}{else}0{/if}, {if $count}{$count}{else}0{/if}, {$action.action_id});
{rdelim});
</script>


{if $album_info}
    <div class='page_header'>
        {assign var=album_url value=$url->url_create('album', $action.owner->user_info.user_username, $album_info.album_id)}
        {lang_sprintf id=1000141 1=$url->url_create('profile', $action.owner->user_info.user_username) 2=$action.owner->user_displayname 3=$url->url_create('albums', $action.owner->user_info.user_username)} &#187; <a href="{$album_url}">{$album_info.album_title}</a>
    </div>
    {if $album_info.album_desc != ""}<div>{$album_info.album_desc}</div>{/if}
    <br/>
{elseif $group_info}
    <div class='page_header'>
        {assign var=album_url value=$url->url_create("group_media", $smarty.const.NULL, $group_info.group_id, $group_info.media_id)}
        <a href='{$url->url_create("group", $smarty.const.NULL, $group_info.group_id)}'>{$group_info.group_title}</a>
        &#187; {lang_print id=2000232}
    </div>
{/if}

{lang_javascript ids='690706008,690706009,690706010,690706011'}
<div class="wall_container he_wall_page_container" id="wall_{$wall_uid}">
	<div class="wall_action he_wall_action_page" id="wall_action_{$action.action_id}">
        <a class="owner_photo" href="{$url->url_create('profile', $action.owner->user_info.user_username)}">
            <img src="{$action.owner->user_photo('./images/nophoto.gif')}" style="border: 0 none;" width="{$misc->photo_size($action.owner->user_photo('./images/nophoto.gif'),'100','100','w')}" alt="{lang_sprintf id=509 1=$action.owner->user_displayname_short}">
        </a>
        
        <div class="media_container" style="overflow: visible;">
            {assign var='action_media' value=''}
            {if $action.action_media !== FALSE}
           		{capture assign='action_media'}
           			{section name=action_media_loop loop=$action.action_media}
           				<a href='{if $album_url}{$album_url}{else}{$action.action_media[action_media_loop].actionmedia_link}{/if}' style="display: block;">
           					<div class="photo_cont">
           						<img src='{if $filename}./uploads_wall/{$filename}{else}{$action.action_media[action_media_loop].actionmedia_path}{/if}' border='0' {if $actiontype != "wallpostphoto"}width='{$action.action_media[action_media_loop].actionmedia_width}'{/if} class='recentaction_media'>
           					</div>
           				</a>
           			{/section}
           		{/capture}
            {/if}
            {lang_sprintf assign=action_text id=$action.action_text args=$action.action_vars}
            {$action_text|nl2br|replace:"[media]":$action_media|choptext:50:"<br>"}
        </div>
        
        <a href="{$referrer}" class="wall_return_link"><img border="0" class="button" src="./images/icons/back16.gif"/>{lang_print id=690706045}</a>
        
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

        <div class="comment_box {if !$user->user_exists && !$action_likes[$action.action_id].value && !$total_comments}display_none{/if}" style="width: 650px;">
            <div class="like_box {if !$action_likes[$action.action_id].value}display_none{/if}">
                <div class="like_content">{$action_likes[$action.action_id].value}</div>
            </div>
            
       		<div class='comment' id='comment_paging'>
				<div class='comment_count'>
					<a href='he_wall_ajax_request.php?limit={$limit}&action_id={$action_id}&task=paging&count={$count}' class="page_button" id="prev" >{lang_print id=690706019}</a> 
					<div class="page_info"><span id="total_current">{$count}</span> {lang_print id=690706038} <span id="total_comments">{$total_comments}</span></div>
					<div class="clr"></div>
				</div>
			</div>
			
            <div class="feed_comments" id="comments_container" >
            {assign var=action_id value=$action.action_id}
		    {include file='he_wall_comments.tpl'}
		    </div>
            {if $user->user_exists}
            <div class="comment_add">
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
            {/if}
        </div>                                
    </div>
</div>

{if $user->user_exists}
<div style="display:none;">
    <div class="comment" id="comment_tpl">
        <a class="comment_photo_block" href="{$url->url_create('profile', $user->user_info.user_username)}">
            <img src="{$user->user_photo('./images/nophoto.gif')}" class="comment_photo" width="{$misc->photo_size($user->user_photo('./images/nophoto.gif'),'32','32','w')}" alt="{lang_sprintf id=509 1=$user->user_displayname_short}">
        </a>
        <div class="comment_body">
            <div class="comment_text">
                <a class="comment_author" href="{$url->url_create('profile', $user->user_info.user_username)}">
                    {$user->user_displayname}
                </a>
                <div class="comment_actual_text"></div>
                
                <div class="comment_actions">
                    <div class="wall_delete_comment">
                        <span class="date_time"></span>
                        &#183;
                        <a href="javascript://" class="delete_comment">{lang_print id=690706020}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="clr"></div>
    </div>
</div>
{/if}

{include file='footer.tpl'}