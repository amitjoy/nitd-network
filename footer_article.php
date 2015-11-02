<?php

switch($page) {

  // CODE FOR PROFILE PAGE
  case "profile":
	$entries = Array();
	$total_entries = 0;
	if($owner->level_info[level_article_allow] != 0) {
    $current_time = time();
	  // START article
	  $article = new rc_article($owner->user_info[user_id]);
	  $entries_per_page = 5;
	  $sort = "article_date_start DESC";

	  // GET PRIVACY LEVEL AND SET WHERE
	  $privacy_level = $owner->user_privacy_max($user, $owner->level_info[level_article_privacy]);
	  $where = "(article_privacy<='$privacy_level') AND article_approved = '1' AND article_draft = '0'";

	  // GET TOTAL ENTRIES
	  $total_entries = $article->article_total($where);

	  // GET ENTRY ARRAY
	  $entries = $article->article_list(0, $entries_per_page, $sort, $where, 1);

	}

	// ASSIGN ENTRIES SMARY VARIABLE
	$smarty->assign('article_entries', $entries);
	$smarty->assign('total_article_entries', $total_entries);
    break;

}
