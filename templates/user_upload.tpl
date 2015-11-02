
{* $Id: user_upload.tpl 204 2009-07-29 00:51:39Z john $ *}

{* JAVASCRIPT FOR FILE UPLOADER *}
<script type='text/javascript' src='./include/uploader/Swiff.Uploader.js'></script>
<script type='text/javascript' src='./include/uploader/Fx.ProgressBar.js'></script>
<script type='text/javascript' src='./include/uploader/FancyUpload2.js'></script>

{assign var=user_upload_field_name value=$user_upload_field_name|default:file1}
{assign var=user_upload_max_files value=$user_upload_max_files|default:10}
{assign var=user_upload_max_size value=$user_upload_max_size|default:'false'}
{assign var=user_upload_allowed_extensions value=$user_upload_allowed_extensions|default:$smarty.const.NULL}

{lang_javascript ids=1190,1194,1229,1230,1231,1232}

{literal}
<script type="text/javascript">
<!--
  {/literal}{if $show_uploader}{literal}
  
  window.addEvent('domready', function()
  {
    {/literal}
    {if empty($user_upload_allowed_extensions)}
    var allowed_extensions = false;
    var allowed_extensions_array = false;
    {else}
    var allowed_extensions = '{$user_upload_allowed_extensions}';
    var allowed_extensions_array = allowed_extensions.split(',');
    {/if}
    {literal}
    
    var customValidationHandler = function(file, errors)
    {
      // No extensions are set, allow all
      if( !allowed_extensions || !allowed_extensions_array )
        return true;
      
      var fileParts = file.name.split('.');
      var fileExtension = ( fileParts ? fileParts[fileParts.length-1] : false );
      
      // Could not get extension, should we return true or false?
      if( !fileExtension || $type(fileExtension)!="string" )
        return true;
      
      fileExtension = fileExtension.toLowerCase();
      
      // File extension not in the list of allowed extensions
      if( !allowed_extensions_array.contains(fileExtension) )
        return false;
      
      return true;
    }
    
    var invalidFileHandler = function(file, errors)
    {
      var msg;
      if( errors.contains('size') )
      {
        msg = SocialEngine.Language.TranslateFormatted(1229, [swiffy.options.limitSize, file.name]);
      }
      else if( errors.contains('length') )
      {
        msg = SocialEngine.Language.TranslateFormatted(1230, [swiffy.options.limitFiles]);
      }
      else if( errors.contains('custom') )
      {
        msg = SocialEngine.Language.TranslateFormatted(1232, [allowed_extensions_array.join(', '), file.name]);
      }
      else
      {
        msg = SocialEngine.Language.Translate(1231);
      }
      alert(msg);
    }
    
    var postData = {'isAjax':1, 'upload_token':'{/literal}{$upload_token}{literal}'};
    $('uploadForm').getElements('input[type=hidden]').each(function(el) { postData[el.get('name')] = el.get('value');});

    var swiffy = new FancyUpload2($('uploader'), $('fileList'),
    {
      'url': '{/literal}{$url->url_base}{literal}user_upload.php?session_id={/literal}{$session_id}{literal}',
      'fieldName': '{/literal}{$user_upload_field_name}{literal}',
      'data': postData,
      'limitSize': {/literal}{$user_upload_max_size}{literal},
      'limitFiles': {/literal}{$user_upload_max_files}{literal},
      'path': './include/uploader/Swiff.Uploader.swf',
      'onLoad': function()
      {
        $('uploader').style.display = 'block';
        $('fallback_link').style.display = 'block';
        $('fallback').style.display = 'none';
      },
      'target': 'uploader_browse',
      'fileInvalid' : invalidFileHandler,
      'validateFile' : customValidationHandler
    });
 
    $('uploader_browse').addEvent('click', function()
    {
      swiffy.browse();
      return false;
    });
 
    $('uploader_upload').addEvent('click', function()
    {
      if(swiffy.files.length == 0) {
        alert(SocialEngine.Language.Translate(1194));
      } else {
        swiffy.upload();
      }
      return false;
    });

    $('fallback_link').addEvent('click', function()
    {
      $('fallback').style.display='block';
      $('fallback_link').style.display='none';
      $('uploader').style.display='none';
      return false;
    });
  });
  {/literal}{/if}{literal}

  function startStatus()
  {
    $('fallback_submit').disabled = true;
    $('fallback_status').value = SocialEngine.Language.Translate(1190);
    window.setTimeout("goStatus()", 400);
  }
  
  function goStatus()
  {
    $('fallback_status').value = $('fallback_status').value + '.';
    if($('fallback_status').value == SocialEngine.Language.Translate(1190)+'....') { $('fallback_status').value = SocialEngine.Language.Translate(1190); }
    window.setTimeout("goStatus()", 400);
  }
// -->
</script>
{/literal}


