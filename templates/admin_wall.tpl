
{*
@author Idris
@copyright Hire-Experts LLC
@version Wall 3.1
*}

{include file='admin_header.tpl'}

<h2>{lang_print id=690706046}</h2>

{lang_print id=690706047}<br /><br />
<br />

{if $result != 0}
<div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=191}</div>
{/if}

<form action="admin_wall.php" method="post">
	<table class="he_tbl" cellspacing="0" cellpadding="0" style="width: 500px;">
	<thead>
		<tr>
			<td class="header" colspan="2"><a href="javascript://"  title="{lang_print id=690706130}"onclick="$(this).getParent('table').getElement('tbody').toggleClass('display_none')">{lang_print id=690706035}</a></td>
		</tr>
	</thead>
	<tbody {if !$result}class="display_none"{/if}>
		<tr class="{cycle values=',bg1'}">
			<td class="item">{lang_print id=690706036}</td>
			<td class="item"><input type="text" size="3" name="setting_he_wall_comments_per_page" value="{$setting.setting_he_wall_comments_per_page}" /></td>
		</tr>
		<tr class="{cycle values=',bg1'}">
			<td class="item">{lang_print id=690706037}</td>
			<td class="item"><input type="text" size="3" name="setting_he_wall_actions_per_page" value="{$setting.setting_he_wall_actions_per_page}" /></td>
		</tr>
		<tr class="{cycle values=',bg1'}">
			<td class="item">{lang_print id=690706105}</td>
			<td class="item"><input type="checkbox" value="1" name="setting_he_wall_guest_view" {if $setting.setting_he_wall_guest_view}checked="checked"{/if}/></td>
		</tr>
        {if $music_plugin_installed}
		<tr class="{cycle values=',bg1'}">
			<td class="item">{lang_print id=690706062}</td>
			<td class="item"><input type="checkbox" value="1" name="setting_he_wall_music_sync" {if $setting.setting_he_wall_music_sync}checked="checked"{/if}/></td>
		</tr>
        {/if}
        {if $album_plugin_installed}
		<tr class="{cycle values=',bg1'}">
			<td class="item">{lang_print id=690706063}</td>
			<td class="item"><input type="checkbox" value="1" name="setting_he_wall_photo_sync" {if $setting.setting_he_wall_photo_sync}checked="checked"{/if}/></td>
		</tr>
        {/if}
        {if $video_plugin_installed}
		<tr class="{cycle values=',bg1'}">
			<td class="item">{lang_print id=690706077}</td>
			<td class="item"><input type="checkbox" value="1" name="setting_he_wall_video_sync" {if $setting.setting_he_wall_video_sync}checked="checked"{/if}/></td>
		</tr>
        {/if}
	</tbody>
	</table>
	<br />
	
	<table class="he_tbl" cellspacing="0" cellpadding="0" style="width: 500px;">
	<thead>
		<tr>
			<td class="header"><a href="javascript://"  title="{lang_print id=690706130}"onclick="$(this).getParent('table').getElement('tbody').toggleClass('display_none')">{lang_print id=690706107}</a></td>
		</tr>
	</thead>
	<tbody {if !$result}class="display_none"{/if}>
		<tr class="{cycle values=',bg1'}">
			<td class="item">{lang_print id=690706108}</td>
		</tr>
		<tr class="{cycle values=',bg1'}">
			<td class="item">
				{lang_print id=690706109} <input type="text" name="setting_he_wall_video_player_width" value="{$setting.setting_he_wall_video_player_width}" size="5" maxlength="4"/> {lang_print id=690706111}&nbsp;&nbsp;&nbsp;
				{lang_print id=690706110} <input type="text" name="setting_he_wall_video_player_height" value="{$setting.setting_he_wall_video_player_height}" size="5" maxlength="4"/> {lang_print id=690706111}
			</td>
		</tr>
		<tr class="{cycle values=',bg1'}">
			<td class="item">{lang_print id=690706112}</td>
		</tr>
		<tr class="{cycle values=',bg1'}">
			<td class="item">
				{lang_print id=690706109} <input type="text" name="setting_he_wall_video_thumb_width" value="{$setting.setting_he_wall_video_thumb_width}" size="5" maxlength="4"/> {lang_print id=690706111}&nbsp;&nbsp;&nbsp;
				{lang_print id=690706110} <input type="text" name="setting_he_wall_video_thumb_height" value="{$setting.setting_he_wall_video_thumb_height}" size="5" maxlength="4"/> {lang_print id=690706111}
			</td>
		</tr>
	</tbody>
	</table>
	<br/>
	
	<table class="he_tbl" cellspacing="0" cellpadding="0" style="width: 500px;">
	<thead>
		<tr>
			<td class="header"><a href="javascript://" title="{lang_print id=690706130}" onclick="$(this).getParent('table').getElement('tbody').toggleClass('display_none')">{lang_print id=690706113}</a></td>
		</tr>
	</thead>
	<tbody {if !$result}class="display_none"{/if}>
		<tr class="{cycle values=',bg1'}">
			<td class="item">{lang_print id=690706114}</td>
		</tr>
		<tr class="{cycle values=',bg1'}">
			<td class="item">
				{lang_print id=690706109} <input type="text" name="setting_he_wall_photo_width" value="{$setting.setting_he_wall_photo_width}" size="5" maxlength="4"/> {lang_print id=690706111}&nbsp;&nbsp;&nbsp;
				{lang_print id=690706110} <input type="text" name="setting_he_wall_photo_height" value="{$setting.setting_he_wall_photo_height}" size="5" maxlength="4"/> {lang_print id=690706111}
			</td>
		</tr>
		<tr class="{cycle values=',bg1'}">
			<td class="item">{lang_print id=690706115}</td>
		</tr>
		<tr class="{cycle values=',bg1'}">
			<td class="item">
				{lang_print id=690706109} <input type="text" name="setting_he_wall_photo_thumb_width" value="{$setting.setting_he_wall_photo_thumb_width}" size="5" maxlength="4"/> {lang_print id=690706111}&nbsp;&nbsp;&nbsp;
				{lang_print id=690706110} <input type="text" name="setting_he_wall_photo_thumb_height" value="{$setting.setting_he_wall_photo_thumb_height}" size="5" maxlength="4"/> {lang_print id=690706111}
			</td>
		</tr>
		<tr class="{cycle values=',bg1'}">
			<td class="item"><b>{lang_print id=690706116}</b><br/> {lang_print id=690706117}</td>
		</tr>
		<tr class="{cycle values=',bg1'}">
			<td class="item">
				<input type="text" name="setting_he_wall_photo_filesize" value="{$setting.setting_he_wall_photo_filesize}" size="5" maxlength="6"/> {lang_print id=690706118}
			</td>
		</tr>
		<tr class="{cycle values=',bg1'}">
			<td class="item"><b>{lang_print id=690706119}</b><br/> {lang_print id=690706120}</td>
		</tr>
		<tr class="{cycle values=',bg1'}">
			<td class="item" style="padding-right: 12px;">
				<textarea style="width: 100%;" class="text" cols="40" rows="2" name="setting_he_wall_photo_exts">{$setting.setting_he_wall_photo_exts}</textarea>
			</td>
		</tr>
		<tr class="{cycle values=',bg1'}">
			<td class="item"><b>{lang_print id=690706121}</b><br/> {lang_print id=690706122}</td>
		</tr>
		<tr class="{cycle values=',bg1'}">
			<td class="item" style="padding-right: 12px;">
				<textarea style="width: 100%;" class="text" cols="40" rows="2" name="setting_he_wall_photo_mimes">{$setting.setting_he_wall_photo_mimes}</textarea>
			</td>
		</tr>
	</tbody>
	</table>
	<br/>
	
	<table class="he_tbl" cellspacing="0" cellpadding="0" style="width: 500px;">
	<thead>
		<tr>
			<td class="header"><a href="javascript://" title="{lang_print id=690706130}" onclick="$(this).getParent('table').getElement('tbody').toggleClass('display_none')">{lang_print id=690706123}</a></td>
		</tr>
	</thead>
	<tbody {if !$result}class="display_none"{/if}>
		<tr class="{cycle values=',bg1'}">
			<td class="item"><b>{lang_print id=690706124}</b><br/> {lang_print id=690706125}</td>
		</tr>
		<tr class="{cycle values=',bg1'}">
			<td class="item">
				<input type="text" name="setting_he_wall_music_filesize" value="{$setting.setting_he_wall_music_filesize}" size="5" maxlength="6"/> {lang_print id=690706118}
			</td>
		</tr>
		<tr class="{cycle values=',bg1'}">
			<td class="item"><b>{lang_print id=690706126}</b><br/> {lang_print id=690706127}</td>
		</tr>
		<tr class="{cycle values=',bg1'}">
			<td class="item" style="padding-right: 12px;">
				<textarea style="width: 100%;" class="text" cols="40" rows="2" name="setting_he_wall_music_exts">{$setting.setting_he_wall_music_exts}</textarea>
			</td>
		</tr>
		<tr class="{cycle values=',bg1'}">
			<td class="item"><b>{lang_print id=690706128}</b><br/> {lang_print id=690706129}</td>
		</tr>
		<tr class="{cycle values=',bg1'}">
			<td class="item" style="padding-right: 12px;">
				<textarea style="width: 100%;" class="text" cols="40" rows="2" name="setting_he_wall_music_mimes">{$setting.setting_he_wall_music_mimes}</textarea>
			</td>
		</tr>
	</tbody>
	</table>
	<br/>
	
	<input type="hidden" value="dosave" name="task" />
	<input type="submit" value="{lang_print id=173}" class="button" />
