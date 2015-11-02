<?php

$page = "document_tags";
include "header.php";

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if( !$user->user_exists && !$params['permission_document'] )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

#CONSTRUCTING TAG CLOUD
$tag_array = array();
$query = "SELECT tag_name, count(t1.document_id) AS Frequency FROM se_documents INNER JOIN se_document_tags AS t1 ON se_documents.document_id = t1.document_id INNER JOIN se_documenttags AS t2 ON t1.tag_id = t2.id WHERE document_approved = 1 AND document_publish = 1 AND document_status = 1 GROUP BY tag_name ORDER BY Frequency DESC LIMIT 100";
$result = $database->database_query($query);
while($info = $database->database_fetch_assoc($result)) {
	$tag_array[$info['tag_name']] = $info['Frequency'];
}
$max_font_size = 32;
$min_font_size = 12;
$max_frequency = max(array_values($tag_array));
$min_frequency = min(array_values($tag_array));
$spread = $max_frequency - $min_frequency;
if($spread == 0) {
	$spread = 1;
}
$step = ($max_font_size - $min_font_size) / ($spread);

$tag_data = array('min_font_size' => $min_font_size, 'max_font_size' => $max_font_size, 'max_frequency' => $max_frequency, 'min_frequency' => $min_frequency, 'step' => $step);


$smarty->assign('tag_array', $tag_array);
$smarty->assign('tag_data', $tag_data);

include "footer.php";
?>