<div id='uploader' style='display: none;'>
  <div style='margin-bottom: 10px;'>
    <div style='float: left; font-weight: bold;'>
      <a href='javascript:void(0);' id='uploader_browse' onClick='this.blur()'><img src='./images/uploader_browse.gif' border='0' class='button'>{lang_print id=1191}</a>
    </div>
    <div style='float: left; padding-left: 20px; font-weight: bold;'>
      <a href='javascript:void(0);' id='uploader_upload' onClick='this.blur()'><img src='./images/uploader_upload.gif' border='0' class='button'>{lang_print id=1189}</a>
    </div>
    <div style='height: 0px; clear: both;'></div>
  </div>
  <div>
    <div>{lang_print id=1192} <span class='overall-title'></span></div>
    <img src='./images/uploader_bar.gif' class='progress overall-progress' />
  </div>
  <div style='margin-top: 5px;'>
    <div>{lang_print id=1193} <span class='current-title'></span></div>
    <img src='./images/uploader_bar.gif' class='progress current-progress' />
  </div>
  <div class='uploading-text' style='display: none;'>{lang_sprintf id=1195 1="<span class='current-rate'></span>" 2="<span class='current-timeleft'></span>"}</div>
  <div class='uploaded-text' style='display: none;'>{lang_print id=1196}</div>
  <ul id='fileList'></ul>
</div>
 

<br />
<div id='fallback_link' class='fallback_link' style='display: none;'><a href='javascript:void(0)'>{lang_print id=1183}</a></div>

<div id='fallback'>

  {* SHOW IMAGES UPLOADED MESSAGE *}
  {if $file_result|@count != 0}
    <br />
    <table cellpadding='0' cellspacing='0'>
      <tr>
        <td class='result'>
          <div class='success' style='text-align: left;'> 
            {foreach from=$file_result name=result_loop key=k item=v}
              <div>{lang_sprintf id=$v.message 1=$v.file_name}</div>
            {/foreach}
          </div>
        </td>
      </tr>
    </table>
  {/if}

  <form action='{$action}' method='POST' enctype='multipart/form-data' id='uploadForm' onSubmit='startStatus()'>

    <div id='div1'>
      <table cellpadding='0' cellspacing='0' class='form'>
      <tr>
      <td class='form1' width='65'>{lang_print id=1184}</td>
      <td class='form2'><input type='file' name='file1' size='60' class='text' onchange="{if $user_upload_max_files>1}$('div2').style.display = 'block';{/if}$('div_submit').style.display = 'block';"></td>
      </tr>
      </table>
    </div>

    <div id='div2' style='display: none;'>
      <table cellpadding='0' cellspacing='0' class='form'>
      <tr>
      <td class='form1' width='65'>{lang_print id=1185}</td>
      <td class='form2'><input type='file' name='file2' size='60' class='text'{if $user_upload_max_files>2} onchange="$('div3').style.display = 'block';"{/if}></td>
      </tr>
      </table>
    </div>

    <div id='div3' style='display: none;'>
      <table cellpadding='0' cellspacing='0' class='form'>
      <tr>
      <td class='form1' width='65'>{lang_print id=1186}</td>
      <td class='form2'><input type='file' name='file3' size='60' class='text'{if $user_upload_max_files>3} onchange="$('div4').style.display = 'block';"{/if}></td>
      </tr>
      </table>
    </div>

    <div id='div4' style='display: none;'>
      <table cellpadding='0' cellspacing='0' class='form'>
      <tr>
      <td class='form1' width='65'>{lang_print id=1187}</td>
      <td class='form2'><input type='file' name='file4' size='60' class='text'{if $user_upload_max_files>4} onchange="$('div5').style.display = 'block';"{/if}></td>
      </tr>
      </table>
    </div>

    <div id='div5' style='display: none;'>
      <table cellpadding='0' cellspacing='0' class='form'>
      <tr>
      <td class='form1' width='65'>{lang_print id=1188}</td>
      <td class='form2'><input type='file' name='file5' size='60' class='text'></td>
      </tr>
      </table>
    </div>

    <div id='div_submit' style='display: none;'>
      <table cellpadding='0' cellspacing='0'>
      <tr>
      <td class='form1' width='65'>&nbsp;</td>
      <td class='form1'>
        <input type='submit' class='button' name='submit' value='{lang_print id=1189}' id='fallback_submit'>&nbsp;
        <input type='hidden' name='task' value='doupload'>
        <input type='hidden' name='MAX_FILE_SIZE' value='{$user_upload_max_size|default:5000000}'>
	{foreach from=$inputs name=input_loop key=k item=v}
	  <input type='hidden' name='{$k}' value='{$v}'>
        {/foreach}
      </td>
      <td class='form2'>
        &nbsp;<input type='text' class='fallback_status' name='status' id='fallback_status' readonly='readonly'>
      </td>
      </tr>
      </table>
    </div>

  </form>
</div>