{include file='header.tpl'}

{* $Id: user_group_edit_settings.tpl 34 2009-01-24 04:17:28Z john $ *}

<table class='tabs' cellpadding='0' cellspacing='0'>
  <tr>
    <td class='tab0'>&nbsp;</td>
    <td class='tab2' NOWRAP><a href='user_group_edit.php?group_id={$group->group_info.group_id}'>{lang_print id=2000097}</a></td><td class='tab'>&nbsp;</td>
    <td class='tab2' NOWRAP><a href='user_group_edit_members.php?group_id={$group->group_info.group_id}'>{lang_print id=2000118}</a></td><td class='tab'>&nbsp;</td>
    <td class='tab1' NOWRAP><a href='user_group_edit_settings.php?group_id={$group->group_info.group_id}'>{lang_print id=2000119}</a></td><td class='tab'>&nbsp;</td>
    <td class='tab3'>&nbsp;</td>
  </tr>
</table>

<table cellpadding='0' cellspacing='0' width='100%'>
<tr>
<td valign='top'>
  <img src='./images/icons/group_edit48.gif' border='0' class='icon_big' />
  {capture assign="linked_groupname"}<a href='{$url->url_create("group", $smarty.const.NULL, $group->group_info.group_id)}'>{$group->group_info.group_title|truncate:30:"...":true}</a>{/capture}
  <div class='page_header'>{lang_sprintf id=2000135 1=$linked_groupname}</div>
  {lang_print id=2000136}
</td>
<td valign='top' align='right'>
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='button'><a href='user_group.php'><img src='./images/icons/back16.gif' border='0' class='button' />{lang_print id=2000120}</a></td></tr>
  </table>
</td>
</tr>
</table>

<br />

{* SHOW SUCCESS MESSAGE *}
{if $result != 0}
  <table cellpadding='0' cellspacing='0'><tr>
  <td class='result'><img src='./images/success.gif' border='0' class='icon' /> {lang_print id=191}</div></td>
  </tr></table>
  <br>
{/if}

<form action='user_group_edit_settings.php' method='post'>

{if $group->groupowner_level_info.level_group_style == 1}
  <div><b>{lang_print id=2000137}</b></div>
  <div class='form_desc'>{lang_print id=2000138}</div>
  <textarea name='style_group' rows='17' cols='50' style='width: 100%; font-family: courier, serif;'>{$style_group}</textarea>
  <br><br>
{/if}


{* SHOW NEW MEMBER APPROVAL SETTING IF ALLOWED BY ADMIN *}
{if $group->groupowner_level_info.level_group_approval == 1}
  <div><b>{lang_print id=2000139}</b></div>
  <div class='form_desc'>{lang_sprintf id=2000140 1=$group->group_info.group_id}{if $group->group_info.group_approval == 1} {lang_print id=2000141}{/if}</div>
  <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='radio' name='group_approval' id='group_approval0' value='0'{if $group->group_info.group_approval == 0} CHECKED{/if}></td><td><label for='group_approval0'>{lang_print id=2000142}</label></td></tr>
    <tr><td><input type='radio' name='group_approval' id='group_approval1' value='1'{if $group->group_info.group_approval == 1} CHECKED{/if}></td><td><label for='group_approval1'>{lang_print id=2000100}</label></td></tr>
  </table>
  <br><br>
{/if}

{* SHOW SEARCH PRIVACY OPTIONS IF ALLOWED BY ADMIN *}
{if $group->groupowner_level_info.level_group_search == 1}
  <div><b>{lang_print id=2000143}</b></div>
  <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='radio' name='group_search' id='group_search_1' value='1'{if $group->group_info.group_search == 1} CHECKED{/if}></td><td><label for='group_search_1'>{lang_print id=2000144}</label></td></tr>
    <tr><td><input type='radio' name='group_search' id='group_search_0' value='0'{if $group->group_info.group_search == 0} CHECKED{/if}></td><td><label for='group_search_0'>{lang_print id=2000145}</label></td></tr>
  </table>
  <br><br>
{/if}

