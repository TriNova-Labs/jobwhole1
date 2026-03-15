<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Base path configuration
$base_path = "/sdg/"; 
$current_page = basename($_SERVER['PHP_SELF']);

// Auth & Variable Check
$first_name = $_SESSION['first_name'] ?? "Admin";
$initial = strtoupper(substr($first_name, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HIRAYA | Admin Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/base.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#f8fafc] pt-20"> 
    <div class="flex flex-col min-h-screen">
        
        <header class="fixed top-0 left-0 right-0 z-[100] bg-[#06201d] text-white h-20 px-8 md:px-20 flex justify-between items-center shadow-2xl border-b border-red-900/30">
            
            <div class="flex items-center gap-4 group cursor-default">
                <div class="bg-red-600 w-11 h-11 rounded-xl flex items-center justify-center shadow-lg shadow-red-900/40 group-hover:scale-110 transition-transform duration-500">
                    <span class="font-black text-white text-xl">A</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-black tracking-[0.2em] uppercase text-sm leading-none">Hiraya</span>
                    <span class="text-[9px] text-red-400 font-bold uppercase tracking-widest mt-1">System Control</span>
                </div>
            </div>

            <nav class="hidden lg:flex items-center gap-10">
                <?php 
                $nav_items = [
                    'index.php' => 'Overview',
                    'manage-users.php' => 'Users',
                    'manage-jobs.php' => 'Verify Jobs'
                ];

                foreach ($nav_items as $page => $label): 
                    $is_active = ($current_page == $page);
                ?>
                    <a href="<?php echo $base_path; ?>dashboard/admin/<?php echo $page; ?>" 
                       class="text-[10px] font-black uppercase tracking-[0.2em] transition-all duration-300 relative py-2 <?php echo $is_active ? 'text-red-400' : 'text-gray-400 hover:text-white'; ?>">
                        <?php echo $label; ?>
                        <?php if ($is_active): ?>
                            <span class="absolute bottom-0 left-0 w-full h-0.5 bg-red-400 rounded-full"></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="relative">
                <button onclick="toggleMenu('adminMenu')" id="adminBtn" class="flex items-center gap-4 bg-white/5 hover:bg-white/10 px-5 py-2.5 rounded-2xl border border-white/10 transition-all duration-300">
                    <div class="w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center text-[10px] font-black text-white shadow-inner">
                        <?php echo $initial; ?>
                    </div>
                    <div class="hidden sm:flex flex-col items-start text-left">
                        <span class="text-[10px] font-black uppercase tracking-widest leading-none">Admin Control</span>
                        <span class="text-[8px] font-bold text-red-500 uppercase tracking-tighter mt-1">Root Access</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[8px] text-gray-500 group-hover:text-white transition-colors"></i>
                </button>

                <div id="adminMenu" class="hidden absolute right-0 mt-4 w-64 bg-white rounded-[1.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.2)] py-2 z-[110] text-gray-800 border border-gray-100 ring-1 ring-black/5 overflow-hidden">
                    <div class="px-6 py-5 bg-gray-50/80 border-b border-gray-100 mb-1">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Authenticated As</p>
                        <p class="text-xs font-black text-[#06201d] truncate"><?php echo htmlspecialchars($first_name); ?> (Staff)</p>
                    </div>
                    
                    <a href="<?php echo $base_path; ?>dashboard/admin/settings.php" class="flex items-center gap-3 px-6 py-4 text-[10px] font-black text-gray-500 uppercase tracking-widest hover:bg-gray-50 hover:text-[#06201d] transition-all">
                        <i class="fa-solid fa-sliders w-4"></i> System Settings
                    </a>
                    
                    <div class="h-px bg-gray-100 mx-4"></div>
                    
                    <a href="<?php echo $base_path; ?>auth/logout.php" class="flex items-center gap-3 px-6 py-4 text-[10px] font-black text-red-600 uppercase tracking-widest hover:bg-red-50 transition-all">
                        <i class="fa-solid fa-power-off w-4"></i> Terminate Session
                    </a>
                </div>
            </div>
        </header>

<script>
    function toggleMenu(id) {
        const menu = document.getElementById(id);
        if (menu) {
            menu.classList.toggle('hidden');
            menu.classList.toggle('animate-in');
            menu.classList.toggle('fade-in');
            menu.classList.toggle('slide-in-from-top-2');
        }
    }

    // Close menu when clicking outside
    window.addEventListener('click', function(e) {
        const adminMenu = document.getElementById('adminMenu');
        const adminBtn = document.getElementById('adminBtn');

        if (adminMenu && !adminMenu.contains(e.target) && !adminBtn.contains(e.target)) {
            adminMenu.classList.add('hidden');
        }
    });
</script>