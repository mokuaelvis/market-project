<?php
session_start();

// Unset all admin session variables
unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);
unset($_SESSION['admin_role']);

// Destroy the session if no other user is logged in
if (!isset($_SESSION['user_id'])) {
    session_destroy();
}

// Redirect to home page
header("Location: index.php");
exit();
?>