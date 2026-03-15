<?php
session_start();

// PATH: Go up 1 level to root, then into includes/
require_once "../includes/db.php"; 

if (!isset($_SESSION['user_id'])) {
    // Redirect to login if session is expired
    header("Location: ../dashboard/auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_id = (int)$_POST['job_id'];
    $seeker_id = $_SESSION['user_id']; 
    $message = trim($_POST['message'] ?? '');
    $use_vault = isset($_POST['use_vault_resume']) ? true : false;
    
    // 1. Duplicate Check: Ensure the user hasn't already applied
    $check_stmt = $pdo->prepare("SELECT id FROM applications WHERE job_id = ? AND seeker_id = ?");
    $check_stmt->execute([$job_id, $seeker_id]);
    if ($check_stmt->fetch()) {
        header("Location: ../dashboard/user/jobs/job-details.php?id=$job_id&error=already_applied");
        exit();
    }

    $final_resume_path = "";
    // Note: This path is relative to this action file. 
    // It goes up to SDG root, then into uploads/documents/
    $upload_dir = "../uploads/documents/"; 

    // Create directory if it doesn't exist for new uploads
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // 2. Resume Selection Logic
    if ($use_vault) {
        // Fetch the existing resume path from the users table
        $stmt = $pdo->prepare("SELECT resume_path FROM users WHERE id = ?");
        $stmt->execute([$seeker_id]);
        $user = $stmt->fetch();

        // Verify the file actually exists in the uploads folder before using it
        if (!empty($user['resume_path']) && file_exists($upload_dir . $user['resume_path'])) {
            $final_resume_path = $user['resume_path'];
        } else {
            header("Location: ../dashboard/user/jobs/job-details.php?id=$job_id&error=no_vault_file");
            exit();
        }
    } else {
        // Handle New File Upload
        if (!isset($_FILES['resume']) || $_FILES['resume']['error'] !== UPLOAD_ERR_OK) {
            header("Location: ../dashboard/user/jobs/job-details.php?id=$job_id&error=upload_failed");
            exit();
        }

        $file = $_FILES['resume'];
        
        // Validate file type (PDF only)
        $file_type = mime_content_type($file['tmp_name']);
        if ($file_type !== 'application/pdf') {
            die("Error: Only PDF files are allowed.");
        }

        // Generate a unique filename to prevent overwriting
        $new_filename = "app_resume_" . $seeker_id . "_" . time() . ".pdf";
        $target_path = $upload_dir . $new_filename;

        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            $final_resume_path = $new_filename;
        } else {
            die("Error: Upload failed. Check folder permissions for " . $upload_dir);
        }
    }

    // 3. Database Insertion
    try {
        // We include 'message' and set initial status to 'pending'
        $sql = "INSERT INTO applications (job_id, seeker_id, resume_path, message, status, applied_at) 
                VALUES (?, ?, ?, ?, 'pending', NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $job_id, 
            $seeker_id, 
            $final_resume_path, 
            $message
        ]);

        // Success redirect back to the job page with toast trigger
        header("Location: ../dashboard/user/jobs/job-details.php?id=$job_id&status=applied");
        exit();

    } catch (PDOException $e) {
        // If the column 'message' is missing, this is where it will catch the error
        die("Database error: " . $e->getMessage());
    }
}