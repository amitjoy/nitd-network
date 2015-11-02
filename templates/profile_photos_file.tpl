{include file='header.tpl'}

{* $Id: profile_photos_file.tpl 235 2009-11-13 04:30:39Z phil $ *}

<div style='width: 532px; margin-left: auto; margin-right: auto;'>

<div class='page_header'>
  {lang_sprintf id=1205 1=$url->url_create('profile', $page_owner->user_info.user_username) 2=$page_owner->user_displayname}
</div>


{* SET CORRECT IMAGE OWNER *}
<script type="text/javascript">

</script>



{* ASSIGN INDICES *}
{assign var="current_index" value="`$media_info.type``$media_info.media_id`"|array_search:$media_keys}
{capture assign="previous_index"}{if $current_index == 0}{math equation="x-1" x=$media|@count}{else}{math equation="x-1" x=$current_index}{/if}{/capture}
{capture assign="next_index"}{if $current_index+1 == $media|@count}0{else}{math equation="x+1" x=$current_index}{/if}{/capture}
{capture assign="current_num"}{math equation="x+1" x=$current_index}{/capture}


<br>

{* SHOW PAGE NAVIGATION *}
<div style='margin-bottom: 6px;'>
  <table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
  <td>
    {lang_sprintf id=1207 1=$current_num 2=$media|@count 3="profile_photos.php?user=`$page_owner->user_info.user_username`" 4=$page_owner->user_displayname_short 5=$url->url_create('profile', $page_owner->user_info.user_username)}
  </td>
  <td style='text-align: right;'>
    {capture assign="prev_media_id"}{$media_keys.$previous_index}{/capture}
    <a href='profile_photos_file.php?user={$page_owner->user_info.user_username}&type={$media[$prev_media_id].type}&media_id={$media[$prev_media_id].media_id}'>{lang_print id=1208}</a>
    &nbsp;&nbsp;&nbsp;
    {capture assign="next_media_id"}{$media_keys.$next_index}{/capture}
    <a href='profile_photos_file.php?user={$page_owner->user_info.user_username}&type={$media[$next_media_id].type}&media_id={$media[$next_media_id].media_id}'>{lang_print id=1209}</a>
  </td>
  </tr>
  </table>
</div>

{* SHOW IMAGE *}
<div class='media'>
  <table cellpadding='0' cellspacing='0' align='center'>
  <tr>
  <td style='text-align: center;'>

    {* CREATE WRAPPER DIV *}
    <div id='media_photo_div' class='media_photo_div' style='width:{$media_info.media_width}px;height:{$media_info.media_height}px;'>

      {* DISPLAY IMAGE *}
      <img src='{$media_info.media_dir}{$media_info.media_id}.{$media_info.media_ext}' id='media_photo' border='0'>

    </div>

    {* SHOW DIV WITH TITLE, DESC, TAGS, ETC *}
    <div class='media_caption' style='width: {if $media_info.media_width > 300}{$media_info.media_width}{else}300{/if}px;'>
      {if $media_info.media_title != ""}<div class='media_title'>{$media_info.media_title}</div>{/if}
      {if $media_info.media_desc != ""}<div>{$media_info.media_desc}</div>{/if}
      {capture assign='parent_link'}{$media_info.media_parent_url|replace:"[media_parent_id]":$media_info.media_parent_id}{/capture}
      <div>{if $media_info.user_id != 0}{lang_sprintf id=1216 1=$parent_link 2=$media_info.media_parent_title 3=$url->url_create("profile", $media_info.user->user_info.user_username) 4=$media_info.user->user_displayname}{else}{lang_sprintf id=1217 1=$parent_link 2=$media_info.media_parent_title}{/if}</div>
      <div id='media_tags' style='display: none; margin-top: 10px;'>{lang_print id=1218}</div>
      {if $allowed_to_tag}
        <a href='javascript:void(0);' onClick="SocialEngine.MediaTag.addTag();">{lang_print id=1212}</a>
      {/if}
      <div class='media_date'>
        {assign var="uploaddate" value=$datetime->time_since($media_info.media_date)}{capture assign="uploaddate_text"}{lang_sprintf id=$uploaddate[0] 1=$uploaddate[1]}{/capture}
        {lang_sprintf id=1219 1=$uploaddate_text} 
        -
        <a href="javascript:TB_show('{lang_print id=1220}', '#TB_inline?height=400&width=400&inlineId=sharethis', '', '../images/trans.gif');">{lang_print id=1220}</a>
        -
        <a href="javascript:TB_show('{lang_print id=1221}', 'user_report.php?return_url={$url->url_current()|escape:url}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');">{lang_print id=1221}</a>
      </div>
    </div>
  </td>
  </tr>
  </table>
</div>


