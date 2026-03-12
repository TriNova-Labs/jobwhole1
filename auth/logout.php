<?php
// 1. Initialize the session to access it
session_start();

// 2. Clear all session variables
$_SESSION = array();

// 3. Destroy the session cookie if it exists
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}

// 4. Destroy the session on the server
session_destroy();

// 5. Redirect the user back to the home page or login page
header("Location: ../index.php");
exit();
?>