</form>

<br/>
<br/>
<h2>{lang_print id=690706103}</h2>

{lang_print id=690706104}<br />


<div class="code_preview">
{ldelim}he_wall_display object='userhome' object_id=$user->user_info.user_id{rdelim}
</div>

<div class="code_preview code_preview_old">
{literal}
<p><span>&#160;&#160;&#160;&#160;&#160;&#160;&#160; </span>{* DISPLAY ACTIONS *}</p>
<p><span>&#160;&#160;&#160;&#160;&#160;&#160;&#160; </span>{section name=actions_loop loop=$actions}</p>
<p><span>&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160; </span>&lt;div id='action_{$actions[actions_loop].action_id}' class='home_action{if $smarty.section.actions_loop.first}_top{/if}'&gt;</p>
<p><span>&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160; </span>&lt;table cellpadding='0' cellspacing='0'&gt;</p>
<p><span>&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160; </span>&lt;tr&gt;</p>
<p><span>&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160; </span>&lt;td valign='top'&gt;</p>
<p><span>&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160; </span>&lt;img src='./images/icons/{$actions[actions_loop].action_icon}' border='0' class='icon' /&gt;</p>
<p><span>&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160; </span>&lt;/td&gt;</p>
<p><span>&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160; </span>&lt;td valign='top' width='100%'&gt;</p>
<p><span>&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160; </span>{assign var='action_date' value=$datetime-&gt;time_since($actions[actions_loop].action_date)}</p>
<p><span>&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160; </span>&lt;div class='home_action_date'&gt;{lang_sprintf id=$action_date[0] 1=$action_date[1]}&lt;/div&gt;</p>
<p><span>&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160; </span>{assign var='action_media' value=''}</p>
<p><span>&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160; </span>{if $actions[actions_loop].action_media !== FALSE}{capture assign='action_media'}{section name=action_media_loop loop=$actions[actions_loop].action_media}&lt;a href='{$actions[actions_loop].action_media[action_media_loop].actionmedia_link}'&gt;&lt;img src='{$actions[actions_loop].action_media[action_media_loop].actionmedia_path}' border='0' width='{$actions[actions_loop].action_media[action_media_loop].actionmedia_width}' class='recentaction_media'&gt;&lt;/a&gt;{/section}{/capture}{/if}</p>
<p><span>&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160; </span>{lang_sprintf assign='action_text' id=$actions[actions_loop].action_text args=$actions[actions_loop].action_vars}</p>
<p><span>&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160; </span>{$action_text|replace:"[media]":$action_media|choptext:50:"&lt;br&gt;"}</p>
<p><span>&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160; </span>&lt;/td&gt;</p>
<p><span>&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160; </span>&lt;/tr&gt;</p>
<p><span>&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160; </span>&lt;/table&gt;</p>
<p><span>&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160; </span>&lt;/div&gt;</p>
<p><span>&#160;&#160;&#160;&#160;&#160;&#160;&#160; </span>{sectionelse}</p>
<p><span>&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160; </span>{lang_print id=738}</p>
<p><span>&#160;&#160;&#160;&#160;&#160;&#160;&#160; </span>{/section}</p>
{/literal}
</div>

{include file='admin_footer.tpl'}