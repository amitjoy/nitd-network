{include file='admin_header.tpl'}

{* $Id: admin_language_import.tpl 8 2009-01-11 06:02:53Z john $ *}

<h2>{lang_print id=1285}</h2>
<div>{lang_print id=1286}</div>
<br />


{if $result}
  <div class='success'>
    <img src='../images/success.gif' class='icon' border='0' />
    {lang_print id=191}
    {if !empty($import_results)}
    <br />
    {lang_print id=1287} {$import_results.updated|@count}<br />
    {lang_print id=1288} {$import_results.created|@count}<br />
    {lang_print id=1289} {$import_results.skipped|@count}<br />
    {lang_print id=1290} {$import_results.failed|@count}
    {/if}
  </div>
{/if}


{if $is_error}
  <div class='error'>
    <img src='../images/error.gif' border='0' class='icon' />
    {if is_numeric($is_error)}{lang_print id=$is_error}{else}{$is_error}{/if}
  </div>
{/if}



<form action='admin_language_import.php' method='post' enctype='multipart/form-data'>

<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class="header">{lang_print id=1291}</td>
  </tr>
  
  <tr>
    <td class="setting1">{lang_print id=1292}</td>
  </tr>
  
  <tr>
    <td class="setting2">
      <select name="language_id" class="text">
        <option value="-1">[{lang_print id=1293}]</option>
        {if $lang_packlist|@count != 0}
        {section name=lang_loop loop=$lang_packlist}
          <option value='{$lang_packlist[lang_loop].language_id}'>{$lang_packlist[lang_loop].language_name}</option>
        {/section}
        {/if}
      </select>
    </td>
  </tr>
  
  <tr>
    <td class="setting1">{lang_print id=1294}</td>
  </tr>
  
  <tr>
    <td class="setting2">
      <table cellpadding='2' cellspacing='0'>
        <tr>
          <td><input type="radio" name="language_import_mode" id="language_import_mode_replace" value="replace" checked /></td>
          <td><label for="language_import_mode_replace">{lang_print id=1295}</label></td>
        </tr>
        <tr>
          <td><input type="radio" name="language_import_mode" id="language_import_mode_ignore" value="ignore" /></td>
          <td><label for="language_import_mode_ignore">{lang_print id=1296}</label></td>
        </tr>
      </table>
    </td>
  </tr>
  
  <tr>
    <td class="setting1">{lang_print id=1297}</td>
  </tr>
  
  <tr>
    <td class="setting2">
      <input type='file' name='language_import_file' size='60' class='text'>
    </td>
  </tr>
  
</table>
<br />

<table cellpadding='0' cellspacing='0'>
  <tr>
    <td>
      <input type='submit' class='button' value='{lang_print id=1298}' />
      <input type='hidden' name='task' value='doimport' />&nbsp;
      </form>
    </td>
    <td>
      <form action="admin_language.php">
      <input type='submit' class='button' value='{lang_print id=39}' />
      </form>
    </td>
  </tr>
</table>

</form>

{include file='admin_footer.tpl'}