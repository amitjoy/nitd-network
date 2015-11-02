<?php

include_once "class_radcodes.php";


// THIS FUNCTION RETURNS TEXT CORRESPONDING TO THE GIVEN ARTICLE PRIVACY LEVEL
// INPUT: $privacy_level REPRESENTING THE LEVEL OF ARTICLE PRIVACY
// OUTPUT: A STRING EXPLAINING THE GIVEN PRIVACY SETTING
function article_privacy_levels($privacy_level) {
	global $functions_article;

	switch($privacy_level) {
	  case 0: $privacy = $functions_article[1]; break;
	  case 1: $privacy = $functions_article[2]; break;
	  case 2: $privacy = $functions_article[3]; break;
	  case 3: $privacy = $functions_article[4]; break;
	  case 4: $privacy = $functions_article[5]; break;
	  case 5: $privacy = $functions_article[6]; break;
	  case 6: $privacy = $functions_article[7]; break;
//	  case 7: $privacy = $functions_article[8]; break;
	  default: $privacy = ""; break;
	}

	return $privacy;
} // END article_privacy_levels() FUNCTION

function article_load_entries($var_name='article_entries', $limit=10, $where="", $sort="article_date_start DESC")
{
	global $smarty;
	
	$criterias = array(
	    "article_approved = '1'",
	    "article_draft = '0'",
	    "article_search= '1'"
	);
	if ($where != "") {
		$criterias[] = $where;
	}
	
	$where = join(' AND ', $criterias);

  $rc_article = new rc_article();
	$article_array = $rc_article->article_list(0, $limit, $sort, $where, 1);
	foreach ($article_array as $k => $article_entry) {
	  $article_array[$k]['article']->article_info['article_body'] = str_replace("\r\n", "",html_entity_decode($article_entry['article']->article_info['article_body']));
	}
	
	$smarty->assign($var_name, $article_array);
	
}

