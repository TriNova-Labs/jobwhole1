<?php
session_start();
require_once "../../includes/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['full_name'];
        header("Location: ../../dashboard/admin/index.php");
        exit();
    } else {
        $error = "Invalid credentials or unauthorized access.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login | HIRAYA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md p-8">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-black text-gray-900 tracking-tighter">HIRAYA <span class="text-teal-600">ADMIN</span></h1>
            <p class="text-gray-400 text-xs font-bold uppercase tracking-[0.3em] mt-2">Secure Gateway</p>
        </div>

        <form method="POST" class="bg-white p-10 rounded-[3rem] shadow-xl shadow-teal-900/5 border border-gray-100 space-y-6">
            <?php if(isset($_GET['status']) && $_GET['status'] === 'registered'): ?>
                <div class="p-4 bg-teal-50 text-teal-700 text-[10px] font-black uppercase rounded-xl border border-teal-100 italic text-center">Registration Successful. Please Login.</div>
            <?php endif; ?>

            <?php if(isset($error)): ?>
                <div class="p-4 bg-red-50 text-red-600 text-xs font-bold rounded-xl border border-red-100 text-center"><?php echo $error; ?></div>
            <?php endif; ?>

            <div>
                <input type="email" name="email" required placeholder="Admin Email" 
                       class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-teal-500 outline-none transition font-bold text-sm">
            </div>

            <div>
                <input type="password" name="password" required placeholder="Password" 
                       class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-teal-500 outline-none transition font-bold text-sm">
            </div>

            <button type="submit" class="w-full py-5 bg-[#06201d] text-white rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-teal-700 transition-all shadow-lg">
                Enter Console
            </button>
        </form>
    </div>
</body>
</html>