<?php
// Move up one level to find the includes folder
require_once '../includes/db.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and sanitize basic inputs
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $email      = trim($_POST['email']);
    $password   = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $company_name = trim($_POST['company_name']);
    $location   = trim($_POST['location']);

    try {
        $pdo->beginTransaction();

        // 1. Insert into Users Table
        // We generate a simple username: firstname.lastname + random number
        $username = strtolower($first_name . "." . $last_name . rand(10, 99));
        
        $userStmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, username, birthday, account_type) VALUES (?, ?, ?, ?, ?, '2000-01-01', 'employer')");
        $userStmt->execute([$first_name, $last_name, $email, $password, $username]);
        
        $employer_id = $pdo->lastInsertId();

        // 2. Insert into Companies Table
        $compStmt = $pdo->prepare("INSERT INTO companies (employer_id, name, location) VALUES (?, ?, ?)");
        $compStmt->execute([$employer_id, $company_name, $location]);

        $pdo->commit();
        
        // Redirect back to the root login page
        header("Location: ../login.php?msg=success");
        exit();

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        // For debugging, you can keep the echo, but in production, redirect with an error msg
        echo "Registration failed: " . $e->getMessage();
    }
}
?>