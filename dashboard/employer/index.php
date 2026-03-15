<?php
session_start();
require_once "../../includes/db.php";

// Auth Check
if (!isset($_SESSION['user_id']) || $_SESSION['account_type'] !== 'employer') {
    header("Location: ../../auth/employer/login.php");
    exit();
}

$employer_id = $_SESSION['user_id'];
$first_name = $_SESSION['first_name'];

// Initialize variables
$impact_level = "Seed";
$impact_color = "text-teal-300";
$total_jobs = 0;
$active_count = 0;
$total_applications = 0; 
$total_hires = 0; // NEW: Track successful hires
$my_jobs = [];
$company_name = "Your Company";

try {
    // 1. Fetch Jobs and Application Counts
    $stmt = $pdo->prepare("
        SELECT j.*, c.name as category_name,
        (SELECT COUNT(*) FROM applications WHERE job_id = j.id) as app_count
        FROM jobs j 
        LEFT JOIN categories c ON j.category_id = c.id 
        WHERE j.employer_id = ? 
        ORDER BY j.posted_date DESC
    ");
    $stmt->execute([$employer_id]);
    $my_jobs = $stmt->fetchAll();

    $total_jobs = count($my_jobs);
    
    foreach($my_jobs as $j) { 
        if($j['is_active'] == 1) $active_count++; 
        $total_applications += $j['app_count']; 
    }

    // 2. NEW: Count total hired candidates ('accepted' status)
    $stmt_hires = $pdo->prepare("
        SELECT COUNT(*) 
        FROM applications a
        JOIN jobs j ON a.job_id = j.id
        WHERE j.employer_id = ? AND a.status = 'accepted'
    ");
    $stmt_hires->execute([$employer_id]);
    $total_hires = $stmt_hires->fetchColumn();

    // 3. Impact Level Logic
    if ($active_count >= 10) {
        $impact_level = "Champion";
        $impact_color = "text-yellow-400";
    } elseif ($active_count >= 5) {
        $impact_level = "Advocate";
        $impact_color = "text-emerald-300";
    } elseif ($active_count >= 1) {
        $impact_level = "Contributor";
        $impact_color = "text-teal-200";
    }

    // 4. Fetch Company Name
    $stmt_comp = $pdo->prepare("SELECT name FROM companies WHERE employer_id = ? LIMIT 1");
    $stmt_comp->execute([$employer_id]);
    $company = $stmt_comp->fetch();
    if ($company) { $company_name = $company['name']; }

} catch (PDOException $e) {
    error_log("Dashboard Error: " . $e->getMessage());
}
?>

<?php include '../../includes/employer/header.php'; ?>

<?php 
$status = $_GET['status'] ?? $_GET['update'] ?? null;
if ($status): 
    $is_new = ($status == 'proposed');
?>
    <div id="toast" class="fixed top-10 left-1/2 -translate-x-1/2 z-[200] animate-in slide-in-from-top duration-500">
        <div class="bg-[#06201d] text-white px-8 py-4 rounded-3xl shadow-2xl flex items-center gap-4 border border-teal-500/30 backdrop-blur-xl">
            <div class="w-8 h-8 bg-teal-500/20 rounded-full flex items-center justify-center">
                <i class="fa-solid fa-check text-teal-400 text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-teal-400">System Notification</p>
                <p class="text-xs font-bold tracking-tight">
                    <?= $is_new ? 'New Opportunity Submitted for Review' : 'Listing Successfully Updated' ?>
                </p>
            </div>
        </div>
    </div>
    <script>setTimeout(() => { document.getElementById('toast').classList.add('opacity-0', 'translate-y-[-20px]'); setTimeout(() => document.getElementById('toast').remove(), 500); }, 4000);</script>
<?php endif; ?>

<main class="pt-28 px-8 md:px-24 bg-gray-50 min-h-screen pb-20 text-left">
    <div class="max-w-7xl mx-auto">
        
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12 gap-4">
            <div>
                <h1 class="text-4xl font-black text-[#06201d] tracking-tight">Dashboard</h1>
                <p class="text-gray-500 mt-1 font-medium">Managing talent for <span class="text-teal-600 font-bold"><?= htmlspecialchars($company_name); ?></span></p>
            </div>
            <a href="add-job.php" class="group bg-[#06201d] hover:bg-teal-700 text-white px-8 py-4 rounded-2xl font-black uppercase tracking-widest text-[10px] transition-all flex items-center gap-3 shadow-xl shadow-teal-900/20">
                <i class="fa-solid fa-plus group-hover:rotate-90 transition-transform"></i> Post New Job
            </a>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 relative overflow-hidden">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Active Listings</p>
                <h3 class="text-4xl font-black text-[#06201d]"><?= $active_count; ?></h3>
                <div class="absolute right-0 bottom-0 p-4 opacity-[0.03] text-6xl"><i class="fa-solid fa-bolt"></i></div>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 relative overflow-hidden">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Total Reach</p>
                <h3 class="text-4xl font-black text-[#06201d]"><?= $total_applications; ?></h3>
                <div class="absolute right-0 bottom-0 p-4 opacity-[0.03] text-6xl"><i class="fa-solid fa-users"></i></div>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-teal-100 relative overflow-hidden group hover:border-teal-300 transition-colors">
                <p class="text-[10px] font-black text-teal-600 uppercase tracking-[0.2em] mb-1">Successful Hires</p>
                <h3 class="text-4xl font-black text-[#06201d]"><?= $total_hires; ?></h3>
                <div class="absolute right-0 bottom-0 p-4 opacity-[0.05] text-6xl text-teal-600"><i class="fa-solid fa-handshake"></i></div>
                <a href="manage-applications.php?status=accepted" class="mt-2 inline-block text-[9px] font-black uppercase text-teal-600 hover:text-[#06201d] transition-colors">View Team →</a>
            </div>
            
            <div class="bg-[#06201d] p-8 rounded-[2.5rem] shadow-2xl shadow-teal-900/30 text-white relative overflow-hidden group">
                <i class="fa-solid fa-leaf absolute -right-4 -bottom-4 text-white/5 text-8xl group-hover:rotate-12 transition-transform duration-700"></i>
                <div class="relative z-10">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-[10px] font-black text-teal-400 uppercase tracking-[0.2em] mb-1">Impact Level</p>
                            <h3 class="text-2xl font-black italic tracking-tighter">SDG 8.3 Contributor</h3>
                        </div>
                        <span class="px-3 py-1 bg-white/10 rounded-full text-[9px] font-black uppercase tracking-widest border border-white/10">
                            <?= $impact_level; ?>
                        </span>
                    </div>
                    <div class="mt-6 flex items-end gap-2">
                        <span class="text-4xl font-black <?= $impact_color; ?>">
                            <?= ($total_jobs > 0) ? round(($active_count / $total_jobs) * 100) : 0; ?>%
                        </span>
                        <span class="text-[9px] font-bold text-teal-200/40 mb-2 uppercase tracking-widest">Verification Rate</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[3rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-10 border-b border-gray-50 flex justify-between items-center">
                <h2 class="text-xl font-black text-[#06201d] tracking-tight">Managed Opportunities</h2>
                <div class="flex gap-2">
                    <div class="w-3 h-3 rounded-full bg-teal-500"></div>
                    <div class="w-3 h-3 rounded-full bg-orange-400"></div>
                    <div class="w-3 h-3 rounded-full bg-gray-200"></div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 text-gray-400 text-[10px] font-black uppercase tracking-[0.2em]">
                            <th class="px-10 py-6">Role & Location</th>
                            <th class="px-10 py-6 text-center">Applicants</th>
                            <th class="px-10 py-6">Date Posted</th>
                            <th class="px-10 py-6">Status</th>
                            <th class="px-10 py-6 text-right">Management</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php if (empty($my_jobs)): ?>
                            <tr>
                                <td colspan="5" class="px-10 py-32 text-center">
                                    <div class="max-w-xs mx-auto">
                                        <i class="fa-solid fa-cloud-upload text-6xl text-gray-100 mb-6 block"></i>
                                        <p class="font-black text-[#06201d] text-sm uppercase tracking-widest">No Listings Found</p>
                                        <p class="text-gray-400 text-xs mt-2 font-medium leading-relaxed">Start connecting with talent by posting your first ethical job opportunity.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($my_jobs as $job): ?>
                                <tr class="group hover:bg-gray-50/80 transition-all <?= (!empty($job['admin_feedback'])) ? 'bg-red-50/20' : ''; ?>">
                                    <td class="px-10 py-8">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-white group-hover:text-teal-600 transition-all border border-transparent group-hover:border-gray-100 shadow-sm">
                                                <i class="fa-solid <?= $job['icon'] ?: 'fa-briefcase' ?> text-lg"></i>
                                            </div>
                                            <div>
                                                <div class="font-black text-[#06201d] text-base"><?= htmlspecialchars($job['title']); ?></div>
                                                <div class="text-[10px] font-bold text-gray-400 mt-0.5 uppercase tracking-widest flex items-center gap-2">
                                                    <i class="fa-solid fa-location-dot text-teal-500/50"></i> <?= htmlspecialchars($job['location']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="px-10 py-8 text-center">
                                        <a href="manage-applications.php?job_id=<?= $job['id']; ?>" class="group/btn inline-flex flex-col items-center">
                                            <span class="text-xl font-black text-[#06201d] group-hover/btn:text-teal-600 transition-colors"><?= $job['app_count']; ?></span>
                                            <span class="text-[8px] font-black uppercase text-teal-600 bg-teal-50 px-2 py-0.5 rounded-md opacity-0 group-hover/btn:opacity-100 transition-all">Review</span>
                                        </a>
                                    </td>

                                    <td class="px-10 py-8 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        <?= date('M d, Y', strtotime($job['posted_date'])); ?>
                                    </td>
                                    <td class="px-10 py-8">
                                        <?php if ($job['is_active'] == 1): ?>
                                            <span class="inline-flex items-center gap-2 px-4 py-2 text-[10px] font-black uppercase tracking-widest bg-teal-50 text-teal-600 rounded-xl border border-teal-100">
                                                <span class="w-1.5 h-1.5 bg-teal-500 rounded-full animate-pulse"></span> Active
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-2 px-4 py-2 text-[10px] font-black uppercase tracking-widest bg-orange-50 text-orange-600 rounded-xl border border-orange-100">
                                                Pending
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-10 py-8 text-right">
                                        <div class="flex justify-end gap-3">
                                            <a href="manage-applications.php?job_id=<?= $job['id']; ?>" title="Manage Applications" class="w-10 h-10 flex items-center justify-center rounded-xl bg-teal-600 text-white hover:bg-[#06201d] transition-all shadow-lg shadow-teal-900/10">
                                                <i class="fa-solid fa-users-gear text-xs"></i>
                                            </a>
                                            <a href="edit-job.php?id=<?= $job['id']; ?>" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-gray-100 text-gray-400 hover:text-teal-600 hover:border-teal-200 transition-all shadow-sm"><i class="fa-solid fa-pen-nib"></i></a>
                                            <a href="delete_job.php?id=<?= $job['id']; ?>" onclick="return confirm('Archive this listing?')" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-gray-100 text-gray-400 hover:text-red-500 hover:border-red-200 transition-all shadow-sm"><i class="fa-solid fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>