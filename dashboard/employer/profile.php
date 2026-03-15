<?php
session_start();
require_once "../../includes/db.php";

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['account_type'] !== 'employer') {
    header("Location: /sdg/auth/employer/login.php");
    exit();
}

$employer_id = $_SESSION['user_id'];

// Fetch current company data
$stmt = $pdo->prepare("SELECT * FROM employers WHERE id = ?");
$stmt->execute([$employer_id]);
$employer = $stmt->fetch(PDO::FETCH_ASSOC);

include '../../includes/employer/header.php';
?>

<main class="bg-gray-50 min-h-screen pt-32 pb-20">
    <div class="max-w-3xl mx-auto px-6">
        <div class="mb-10">
            <a href="index.php" class="text-[10px] font-black uppercase tracking-[0.2em] text-teal-600 flex items-center gap-2 mb-2 hover:gap-3 transition-all">
                <i class="fa-solid fa-arrow-left"></i> Dashboard
            </a>
            <h1 class="text-4xl font-black text-[#06201d] tracking-tight">Company Profile</h1>
            <p class="text-gray-400 text-[10px] font-black uppercase tracking-[0.2em] mt-2">Manage your public presence</p>
        </div>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <div class="mb-6 bg-teal-50 border border-teal-200 text-teal-700 px-6 py-4 rounded-2xl text-xs font-black uppercase tracking-widest flex items-center gap-3">
                <i class="fa-solid fa-circle-check"></i>
                Profile updated successfully!
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50 overflow-hidden">
            <form action="update-profile.php" method="POST" enctype="multipart/form-data" class="p-10 space-y-8">
                
                <div class="flex flex-col md:flex-row items-center gap-8 pb-8 border-b border-gray-50">
                    <div class="w-24 h-24 bg-gray-50 rounded-3xl overflow-hidden border-2 border-dashed border-gray-200 flex items-center justify-center shadow-inner">
                        <?php if (!empty($employer['logo_path'])): ?>
                            <img src="../../uploads/logos/<?php echo htmlspecialchars($employer['logo_path']); ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <i class="fa-solid fa-building text-3xl text-gray-200"></i>
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex-1 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Company Logo</label>
                        <input type="file" name="logo" accept="image/*" 
                               class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer">
                        <p class="text-[9px] text-gray-400 font-medium">JPG or PNG. Max 2MB.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-2">Company Name</label>
                        <input type="text" name="full_name" value="<?php echo htmlspecialchars($employer['full_name']); ?>" required
                               class="w-full px-6 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:border-teal-500 outline-none transition-all font-bold text-[#06201d]">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-2">Registered Email</label>
                        <input type="email" value="<?php echo htmlspecialchars($employer['email']); ?>" disabled
                               class="w-full px-6 py-4 rounded-2xl border border-gray-100 bg-gray-100 text-gray-400 outline-none cursor-not-allowed font-medium">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-2">Company Bio / Mission</label>
                    <textarea name="bio" rows="5" placeholder="Describe your company culture and mission..."
                              class="w-full px-6 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:border-teal-500 outline-none transition-all font-medium text-gray-600"><?php echo htmlspecialchars($employer['bio'] ?? ''); ?></textarea>
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full md:w-auto px-12 py-5 bg-[#06201d] text-teal-400 font-black uppercase tracking-widest rounded-2xl hover:bg-teal-600 hover:text-white transition-all shadow-lg shadow-teal-900/20">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>