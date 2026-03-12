<?php
session_start();
require_once __DIR__ . "/config/db_connect.php";

$active_form = 'signup';
$message = "";
$message_type = "";
$show_message = false;

/* ===================== LOGIN ===================== */
if (isset($_POST['login'])) {
    $active_form = 'login';
    $show_message = true;

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
        $message_type = "error";
    } else {

        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['fullname'] = $user['fullname'];

                header("Location: home.php");
                exit();
            } else {
                $message = "Incorrect password.";
                $message_type = "error";
            }
        } else {
            $message = "Email not found.";
            $message_type = "error";
        }
    }
}


/* ===================== SIGNUP ===================== */
if (isset($_POST['signup'])) {
    $active_form = 'signup';
    $show_message = true;

    $fullname = trim($_POST['fullname']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
        $message_type = "error";
    } elseif ($password !== $confirm) {
        $message = "Passwords do not match.";
        $message_type = "error";
    } else {

        // Check if email already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "Email already registered.";
            $message_type = "error";
        } else {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $fullname, $email, $hashed_password);

            if ($stmt->execute()) {
                $_SESSION['user_id'] = $stmt->insert_id;
                $_SESSION['fullname'] = $fullname;

                header("Location: home.php");
                exit();
            } else {
                $message = "Something went wrong.";
                $message_type = "error";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HIRAYA - Log In & Sign Up</title>
    <style>
        :root {
            --primary-bg-gradient: linear-gradient(135deg, #00bfa5, #004d40);
            --modal-bg: rgba(255, 255, 255, 0.85);
            --text-dark: #004d40;
            --primary-accent: #2ae7d7;
            --secondary-accent: #6db0aa;
            --radius: 16px;
            --fast: 200ms;
        }

            {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--primary-bg-gradient);
            overflow: hidden;
            position: relative;
            color: var(--text-dark);
        }

        .page-title {
            position: absolute;
            top: 20px;
            left: 20px;
            color: #fff;
            z-index: 2;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.4);
        }

        .page-title h1 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.3rem;
        }

        .page-title span {
            font-size: 1.3rem;
            font-weight: 500;
            display: block;
            margin-left: 2px;
        }

        .bg-text {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
            z-index: 0;
        }

        .bg-text span {
            position: absolute;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.12);
            white-space: nowrap;
            animation: floatDynamic 25s ease-in-out infinite;
            user-select: none;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .bg-text span:hover {
            transform: scale(1.3) rotate(var(--rotate));
            opacity: 0.25;
        }

        @keyframes floatDynamic {
            0% {
                transform: translate(0px, 0px) rotate(var(--rotate));
                opacity: var(--opacity);
            }

            25% {
                transform: translate(var(--x1), var(--y1)) rotate(calc(var(--rotate)+5deg));
                opacity: calc(var(--opacity)+0.05);
            }

            50% {
                transform: translate(var(--x2), var(--y2)) rotate(calc(var(--rotate)-5deg));
                opacity: var(--opacity);
            }

            75% {
                transform: translate(var(--x3), var(--y3)) rotate(calc(var(--rotate)+3deg));
                opacity: calc(var(--opacity)+0.05);
            }

            100% {
                transform: translate(0px, 0px) rotate(var(--rotate));
                opacity: var(--opacity);
            }
        }

        .container {
            position: relative;
            z-index: 1;
            background: var(--modal-bg);
            padding: 2.5rem;
            border-radius: var(--radius);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
            width: 420px;
            backdrop-filter: blur(10px);
            transform: translateY(-20px);
            opacity: 0;
            animation: fadeIn 0.6s forwards;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .container:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
        }

        @keyframes fadeIn {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .tab-buttons {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .tab-buttons button {
            flex: 1;
            padding: 10px;
            cursor: pointer;
            border: none;
            background: transparent;
            font-weight: bold;
            transition: color var(--fast), border-bottom var(--fast);
            color: var(--text-dark);
        }

        .tab-buttons button.active {
            border-bottom: 3px solid var(--primary-accent);
            color: var(--primary-accent);
        }

        .form-message {
            margin-bottom: 1rem;
            font-size: 1rem;
            font-weight: 600;
            padding: 10px;
            border-radius: 12px;
            text-align: center;
        }

        .form-message.success {
            background: rgba(30, 142, 133, 0.2);
            color: var(--text-dark);
            border: 1px solid rgba(30, 142, 133, 0.5);
        }

        .form-message.error {
            background: rgba(255, 87, 34, 0.25);
            color: #b71c1c;
            border: 1px solid rgba(255, 87, 34, 0.5);
        }

        form {
            display: none;
            flex-direction: column;
            animation: fadeForm 0.4s ease forwards;
        }

        form.active {
            display: flex;
        }

        @keyframes fadeForm {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .input-group input {
            padding: 12px;
            border-radius: var(--radius);
            border: 1px solid rgba(0, 0, 0, 0.2);
            outline: none;
            font-size: 0.95rem;
            width: 100%;
            transition: border var(--fast), box-shadow var(--fast), transform 0.2s ease;
        }

        .input-group input:focus {
            border-color: var(--primary-accent);
            box-shadow: 0 0 12px rgba(30, 142, 133, 0.3);
            transform: scale(1.02);
        }

        .input-group label {
            position: absolute;
            top: 12px;
            left: 12px;
            font-size: 0.9rem;
            color: rgba(0, 0, 0, 0.5);
            pointer-events: none;
            transition: all var(--fast);
        }

        .input-group input:focus+label,
        .input-group input:not(:placeholder-shown)+label {
            top: -8px;
            left: 10px;
            font-size: 0.75rem;
            background: var(--modal-bg);
            padding: 0 4px;
            color: var(--primary-accent);
        }

        button.submit-btn {
            background: linear-gradient(135deg, #1e8e85, #00bfa5);
            color: #fff;
            border: none;
            padding: 12px;
            border-radius: var(--radius);
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button.submit-btn:hover {
            background: linear-gradient(135deg, #00bfa5, #1e8e85);
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        @media(max-width:450px) {
            .container {
                width: 90%;
                padding: 1.5rem;
                font-size: 0.9rem;
            }

            .page-title h1 {
                font-size: 1.6rem;
            }

            .page-title span {
                font-size: 1.2rem;
            }
        }
    </style>
</head>

<body>

    <div class="page-title">
        <h1>HIRAYA</h1>
        <span>Your career journey starts here!</span>
    </div>

   <div class="bg-text">
    <span>HIRAYA</span>
    <span>WELCOME</span>
    <span>8.3</span>
    <span>SDG</span>
    <span>✨</span>
</div>


    <div class="container">
        <div class="tab-buttons">
            <button class="tab-btn <?php echo $active_form == 'signup' ? 'active' : ''; ?>" data-tab="signup">Sign Up</button>
            <button class="tab-btn <?php echo $active_form == 'login' ? 'active' : ''; ?>" data-tab="login">Log In</button>
        </div>

        <?php if ($show_message && $message != ""){ ?>
            <div class="form-message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php } ?>

        <!-- Sign Up Form -->
        <form id="signup-form" class="<?php echo $active_form == 'signup' ? 'active' : ''; ?>" method="POST">
            <div class="form-message">Join HIRAYA today! Create your account to start your career journey.</div>
            <div class="input-group"><input type="text" name="fullname" placeholder=" " required><label>Full
                    Name</label></div>
            <div class="input-group"><input type="email" name="email" placeholder=" "
                    required><label>Email</label></div>
            <div class="input-group"><input type="password" name="password" placeholder=" "
                    required><label>Password</label></div>
            <div class="input-group"><input type="password" name="confirm_password" placeholder=" "
                    required><label>Confirm Password</label></div>
            <button type="submit" class="submit-btn" name="signup">Sign Up</button>
</form>



        <!-- Log In Form -->
        <form id="login-form" class="<?php echo $active_form == 'login' ? 'active' : ''; ?>" method="POST">
            <div class="form-message">Welcome back! Please enter your credentials to log in.</div>
            <div class="input-group"><input type="email" name="email" placeholder=" " required><label>Email</label>
            </div>
            <div class="input-group"><input type="password" name="password" placeholder=" "
                    required><label>Password</label></div>
            <button type="submit" class="submit-btn" name="login">Log In</button>
        </form>
    </div>

    <script>
        /* Tab Switching */
        const tabs = document.querySelectorAll('.tab-btn');
        const forms = { login: document.getElementById('login-form'), signup: document.getElementById('signup-form') };
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                Object.values(forms).forEach(f => f.classList.remove('active'));
                forms[tab.dataset.tab].classList.add('active');
            });
        });

        /* Scatter background texts dynamically */
       const bgTexts = document.querySelectorAll('.bg-text span');

bgTexts.forEach(span => {
    const top = Math.random() * 95;
    const left = Math.random() * 95;
    const rotate = (Math.random() * 60) - 30;
    const fontSize = (Math.random() * 1.5) + 1.2;
    const opacity = (Math.random() * 0.15) + 0.1;

    span.style.top = `${top}%`;
    span.style.left = `${left}%`;
    span.style.fontSize = `${fontSize}rem`;
    span.style.opacity = opacity;

    span.style.setProperty('--rotate', `${rotate}deg`);
    span.style.setProperty('--opacity', opacity);

    span.style.setProperty('--x1', `${Math.random() * 30 - 15}px`);
    span.style.setProperty('--y1', `${Math.random() * 30 - 15}px`);
    span.style.setProperty('--x2', `${Math.random() * 30 - 15}px`);
    span.style.setProperty('--y2', `${Math.random() * 30 - 15}px`);
    span.style.setProperty('--x3', `${Math.random() * 30 - 15}px`);
    span.style.setProperty('--y3', `${Math.random() * 30 - 15}px`);
});

    </script>
</body>

</html>