<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JOB WHOLE | Where Inclusive Careers Begin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo $css_path; ?>">
<body class="bg-gray-50 font-sans">

    <header class="fixed w-full z-50 bg-opacity-90 backdrop-blur-md bg-[#06201d] text-white py-4 px-8 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <span class="bg-teal-600 p-1 px-3 rounded font-bold">J</span>
            <span class="font-bold tracking-widest">JOB WHOLE</span>
        </div>
        <nav class="hidden md:flex gap-8 text-sm font-medium">
    <a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'text-teal-400 border-b-2 border-teal-400' : 'hover:text-teal-400'; ?>">
        Home
    </a>
    <a href="jobs.php" class="<?php echo ($current_page == 'jobs.php') ? 'text-teal-400 border-b-2 border-teal-400' : 'hover:text-teal-400'; ?>">
        Jobs
    </a>
    <a href="companies.php" class="<?php echo ($current_page == 'companies.php') ? 'text-teal-400 border-b-2 border-teal-400' : 'hover:text-teal-400'; ?>">
        Companies
    </a>
    <a href="resources.php" class="<?php echo ($current_page == 'resources.php') ? 'text-teal-400 border-b-2 border-teal-400' : 'hover:text-teal-400'; ?>">
        Resources
    </a>
</nav>
        <div class="flex items-center gap-2 text-sm">
            <div class="w-8 h-8 bg-teal-800 rounded-full flex items-center justify-center">L</div>
            <span>Leonardson Bautista ▾</span>
        </div>
    </header>
    
    <?php
// Get the current file name (e.g., 'index.php' or 'jobs.php')
$current_page = basename($_SERVER['PHP_SELF']);
?>
