<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

/**
 * 1. INITIALIZATION & ERROR PREVENTION
 * These defaults ensure that if the dashboard variables aren't 
 * loaded yet, the page won't show PHP Warnings.
 */
$impact_level = $impact_level ?? "Seed";
$verification_rate = $verification_rate ?? 0;

/**
 * 2. PATHS & SESSION DATA
 */
$base_path = "/sdg/"; 
$current_page = basename($_SERVER['PHP_SELF']);
$first_name = $_SESSION['first_name'] ?? "Employer";
$initial = strtoupper(substr($first_name, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HIRAYA | Employer Console</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/base.css">
</head>
<body class="bg-[#f8fafc] font-sans pt-20"> <div class="flex flex-col min-h-screen">
        
        <header class="fixed top-0 left-0 right-0 z-50 bg-[#06201d] text-white h-20 px-8 md:px-20 flex justify-between items-center shadow-2xl border-b border-white/5">
            
            <div class="flex items-center gap-4">
                <div class="bg-teal-600 w-11 h-11 rounded-xl flex items-center justify-center shadow-lg shadow-teal-900/40">
                    <span class="font-black text-white text-xl">H</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-black tracking-[0.2em] uppercase text-sm leading-none">Hiraya</span>
                    <span class="text-[9px] text-teal-400 font-bold uppercase tracking-widest mt-1">Employer Console</span>
                </div>
            </div>

            <nav class="hidden lg:flex items-center gap-12">
                <a href="<?php echo $base_path; ?>dashboard/employer/index.php" 
                   class="text-[10px] font-black uppercase tracking-[0.2em] transition-all duration-300 <?php echo ($current_page == 'index.php') ? 'text-teal-400 border-b-2 border-teal-400 pb-1' : 'text-gray-400 hover:text-white'; ?>">
                    Overview
                </a>
                <a href="<?php echo $base_path; ?>dashboard/employer/add-job.php" 
                   class="text-[10px] font-black uppercase tracking-[0.2em] transition-all duration-300 <?php echo ($current_page == 'add-job.php') ? 'text-teal-400 border-b-2 border-teal-400 pb-1' : 'text-gray-400 hover:text-white'; ?>">
                    Post Job
                </a>
                <a href="<?php echo $base_path; ?>dashboard/employer/profile.php" 
                   class="text-[10px] font-black uppercase tracking-[0.2em] transition-all duration-300 <?php echo ($current_page == 'companies.php') ? 'text-teal-400 border-b-2 border-teal-400 pb-1' : 'text-gray-400 hover:text-white'; ?>">
                    My Company
                </a>
            </nav>

            <div class="relative">
                <button onclick="toggleMenu('employerMenu')" id="empBtn" class="flex items-center gap-4 bg-white/5 hover:bg-white/10 px-5 py-2.5 rounded-2xl border border-white/10 transition-all duration-300 cursor-pointer">
                    <div class="w-8 h-8 bg-teal-500 rounded-lg flex items-center justify-center text-[10px] font-black text-white shadow-inner">
                        <?php echo $initial; ?>
                    </div>
                    <div class="hidden sm:flex flex-col items-start text-left">
                        <span class="text-[10px] font-black uppercase tracking-widest leading-none"><?php echo htmlspecialchars($first_name); ?></span>
                        <span class="text-[8px] font-bold text-teal-500 uppercase tracking-tighter mt-1">Verified Partner</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[8px] text-teal-400/50"></i>
                </button>

                <div id="employerMenu" class="hidden absolute right-0 mt-4 w-60 bg-white rounded-[1.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.2)] py-3 z-[60] text-gray-800 border border-gray-100 overflow-hidden ring-1 ring-black/5 animate-in fade-in slide-in-from-top-2 duration-200">
                    <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100 mb-2">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Logged in as</p>
                        <p class="text-xs font-bold text-gray-900 truncate"><?php echo htmlspecialchars($first_name); ?></p>
                    </div>
                    
                    <a href="<?php echo $base_path; ?>auth/logout.php" class="flex items-center gap-3 px-6 py-4 text-[10px] font-black text-red-500 uppercase tracking-widest hover:bg-red-50 transition-colors">
                        <i class="fa-solid fa-power-off w-4 text-center"></i> Sign Out
                    </a>
                </div>
            </div>
        </header>

<script>
    function toggleMenu(id) {
        const menu = document.getElementById(id);
        if (menu) menu.classList.toggle('hidden');
    }

    // Close menu when clicking outside
    window.addEventListener('click', function(e) {
        const menu = document.getElementById('employerMenu');
        const btn = document.getElementById('empBtn');
        if (menu && !menu.contains(e.target) && !btn.contains(e.target)) {
            menu.classList.add('hidden');
        }
    });
</script>