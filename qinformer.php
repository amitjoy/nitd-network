<?php
$page = "qinformer";
include "header.php";
$row_qinformer='';
$sql = "
	SELECT user_id,user_profilecat_id
	FROM se_users WHERE user_username = '".htmlspecialchars(trim($_GET['name']),ENT_QUOTES)."' LIMIT 1";
$user_qinfo = $database->database_fetch_assoc($database->database_query($sql));

$info = new se_user(Array($user_qinfo['user_id'], htmlspecialchars(trim($_GET['name']),ENT_QUOTES)));

$field = new se_field("profile",$info->profile_info);
$field->cat_list( 0, 1, 0, "profilecat_id='".$user_qinfo['user_profilecat_id']."'", "", "" );

$qinformer_settings = get_qinformer_settings();
$row_qinformer .="
  <tr>
    <td	width=40%>Username</td>
    <td width=60%>".htmlspecialchars(trim($_GET['name']),ENT_QUOTES)."</td>
  </tr>
";

for ( $i = 0; $i < count( $field->fields_all ); $i ++ ) {

if(isset($qinformer_settings['fields']) && $qinformer_settings['fields'])
{
$qinformer_fields = explode("|", $qinformer_settings['fields']);
$count_fields_enable = count($qinformer_fields);
	if ($field->fields_all[$i]['field_value_formatted'] != '')
	for ($j=0; $j < $count_fields_enable; $j++) {	
	$field_value='';
		if ($field->fields_all[$i]['field_id'] == $qinformer_fields[$j])
		{
			if ($field->fields_all[$i]['field_type'] == 1 || $field->fields_all[$i]['field_type'] == 2) 
				$field_value=$field->fields_all[$i]['field_value_formatted'];
				
    		if ($field->fields_all[$i]['field_type'] == 3) 
			{
				for ($n=0; $n < count($field->fields_all[$i]['field_options']); $n++) 
				{
					if ($field->fields_all[$i]['field_options'][$n]['value'] == $field->fields_all[$i]['field_value']) 
					$field_value=$field->fields_all[$i]['field_value_formatted'];
				}
			}
    		if ($field->fields_all[$i]['field_type'] == 4) 
			{
				for ($n=0; $n < count($field->fields_all[$i]['field_options']); $n++) 
				{
					if ($field->fields_all[$i]['field_options'][$n]['value'] == $field->fields_all[$i]['field_value']) 
					$field_value=$field->fields_all[$i]['field_value_formatted'];
				}
			}			
			if ($field->fields_all[$i]['field_type'] == 5)
				{
				SE_Language::_preload(852);
			    SE_Language::load();				
				$years_old = "<br/>(".sprintf(SE_Language::_get(852),$datetime->age($field->fields_all[$i]['field_value'])).")";
				if (substr($field->fields_all[$i]['field_value'],0,4) == "0000") $years_old = "";	
				$field_value=$field->fields_all[$i]['field_value_formatted'].$years_old;
				}
    		if ($field->fields_all[$i]['field_type'] == 6) 
			{
				for ($n=0; $n < count($field->fields_all[$i]['field_options']); $n++) 
				{
					if (in_array($field->fields_all[$i]['field_options'][$n]['value'],$field->fields_all[$i]['field_value'])) 
					$field_value=$field->fields_all[$i]['field_value_formatted'];
				}
			}		
	    SE_Language::_preload($field->fields_all[$i]['field_title']);
	    SE_Language::load();								
		$row_qinformer .="
 		 <tr>
  		  <td	width=40%>".SE_Language::_get($field->fields_all[$i]['field_title'])."</td>
  		  <td width=60%>".$field_value."</td>
 		 </tr>
		";
		}
		}
	}	
}
echo "
<table width=100% border=0 cellspacing=3 cellpadding=0 class=q_inform align=center>
".$row_qinformer."
</table>
";
?>
