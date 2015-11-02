{include file='admin_header.tpl'}

{* $Id: admin_language_export.tpl 8 2009-01-11 06:02:53Z john $ *}

<h2>Language Export Tool: {$language_info.language_name}</h2>
<p>
  You are about to export the language variables for the language
  "{$language_info.language_name}" (ID: {$language_info.language_id},
  Code: {$language_info.language_code}).
</p>
<br />

{if $result}
  <div class='success'>
    <img src='../images/success.gif' class='icon' border='0' />
    {lang_print id=191}
  </div>
{/if}

{if $is_error}
  <div class='error'>
    <img src='../images/error.gif' border='0' class='icon' />
    {if is_numeric($is_error)}{lang_print id=$is_error}{else}{$is_error}{/if}
  </div>
{/if}


<form action='admin_language_export.php?language_id={$language_info.language_id}' method='post'>

<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class="header">
      Export Options
    </td>
  </tr>
  
  <tr>
    <td class="setting1">
      You can select a specific range of variables to export. (Default is all)
    </td>
  </tr>
  
  <tr>
    <td class="setting2">
      <table cellpadding='2' cellspacing='0'>
        <tr>
          <td><input type="text" class="text" name="start" value="" /></td>
          <td><label>Start</label></td>
        </tr>
        <tr>
          <td><input type="text" class="text" name="end" value="" /></td>
          <td><label>End</label></td>
        </tr>
      </table>
    </td>
  </tr>
  
  <tr>
    <td class="setting1">
      Output File Name. Options are [code] for the language code (ex "en"),
      [range] for the output range, [id] for the language id. The file extension
      will always be ".php"
    </td>
  </tr>
  
  <tr>
    <td class="setting2">
      <table cellpadding='2' cellspacing='0'>
        <tr>
          <td><input type="text" class="text" name="output_filename" value="language_[code]" /></td>
        </tr>
      </table>
    </td>
  </tr>
  
</table>
<br />

<table cellpadding='0' cellspacing='0'>
  <tr>
    <td>
      <input type='submit' class='button' value='Export' />
      <input type='hidden' name='task' value='doexport' />&nbsp;
      </form>
    </td>
    <td>
      <form action="admin_language.php">
      <input type='submit' class='button' value='Cancel' />
      </form>
    </td>
  </tr>
</table>



{include file='admin_footer.tpl'}