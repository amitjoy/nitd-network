<?php

$page = "admin_subnetworks";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['s'])) { $s = $_POST['s']; } elseif(isset($_GET['s'])) { $s = $_GET['s']; } else { $s = "id"; }

// SET RESULT VARIABLE
$result = 0;




// UPDATE DIFFERENTIATION FIELDS
if($task == "doupdate") {
  $setting[setting_subnet_field1_id] = $_POST['setting_subnet_field1_id'];
  $setting[setting_subnet_field2_id] = $_POST['setting_subnet_field2_id'];
  $database->database_query("UPDATE se_settings SET setting_subnet_field1_id='$setting[setting_subnet_field1_id]', setting_subnet_field2_id='$setting[setting_subnet_field2_id]'");
  $result = 191;




// CREATE SUBNETWORK
} elseif($task == "create") {
  $subnet_name = $_POST['subnet_name'];
  $subnet_field1_qual = htmlspecialchars_decode($_POST['subnet_field1_qual'], ENT_QUOTES);
  $subnet_field1_value = $_POST['subnet_field1_value'];
  $subnet_field1_month = $_POST['subnet_field1_month'];
  $subnet_field1_day = $_POST['subnet_field1_day'];
  $subnet_field1_year = $_POST['subnet_field1_year'];
  $subnet_field2_qual = htmlspecialchars_decode($_POST['subnet_field2_qual'], ENT_QUOTES);
  $subnet_field2_value = $_POST['subnet_field2_value'];
  $subnet_field2_month = $_POST['subnet_field2_month'];
  $subnet_field2_day = $_POST['subnet_field2_day'];
  $subnet_field2_year = $_POST['subnet_field2_year'];

  // FIELD 1 IS A DATE
  if($subnet_field1_value == "" && $subnet_field1_month != "" && $subnet_field1_day != "" && $subnet_field1_year != "") {  
    $subnet_field1_value = $datetime->MakeTime("0", "0", "0", "$subnet_field1_month", "$subnet_field1_day", "$subnet_field1_year");
  }

  // FIELD 2 IS A DATE
  if($subnet_field2_value == "" && $subnet_field2_month != "" && $subnet_field2_day != "" && $subnet_field2_year != "") {  
    $subnet_field2_value = $datetime->MakeTime("0", "0", "0", "$subnet_field2_month", "$subnet_field2_day", "$subnet_field2_year");
  }

  // FIELD 2 IS NOT FULLY FILLED OUT
  if(($subnet_field2_qual != "" && $subnet_field2_value == "") || ($subnet_field2_qual == "" && $subnet_field2_value != "")) { $subnet_field2_qual = ""; $subnet_field2_value = ""; }

  // CREATE SUBNETWORK IF NO ERRORS
  if($subnet_name != "" && $subnet_field1_qual != "" && $subnet_field1_value != "") {
    $name_languagevar_id = SE_Language::edit(0, $subnet_name, NULL, LANGUAGE_INDEX_SUBNETS);
    $database->database_query("INSERT INTO se_subnets (subnet_name, subnet_field1_qual, subnet_field1_value, subnet_field2_qual, subnet_field2_value) VALUES ('$name_languagevar_id', '$subnet_field1_qual', '$subnet_field1_value', '$subnet_field2_qual', '$subnet_field2_value')");
  }



// EDIT SUBNETWORK
} elseif($task == "edit") {
  $subnet_id = $_POST['subnet_id'];
  $subnet_name = $_POST['subnet_name'];
  $subnet_field1_qual = htmlspecialchars_decode($_POST['subnet_field1_qual'], ENT_QUOTES);
  $subnet_field1_value = $_POST['subnet_field1_value'];
  $subnet_field1_month = $_POST['subnet_field1_month'];
  $subnet_field1_day = $_POST['subnet_field1_day'];
  $subnet_field1_year = $_POST['subnet_field1_year'];
  $subnet_field2_qual = htmlspecialchars_decode($_POST['subnet_field2_qual'], ENT_QUOTES);
  $subnet_field2_value = $_POST['subnet_field2_value'];
  $subnet_field2_month = $_POST['subnet_field2_month'];
  $subnet_field2_day = $_POST['subnet_field2_day'];
  $subnet_field2_year = $_POST['subnet_field2_year'];

  // FIELD 1 IS A DATE
  if($subnet_field1_value == "" && $subnet_field1_month != "" && $subnet_field1_day != "" && $subnet_field1_year != "") {  
    $subnet_field1_value = $datetime->MakeTime("0", "0", "0", "$subnet_field1_month", "$subnet_field1_day", "$subnet_field1_year");
  }

  // FIELD 2 IS A DATE
  if($subnet_field2_value == "" && $subnet_field2_month != "" && $subnet_field2_day != "" && $subnet_field2_year != "") {  
    $subnet_field2_value = $datetime->MakeTime("0", "0", "0", "$subnet_field2_month", "$subnet_field2_day", "$subnet_field2_year");
  }

  // FIELD 2 IS NOT FULLY FILLED OUT
  if(($subnet_field2_qual != "" && $subnet_field2_value == "") || ($subnet_field2_qual == "" && $subnet_field2_value != "")) { $subnet_field2_qual = ""; $subnet_field2_value = ""; }

  // EDIT SUBNETWORK IF NO ERRORS
  $subnet = $database->database_query("SELECT subnet_name FROM se_subnets WHERE subnet_id='$subnet_id'");
  if($subnet_name != "" && $subnet_field1_qual != "" && $subnet_field1_value != "" && $database->database_num_rows($subnet) == 1) {
    $subnet_info = $database->database_fetch_assoc($subnet);
    SE_Language::edit($subnet_info[subnet_name], $subnet_name);
    $database->database_query("UPDATE se_subnets SET subnet_field1_qual='$subnet_field1_qual', subnet_field1_value='$subnet_field1_value', subnet_field2_qual='$subnet_field2_qual', subnet_field2_value='$subnet_field2_value' WHERE subnet_id='$subnet_id'");
  }

  
  // Flush cached stuff
  $cache_object = SECache::getInstance();
  if( is_object($cache_object) )
  {
    $cache_object->remove('site_subnetworks_'.$subnet_id);
  }
  
}

