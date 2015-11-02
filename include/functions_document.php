<?php

//  THIS FILE CONTAINS FACEBOOK CONNECT RELATED FUNCTIONS
//  FUNCTIONS IN THIS FILE:
// search_documents()
// site_statistics_document
// deleteuser_document
// document_createslug


// THIS FUNCTION IS RUN DURING THE SEARCH PROCESS TO SEARCH THROUGH DOCUMENTS
// INPUT: $search_text REPRESENTING THE STRING TO SEARCH FOR
//	  $total_only REPRESENTING 1/0 DEPENDING ON WHETHER OR NOT TO RETURN JUST THE TOTAL RESULTS
//	  $search_objects REPRESENTING AN ARRAY CONTAINING INFORMATION ABOUT THE POSSIBLE OBJECTS TO SEARCH
//	  $results REPRESENTING THE ARRAY OF SEARCH RESULTS
//	  $total_results REPRESENTING THE TOTAL SEARCH RESULTS
// OUTPUT: 

function search_documents() {
		global $database, $url, $results_per_page, $p, $search_text, $t, $search_objects, $results, $total_results;
		
		// START TO QUERY BUILD
	$sql = "
    SELECT
      se_documents.document_id,
      se_documents.document_title,
      se_documents.document_slug,
      se_documents.document_description,
      se_users.user_id,
      se_users.user_username,
      se_users.user_photo,
      se_users.user_fname,
      se_users.user_lname
    FROM
      se_documents 
      INNER JOIN 
       se_users 
       ON se_documents.document_user_id=se_users.user_id
      INNER JOIN
       se_levels
      ON se_users.user_level_id=se_levels.level_id 
      LEFT JOIN
	       se_document_tags
	       ON se_documents.document_id = se_document_tags.document_id
	    LEFT JOIN se_documenttags
	       ON se_document_tags.tag_id = se_documenttags.id  
    WHERE

      (
        se_documents.document_search='1' ||
        se_levels.level_document_search='0'
      ) &&
      (
        document_title LIKE '%$search_text%' ||
        document_description LIKE '%$search_text%' ||
        document_fulltext LIKE '%$search_text%' ||
        tag_name LIKE '%$search_text%'
      )
      &&
      (
        se_documents.document_approved='1'
      )
      &&
      (
        se_documents.document_publish='1'
      )
      &&
      (
        se_documents.document_status='1'
      )
     GROUP BY se_documents.document_id 
  ";
	
	// GET TOTAL DOCUMNETS
	$total_documents = $database->database_num_rows($database->database_query($sql." LIMIT 201"));
	
	
		// IF NOT TOTAL ONLY
		if( $t=="document" )
	  {
		  //  DOCUMENTS PAGES
		  $start = ($p - 1) * $results_per_page;
		  $limit = $results_per_page + 1;
	    
		  // SEARCH DOCUMENTS
	    $sql .= " ORDER BY se_documents.document_id DESC LIMIT $start, $limit";
		  $resource = $database->database_query($sql) or die($database->database_error());
	    
		  while( $document_info=$database->database_fetch_assoc($resource) )
	    {
		    // CREATE AN OBJECT FOR AUTHOR
		    $profile = new se_user();
		    $profile->user_info['user_id']        = $document_info['user_id'];
		    $profile->user_info['user_username']  = $document_info['user_username'];
		    $profile->user_info['user_fname']     = $document_info['user_fname'];
		    $profile->user_info['user_lname']     = $document_info['user_lname'];
		    $profile->user_info['user_photo']     = $document_info['user_photo'];
		    $profile->user_displayname();
	      
	      $result_url = $url->url_create("document", $document_info['user_username'], $document_info['document_id'], $document_info['document_slug']);
	      $result_name = 650003007;
	      $result_desc = 650003008;
	      
	      
		    $results[] = array(
	        'result_url'    => $result_url,
					'result_icon'   => './images/icons/document60.gif',
					'result_name'   => $result_name,
					'result_name_1' => $document_info['document_title'],
					'result_desc'   => $result_desc,
					'result_desc_1' => $url->url_create('profile', $profile->user_info['user_username']),
					'result_desc_2' => $profile->user_displayname,
					'result_desc_3' => $document_info['document_description'],
	      );
		  }
	    
		  // SET TOTAL RESULTS
		  $total_results = $total_documents;
		}
		
	// SET ARRAY VALUES
	SE_Language::_preload_multi(650003009, 650003007, 650003008);
	if($total_documents > 200) { $total_documents = "200+"; }
  
	$search_objects[] = array(
    'search_type' => 'document',
    'search_lang' => 650003009,
    'search_total' => $total_documents
  );
}
	
// THIS FUNCTION IS RUN WHEN GENERATING SITE STATISTICS
// INPUT: 
// OUTPUT: 
function site_statistics_document(&$args)
{
  global $database;
  
  $statistics =& $args['statistics'];
  
  // NOTE: CACHING WILL BE HANDLED BY THE FUNCTION THAT CALLS THIS
  
  $total = $database->database_fetch_assoc($database->database_query("SELECT COUNT(document_id) AS total FROM se_documents WHERE (document_approved = '1') AND (document_publish = '1') AND (document_status = 1)"));
  $statistics['documents'] = array(
    'title' => 650003011,
    'stat'  => (int) ( isset($total['total']) ? $total['total'] : 0 )
  );
}

// END OF THE FUNCTION 

function document_lsettings($key, $type) {
		return false;
}
	
	
// THIS FUNCTION IS RUN WHEN A USER IS DELETED
// INPUT: $user_id REPRESENTING THE USER ID OF THE USER BEING DELETED
// OUTPUT: 
function deleteuser_document($user_id)
{
	global $database;
	
	//GETTING SCRIBD PARAMETERS
	$query = "SELECT * FROM se_document_parameters";
	$params = $database->database_fetch_assoc($database->database_query($query));
	
	$scribd_api_key = $params['api_key'];
	$scribd_secret = $params['secret_key'];
	$scribd = new Document($scribd_api_key, $scribd_secret, $user_id);
	
	//GET ALL DOCUMENTS OWNED BY USER	
	$result = $database->database_query("SELECT document_id, document_doc_id, document_filepath FROM se_documents WHERE document_user_id = '$user_id'");

	if($database->database_num_rows($result) > 0) {
		while($doc_info = $database->database_fetch_assoc($result)) {
			$scribd->delete($doc_info['document_doc_id']);
			$database->database_query("DELETE FROM se_documents_tags WHERE document_id = '{$doc_info['document_id']}'");
			if($doc_info['document_filepath'] != '') {
				unlink('.' . $doc_info['document_filepath']);
			}
		}
	}
	// DELETE DOCUMENT ENTRIES AND COMMENTS
	$database->database_query("DELETE FROM se_documents, se_documentcomments USING se_documents LEFT JOIN se_documentcomments ON se_documents.document_id=se_documentcomments.documentcomment_document_id WHERE se_documents.document_user_id='$user_id'");

	// DELETE COMMENTS POSTED BY USER
	$database->database_query("DELETE FROM se_documentcomments WHERE documentcomment_authoruser_id='$user_id'");

}



function document_createslug($string) {	
 $slug = trim($string);
 $slug= preg_replace('/[^a-zA-Z0-9 -]/','',$slug ); 
 $slug= str_replace(' ','-', $slug); 
 $slug= strtolower($slug); 
 return $slug;
}
?>