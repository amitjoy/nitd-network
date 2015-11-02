<?
$page = "articles";
include "header.php";

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if($user->user_exists == 0 & $setting[setting_permission_article] == 0) {
  $page = "error";
  $smarty->assign('error_header', 11150636);
  $smarty->assign('error_message', 11150637);
  $smarty->assign('error_submit', 11150638);
  include "footer.php";
}


if(isset($_POST['articlecat_id'])) { $articlecat_id = $_POST['articlecat_id']; } elseif(isset($_GET['articlecat_id'])) { $articlecat_id = $_GET['articlecat_id']; } else { $articlecat_id = ""; }
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }

$keyword = rc_toolkit::get_request('keyword');
$f = rc_toolkit::get_request('f');
$tag = rc_toolkit::get_request('tag');

// CREATE ARTICLE OBJECT
$now = time();
$current_time = time();
$article = new rc_article();
$rc_tag = new rc_articletag();


$criterias = array(
  "article_approved = '1'",
  "article_draft = '0'",
  "article_search= '1'"
);
if ($owner->user_exists) {
	$criterias[] = "article_user_id = '{$owner->user_info['user_id']}'";
}
if (strlen($keyword)) {
	$criterias[] = "(article_title LIKE '%$keyword%' OR article_body LIKE '%$keyword%')";
}
if ($f == 1) {
	$criterias[] = "article_featured = '1'";
}

if (strlen($tag)) {
	$ids = $rc_tag->get_object_ids_tagged_with($tag);
	$criterias[] = "article_id IN ('" . join("','",$ids) . "')";
}

$article_menu_filter = $criterias;
$rc_articlecats = new rc_articlecats();
$menu_options = array(
  'expanded_category_id' => $articlecat_id,
  'count_criteria' => join(" AND ", $article_menu_filter)
);
$categories = $rc_articlecats->get_category_menu($menu_options);

if ($articlecat_id != "") {
	if ($articlecat_id > 0) {
		$criterias[] = "(article_articlecat_id='$articlecat_id' OR articlecat_dependency='$articlecat_id')";
	}
	else {
		$criterias[] = "article_articlecat_id='0'";
	}
}
else {
	$nocat = 1;
}
$where = join(' AND ', $criterias);


// GET TOTAL ARTICLES
$total_articles = $article->article_total($where);
$articles_totalnocat = $article->article_total(join(' AND ', array_merge($article_menu_filter,array('no'=>"article_articlecat_id='0'"))));

// MAKE ARTICLE PAGES
$articles_per_page = 10;
$page_vars = make_page($total_articles, $articles_per_page, $p);


$s = rc_toolkit::get_request('s','date');
if ($s == 'view') {
	$sort = "article_views DESC";
}
elseif ($s == 'title') {
	$sort = "article_title ASC";
}
else {
	$sort = "article_date_start DESC";
	$s = 'date';
}

$category_info = $rc_articlecats->get_record($articlecat_id);

/*
rc_toolkit::debug($categories, "CATEGORIES MENU");
rc_toolkit::debug($total_articles, "total_articles");
rc_toolkit::debug($where, "where");
rc_toolkit::debug($sort, "sort");
 */

// GET ARTICLES
$article_array = $article->article_list($page_vars[0], $articles_per_page, $sort, $where, 1);

foreach ($article_array as $k => $article_entry) {
  $article_array[$k]['article']->article_info['article_body'] = str_replace("\r\n", "",html_entity_decode($article_entry['article']->article_info['article_body']));
}

//rc_toolkit::debug($article_array, "article_array");


// POPULAR TAGS
$popular_max_tags = 50;
$popular_order_tag_by = 'name'; // use 'count' or 'name'
$popular_distribution_classes=array(1,3,7,10,16,25,40,50);
$popular_tags = $rc_tag->get_popular_tags($popular_max_tags, $popular_order_tag_by, null, $popular_distribution_classes);
$smarty->assign('popular_tags', $popular_tags);
//rc_toolkit::debug($popular_tags, "popular_tags");
// -----------------

$smarty->assign('tag', $tag);
$smarty->assign('keyword', $keyword);
$smarty->assign('s', $s);
$smarty->assign('f', $f);

// ASSIGN SMARTY VARIABLES AND DISPLAY BROWSE ARTICLES PAGE
$smarty->assign('articles_totalnocat', $articles_totalnocat);
$smarty->assign('total_articles', $total_articles);
$smarty->assign('categories', $categories);
$smarty->assign('articlecat_id', $articlecat_id);
$smarty->assign('articlecat_title', $category_info[articlecat_title]);
$smarty->assign('article_array', $article_array);
$smarty->assign('nocat', $nocat);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($article_array));
include "footer.php";
?>