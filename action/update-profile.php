<?php
session_start();
require_once "../../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // 1. Sanitize Text Data
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $middle_initial = strtoupper(trim($_POST['middle_initial']));
    $extension = trim($_POST['extension']);

    // Fetch current profile to check for existing files (for cleanup)
    $stmt = $pdo->prepare("SELECT resume_path, nbi_clearance_path FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $current_user = $stmt->fetch();

    // 2. Handle File Uploads (Professional Vault)
    $upload_dir = "../../uploads/documents/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $files_to_upload = [
        'resume' => 'resume_path', 
        'nbi_clearance' => 'nbi_clearance_path'
    ];

    $db_updates = [];
    $params = [$first_name, $last_name, $middle_initial, $extension];

    foreach ($files_to_upload as $file_key => $db_column) {
        if (!empty($_FILES[$file_key]['name'])) {
            $file = $_FILES[$file_key];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['pdf', 'jpg', 'jpeg', 'png'];

            if (in_array($ext, $allowed)) {
                // Generate unique professional filename
                $new_filename = $file_key . "_" . $user_id . "_" . time() . "." . $ext;
                $target_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($file['tmp_name'], $target_path)) {
                    // Delete the old file from server to save space
                    if (!empty($current_user[$db_column]) && file_exists($upload_dir . $current_user[$db_column])) {
                        unlink($upload_dir . $current_user[$db_column]);
                    }

                    $db_updates[] = "$db_column = ?";
                    $params[] = $new_filename;
                }
            }
        }
    }

    // 3. Construct and Execute SQL
    $sql = "UPDATE users SET first_name = ?, last_name = ?, middle_initial = ?, extension = ?";
    
    if (!empty($db_updates)) {
        $sql .= ", " . implode(", ", $db_updates);
    }
    
    $sql .= " WHERE id = ?";
    $params[] = $user_id;

    try {
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute($params)) {
            header("Location: ../user/profile.php?status=success");
        } else {
            header("Location: ../user/profile.php?status=error");
        }
    } catch (PDOException $e) {
        // Log error if needed: error_log($e->getMessage());
        header("Location: ../user/profile.php?status=error");
    }
    exit();
}