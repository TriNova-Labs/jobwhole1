<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
$base_path = "/SDG/"; 

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
    <link rel="stylesheet" href="<?php echo $css_path ?? $base_path . 'assets/css/bg.css'; ?>">
</head>
<body class="min-h-screen flex flex-col bg-gray-50 font-sans">

    <header class="fixed w-full z-50 bg-[#06201d]/90 backdrop-blur-md text-white py-4 px-8 flex justify-between items-center border-b border-white/5">
        <div class="flex items-center gap-2">
            <span class="bg-teal-600 p-1 px-3 rounded font-bold shadow-lg shadow-teal-900/20">H</span>
            <span class="font-bold tracking-widest uppercase text-sm">Hiraya</span>
        </div>

        <nav class="hidden md:flex gap-8 text-[11px] font-bold uppercase tracking-widest">
            <a href="<?php echo $base_path; ?>index.php" class="<?php echo ($current_page == 'index.php') ? 'text-teal-400' : 'text-gray-300 hover:text-white transition'; ?>">Home</a>
            <a href="<?php echo $base_path; ?>dashboard/user/jobs/jobs.php" class="<?php echo ($current_page == 'jobs.php') ? 'text-teal-400' : 'text-gray-300 hover:text-white transition'; ?>">Jobs</a>
            <a href="<?php echo $base_path; ?>dashboard/user/companies.php" class="<?php echo ($current_page == 'companies.php') ? 'text-teal-400' : 'text-gray-300 hover:text-white transition'; ?>">Companies</a>
            <a href="<?php echo $base_path; ?>dashboard/user/resources.php" class="<?php echo ($current_page == 'resources.php') ? 'text-teal-400' : 'text-gray-300 hover:text-white transition'; ?>">Resources</a>
        </nav> 

        <div class="relative inline-block text-left">
            <button onclick="toggleDropdown()" class="flex items-center gap-3 bg-white/5 hover:bg-white/10 px-4 py-2 rounded-full transition-all active:scale-95 cursor-pointer border border-white/10">
                <div class="w-7 h-7 bg-teal-500 rounded-full flex items-center justify-center text-[10px] font-bold text-white shadow-sm">
                    <?php echo htmlspecialchars($initial); ?>
                </div>
                <span class="text-white text-xs font-bold"><?php echo htmlspecialchars($first_name); ?></span>
                <i class="fa-solid fa-chevron-down text-[10px] text-gray-500"></i>
            </button>

            <div id="userMenu" class="hidden absolute right-0 mt-3 w-52 bg-white rounded-2xl shadow-2xl border border-gray-100 py-2 z-50 text-gray-800">
                <a href="<?php echo $base_path; ?>dashboard/user/index.php" class="flex items-center gap-3 px-4 py-3 text-xs font-bold hover:bg-gray-50 hover:text-teal-600 transition">
                    <i class="fa-solid fa-gauge-high w-4"></i> Dashboard
                </a>
                <div class="border-t border-gray-50 mt-1">
                    <a href="<?php echo $base_path; ?>auth/logout.php" 
                       onclick="return confirm('Are you sure you want to sign out?')"
                       class="flex items-center gap-3 px-4 py-3 text-xs font-bold text-red-500 hover:bg-red-50 transition-all active:scale-95">
                        <i class="fa-solid fa-right-from-bracket w-4"></i> Sign Out
                    </a>
                </div>
            </div>
        </div>
    </header>

    <script>
        function toggleDropdown() {
            document.getElementById('userMenu').classList.toggle('hidden');
        }
        
        // Close dropdown when clicking outside
        window.onclick = function(event) {
            if (!event.target.closest('button')) {
                const menu = document.getElementById('userMenu');
                if (!menu.classList.contains('hidden')) {
                    menu.classList.add('hidden');
                }
            }
        }
    </script>