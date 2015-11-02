
{*
@author Ermek
@copyright Hire-Experts LLC
@version Wall 3.1
*}

<script type="text/javascript">
window.addEvent('domready', function(){ldelim}
    he_wall.action_ids = {$js_action_ids};
    he_wall.wall_object = '{$wall_object}';
    he_wall.wall_object_id = {$wall_object_id};
    he_wall.allow_post = {$allow_post};
    he_wall.actions_per_page = {$setting.setting_he_wall_actions_per_page},
    he_wall.construct( '{$wall_uid}' );
{rdelim});
</script>

{if $wall_object != 'userhome'}
<style type="text/css">
.wall_action .wall_link {ldelim}display: none;{rdelim}
</style>
{/if}

{lang_javascript ids='690706008,690706009,690706010,690706011,690706058,690706072,690706073'}

<div class="wall_container" id="wall_{$wall_uid}">
    {if $allow_post}
    <div class="wall_post_action">
        <div class="wall_post_body">        
            <div class="input_div">
                <textarea class="post_action_input">{lang_print id=690706010}</textarea>
            </div>            
            <div class="tab_contents">
                <div class="default_tab">
                    <div class="wall_post_tabs">
                        <div class="wall_tab_title">{lang_print id=690706023}</div>
                        <a href="javascript://" class="wall_tab_icon upload_photo_btn">
                            <img src="./images/he_wall_add_photo.gif" border="0"/>
                            <div class="wall_tooltip_cont display_none"><div class="wall_tooltip">{lang_print id=690706024}</div></div>
                        </a>
                        <a href="javascript://" class="wall_tab_icon add_link_btn">
                            <img src="./images/he_wall_add_link.png" border="0"/>
                            <div class="wall_tooltip_cont display_none"><div class="wall_tooltip">{lang_print id=690706025}</div></div>
                        </a>
                        <a href="javascript://" class="wall_tab_icon add_music_btn">
                            <img src="./images/icons/he_wall_music_icon.png" border="0"/>
                            <div class="wall_tooltip_cont display_none"><div class="wall_tooltip">{lang_print id=690706026}</div></div>
                        </a>
                        <a href="javascript://" class="wall_tab_icon add_video_btn">
                            <img src="./images/icons/he_wall_video_icon.gif" border="0"/>
                            <div class="wall_tooltip_cont display_none"><div class="wall_tooltip">{lang_print id=690706068}</div></div>
                        </a>
                    </div>
                    <div class="btn_div">
                        <input type="button" class="button post_action_btn" value="{lang_print id=690706042}"/>
                        <div class="privacy_block">
                        <a href="javascript://" class="wall_tab_icon action_privacy_btn">
                            <img src="./images/he_wall_post_privacy.png" border="0"/>
                            <img src="./images/he_wall_expand.png" border="0"/>
                            <div class="wall_tooltip_cont display_none"><div class="wall_tooltip">{lang_print id=690706080}</div></div>
                        </a>
                        <div class="wall_action_privacy display_none">
                            {foreach from=$privacy_options key=k item=v name=wall_action_privacy}
                                <div id="privacy_{$k}" class="privacy_option {if $smarty.foreach.wall_action_privacy.first}active{else}border{/if}">{lang_print id=$v}</div>
                            {/foreach}
                        </div>
                        </div>
                    </div>
                    <div class="clr"></div>
                </div>
                <div class="tab_content upload_photo_tab display_none">
                    <div class="upload_photo_menu">
                        <span class="tab_title">{lang_print id=690706024}</span>
                        <a href="javascript://" class="close_tab" title="{lang_print id=690706052}"><img src="./images/he_wall_close.png" border="0"/></a>
                        <div class="clr"></div>
                    </div> 
                    <form action="he_wall_ajax_request.php" id="photo_form_{$wall_uid}" method="post" class="upload_photo_form" enctype="multipart/form-data" onsubmit="return he_wall.post_photo(this);">
                        <input type="file" name="wall_photo" class="wall_photo"/>
                        <input type="hidden" name="wall_object" value="{$wall_object}"/>
                        <input type="hidden" name="wall_object_id" value="{$wall_object_id}"/>
                        <input type="hidden" name="wall_action_privacy" class="share_photo_privacy"/>
                        <input type="hidden" name="task" value="share_photo"/>
                        <input type="hidden" name="text" class="share_photo_text"/>
                        <div class="btn_div">
                            <input type="submit" class="button" value="{lang_print id=690706039}"/>
                        </div>
                    </form>            
                </div>
                <div class="tab_content add_link_tab display_none">
                    <div class="add_link_menu">
                        <span class="tab_title">{lang_print id=690706025}</span>
                        <a href="javascript://" class="close_tab" title="{lang_print id=690706052}"><img src="./images/he_wall_close.png" border="0"/></a>
                        <div class="clr"></div>
                    </div> 
                    <div class="add_link_body">
                        {lang_print id=690706027} <input type="text" name="link" class="text share_link_input"/>
                    </div>
                    <div class="btn_div">
                        <input type="button" class="button post_link_btn" value="{lang_print id=690706039}"/>
                    </div>
                </div>
                <div class="tab_content add_music_tab display_none">
                    <div class="add_music_menu">
                        <span class="tab_title">{lang_print id=690706026}</span>
                        <a href="javascript://" class="close_tab" title="{lang_print id=690706052}"><img src="./images/he_wall_close.png" border="0"/></a>
                        <div class="clr"></div>
                    </div> 
                    <form action="he_wall_ajax_request.php" id="music_form_{$wall_uid}" method="post" class="add_music_form" enctype="multipart/form-data" onsubmit="return he_wall.post_music(this);">
                        <input type="file" name="wall_music" class="wall_music"/>
                        <input type="hidden" name="wall_object" value="{$wall_object}"/>
                        <input type="hidden" name="wall_object_id" value="{$wall_object_id}"/>
                        <input type="hidden" name="wall_action_privacy" class="share_music_privacy"/>
                        <input type="hidden" name="task" value="share_music"/>
                        <input type="hidden" name="text" class="share_music_text"/>
                        <div class="btn_div">
                            <input type="submit" class="button" value="{lang_print id=690706043}"/>
                        </div>
                    </form>
                </div>
                <div class="tab_content add_video_tab display_none">
                    <div class="add_video_menu">
                        <span class="tab_title">{lang_print id=690706068}</span>
                        <a href="javascript://" class="close_tab" title="{lang_print id=690706052}"><img src="./images/he_wall_close.png" border="0"/></a>
                        <div class="clr"></div>
                    </div> 
                    <div class="add_video_body">
                        <div class="video_provider_div">
                            <select name="video_provider" class="text video_provider">
                                <option value="">{lang_print id=690706069}</option>
                                <option value="youtube">{lang_print id=690706070}</option>
                                <option value="vimeo">{lang_print id=690706071}</option>
                            </select>
                        </div>
                        <div class="video_url_div"><input type="text" name="video_url" class="text video_url"/></div>
                    </div>
                    <div class="btn_div">
                        <input type="button" class="button post_video_btn" value="{lang_print id=690706039}"/>
                    </div>
                </div>
                <div class="clr"></div>
            </div>
        </div>
    </div>
    {/if}
    <div class="wall_actions">
        <div>{include file='he_wall_actions.tpl'}</div>
    </div>
    <div class="wall_show_more">
        <a href="javascript://" id="show_more_btn" {if $wall_action_count<$setting.setting_he_wall_actions_per_page}class="display_none"{/if}>{lang_print id=690706028}</a><span class="no_more_info {if $wall_action_count==$setting.setting_he_wall_actions_per_page}display_none{/if}">{lang_print id=690706029}</span>
    </div>
</div>

<div style="display: none;">
    <div class="wall_action hide_action_div" id="hide_action_tpl">
        <div class="hide_actions">
            <input type="button" class="button hide_btn" value="{lang_print id=690706040}"/>
            <input type="button" class="button cancel_btn" value="{lang_print id=690706041}"/>
        </div>
    </div>
    <div class="wall_action remove_action_div" id="remove_action_tpl">
        <div class="hide_actions">
            <input type="button" class="button remove_btn" value="{lang_print id=690706051}"/>
            <input type="button" class="button cancel_btn" value="{lang_print id=690706041}"/>
        </div>
    </div>
    
    {if $user->user_exists}
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
                        <a href="javascript://" class="delete_comment">{lang_print id=690706022}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="clr"></div>
    </div>
    {/if}
</div>