{* SHOW USER INVITATION OPTION *}
<div><b>{lang_print id=2000216}</b></div>
<table cellpadding='0' cellspacing='0'>
  <tr><td><input type='radio' name='group_invite' id='group_invite_1' value='1'{if $group->group_info.group_invite == 1} CHECKED{/if}></td><td><label for='group_invite_1'>{lang_print id=2000217}</label></td></tr>
  <tr><td><input type='radio' name='group_invite' id='group_invite_0' value='0'{if $group->group_info.group_invite == 0} CHECKED{/if}></td><td><label for='group_invite_0'>{lang_print id=2000218}</label></td></tr>
</table>
<br><br>

{* SHOW ALLOW PRIVACY SETTINGS *}
{if $privacy_options|@count > 1}
  <div><b>{lang_print id=2000146}</b></div>
  <div class='form_desc'>{lang_print id=2000147}</div>
  <table cellpadding='0' cellspacing='0'>
  {foreach from=$privacy_options name=privacy_loop key=k item=v}
    <tr>
    <td><input type='radio' name='group_privacy' id='privacy_{$k}' value='{$k}'{if $group->group_info.group_privacy == $k} checked='checked'{/if}></td>
    <td><label for='privacy_{$k}'>{lang_print id=$v}</label></td>
    </tr>
  {/foreach}
  </table>
  <br><br>
{/if}

{* SHOW ALLOW COMMENT SETTINGS *}
{if $comment_options|@count > 1}
  <div><b>{lang_print id=2000148}</b></div>
  <div class='form_desc'>{lang_print id=2000149}</div>
  <table cellpadding='0' cellspacing='0'>
  {foreach from=$comment_options name=comment_loop key=k item=v}
    <tr>
    <td><input type='radio' name='group_comments' id='comment_{$k}' value='{$k}'{if $group->group_info.group_comments == $k} checked='checked'{/if}></td>
    <td><label for='comment_{$k}'>{lang_print id=$v}</label></td>
    </tr>
  {/foreach}
  </table>
  <br><br>
{/if}

{* SHOW ALLOW DISCUSSION SETTINGS *}
{if $discussion_options|@count > 1}
  <div><b>{lang_print id=2000150}</b></div>
  <div class='form_desc'>{lang_print id=2000151}</div>
  <table cellpadding='0' cellspacing='0'>
  {foreach from=$discussion_options name=discussion_loop key=k item=v}
    <tr>
    <td><input type='radio' name='group_discussion' id='discussion_{$k}' value='{$k}'{if $group->group_info.group_discussion == $k} checked='checked'{/if}></td>
    <td><label for='discussion_{$k}'>{lang_print id=$v}</label></td>
    </tr>
  {/foreach}
  </table>
  <br><br>
{/if}

{* SHOW ALLOW UPLOAD SETTINGS *}
{if $upload_options|@count > 1}
  <div><b>{lang_print id=2000212}</b></div>
  <div class='form_desc'>{lang_print id=2000213}</div>
  <table cellpadding='0' cellspacing='0'>
  {foreach from=$upload_options name=upload_loop key=k item=v}
    <tr>
    <td><input type='radio' name='group_upload' id='upload_{$k}' value='{$k}'{if $group->group_info.group_upload == $k} checked='checked'{/if}></td>
    <td><label for='upload_{$k}'>{lang_print id=$v}</label></td>
    </tr>
  {/foreach}
  </table>
  <br><br>
{/if}

{* SHOW ALLOW TAG SETTINGS *}
{if $tag_options|@count > 1}
  <div><b>{lang_print id=2000214}</b></div>
  <div class='form_desc'>{lang_print id=2000215}</div>
  <table cellpadding='0' cellspacing='0'>
  {foreach from=$tag_options name=tag_loop key=k item=v}
    <tr>
    <td><input type='radio' name='groupalbum_tag' id='tag_{$k}' value='{$k}'{if $groupalbum_info.groupalbum_tag == $k} checked='checked'{/if}></td>
    <td><label for='tag_{$k}'>{lang_print id=$v}</label></td>
    </tr>
  {/foreach}
  </table>
  <br><br>
{/if}

<input type='submit' class='button' value='{lang_print id=173}'>
<input type='hidden' name='group_id' value='{$group->group_info.group_id}'>
<input type='hidden' name='task' value='dosave'>
</form>


{include file='footer.tpl'}