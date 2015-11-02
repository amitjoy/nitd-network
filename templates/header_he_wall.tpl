
{*
@author Ermek
@copyright Hire-Experts LLC
@version Wall 3.1
*}

{if $he_wall_page}
<link rel="stylesheet" href="./templates/he_styles.css" title="stylesheet" type="text/css" />
<link rel="stylesheet" href="./templates/he_wall.css" title="stylesheet" type="text/css" />

<script type="text/javascript" src="./include/js/webtoolkit.aim.js"></script>
<script type="text/javascript" src="./include/standalone/audio-player.js"></script>
<script type="text/javascript" src="./include/js/he_wall.js"></script>
<script type="text/javascript">
    AudioPlayer.setup("./include/standalone/player.swf", {ldelim} width: 290 {rdelim});
    {if $he_wall_show_video_player}he_wall.show_video_player = true;{/if}
</script> 
{/if}

{if $he_wall_group_page}
<script type="text/javascript">
window.addEvent('domready', function(){ldelim}
    var $tab_menu = $$('.group_tab_end');
    $('he_wall_group_tab').inject($tab_menu[0], 'before');

    var $tab_content = $$('.group_content');
    $('group_wall').inject($tab_content[0], 'bottom');
{rdelim});
</script>

<div class="display_none">
    <table>
        <tr>
            <td valign="bottom" id="he_wall_group_tab">
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="group_tab" id="group_tabs_wall" onMouseUp="this.blur()">
                            <a href="javascript:void(0);" onMouseDown="loadGroupTab('wall');" onMouseUp="this.blur()">{lang_print id=690706100}</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    
    <div id="group_wall" style="display: none;">
        <div>
            <img border="0" class="icon_big" src="./images/he_wall_big_icon.png"/>
            <div class="page_header">{lang_print id=690706048}</div>
            <div style='clear: both;'></div>
        </div>
        <div style="padding-left:5px;">
            {he_wall_display object='group' object_id=$group->group_info.group_id}
        </div>
    </div>
</div>
{/if}