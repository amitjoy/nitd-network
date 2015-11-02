<?

include_once("class_radcodes.php");


class rc_articlecats extends rc_categories {

  var $pk = 'articlecat_id'; // primary
  var $pd = 'articlecat_dependency'; // dependency col
  var $pt = 'articlecat_title'; // title col
  var $table = 'se_articlecats';
  
  function get_category_menu($options = array())
  {
  	$order = isset($options['order']) ? $options['order'] : 'articlecat_title';
  	
  	$raw_cats = $this->get_records("ORDER BY $order");
  	
  	$stats = $this->count_category_articles($options['count_criteria']);
  	
  	$categories = array();
  	
  	// build subcats
  	foreach ($raw_cats as $cat) {
  		if ($cat['articlecat_dependency'] != 0) {
  			$subcats[$cat['articlecat_dependency']][] = array(
           'subcategory_id' => $cat['articlecat_id'],
           'subcategory_title' => $cat['articlecat_title'],
           'subcategory_totalarticles' => ($stats[$cat['articlecat_id']] ? $stats[$cat['articlecat_id']] : 0)
  			);
  		}
  	}
  	
  	// build main cat
  	foreach ($raw_cats as $cat) {
  		if ($cat['articlecat_dependency'] == 0) {
  			$articlecat_totalarticles = $stats[$cat['articlecat_id']] ? $stats[$cat['articlecat_id']] : 0;
  			$articlecat_subcats = isset($subcats[$cat['articlecat_id']]) ? $subcats[$cat['articlecat_id']] : array();
  			
  			$expanded = $options['expanded_category_id'] == $cat['articlecat_id'];
  			foreach ($articlecat_subcats as $sc) {
  				$articlecat_totalarticles += $sc['subcategory_totalarticles'];
  				$expanded = $expanded || $options['expanded_category_id'] == $sc['subcategory_id'];
  			}

  			$categories[] = array(
					"articlecat_id" => $cat['articlecat_id'],
					"articlecat_title" => $cat['articlecat_title'],
					"articlecat_totalarticles" => $articlecat_totalarticles,
					"articlecat_expanded" => $expanded ? 1 : 0,
					"articlecat_subcats" => $articlecat_subcats
  			);
  		}
  	}
  	
  	return $categories;
  	
  	
  	
  }
  
  function count_category_articles($criteria = null)
  {
  	$stats = array();
  	if (strlen($criteria)) $where = " WHERE $criteria";
  	$query = "SELECT article_articlecat_id as category_id, COUNT(article_id) as total FROM se_articles $where GROUP BY article_articlecat_id";
  	$res = $this->db->database_query($query);
  	while ($row = $this->db->database_fetch_assoc($res)) {
  		$stats[$row['category_id']] = $row['total'];
  	}
  	return $stats;
  }
  
}




class rc_article {

	// INITIALIZE VARIABLES
	var $is_error;			// DETERMINES WHETHER THERE IS AN ERROR OR NOT
	var $error_message;		// CONTAINS RELEVANT ERROR MESSAGE

	var $user_id;			// CONTAINS THE USER ID OF THE USER WHOSE ARTICLES WE ARE EDITING OR THE LOGGED-IN USER
	var $is_member;			// DETERMINES WHETHER USER IS IN THE ARTICLEMEMBER TABLE OR NOT

	var $article_exists;		// DETERMINES WHETHER THE ARTICLE HAS BEEN SET AND EXISTS OR NOT

	var $article_info;		// CONTAINS THE ARTICLE INFO OF THE ARTICLE WE ARE EDITING
	var $articleowner_level_info;	// CONTAINS THE ARTICLE CREATOR'S LEVEL INFO
	var $articlemember_info;		// CONTAINS THE ARTICLE MEMBER INFO FOR THE LOGGED-IN USER


	// THIS METHOD SETS INITIAL VARS
	// INPUT: $user_id (OPTIONAL) REPRESENTING THE USER ID OF THE USER WHOSE ARTICLES WE ARE CONCERNED WITH
	//	  $article_id (OPTIONAL) REPRESENTING THE ARTICLE ID OF THE ARTICLE WE ARE CONCERNED WITH
	// OUTPUT: 
	function rc_article($user_id = 0, $article_id = 0) {
	  global $database, $user;

	  $this->user_id = $user_id;
	  $this->article_exists = 0;
	  $this->is_member = 0;

	  if($article_id != 0) {
	    $article = $database->database_query("SELECT * FROM se_articles WHERE article_id='$article_id'");
	    if($database->database_num_rows($article) == 1) {
	      $this->article_exists = 1;
	      $this->article_info = $database->database_fetch_assoc($article);
	      
	      // GET LEVEL INFO
	      if($this->article_info[article_user_id] == $user->user_info[user_id]) {
	        $this->articleowner_level_info = $user->level_info;
	      } else {
		      $this->articleowner_level_info = $database->database_fetch_assoc($database->database_query("SELECT se_levels.* FROM se_users LEFT JOIN se_levels ON se_users.user_level_id=se_levels.level_id WHERE se_users.user_id='".$this->article_info[article_user_id]."'"));
	      }

	    }
	  }

	} // END se_article() METHOD








