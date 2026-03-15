<?php
include '../../includes/db.php'; 

// 1. ACTION LOGIC: Handle deletions before the header
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $company_id = intval($_GET['id']);
    $stmt = $pdo->prepare("DELETE FROM companies WHERE id = ?");
    $stmt->execute([$company_id]);
    header("Location: manage-companies.php?msg=deleted");
    exit();
}

// 2. HEADER: Standardized flex-wrapper
include '../../includes/admin/header.php';

// 3. DATA FETCHING: Get all partner companies
$query = "SELECT * FROM companies ORDER BY name ASC";
$companies = $pdo->query($query)->fetchAll();
?>

<main class="flex-grow bg-gray-50 pt-32 pb-20 px-8">
    <div class="max-w-6xl mx-auto">
        
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-10">
            <div>
                <h1 class="text-4xl font-black text-[#06201d] tracking-tight">Partner Companies</h1>
                <p class="text-gray-500 mt-1">Manage the organizations contributing to ethical growth on HIRAYA.</p>
            </div>
            <a href="add-company.php" class="bg-teal-600 hover:bg-teal-700 text-white px-8 py-4 rounded-2xl font-bold text-sm transition-all shadow-lg shadow-teal-900/20 flex items-center gap-2">
                <i class="fa-solid fa-plus text-xs"></i> Add New Partner
            </a>
        </header>

        <?php if (isset($_GET['msg'])): ?>
            <div class="mb-8 p-4 rounded-2xl bg-[#06201d] text-teal-400 text-sm font-bold flex items-center gap-3 border border-teal-900/50">
                <i class="fa-solid fa-circle-check"></i>
                Company record has been successfully updated.
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100">
                            <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Company Details</th>
                            <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Industry</th>
                            <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Location</th>
                            <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php if (count($companies) > 0): ?>
                            <?php foreach ($companies as $company): ?>
                                <tr class="hover:bg-gray-50/30 transition-colors group">
                                    <td class="px-10 py-8">
                                        <div class="flex items-center gap-5">
                                            <div class="w-12 h-12 bg-teal-50 rounded-2xl flex items-center justify-center text-teal-700 font-black border border-teal-100/50 group-hover:scale-110 transition-transform">
                                                <?php echo strtoupper(substr($company['name'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <span class="block font-black text-gray-900 tracking-tight text-lg"><?php echo htmlspecialchars($company['name']); ?></span>
                                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Partner ID: #<?php echo $company['id']; ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-10 py-8 text-sm font-bold text-gray-500 uppercase tracking-tight">
                                        <?php echo htmlspecialchars($company['industry'] ?? 'General'); ?>
                                    </td>
                                    <td class="px-10 py-8 text-sm text-gray-500">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-location-dot text-teal-500/30 text-xs"></i>
                                            <span class="font-medium"><?php echo htmlspecialchars($company['location']); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-10 py-8 text-right">
                                        <div class="flex justify-end gap-6 text-[10px] font-black uppercase tracking-[0.1em]">
                                            <a href="edit-company.php?id=<?php echo $company['id']; ?>" class="text-teal-600 hover:text-teal-800 transition-colors">Edit Details</a>
                                            <a href="?action=delete&id=<?php echo $company['id']; ?>" 
                                               onclick="return confirm('Remove this partner? All linked jobs will be affected.')" 
                                               class="text-red-400 hover:text-red-600 transition-colors">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="px-10 py-20 text-center">
                                    <p class="text-gray-400 italic font-medium">No partner companies registered yet.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>