// DELETE SUBNETWORK
elseif($task == "delete")
{
  $subnet_id = $_GET['subnet_id'];

  // DELETE SUBNETWORK AND MOVE ALL USERS TO DEFAULT SUBNETWORK
  if($database->database_num_rows($database->database_query("SELECT subnet_id FROM se_subnets WHERE subnet_id='$subnet_id'")) == 1) {
    $database->database_query("DELETE FROM se_subnets, se_languagevars USING se_subnets JOIN se_languagevars ON se_subnets.subnet_name=se_languagevars.languagevar_id WHERE subnet_id='$subnet_id'");
    $database->database_query("UPDATE se_users SET user_subnet_id='0' WHERE user_subnet_id='$subnet_id'");
    $result = 638;
  }
}











// SET SUBNETWORK SORT-BY VARIABLES FOR HEADING LINKS
$i = "id";   // SUBNET_ID
$u = "ud";    // NUMBER OF USERS

// SET SORT VARIABLE FOR DATABASE QUERY
if($s == "i") {
  $sort = "subnet_id";
  $i = "id";
} elseif($s == "id") {
  $sort = "subnet_id DESC";
  $i = "i";
} elseif($s == "u") {
  $sort = "users";
  $u = "ud";
} elseif($s == "ud") {
  $sort = "users DESC";
  $u = "u";
} else {
  $sort = "subnet_id DESC";
  $i = "i";
}







// GET NON DEPENDENT FIELDS
$field = new se_field("profile");
$field->cat_list();
$cat_array = $field->cats;