	// THIS METHOD RETURNS THE TOTAL NUMBER OF ARTICLES
	// INPUT: $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
	//	  $article_details (OPTIONAL) REPRESENTING A BOOLEAN THAT DETERMINES WHETHER TO RETRIEVE ARTICLE CREATOR
	// OUTPUT: AN INTEGER REPRESENTING THE NUMBER OF ARTICLES
	function article_total($where = "", $article_details = 0) {
	  global $database;
	  
	  // BEGIN ENTRY QUERY
	  $article_query = "SELECT article_id FROM se_articles LEFT JOIN se_articlecats ON se_articles.article_articlecat_id=se_articlecats.articlecat_id";

	  // IF NO USER ID SPECIFIED, JOIN TO USER TABLE
	  if($article_details == 1) { $article_query .= " LEFT JOIN se_users ON se_articles.article_user_id=se_users.user_id"; }	  
	  
	  // ADD WHERE IF NECESSARY
	  if($where != "" | $this->user_id != 0) { $article_query .= " WHERE"; }

	  // ENSURE USER ID IS NOT EMPTY
	  if($this->user_id != 0) { $article_query .= " article_user_id='".$this->user_id."'"; }

	  // INSERT AND IF NECESSARY
	  if($this->user_id != 0 & $where != "") { $article_query .= " AND"; }

	  // ADD WHERE CLAUSE, IF NECESSARY
	  if($where != "") { $article_query .= " $where"; }

	  // GET AND RETURN TOTAL BLOG ENTRIES
	  $article_total = $database->database_num_rows($database->database_query($article_query));
	  return $article_total;
	  
	} // END article_total() METHOD








	// THIS METHOD RETURNS AN ARRAY OF ARTICLES
	// INPUT: $start REPRESENTING THE ARTICLE TO START WITH
	//	  $limit REPRESENTING THE NUMBER OF ARTICLES TO RETURN
	//	  $sort_by (OPTIONAL) REPRESENTING THE ORDER BY CLAUSE
	//	  $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
	//	  $article_details (OPTIONAL) REPRESENTING A BOOLEAN THAT DETERMINES WHETHER TO RETRIEVE ARTICLE CREATOR
	// OUTPUT: AN ARRAY OF ARTICLES
	function article_list($start, $limit, $sort_by = "article_id DESC", $where = "", $article_details = 0) {
	  global $database, $user, $owner;


	  // BEGIN QUERY
	  $article_query = "SELECT se_articles.*, se_articlecats.articlecat_title, count(articlecomment_id) AS total_comments";
	
	  // IF NO USER ID SPECIFIED, RETRIEVE USER INFORMATION
	  if($this->user_id == 0) { $article_query .= ", se_users.user_id, se_users.user_username, se_users.user_photo, se_users.user_fname, se_users.user_lname "; }

	  // CONTINUE QUERY
	  $article_query .= " FROM se_articles LEFT JOIN se_articlecats ON se_articles.article_articlecat_id=se_articlecats.articlecat_id LEFT JOIN se_articlecomments ON se_articles.article_id=se_articlecomments.articlecomment_article_id";

	  // IF NO USER ID SPECIFIED, JOIN TO USER TABLE
	  if($this->user_id == 0) { $article_query .= " LEFT JOIN se_users ON se_articles.article_user_id=se_users.user_id"; }

	  // ADD WHERE IF NECESSARY
	  if($where != "" | $this->user_id != 0) { $article_query .= " WHERE"; }

	  // ENSURE USER ID IS NOT EMPTY
	  if($this->user_id != 0) { $article_query .= " article_user_id='".$this->user_id."'"; }

	  // INSERT AND IF NECESSARY
	  if($this->user_id != 0 & $where != "") { $article_query .= " AND"; }

	  // ADD WHERE CLAUSE, IF NECESSARY
	  if($where != "") { $article_query .= " $where"; }

	  // ADD GROUP BY, ORDER, AND LIMIT CLAUSE
	  $article_query .= " GROUP BY article_id ORDER BY $sort_by LIMIT $start, $limit";

	  // RUN QUERY
	  $articleentries = $database->database_query($article_query);
	  
	  $rc_tag = new rc_articletag();
	  
	  // GET BLOG ENTRIES INTO AN ARRAY
	  $article_array = Array();
	  while($article_info = $database->database_fetch_assoc($articleentries)) {

	    // CREATE OBJECT FOR EVENT
	    $article = new rc_article($article_info[user_id]);
	    $article->article_exists = 1;
	    $article->article_info= $article_info;	    
	    
	    // CONVERT HTML CHARACTERS BACK
	    //$article_body = str_replace("\r\n", "", html_entity_decode($article_info[article_body]));

	    // IF NO USER ID SPECIFIED, CREATE OBJECT FOR AUTHOR
	    if($this->user_id == 0) {
	      $author = new se_user();
	      $author->user_exists = 1;
	      $author->user_info[user_id] = $article_info[user_id];
	      $author->user_info[user_username] = $article_info[user_username];
	      $author->user_info[user_photo] = $article_info[user_photo];
        $author->user_info[user_fname] = $article_info[user_fname];
        $author->user_info[user_lname] = $article_info[user_lname];
	    // OTHERWISE, SET AUTHOR TO OWNER/LOGGED-IN USER
	    } elseif($owner->user_exists != 0 & $owner->user_info[user_id] == $article_info[article_user_id]) {
	      $author = $owner;
	    } elseif($user->user_exists != 0 & $user->user_info[user_id] == $article_info[article_user_id]) {
	      $author = $user;
	    }
      $author->user_displayname();
	    
	    // SET EVENT ARRAY
	    $article_array[] = Array('article' => $article,
				'tags' => $rc_tag->get_object_tags($article_info['article_id']),
				'article_author' => $author);	    
	  }

	  // RETURN ARRAY
	  return $article_array;

	} // END article_list() METHOD








