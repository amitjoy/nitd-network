<?php

$page = "user_logout";
include "header.php";

if( @$_GET['token'] == $session->get('token') || strtoupper($_SERVER['REQUEST_METHOD']) === 'POST' )
{
  $user->user_logout();
}

// FORWARD TO USER LOGIN PAGE
cheader("home.php");
exit();
?>