// GET PRIMARY AND SECONDARY FIELD INFO
$primary_query = $database->database_query("SELECT profilefield_id AS field_id, profilefield_title AS field_title, profilefield_type AS field_type, profilefield_special AS field_special, profilefield_options AS field_options FROM se_profilefields WHERE profilefield_id='$setting[setting_subnet_field1_id]'");
$secondary_query = $database->database_query("SELECT profilefield_id AS field_id, profilefield_title AS field_title, profilefield_type AS field_type, profilefield_special AS field_special, profilefield_options AS field_options FROM se_profilefields WHERE profilefield_id='$setting[setting_subnet_field2_id]'");

// IF PROFILE CATEGORY HAS BEEN SELECTED
if($setting[setting_subnet_field1_id] == "-1") {
  $primary[field_id] = -1;
  $primary[field_title] = 617;
  $primary[field_type] = 3;
  $primary[field_options] = Array();
  for($c=0;$c<count($cat_array);$c++) { $primary[field_options][] = Array('value' => $cat_array[$c][cat_id], 'label' => $cat_array[$c][cat_title]); }


// IF EMAIL ADDRESS HAS BEEN SELECTED
} elseif($setting[setting_subnet_field1_id] == "0") {
  $primary[field_id] = 0;
  $primary[field_title] = 616;
  $primary[field_type] = 1;

// IF PROFILE FIELD HAS BEEN SELECTED
} elseif($database->database_num_rows($primary_query) == 1) {
  $primary = $database->database_fetch_assoc($primary_query);
  $primary[field_options] = unserialize($primary[field_options]);
  if($primary[field_special] == 1) { $primary[field_type] = 1; }

// IF NO SELECTION
} else {
  $primary[field_id] = -2;
  $primary[field_title] = 0;
  $primary[field_type] = 1;
}


// IF PROFILE CATEGORY HAS BEEN SELECTED
if($setting[setting_subnet_field2_id] == "-1") {
  $secondary[field_id] = -1;
  $secondary[field_title] = 617;
  $secondary[field_type] = 3;
  $secondary[field_options] = Array();
  for($c=0;$c<count($cat_array);$c++) { $secondary[field_options][] = Array('value' => $cat_array[$c][cat_id], 'label' => $cat_array[$c][cat_title]); }

// IF EMAIL ADDRESS HAS BEEN SELECTED
} elseif($setting[setting_subnet_field2_id] == "0") {
  $secondary[field_id] = 0;
  $secondary[field_title] = 616;
  $secondary[field_type] = 1;

// IF PROFILE FIELD HAS BEEN SELECTED
} elseif($database->database_num_rows($secondary_query) == 1) {
  $secondary = $database->database_fetch_assoc($secondary_query);
  $secondary[field_options] = unserialize($secondary[field_options]);
  if($secondary[field_special] == 1) { $secondary[field_type] = 1; }

// IF NO SELECTION
} else {
  $secondary[field_id] = -2;
  $secondary[field_title] = 0;
  $secondary[field_type] = 1;
}








// GET SUBNETWORK ARRAY
$subnets = $database->database_query("SELECT se_subnets.*, count(se_users.user_id) AS users FROM se_subnets LEFT JOIN se_users ON se_subnets.subnet_id=se_users.user_subnet_id GROUP BY se_subnets.subnet_id ORDER BY $sort");
$subnet_array = Array();

