<?php
session_start();
require_once "../includes/db.php";

$error = "";
if (isset($_POST['signup'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);
    
    if ($check->rowCount() > 0) {
        $error = "Email already in use.";
    } else {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        // Using your preferred username logic
        $username = strtolower($first_name . rand(100, 999));
        $account_type = 'user';
        
        $sql = "INSERT INTO users (first_name, last_name, email, password, username, birthday, account_type) VALUES (?, ?, ?, ?, ?, '2000-01-01', ?)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$first_name, $last_name, $email, $hashed, $username, $account_type])) {
            // --- AUTOMATIC LOGIN LOGIC ---
            // Get the ID of the user we just created
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['first_name'] = $first_name;
            $_SESSION['last_name'] = $last_name;
            $_SESSION['account_type'] = $account_type;

            // Redirect to the main index outside the auth folder
            header("Location: ../index.php");
            exit();
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HIRAYA - Register</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <div class="bg-text">
        <span>HIRAYA</span><span>SDG 8.3</span><span>INCLUSIVE</span>
        <span>JOIN US</span><span>CAREERS</span>
    </div>

    <div class="page-title">
        <h1>HIRAYA</h1>
    </div>

    <div class="container">
        <h2 style="margin-bottom: 1.5rem;">Create Account</h2>
        
        <?php if($error): ?> 
            <div class="error-msg"><?php echo $error; ?></div> 
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <input type="text" name="first_name" placeholder=" " required>
                <label>First Name</label>
            </div>
            <div class="input-group">
                <input type="text" name="last_name" placeholder=" " required>
                <label>Last Name</label>
            </div>
            <div class="input-group">
                <input type="email" name="email" placeholder=" " required>
                <label>Email Address</label>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder=" " required>
                <label>Password</label>
            </div>
            <button type="submit" name="signup" class="submit-btn">Register & Join</button>
        </form>

        <p class="switch-link">
            Already a member? <a href="login.php">Login here</a>
        </p>
    </div>

    <script>
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