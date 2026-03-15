<?php
session_start();
require_once "../../includes/db.php"; 

// 1. Authentication and Company Context
if (!isset($_SESSION['user_id']) || $_SESSION['account_type'] !== 'employer') {
    header("Location: ../../auth/login.php");
    exit();
}

$employer_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT id FROM companies WHERE employer_id = ? LIMIT 1");
    $stmt->execute([$employer_id]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$company) { 
        die("Complete company profile first."); 
    }
    $company_id = $company['id'];

    $categories = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { 
    die("DB Error: " . $e->getMessage()); 
}

// 2. Final Database Insertion Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_post'])) {
    try {
        // Standardize tags to match edit-job logic
        $tags_array = isset($_POST['tags']) ? $_POST['tags'] : [];
        $tags_string = !empty($tags_array) ? implode(',', $tags_array) : '';

        // Capture raw numeric values and currency separately (better for filtering later)
        $salary_min = !empty($_POST['salary_min']) ? $_POST['salary_min'] : NULL;
        $salary_max = !empty($_POST['salary_max']) ? $_POST['salary_max'] : NULL;
        $salary_currency = $_POST['salary_currency'] ?? 'PHP';

        $sql = "INSERT INTO jobs (
        employer_id, company_id, category_id, icon, title, 
        description, location, work_type, job_type, 
        salary_min, salary_max, salary_currency, tags, requirements, benefits, 
        is_active, posted_date
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, NOW())";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $employer_id, 
            $company_id, 
            $_POST['category_id'], 
            $_POST['icon'], 
            $_POST['title'], 
            $_POST['description'], 
            $_POST['location'], 
            $_POST['work_type'], 
            $_POST['job_type'], 
            $salary_min, 
            $salary_max,
            $salary_currency, 
            $tags_string, 
            $_POST['requirements'], 
            $_POST['benefits']
        ]);

        header("Location: index.php?status=proposed");
        exit();
    } catch (PDOException $e) { 
        $db_error = $e->getMessage(); 
        error_log("Job Post Error: " . $db_error);
    }
}

include '../../includes/employer/header.php'; 
?>

<main id="mainContent" class="bg-[#f8fafc] min-h-screen pt-28 pb-20 transition-all duration-500">
    <div class="max-w-5xl mx-auto px-6 text-left">
        
        <div class="mb-10">
            <h1 class="text-3xl font-black text-[#06201d] tracking-tight">Post a New Opportunity</h1>
            <p class="text-gray-500 mt-2 font-medium">Connect with ethical talent and grow your team inclusively.</p>
        </div>

        <form id="jobForm" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100">
                    <label class="text-[10px] font-black uppercase tracking-widest text-teal-600 block mb-4">Select Listing Icon</label>
                    <div class="grid grid-cols-5 sm:grid-cols-10 gap-3 mb-8">
                        <?php 
                        $icons = ['fa-code', 'fa-briefcase', 'fa-pen-nib', 'fa-microchip', 'fa-user-nurse', 'fa-chart-line', 'fa-headset', 'fa-truck', 'fa-graduation-cap', 'fa-laptop-code'];
                        foreach ($icons as $idx => $icon): ?>
                            <label class="cursor-pointer">
                                <input type="radio" name="icon" value="<?= $icon ?>" class="peer hidden" <?= $idx === 1 ? 'checked' : '' ?>>
                                <div class="w-full aspect-square rounded-xl border-2 border-gray-50 flex items-center justify-center text-gray-300 peer-checked:border-teal-500 peer-checked:bg-teal-50 peer-checked:text-teal-600 transition-all duration-300">
                                    <i class="fa-solid <?= $icon ?> text-lg"></i>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Job Title</label>
                            <input type="text" name="title" id="in_title" required placeholder="e.g. UI/UX Designer" class="w-full px-5 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-teal-500 transition-all font-bold">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Category</label>
                            <select name="category_id" id="in_category" class="w-full px-5 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-teal-500 font-bold">
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Job Description</label>
                        <textarea name="description" id="in_desc" rows="4" required class="w-full px-5 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-teal-500 font-medium"></textarea>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Requirements</label>
                        <textarea name="requirements" id="in_req" rows="4" placeholder="List key qualifications..." class="w-full px-5 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-teal-500 font-medium"></textarea>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Benefits</label>
                        <textarea name="benefits" id="in_ben" rows="4" placeholder="Health insurance, remote options, etc." class="w-full px-5 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-teal-500 font-medium"></textarea>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Location</label>
                        <input type="text" name="location" id="in_loc" required placeholder="City, Country" class="w-full px-5 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-teal-500 font-bold">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Work Type</label>
                            <select name="work_type" id="in_work" class="w-full px-4 py-3 rounded-xl bg-gray-50 border-none text-sm font-bold appearance-none">
                                <option>On-site</option><option>Remote</option><option>Hybrid</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Emp. Type</label>
                            <select name="job_type" id="in_jobtype" class="w-full px-4 py-3 rounded-xl bg-gray-50 border-none text-sm font-bold appearance-none">
                                <option>Full-time</option><option>Part-time</option><option>Contract</option><option>Internship</option><option>Freelance</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Salary Range (Monthly)</label>
                        <div class="flex items-center gap-2">
                            <div class="flex items-center bg-gray-50 rounded-xl px-3 w-full">
                                <select name="salary_currency" id="in_curr" class="bg-transparent border-none text-[10px] font-black text-teal-600 focus:ring-0 p-0 cursor-pointer">
                                    <option value="PHP">₱ PHP</option>
                                    <option value="USD">$ USD</option>
                                </select>
                                <input type="number" name="salary_min" id="in_min" placeholder="Min" class="w-full bg-transparent border-none focus:ring-0 py-3 font-bold text-sm">
                            </div>
                            <input type="number" name="salary_max" id="in_max" placeholder="Max" class="w-1/2 px-4 py-3 rounded-xl bg-gray-50 border-none font-bold text-sm">
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 block mb-4">Inclusivity Tags</label>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach(['PWD-Friendly', 'Entry-Level', 'Immediate', 'SDG 8.3'] as $tag): ?>
                            <label class="cursor-pointer">
                                <input type="checkbox" name="tags[]" value="<?= $tag ?>" class="peer hidden">
                                <div class="px-4 py-2 rounded-xl border border-gray-50 bg-gray-50 text-[10px] font-bold text-gray-400 peer-checked:bg-teal-600 peer-checked:text-white transition-all uppercase tracking-tighter">#<?= $tag ?></div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button type="button" onclick="showReview()" class="w-full py-5 bg-[#06201d] text-white rounded-[1.5rem] font-black uppercase tracking-[0.2em] text-xs shadow-xl shadow-teal-900/20 hover:scale-[1.02] active:scale-95 transition-all">
                    Review Listing
                </button>
            </div>
        </form>
    </div>
