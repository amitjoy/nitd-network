<?php

$page = "admin_document_category";
include "admin_header.php";


//GETTING THE DOCUMENT_CATEGORIES AND SUBCATEGORIES
$result = $database->database_query("SELECT * FROM se_document_categories WHERE cat_dependency='0' ORDER BY cat_order");
$categories = array();
if($database->database_num_rows($result) > 0) {
	while($info = $database->database_fetch_assoc($result)) {
		//GETTING SUB CATEGORIES ASSOCIATED WITH THIS CATEGORY
		$sub_cat_array = array();
		$sub_cat = $database->database_query("SELECT * FROM se_document_categories WHERE cat_dependency = '{$info['category_id']}' ORDER BY cat_order");
		while($info2 = $database->database_fetch_assoc($sub_cat)) {
			$tmp_array = array('sub_cat_id' => $info2['category_id'],
												 'sub_cat_name' => $info2['category_name'],
												 'order' => $info2['cat_order']		
			);
			$sub_cat_array[] = $tmp_array;
		}
		$category_array = array('category_id' => $info['category_id'],
		                        'category_name' => $info['category_name'],
		                        'order' => $info['order'],
		                        'sub_categories' => $sub_cat_array  
		                  );
		$categories[] = $category_array;                  
		
	}
}
$smarty->assign('categories', $categories);
include "admin_footer.php";
?>