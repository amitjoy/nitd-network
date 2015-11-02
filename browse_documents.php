<?php

$page = "browse_documents";
include "header.php";


$query = "SELECT * FROM se_document_parameters";
$params = $database->database_fetch_assoc($database->database_query($query));

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if( !$user->user_exists && !$params['permission_document'] )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

// PARSE GET/POST
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
if(isset($_POST['s'])) { $s = $_POST['s']; } elseif(isset($_GET['s'])) { $s = $_GET['s']; } else { $s = "created DESC"; }
if(isset($_POST['v'])) { $v = $_POST['v']; } elseif(isset($_GET['v'])) { $v = $_GET['v']; } else { $v = 0; }
if(isset($_POST['i'])) { $i = $_POST['i']; } elseif(isset($_GET['i'])) { $i = $_GET['i']; } else { $i = ""; }
if(isset($_POST['tag'])) { $tag = $_POST['tag']; } elseif(isset($_GET['tag'])) { $tag = $_GET['tag']; } else { $tag = ""; }
if(isset($_POST['search'])) { $search = $_POST['search']; } elseif(isset($_GET['search'])) { $search = $_GET['search']; } else { $search = ""; }
// ENSURE SORT/VIEW ARE VALID
if($s != "document_datecreated DESC"  && $s != "document_views DESC" && $s != "document_dateupdated DESC" && $s != 'total_comments DESC') { $s = "document_datecreated DESC"; }
if($v != "0" && $v != "1" && $v != '2') { $v = 0; }

// CREATE SCRIBD OBJECT
$document= new Document(null, null);


// SET WHERE CLAUSE
$where = "CASE
      WHEN se_documents.document_user_id='{$user->user_info[user_id]}'
        THEN TRUE
      WHEN ((se_documents.document_privacy & @SE_PRIVACY_REGISTERED) AND '{$user->user_exists}'<>0)
        THEN TRUE
      WHEN ((se_documents.document_privacy & @SE_PRIVACY_ANONYMOUS) AND '{$user->user_exists}'=0)
        THEN TRUE
      WHEN ((se_documents.document_privacy & @SE_PRIVACY_FRIEND) AND (SELECT TRUE FROM se_friends WHERE friend_user_id1=se_documents.document_user_id AND friend_user_id2='{$user->user_info[user_id]}' AND friend_status='1' LIMIT 1))
        THEN TRUE
      WHEN ((se_documents.document_privacy & @SE_PRIVACY_SUBNET) AND '{$user->user_exists}'<>0 AND (SELECT TRUE FROM se_users WHERE user_id=se_documents.document_user_id AND user_subnet_id='{$user->user_info[user_subnet_id]}' LIMIT 1))
        THEN TRUE
      WHEN ((se_documents.document_privacy & @SE_PRIVACY_FRIEND2) AND (SELECT TRUE FROM se_friends AS friends_primary LEFT JOIN se_users ON friends_primary.friend_user_id1=se_users.user_id LEFT JOIN se_friends AS friends_secondary ON friends_primary.friend_user_id2=friends_secondary.friend_user_id1 WHERE friends_primary.friend_user_id1=se_documents.document_user_id AND friends_secondary.friend_user_id2='{$user->user_info[user_id]}' AND se_users.user_subnet_id='{$user->user_info[user_subnet_id]}' LIMIT 1))
        THEN TRUE
      ELSE FALSE
  END";


// ONLY MY FRIENDS' DOCUMENTS
if( $v=="1" && $user->user_exists )
{
  // SET WHERE CLAUSE
  $where .= " && (
    SELECT
      TRUE
    FROM
      se_friends
    WHERE
      friend_user_id1='{$user->user_info[user_id]}' &&
      friend_user_id2=se_documents.document_user_id &&
      friend_status=1
    )
  ";
}

$where .= " AND (document_approved = '1') AND (document_publish = '1') AND (document_status = 1) ";

//ONLY FEATURED DOCUMENTS
if($v == 2) {
	$where .= " AND  (document_featured = 1)";
}

// SEARCH
if ($search != "") {
 $where .= " AND  (document_title LIKE '%$search%' OR document_description LIKE '%$search%' OR document_fulltext LIKE '%$search%' OR tag_name LIKE '%$search%')";
}

// SEARCH BY TAG
if ($tag != "") {
  $where .= " AND  (tag_name LIKE '%$tag%')";
}

//  SEARCH BY CATGEORY
if($i != "") {
	$i = (int) $i;
	//CHECKING IF THE CATEGORY HAS SUB CATEGORIES
	$result = $database->database_query("SELECT category_id FROM se_document_categories WHERE cat_dependency = '$i'");
	while($info = $database->database_fetch_assoc($result)) {
		$sub_cats[] = $info['category_id'];
	}
	if(!empty($sub_cats)) {
		$string = $i . ', ' .implode(", ", $sub_cats);
	}
	else {
		$string = $i;
	}
	 $where .= " AND (document_category_id IN ($string))";
	//if the category has a  main category
	$main_cat = $database->database_fetch_assoc($database->database_query("SELECT t2.category_id, t2.category_name FROM se_document_categories as t1 INNER JOIN se_document_categories t2 ON t1.cat_dependency = t2.category_id WHERE t1.category_id = '$i'"));
	if(!empty($main_cat)) {
		$smarty->assign("main_cat_id", $main_cat['category_id']);
	}
}



//GETTING FIVE RECENTLY UPLOADED DOCUMENTS AND UPDATED THEIR STATUS
$where_temp .=  $where . " AND (document_status = 0)"; 
$doc_forUpdate = $document->documents_list(0, 5, 'document_datecreated DESC', $where_temp, 1);



