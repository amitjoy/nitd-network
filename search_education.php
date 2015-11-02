<?
$page = "search_education";
include "header.php";

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if($user->user_exists == 0 & $setting[setting_permission_education] == 0) {
  $page = "error";
  $smarty->assign('error_header', 11040620);
  $smarty->assign('error_message', 11040619);
  $smarty->assign('error_submit', 11040621);
  include "footer.php";
}


$task = rc_toolkit::get_request('task','main');
$p = rc_toolkit::get_request('p',1);

$result = "";
$rc_validator = new rc_validator();
$rc_education = new rc_education();

//if($user->level_info[level_education_allow] == 0) { header("Location: user_home.php"); exit(); }

$educations_per_page = 20;

$searched_fields = rc_toolkit::get_request('search',array());

if ($task == 'search' || $task == 'browse') {
  
  $searchable_fields = array(
    'education_name',
    'education_year',
    'education_for',
    'education_degree',
    'education_concentration1',
    'education_concentration2',
    'education_concentration3'
  );
  
  $operation = strtolower(rc_toolkit::get_request('operation','and'));
  if (!in_array($operation, array('and','or'))) $operation = 'and';

  foreach ($searched_fields as $field => $value) {
    // security filter !!
    if (in_array($field, $searchable_fields)) {
      $value = mysql_real_escape_string($value);
      $search_data[$field] = " $field LIKE '%$value%' ";
      $search_query  .= "search[$field]=".urlencode($value).'&';
    }
  }
  
  $criteria = " JOIN se_users ON se_users.user_id = se_educations.education_user_id";
  if (count($search_data)) {
    $criteria .= " WHERE " . join(" $operation ", $search_data);
  }
  $criteria .= " ORDER BY user_username ASC";
  $all_educations = $rc_education->get_records($criteria, true);
  
  $page_vars = make_page(count($all_educations), $educations_per_page, $p);

  $educations = array_slice($all_educations, $page_vars[0], $educations_per_page);
  
  $educations = $rc_education->build_searchable_fields($educations);
  foreach ($educations as $k=>$e) {
    $u = new se_user();
    $u->user_info[user_id] = $e[user_id];
    $u->user_info[user_username] = $e[user_username];
    $u->user_info[user_photo] = $e[user_photo];
    $educations[$k]['user'] = $u;
  }
}

$yearoptions = array();
foreach (range(date('Y') + 4, date('Y') - 100) as $number) {
  $yearoptions[$number] = $number;
}

$foroptions = array();
foreach (explode('|',SE_Language::_get(11040103)) as $v) {
  $foroptions[$v] = $v;
}

$smarty->assign('task', $task);

$smarty->assign('search_query', $search_query);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($educations));

$smarty->assign('search', $searched_fields);
$smarty->assign('total_educations',count($all_educations));

$smarty->assign('yearoptions',$yearoptions);
$smarty->assign('foroptions',$foroptions);
// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('educations', $educations);
$smarty->assign('rc_education', $rc_education);

$smarty->assign('is_error', $rc_validator->has_errors());
$smarty->assign('error_message', join(" ",$rc_validator->get_errors()));
$smarty->assign('result', $result);

include "footer.php";
?>