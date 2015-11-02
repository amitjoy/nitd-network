{* $Id: document_tags.tpl 1 2010-02-02 09:36:11Z SocialEngineAddOns $ *}
{include file='header.tpl'}
<img src='./images/icons/document48.gif' border='0' class='icon_big'>
<div class='page_header'>{lang_print id=650003231}</div>
<div>{lang_print id=650003232}</div>

<br />

<div style="margin-top:50px;">
{foreach item=frequency key=tag from=$tag_array }
{math assign='step' equation="x + (a-b)*y" x=$tag_data.min_font_size a=$frequency b=$tag_data.min_frequency y=$tag_data.step}
<a href='{$url->url_create("browsedoctag", $tag)}' style="font-size:{$step}px;">{$tag}<sup>{$frequency}</sup></a>&nbsp;
{/foreach}
</div>
<br /><br /><br /><br /><br />
{include file='footer.tpl'}