$scribd_api_key = $params['api_key'];
$scribd_secret = $params['secret_key'];
foreach($doc_forUpdate as $value) {
	$scribd = new Document($scribd_api_key, $scribd_secret, $value->document_info['document_user_id']);
  $scribd->my_user_id = $value->document_info['document_user_id']; 
  try {
    $stat = trim($scribd->getConversionStatus($value->document_info['document_doc_id']));
	}
	catch(Exception $e) {
		$message =  $e->getMessage();
		$excep_error = 1;
		$smarty->assign('excep_message', $message);
	} 

  if($stat == 'DONE') {  	
  	try {
	  	//GETTING DOCUMENT'S FULL TEXT
	  	$texturl = $scribd->getDownloadUrl($value->document_info['document_doc_id'], 'txt');
			//for some reason, the URL comes back with leading and trailing spaces
			$texturl = trim($texturl['download_link']);
			$file_contents = file_get_contents($texturl);
			if (empty($file_contents)) {
				$site_url = $texturl;
				$ch = curl_init();
				$timeout = 0; // set to zero for no timeout
				curl_setopt ($ch, CURLOPT_URL, $site_url);
				curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
				
				ob_start();
				curl_exec($ch);
				curl_close($ch);
				$file_contents = ob_get_contents();
				ob_end_clean();
			}
			$full_text = mysql_real_escape_string($file_contents);
			$setting = $scribd->getSettings($value->document_info['document_doc_id']);
			$thumbnail_url = trim($setting['thumbnail_url']);
			//UPDATING DOCUMENT STATUS AND FULL TEXT
			$database->database_query("UPDATE se_documents SET document_fulltext='$full_text', document_thumbnail='$thumbnail_url', document_status = '1' WHERE document_id='{$value->document_info['document_id']}'");
			
			// BECUSE OF LARGE DOCUMNET IF SQL QUEY FALIED THEN WE RUN ANOTHER QUERY FOR THIS  SO THAT STATUS CAN BE 1
			if ($database->database_error()) { 
			  $database->database_query("UPDATE se_documents SET document_status = '1' WHERE document_id='{$value->document_info['document_id']}'");
			}
			
		}
	  catch(Exception $e) {
	  	if($e->getCode() == 619) {
		  	$database->database_query("UPDATE se_documents SET document_status = '3' WHERE document_id='{$value->document_info['document_id']}'");
//		  	$subject = 'Document Delete';
//				$message = 'Document Delete';
//				send_generic($value->document_owner->user_info['user_email'], 'admin', $subject, $message);
	  	}
	  }
  }
  elseif ($stat == 'ERROR') {
  	$database->database_query("UPDATE se_documents SET document_status = '2' WHERE document_id='{$value->document_info['document_id']}'");
  	//SENDING AN EMAIL TO THE OWNER
//  	$subject = 'Document conversion failed at scribd';
//		$message = 'Document conversion failed at scribd';
//		send_generic($value->document_owner->user_info['user_email'], 'admin', $subject, $message);
  }
}




$where_browse_listing = $where . " AND (document_status = 1)"; 
$entries_per_page = 10;
// GET TOTAL DOCUMNETS
$total_entries = $document->documents_total($where_browse_listing);
$page_vars = make_page($total_entries, $entries_per_page, $p);



// GET DOCMENTS ARRAY 
$documents = $document->documents_list($page_vars[0], $entries_per_page, $s, $where_browse_listing, 1);


//GETTING TOTAL UNCATEGORIZED DOCUMENTS
$uncategorized_where_final = $where . " AND (document_category_id = '0') AND (document_status = 1) ";
$total_uncategorized = $document->documents_total($uncategorized_where_final);
$smarty->assign('total_uncategorized', $total_uncategorized);


//GETTING THE FEATURED DOCUMENTS
$fetured_where =  $where . " AND (document_featured = '1') AND (document_status = 1) ";
$document_featured = $document->documents_list(0, 10, 'document_datecreated DESC', $fetured_where, 1);


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

#CONSTRUCTING TAG CLOUD
$tag_array = array();
$query = "SELECT tag_name, count(t1.document_id) AS Frequency FROM se_documents INNER JOIN se_document_tags AS t1 ON se_documents.document_id = t1.document_id INNER JOIN se_documenttags AS t2 ON t1.tag_id = t2.id WHERE document_approved = 1 AND document_publish = 1 AND document_status = 1 GROUP BY tag_name ORDER BY Frequency DESC LIMIT 100";
$result = $database->database_query($query);
while($info = $database->database_fetch_assoc($result)) {
	$tag_array[$info['tag_name']] = $info['Frequency'];
}
$max_font_size = 18;
$min_font_size = 12;
$max_frequency = max(array_values($tag_array));
$min_frequency = min(array_values($tag_array));
$spread = $max_frequency - $min_frequency;
if($spread == 0) {
	$spread = 1;
}
$step = ($max_font_size - $min_font_size) / ($spread);

$tag_data = array('min_font_size' => $min_font_size, 'max_font_size' => $max_font_size, 'max_frequency' => $max_frequency, 'min_frequency' => $min_frequency, 'step' => $step);



// ASSIGN SMARTY VARIABLES AND SHOW VIEW DOCUMENTS PAGE
$smarty->assign('search', $search);
$smarty->assign('documents', $documents);
$smarty->assign('total_entries', $total_entries);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($documents));
$smarty->assign('s', $s);
$smarty->assign('v', $v);
$smarty->assign('i', $i);
$smarty->assign('tag_array', $tag_array);
$smarty->assign('tag_data', $tag_data);
$smarty->assign("categories", $categories);
$smarty->assign("featured", $document_featured);
$smarty->assign("tag_main", $tag);
$smarty->assign("excep_error", $excep_error);
$smarty->assign("excep_message", $excep_message);
include "footer.php";
?>