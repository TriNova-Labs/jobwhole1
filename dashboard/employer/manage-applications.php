<?php
session_start();
require_once "../../includes/db.php";

// 1. Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['account_type'] !== 'employer') {
    header("Location: /sdg/auth/employer/login.php"); 
    exit();
}

$employer_id = $_SESSION['user_id'];

// NEW: Get filters from URL
$job_filter = isset($_GET['job_id']) ? $_GET['job_id'] : null;
$status_filter = isset($_GET['status']) ? $_GET['status'] : null;

try {
    /**
     * 2. The Query
     * Updated to support status filtering (e.g., viewing only 'accepted' candidates)
     */
    $query = "
        SELECT 
            a.id as app_id, a.status, a.resume_path, a.message,
            u.first_name, u.last_name, u.email,
            j.title as job_title, j.id as job_id
        FROM applications a
        JOIN jobs j ON a.job_id = j.id
        JOIN users u ON a.seeker_id = u.id
        JOIN employers e ON j.employer_id = e.id
        WHERE e.id = ?
    ";

    $params = [$employer_id];

    if ($job_filter) {
        $query .= " AND j.id = ?";
        $params[] = $job_filter;
    }

    if ($status_filter) {
        $query .= " AND a.status = ?";
        $params[] = $status_filter;
    }

    $query .= " ORDER BY a.id DESC"; // Newest applicants first

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'message') !== false) {
        $pdo->exec("ALTER TABLE applications ADD COLUMN message TEXT AFTER resume_path");
        header("Refresh:0");
        exit();
    }
    die("Database Error: " . $e->getMessage());
}

include '../../includes/employer/header.php';
?>

