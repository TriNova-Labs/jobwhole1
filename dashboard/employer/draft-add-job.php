<?php
/**
 * HIRAYA | Post Job Logic
 * Focus: SDG 8 - Decent Work & Economic Growth
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once "../../includes/db.php"; 

// 1. AUTHENTICATION CHECK
if (!isset($_SESSION['user_id']) || $_SESSION['account_type'] !== 'employer') {
    header("Location: ../../auth/login.php");
    exit();
}

$employer_id = $_SESSION['user_id'];
$first_name = $_SESSION['first_name'] ?? "Employer";

// 2. FETCH NECESSARY DATA
try {
    // Get Company ID
    $stmt = $pdo->prepare("SELECT id FROM companies WHERE employer_id = ? LIMIT 1");
    $stmt->execute([$employer_id]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$company) {
        die("Please complete your Company Profile before posting a job.");
    }
    $company_id = $company['id'];

    // Get Categories for dropdown
    $categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database Connection Error: " . $e->getMessage());
}

// 3. FORM SUBMISSION HANDLING
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Convert Tags array to Comma-Separated String to fix Constraint Violation
    $tags_array = $_POST['tags'] ?? []; 
    $tags_string = implode(', ', $tags_array); 

    try {
        $sql = "INSERT INTO jobs (
            employer_id, company_id, category_id, icon, title, 
            description, location, work_type, job_type, 
            salary_min, salary_max, tags, is_active, posted_date
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, NOW())";

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
            $_POST['salary_min'],
            $_POST['salary_max'],
            $tags_string
        ]);

        header("Location: add-job.php?status=submitted");
        exit();

    } catch (PDOException $e) {
        $error_info = "SQL Error: " . $e->getMessage();
    }
}

// Include your header
include '../../includes/employer/header.php'; 
?>

<style>
    /* Prevent fields from being hidden by external CSS */
    .form-input-fix { 
        color: #1a202c !important; 
        background-color: #ffffff !important;
        display: block !important;
        width: 100% !important;
    }
    textarea::placeholder, input::placeholder { color: #a0aec0 !important; }
</style>

<main class="bg-gray-50 min-h-screen pt-32 pb-20 text-left">
    <div class="max-w-4xl mx-auto px-6">
        <a href="index.php" class="text-gray-400 hover:text-teal-600 transition flex items-center gap-2 mb-6 text-sm font-bold">
            <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
        </a>

        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-[#06201d] p-8 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold">Post a New Opportunity</h1>
                        <p class="text-teal-200/70 text-sm mt-1">HIRAYA Verification Queue</p>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] uppercase tracking-widest text-teal-500 font-bold">Posting for</span>
                        <p class="font-bold text-sm">Company #<?php echo $company_id; ?></p>
                    </div>
                </div>
            </div>

            <form action="add-job.php" method="POST" class="p-8 space-y-8">
                <?php if (isset($_GET['status']) && $_GET['status'] === 'submitted'): ?>
                    <div class="bg-teal-50 border border-teal-200 text-teal-700 px-6 py-4 rounded-2xl flex items-center gap-4">
                        <i class="fa-solid fa-circle-check text-xl"></i>
                        <p class="font-medium">Job submitted! Our team will verify it shortly.</p>
                    </div>
                <?php endif; ?>

                <?php if (isset($error_info)): ?>
                    <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl">
                        <p class="font-bold text-sm uppercase">Submission Error</p>
                        <p class="text-xs mt-1 opacity-80"><?php echo htmlspecialchars($error_info); ?></p>
                    </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase text-gray-500 tracking-wider">Job Title</label>
                        <input type="text" name="title" required placeholder="e.g. Senior Full-Stack Developer" class="form-input-fix px-5 py-3.5 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-teal-500 outline-none transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase text-gray-500 tracking-wider">Category</label>
                        <select name="category_id" required class="form-input-fix px-5 py-3.5 rounded-2xl border border-gray-200 bg-white cursor-pointer">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="space-y-4">
                    <label class="text-xs font-bold uppercase text-gray-500 tracking-wider">Listing Icon</label>
                    <div class="grid grid-cols-5 md:grid-cols-10 gap-3">
                        <?php 
                        $icons = ['fa-code', 'fa-briefcase', 'fa-pen-nib', 'fa-microchip', 'fa-user-nurse', 'fa-chart-line', 'fa-headset', 'fa-truck', 'fa-graduation-cap', 'fa-laptop-code'];
                        foreach ($icons as $index => $i_class): ?>
                            <label class="cursor-pointer group">
                                <input type="radio" name="icon" value="<?php echo $i_class; ?>" class="peer hidden" <?php echo $index === 0 ? 'checked' : ''; ?>>
                                <div class="w-full h-12 rounded-xl border-2 border-gray-100 flex items-center justify-center text-gray-400 peer-checked:border-teal-500 peer-checked:bg-teal-50 peer-checked:text-teal-600 group-hover:border-teal-200 transition-all">
                                    <i class="fa-solid <?php echo $i_class; ?>"></i>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase text-gray-500">Work Type</label>
                        <select name="work_type" class="form-input-fix px-5 py-3.5 rounded-2xl border border-gray-200">
                            <option>On-site</option>
                            <option>Remote</option>
                            <option>Hybrid</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase text-gray-500">Job Type</label>
                        <select name="job_type" class="form-input-fix px-5 py-3.5 rounded-2xl border border-gray-200">
                            <option>Full-time</option>
                            <option>Part-time</option>
                            <option>Contract</option>
                            <option>Internship</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase text-gray-500">Location</label>
                        <input type="text" name="location" required placeholder="City, Country" class="form-input-fix px-5 py-3.5 rounded-2xl border border-gray-200">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase text-gray-500">Min Monthly Salary (₱)</label>
                        <input type="number" name="salary_min" required placeholder="30000" class="form-input-fix px-5 py-3.5 rounded-2xl border border-gray-200">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase text-gray-500">Max Monthly Salary (₱)</label>
                        <input type="number" name="salary_max" required placeholder="50000" class="form-input-fix px-5 py-3.5 rounded-2xl border border-gray-200">
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="text-xs font-bold uppercase text-gray-500 tracking-wider">Inclusivity Tags (SDG 8)</label>
                    <div class="flex flex-wrap gap-3">
                        <?php 
                        $tags_list = ['PWD-Friendly', 'Entry-Level', 'SDG-Aligned', 'Immediate Hire', 'Gender-Neutral'];
                        foreach ($tags_list as $t): ?>
                            <label class="cursor-pointer group">
                                <input type="checkbox" name="tags[]" value="<?php echo $t; ?>" class="peer hidden">
                                <div class="px-4 py-2 rounded-xl border border-gray-200 text-xs font-bold text-gray-500 peer-checked:bg-teal-600 peer-checked:text-white peer-checked:border-teal-600 group-hover:bg-gray-50 transition-all"><?php echo $t; ?></div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <label class="text-xs font-bold uppercase text-gray-500">Job Description</label>
                        <span id="char-count" class="text-[10px] font-bold text-gray-400">0 / 500</span>
                    </div>
                    <textarea name="description" id="job-desc" rows="5" required maxlength="500" oninput="updateCount()" placeholder="Describe the role and why it's a great fit for ethical seekers..." class="form-input-fix px-5 py-4 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-teal-500 outline-none transition-all"></textarea>
                </div>

                <div class="flex flex-col md:flex-row gap-4 pt-4">
                    <button type="submit" class="w-full py-4 bg-teal-600 text-white font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-teal-900/20 hover:bg-teal-700 hover:-translate-y-0.5 transition-all">
                        Post for Approval
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
function updateCount() {
    const textarea = document.getElementById('job-desc');
    document.getElementById('char-count').innerText = `${textarea.value.length} / 500`;
}
</script>

<?php include '../../includes/footer.php'; ?>