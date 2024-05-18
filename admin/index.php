<?php
session_start();
date_default_timezone_set('UTC'); // Set the desired time zone, e.g., 'UTC'

// Check if the user is already logged in,
// assuming there's a session variable set upon login (e.g., $_SESSION['user_id'])
if(!isset($_SESSION['user_id'])) {
    // If the session variable is not set, redirect to the login page
    header("Location: login.php");
    exit();
}

?>