	// THIS METHOD CREATES A NEW ARTICLE
	// INPUT: $article_title REPRESENTING THE ARTICLE TITLE
	//	  $article_body REPRESENTING THE ARTICLE DESCRIPTION
	//	  $articlecat_id REPRESENTING THE ARTICLE CATEGORY ID
	//	  $article_date_start REPRESENTING THE ARTICLE'S START TIMESTAMP
	//	  $article_search REPRESENTING WHETHER THE ARTICLE SHOULD BE SEARCHABLE
	//	  $article_privacy REPRESENTING THE PRIVACY OF THE ARTICLE
	//	  $article_comments REPRESENTING WHO CAN POST COMMENTS ON THE ARTICLE
	//	  $article_draft REPRESENTING WHETHER THE ARTICLE IS A DRAFT
	
	// OUTPUT: THE NEWLY CREATED ARTICLE'S ARTICLE ID
	function article_create($article_title, $article_body, $articlecat_id, $article_date_start, $article_draft, $article_approved, $article_search, $article_privacy, $article_comments) {
	  global $database;

	  $article_slug = $this->generate_slug($article_title);
	  
	  // ADD ROW TO ARTICLES TABLE
	  $database->database_query("INSERT INTO se_articles (article_user_id, article_articlecat_id, article_title, article_body, article_date_start, article_draft, article_approved, article_search, article_privacy, article_comments, article_slug) VALUES ('".$this->user_id."', '$articlecat_id', '$article_title', '$article_body', '$article_date_start', '$article_draft', '$article_approved', '$article_search', '$article_privacy', '$article_comments', '$article_slug')");
	  $article_id = $database->database_insert_id();

	  // MAKE CREATOR A MEMBER
	  $database->database_query("INSERT INTO se_articlemembers (articlemember_user_id, articlemember_article_id, articlemember_status) VALUES ('".$this->user_id."', '$article_id', '1')");

	  // ADD ARTICLE STYLES ROW
	  $database->database_query("INSERT INTO se_articlestyles (articlestyle_article_id) VALUES ('$article_id')");

	  // ADD ARTICLE ALBUM
	  $database->database_query("INSERT INTO se_articlealbums (articlealbum_article_id, articlealbum_datecreated, articlealbum_dateupdated, articlealbum_title, articlealbum_desc, articlealbum_search, articlealbum_privacy, articlealbum_comments) VALUES ('$article_id', '".time()."', '".time()."', '', '', '$article_search', '$article_privacy', '$article_comments')");

	  // ADD ARTICLE DIRECTORY
	  $article_directory = $this->article_dir($article_id);
	  $article_path_array = explode("/", $article_directory);
	  array_pop($article_path_array);
	  array_pop($article_path_array);
	  $subdir = implode("/", $article_path_array)."/";
	  if(!is_dir($subdir)) { 
	    mkdir($subdir, 0777); 
	    chmod($subdir, 0777); 
	    $handle = fopen($subdir."index.php", 'x+');
	    fclose($handle);
	  }
	  mkdir($article_directory, 0777);
	  chmod($article_directory, 0777);
	  $handle = fopen($article_directory."/index.php", 'x+');
	  fclose($handle);

	  return $article_id;

	} // END article_create() METHOD









	// THIS METHOD DELETES AN ARTICLE
	// INPUT: $article_id (OPTIONAL) REPRESENTING THE ID OF THE ARTICLE TO DELETE
	// OUTPUT:
	function article_delete($article_id = 0) {
	  global $database;

	  if($article_id == 0) { $article_id = $this->article_info[article_id]; }

	  $database->database_query("DELETE FROM se_articletags WHERE tag_object_id='$article_id'");
	  
	  // DELETE ARTICLE ALBUM, MEDIA, MEDIA COMMENTS
	  $database->database_query("DELETE FROM se_articlealbums, se_articlemedia, se_articlemediacomments USING se_articlealbums LEFT JOIN se_articlemedia ON se_articlealbums.articlealbum_id=se_articlemedia.articlemedia_articlealbum_id LEFT JOIN se_articlemediacomments ON se_articlemedia.articlemedia_id=se_articlemediacomments.articlemediacomment_articlemedia_id WHERE se_articlealbums.articlealbum_article_id='$article_id'");

	  // DELETE ARTICLE ROW
	  $database->database_query("DELETE FROM se_articles WHERE se_articles.article_id='$article_id'");

	  // DELETE ARTICLE COMMENTS
	  $database->database_query("DELETE FROM se_articlecomments WHERE se_articlecomments.articlecomment_article_id='$article_id'");

	  // DELETE ARTICLE'S FILES
	  if(is_dir($this->article_dir($article_id))) {
	    $dir = $this->article_dir($article_id);
	  } else {
	    $dir = ".".$this->article_dir($article_id);
	  }
	  if($dh = opendir($dir)) {
	    while(($file = readdir($dh)) !== false) {
	      if($file != "." & $file != "..") {
	        unlink($dir.$file);
	      }
	    }
	    closedir($dh);
	  }
	  rmdir($dir);

	} // END article_delete() METHOD








