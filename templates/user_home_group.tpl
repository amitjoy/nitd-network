
{* $Id: user_home_group.tpl 247 2009-11-14 03:30:43Z phil $ *}

{* BEGIN GROUPS *}
{if $total_group_subscribes != 0}
  <div class='spacer10'></div>
  <div class='header'>{lang_print id=2000240}</div>
  <div class='network_content'>
    {section name=subscribe_loop loop=$group_subscribes}
      {if $group_subscribes[subscribe_loop].total_comments != 0 OR $group_subscribes[subscribe_loop].total_posts != 0 OR $group_subscribes[subscribe_loop].total_photos != 0}
        {if !$smarty.section.subscribe_loop.first}<div style='height: 5px;'></div>{/if}
        <div style='font-weight: bold; margin-bottom: 2px;'>
          <a href='{$url->url_create("group", $smarty.const.NULL, $group_subscribes[subscribe_loop].group_id)}'>{$group_subscribes[subscribe_loop].group_title}</a>
        </div>
        {if $group_subscribes[subscribe_loop].total_comments != 0} 
        <div style='font-size: 9px;'>
          - <a href='{$url->url_create("group", $smarty.const.NULL, $group_subscribes[subscribe_loop].group_id)}&v=comments'>{lang_sprintf id=2000241 1=$group_subscribes[subscribe_loop].total_comments}</a>
        </div>
        {/if}
        {if $group_subscribes[subscribe_loop].total_posts != 0} 
        <div style='font-size: 9px;'>
          - <a href='{$url->url_create("group", $smarty.const.NULL, $group_subscribes[subscribe_loop].group_id)}&v=discussions'>{lang_sprintf id=2000242 1=$group_subscribes[subscribe_loop].total_posts}</a>
        </div>
        {/if}
        {if $group_subscribes[subscribe_loop].total_photos != 0} 
        <div style='font-size: 9px;'>
          - <a href='{$url->url_create("group", $smarty.const.NULL, $group_subscribes[subscribe_loop].group_id)}&v=photos'>{lang_sprintf id=2000243 1=$group_subscribes[subscribe_loop].total_photos}</a>
        </div>
        {/if}
        {assign var='group_updates' value=$group_updates+1}
      {/if}
    {/section}
    {if $group_updates == 0}
      {lang_print id=2000245}
    {/if}
  </div>
{/if}
{* END GROUPS *}
