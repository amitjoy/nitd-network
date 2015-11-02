<html>
<head>

{* $Id: user_blog_album_browse.tpl 20 2009-01-15 04:04:40Z john $ *}

<script type="text/javascript" src="./include/js/mootools12.js"></script>
<script type="text/javascript" src="./include/js/mootools12-more.js"></script>

<link rel="stylesheet" href="./templates/styles.css" title="stylesheet" type="text/css" />
<link rel="stylesheet" href="./templates/styles_global.css" title="stylesheet" type="text/css" />
<link rel="stylesheet" href="./templates/styles_blog_album.css" title="stylesheet" type="text/css" />

{literal}
<script type="text/javascript">

  var base_path = '{/literal}{$url->url_base|escape:"quotes"}{literal}';
  var user_path = '{/literal}{$url->url_userdir($user->user_info.user_id)|escape:"quotes"|replace:"./":""}{literal}';
  
  // Not working at all
  //user_path.replace(/^\.\//, '');
  
  
  function album_show(album_id)
  {
    var currentElement = $('seBlogAlbum_album_' + album_id);
    var currentMode = ( currentElement.style.display=="none" ? 'block' : 'none' );
    
    $$('.seBlogAlbum_album').each(function(eachElement)
    {
      if( eachElement.id==currentElement.id )
        eachElement.style.display = currentMode;
      else
        eachElement.style.display = 'none';
    });
  }
  
  function show_image(media_id)
  {
    var currentElement = $('seBlogAlbum_albumMedia_image_' + media_id);
    var currentMode = ( currentElement.style.display=="none" ? 'block' : 'none' );
    
    $$('.seBlogAlbum_albumMedia_image').each(function(eachElement)
    {
      if( eachElement.id==currentElement.id )
        eachElement.style.display = currentMode;
      else
        eachElement.style.display = 'none';
    });
  }
  
  function add_image(file)
  {
    window.parent.OnDialogTabChange('divInfo');
    window.parent.sActualBrowser = '' ;
    window.parent.SetUrl( base_path + user_path + file ) ;
  }

</script>
{/literal}

</head>
<body style="background: transparent;">

<div class="seBlogAlbum">


  {capture name=user_dir}{$url->url_base}{$url->url_userdir($user->user_info.user_id)}{/capture}


  {* ALBUMS *}
  {if empty($album_id)}

    <div class="seBlogAlbumHeadline"><a href="user_blog_album_browse.php">My Albums</a></div>
    <br />
    
    <table>
    {section name=album_loop loop=$albums}
    
      {* SET ALBUM COVER *}
      {if $albums[album_loop].album_cover_id == 0}
        {assign var='album_cover_src' value='./images/icons/folder_big.gif'}
      {else}
        {* IF IMAGE, GET THUMBNAIL *}
        {if $albums[album_loop].album_cover_ext == "jpeg" OR $albums[album_loop].album_cover_ext == "jpg" OR $albums[album_loop].album_cover_ext == "gif" OR $albums[album_loop].album_cover_ext == "png" OR $albums[album_loop].album_cover_ext == "bmp"}
          {assign var='album_cover_dir' value=$url->url_userdir($user->user_info.user_id)}
          {assign var='album_cover_src' value="`$album_cover_dir``$albums[album_loop].album_cover_id`_thumb.jpg"}
        {* SET THUMB PATH FOR AUDIO *}
        {elseif $albums[album_loop].album_cover_ext == "mp3" OR $albums[album_loop].album_cover_ext == "mp4" OR $albums[album_loop].album_cover_ext == "wav"}
          {assign var='album_cover_src' value='./images/icons/album_audio_big.gif'}
        {* SET THUMB PATH FOR VIDEO *}
        {elseif $albums[album_loop].album_cover_ext == "mpeg" OR $albums[album_loop].album_cover_ext == "mpg" OR $albums[album_loop].album_cover_ext == "mpa" OR $albums[album_loop].album_cover_ext == "avi" OR $albums[album_loop].album_cover_ext == "swf" OR $albums[album_loop].album_cover_ext == "mov" OR $albums[album_loop].album_cover_ext == "ram" OR $albums[album_loop].album_cover_ext == "rm"}
          {assign var='album_cover_src' value='./images/icons/album_video_big.gif'}
        {* SET THUMB PATH FOR UNKNOWN *}
        {else}
          {assign var='album_cover_src' value='./images/icons/album_file_big.gif'}
        {/if}
      {/if}

      {* CHECK IF ALBUM IS UNTITLED *}
      {if $albums[album_loop].album_title != ""}
        {assign var="album_title" value=$albums[album_loop].album_title|truncate:30:"...":true}
      {else}
        {assign var="album_title" value=$user_album11}
      {/if}
      
      
      <tr>
        <td>
          <a href="user_blog_album_browse.php?album_id={$albums[album_loop].album_id}">
            <img src='{$album_cover_src}' border='0' width='{$misc->photo_size($album_cover_src,"75","75","w")}'>
          </a>
        </td>
        <td style="vertical-align: top; padding-top: 2px; padding-left: 5px;">
          <a href="user_blog_album_browse.php?album_id={$albums[album_loop].album_id}" valign="top" style="padding-top: 2px;">
            {$album_title}
          </a>
        </td>
      </tr>
      
    {/section}
    </table>



  {* MEDIA *}
  {elseif !empty($album_id) && $media_total>0}

    <div class="seBlogAlbumHeadline"><a href="user_blog_album_browse.php">My Albums</a> &gt;&gt;&gt; {$album_info.album_title}</div>
    <br />
    
    <table>
    {section name=media_loop loop=$media}
      
      {* IF IMAGE, GET THUMBNAIL *}
      {if $media[media_loop].media_ext == "jpeg" OR $media[media_loop].media_ext == "jpg" OR $media[media_loop].media_ext == "gif" OR $media[media_loop].media_ext == "png" OR $media[media_loop].media_ext == "bmp"}
        {assign var='file_dir' value=$url->url_userdir($user->user_info.user_id)}
        {assign var='file_thumb_src' value="`$file_dir``$media[media_loop].media_id`_thumb.jpg"}
        {assign var='file_name_full' value="`$media[media_loop].media_id`.jpg"}
        
        <tr>
          <td>
            <a href="javascript:void(0);" onclick="add_image('{$file_name_full|escape:quotes}');">
              <img src='{$file_thumb_src}' border='0' width='{$misc->photo_size($file_thumb_src,"75","75","w")}'>
            </a>
          </td>
          <td>
            <a href="javascript:void(0);" onclick="add_image('{$file_name_full|escape:quotes}');" valign="top" style="padding-top: 2px;">
              {$files[files_loop].media_title}
            </a>
          </td>
        </tr>
      {/if}
      
    {/section}
    </table>

  {else}

    No media

  {/if}

</div>

</body>
</html>