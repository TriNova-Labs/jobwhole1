<?php
include '../../includes/db.php'; 

// 1. ACTION LOGIC: Handle approvals/deletions before the header to prevent 'Headers already sent' errors
if (isset($_GET['action']) && isset($_GET['id'])) {
    $job_id = intval($_GET['id']);
    
    if ($_GET['action'] === 'approve') {
        $stmt = $pdo->prepare("UPDATE jobs SET is_active = 1 WHERE id = ?");
        $stmt->execute([$job_id]);
        header("Location: manage-jobs.php?msg=approved");
        exit();
    } elseif ($_GET['action'] === 'reject') {
        $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ?");
        $stmt->execute([$job_id]);
        header("Location: manage-jobs.php?msg=deleted");
        exit();
    }
}

// 2. HEADER: Includes the standardized flex-wrapper
include '../../includes/admin/header.php';

// 3. DATA FETCHING: Get all jobs with their company names
$query = "SELECT j.*, c.name AS company_name 
          FROM jobs j 
          JOIN companies c ON j.company_id = c.id 
          ORDER BY j.is_active ASC, j.posted_date DESC";
$all_jobs = $pdo->query($query)->fetchAll();

// Count pending jobs for the badge
$pending_count = 0;
foreach($all_jobs as $job) if($job['is_active'] == 0) $pending_count++;
?>

<main class="flex-grow bg-gray-50 pt-32 pb-20 px-8">
    <div class="max-w-6xl mx-auto">
        
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-10">
            <div>
                <h1 class="text-4xl font-black text-[#06201d] tracking-tight">Job Management</h1>
                <p class="text-gray-500 mt-1">Review, verify, or remove active listings on the platform.</p>
            </div>
            <div class="flex items-center gap-3 bg-teal-50 border border-teal-100 px-5 py-3 rounded-2xl">
                <span class="text-teal-700 font-bold text-xs uppercase tracking-widest">
                    <?php echo $pending_count; ?> Pending Actions
                </span>
                <div class="w-2 h-2 bg-teal-500 rounded-full <?php echo $pending_count > 0 ? 'animate-pulse' : ''; ?>"></div>
            </div>
        </header>

        <?php if (isset($_GET['msg'])): ?>
            <div class="mb-8 p-4 rounded-2xl bg-[#06201d] text-teal-400 text-sm font-bold flex items-center gap-3 border border-teal-900/50">
                <i class="fa-solid fa-circle-check"></i>
                Listing has been successfully <?php echo htmlspecialchars($_GET['msg']); ?>.
            </div>
        <?php endif; ?>

        <div class="grid gap-6">
            <?php if (count($all_jobs) > 0): ?>
                <?php foreach ($all_jobs as $job): ?>
                    <div class="bg-white p-8 rounded-[2.5rem] border <?php echo $job['is_active'] == 0 ? 'border-amber-200' : 'border-gray-100'; ?> shadow-sm hover:shadow-xl transition-all duration-300">
                        <div class="flex flex-col lg:flex-row justify-between gap-8">
                            
                            <div class="flex gap-6">
                                <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center text-teal-600 text-2xl border border-gray-100 shrink-0">
                                    <i class="fa-solid <?php echo htmlspecialchars($job['icon'] ?? 'fa-briefcase'); ?>"></i>
                                </div>
                                
                                <div>
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="font-black text-xl text-gray-900 tracking-tight"><?php echo htmlspecialchars($job['title']); ?></h3>
                                        <?php if ($job['is_active'] == 0): ?>
                                            <span class="px-2 py-1 bg-amber-100 text-amber-600 text-[9px] font-black uppercase rounded-lg">Verification Required</span>
                                        <?php else: ?>
                                            <span class="px-2 py-1 bg-teal-100 text-teal-600 text-[9px] font-black uppercase rounded-lg">Active</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <p class="text-teal-600 font-black text-xs uppercase tracking-widest mb-4">
                                        <?php echo htmlspecialchars($job['company_name']); ?>
                                    </p>
                                    
                                    <div class="flex flex-wrap gap-4 text-[11px] text-gray-400 font-bold uppercase tracking-wider">
                                        <span class="flex items-center gap-2"><i class="fa-solid fa-location-dot text-teal-500/40"></i> <?php echo htmlspecialchars($job['location']); ?></span>
                                        <span class="flex items-center gap-2"><i class="fa-solid fa-wallet text-teal-500/40"></i> ₱<?php echo number_format($job['salary_min']); ?> - <?php echo number_format($job['salary_max']); ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <?php if ($job['is_active'] == 0): ?>
                                    <a href="?action=approve&id=<?php echo $job['id']; ?>" class="flex-1 lg:flex-none text-center px-8 py-4 bg-teal-600 text-white rounded-2xl font-bold hover:bg-teal-700 transition shadow-lg shadow-teal-900/10 text-sm">
                                        Approve
                                    </a>
                                <?php endif; ?>
                                <a href="?action=reject&id=<?php echo $job['id']; ?>" onclick="return confirm('Permanently delete this listing?')" class="flex-1 lg:flex-none text-center px-8 py-4 bg-red-50 text-red-500 rounded-2xl font-bold hover:bg-red-500 hover:text-white transition text-sm">
                                    Delete
                                </a>
                            </div>
                        </div>
                        
                        <div class="mt-8 pt-6 border-t border-gray-50">
                            <h4 class="text-[10px] font-black text-gray-300 uppercase tracking-[0.2em] mb-3">Description Preview</h4>
                            <p class="text-gray-500 text-sm leading-relaxed italic">
                                "<?php echo nl2br(htmlspecialchars(substr($job['description'], 0, 200))); ?>..."
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="py-24 text-center bg-white rounded-[3rem] border border-dashed border-gray-200">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fa-solid fa-cloud-moon text-gray-200 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-300 tracking-tight">The job board is currently empty</h3>
                    <p class="text-gray-400 text-sm mt-2">Active postings from employers will appear here.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>