{* DIV FOR SHARE THIS WINDOW *}
<div style='display: none;' id='sharethis'>
  <div style='margin: 10px 0px 10px 0px;'>{lang_print id=1222}</div>
  <div style='margin: 10px 0px 10px 0px; font-weight: bold;'>{lang_print id=1223}</div>
  <textarea readonly='readonly' onClick='this.select()' class='text' rows='2' cols='30' style='width: 95%; font-size: 9px;'>{$url->url_base}{$media_info.media_dir|replace:"./":""}{$media_info.media_id}.{$media_info.media_ext}</textarea>
  <div style='margin: 10px 0px 10px 0px; font-weight: bold;'>{lang_print id=1224}</div>
  <textarea readonly='readonly' onClick='this.select()' class='text' rows='2' cols='30' style='width: 95%; font-size: 9px;'><a href='{$url->url_base}{$media_info.media_dir|replace:"./":""}{$media_info.media_id}.{$media_info.media_ext}'><img src='{$url->url_base}{$media_info.media_dir|replace:"./":""}{$media_info.media_id}.{$media_info.media_ext}' border='0'></a></textarea>
  <div style='margin: 10px 0px 10px 0px; font-weight: bold;'>{lang_print id=1225}</div>
  <textarea readonly='readonly' onClick='this.select()' class='text' rows='2' cols='30' style='width: 95%; font-size: 9px;'><a href='{$url->url_base}{$media_info.media_dir|replace:"./":""}{$media_info.media_id}.{$media_info.media_ext}'>{if $media_info.media_title != ""}{$media_info.media_title}{else}{lang_print id=589}{/if}</a></textarea>
  <div style='margin: 10px 0px 10px 0px; font-weight: bold;'>{lang_print id=1226}</div>
  <textarea readonly='readonly' onClick='this.select()' class='text' rows='2' cols='30' style='width: 95%; font-size: 9px;'>[url={$url->url_base}{$media_info.media_dir|replace:"./":""}{$media_info.media_id}.{$media_info.media_ext}][img]{$url->url_base}{$media_info.media_dir|replace:"./":""}{$media_info.media_id}.{$media_info.media_ext}[/img][/url]</textarea>
  <div style='margin-top: 10px;'>
    <input type='button' class='button' value='{lang_print id=1227}' onClick='parent.TB_remove();'>
  </div>
</div>


{* TAGGING *}
{lang_javascript ids=39,1212,1213,1214,1215,1228}
      
<script type="text/javascript">
        
  SocialEngine.MediaTag = new SocialEngineAPI.Tags({ldelim}
      'canTag' : {if $allowed_to_tag}true{else}false{/if},

      'type' : '{$media_info.type_prefix}',
      'media_id' : {$media_info.media_id},
      'media_dir' : '{$media_info.media_dir}',

      'object_owner' : {if $media_info.user_id != 0}false{else}'{$media_info.type_prefix}'{/if},
      'object_owner_id' : {if $media_info.user_id != 0}false{else}{$media_info.media_parent_id}{/if}

    {rdelim});
        
    SocialEngine.RegisterModule(SocialEngine.MediaTag);
       
    {section name=tag_loop loop=$tags}
      insertTag('{$tags[tag_loop].mediatag_id}', '{if $tags[tag_loop].tagged_user->user_exists}{$url->url_create("profile", $tags[tag_loop].tagged_user->user_info.user_username)}{/if}', '{if $tags[tag_loop].tag_user->user_exists}{$tags[tag_loop].tagged_user->user_displayname}{else}{$tags[tag_loop].mediatag_text}{/if}', '{$tags[tag_loop].mediatag_x}', '{$tags[tag_loop].mediatag_y}', '{$tags[tag_loop].mediatag_width}', '{$tags[tag_loop].mediatag_height}', '{$tags[tag_loop].tagged_user->user_info.user_username}')
    {/section}

    // Backwards
    function insertTag(tag_id, tag_link, tag_text, tag_x, tag_y, tag_width, tag_height, tagged_user)
    {ldelim}
      SocialEngine.MediaTag.insertTag(tag_id, tag_link, tag_text, tag_x, tag_y, tag_width, tag_height, tagged_user);
    {rdelim}

  </script>

</div>


