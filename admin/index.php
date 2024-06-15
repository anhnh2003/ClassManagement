<?php
session_start();
include('../includes/adminVerify.php');
    // Token is valid, continue
    header('location:dashboard.php');
 ?>
