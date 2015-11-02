<?php
$page = "admin_userconnections_layout_preview";
include "admin_header.php";

$preview_number = $_GET['preview_number'] ;

$smarty->assign('preview_number', $preview_number);
include "admin_footer.php";
?>