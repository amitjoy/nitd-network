<?php

$page = "user_userconnection_contacts_2degree";
include "header.php";

// $second_degree_contacts_id VALUE OF THIS VARIABLE IS ALREADY ASSIGNED IN HEADER_USERCONNNECTION.PHP
$second_degree_contacts_users_information = userconnection_users_information($second_degree_contacts_id);
$smarty->assign('second_degree_contacts_users_information', $second_degree_contacts_users_information);

include "footer.php";
?>