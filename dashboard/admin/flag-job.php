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
            <div class="flex gap-2">
                <?php if($job['is_active'] == 0 && !empty($job['admin_feedback'])): ?>
                    <span class="px-4 py-1.5 bg-red-50 text-red-600 text-[10px] font-black uppercase tracking-widest rounded-full border border-red-100">
                        Awaiting Correction
                    </span>
                <?php elseif($job['is_active'] == 0): ?>
                    <span class="px-4 py-1.5 bg-orange-50 text-orange-600 text-[10px] font-black uppercase tracking-widest rounded-full border border-orange-100">
                        Pending Review
                    </span>
                <?php else: ?>
                    <span class="px-4 py-1.5 bg-teal-50 text-teal-600 text-[10px] font-black uppercase tracking-widest rounded-full border border-teal-100">
                        Published
                    </span>
                <?php endif; ?>
            </div>
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
                        <i class="fa-solid fa-calendar-day mr-1 text-teal-600"></i> 
                        Posted <?php echo date('M d, Y', strtotime($job['created_at'])); ?>
                    </div>
                </div>
            </div>

            <div class="p-10 space-y-10">
                <section>
                    <h2 class="text-[10px] font-black uppercase tracking-widest text-teal-600 mb-4">Job Description</h2>
                    <div class="text-gray-600 leading-relaxed text-sm">
                        <?php echo nl2br(htmlspecialchars($job['description'])); ?>
                    </div>
                </section>

                <section>
                    <h2 class="text-[10px] font-black uppercase tracking-widest text-teal-600 mb-4">Requirements & Skills</h2>
                    <div class="text-gray-600 leading-relaxed text-sm">
                        <?php echo nl2br(htmlspecialchars($job['requirements'] ?? 'No specific requirements listed.')); ?>
                    </div>
                </section>

                <?php if(!empty($job['admin_feedback'])): ?>
                <section class="p-6 bg-red-50 rounded-2xl border border-red-100">
                    <h2 class="text-[10px] font-black uppercase tracking-widest text-red-600 mb-2">Previous Admin Feedback</h2>
                    <p class="text-sm text-red-700 italic">"<?php echo htmlspecialchars($job['admin_feedback']); ?>"</p>
                </section>
                <?php endif; ?>
            </div>

            <div class="p-8 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <button onclick="toggleModal()" class="text-[10px] font-black uppercase tracking-widest text-red-400 hover:text-red-600 transition">
                    Flag for Correction
                </button>
                
                <div class="flex gap-3">
                    <?php if($job['is_active'] == 0): ?>
                    <a href="manage-jobs.php?approve_id=<?php echo $job['id']; ?>" 
                       class="px-8 py-4 bg-teal-600 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-teal-700 transition shadow-lg shadow-teal-900/10">
                        Approve & Publish
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<div id="feedbackModal" class="hidden fixed inset-0 bg-[#06201d]/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-white w-full max-w-md rounded-[2.5rem] p-10 shadow-2xl relative">
        <h3 class="text-xl font-black text-gray-900 mb-2">Review Feedback</h3>
        <p class="text-xs text-gray-400 font-medium mb-6">Specify what changes are needed before this job can be published.</p>
        
        <form action="flag-job.php" method="POST">
            <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
            <textarea name="feedback" required placeholder="e.g. Please clarify the specific SDG 8 initiatives or adjust salary range transparency..." 
                      class="w-full h-40 p-5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-teal-500 outline-none transition font-medium text-sm mb-6"></textarea>
            
            <div class="flex gap-4">
                <button type="button" onclick="toggleModal()" class="flex-1 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-gray-600 transition">Cancel</button>
                <button type="submit" class="flex-1 py-4 bg-[#06201d] text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-teal-700 transition shadow-lg">Submit Review</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleModal() {
    const modal = document.getElementById('feedbackModal');
    modal.classList.toggle('hidden');
    // Prevent scrolling when modal is open
    document.body.style.overflow = modal.classList.contains('hidden') ? 'auto' : 'hidden';
}

// Close modal when clicking outside of the white box
window.onclick = function(event) {
    const modal = document.getElementById('feedbackModal');
    if (event.target == modal) {
        toggleModal();
    }
}
</script>

<?php include '../../includes/footer.php'; ?>