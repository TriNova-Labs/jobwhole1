<?php
session_start();
require_once "../../includes/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id']) || $_SESSION['account_type'] !== 'employer') {
        exit("Unauthorized");
    }

    $employer_id = $_SESSION['user_id'];
    $full_name = trim($_POST['full_name']);
    $bio = trim($_POST['bio']);

    try {
        // 1. Column Maintenance: Ensure 'bio' and 'logo_path' exist
        $columns = $pdo->query("SHOW COLUMNS FROM employers")->fetchAll(PDO::FETCH_COLUMN);
        
        if (!in_array('bio', $columns)) {
            $pdo->exec("ALTER TABLE employers ADD COLUMN bio TEXT AFTER full_name");
        }
        if (!in_array('logo_path', $columns)) {
            $pdo->exec("ALTER TABLE employers ADD COLUMN logo_path VARCHAR(255) AFTER bio");
        }

        // 2. Handle Logo Upload if a file was provided
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = "../../uploads/logos/";
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $file_ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png'];

            if (in_array($file_ext, $allowed_exts)) {
                // Generate unique filename to avoid browser cache issues
                $new_logo_name = "logo_" . $employer_id . "_" . time() . "." . $file_ext;
                $dest_path = $upload_dir . $new_logo_name;

                if (move_uploaded_file($_FILES['logo']['tmp_name'], $dest_path)) {
                    // Update database with the new logo path
                    $stmtLogo = $pdo->prepare("UPDATE employers SET logo_path = ? WHERE id = ?");
                    $stmtLogo->execute([$new_logo_name, $employer_id]);
                }
            }
        }

        // 3. Update Text Fields
        $stmt = $pdo->prepare("UPDATE employers SET full_name = ?, bio = ? WHERE id = ?");
        $stmt->execute([$full_name, $bio, $employer_id]);

        // 4. Update session so the header reflects the change immediately
        $_SESSION['first_name'] = $full_name;

        header("Location: profile.php?status=success");
        exit();

    } catch (PDOException $e) {
        die("Error updating profile: " . $e->getMessage());
    }
}