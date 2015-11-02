<link rel="stylesheet" href="./templates/styles_article.css" title="stylesheet" type="text/css">  
<script type="text/javascript" src="./include/fckeditor/fckeditor.js"></script>
<script type="text/javascript" src="./include/js/showhide.js"></script>
{* YOU MUST BE KIDDING *}

{* ASSIGN MENU VARIABLES *}
{if $user->level_info.level_article_allow != 0}
  {array var="article_menu" value="user_article.php"}
  {array var="article_menu" value="article16.gif"}
  {array var="article_menu" value=$header_article1}
  {array var="global_plugin_menu" value=$article_menu} 
{/if}