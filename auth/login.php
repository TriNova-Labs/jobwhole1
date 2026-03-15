<?php
session_start();
// We only need this to check for messages from the process file
$error = "";
$success = "";

if (isset($_GET['msg']) && $_GET['msg'] == 'registered') {
    $success = "Account created successfully! Please log in.";
}
if (isset($_GET['error']) && $_GET['error'] == 'invalid') {
    $error = "Invalid email or password. Please try again.";
}
if (isset($_GET['logout']) && $_GET['logout'] == 'success') {
    $success = "You have been successfully signed out.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HIRAYA - Login</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .spinner { width: 18px; height: 18px; border: 2px solid rgba(255,255,255,0.3); border-radius: 50%; border-top-color: #fff; animation: spin 0.8s linear infinite; display: none; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .hidden { display: none; }
        .alert-box { background: rgba(20, 184, 166, 0.1); border: 1px solid rgba(20, 184, 166, 0.3); color: #2dd4bf; font-size: 0.75rem; padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; }
        .error-box { background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; }
    </style>
</head>
<body>
    <div class="bg-text"><span>HIRAYA</span><span>SDG 8.3</span><span>INCLUSIVE</span></div>

    <div class="container">
        <h2>Welcome Back</h2>
        
        <?php if($error): ?> 
            <div class="alert-box error-box"><?php echo $error; ?></div> 
        <?php endif; ?>

        <?php if($success): ?> 
            <div class="alert-box"><?php echo $success; ?></div> 
        <?php endif; ?>

        <form action="login_process.php" method="POST" id="loginForm">
            <div class="input-group">
                <input type="email" name="email" id="email" placeholder=" " required autocomplete="off">
                <label>Email Address</label>
            </div>
            <div class="input-group">
                <input type="password" name="password" id="password" placeholder=" " required autocomplete="new-password">
                <label>Password</label>
            </div>
            <button type="submit" name="login" id="loginBtn" class="submit-btn" style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                <span id="btnText">Sign In</span>
                <div id="btnSpinner" class="spinner"></div>
            </button>
        </form>
    </div>

    <script>
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');
        const btnSpinner = document.getElementById('btnSpinner');
        const btnText = document.getElementById('btnText');

        loginForm.addEventListener('submit', () => {
            loginBtn.disabled = true;
            btnText.classList.add('hidden');
            btnSpinner.style.display = 'block';
        });

        window.onload = () => {
            document.getElementById('email').value = '';
            document.getElementById('password').value = '';
        };
    </script>
</body>
</html>