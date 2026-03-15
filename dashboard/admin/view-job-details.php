<?php
session_start();
require_once "../../includes/db.php";

// Admin Auth Check
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../auth/admin/login.php");
    exit();
}

// Fetch Job ID
$job_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch Job Details with Company Info
$stmt = $pdo->prepare("SELECT jobs.*, companies.name as company_name, companies.location as company_loc 
                       FROM jobs 
                       JOIN companies ON jobs.company_id = companies.id 
                       WHERE jobs.id = ?");
$stmt->execute([$job_id]);
$job = $stmt->fetch();

if (!$job) {
    die("Job listing not found.");
}

include '../../includes/header.php';
?>

<main class="bg-gray-50 min-h-screen pt-32 pb-20 text-left">
    <div class="max-w-4xl mx-auto px-6">
        
        <div class="mb-10 flex justify-between items-center">
            <a href="manage-jobs.php" class="text-xs font-black uppercase tracking-widest text-teal-600 hover:text-teal-700 transition flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Back to Queue
            </a>
            <span class="px-4 py-1.5 bg-orange-50 text-orange-600 text-[10px] font-black uppercase tracking-widest rounded-full border border-orange-100">
                Pending Review
            </span>
        </div>

        <div class="bg-white rounded-[3rem] border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-10 border-b border-gray-50 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex items-center gap-6 mb-6">
                    <div class="w-16 h-16 bg-[#06201d] text-teal-400 rounded-2xl flex items-center justify-center text-2xl shadow-xl">
                        <i class="fa-solid <?php echo htmlspecialchars($job['icon']); ?>"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-black text-gray-900 tracking-tight"><?php echo htmlspecialchars($job['title']); ?></h1>
                        <p class="text-gray-500 font-bold uppercase text-xs tracking-widest mt-1">
                            <?php echo htmlspecialchars($job['company_name']); ?> • <?php echo htmlspecialchars($job['location']); ?>
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <div class="px-4 py-2 bg-white border border-gray-100 rounded-xl text-[10px] font-black uppercase tracking-widest text-gray-500">
                        <i class="fa-solid fa-money-bill-wave mr-1 text-teal-600"></i> 
                        ₱<?php echo number_format($job['salary_min']); ?> - ₱<?php echo number_format($job['salary_max']); ?>
                    </div>
                    <div class="px-4 py-2 bg-white border border-gray-100 rounded-xl text-[10px] font-black uppercase tracking-widest text-gray-500">
                        <i class="fa-solid fa-briefcase mr-1 text-teal-600"></i> Full-time
                    </div>
                </div>
            </div>

            <div class="p-10 space-y-10">
                <section>
                    <h2 class="text-[10px] font-black uppercase tracking-widest text-teal-600 mb-4">Job Description</h2>
                    <div class="text-gray-600 leading-relaxed text-sm prose max-w-none">
                        <?php echo nl2br(htmlspecialchars($job['description'])); ?>
                    </div>
                </section>

                <section>
                    <h2 class="text-[10px] font-black uppercase tracking-widest text-teal-600 mb-4">Requirements & Skills</h2>
                    <div class="text-gray-600 leading-relaxed text-sm">
                        <?php echo nl2br(htmlspecialchars($job['requirements'] ?? 'No specific requirements listed.')); ?>
                    </div>
                </section>
            </div>

            <div class="p-8 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <button class="text-[10px] font-black uppercase tracking-widest text-red-400 hover:text-red-600 transition">
                    Flag for Correction
                </button>
                
                <div class="flex gap-3">
                    <a href="manage-jobs.php?approve_id=<?php echo $job['id']; ?>" 
                       class="px-8 py-4 bg-teal-600 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-teal-700 transition shadow-lg shadow-teal-900/10">
                        Approve & Publish
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>