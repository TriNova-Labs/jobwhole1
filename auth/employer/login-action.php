<?php
include '../../includes/db.php'; 
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM employers WHERE email = ?");
    $stmt->execute([$email]);
    $employer = $stmt->fetch();

    if ($employer && password_verify($password, $employer['password'])) {
        // Standardizing session keys
        $_SESSION['user_id'] = $employer['id'];
        $_SESSION['first_name'] = $employer['full_name'];
        $_SESSION['account_type'] = 'employer'; 
        
        // Direct redirect to the dashboard index
        header("Location: ../../dashboard/employer/index.php");
        exit();
    } else {
        // Redirect back to login with error
        header("Location: login.php?error=invalid");
        exit();
    }
}