<?
$page = "user_education";
include "header.php";

$task = rc_toolkit::get_request('task','main');
$result = "";
$rc_validator = new rc_validator();
$rc_education = new rc_education($user->user_info[user_id]);

if($user->level_info[level_education_allow] == 0) { header("Location: user_home.php"); exit(); }

if ($task == 'dosave') {
  
  $educations = $_POST['educations'];
  //rc_toolkit::debug($educations);
  foreach ($educations as $eid=>$education) {
    if (strlen($education['education_name'])==0) {
      $rc_education->delete($eid); 
    }
    elseif ($eid == 'new') {
      $rc_education->insert($education);
    }
    else {
      $rc_education->update($eid,$education);
    }

  }
  
  $result = 11040714;
  
}

$educations = $rc_education->get_educations();
$educations['new'] = array(
  'education_id' => 'new',
  'education_name' => '',
  'education_year' => '',
  'education_for' => '',
  'education_degree' => '',
  'education_concentration1' => '',
  'education_concentration2' => '',
  'education_concentration3' => ''
);




$yearoptions = array();
foreach (range(date('Y') + 4, date('Y') - 100) as $number) {
  $yearoptions[$number] = $number;
}

$foroptions = array();
foreach (explode('|',SE_Language::_get(11040103)) as $v) {
  $foroptions[$v] = $v;
}

//print_r($foroptions);
//print_r($yearoptions);

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