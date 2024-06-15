<?php
session_start();
include('../includes/dbconnection.php');
include('../includes/studentVerify.php');
// Check if the user is logged in and the session variables are set
  
    header('location:dashboard.php');
?>