{include file='header.tpl'}

{* $Id: user_editprofile_photo.tpl 130 2009-03-21 23:36:57Z john $ *}

<table class='tabs' cellpadding='0' cellspacing='0'>
<tr>
<td class='tab0'>&nbsp;</td>
{section name=cat_loop loop=$cats}
  <td class='tab2' NOWRAP><a href='user_editprofile.php?cat_id={$cats[cat_loop].subcat_id}'>{lang_print id=$cats[cat_loop].subcat_title}</a></td><td class='tab'>&nbsp;</td>
{/section}
{if $user->level_info.level_photo_allow != 0}<td class='tab1' NOWRAP><a href='user_editprofile_photo.php'>{lang_print id=762}</a></td><td class='tab'>&nbsp;</td>{/if}
{if $user->level_info.level_profile_style != 0 || $user->level_info.level_profile_style_sample != 0}<td class='tab2' NOWRAP><a href='user_editprofile_style.php'>{lang_print id=763}</a></td>{/if}
<td class='tab3'>&nbsp;</td>
</tr>
</table>

<img src='./images/icons/editprofile48.gif' border='0' class='icon_big' />
<div class='page_header'>{lang_print id=769}</div>
<div>{lang_print id=713}</div>
<br />
<br />


{* SHOW ERROR MESSAGE *}
{if $is_error != 0}
  <div class='center'>
    <table cellpadding='0' cellspacing='0'>
      <tr>
        <td class='result'>
          <div class='error'>
            <img src='./images/error.gif' class='icon' border='0' />
            {lang_print id=$is_error}
          </div>
        </td>
      </tr>
    </table>
  </div>
  <br />
{/if}


{* SHOW PHOTO ON LEFT AND UPLOAD FIELD ON RIGHT *}
<table cellpadding='0' cellspacing='0'>
  <tr>
    <td class='editprofile_photoleft'>
      {lang_print id=770}<br />
      <table cellpadding='0' cellspacing='0' width='202'>
        <tr>
          <td class='editprofile_photo'><img id="userEditPhotoImg" src='{$user->user_photo("./images/nophoto.gif")}' border='0' />
        </td>
      </tr>
      </table>
      {* SHOW REMOVE PHOTO LINK IF NECESSARY *}
      {if $user->user_photo() != ""}
      <div id="userEditRemovePhotoLink">[ <a href='javascript:void(0);' onclick='SocialEngine.Viewer.userPhotoRemove(); return false;'>{lang_print id=771}</a> ]</div>
      {/if}
    </td>
    <td class='editprofile_photoright'>
      <form action='user_editprofile_photo.php' method='post' enctype='multipart/form-data'>
      {lang_print id=772}<br />
      <input type='file' class='text' name='photo' size='30' />
      <input type='submit' class='button' value='{lang_print id=714}' />
      <input type='hidden' name='task' value='upload' />
      <input type='hidden' name='MAX_FILE_SIZE' value='5000000' />
      </form>
      <div>{lang_print id=715} {$user->level_info.level_photo_exts}</div>
    </td>
  </tr>
</table>

{include file='footer.tpl'}