{* SHOW CAROUSEL *}
<table cellpadding='0' cellspacing='0' align='center' style='margin-top: 20px;'>
<tr>
<td><a href='javascript:void(0);' onClick='moveLeft();this.blur()'><img src='./images/icons/media_moveleft.gif' border='0' onMouseOver="this.src='./images/icons/media_moveleft2.gif';" onMouseOut="this.src='./images/icons/media_moveleft.gif';"></a></td>
<td>

  <div id='album_carousel' style='width: 562px; margin: 0px 5px 0px 5px; text-align: center; overflow: hidden;'>

    <table cellpadding='0' cellspacing='0'>
    <tr>
    <td id='thumb-2' style='padding: 0px 5px 0px 5px;'><img src='./images/media_placeholder.gif' border='0' width='70'></td>
    <td id='thumb-1' style='padding: 0px 5px 0px 5px;'><img src='./images/media_placeholder.gif' border='0' width='70'></td>
    <td id='thumb0' style='padding: 0px 5px 0px 5px;'><img src='./images/media_placeholder.gif' border='0' width='70'></td>
    {foreach name=media_loop from=$media key=k item=v}

      {* IF IMAGE, GET THUMBNAIL *}
      {if $v.media_ext == "jpeg" || $v.media_ext == "jpg" || $v.media_ext == "gif" || $v.media_ext == "png" || $v.media_ext == "bmp"}
        {assign var='file_src' value="`$v.media_dir``$v.media_id`_thumb.jpg"}
      {* SET THUMB PATH FOR UNKNOWN *}
      {else}
        {assign var='file_src' value='./images/icons/file_big.gif'}
      {/if}

      {* SHOW THUMBNAILS *}
      <td id='thumb{$smarty.foreach.media_loop.iteration}' class='carousel_item{if $v.media_id == $media_info.media_id && $v.type == $media_info.type}_active{/if}'><a href='profile_photos_file.php?user={$page_owner->user_info.user_username}&type={$v.type}&media_id={$v.media_id}'><img src='{$file_src}' border='0' width='{$misc->photo_size($file_src,'70','70','w')}' onClick='this.blur()'></a></td>

    {/foreach}
    <td id='thumb{math equation="x+1" x=$media|@count}' class='carousel_item'><img src='./images/media_placeholder.gif' border='0' width='70'></td>
    <td id='thumb{math equation="x+2" x=$media|@count}' class='carousel_item'><img src='./images/media_placeholder.gif' border='0' width='70'></td>
    <td id='thumb{math equation="x+3" x=$media|@count}' class='carousel_item'><img src='./images/media_placeholder.gif' border='0' width='70'></td>
    </tr>
    </table>

  </div>

</td>
<td><a href='javascript:void(0);' onClick='moveRight();this.blur()'><img src='./images/icons/media_moveright.gif' border='0' onMouseOver="this.src='./images/icons/media_moveright2.gif';" onMouseOut="this.src='./images/icons/media_moveright.gif';"></a></td>
</tr>
</table>


<div style='width: {$menu_width}px; margin-left: auto; margin-right: auto;'>


{* JAVASCRIPT FOR CAROUSEL *}
{literal}
<script type='text/javascript'>
<!--

  var visiblePhotos = 7;
  var current_id = 0;
  var myFx;

  window.addEvent('domready', function() {
    myFx = new Fx.Scroll('album_carousel');
    current_id = parseInt({/literal}{math equation="x-2" x=$current_index}{literal});
    var position = $('thumb'+current_id).getPosition($('album_carousel'));
    myFx.set(position.x, position.y);
  });


  function moveLeft() {
    if($('thumb'+(current_id-1))) {
      myFx.toElement('thumb'+(current_id-1));
      myFx.toLeft();
      current_id = parseInt(current_id-1);
    }
  }

  function moveRight() {
    if($('thumb'+(current_id+visiblePhotos))) {
      myFx.toElement('thumb'+(current_id+1));
      myFx.toRight();
      current_id = parseInt(current_id+1);
    }
  }

//-->
</script>
{/literal}

<br>


{* DISPLAY POST COMMENT BOX *}
<div style='margin-left: auto; margin-right: auto;'>

  {* COMMENTS *}
  <div id="{$media_info.type_prefix}media_{$media_info.media_id}_postcomment"></div>
  <div id="{$media_info.type_prefix}media_{$media_info.media_id}_comments" style='margin-left: auto; margin-right: auto;'></div>
      
  {lang_javascript ids=39,155,175,182,183,184,185,187,784,787,829,830,831,832,833,834,835,854,856,891,1025,1026,1032,1034,1071}
      
  <script type="text/javascript">
        
    SocialEngine.MediaComments = new SocialEngineAPI.Comments({ldelim}
      'canComment' : {if $allowed_to_comment}true{else}false{/if},
      'commentHTML' : '{$setting.setting_comment_html|replace:",":", "}',
      'commentCode' : {if $setting.setting_comment_code}true{else}false{/if},

      'type' : '{$media_info.type_prefix}media',
      'typeIdentifier' : '{$media_info.type_id}',
      'typeID' : {$media_info.media_id},
          
      'typeTab' : '{$media_info.type_prefix}media',
      'typeCol' : '{$media_info.type_prefix}media',
      'typeTabParent' : '{$media_info.type_prefix}albums',
      'typeColParent' : '{$media_info.type_prefix}album',
      'typeChild' : true,

      'object_owner' : {if $media_info.user_id != 0}false{else}'{$media_info.type_prefix}'{/if},
      'object_owner_id' : {if $media_info.user_id != 0}false{else}{$media_info.media_parent_id}{/if},
          
      'initialTotal' : {$total_comments|default:0}
    {rdelim});
        
    SocialEngine.RegisterModule(SocialEngine.MediaComments);
       
    // Backwards
    function addComment(is_error, comment_body, comment_date)
    {ldelim}
      SocialEngine.MediaComments.addComment(is_error, comment_body, comment_date);
    {rdelim}
        
    function getComments(direction)
    {ldelim}
      SocialEngine.MediaComments.getComments(direction);
    {rdelim}

  </script>

</div>




</div>



{include file='footer.tpl'}