	// THIS METHOD DELETES SELECTED ARTICLES
	// INPUT: $start REPRESENTING THE GROUP TO START WITH
	//	  $limit REPRESENTING THE NUMBER OF GROUPS TO RETURN
	//	  $sort_by (OPTIONAL) REPRESENTING THE ORDER BY CLAUSE
	//	  $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
	//	  $article_details (OPTIONAL) REPRESENTING A BOOLEAN THAT DETERMINES WHETHER TO RETRIEVE ARTICLE CREATOR
	// OUTPUT: AN ARRAY OF ARTICLES
	function article_delete_selected($start, $limit, $sort_by = "article_id DESC", $where = "", $article_details = 0) {
	  global $database, $user;

	  // BEGIN QUERY
	  $article_query = "SELECT se_articles.*";

	  // SELECT RELEVANT ARTICLE DETAILS IF NECESSARY
	  if($article_details == 1) { $article_query .= ", se_users.user_id, se_users.user_username"; }

	  // IF USER ID NOT EMPTY, GET USER AS MEMBER
	  if($this->user_id != 0) { $article_query .= ", se_articlemembers.articlemember_status"; }

	  // CONTINUE QUERY
	  $article_query .= " FROM";

	  // IF USER ID NOT EMPTY, SELECT FROM ARTICLEMEMBER TABLE
	  if($this->user_id != 0) { 
	    $article_query .= " se_articlemembers LEFT JOIN se_articles ON se_articlemembers.articlemember_article_id=se_articles.article_id "; 
	  } else {
	    $article_query .= " se_articles";
	  }

	  // CONTINUE QUERY IF NECESSARY
	  if($article_details == 1) { $article_query .= " LEFT JOIN se_users ON se_articles.article_user_id=se_users.user_id"; }

	  // ADD WHERE IF NECESSARY
	  if($where != "" | $this->user_id != 0) { $article_query .= " WHERE"; }

	  // IF USER ID NOT EMPTY, MAKE SURE USER IS A MEMBER
	  if($this->user_id != 0) { $article_query .= " se_articlemembers.articlemember_user_id='".$this->user_id."' AND se_articlemembers.articlemember_status<>'-1'"; }

	  // INSERT AND IF NECESSARY
	  if($this->user_id != 0 & $where != "") { $article_query .= " AND"; }

	  // ADD WHERE CLAUSE, IF NECESSARY
	  if($where != "") { $article_query .= " $where"; }

	  // ADD GROUP BY, ORDER, AND LIMIT CLAUSE
	  $article_query .= " GROUP BY article_id ORDER BY $sort_by LIMIT $start, $limit";

	  // RUN QUERY
	  $articles = $database->database_query($article_query);

	  // GET ARTICLES INTO AN ARRAY
	  while($article_info = $database->database_fetch_assoc($articles)) {
    	    $var = "delete_article_".$article_info[article_id];
	    if($_POST[$var] == 1) { $this->article_delete($article_info[article_id]); }
	  }

	} // END article_delete_selected() METHOD








	// THIS METHOD UPDATES THE ARTICLE'S LAST UPDATE DATE
	// INPUT: 
	// OUTPUT: 
	function article_lastupdate() {
	  global $database;

	  $database->database_query("UPDATE se_articles SET article_dateupdated='".time()."' WHERE article_id='".$this->article_info[article_id]."'");
	  
	} // END article_lastupdate() METHOD



  
  function article_owner()
  {
    if (!$this->article_owner) {
      $this->article_owner = new se_user(Array($this->article_info[article_user_id]));
      $this->article_owner->user_displayname();
    }
  }



	// THIS METHOD RETURNS MAXIMUM PRIVACY LEVEL VIEWABLE BY A USER WITH REGARD TO THE CURRENT ARTICLE
	// INPUT: $user REPRESENTING A USER OBJECT
	//	  $allowable_privacy (OPTIONAL) REPRESENTING A STRING OF ALLOWABLE PRIVACY SETTINGS
	// OUTPUT: RETURNS THE INTEGER REPRESENTING THE MAXIMUM PRIVACY LEVEL VIEWABLE BY A USER WITH REGARD TO THE CURRENT ARTICLE
	function article_privacy_max($user, $allowable_privacy = "0123456") {
	  global $database;

	  $this->article_owner();
	  
	  $privacy_level = 6;
	  if ($this->article_owner->user_exists) {
	    $privacy_level = $this->article_owner->user_privacy_max($user, $allowable_privacy);
	  }

	  return $privacy_level;

	} // END article_privacy_max() METHOD

	// THIS METHOD RETURNS THE PATH TO THE GIVEN ARTICLE'S DIRECTORY
	// INPUT: $article_id (OPTIONAL) REPRESENTING A ARTICLE'S ARTICLE_ID
	// OUTPUT: A STRING REPRESENTING THE RELATIVE PATH TO THE ARTICLE'S DIRECTORY
	function article_dir($article_id = 0) {

	  if($article_id == 0 & $this->article_exists) { $article_id = $this->article_info[article_id]; }

	  $subdir = $article_id+999-(($article_id-1)%1000);
	  $articledir = "./uploads_article/$subdir/$article_id/";
	  return $articledir;

	} // END article_dir() METHOD








