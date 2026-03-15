<?php
// ob_start() prevents errors if there is accidental whitespace in included files
ob_start();
session_start();
require_once '../includes/db.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        // 1. Fetch user from the database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // 2. Verify Password
        if ($user && password_verify($password, $user['password'])) {
            
            // 3. Store essential data in Session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['account_type'] = $user['account_type'];

            // 4. Security: Prevent browser from caching the dashboard
            header("Cache-Control: no-cache, no-store, must-revalidate"); 
            header("Pragma: no-cache"); 
            header("Expires: 0");

            // 5. Role-Based Redirection
            if ($user['account_type'] === 'employer') {
                // If it's an employer, send to their specific dashboard
                $target = "../dashboard/employer/employer.php";
            } else {
                // Regular users or job seekers go to the main index
                $target = "../index.php";
            }

            // Dual-Action Redirect (PHP + JavaScript Fallback)
            header("Location: " . $target);
            echo "<script>window.location.href='" . $target . "';</script>";
            exit();

        } else {
            // 6. Failed Login: Redirect back to login.php with error code
            header("Location: login.php?error=invalid");
            exit();
        }

    } catch (PDOException $e) {
        // Log the error and show a user-friendly message
        error_log("Login error: " . $e->getMessage());
        die("A technical error occurred. Please try again later.");
    }
} else {
    // If someone tries to access this file directly without POSTing
    header("Location: login.php");
    exit();
}

ob_end_flush();
?>