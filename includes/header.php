<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get the current file name for navigation highlighting
$current_page = basename($_SERVER['PHP_SELF']);

// Set dynamic user data based on session, or fallback to Guest
$first_name = $_SESSION['first_name'] ?? "Guest";
$last_name = $_SESSION['last_name'] ?? "User";
$full_name = $first_name . " " . $last_name;
$initial = strtoupper(substr($first_name, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HIRAYA | Where Inclusive Careers Begin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $css_path ?? 'assets/css/bg.css'; ?>">
</head>
<body class="bg-gray-50 font-sans">

    <header class="fixed w-full z-50 bg-opacity-90 backdrop-blur-md bg-[#06201d] text-white py-4 px-8 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <span class="bg-teal-600 p-1 px-3 rounded font-bold">H</span>
            <span class="font-bold tracking-widest uppercase">Hiraya</span>
        </div>

        <nav class="hidden md:flex gap-8 text-sm font-medium">
            <a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'text-teal-400 border-b-2 border-teal-400' : 'hover:text-teal-400 transition'; ?>">
                Home
            </a>
            <a href="jobs.php" class="<?php echo ($current_page == 'jobs.php') ? 'text-teal-400 border-b-2 border-teal-400' : 'hover:text-teal-400 transition'; ?>">
                Jobs
            </a>
            <a href="companies.php" class="<?php echo ($current_page == 'companies.php') ? 'text-teal-400 border-b-2 border-teal-400' : 'hover:text-teal-400 transition'; ?>">
                Companies
            </a>
            <a href="resources.php" class="<?php echo ($current_page == 'resources.php') ? 'text-teal-400 border-b-2 border-teal-400' : 'hover:text-teal-400 transition'; ?>">
                Resources
            </a>
        </nav>

        <div class="relative inline-block text-left">
            <button onclick="toggleDropdown()" class="flex items-center gap-3 bg-white/10 hover:bg-white/20 px-4 py-2 rounded-full transition cursor-pointer border border-white/10">
                <div class="w-8 h-8 bg-teal-500 rounded-full flex items-center justify-center text-sm font-bold text-white shadow-sm">
                    <?php echo htmlspecialchars($initial); ?>
                </div>
                <span class="text-white text-sm font-medium">
                    <?php echo htmlspecialchars($full_name); ?>
                </span>
                <i class="fa-solid fa-chevron-down text-[10px] text-gray-400"></i>
            </button>

            <div id="userMenu" class="hidden absolute right-0 mt-3 w-52 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50 overflow-hidden text-gray-800">
                <div class="px-4 py-2 border-b border-gray-50 mb-1">
                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Account Menu</p>
                </div>
                
                <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 text-sm hover:bg-teal-50 hover:text-teal-600 transition">
                    <i class="fa-solid fa-gauge-high w-4"></i> Dashboard
                </a>
                <a href="profile.php" class="flex items-center gap-3 px-4 py-3 text-sm hover:bg-teal-50 hover:text-teal-600 transition">
                    <i class="fa-solid fa-user w-4"></i> My Profile
                </a>
                <a href="settings.php" class="flex items-center gap-3 px-4 py-3 text-sm hover:bg-teal-50 hover:text-teal-600 transition">
                    <i class="fa-solid fa-gear w-4"></i> Settings
                </a>
                
                <div class="border-t border-gray-100 mt-1">
                    <a href="auth/logout.php" class="flex items-center gap-3 px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition">
                        <i class="fa-solid fa-right-from-bracket w-4"></i> Sign Out
                    </a>
                </div>
            </div>
        </div>
    </header>

    <script>
        function toggleDropdown() {
            const menu = document.getElementById('userMenu');
            menu.classList.toggle('hidden');
        }

        // Close the dropdown if clicking outside
        window.addEventListener('click', function(e) {
            const menu = document.getElementById('userMenu');
            const button = menu.previousElementSibling;
            if (menu && !menu.contains(e.target) && !button.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>