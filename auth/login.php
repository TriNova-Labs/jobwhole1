<?php
session_start();
// Stepping out of 'auth' folder to reach 'includes'
require_once "../includes/db.php";

$error = "";
$success = "";

// Check for registration success message if they didn't auto-login
if (isset($_GET['msg']) && $_GET['msg'] == 'registered') {
    $success = "Account created successfully! Please log in.";
}

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Using PDO to fetch user data
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Set sessions for the dynamic header
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['account_type'] = $user['account_type'];

        // Redirect to the main index outside the auth folder
        header("Location: ../index.php");
        exit();
    } else {
        $error = "Invalid email or password. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HIRAYA - Login</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <div class="bg-text">
        <span>HIRAYA</span><span>SDG 8.3</span><span>INCLUSIVE</span>
        <span>WELCOME</span><span>JOBS</span><span>PH</span>
    </div>

    <div class="page-title">
        <h1>HIRAYA</h1>
    </div>

    <div class="container">
        <h2>Welcome Back</h2>
        
        <?php if($error): ?> 
            <div class="error-msg"><?php echo $error; ?></div> 
        <?php endif; ?>

        <?php if($success): ?> 
            <div class="success-msg"><?php echo $success; ?></div> 
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <input type="email" name="email" placeholder=" " required>
                <label>Email Address</label>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder=" " required>
                <label>Password</label>
            </div>
            <button type="submit" name="login" class="submit-btn">Sign In</button>
        </form>

        <p class="switch-link">
            Don't have an account? <a href="signup.php">Create one here</a>
        </p>
    </div>

    <script>
        // Dynamically scatter the background texts
        document.querySelectorAll('.bg-text span').forEach(span => {
            span.style.top = `${Math.random() * 90}%`;
            span.style.left = `${Math.random() * 90}%`;
            span.style.fontSize = `${Math.random() * 1.5 + 1}rem`;
            const opacity = Math.random() * 0.1 + 0.05;
            span.style.opacity = opacity;
            
            span.style.setProperty('--rotate', `${Math.random() * 40 - 20}deg`);
            span.style.setProperty('--x2', `${Math.random() * 50 - 25}px`);
            span.style.setProperty('--y2', `${Math.random() * 50 - 25}px`);
        });
    </script>
</body>
</html>