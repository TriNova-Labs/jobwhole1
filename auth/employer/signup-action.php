<?php
include '../../includes/db.php'; 
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $company_name = trim($_POST['company_name']);

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO employers (full_name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$full_name, $email, $password]);
        $new_employer_id = $pdo->lastInsertId();

        $stmt2 = $pdo->prepare("INSERT INTO companies (employer_id, name) VALUES (?, ?)");
        $stmt2->execute([$new_employer_id, $company_name]);

        $pdo->commit();

        // Standardizing session keys to match dashboard protection
        $_SESSION['user_id'] = $new_employer_id;
        $_SESSION['first_name'] = $full_name;
        $_SESSION['account_type'] = 'employer'; 

        // Redirect to the dashboard index
        header("Location: ../../dashboard/employer/index.php");
        exit();

    } catch (PDOException $e) {
        $pdo->rollBack();
        header("Location: signup.php?error=db_error");
        exit();
    }
}