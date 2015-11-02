{include file='admin_header.tpl'}

{* $Id: admin_video_utilities.tpl 13 2009-01-11 06:04:29Z john $ *}

<h2>{lang_print id=5500159}</h2>
{lang_print id=5500160}
<br />
<br />

<b><a href='admin_video_utilities.php?task=version'>{lang_print id=5500161}</a></b>
<br />
{lang_print id=5500162}
<br />
<br />

<b><a href='admin_video_utilities.php?task=formats'>{lang_print id=5500163}</a></b>
<br />
{lang_print id=5500164}
<br />
<br />

<b>{if $log_browser_enabled}<a href='admin_video_utilities.php?task=logbrowse'>{/if}{lang_print id=5500165}{if $log_browser_enabled}</a>{/if}</b>
<br />
{lang_print id=5500166}{if !$log_browser_enabled} ({lang_print id=5500174}){/if}
<br />
<br />


{if $task=="version"}

  <h2>{lang_print id=5500167}</h2>
  
  <textarea style="width:100%; height:600px;">{$version_output}</textarea>

{elseif $task=="formats"}

  <h2>{lang_print id=5500168}</h2>
  <p>{lang_print id=5500169}</p>
  
  <textarea style="width:100%; height:600px;">{$format_output}</textarea>

{elseif $task=="logbrowse"}

  <h2>{lang_print id=5500165}</h2>
  
  <div style="border:1px solid #aaaaaa; padding:15px;">
  
  {section name=log_loop loop=$log_files}
  
    <div style="display:block;margin-bottom: 8px;">
      <a href="admin_video_utilities.php?task=logfile&file={$log_files[log_loop].file}">{$log_files[log_loop].file}</a><br />
      {lang_sprintf id=5500170 1=$log_files[log_loop].size}<br />
      {lang_sprintf id=5500171 1=$log_files[log_loop].type}
    </div>
  
  {sectionelse}
    
    No log files found.
    
  {/section}
  
  </div>

{elseif $task=="logfile"}

  <h2>{lang_print id=5500165}</h2>
  <p>{lang_sprintf id=5500172 1=$log_filename}</p>
  
  <textarea style="width:100%; height:600px;">{$log_output}</textarea>
  
{/if}



{include file='admin_footer.tpl'}