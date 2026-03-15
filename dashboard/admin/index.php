<?php
session_start();
require_once "../../includes/db.php";

// Admin Auth Check
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../auth/admin/login.php");
    exit();
}

// Fetch Stats for SDG 8 Impact Tracking
$totalJobs = $pdo->query("SELECT COUNT(*) FROM jobs WHERE is_active = 1")->fetchColumn();
$pendingJobs = $pdo->query("SELECT COUNT(*) FROM jobs WHERE is_active = 0")->fetchColumn();
$totalCompanies = $pdo->query("SELECT COUNT(*) FROM companies WHERE is_verified = 1")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(); // Dynamic user count

// Calculate Average Salary for "Decent Work" Metric
$avgSalary = $pdo->query("SELECT AVG((salary_min + salary_max) / 2) FROM jobs")->fetchColumn();

// Fetch Admin Team Members
$adminsStmt = $pdo->query("SELECT full_name, email, created_at FROM admins ORDER BY created_at DESC");
$adminTeam = $adminsStmt->fetchAll();

include '../../includes/admin/header.php';
?>

<main class="bg-gray-50 min-h-screen pt-32 pb-20">
    <div class="max-w-7xl mx-auto px-6">
        
        <header class="mb-12 flex flex-col md:flex-row md:items-center justify-between gap-4 text-left">
            <div>
                <h1 class="text-4xl font-black text-gray-900 tracking-tight">Admin Console</h1>
                <p class="text-gray-500 mt-1 font-medium">Monitoring the HIRAYA ecosystem & SDG 8 progress.</p>
            </div>
            <div class="flex gap-3">
                <div class="px-5 py-2 bg-white rounded-2xl border border-gray-100 shadow-sm flex items-center gap-2">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    <span class="text-xs font-bold text-gray-600 uppercase tracking-widest">System Live</span>
                </div>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12 text-left">
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm hover:shadow-md transition-all">
                <p class="text-[10px] font-black text-teal-600 uppercase tracking-[0.2em] mb-4">Total Listings</p>
                <div class="flex items-end gap-2">
                    <span class="text-4xl font-black text-gray-900"><?php echo $totalJobs; ?></span>
                    <span class="text-gray-400 text-xs font-bold mb-1">Live Jobs</span>
                </div>
            </div>

            <div class="bg-[#06201d] p-8 rounded-[2.5rem] shadow-xl shadow-teal-900/20 relative overflow-hidden group">
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-teal-400 uppercase tracking-[0.2em] mb-4">Verification Queue</p>
                    <div class="flex items-end gap-2">
                        <span class="text-4xl font-black text-white"><?php echo $pendingJobs; ?></span>
                        <a href="manage-jobs.php" class="text-teal-300 text-xs font-bold mb-1 hover:underline">Review Now →</a>
                    </div>
                </div>
                <i class="fa-solid fa-clock-rotate-left absolute -right-4 -bottom-4 text-white/5 text-7xl transition-transform group-hover:scale-110"></i>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <p class="text-[10px] font-black text-purple-600 uppercase tracking-[0.2em] mb-4">Partner Growth</p>
                <div class="flex items-end gap-2">
                    <span class="text-4xl font-black text-gray-900"><?php echo $totalCompanies; ?></span>
                    <span class="text-gray-400 text-xs font-bold mb-1">Verified Co.</span>
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <p class="text-[10px] font-black text-orange-600 uppercase tracking-[0.2em] mb-4">Avg. Salary Rate</p>
                <div class="flex items-end gap-2">
                    <span class="text-3xl font-black text-gray-900">₱<?php echo number_format($avgSalary / 1000, 1); ?>k</span>
                    <span class="text-gray-400 text-xs font-bold mb-1">Monthly</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 text-left">
            <div class="lg:col-span-2 space-y-12">
                
                <section>
                    <h2 class="text-xl font-bold text-gray-900 px-2 mb-6">Administrative Actions</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="manage-users.php" class="group p-6 bg-white border border-gray-100 rounded-3xl flex items-center gap-5 hover:border-teal-500/50 transition-all">
                            <div class="w-14 h-14 bg-teal-50 text-teal-600 rounded-2xl flex items-center justify-center text-xl group-hover:bg-teal-600 group-hover:text-white transition-all">
                                <i class="fa-solid fa-users-gear"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900">User Management</p>
                                <p class="text-xs text-gray-400">Manage <?php echo $totalUsers; ?> active accounts</p>
                            </div>
                        </a>
                        
                        <a href="verify-companies.php" class="group p-6 bg-white border border-gray-100 rounded-3xl flex items-center gap-5 hover:border-teal-500/50 transition-all">
                            <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-xl group-hover:bg-blue-600 group-hover:text-white transition-all">
                                <i class="fa-solid fa-building-circle-check"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900">Company Audits</p>
                                <p class="text-xs text-gray-400">Review ethical certifications</p>
                            </div>
                        </a>
                    </div>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-gray-900 px-2 mb-6">Administrative Team</h2>
                    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Name</th>
                                    <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Email</th>
                                    <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Registered</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php foreach ($adminTeam as $member): ?>
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-8 py-5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 bg-[#06201d] text-teal-400 rounded-lg flex items-center justify-center text-[10px] font-black">
                                                    <?php echo strtoupper(substr($member['full_name'], 0, 2)); ?>
                                                </div>
                                                <span class="font-bold text-gray-900 text-sm"><?php echo htmlspecialchars($member['full_name']); ?></span>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 text-sm text-gray-500 font-medium">
                                            <?php echo htmlspecialchars($member['email']); ?>
                                        </td>
                                        <td class="px-8 py-5 text-right">
                                            <span class="text-[10px] font-black text-gray-400 uppercase">
                                                <?php echo date('M d, Y', strtotime($member['created_at'])); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

            <div>
                <div class="bg-gradient-to-br from-teal-600 to-[#06201d] p-8 rounded-[2.5rem] text-white sticky top-32">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mb-6">
                        <i class="fa-solid fa-leaf text-teal-300"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-2">SDG 8 Alignment</h3>
                    <p class="text-teal-100/70 text-sm leading-relaxed mb-6">
                        Your platform is currently supporting decent work opportunities for marginalized sectors in Bataan.
                    </p>
                    <div class="space-y-4">
                        <div class="flex justify-between text-xs font-bold uppercase tracking-widest text-teal-400">
                            <span>Inclusive Hiring</span>
                            <span>85%</span>
                        </div>
                        <div class="w-full bg-white/10 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-teal-400 h-full" style="width: 85%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>