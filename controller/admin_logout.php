<?php

$page = "admin_logout";
include "admin_header.php";

$admin->admin_logout();

// FORWARD TO ADMIN LOGIN PAGE
cheader("admin_login.php");
exit();
?>