</main>

<div id="reviewModal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-[#06201d]/60 backdrop-blur-md"></div>
    <div class="absolute inset-0 flex items-center justify-center p-6">
        <div class="bg-white w-full max-w-2xl rounded-[3rem] shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-300">
            <div class="p-12">
                <div class="flex justify-between items-start mb-10">
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-[0.3em] text-teal-600 mb-2 block">Confirm Listing Details</span>
                        <h2 id="rev_title" class="text-3xl font-black text-[#06201d] tracking-tight leading-none"></h2>
                    </div>
                    <button type="button" onclick="closeReview()" class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-10 mb-10">
                    <div>
                        <label class="text-[9px] font-black uppercase tracking-widest text-gray-300 block mb-2">Compensation</label>
                        <p id="rev_salary" class="text-lg font-bold text-gray-900"></p>
                    </div>
                    <div>
                        <label class="text-[9px] font-black uppercase tracking-widest text-gray-300 block mb-2">Location & Type</label>
                        <p id="rev_meta" class="text-lg font-bold text-gray-900"></p>
                    </div>
                </div>
                <div class="mb-10">
                    <label class="text-[9px] font-black uppercase tracking-widest text-gray-300 block mb-2">Description Preview</label>
                    <p id="rev_desc" class="text-sm text-gray-500 leading-relaxed italic line-clamp-3"></p>
                </div>
                <button type="submit" form="jobForm" name="confirm_post" onclick="startSubmitAnimation()" class="w-full py-5 bg-teal-600 text-white rounded-2xl font-black uppercase tracking-widest text-[10px] shadow-lg hover:bg-[#06201d] transition-all flex items-center justify-center gap-3">
                    <span id="submitText">Finalize & Submit</span>
                    <i id="submitIcon" class="fa-solid fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<div id="successOverlay" class="fixed inset-0 z-[200] hidden flex items-center justify-center bg-white">
    <div class="text-center">
        <div class="w-24 h-24 bg-teal-50 text-teal-500 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fa-solid fa-check text-4xl animate-bounce"></i>
        </div>
        <h2 class="text-3xl font-black text-[#06201d] tracking-tighter">Proposal Sent!</h2>
        <p class="text-gray-400 mt-2 font-medium">Wait a moment while we redirect you...</p>
    </div>
</div>

<script>
function showReview() {
    document.getElementById('rev_title').innerText = document.getElementById('in_title').value || "Untitled Position";
    const curr = document.getElementById('in_curr').value === 'PHP' ? '₱' : '$';
    const min = document.getElementById('in_min').value;
    const max = document.getElementById('in_max').value;
    document.getElementById('rev_salary').innerText = min ? `${curr}${Number(min).toLocaleString()} - ${Number(max).toLocaleString()}` : "Not specified";
    const loc = document.getElementById('in_loc').value || "Remote";
    const type = document.getElementById('in_jobtype').value;
    document.getElementById('rev_meta').innerText = `${loc} • ${type}`;
    document.getElementById('rev_desc').innerText = document.getElementById('in_desc').value || "No description provided.";
    document.getElementById('reviewModal').classList.remove('hidden');
}

function closeReview() {
    document.getElementById('reviewModal').classList.add('hidden');
}

function startSubmitAnimation() {
    document.getElementById('reviewModal').classList.add('hidden');
    document.getElementById('mainContent').classList.add('opacity-0', 'scale-95');
    document.getElementById('successOverlay').classList.remove('hidden');
}
</script>

<?php if(isset($db_error)): ?>
    <script>alert("Database Error: <?php echo addslashes($db_error); ?>");</script>
<?php endif; ?>

<?php include '../../includes/footer.php'; ?>