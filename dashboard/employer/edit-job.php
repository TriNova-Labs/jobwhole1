<?php
session_start();
require_once "../../includes/db.php";

// Auth Check
if (!isset($_SESSION['user_id']) || $_SESSION['account_type'] !== 'employer') {
    header("Location: ../../auth/employer/login.php");
    exit();
}

$job_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$employer_id = $_SESSION['user_id'];

// Handle the Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $location = $_POST['location'];
    $category_id = (int)$_POST['category_id']; 
    $icon = $_POST['icon'] ?? 'fa-briefcase';
    $description = $_POST['description'];
    $requirements = $_POST['requirements']; // Now mapped to DB
    $benefits = $_POST['benefits'];         // Now mapped to DB
    $salary_min = !empty($_POST['salary_min']) ? $_POST['salary_min'] : NULL;
    $salary_max = !empty($_POST['salary_max']) ? $_POST['salary_max'] : NULL;
    $salary_currency = $_POST['salary_currency'] ?? 'PHP'; // New currency field
    $work_type = $_POST['work_type'];
    $job_type = $_POST['job_type'];
    
    // Convert inclusivity tags array to a string for DB storage
    $tags = isset($_POST['tags']) ? implode(',', $_POST['tags']) : '';

    try {
        $sql = "UPDATE jobs SET 
                title = ?, location = ?, category_id = ?, icon = ?,
                description = ?, requirements = ?, benefits = ?,
                salary_min = ?, salary_max = ?, salary_currency = ?,
                work_type = ?, job_type = ?, tags = ?, 
                admin_feedback = NULL, is_active = 0 
                WHERE id = ? AND employer_id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $title, $location, $category_id, $icon,
            $description, $requirements, $benefits,
            $salary_min, $salary_max, $salary_currency,
            $work_type, $job_type, $tags, $job_id, $employer_id
        ]);

        header("Location: index.php?update=success");
        exit();
    } catch (PDOException $e) {
        $error = "Update failed: " . $e->getMessage();
    }
}

// Fetch existing data
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ? AND employer_id = ?");
$stmt->execute([$job_id, $employer_id]);
$job = $stmt->fetch();

if (!$job) { die("Listing not found."); }

// Process current tags for UI display
$current_tags = explode(',', $job['tags'] ?? '');

// Fetch Categories for dropdown
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>

<?php include '../../includes/employer/header.php'; ?>

