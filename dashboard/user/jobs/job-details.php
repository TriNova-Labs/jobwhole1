<?php
session_start();

/** * PATH CORRECTION: 
 * File is in /dashboard/user/jobs/
 * Go up 3 levels to reach the root /sdg/
 */
require_once "../../../includes/db.php";

// Get Job ID from URL
$job_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($job_id === 0) {
    header("Location: index.php");
    exit();
}

// 1. CHECK IF USER HAS ALREADY APPLIED & FETCH PROFILE DATA
$has_applied = false;
$userProfile = null;

if (isset($_SESSION['user_id'])) {
    try {
        // Check Application Status
        $check_stmt = $pdo->prepare("SELECT id FROM applications WHERE job_id = ? AND seeker_id = ?");
        $check_stmt->execute([$job_id, $_SESSION['user_id']]);
        $has_applied = $check_stmt->fetch() ? true : false;

        // Fetch professional profile for the modal (First Name, Last Name, and Resume Path)
        $userStmt = $pdo->prepare("SELECT first_name, last_name, resume_path FROM users WHERE id = ?");
        $userStmt->execute([$_SESSION['user_id']]);
        $userProfile = $userStmt->fetch();

    } catch (PDOException $e) {
        // Silently fail check
    }
}

try {
    // Join with companies and categories to get full job details
    $stmt = $pdo->prepare("
        SELECT j.*, c.name as company_name, c.website, cat.name as category_name 
        FROM jobs j
        JOIN companies c ON j.company_id = c.id
        JOIN categories cat ON j.category_id = cat.id
        WHERE j.id = ? AND j.is_active = 1
    ");
    $stmt->execute([$job_id]);
    $job = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$job) {
        die("Job listing not found or pending verification.");
    }

    $symbol = ($job['salary_currency'] === 'USD') ? '$' : '₱';

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// FIXED PATH: Go up 3 levels for header
include '../../../includes/header.php';
?>

<main class="bg-gray-50 min-h-screen pt-32 pb-20 text-left">
    <?php if (isset($_GET['status']) && $_GET['status'] === 'applied'): ?>
    <div id="success-toast" class="fixed top-32 right-6 md:right-10 z-[110] bg-[#06201d] text-white px-8 py-5 rounded-[2rem] shadow-2xl flex items-center gap-4 animate-in slide-in-from-right duration-500 border border-teal-500/30">
        <div class="bg-teal-500 w-10 h-10 rounded-full flex items-center justify-center shadow-lg shadow-teal-500/20">
            <i class="fa-solid fa-check text-white"></i>
        </div>
        <div>
            <p class="font-black uppercase text-[10px] tracking-[0.2em]">Application Sent!</p>
            <p class="text-xs text-teal-200/60 font-medium">HIRAYA has delivered your resume to the employer.</p>
        </div>
        <button onclick="this.parentElement.remove()" class="ml-4 text-gray-500 hover:text-white transition">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <script>setTimeout(() => { document.getElementById('success-toast')?.remove(); }, 5000);</script>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
    <div id="error-toast" class="fixed top-32 right-6 md:right-10 z-[110] bg-red-950 text-white px-8 py-5 rounded-[2rem] shadow-2xl flex items-center gap-4 animate-in slide-in-from-right duration-500 border border-red-500/30">
        <div class="bg-red-500 w-10 h-10 rounded-full flex items-center justify-center">
            <i class="fa-solid fa-exclamation text-white"></i>
        </div>
        <div>
            <p class="font-black uppercase text-[10px] tracking-[0.2em]">Error</p>
            <p class="text-xs text-red-200/60 font-medium">
                <?php 
                    if($_GET['error'] === 'already_applied') echo "You have already applied for this job.";
                    else if($_GET['error'] === 'no_vault_file') echo "No resume found in your vault.";
                    else echo "Something went wrong. Please try again.";
                ?>
            </p>
        </div>
    </div>
    <script>setTimeout(() => { document.getElementById('error-toast')?.remove(); }, 5000);</script>
    <?php endif; ?>

    <div class="max-w-5xl mx-auto px-6">
        <a href="index.php" class="text-gray-400 hover:text-teal-600 transition flex items-center gap-2 mb-8 text-[10px] font-black uppercase tracking-widest">
            <i class="fa-solid fa-arrow-left-long"></i> Back to Job Board
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                    <div class="flex gap-6">
                        <div class="w-16 h-16 bg-teal-50 rounded-2xl flex items-center justify-center text-teal-600 text-2xl shrink-0 border border-teal-100">
                            <i class="fa-solid <?php echo htmlspecialchars($job['icon'] ?? 'fa-briefcase'); ?>"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-teal-600"><?php echo htmlspecialchars($job['category_name']); ?></span>
                            <h1 class="text-3xl font-black text-[#06201d] mt-1 tracking-tight"><?php echo htmlspecialchars($job['title']); ?></h1>
                            <p class="text-gray-400 font-bold mt-1"><?php echo htmlspecialchars($job['company_name']); ?></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8 pt-8 border-t border-gray-50">
                        <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 rounded-2xl text-[10px] font-black text-[#06201d] uppercase tracking-tighter">
                            <i class="fa-solid fa-briefcase text-teal-600 text-lg"></i> <?php echo htmlspecialchars($job['job_type']); ?>
                        </div>
                        <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 rounded-2xl text-[10px] font-black text-[#06201d] uppercase tracking-tighter">
                            <i class="fa-solid fa-location-dot text-teal-600 text-lg"></i> <?php echo htmlspecialchars($job['location']); ?>
                        </div>
                        <div class="flex items-center gap-3 px-4 py-3 bg-teal-600 rounded-2xl text-[10px] font-black text-white uppercase tracking-tighter">
                            <i class="fa-solid fa-wallet text-white text-lg"></i> <?php echo $symbol . number_format($job['salary_min']); ?> - <?php echo number_format($job['salary_max']); ?>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6 block">About the Role</h3>
                    <div class="prose prose-teal max-w-none text-gray-600 leading-relaxed font-medium">
                        <?php echo nl2br(htmlspecialchars($job['description'])); ?>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-[#06201d] rounded-[2.5rem] p-8 text-white shadow-xl shadow-teal-900/20">
                    <h3 class="text-xl font-black mb-2 tracking-tight">
                        <?php echo $has_applied ? 'Application Sent' : 'Ready to Apply?'; ?>
                    </h3>
                    <p class="text-teal-200/50 text-[11px] font-bold uppercase tracking-widest mb-8 leading-relaxed">
                        <?php echo $has_applied ? 'Check your dashboard for updates.' : 'This employer is looking for talent like you.'; ?>
                    </p>
                    
                    <?php if ($has_applied): ?>
                        <div class="w-full py-5 bg-white/10 text-teal-400 font-black uppercase tracking-widest text-[11px] rounded-2xl flex items-center justify-center gap-3 border border-teal-500/20 mb-4 cursor-default">
                            <i class="fa-solid fa-circle-check"></i> Already Applied
                        </div>
                    <?php else: ?>
                        <button onclick="openApplyModal()" class="w-full py-5 bg-teal-500 text-white font-black uppercase tracking-widest text-[11px] rounded-2xl hover:bg-teal-400 transition-all shadow-lg mb-4 active:scale-95">
                            Apply Now
                        </button>
                    <?php endif; ?>

                    <button class="w-full py-5 bg-white/5 text-white font-black uppercase tracking-widest text-[10px] rounded-2xl hover:bg-white/10 transition-all">
                        <?php echo $has_applied ? 'Track Application' : 'Save for Later'; ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php if (!$has_applied): ?>
    <div id="applyModal" class="hidden fixed inset-0 z-[100] bg-[#06201d]/80 backdrop-blur-md flex items-center justify-center p-6">
        <div class="bg-white w-full max-w-lg rounded-[3rem] overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-300">
            <div class="bg-[#06201d] p-8 text-white flex justify-between items-start">
                <div>
                    <h3 class="font-black text-2xl tracking-tight">Submit Interest</h3>
                    <p class="text-teal-400 text-[10px] font-black uppercase tracking-[0.2em] mt-1">Applying to <?php echo htmlspecialchars($job['company_name']); ?></p>
                </div>
                <button onclick="closeApplyModal()" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-teal-400 hover:text-white transition">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="../../../action/submit-application.php" method="POST" enctype="multipart/form-data" class="p-10 space-y-6">
                <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
                
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-2">Applicant Name</label>
                    <input type="text" name="applicant_name" required 
                           value="<?php echo htmlspecialchars(($userProfile['first_name'] ?? '') . ' ' . ($userProfile['last_name'] ?? '')); ?>" 
                           class="w-full px-6 py-4 rounded-2xl border-none bg-gray-50 font-bold text-[#06201d]" readonly>
                </div>

                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-2">CV / Resume (PDF Only)</label>
                    
                    <?php if (!empty($userProfile['resume_path'])): ?>
                        <div class="p-4 rounded-2xl bg-teal-50 border border-teal-100 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-file-shield text-teal-600 text-lg"></i>
                                <div>
                                    <span class="block text-[10px] font-black text-teal-800 uppercase tracking-tight">Use Saved Resume</span>
                                    <span class="block text-[9px] text-teal-600/60 font-medium truncate max-w-[150px]"><?php echo htmlspecialchars($userProfile['resume_path']); ?></span>
                                </div>
                            </div>
                            <input type="checkbox" name="use_vault_resume" id="vaultCheck" value="1" checked class="w-5 h-5 accent-[#06201d]">
                        </div>
                        <p class="text-[9px] text-gray-400 font-bold px-2 uppercase tracking-widest text-center">--- Or upload new ---</p>
                    <?php endif; ?>

                    <div class="relative group">
                        <input type="file" id="resumeInput" name="resume" accept=".pdf" class="absolute inset-0 opacity-0 cursor-pointer z-10">
                        <div id="fileContainer" class="w-full px-6 py-5 rounded-2xl border-2 border-dashed border-gray-100 group-hover:border-teal-500/30 flex items-center justify-center gap-3 transition-all">
                            <i id="fileIcon" class="fa-solid fa-cloud-arrow-up text-teal-600"></i>
                            <span id="fileNameDisplay" class="text-[10px] font-black uppercase tracking-widest text-gray-400">Select PDF</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-2">Message (Optional)</label>
                    <textarea name="message" rows="3" placeholder="Why are you a great fit?" class="w-full px-6 py-4 rounded-2xl border-none bg-gray-50 outline-none font-medium text-gray-600"></textarea>
                </div>

                <button type="submit" class="w-full py-5 bg-[#06201d] text-white font-black uppercase tracking-[0.2em] text-xs rounded-2xl shadow-xl hover:bg-teal-700 transition-all">
                    Confirm Application
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</main>

<script>
function openApplyModal() {
    document.getElementById('applyModal')?.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeApplyModal() {
    document.getElementById('applyModal')?.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// File Upload Visual Feedback
const resumeInput = document.getElementById('resumeInput');
if(resumeInput) {
    resumeInput.addEventListener('change', function() {
        const file = this.files[0];
        const display = document.getElementById('fileNameDisplay');
        const icon = document.getElementById('fileIcon');
        const container = document.getElementById('fileContainer');

        if (file) {
            display.textContent = file.name;
            display.classList.remove('text-gray-400');
            display.classList.add('text-teal-600');
            icon.classList.remove('fa-cloud-arrow-up');
            icon.classList.add('fa-file-pdf');
            container.classList.add('bg-teal-50/50', 'border-teal-500/50');
            
            // Auto-uncheck vault if new file is selected
            const vaultCheck = document.getElementById('vaultCheck');
            if(vaultCheck) vaultCheck.checked = false;
        }
    });
}
</script>

<?php 
// FIXED PATH: Go up 3 levels for footer
include '../../../includes/footer.php'; 
?>