// LOOP OVER SUBNETWORKS
while($subnet_info = $database->database_fetch_assoc($subnets)) {

  switch($primary[field_type]) {
    case "1":
    case "2":
      $subnet_field1_value = $subnet_info[subnet_field1_value];
      $subnet_field1_value_date = 0;
      break;
    case "3":
    case "4":
      $subnet_field1_value_date = 0;
      // LOOP OVER FIELD OPTIONS
      $options = $primary[field_options];
      for($i=0,$max=count($options);$i<$max;$i++) {
        if($subnet_info[subnet_field1_value] == $options[$i][value]) {
          SE_Language::_preload_multi($options[$i][label]);
          SE_Language::load();
          $subnet_field1_value = SE_Language::_get($options[$i][label]);
          break;
        }  
      }
      break;
    case "5":
      $subnet_field1_value = $datetime->cdate($setting[setting_dateformat], $subnet_info[subnet_field1_value]);
      $subnet_field1_value_date = $subnet_info[subnet_field1_value];
      break;
  }

  // SET SECONDARY FIELD TITLE
  $subnet_field2_qual = "";
  $subnet_field2_value = "";
  if($setting[setting_subnet_field2_id] != -2 && $subnet_info[subnet_field2_qual] != "" && $subnet_info[subnet_field2_value] != "") {
    $subnet_field2_qual = $subnet_info[subnet_field2_qual];
    switch($secondary[field_type]) {
      case "1":
      case "2":
        $subnet_field2_value = $subnet_info[subnet_field2_value];
        $subnet_field2_value_date = 0;
        break;
      case "3":
      case "4":
        $subnet_field2_value_date = 0;
        // LOOP OVER FIELD OPTIONS
        $options = $secondary[field_options];
        for($i=0,$max=count($options);$i<$max;$i++) {
          if($subnet_info[subnet_field2_value] == $options[$i][value]) {
            SE_Language::_preload_multi($options[$i][label]);
            SE_Language::load();
            $subnet_field2_value = SE_Language::_get($options[$i][label]);
            break;
          }  
        }
        break;
      case "5":
        $subnet_field2_value_date = $subnet_info[subnet_field2_value];
        $subnet_field2_value = $datetime->cdate($setting[setting_dateformat], $subnet_info[subnet_field2_value]);
        break;
    }
  }

  // SET SUBNET ARRAY AND INCREMENT SUBNET COUNT
  SE_Language::_preload($subnet_info[subnet_name]);
  $subnet_array[] = Array('subnet_id' => $subnet_info[subnet_id],
				'subnet_name' => $subnet_info[subnet_name],
				'subnet_field1_qual' => $subnet_info[subnet_field1_qual],
				'subnet_field1_value_formatted' => $subnet_field1_value,
				'subnet_field1_value' => $subnet_info[subnet_field1_value],
				'subnet_field1_month' => $datetime->cdate("n", $subnet_field1_value_date),
				'subnet_field1_day' => $datetime->cdate("j", $subnet_field1_value_date),
				'subnet_field1_year' => $datetime->cdate("Y", $subnet_field1_value_date),
				'subnet_field2_qual' => $subnet_field2_qual,
				'subnet_field2_value_formatted' => $subnet_field2_value,
				'subnet_field2_value' => $subnet_info[subnet_field2_value],
				'subnet_field2_month' => $datetime->cdate("n", $subnet_field2_value_date),
				'subnet_field2_day' => $datetime->cdate("j", $subnet_field2_value_date),
				'subnet_field2_year' => $datetime->cdate("Y", $subnet_field2_value_date),
				'subnet_users' => $subnet_info[users]);
}





// SET NUMBER OF USERS IN DEFAULT SUBNETWORK
$default_users = $database->database_fetch_assoc($database->database_query("SELECT count(*) AS total FROM se_users WHERE user_subnet_id='0'"));


// ASSIGN VARIABLES AND SHOW SUBNETWORK PAGE
$smarty->assign('s', $s);
$smarty->assign('i', $i);
$smarty->assign('u', $u);
$smarty->assign('result', $result);
$smarty->assign('subnets', $subnet_array);
$smarty->assign('cats', $cat_array);
$smarty->assign('default_users', $default_users[total]);
$smarty->assign('primary', $primary);
$smarty->assign('secondary', $secondary);
include "admin_footer.php";
?>