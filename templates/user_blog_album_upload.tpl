<html>
<head>

{* $Id: user_blog_album_upload.tpl 5 2009-01-11 06:01:16Z john $ *}

<script type="text/javascript" src="./include/js/mootools12.js"></script>
<script type="text/javascript" src="./include/js/mootools12-more.js"></script>

<link rel="stylesheet" href="./templates/styles.css" title="stylesheet" type="text/css" />
<link rel="stylesheet" href="./templates/styles_global.css" title="stylesheet" type="text/css" />
<link rel="stylesheet" href="./templates/styles_blog_album.css" title="stylesheet" type="text/css" />

{* UPLOAD SUCCESS *}
{if $task=="doupload" && !empty($file_result.file1.media_path)}
  {literal}
  <script type="text/javascript">
  <!--
    window.addEvent('load', function()
    {
      window.parent.OnDialogTabChange('divInfo');
      window.parent.sActualBrowser = '' ;
      window.parent.SetUrl( '{/literal}{$file_result.file1.media_path}{literal}' ) ;
    });
  // -->
  </script>
  {/literal}
{/if}



{literal}
<script type="text/javascript">
<!--
  window.addEvent('load', function()
  {
    // Fix input sizes
    $$('input').each(function(inputElement)
    {
      if( inputElement.type=='file' )
        inputElement.size = 25;
    });
    
    // Add album selector
    var uploadForm = $('uploadForm');
    var albumSelect = $('albumSelectTemplate');
    var albumSelectBox = $('albumSelectBox');
    
    
    {/literal}{if $albums_total>0}{section name=album_loop loop=$albums}{literal}
    (new Element('option', {
      'value' : {/literal}{$albums[album_loop].album_id}{literal},
      'html' : '{/literal}{$albums[album_loop].album_title}{literal}'
    })).inject(albumSelectBox);
    {/literal}{/section}{/if}{literal}
    
    (new Element('option', {
      'value' : 0,
      'html' : '[New Album]'
    })).inject(albumSelectBox);
    
    albumSelect.inject(uploadForm, 'top');
    albumSelect.style.display = '';
    
    $('newAlbumContainer').style.display = ( !$('albumSelectBox').value || $('albumSelectBox').value=='0' ? '' : 'none');
    
    // Modify form
    $('div1').getElement('input').onchange = function()
    {
      $('div_submit').style.display = 'block';
    };
    
    (new Element('br')).inject($('div1'), 'after');
  });
// -->
</script>
{/literal}

<body style="background: transparent;">
<div class="seBlogAlbum">

  <div id="albumSelectTemplate" style="display:none;">
    <table style="width:100%;"><tr><td valign='top'>
      <span id="albumSelectContainer">
        <label>Album:</label><br />
        <select name="album_id" id="albumSelectBox" onchange="$('newAlbumContainer').style.display = ( !this.value || this.value=='0' ? '' : 'none');"></select>
        <br />
        <br />
      </span>
      
    </td><td valign='top'>
      
      <span id="newAlbumContainer">
        <label>New Album Name:</label><br />
        <input type="text" name="album_title" />
        <br />
        <br />
      </span>
    </tr></table>
  </div>


  <div class="seBlogAlbumUploaderDiv">
    {include file='user_upload.tpl' action='user_blog_album_upload.php' session_id=$session_id upload_token=$upload_token show_uploader=$show_uploader inputs=$inputs file_result=$file_result}
  </div>

</div>

</body>
</html>