
<table style="width: 100%;" cellpadding='0' cellspacing='0'>
  <tr>
    <td nowrap="nowrap" width="1" id='se_debug_tabs_summary' class="profile_tab2" style="border-top: 0px solid #ffffff;">
      <a href="javascript:void(0);" onclick="loadDebugTab('summary');return false;">Summary</a>
    </td>
    <td nowrap="nowrap" width="1" id='se_debug_tabs_php' class="profile_tab" style="border-top: 0px solid #ffffff;">
      <a href="javascript:void(0);" onclick="loadDebugTab('php');return false;">PHP</a>
    </td>
    <td nowrap="nowrap" width="1" id='se_debug_tabs_sql' class="profile_tab" style="border-top: 0px solid #ffffff;">
      <a href="javascript:void(0);" onclick="loadDebugTab('sql');return false;">SQL</a>
    </td>
    <td class="profile_tab" style="border-top: 0px solid #ffffff;">&nbsp;</td>
  </tr>
</table>



{* SUMMARY *}

<div id="se_debug_summary" align='left' style="margin: 5px;">
  <div>Total Time: {$debug_benchmark_total}</div>
  <div>Total SQL Time: {$database->log_data_totals.time|default:0}</div>
  <div>Total SQL Queries: {$database->log_data_totals.total|default:0}</div>
  <div>Total SQL Queries (failed): {$database->log_data_totals.failed|default:0}</div>
</div>



{* PHP *}
<div id="se_debug_php" align='left' style="margin: 5px;display:none;">
  {foreach from=$debug_benchmark key=benchmark_label item=benchmark_data}
  <div style="display:block;border: 1px solid #cccccc;">
    <table cellpadding='0' cellspacing='0'>
      <tr>
        <td style="padding: 5px 10px 5px 10px;">
          <span style="display:block;">Label: {$benchmark_label}</span>
          <span style="display:block;">Time: {$benchmark_data.total|string_format:"%.4f"}</span>
          <span style="display:block;">Index: {$benchmark_data.index}</span>
          <span style="display:block;"><a href="javascript:void(0);" onclick="$('se_debug_benchmark_details_{$benchmark_label}').style.display = ( $('se_debug_benchmark_details_{$benchmark_label}').style.display=='none' ? '' : 'none');">Details</a></span>
          <span id="se_debug_benchmark_details_{$benchmark_label}" style="display: none;">
            {foreach from=$benchmark_data.list key=benchmark_detail_index item=benchmark_detail_data}
            <span style="display:block;">Index: {$benchmark_detail_index}</span>
            <span style="display:block;">Delta: {$benchmark_detail_data.delta_time|string_format:"%.4f"} / {$benchmark_detail_data.delta_memory}</span>
            <span style="display:block;">Start: {$benchmark_detail_data.start_time|string_format:"%.4f"} / {$benchmark_detail_data.start_memory}</span>
            <span style="display:block;">End: {$benchmark_detail_data.end_time|string_format:"%.4f"} / {$benchmark_detail_data.end_memory}</span>
            <span style="display:block;">Note: {$benchmark_detail_data.note}</span>
            {/foreach}
          </span>
        </td>
      </tr>
    </table>
  </div>
  {/foreach}
</div>


{* SQL *}

<div id="se_debug_sql" align='left' style="margin: 5px;display:none;">
  <div>Total SQL Time: {$database->log_data_totals.time|default:0}</div>
  <div>Total SQL Queries: {$database->log_data_totals.total|default:0}</div>
  <div>Total SQL Queries (failed): {$database->log_data_totals.failed|default:0}</div>
  <br />
  <br />
  
  <div id="sqlexplaindiv" style="display:none;">
    <div id="sqlquery"   style="width: 98%; display: block; padding: 5px; border:1px solid #cccccc; border-bottom:none;"></div>
    <div id="sqlexplain" style="width: 98%; display: block; padding: 5px; border:1px solid #cccccc;"></div>
  </div>
  
  {$database->database_benchmark_sort()}
  
  {section name=stat_loop loop=$database->log_data}
  <div style="display:block;border: 1px solid #cccccc;">
    <table cellpadding='0' cellspacing='0'>
      <tr>
        <td style="width:10px;background:{$database->log_data[stat_loop].color};">&nbsp;</td>
        <td style="padding: 5px 10px 5px 10px;">
          <span style="display:block;">Index: {$database->log_data[stat_loop].index}</span>
          <span style="display:block;">Hash: {$database->log_data[stat_loop].query_hash}</span>
          {if !$database->log_data[stat_loop].result}
            <span style="display:block;"><span style="color: #ff0000;">Error:</span> {$database->log_data[stat_loop].error}</span>
          {/if}
          <span style="display:block;">Benchmark: {$database->log_data[stat_loop].time}</span>
          <span style="display:block;">Short: {$database->log_data[stat_loop].query|truncate:70}</span>
          <span style="display:block;"><a href="javascript:void(0);" onclick="$('se_debug_sql_query_{$database->log_data[stat_loop].index}').style.display = ( $('se_debug_sql_query_{$database->log_data[stat_loop].index}').style.display=='none' ? '' : 'none');">Details</a></span>
          <div id="se_debug_sql_query_{$database->log_data[stat_loop].index}" style="display:none;">
            {counter start=0 skip=1 print=0}
            {section name=backtrace_loop loop=$database->log_data[stat_loop].backtrace}
              <span style="display:block;">Backtrace {counter}:  {$database->log_data[stat_loop].backtrace[backtrace_loop].file_short} [{$database->log_data[stat_loop].backtrace[backtrace_loop].line}] {$database->log_data[stat_loop].backtrace[backtrace_loop].function}</span>
            {/section}
            <span style="display:block;">Query: {$database->log_data[stat_loop].query}</span>
          </div>
        </td>
      </tr>
    </table>
  </div>
  {/section}
  
  {literal}
  <script type="text/javascript">
    
    window.addEvent('load', function()
    {
      if( failed_queries>0 )
        alert_summary(failed_queries);
    });
    
  </script>
  {/literal}
</div>