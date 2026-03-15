<?php
session_start();
require_once "../../includes/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_id = $_POST['job_id'];
    $user_id = $_SESSION['user_id'] ?? null;
    $name = trim($_POST['applicant_name']);
    $message = trim($_POST['message']);
    
    // File Upload Logic
    $target_dir = "../../uploads/resumes/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
    
    $file_extension = pathinfo($_FILES["resume"]["name"], PATHINFO_EXTENSION);
    $new_filename = "resume_" . time() . "_" . uniqid() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;

    if (move_uploaded_file($_FILES["resume"]["tmp_name"], $target_file)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO applications (job_id, user_id, applicant_name, message, resume_path, status) VALUES (?, ?, ?, ?, ?, 'pending')");
            $stmt->execute([$job_id, $user_id, $name, $message, $new_filename]);
            
            header("Location: job-details.php?id=$job_id&status=applied");
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }
}