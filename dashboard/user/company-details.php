<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../../includes/db.php';

// 1. Get the Company ID from URL
$company_id = $_GET['id'] ?? null;

if (!$company_id) {
    header("Location: companies.php");
    exit;
}

try {
    // 2. Fetch Company Details
    $stmt = $pdo->prepare("SELECT * FROM companies WHERE id = ?");
    $stmt->execute([$company_id]);
    $company = $stmt->fetch();

    if (!$company) { 
        die("<div class='min-h-screen flex items-center justify-center font-sans text-gray-500'>Company not found. <a href='companies.php' class='ml-2 text-teal-600 underline'>Return to list</a></div>"); 
    }

    // 3. Fetch Jobs posted by this specific company
    // Using 'company_id' as the foreign key in the jobs table
    $jobStmt = $pdo->prepare("SELECT * FROM jobs WHERE company_id = ?");
    $jobStmt->execute([$company_id]);
    $jobs = $jobStmt->fetchAll();

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Header Variables
$base_path = "/SDG/";
$first_name = $_SESSION['first_name'] ?? "Guest";
$initial = strtoupper(substr($first_name, 0, 1));
$current_page = 'companies.php'; // Keep 'Companies' highlighted in nav
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($company['name']); ?> | HIRAYA Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 font-sans">

    <header class="fixed w-full z-50 bg-[#06201d]/90 backdrop-blur-md text-white py-4 px-8 flex justify-between items-center border-b border-white/5">
        <div class="flex items-center gap-2">
            <span class="bg-teal-600 p-1 px-3 rounded font-bold shadow-lg shadow-teal-900/20">H</span>
            <span class="font-bold tracking-widest uppercase text-sm">Hiraya</span>
        </div>

        <nav class="hidden md:flex gap-8 text-[11px] font-bold uppercase tracking-widest">
            <a href="<?php echo $base_path; ?>index.php" class="text-gray-300 hover:text-white transition">Home</a>
            <a href="<?php echo $base_path; ?>dashboard/user/jobs/jobs.php" class="text-gray-300 hover:text-white transition">Jobs</a>
            <a href="<?php echo $base_path; ?>dashboard/user/companies.php" class="text-teal-400">Companies</a>
            <a href="<?php echo $base_path; ?>dashboard/user/resources.php" class="text-gray-300 hover:text-white transition">Resources</a>
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
                    <a href="<?php echo $base_path; ?>auth/logout.php" onclick="return confirm('Are you sure?')" class="flex items-center gap-3 px-4 py-3 text-xs font-bold text-red-500 hover:bg-red-50 transition-all">
                        <i class="fa-solid fa-right-from-bracket w-4"></i> Sign Out
                    </a>
                </div>
            </div>
        </div>
    </header>
    <main class="pt-24 pb-20">
        <div class="max-w-6xl mx-auto bg-white rounded-3xl shadow-sm overflow-hidden border border-gray-100">
            <div class="h-64 bg-gradient-to-r from-[#06201d] to-teal-900 relative">
                <?php if(!empty($company['cover_photo'])): ?>
                    <img src="../../assets/img/covers/<?php echo $company['cover_photo']; ?>" class="w-full h-full object-cover opacity-80">
                <?php endif; ?>
                <div class="absolute inset-0 bg-black/10"></div>
            </div>

            <div class="px-10 pb-10 relative">
                <div class="flex flex-col md:flex-row items-end gap-8 -mt-20">
                    <div class="w-44 h-44 bg-white p-3 rounded-[2.5rem] shadow-xl relative z-10">
                        <div class="w-full h-full bg-teal-50 rounded-[2rem] flex items-center justify-center text-teal-600 text-6xl font-bold border border-teal-100">
                            <?php echo substr(htmlspecialchars($company['name']), 0, 1); ?>
                        </div>
                    </div>

                    <div class="flex-1 mb-4 text-center md:text-left">
                        <div class="flex flex-col md:flex-row md:items-center gap-3">
                            <h1 class="text-3xl font-black text-gray-900 leading-tight">
                                <?php echo htmlspecialchars($company['name']); ?>
                            </h1>
                            <?php if($company['is_verified']): ?>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-wider self-center">
                                    <i class="fa-solid fa-circle-check"></i> Verified
                                </span>
                            <?php endif; ?>
                        </div>
                        <p class="text-gray-500 font-bold text-sm mt-1 uppercase tracking-widest">
                            <?php echo htmlspecialchars($company['industry'] ?? 'Corporate Partner'); ?> • <?php echo htmlspecialchars($company['location']); ?>
                        </p>
                    </div>

                    <div class="flex gap-3 mb-6">
                        <?php if(!empty($company['website'])): ?>
                            <a href="<?php echo htmlspecialchars($company['website']); ?>" target="_blank" class="px-6 py-3 bg-gray-900 text-white rounded-2xl font-bold text-xs hover:bg-teal-600 transition shadow-lg shadow-gray-900/10">Visit Website</a>
                        <?php endif; ?>
                        <button class="px-6 py-3 bg-white border border-gray-200 text-gray-700 rounded-2xl font-bold text-xs hover:bg-gray-50 transition">Follow</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-6xl mx-auto mt-8 grid grid-cols-1 lg:grid-cols-12 gap-8 px-4 lg:px-0">
            
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100">
                    <h2 class="text-xs font-black uppercase tracking-[0.2em] text-gray-400 mb-6">Company Overview</h2>
                    <p class="text-gray-600 text-sm leading-relaxed mb-8">
                        <?php echo nl2br(htmlspecialchars($company['description'])); ?>
                    </p>
                    <div class="space-y-5 pt-6 border-t border-gray-50">
                        <div class="flex items-center gap-4 text-sm font-bold text-gray-700">
                            <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-teal-600">
                                <i class="fa-solid fa-users"></i>
                            </div>
                            <span><?php echo htmlspecialchars($company['size'] ?? '51-200'); ?> Employees</span>
                        </div>
                        <div class="flex items-center gap-4 text-sm font-bold text-gray-700">
                            <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-teal-600">
                                <i class="fa-solid fa-earth-asia"></i>
                            </div>
                            <span class="truncate"><?php echo htmlspecialchars($company['location']); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-8">
                <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center mb-8">
                        <h2 class="text-xs font-black uppercase tracking-[0.2em] text-gray-400">Current Job Openings</h2>
                        <span class="px-3 py-1 bg-teal-50 text-teal-700 rounded-lg text-[10px] font-black"><?php echo count($jobs); ?> POSTS</span>
                    </div>
                    
                    <div class="space-y-4">
                        <?php if(count($jobs) > 0): ?>
                            <?php foreach($jobs as $job): ?>
                                <a href="jobs/job-details.php?id=<?php echo $job['id']; ?>" class="flex items-center justify-between p-5 border border-gray-50 rounded-2xl hover:border-teal-500/20 hover:bg-teal-50/10 transition-all group">
                                    <div class="flex items-center gap-5">
                                        <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-400 group-hover:bg-teal-600 group-hover:text-white transition-all duration-300">
                                            <i class="fa-solid fa-briefcase text-xl"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900 group-hover:text-teal-600 transition-colors"><?php echo htmlspecialchars($job['title']); ?></h4>
                                            <div class="flex gap-3 mt-1">
                                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400"><?php echo htmlspecialchars($job['job_type']); ?></span>
                                                <span class="text-[10px] font-black uppercase tracking-widest text-teal-600"><?php echo htmlspecialchars($job['location']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <span class="text-[10px] font-bold text-gray-400 bg-gray-50 px-3 py-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity">View Details</span>
                                        <i class="fa-solid fa-arrow-right text-gray-300 group-hover:translate-x-1 group-hover:text-teal-600 transition-all"></i>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-16">
                                <i class="fa-solid fa-briefcase text-4xl text-gray-100 mb-4"></i>
                                <p class="text-gray-400 text-sm font-medium italic">This organization hasn't posted any jobs yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function toggleDropdown() {
            document.getElementById('userMenu').classList.toggle('hidden');
        }
        window.onclick = function(event) {
            if (!event.target.closest('button')) {
                const menu = document.getElementById('userMenu');
                if (menu && !menu.classList.contains('hidden')) menu.classList.add('hidden');
            }
        }
    </script>
</body>
</html>