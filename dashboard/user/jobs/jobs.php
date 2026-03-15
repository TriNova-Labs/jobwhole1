<?php
// 1. Database Connection
include '../../../includes/db.php'; 

// 2. Include Header
include '../../../includes/header.php';

// 3. Fetch Jobs from Database (Including salary_currency and icon)
try {
    $query = "SELECT j.*, c.name AS company_name, c.logo AS company_logo, c.is_verified 
              FROM jobs j
              JOIN companies c ON j.company_id = c.id
              WHERE j.is_active = 1 
              AND c.is_verified = 1 
              ORDER BY j.posted_date DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}
?>

<main class="bg-gray-50 min-h-screen pt-32 pb-20">
    <div class="max-w-6xl mx-auto px-6">
        <header class="mb-12">
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Find Your Next Role</h1>
            <p class="text-gray-500 mt-2 font-medium">Discover inclusive opportunities from verified employers.</p>
        </header>

        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-wrap gap-4 mb-10">
            <input type="text" placeholder="Search by title, company, or keywords" class="flex-1 min-w-[250px] px-4 py-2 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition font-medium">
            <select class="px-4 py-2 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-teal-500/20 text-gray-600 font-bold">
                <option>Location</option>
                <option>Remote</option>
                <option>Manila</option>
            </select>
            <button class="bg-teal-600 text-white px-8 py-2 rounded-xl font-black uppercase text-xs tracking-widest hover:bg-teal-700 transition shadow-lg shadow-teal-900/20 active:scale-95 transition-transform">
                Find Jobs
            </button>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <?php if (count($jobs) > 0): ?>
                <?php foreach ($jobs as $job): 
                    // Dynamic Currency Logic
                    $symbol = ($job['salary_currency'] === 'USD') ? '$' : '₱';
                ?>
                    <div class="bg-white p-6 rounded-3xl border border-gray-100 hover:border-teal-500/30 hover:shadow-xl hover:shadow-teal-900/5 hover:-translate-y-1 transition-all duration-300 group flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                        
                        <div class="flex gap-6 items-center">
                            <div class="w-16 h-16 rounded-2xl bg-teal-50 flex items-center justify-center text-teal-600 text-2xl border border-teal-50 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                                <i class="fa-solid <?php echo htmlspecialchars($job['icon'] ?? 'fa-briefcase'); ?>"></i>
                            </div>
                            
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-xl font-black text-[#06201d] group-hover:text-teal-600 transition-colors tracking-tight">
                                        <?php echo htmlspecialchars($job['title']); ?>
                                    </h3>
                                    <?php if($job['is_verified']): ?>
                                        <i class="fa-solid fa-circle-check text-blue-500 text-[10px]" title="Verified Employer"></i>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="flex flex-wrap items-center gap-y-2 gap-x-4 mt-1 text-sm text-gray-400 font-bold">
                                    <span class="text-teal-600"><?php echo htmlspecialchars($job['company_name']); ?></span>
                                    <span><i class="fa-solid fa-location-dot mr-1 text-gray-300"></i> <?php echo htmlspecialchars($job['location']); ?></span>
                                    <span><i class="fa-solid <?php echo ($job['work_type'] === 'Remote') ? 'fa-house-laptop' : 'fa-building'; ?> mr-1 text-gray-300"></i> <?php echo htmlspecialchars($job['work_type']); ?></span>
                                </div>
                                
                                <div class="flex gap-2 mt-4">
                                    <?php 
                                        $tags = explode(',', $job['tags']);
                                        foreach($tags as $tag): 
                                            if(empty(trim($tag))) continue;
                                    ?>
                                        <span class="text-[9px] font-black uppercase tracking-widest bg-gray-50 text-gray-400 px-3 py-1.5 rounded-lg group-hover:bg-teal-600 group-hover:text-white transition-colors duration-300 italic">
                                            #<?php echo trim($tag); ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col items-end gap-3 w-full md:w-auto">
                            <div class="text-right">
                                <span class="block text-xl font-black text-[#06201d]">
                                    <?php echo $symbol . number_format($job['salary_min']); ?> – <?php echo number_format($job['salary_max']); ?>
                                </span>
                                <span class="text-[10px] text-gray-400 font-black uppercase tracking-tighter">
                                    Posted <?php echo date('M d, Y', strtotime($job['posted_date'])); ?>
                                </span>
                            </div>
                            <a href="job-details.php?id=<?php echo $job['id']; ?>" class="mt-2 w-full md:w-auto text-center px-10 py-3 bg-[#06201d] text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-teal-600 transition-all shadow-lg shadow-teal-900/10 active:scale-95">
                                View Details
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-24 bg-white rounded-[3rem] border border-dashed border-gray-200">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fa-solid fa-magnifying-glass text-2xl text-gray-200"></i>
                    </div>
                    <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">No opportunities found at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include '../../../includes/footer.php'; ?>