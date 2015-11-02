<?php

$page = "admin_document_cat_update";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['hideSearch'])) { $hideSearch = $_POST['hideSearch']; } elseif(isset($_GET['hideSearch'])) { $hideSearch = $_GET['hideSearch']; }
if(isset($_POST['hideDisplay'])) { $hideDisplay = $_POST['hideDisplay']; } elseif(isset($_GET['hideDisplay'])) { $hideDisplay = $_GET['hideDisplay']; }
if(isset($_POST['hideSpecial'])) { $hideSpecial = $_POST['hideSpecial']; } elseif(isset($_GET['hideSpecial'])) { $hideSpecial = $_GET['hideSpecial']; }





// SAVE CATEGORY
if($task == "savecat")
{
  $cat_id = $_GET['cat_id'];
  $cat_title = $_GET['cat_title'];
  $cat_dependency = $_GET['cat_dependency'];

  // IF CAT TITLE IS BLANK, DELETE
  if($cat_title == "") {

    if($cat_id != "new") {
    	//CHECKING IF THE CATEGORY HAS SUB CATEGORIES
			$result = $database->database_query("SELECT category_id FROM se_document_categories WHERE cat_dependency = '$cat_id'");
			while($info = $database->database_fetch_assoc($result)) {
				$sub_cats[] = $info['category_id'];
			}
			if(!empty($sub_cats)) {
				$string = $cat_id . ', ' .implode(", ", $sub_cats);
			}
			else {
				$string = $cat_id;
			}
    	
    	
    	$database->database_query("DELETE t1,t2 FROM se_document_categories AS t1 LEFT JOIN se_document_categories AS t2 ON t1.category_id = t2.cat_dependency  WHERE t1.category_id = '{$cat_id}'");
    	
    	//MAKING THE CATEGORIES OF ALL DOCUMENTS THAT BELONGS TO THIS CATEGORY, ZERO
    	$database->database_query("UPDATE se_documents SET document_category_id=0 WHERE document_category_id IN ($string)");
    }

    // SEND AJAX CONFIRMATION
    echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><script type='text/javascript'>";
    echo "window.parent.removecat('$cat_id');";
    echo "</script></head><body></body></html>";
    exit();

  // SAVE CHANGES
  } else {

    //SAVING CHANGES
    if($cat_id == 'new') {
    	$cat_order = $database->database_fetch_assoc($database->database_query("SELECT max(cat_order) AS cat_order FROM se_document_categories"));
	    $cat_order = $cat_order[cat_order]+1;
	    $database->database_query("INSERT INTO se_document_categories (category_name, cat_order, cat_dependency) VALUES ('$cat_title', '$cat_order', '$cat_dependency')");
	    $newcat_id = $database->database_insert_id();
    }
    else {
    	$database->database_query("UPDATE se_document_categories SET category_name = '{$cat_title}' WHERE category_id = '{$cat_id}'");
    	$newcat_id = $cat_id;
    }

    // SEND AJAX CONFIRMATION
    echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><script type='text/javascript'>";
    echo "window.parent.savecat_result('$cat_id', '$newcat_id', '$cat_title', '$cat_dependency');";
    echo "</script></head><body></body></html>";
    exit();

  }



// CHANGE ORDER OF CATS/FIELDS
} elseif($task == "changeorder") {
  $listorder = $_GET['listorder'];
  $divId = $_GET['divId'];

  $list = explode(",", $listorder);

  // RESORT CATEGORIES
  if($divId == "categories") {
    for($i=0;$i<count($list);$i++) {
      $cat_id = substr($list[$i], 4);
      $database->database_query("UPDATE se_document_categories SET cat_order='".($i+1)."' WHERE category_id='$cat_id'");
    }

  // RESORT SUBCATEGORIES
  } elseif(substr($divId, 0, 7) == "subcats") {
    for($i=0;$i<count($list);$i++) {
      $cat_id = substr($list[$i], 4);
      $database->database_query("UPDATE se_document_categories SET cat_order='".($i+1)."' WHERE category_id='$cat_id'");
    }

  } 

}  

?>