<main class="bg-gray-50 min-h-screen pt-32 pb-20 text-left">
    <div class="max-w-6xl mx-auto px-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
            <div>
                <a href="index.php" class="text-[10px] font-black uppercase tracking-[0.2em] text-teal-600 flex items-center gap-2 mb-2 hover:gap-3 transition-all">
                    <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
                </a>
                <h1 class="text-4xl font-black text-[#06201d] tracking-tight">
                    <?php echo ($status_filter === 'accepted') ? 'Your Team' : 'Review Applicants'; ?>
                </h1>
                <p class="text-gray-400 text-[10px] font-black uppercase tracking-[0.2em] mt-2">
                    <?php 
                        if ($job_filter && !empty($applications)) {
                            echo "Candidates for: " . htmlspecialchars($applications[0]['job_title']);
                        } elseif ($status_filter === 'accepted') {
                            echo "All Hired Personnel";
                        } else {
                            echo "All Active Applications";
                        }
                    ?>
                </p>
            </div>
            
            <div class="bg-white px-6 py-3 rounded-2xl border border-gray-100 shadow-sm">
                <span class="text-xs font-bold text-gray-500">Result Count: </span>
                <span class="text-lg font-black text-teal-600"><?php echo count($applications); ?></span>
            </div>
        </div>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'updated'): ?>
            <div class="mb-6 bg-teal-50 border border-teal-200 text-teal-700 px-6 py-4 rounded-2xl flex items-center gap-3 animate-pulse">
                <i class="fa-solid fa-circle-check"></i>
                <span class="text-xs font-black uppercase tracking-widest">Candidate status updated and notification sent.</span>
            </div>
        <?php endif; ?>

        <div class="grid gap-6">
            <?php if (empty($applications)): ?>
                <div class="bg-white rounded-[3rem] p-20 text-center border border-gray-100 shadow-sm">
                    <i class="fa-solid fa-user-astronaut text-6xl text-gray-100 mb-6 block"></i>
                    <p class="font-black text-[#06201d] uppercase tracking-widest text-sm">No candidates found</p>
                    <p class="text-gray-400 text-xs mt-2 font-medium">Try clearing your filters or checking other listings.</p>
                </div>
            <?php else: ?>
                <?php foreach ($applications as $app): 
                    $is_hired = ($app['status'] === 'accepted');
                ?>
                    <div class="bg-white rounded-[2.5rem] p-8 border <?php echo $is_hired ? 'border-teal-500/30 bg-teal-50/10' : 'border-gray-100'; ?> shadow-sm flex flex-col md:flex-row justify-between items-center gap-8 group hover:border-teal-200 hover:shadow-xl hover:shadow-teal-900/5 transition-all">
                        
                        <div class="flex items-center gap-5 w-full md:w-auto">
                            <div class="relative">
                                <div class="w-16 h-16 <?php echo $is_hired ? 'bg-teal-600' : 'bg-[#06201d]'; ?> text-teal-400 rounded-2xl flex items-center justify-center font-black text-xl shadow-lg transition-colors">
                                    <?php echo strtoupper($app['first_name'][0] . $app['last_name'][0]); ?>
                                </div>
                                <?php if ($is_hired): ?>
                                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-teal-400 text-[#06201d] rounded-full flex items-center justify-center border-2 border-white text-[10px] shadow-sm">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <h2 class="font-black text-[#06201d] text-xl leading-tight">
                                    <?php echo htmlspecialchars($app['first_name'] . " " . $app['last_name']); ?>
                                </h2>
                                <p class="text-xs text-teal-600 font-bold mt-1"><?php echo htmlspecialchars($app['email']); ?></p>
                                <div class="mt-2 flex gap-2">
                                    <?php 
                                        $statusClass = 'bg-gray-50 text-gray-400';
                                        if ($app['status'] == 'accepted') $statusClass = 'bg-teal-600 text-white border-teal-600';
                                        if ($app['status'] == 'rejected') $statusClass = 'bg-red-50 text-red-500 border-red-100';
                                    ?>
                                    <span class="text-[9px] font-black uppercase tracking-widest px-3 py-1 rounded-full border <?php echo $statusClass; ?>">
                                        <?php echo $is_hired ? 'Team Member' : 'Status: ' . ucfirst($app['status']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex-1 px-8 md:border-l border-gray-100 w-full">
                            <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest mb-2">Cover Message</p>
                            <p class="text-sm text-gray-600 italic leading-relaxed line-clamp-2">
                                "<?php echo htmlspecialchars($app['message'] ?? 'No additional message provided.'); ?>"
                            </p>
                            
                            <div class="mt-4 flex gap-4">
                                <a href="../../uploads/documents/<?php echo $app['resume_path']; ?>" target="_blank" class="inline-flex items-center gap-2 text-teal-600 font-black text-[10px] uppercase tracking-widest hover:text-[#06201d] transition-colors">
                                    <i class="fa-solid fa-file-pdf text-base"></i> View Resume
                                </a>
                            </div>
                        </div>

                        <div class="flex gap-3 w-full md:w-auto border-t md:border-t-0 pt-6 md:pt-0">
                            <form action="process-application.php" method="POST" class="flex gap-3 w-full">
                                <input type="hidden" name="app_id" value="<?php echo $app['app_id']; ?>">
                                <input type="hidden" name="job_id" value="<?php echo $app['job_id']; ?>">
                                
                                <button name="status" value="accepted" 
                                    class="flex-1 md:flex-none px-8 py-4 bg-teal-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-[#06201d] transition-all shadow-lg shadow-teal-900/10 <?php echo $is_hired ? 'opacity-30 cursor-not-allowed' : ''; ?>"
                                    <?php echo $is_hired ? 'disabled' : ''; ?>>
                                    <?php echo $is_hired ? 'Hired' : 'Hire'; ?>
                                </button>
                                
                                <button name="status" value="rejected" 
                                    class="flex-1 md:flex-none px-8 py-4 bg-white border border-red-100 text-red-500 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-red-50 transition-all <?php echo ($app['status'] === 'rejected') ? 'opacity-30 cursor-not-allowed' : ''; ?>"
                                    <?php echo ($app['status'] === 'rejected') ? 'disabled' : ''; ?>>
                                    Reject
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>