	// THIS METHOD OUTPUTS THE PATH TO THE ARTICLE'S PHOTO OR THE GIVEN NOPHOTO IMAGE
	// INPUT: $nophoto_image (OPTIONAL) REPRESENTING THE PATH TO AN IMAGE TO OUTPUT IF NO PHOTO EXISTS
	// OUTPUT: A STRING CONTAINING THE PATH TO THE ARTICLE'S PHOTO
	function article_photo($nophoto_image = "") {

	  $article_photo = $this->article_dir($this->article_info[article_id]).$this->article_info[article_photo];
	  if(!file_exists($article_photo) | $this->article_info[article_photo] == "") { $article_photo = $nophoto_image; }
	  return $article_photo;
	  
	} // END article_photo() METHOD








	// THIS METHOD UPLOADS AN ARTICLE PHOTO ACCORDING TO SPECIFICATIONS AND RETURNS ARTICLE PHOTO
	// INPUT: $photo_name REPRESENTING THE NAME OF THE FILE INPUT
	// OUTPUT: 
	function article_photo_upload($photo_name) {
	  global $database, $url;

	  // SET KEY VARIABLES
	  $file_maxsize = "4194304";
	  $file_exts = explode(",", str_replace(" ", "", strtolower($this->articleowner_level_info[level_article_photo_exts])));
	  $file_types = explode(",", str_replace(" ", "", strtolower("image/jpeg, image/jpg, image/jpe, image/pjpeg, image/pjpg, image/x-jpeg, x-jpg, image/gif, image/x-gif, image/png, image/x-png")));
	  $file_maxwidth = $this->articleowner_level_info[level_article_photo_width];
	  $file_maxheight = $this->articleowner_level_info[level_article_photo_height];
	  $photo_newname = "0_".rand(1000, 9999).".jpg";
	  $file_dest = $this->article_dir($this->article_info[article_id]).$photo_newname;

	  $new_photo = new se_upload();
	  $new_photo->new_upload($photo_name, $file_maxsize, $file_exts, $file_types, $file_maxwidth, $file_maxheight);

	  // UPLOAD AND RESIZE PHOTO IF NO ERROR
	  if($new_photo->is_error == 0) {

	    // DELETE OLD AVATAR IF EXISTS
	    $this->article_photo_delete();

	    // CHECK IF IMAGE RESIZING IS AVAILABLE, OTHERWISE MOVE UPLOADED IMAGE
	    if($new_photo->is_image == 1) {
	      $new_photo->upload_photo($file_dest);
	    } else {
	      $new_photo->upload_file($file_dest);
	    }

	    // UPDATE ARTICLE INFO WITH IMAGE IF STILL NO ERROR
	    if($new_photo->is_error == 0) {
	      $database->database_query("UPDATE se_articles SET article_photo='$photo_newname' WHERE article_id='".$this->article_info[article_id]."'");
	      $this->article_info[article_photo] = $photo_newname;
	    }
	  }
	
	  $this->is_error = $new_photo->is_error;
	  $this->error_message = $new_photo->error_message;
	  
	} // END article_photo_upload() METHOD








	// THIS METHOD DELETES A ARTICLE PHOTO
	// INPUT: 
	// OUTPUT: 
	function article_photo_delete() {
	  global $database;
	  $article_photo = $this->article_photo();
	  if($article_photo != "") {
	    unlink($article_photo);
	    $database->database_query("UPDATE se_articles SET article_photo='' WHERE article_id='".$this->article_info[article_id]."'");
	    $this->article_info[article_photo] = "";
	  }
	} // END article_photo_delete() METHOD








	// THIS METHOD UPLOADS MEDIA TO A ARTICLE ALBUM
	// INPUT: $file_name REPRESENTING THE NAME OF THE FILE INPUT
	//	  $articlealbum_id REPRESENTING THE ID OF THE ARTICLE ALBUM TO UPLOAD THE MEDIA TO
	//	  $space_left REPRESENTING THE AMOUNT OF SPACE LEFT
	// OUTPUT:
	function article_media_upload($file_name, $articlealbum_id, &$space_left) {
	  global $class_article, $database, $url;

	  // SET KEY VARIABLES
	  $file_maxsize = $this->articleowner_level_info[level_article_album_maxsize];
	  $file_exts = explode(",", str_replace(" ", "", strtolower($this->articleowner_level_info[level_article_album_exts])));
	  $file_types = explode(",", str_replace(" ", "", strtolower($this->articleowner_level_info[level_article_album_mimes])));
	  $file_maxwidth = $this->articleowner_level_info[level_article_album_width];
	  $file_maxheight = $this->articleowner_level_info[level_article_album_height];

	  $new_media = new se_upload();
	  $new_media->new_upload($file_name, $file_maxsize, $file_exts, $file_types, $file_maxwidth, $file_maxheight);

	  // UPLOAD AND RESIZE PHOTO IF NO ERROR
	  if($new_media->is_error == 0) {

	    // INSERT ROW INTO MEDIA TABLE
	    $database->database_query("INSERT INTO se_articlemedia (
							articlemedia_articlealbum_id,
							articlemedia_date
							) VALUES (
							'$articlealbum_id',
							'".time()."'
							)");
	    $articlemedia_id = $database->database_insert_id();

	    // CHECK IF IMAGE RESIZING IS AVAILABLE, OTHERWISE MOVE UPLOADED IMAGE
	    if($new_media->is_image == 1) {
	      $file_dest = $this->article_dir($this->article_info[article_id]).$articlemedia_id.".jpg";
	      $thumb_dest = $this->article_dir($this->article_info[article_id]).$articlemedia_id."_thumb.jpg";
	      $new_media->upload_photo($file_dest);
	      $new_media->upload_photo($thumb_dest, 200, 200);
	      $file_ext = "jpg";
	      $file_filesize = filesize($file_dest);
	    } else {
	      $file_dest = $this->article_dir($this->article_info[article_id]).$articlemedia_id.".".$new_media->file_ext;
	      $new_media->upload_file($file_dest);
	      $file_ext = $new_media->file_ext;
	      $file_filesize = filesize($file_dest);
	    }

	    // CHECK SPACE LEFT
	    if($file_filesize > $space_left) {
	      $new_media->is_error = 1;
	      $new_media->error_message = $class_article[1].$_FILES[$file_name]['name'];
	    } else {
	      $space_left = $space_left-$file_filesize;
	    }

	    // DELETE FROM DATABASE IF ERROR
	    if($new_media->is_error != 0) {
	      $database->database_query("DELETE FROM se_articlemedia WHERE articlemedia_id='$articlemedia_id' AND articlemedia_articlealbum_id='$articlealbum_id'");
	      @unlink($file_dest);

	    // UPDATE ROW IF NO ERROR
	    } else {
	      $database->database_query("UPDATE se_articlemedia SET articlemedia_ext='$file_ext', articlemedia_filesize='$file_filesize' WHERE articlemedia_id='$articlemedia_id' AND articlemedia_articlealbum_id='$articlealbum_id'");
	    }
	  }
	
	  // RETURN FILE STATS
	  $file = Array('is_error' => $new_media->is_error,
			'error_message' => $new_media->error_message,
			'articlemedia_id' => $articlemedia_id,
			'articlemedia_ext' => $file_ext,
			'articlemedia_filesize' => $file_filesize);
	  return $file;

	} // END article_media_upload() METHOD








