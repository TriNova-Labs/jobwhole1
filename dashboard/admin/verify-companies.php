<?php
session_start();
require_once "../../includes/db.php";

// Admin Auth Check
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../auth/admin/login.php");
    exit();
}

// Handle Verification Toggle
if (isset($_GET['id']) && isset($_GET['status'])) {
    $new_status = $_GET['status'] == 'verify' ? 1 : 0;
    $stmt = $pdo->prepare("UPDATE companies SET is_verified = ? WHERE id = ?");
    $stmt->execute([$new_status, $_GET['id']]);
    header("Location: verify-companies.php?update=success");
    exit();
}

// Fetch all companies
$companies = $pdo->query("SELECT * FROM companies ORDER BY is_verified ASC, name ASC")->fetchAll();

include '../../includes/header.php';
?>

<main class="bg-gray-50 min-h-screen pt-32 pb-20 text-left">
    <div class="max-w-7xl mx-auto px-6">
        
        <div class="mb-10">
            <a href="index.php" class="text-xs font-black uppercase tracking-widest text-teal-600 hover:text-teal-700 transition flex items-center gap-2 mb-4">
                <i class="fa-solid fa-arrow-left"></i> Dashboard
            </a>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight">Company Audits</h1>
            <p class="text-gray-500 mt-1 font-medium">Review and verify business partners for ethical alignment.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($companies as $company): ?>
                <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm flex flex-col justify-between hover:shadow-md transition-all">
                    <div>
                        <div class="flex justify-between items-start mb-6">
                            <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center text-teal-600 border border-gray-100">
                                <i class="fa-solid fa-building text-xl"></i>
                            </div>
                            <?php if ($company['is_verified']): ?>
                                <span class="px-3 py-1 bg-teal-50 text-teal-600 text-[10px] font-black uppercase tracking-widest rounded-lg border border-teal-100">
                                    <i class="fa-solid fa-check-double mr-1"></i> Verified
                                </span>
                            <?php else: ?>
                                <span class="px-3 py-1 bg-orange-50 text-orange-600 text-[10px] font-black uppercase tracking-widest rounded-lg border border-orange-100">
                                    Pending Audit
                                </span>
                            <?php endif; ?>
                        </div>

                        <h3 class="text-xl font-black text-gray-900 mb-1"><?php echo htmlspecialchars($company['name']); ?></h3>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-4 italic">
                            <i class="fa-solid fa-location-dot mr-1"></i> <?php echo htmlspecialchars($company['location'] ?? 'Location TBD'); ?>
                        </p>
                        <p class="text-sm text-gray-500 leading-relaxed line-clamp-3 mb-6">
                            <?php echo htmlspecialchars($company['description'] ?? 'No description provided.'); ?>
                        </p>
                    </div>

                    <div class="pt-6 border-t border-gray-50 flex items-center justify-between">
                        <a href="company-profile.php?id=<?php echo $company['id']; ?>" class="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-teal-600 transition">View Profile</a>
                        
                        <?php if (!$company['is_verified']): ?>
                            <a href="verify-companies.php?id=<?php echo $company['id']; ?>&status=verify" 
                               class="px-6 py-3 bg-[#06201d] text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-teal-700 transition shadow-lg shadow-teal-900/10">
                                Approve Partner
                            </a>
                        <?php else: ?>
                            <a href="verify-companies.php?id=<?php echo $company['id']; ?>&status=unverify" 
                               class="px-6 py-3 bg-white border border-gray-200 text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-xl hover:text-red-500 hover:border-red-100 transition">
                                Revoke
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($companies)): ?>
            <div class="bg-white p-20 rounded-[3rem] text-center border border-dashed border-gray-200">
                <i class="fa-solid fa-building-circle-exclamation text-5xl text-gray-100 mb-4"></i>
                <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">No companies registered yet</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>