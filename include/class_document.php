<?php

// THIS CLASS INHERTING THE  SCRIBD LIBRARY
// documents_total
// documents_list
// document_delete
// document_view


include_once("scribd.php");


class Document extends Scribd  {

	public $user_id;
	var $document_exists;

	public function __construct($api_key = NULL, $secret = NULL, $user_id = 0, $document_id = NULL) {
		global $database, $user, $owner;
		$this->user_id = $user_id;
		$this->document_exists = FALSE;
		$this->api_key = $api_key;
		$this->secret = $secret;
		$this->url = "http://api.scribd.com/api?api_key=" . $api_key;
		parent::__construct($api_key, $secret);
		if( $document_id )
    {
      // GENERATE QUERY
      $sql = "
        SELECT
          se_documents.*,
          se_document_categories.category_name,
          se_document_categories.category_id
        FROM
          se_documents
          LEFT JOIN
            se_document_categories
            ON se_documents.document_category_id = se_document_categories.category_id
        WHERE
          document_id='{$document_id}'
      ";
      
      if( $user_id ) $sql .= " &&
        document_user_id='{$user_id}'
      ";
      
      $sql .= "
        LIMIT
          1
      ";
      
      $resource = $database->database_query($sql) or die($database->database_error()."<br /><b>SQL:</b> $sql");
      
      if( $database->database_num_rows($resource) )
      {
        $this->document_info = $database->database_fetch_assoc($resource);
        //FETCHING TAGS ASSOCIATED WITH THE DOCUMENT
		    $tag_array = array();
		    $query = "SELECT tag_name FROM se_document_tags INNER JOIN se_documenttags ON se_document_tags.tag_id = se_documenttags.id WHERE se_document_tags.document_id = '{$this->document_info['document_id']}'";   
		    $result = $database->database_query($query);
		    while($info = $database->database_fetch_assoc($result)) {
		   	 $tag_array[] = $info['tag_name'];
		    }
		    $this->document_info['tags'] = $tag_array;
	      $this->document_exists = TRUE;
        
        // GET OWNER INFO
        if( $user->user_exists && $this->document_info['document_user_id']==$user->user_info['user_id'] )
          $this->document_owner =& $user;
        elseif( $owner->user_exists && $this->document_info['document_user_id']==$owner->user_info['user_id'] )
          $this->document_owner =& $owner;
        else
          $this->document_owner = new se_user(array($this->document_info['document_user_id']));
      }
    }
	 }
	 
	 
	//RETURNS THE TOTAL NUMBER OF DOCUMENTS ACCORDING TO SEARCH CRITERIA
	function documents_total($where) {
		global $database;
		
    // BEGIN SQL QUERY
    $document_query = "SELECT se_documents.document_id FROM se_documents";
    
    // IF DO NOT SET ANY USER THEN JOIN THE TABLE WITH THE SE USERS TABLE      
	  if( !$this->user_id ) $document_query .= " LEFT JOIN se_users ON se_documents.document_user_id=se_users.user_id ";
	  
	  // FIND THE TAG
	    $document_query .= "
	    LEFT JOIN
	       se_document_tags
	       ON se_documents.document_id = se_document_tags.document_id
	    LEFT JOIN se_documenttags
	       ON se_document_tags.tag_id = se_documenttags.id
	     ";
	    
     // ADD WHERE STATMENT IF REQUIRED
    if($where != "" | $this->user_id != 0) { $document_query .= " WHERE"; }

    // ADD WHERE CONDITION, IF REQUIRED
    if($where != "") { $document_query .= " $where"; }
    
    // GROUP THE DOCUMENTS    
    $document_query .= " GROUP BY se_documents.document_id";
    
    // RETURN TOTAL DOCUMENTS
    $document_total = $database->database_num_rows($database->database_query($document_query));
    return $document_total;

	}
	
	
	//RETURN THE LIST OF THE DOCUMENTS ACCORDING TO SEARCH CRITERIA
	function documents_list($start, $limit, $sort_by = "created DESC", $where = "", $document_details = 0) {
		global $database, $user;
		
		// START THE SQL QUERY
	  $sql = "
      SELECT
        se_documents.*,
        se_documents.document_totalcomments AS total_comments,
        se_document_categories.category_name,
        se_document_categories.category_id
    ";
	  
	   // SELECT USER  DETAIL OF DOCUMENT IF REQUIRED
	  if($document_details == 1) $sql .= ",
        se_users.user_id,
        se_users.user_username,
        se_users.user_photo,
        se_users.user_fname,
        se_users.user_lname
    ";
	  
	  // ADD FROM TABLE 
	  $sql .= "
      FROM
        se_documents
    ";
	  
	  if($document_details == 1) $sql .= "
      LEFT JOIN
        se_users
        ON se_documents.document_user_id=se_users.user_id
    ";
    
	  // JOIN WITH THE CATEGORIES TABLE
	  
	  $sql .= "
	     LEFT JOIN
	       se_document_categories
	       ON se_documents.document_category_id = se_document_categories.category_id
	  ";
	  
	  // JOIN WITH THE TAGS TABLE
	  
	  $sql .= "
	    LEFT JOIN
	       se_document_tags
	       ON se_documents.document_id = se_document_tags.document_id
	    LEFT JOIN se_documenttags
	       ON se_document_tags.tag_id = se_documenttags.id
	     ";
	  
	  
	 // ADD WHERE STATMENT IF REQUIRED
	  if($where != "" | $this->user_id != 0) $sql .= "
      WHERE
    ";
	  
	  // CHECK FOR USER ID
	  if($this->user_id != 0) $sql .= "
        se_documents.document_user_id='{$this->user_id}'
    ";
	  
	  // ADD AND STATMENT IF REQUIRED
	  if($this->user_id != 0 & $where != "") $sql .= " AND";
    
	 // ADD WHERE CONDITION, IF REQUIRED
	  if($where != "") $sql .= "
        $where
    ";
    
	  // ADD ORDER, AND LIMIT CLAUSE
	  $sql .= "
	    GROUP BY
	      se_documents.document_id
      ORDER BY
        $sort_by
      LIMIT
        $start, $limit
    ";

     $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
     $document_array = array();
    
     while($document_info = $database->database_fetch_assoc($resource)) {

       // CREATE OBJECT FOR DOCUMENT
	    $document = new Document(null, null, $document_info['user_id']);
	    $document->document_exists = TRUE;
	    
	    // CREATE OBJECT FOR DOCUMENT CREATOR IF DOCUMENT DETAILS
	    if( $document_details )
      {
      	$document_creator = new se_user();
	      $document_creator->user_exists = TRUE;
	      $document_creator->user_info['user_id'] = $document_info['user_id'];
	      $document_creator->user_info['user_username'] = $document_info['user_username'];
	      $document_creator->user_info['user_photo'] = $document_info['user_photo'];
	      $document_creator->user_info['user_fname'] = $document_info['user_fname'];
	      $document_creator->user_info['user_lname'] = $document_info['user_lname'];
	      $document_creator->user_displayname();
        $document->document_owner =& $document_creator;
        unset($document_creator);
	    }
	    
	    //CHECKING IF THE ASSOCIATED CATEGORY HAS A MAIN CATEGORY
			$main_cat = $database->database_fetch_assoc($database->database_query("SELECT t2.category_id, t2.category_name FROM se_document_categories as t1 INNER JOIN se_document_categories t2 ON t1.cat_dependency = t2.category_id WHERE t1.category_id = '{$document_info['category_id']}'"));
			if(!empty($main_cat)) {
				$document_info['main_cat'] = $main_cat;
			}
			
	   //GETTING THE TAGS ASSOCIATED WITH THE DOCUMENT
	   $tag_array = array();
	   $query = "SELECT tag_name FROM se_document_tags INNER JOIN se_documenttags ON se_document_tags.tag_id = se_documenttags.id WHERE se_document_tags.document_id = '{$document_info['document_id']}'";   $result = $database->database_query($query);
	   while($info = $database->database_fetch_assoc($result)) {
	   	$tag_array[] = $info['tag_name'];
	   }
	   $document_info['tags'] = $tag_array;
	   $document->document_info = $document_info;
     $document_array[] = $document;
     
     }
     // RETURN ARRAY
	  return $document_array;
	}
	
	
	//THIS METHOD DELETES A DOCUMENT FOR SCRIBD'S SERVER AS WELL AS FROM LOCAL SERVER(IF EXISTS)
	function document_delete($doc_id) {
		global $database;
		
		// FIND THE DOCUMENT SETTINGS PARAMETER
      $query = "SELECT * FROM se_document_parameters";
      $params = $database->database_fetch_assoc($database->database_query($query));
      $scribd_api_key = $params['api_key'];
      $scribd_secret = $params['secret_key'];
      // CRAETE DOCUMENT OBJECT
      $scribd = new Document($scribd_api_key, $scribd_secret, $user->user_info['user_id']);
      
      // IF DOCUMNET ID IS NUMERIC
		  if(is_numeric($doc_id)) {
			
			$document = $database->database_fetch_assoc($database->database_query("SELECT document_user_id, document_doc_id, document_filepath FROM se_documents WHERE document_id = '$doc_id'"));
			if(!empty($document)) {
				$scribd->my_user_id = $document['document_user_id'];
				$data = $scribd->delete($document['document_doc_id']);
				if($data) {
					
					// DELETE THE DOCUMENTS
					$database->database_query("DELETE FROM se_documents WHERE document_id = '$doc_id'");
					
					// DELETE DOCUMENTS TAGS
					$database->database_query("DELETE FROM se_documents_tags WHERE document_id = '$doc_id'");
					
					// DELETE DOCUMENTS COMMENTS
					$database->database_query("DELETE FROM se_documentcomments WHERE documentcomment_document_id = '$doc_id'");
					
					// DELETE DOCUMENTS NOTIFICATIONS
					$database->database_query("
				    DELETE FROM
				      se_notifys
				    USING
				      se_notifys
				    LEFT JOIN
				      se_notifytypes
				      ON se_notifys.notify_notifytype_id=se_notifytypes.notifytype_id
				    WHERE
				      se_notifytypes.notifytype_name='documentcomment' AND
				      notify_object_id='$doc_id'
				  ");
					
					// UNLINK THE DOCUMENTS 
					if(!empty($document['document_filepath']))
					unlink('.'. $document['document_filepath']);
				}
			} 
			else 
			  return FALSE;
			
		}
		elseif (is_array($doc_id)) { // IF MORE THAN ONE DOCUMENTS IS GOING TO DELETE AT A ONE TIME
			foreach($doc_id as $document_id) {
				$document = $database->database_fetch_assoc($database->database_query("SELECT document_user_id, document_doc_id, document_filepath FROM se_documents WHERE document_id = '$document_id'"));
				if(!empty($document)) {
					$scribd->my_user_id = $document['document_user_id'];
					$data = $scribd->delete($document['document_doc_id']);
					if($data) {
							// DELETE THE DOCUMENTS
						$database->database_query("DELETE FROM se_documents WHERE document_id = '$document_id'");
						
						// DELETE DOCUMENTS TAGS
						$database->database_query("DELETE FROM se_documents_tags WHERE document_id = '$document_id'");
						
						// DELETE DOCUMENTS COMMENTS
					$database->database_query("DELETE FROM se_documentcomments WHERE documentcomment_document_id = '$document_id'");
					
					// DELETE DOCUMENTS NOTIFICATIONS
					$database->database_query("
				    DELETE FROM
				      se_notifys
				    USING
				      se_notifys
				    LEFT JOIN
				      se_notifytypes
				      ON se_notifys.notify_notifytype_id=se_notifytypes.notifytype_id
				    WHERE
				      se_notifytypes.notifytype_name='documentcomment' AND
				      notify_object_id='$document_id'
				  ");
					// UNLINK THE DOCUMENTS 
						if(!empty($document['document_filepath']))
						unlink('.'. $document['document_filepath']);
					}
				}
			}
		}
		else 
			return FALSE;
	}
	
	//THIS METHOD USE FOR VIEW THE DOCUMENTS 
	function document_view()
  {
	  global $database;
    
    if( !$this->document_exists || !$this->document_info['document_id'] )
      return FALSE;
    
	  // UPDATE QUERY
    $sql = "
      UPDATE
        se_documents
      SET
        document_views=document_views+1
      WHERE
        document_id='{$this->document_info['document_id']}'
      LIMIT
        1
    ";
    
	  // RUN SQL QUERY
    $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
    
    return (bool) $database->database_affected_rows($resource);
  }
}
?>