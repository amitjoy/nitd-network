<?php

$page = "admin_language_edit";
include "admin_header.php";

if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['phrase'])) { $phrase = $_POST['phrase']; } elseif(isset($_GET['phrase'])) { $phrase = $_GET['phrase']; } else { $phrase = ""; }
if(isset($_POST['phrase_id'])) { $phrase_id = $_POST['phrase_id']; } elseif(isset($_GET['phrase_id'])) { $phrase_id = $_GET['phrase_id']; } else { $phrase_id = ""; }
if(isset($_POST['language_id'])) { $language_id = $_POST['language_id']; } elseif(isset($_GET['language_id'])) { $language_id = $_GET['language_id']; } else { $language_id = 0; }
if(isset($_POST['languagevar_id'])) { $languagevar_id = $_POST['languagevar_id']; } elseif(isset($_GET['languagevar_id'])) { $languagevar_id = $_GET['languagevar_id']; } else { $languagevar_id = 0; }


// IF JSON REQUEST, SEND BACK DATA
if($task == "getphrase") {
  $langvar_query = $database->database_query("SELECT * FROM se_languagevars WHERE languagevar_id='$languagevar_id'");
  while($langvar_info = $database->database_fetch_assoc($langvar_query)) {
    if($json != "") { $json .= ", "; }
    $json .= "{'$langvar_info[languagevar_language_id]':'".str_replace("\\", "\\\\", htmlspecialchars(str_replace("\n", "<br>", str_replace("\r\n", "<br>", $langvar_info[languagevar_value])), ENT_QUOTES))."'}";
  }
  $json = "{'phrases':[".$json."]}";
  echo $json;
  exit();



// ELSE TASK IS TO SAVE LANGUAGE VARIABLES
} elseif($task == "edit") {
  $languagevar_value = $_POST['languagevar_value'];
  while(list($lang_id, $value) = each($languagevar_value)) {
    SE_Language::edit($languagevar_id, htmlspecialchars_decode($value, ENT_QUOTES), $lang_id);
  }

  // RUN JAVASCRIPT TO UPDATE MAIN PAGE
  echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><script type='text/javascript'>";
  echo "window.parent.edit_result('$languagevar_id', '".str_replace("'", "\'", str_replace("\n", "", str_replace("\r\n", "", htmlspecialchars_decode($languagevar_value[$language_id], ENT_QUOTES))))."');";
  echo "</script></head><body></body></html>";
  exit();
}


// VALIDATE LANGUAGE
$lang_query = $database->database_query("SELECT * FROM se_languages WHERE language_id='$language_id'");
if($database->database_num_rows($lang_query) != 1) { header("Location: admin_language.php"); exit(); }
$language = $database->database_fetch_assoc($lang_query);


$var_query = "SELECT se_languagevars.*, 
		CASE
		  WHEN (languagevar_id <= 500000)
		    THEN 'Normal Variable'
		  WHEN (500000 < languagevar_id AND languagevar_id <= 600000)
		    THEN 'Profile Fields/Tabs'
		  WHEN (600000 < languagevar_id AND languagevar_id <= 633000)
		    THEN 'Friendship Types'
		  WHEN (633000 < languagevar_id AND languagevar_id <= 666000)
		    THEN 'User Levels'
		  WHEN (666000 < languagevar_id AND languagevar_id <= 700000)
		    THEN 'Subnetworks'
		  WHEN (700000 < languagevar_id AND languagevar_id <= 750000)
		    THEN 'Recent Actions'
		  WHEN (750000 < languagevar_id AND languagevar_id <= 800000)
		    THEN 'Notifications'
		  WHEN (800000 < languagevar_id AND languagevar_id <= 850000)
		    THEN 'FAQ Categories/Questions'
		  WHEN (850000 < languagevar_id AND languagevar_id <= 900000)
		    THEN 'System Emails'
		  WHEN (1000000 < languagevar_id AND languagevar_id <= 1500000)
		    THEN 'Album Plugin'
		  WHEN (1500000 < languagevar_id AND languagevar_id <= 2000000)
		    THEN 'Blog Plugin'
		  WHEN (2000000 < languagevar_id AND languagevar_id <= 2500000)
		    THEN 'Group Plugin'
		  WHEN (2500000 < languagevar_id AND languagevar_id <= 3000000)
		    THEN 'Poll Plugin'
		  WHEN (3000000 < languagevar_id AND languagevar_id <= 3500000)
		    THEN 'Event Plugin'
		  WHEN (3500000 < languagevar_id AND languagevar_id <= 4000000)
		    THEN 'Chat Plugin'
		  WHEN (4000000 < languagevar_id AND languagevar_id <= 4500000)
		    THEN 'Music Plugin'
		  WHEN (4500000 < languagevar_id AND languagevar_id <= 5000000)
		    THEN 'Classified Plugin'
		  ELSE
		    'Custom Variable'
		END
		AS languagevar_category FROM se_languagevars WHERE languagevar_language_id='$language_id'";
if($phrase_id != "") { $var_query .= " AND languagevar_id = $phrase_id"; $phrase = ""; }
if($phrase != "") { $var_query .= " AND languagevar_value LIKE '%".str_replace("%", "\%", $phrase)."%'"; }

// GET TOTAL LANGUAGE VARS
$total_vars = $database->database_num_rows($database->database_query($var_query));

// MAKE LANGUAGE VAR PAGES
$vars_per_page = 25;
$page_vars = make_page($total_vars, $vars_per_page, $p);

// GET LANGUAGE VARS
$var_query .= " ORDER BY languagevar_id LIMIT $page_vars[0], $vars_per_page";
$vars = $database->database_query($var_query);
while($var_info = $database->database_fetch_assoc($vars)) {
  $langvars[] = Array('languagevar_id' => $var_info[languagevar_id],
			'languagevar_value' => htmlspecialchars($var_info[languagevar_value], ENT_NOQUOTES),
			'languagevar_category' => $var_info[languagevar_category],
			'languagevar_default' => $var_info[languagevar_default]);

}

// GET LANGUAGE PACK LIST
$lang_packlist = SE_Language::list_packs();
ksort($lang_packlist);
$lang_packlist = array_values($lang_packlist);


// ASSIGN VARIABLES AND SHOW ADMIN USER LEVELS PAGE
$smarty->assign('language', $language);
$smarty->assign('langvars', $langvars);
$smarty->assign('lang_packlist', $lang_packlist);
$smarty->assign('phrase_id', $phrase_id);
$smarty->assign('phrase', $phrase);
$smarty->assign('p', $p);
$smarty->assign('total_vars', $total_vars);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($langvars));
include "admin_footer.php";
?>