	// THIS METHOD RETURNS THE SPACE USED
	// INPUT: $articlealbum_id (OPTIONAL) REPRESENTING THE ID OF THE ALBUM TO CALCULATE
	// OUTPUT: AN INTEGER REPRESENTING THE SPACE USED
	function article_media_space($articlealbum_id = 0) {
	  global $database;

	  // BEGIN QUERY
	  $articlemedia_query = "SELECT sum(se_articlemedia.articlemedia_filesize) AS total_space";
	
	  // CONTINUE QUERY
	  $articlemedia_query .= " FROM se_articlealbums LEFT JOIN se_articlemedia ON se_articlealbums.articlealbum_id=se_articlemedia.articlemedia_articlealbum_id";

	  // ADD WHERE IF NECESSARY
	  if($this->article_exists != 0 | $articlealbum_id != 0) { $articlemedia_query .= " WHERE"; }

	  // IF ARTICLE EXISTS, SPECIFY ARTICLE ID
	  if($this->article_exists != 0) { $articlemedia_query .= " se_articlealbums.articlealbum_article_id='".$this->article_info[article_id]."'"; }

	  // ADD AND IF NECESSARY
	  if($this->article_exists != 0 & $articlealbum_id != 0) { $articlemedia_query .= " AND"; }

	  // SPECIFY ALBUM ID IF NECESSARY
	  if($articlealbum_id != 0) { $articlemedia_query .= " se_articlealbums.articlealbum_id='$articlealbum_id'"; }

	  // GET AND RETURN TOTAL SPACE USED
	  $space_info = $database->database_fetch_assoc($database->database_query($articlemedia_query));
	  return $space_info[total_space];

	} // END article_media_space() METHOD








	// THIS METHOD RETURNS THE NUMBER OF ARTICLE MEDIA
	// INPUT: $articlealbum_id (OPTIONAL) REPRESENTING THE ID OF THE ARTICLE ALBUM TO CALCULATE
	// OUTPUT: AN INTEGER REPRESENTING THE NUMBER OF FILES
	function article_media_total($articlealbum_id = 0) {
	  global $database;

	  // BEGIN QUERY
	  $articlemedia_query = "SELECT count(se_articlemedia.articlemedia_id) AS total_files";
	
	  // CONTINUE QUERY
	  $articlemedia_query .= " FROM se_articlealbums LEFT JOIN se_articlemedia ON se_articlealbums.articlealbum_id=se_articlemedia.articlemedia_articlealbum_id";

	  // ADD WHERE IF NECESSARY
	  if($this->article_exists != 0 | $articlealbum_id != 0) { $articlemedia_query .= " WHERE"; }

	  // IF ARTICLE EXISTS, SPECIFY ARTICLE ID
	  if($this->article_exists != 0) { $articlemedia_query .= " se_articlealbums.articlealbum_article_id='".$this->article_info[article_id]."'"; }

	  // ADD AND IF NECESSARY
	  if($this->article_exists != 0 & $articlealbum_id != 0) { $articlemedia_query .= " AND"; }

	  // SPECIFY ALBUM ID IF NECESSARY
	  if($articlealbum_id != 0) { $articlemedia_query .= " se_articlealbums.articlealbum_id='$articlealbum_id'"; }

	  // GET AND RETURN TOTAL FILES
	  $file_info = $database->database_fetch_assoc($database->database_query($articlemedia_query));
	  return $file_info[total_files];

	} // END article_media_total() METHOD








