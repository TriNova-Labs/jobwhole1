<?php
session_start();
require_once "../../includes/db.php";

// Admin Auth Check
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../auth/admin/login.php");
    exit();
}

// Handle User Deletion
if (isset($_GET['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$_GET['delete_id']]);
    header("Location: manage-users.php?status=deleted");
    exit();
}

// Fetch Users - ensuring 'role' and 'created_at' columns exist
$users = $pdo->query("SELECT id, first_name, last_name, email, role, created_at FROM users ORDER BY created_at DESC")->fetchAll();

include '../../includes/admin/header.php';
?>

<main class="bg-gray-50 min-h-screen pt-32 pb-20">
    <div class="max-w-7xl mx-auto px-6">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
            <div class="text-left">
                <a href="index.php" class="text-xs font-black uppercase tracking-widest text-teal-600 hover:text-teal-700 transition flex items-center gap-2 mb-4">
                    <i class="fa-solid fa-arrow-left"></i> Dashboard
                </a>
                <h1 class="text-4xl font-black text-gray-900 tracking-tight">User Management</h1>
                <p class="text-gray-500 mt-1 font-medium">Oversee account activity across the Hiraya network.</p>
            </div>

            <div class="relative w-full md:w-80">
                <i class="fa-solid fa-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="userSearch" placeholder="Search by name or email..." 
                       class="w-full pl-12 pr-6 py-4 bg-white border border-gray-100 rounded-2xl shadow-sm focus:ring-2 focus:ring-teal-500 outline-none transition font-bold text-xs uppercase tracking-widest">
            </div>
        </div>

        <div class="flex justify-start mb-8">
            <div class="flex bg-white p-1.5 rounded-2xl shadow-sm border border-gray-100" id="filter-group">
                <button data-filter="all" class="filter-btn px-8 py-3 rounded-xl bg-[#06201d] text-white text-[10px] font-black uppercase tracking-widest shadow-lg transition-all">All Accounts</button>
                <button data-filter="seeker" class="filter-btn px-8 py-3 rounded-xl text-gray-400 text-[10px] font-black uppercase tracking-widest hover:text-teal-600 transition-all">Job Seekers</button>
                <button data-filter="employer" class="filter-btn px-8 py-3 rounded-xl text-gray-400 text-[10px] font-black uppercase tracking-widest hover:text-teal-600 transition-all">Employers</button>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
            <table class="w-full text-left" id="userTable">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400">User Identification</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Account Role</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Date Joined</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php foreach ($users as $user): ?>
                        <?php 
                            $fullName = $user['first_name'] . ' ' . $user['last_name'];
                            $role = strtolower($user['role'] ?? 'seeker');
                        ?>
                        <tr class="user-row hover:bg-gray-50/50 transition-colors" data-role="<?php echo $role; ?>">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-teal-50 text-teal-700 rounded-xl flex items-center justify-center font-black text-xs border border-teal-100 uppercase">
                                        <?php echo substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1); ?>
                                    </div>
                                    <div class="text-left">
                                        <p class="font-bold text-gray-900 text-sm userName"><?php echo htmlspecialchars($fullName); ?></p>
                                        <p class="text-xs text-gray-400 font-medium userEmail"><?php echo htmlspecialchars($user['email']); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest 
                                    <?php echo $role === 'employer' ? 'bg-purple-50 text-purple-600 border border-purple-100' : 'bg-blue-50 text-blue-600 border border-blue-100'; ?>">
                                    <?php echo $user['role'] ?? 'Seeker'; ?>
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <span class="text-[10px] font-black text-gray-400 uppercase">
                                    <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="manage-users.php?delete_id=<?php echo $user['id']; ?>" 
                                       onclick="return confirm('Are you sure you want to remove this account?')"
                                       class="w-10 h-10 flex items-center justify-center text-gray-300 hover:text-red-500 transition-all hover:bg-red-50 rounded-xl">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div id="noResults" class="hidden p-20 text-center">
                <i class="fa-solid fa-user-slash text-4xl text-gray-200 mb-4"></i>
                <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">No matching users found</p>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('userSearch');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const rows = document.querySelectorAll('.user-row');
    const noResults = document.getElementById('noResults');

    let activeFilter = 'all';

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        let visibleCount = 0;

        rows.forEach(row => {
            const name = row.querySelector('.userName').textContent.toLowerCase();
            const email = row.querySelector('.userEmail').textContent.toLowerCase();
            const role = row.getAttribute('data-role');

            const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
            const matchesRole = activeFilter === 'all' || role === activeFilter;

            if (matchesSearch && matchesRole) {
                row.style.display = 'table-row';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        noResults.classList.toggle('hidden', visibleCount > 0);
    }

    // Role Filter Click
    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            activeFilter = button.getAttribute('data-filter');
            
            // UI Toggle
            filterButtons.forEach(btn => {
                btn.classList.remove('bg-[#06201d]', 'text-white', 'shadow-lg');
                btn.classList.add('text-gray-400');
            });
            button.classList.add('bg-[#06201d]', 'text-white', 'shadow-lg');
            button.classList.remove('text-gray-400');

            filterTable();
        });
    });

    // Search Input
    searchInput.addEventListener('input', filterTable);
});
</script>

<?php include '../../includes/footer.php'; ?>