<?php
// 1. Database Connection and Session Initialization
require_once '../includes/db.php'; 
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        // 2. Fetch user from the database by email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // 3. Verify Password and Account Status
        if ($user && password_verify($password, $user['password'])) {
            
            // 4. Store user data in Session variables for the header
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['account_type'] = $user['account_type']; // 'user', 'employer', or 'admin'

            // 5. Redirect based on user role
            if ($user['account_type'] === 'employer') {
                header("Location: ../dashboard_employer.php");
            } else {
                header("Location: ../index.php");
            }
            exit();

        } else {
            // Redirect back with error message if credentials fail
            header("Location: ../login.php?error=invalid_credentials");
            exit();
        }

    } catch (PDOException $e) {
        die("Login error: " . $e->getMessage());
    }
} else {
    header("Location: ../login.php");
    exit();
}
?>