	// THIS METHOD RETURNS AN ARRAY OF ARTICLE MEDIA
	// INPUT: $start REPRESENTING THE ARTICLE MEDIA TO START WITH
	//	  $limit REPRESENTING THE NUMBER OF ARTICLE MEDIA TO RETURN
	//	  $sort_by (OPTIONAL) REPRESENTING THE ORDER BY CLAUSE
	//	  $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
	// OUTPUT: AN ARRAY OF ARTICLE MEDIA
	function article_media_list($start, $limit, $sort_by = "articlemedia_id DESC", $where = "") {
	  global $database;

	  // BEGIN QUERY
	  $articlemedia_query = "SELECT se_articlemedia.*, se_articlealbums.articlealbum_id, se_articlealbums.articlealbum_article_id, se_articlealbums.articlealbum_title, count(se_articlemediacomments.articlemediacomment_id) AS total_comments";
	
	  // CONTINUE QUERY
	  $articlemedia_query .= " FROM se_articlemedia LEFT JOIN se_articlemediacomments ON se_articlemediacomments.articlemediacomment_articlemedia_id=se_articlemedia.articlemedia_id LEFT JOIN se_articlealbums ON se_articlealbums.articlealbum_id=se_articlemedia.articlemedia_articlealbum_id";

	  // ADD WHERE IF NECESSARY
	  if($this->article_exists != 0 | $where != "") { $articlemedia_query .= " WHERE"; }

	  // IF ARTICLE EXISTS, SPECIFY ARTICLE ID
	  if($this->article_exists != 0) { $articlemedia_query .= " se_articlealbums.articlealbum_article_id='".$this->article_info[article_id]."'"; }

	  // ADD AND IF NECESSARY
	  if($this->article_exists != 0 & $where != "") { $articlemedia_query .= " AND"; }

	  // ADD ADDITIONAL WHERE CLAUSE
	  if($where != "") { $articlemedia_query .= " $where"; }

	  // ADD GROUP BY, ORDER, AND LIMIT CLAUSE
	  $articlemedia_query .= " GROUP BY articlemedia_id ORDER BY $sort_by LIMIT $start, $limit";

	  // RUN QUERY
	  $articlemedia = $database->database_query($articlemedia_query);

	  // GET ARTICLE MEDIA INTO AN ARRAY
	  $articlemedia_array = Array();
	  while($articlemedia_info = $database->database_fetch_assoc($articlemedia)) {

	    // CREATE ARRAY OF MEDIA DATA
	    $articlemedia_array[] = Array('articlemedia_id' => $articlemedia_info[articlemedia_id],
					'articlemedia_articlealbum_id' => $articlemedia_info[articlemedia_articlealbum_id],
					'articlemedia_date' => $articlemedia_info[articlemedia_date],
					'articlemedia_title' => $articlemedia_info[articlemedia_title],
					'articlemedia_desc' => str_replace("<br>", "\r\n", $articlemedia_info[articlemedia_desc]),
					'articlemedia_ext' => $articlemedia_info[articlemedia_ext],
					'articlemedia_filesize' => $articlemedia_info[articlemedia_filesize],
					'articlemedia_comments_total' => $articlemedia_info[total_comments]);

	  }

	  // RETURN ARRAY
	  return $articlemedia_array;

	} // END article_media_list() METHOD








	// THIS METHOD UPDATES ARTICLE MEDIA INFORMATION
	// INPUT: $start REPRESENTING THE ARTICLE MEDIA TO START WITH
	//	  $limit REPRESENTING THE NUMBER OF ARTICLE MEDIA TO RETURN
	//	  $sort_by (OPTIONAL) REPRESENTING THE ORDER BY CLAUSE
	//	  $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
	// OUTPUT: AN ARRAY CONTAINING ALL THE ARTICLE MEDIA ID
	function article_media_update($start, $limit, $sort_by = "articlemedia_id DESC", $where = "") {
	  global $database;

	  // BEGIN QUERY
	  $articlemedia_query = "SELECT se_articlemedia.*, se_articlealbums.articlealbum_id, se_articlealbums.articlealbum_article_id, se_articlealbums.articlealbum_title, count(se_articlemediacomments.articlemediacomment_id) AS total_comments";
	
	  // CONTINUE QUERY
	  $articlemedia_query .= " FROM se_articlemedia LEFT JOIN se_articlemediacomments ON se_articlemediacomments.articlemediacomment_articlemedia_id=se_articlemedia.articlemedia_id LEFT JOIN se_articlealbums ON se_articlealbums.articlealbum_id=se_articlemedia.articlemedia_articlealbum_id";

	  // ADD WHERE IF NECESSARY
	  if($this->article_exists != 0 | $where != "") { $articlemedia_query .= " WHERE"; }

	  // IF ARTICLE EXISTS, SPECIFY ARTICLE ID
	  if($this->article_exists != 0) { $articlemedia_query .= " se_articlealbums.articlealbum_article_id='".$this->article_info[article_id]."'"; }

	  // ADD AND IF NECESSARY
	  if($this->article_exists != 0 & $where != "") { $articlemedia_query .= " AND"; }

	  // ADD ADDITIONAL WHERE CLAUSE
	  if($where != "") { $articlemedia_query .= " $where"; }

	  // ADD GROUP BY, ORDER, AND LIMIT CLAUSE
	  $articlemedia_query .= " GROUP BY articlemedia_id ORDER BY $sort_by LIMIT $start, $limit";

	  // RUN QUERY
	  $articlemedia = $database->database_query($articlemedia_query);

	  // GET ARTICLE MEDIA INTO AN ARRAY
	  $articlemedia_array = Array();
	  while($articlemedia_info = $database->database_fetch_assoc($articlemedia)) {
	    $var1 = "articlemedia_title_".$articlemedia_info[articlemedia_id];
	    $var2 = "articlemedia_desc_".$articlemedia_info[articlemedia_id];
	    $articlemedia_title = censor($_POST[$var1]);
	    $articlemedia_desc = censor(str_replace("\r\n", "<br>", $_POST[$var2]));
	    if($articlemedia_title != $articlemedia_info[articlemedia_title] OR $articlemedia_desc != $articlemedia_info[articlemedia_desc]) {
	      $database->database_query("UPDATE se_articlemedia SET articlemedia_title='$articlemedia_title', articlemedia_desc='$articlemedia_desc' WHERE articlemedia_id='$articlemedia_info[articlemedia_id]'");
	    }
	    $articlemedia_array[] = $articlemedia_info[articlemedia_id];
	  }

	  return $articlemedia_array;

	} // END article_media_update() METHOD