<main class="pt-28 px-8 md:px-24 bg-gray-50 min-h-screen pb-20 text-left">
    <div class="max-w-6xl mx-auto">
        <header class="mb-12">
            <a href="index.php" class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 hover:text-teal-600 transition flex items-center gap-2 mb-4">
                <i class="fa-solid fa-arrow-left-long"></i> Back to Dashboard
            </a>
            <h1 class="text-4xl font-black text-[#06201d] tracking-tight">Refine Listing</h1>
            <p class="text-gray-500 font-medium mt-1">Update your opportunity to align with SDG 8.3 standards.</p>
        </header>

        <form action="" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                    <label class="text-[10px] font-black text-teal-600 uppercase tracking-[0.2em] mb-6 block">Select Listing Icon</label>
                    <div class="flex flex-wrap gap-4">
                        <?php 
                        $icons = ['fa-code', 'fa-briefcase', 'fa-pen-nib', 'fa-microchip', 'fa-user-doctor', 'fa-chart-line', 'fa-headset', 'fa-truck', 'fa-graduation-cap', 'fa-building'];
                        foreach($icons as $i): 
                        ?>
                        <label class="cursor-pointer">
                            <input type="radio" name="icon" value="<?php echo $i; ?>" class="hidden peer" <?php echo ($job['icon'] == $i) ? 'checked' : ''; ?>>
                            <div class="w-12 h-12 flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 border border-transparent peer-checked:bg-teal-50 peer-checked:text-teal-600 peer-checked:border-teal-200 transition-all hover:bg-gray-100">
                                <i class="fa-solid <?php echo $i; ?>"></i>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="md:col-span-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 block ml-2">Job Title</label>
                        <input type="text" name="title" required value="<?php echo htmlspecialchars($job['title']); ?>" 
                               class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-teal-500/20 font-bold text-[#06201d]">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 block ml-2">Category</label>
                        <select name="category_id" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl font-bold text-[#06201d] appearance-none cursor-pointer">
                            <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($job['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 block ml-2">Job Description</label>
                        <textarea name="description" rows="5" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl font-medium text-gray-700 leading-relaxed"><?php echo htmlspecialchars($job['description']); ?></textarea>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 block">Requirements</label>
                        <textarea name="requirements" rows="4" placeholder="List key qualifications..." class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl font-medium text-gray-600 leading-relaxed"><?php echo htmlspecialchars($job['requirements']); ?></textarea>
                    </div>
                    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 block">Benefits</label>
                        <textarea name="benefits" rows="4" placeholder="Health insurance, remote options, etc." class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl font-medium text-gray-600 leading-relaxed"><?php echo htmlspecialchars($job['benefits']); ?></textarea>
                    </div>
                </div>
            </div>

            <div class="space-y-8">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 space-y-6">
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 block ml-2">Location</label>
                        <input type="text" name="location" value="<?php echo htmlspecialchars($job['location']); ?>" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl font-bold text-[#06201d]">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 block ml-2">Work Type</label>
                            <select name="work_type" class="w-full px-4 py-4 bg-gray-50 border-none rounded-2xl font-bold text-[11px] cursor-pointer appearance-none">
                                <option value="On-site" <?php echo ($job['work_type'] == 'On-site') ? 'selected' : ''; ?>>On-site</option>
                                <option value="Remote" <?php echo ($job['work_type'] == 'Remote') ? 'selected' : ''; ?>>Remote</option>
                                <option value="Hybrid" <?php echo ($job['work_type'] == 'Hybrid') ? 'selected' : ''; ?>>Hybrid</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 block ml-2">Emp. Type</label>
                            <select name="job_type" class="w-full px-4 py-4 bg-gray-50 border-none rounded-2xl font-bold text-[11px] cursor-pointer appearance-none">
                                <?php 
                                $types = ['Full-time', 'Part-time', 'Contract', 'Internship', 'Freelance'];
                                foreach($types as $t): ?>
                                    <option value="<?php echo $t; ?>" <?php echo ($job['job_type'] == $t) ? 'selected' : ''; ?>><?php echo $t; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 block ml-2">Salary Range (Monthly)</label>
                        <div class="flex items-center gap-2">
                            <div class="flex items-center bg-gray-50 rounded-2xl px-3 w-full border-none">
                                <select name="salary_currency" class="px-2 py-2 rounded-lg border-none bg-transparent focus:ring-0 text-[10px] font-black text-teal-600">
                                    <option value="PHP" <?php echo ($job['salary_currency'] == 'PHP') ? 'selected' : ''; ?>>₱</option>
                                    <option value="USD" <?php echo ($job['salary_currency'] == 'USD') ? 'selected' : ''; ?>>$</option>
                                </select>
                                <input type="number" name="salary_min" placeholder="Min" value="<?php echo $job['salary_min']; ?>" class="w-full bg-transparent border-none focus:ring-0 py-4 font-bold text-sm text-[#06201d]">
                            </div>
                            <input type="number" name="salary_max" placeholder="Max" value="<?php echo $job['salary_max']; ?>" class="w-1/2 px-4 py-4 bg-gray-50 border-none rounded-2xl font-bold text-sm text-[#06201d]">
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 block ml-2">Inclusivity Tags</label>
                        <div class="flex flex-wrap gap-2">
                            <?php 
                            $available_tags = ['PWD-Friendly', 'Senior-Level', 'Gender-Neutral', 'SDG 8.3'];
                            foreach($available_tags as $t): 
                                $is_active = in_array($t, $current_tags);
                            ?>
                            <label class="cursor-pointer">
                                <input type="checkbox" name="tags[]" value="<?php echo $t; ?>" class="hidden peer" <?php echo $is_active ? 'checked' : ''; ?>>
                                <div class="px-4 py-2 rounded-xl bg-gray-50 text-[9px] font-black text-gray-400 uppercase tracking-widest border border-transparent peer-checked:bg-teal-600 peer-checked:text-white transition-all hover:bg-gray-100">
                                    <?php echo $t; ?>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-[#06201d] hover:bg-teal-900 text-white py-6 rounded-[2rem] font-black uppercase tracking-[0.2em] text-xs transition-all shadow-xl shadow-teal-900/10 active:scale-[0.98]">
                    Update & Resubmit
                </button>
            </div>
        </form>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>