{include file='header_global.tpl'}

{* $Id: user_group_upload.tpl 10 2009-01-11 06:03:42Z john $ *}


{* UPDATE PHOTOS ON TAB IF SUCCESSFUL *}
{literal}
<script type="text/javascript">
<!-- 
  if(window.parent.getFiles) {
    $(window.parent.document.getElementById('TB_overlay')).addEvent('click', function() { window.parent.getFiles(1); });
    $(window.parent.document.getElementById('TB_closeWindowButton')).addEvent('click', function() { window.parent.getFiles(1); });
    $(window.parent.document).addEvent('keyup', function(event) { if(event.code == 27) { window.parent.getFiles(1); }});
  }
//-->
</script>
{/literal}

<div style='text-align:left; padding: 10px;'>
  <div style='padding-bottom: 5px;'>
    {lang_print id=2000246} {lang_sprintf id=2000247 1=$space_left} {lang_sprintf id=2000249 1=$max_filesize 2=$allowed_exts}
  </div>

  {include file='user_upload.tpl' action='user_group_upload.php' session_id=$session_id upload_token=$upload_token show_uploader=$show_uploader inputs=$inputs file_result=$file_result}

</div>



</body>
</html>