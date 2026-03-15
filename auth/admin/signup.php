<?php
session_start();
require_once "../../includes/db.php";

// Define your secret master key here
define("SECRET_ADMIN_CODE", "HIRAYA_ADMIN_2026");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $admin_code = $_POST['admin_code'];

    // Security Check: Verify the Secret Code first
    if ($admin_code !== SECRET_ADMIN_CODE) {
        $error = "Invalid Secret Access Code. Unauthorized registration attempt.";
    } else {
        try {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO admins (full_name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$full_name, $email, $hashed_password]);
            
            header("Location: login.php?status=registered");
            exit();
        } catch (PDOException $e) {
            $error = "Registration failed: This email is already registered.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Secure Admin Signup | HIRAYA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md p-8">
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-[#06201d] text-white rounded-2xl mb-4 shadow-xl">
                <i class="fa-solid fa-key text-2xl"></i>
            </div>
            <h1 class="text-2xl font-black text-gray-900 uppercase tracking-tighter">Admin Enrollment</h1>
            <p class="text-gray-400 text-sm font-medium mt-1">Verification required for system access</p>
        </div>

        <form method="POST" class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 space-y-5">
            <?php if(isset($error)): ?>
                <div class="p-4 bg-red-50 text-red-600 text-[10px] font-black uppercase rounded-xl border border-red-100 text-center italic">
                    <i class="fa-solid fa-triangle-exclamation mr-1"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Full Name</label>
                <input type="text" name="full_name" required placeholder="John Doe" 
                       class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-teal-500 outline-none transition font-bold text-sm">
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Admin Email</label>
                <input type="email" name="email" required placeholder="admin@hiraya.com" 
                       class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-teal-500 outline-none transition font-bold text-sm">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Password</label>
                    <input type="password" name="password" required 
                           class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-teal-500 outline-none transition font-bold text-sm">
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-orange-500 mb-2 ml-1">Secret Code</label>
                    <input type="password" name="admin_code" required 
                           class="w-full px-5 py-4 bg-orange-50 border border-orange-100 text-orange-700 rounded-2xl focus:ring-2 focus:ring-orange-500 outline-none transition font-bold text-sm">
                </div>
            </div>

            <button type="submit" class="w-full py-5 bg-[#06201d] text-white rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-teal-700 transition-all shadow-lg">
                Authorize Account
            </button>
        </form>
        
        <p class="text-center mt-8 text-xs text-gray-400 font-bold">
            HAVE AN ACCOUNT? <a href="login.php" class="text-teal-600 hover:underline">LOG IN</a>
        </p>
    </div>
</body>
</html>