<?php
session_start();
require_once "../../includes/db.php";

// 1. Security Check: Match the session from login-action.php
if (!isset($_SESSION['user_id']) || $_SESSION['account_type'] !== 'employer') {
    header("Location: /sdg/auth/employer/login.php?error=unauthorized");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /sdg/dashboard/employer/index.php");
    exit();
}

$employer_id = $_SESSION['user_id'];
$app_id = $_POST['app_id'] ?? null;
$new_status = $_POST['status'] ?? null;
$job_id = $_POST['job_id'] ?? ''; 

$allowed_statuses = ['accepted', 'rejected', 'pending'];
if (!$app_id || !in_array($new_status, $allowed_statuses)) {
    header("Location: /sdg/dashboard/employer/manage-applications.php?error=invalid");
    exit();
}

try {
    /**
     * 2. Verification Query
     * Checks if the application exists AND belongs to the logged-in employer.
     */
    $check_stmt = $pdo->prepare("
        SELECT 
            a.id, 
            u.email as seeker_email, 
            u.first_name as seeker_name, 
            j.title as job_title, 
            e.full_name as employer_name
        FROM applications a
        JOIN jobs j ON a.job_id = j.id
        JOIN users u ON a.seeker_id = u.id
        JOIN employers e ON j.employer_id = e.id
        WHERE a.id = ? AND e.id = ?
    ");
    $check_stmt->execute([$app_id, $employer_id]);
    $app_data = $check_stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($app_data) {
        // 3. Update the Status in the applications table
        $update_stmt = $pdo->prepare("UPDATE applications SET status = ? WHERE id = ?");
        $update_stmt->execute([$new_status, $app_id]);

        // 4. Send Notification
        if ($new_status === 'accepted' || $new_status === 'rejected') {
            sendStatusEmail(
                $app_data['seeker_email'], 
                $app_data['seeker_name'], 
                $app_data['job_title'], 
                $app_data['employer_name'], 
                $new_status
            );
        }

        // 5. Success redirect - Keeps the job filter active if it was set
        $redirect_url = "/sdg/dashboard/employer/manage-applications.php?status=updated";
        if (!empty($job_id) && $job_id !== 'null') {
            $redirect_url .= "&job_id=" . urlencode($job_id);
        }
        
        header("Location: " . $redirect_url);
        exit();
    } else {
        header("Location: /sdg/dashboard/employer/index.php?error=notfound");
        exit();
    }

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    header("Location: /sdg/dashboard/employer/index.php?error=server");
    exit();
}

/**
 * Email Helper Function
 */
function sendStatusEmail($to, $name, $job_title, $employer_name, $status) {
    $subject = "Update on your application for $job_title";
    $status_text = ($status === 'accepted') ? "Hired / Selected" : "Not Selected";
    $color = ($status === 'accepted') ? '#0d9488' : '#ef4444';

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Hiraya Notifications <noreply@hiraya.com>" . "\r\n";

    $body = "
    <div style='font-family: sans-serif; max-width: 600px; margin: auto; border: 1px solid #eee; border-radius: 20px; overflow: hidden;'>
        <div style='background: #06201d; padding: 20px; text-align: center; color: #2dd4bf;'>
            <h1 style='margin:0;'>HIRAYA</h1>
        </div>
        <div style='padding: 30px;'>
            <p>Hi $name,</p>
            <p>Your application for <strong>$job_title</strong> at <strong>$employer_name</strong> has been updated:</p>
            <div style='padding: 20px; background: #f9fafb; border-left: 5px solid $color; font-weight: bold; font-size: 18px; color: $color;'>
                $status_text
            </div>
            <p style='margin-top: 20px; font-size: 12px; color: #9ca3af;'>Log in to your HIRAYA dashboard for more details and next steps.</p>
        </div>
    </div>";

    // Use @ to suppress errors if local mail server (XAMPP) is not configured
    @mail($to, $subject, $body, $headers);
}