	// THIS METHOD DELETES SELECTED ARTICLE MEDIA
	// INPUT: $start REPRESENTING THE ARTICLE MEDIA TO START WITH
	//	  $limit REPRESENTING THE NUMBER OF ARTICLE MEDIA TO RETURN
	//	  $sort_by (OPTIONAL) REPRESENTING THE ORDER BY CLAUSE
	//	  $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
	// OUTPUT:
	function article_media_delete($start, $limit, $sort_by = "articlemedia_id DESC", $where = "") {
	  global $database, $url;

	  // BEGIN QUERY
	  $articlemedia_query = "SELECT se_articlemedia.*, se_articlealbums.articlealbum_id, se_articlealbums.articlealbum_article_id, se_articlealbums.articlealbum_title, count(se_articlemediacomments.articlemediacomment_id) AS total_comments";
	
	  // CONTINUE QUERY
	  $articlemedia_query .= " FROM se_articlemedia LEFT JOIN se_articlemediacomments ON se_articlemediacomments.articlemediacomment_articlemedia_id=se_articlemedia.articlemedia_id LEFT JOIN se_articlealbums ON se_articlealbums.articlealbum_id=se_articlemedia.articlemedia_articlealbum_id";

	  // ADD WHERE IF NECESSARY
	  if($this->article_exists != 0 | $where != "") { $articlemedia_query .= " WHERE"; }

	  // IF ARTICLE EXISTS, SPECIFY ARTICLE ID
	  if($this->article_exists != 0) { $articlemedia_query .= " se_articlealbums.articlealbum_article_id='".$this->article_info[article_id]."'"; }

	  // ADD AND IF NECESSARY
	  if($this->article_exists != 0 & $where != "") { $articlemedia_query .= " AND"; }

	  // ADD ADDITIONAL WHERE CLAUSE
	  if($where != "") { $articlemedia_query .= " $where"; }

	  // ADD GROUP BY, ORDER, AND LIMIT CLAUSE
	  $articlemedia_query .= " GROUP BY articlemedia_id ORDER BY $sort_by LIMIT $start, $limit";

	  // RUN QUERY
	  $articlemedia = $database->database_query($articlemedia_query);

	  // LOOP OVER ARTICLE MEDIA
	  $articlemedia_delete = "";
	  while($articlemedia_info = $database->database_fetch_assoc($articlemedia)) {
	    $var = "delete_articlemedia_".$articlemedia_info[articlemedia_id];
	    if($_POST[$var] == 1) { 
	      if($articlemedia_delete != "") { $articlemedia_delete .= " OR "; }
	      $articlemedia_delete .= "articlemedia_id='$articlemedia_info[articlemedia_id]'";
	      $articlemedia_path = $this->article_dir($this->article_info[article_id]).$articlemedia_info[articlemedia_id].".".$articlemedia_info[articlemedia_ext];
	      if(file_exists($articlemedia_path)) { unlink($articlemedia_path); }
	      $thumb_path = $this->article_dir($this->article_info[article_id]).$articlemedia_info[articlemedia_id]."_thumb.".$articlemedia_info[articlemedia_ext];
	      if(file_exists($thumb_path)) { unlink($thumb_path); }
	    }
	  }

	  // IF DELETE CLAUSE IS NOT EMPTY, DELETE ARTICLE MEDIA
	  if($articlemedia_delete != "") { $database->database_query("DELETE FROM se_articlemedia, se_articlemediacomments USING se_articlemedia LEFT JOIN se_articlemediacomments ON se_articlemedia.articlemedia_id=se_articlemediacomments.articlemediacomment_articlemedia_id WHERE ($articlemedia_delete)"); }

	} // END article_media_delete() METHOD

	
	function is_article_active()
	{
	  return !($this->article_info[article_approved] == 0 || $this->article_info[article_draft] == 1);
	}
	
	function generate_slug($text, $options=array())
  {
    if (!isset($options['case'])) {
      $text = strtolower($text);
    }
    
    $space = $options['space'] ? $options['space'] : '-';
    
    // strip all non word chars
    $text = preg_replace('/\W/', ' ', $text);
 
    // replace all white space sections with a dash
    $text = preg_replace('/\ +/', $space, $text);
 
    // trim dashes
    $text = preg_replace('/\-$/', '', $text);
    $text = preg_replace('/^\-/', '', $text);
 
    return $text;
	}
	
	
}

class rc_articletag extends rc_tag  {
  var $table = 'se_articletags';
  
  
}


