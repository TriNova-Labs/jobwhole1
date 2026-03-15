<?php
session_start();
require_once "../../includes/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit();
}

// Handle feedback messages
$status = $_GET['status'] ?? '';

// Fetch latest user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

include '../../includes/header.php';
?>

<main class="bg-gray-50 min-h-screen pb-20">
    <?php if ($status === 'success'): ?>
    <div id="toast" class="fixed top-10 left-1/2 -translate-x-1/2 z-50 bg-[#06201d] text-white px-8 py-4 rounded-2xl shadow-2xl font-black uppercase tracking-widest text-[10px] flex items-center gap-3 animate-bounce">
        <i class="fa-solid fa-circle-check text-teal-400"></i> Profile Updated Successfully
    </div>
    <script>setTimeout(() => document.getElementById('toast').remove(), 3000);</script>
    <?php endif; ?>

    <div class="h-64 bg-[#06201d] w-full"></div>

    <div class="max-w-5xl mx-auto px-6 -mt-32">
        <div class="bg-white rounded-[3rem] shadow-xl shadow-gray-200/50 overflow-hidden border border-gray-100">
            <div class="p-8 md:p-12 flex flex-col md:flex-row items-center gap-8 border-b border-gray-50">
                <div class="w-32 h-32 bg-teal-50 border-4 border-white rounded-[2.5rem] shadow-lg flex items-center justify-center text-4xl font-black text-teal-600">
                    <?php echo strtoupper(substr($user['first_name'] ?? $user['username'], 0, 1)); ?>
                </div>

                <div class="flex-1 text-center md:text-left">
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mb-2">
                        <h1 class="text-3xl font-black text-[#06201d] tracking-tight">
                            <?php 
                                if(!empty($user['last_name'])) {
                                    echo htmlspecialchars($user['last_name'] . ', ' . $user['first_name'] . ' ' . $user['middle_initial'] . ' ' . $user['extension']);
                                } else {
                                    echo htmlspecialchars($user['username']);
                                }
                            ?>
                        </h1>
                        <span class="px-4 py-1 bg-teal-100 text-teal-700 text-[10px] font-black uppercase tracking-widest rounded-full border border-teal-200">
                            <?php echo $user['employment_status'] ?? 'searching'; ?>
                        </span>
                    </div>
                    <p class="text-gray-400 font-bold tracking-tight uppercase text-xs">Professional Job Seeker • Verified Member</p>
                </div>

                <button onclick="toggleEditMode()" class="px-8 py-4 bg-gray-50 text-[#06201d] font-black uppercase tracking-widest text-[11px] rounded-2xl hover:bg-gray-100 transition-all border border-gray-100">
                    <i class="fa-solid fa-pen-to-square mr-2"></i> Edit Profile
                </button>
            </div>

            <div class="flex px-12 bg-white sticky top-0 z-10">
                <button class="px-6 py-4 border-b-2 border-teal-600 text-teal-600 text-xs font-black uppercase tracking-widest">Personal Info</button>
                <button class="px-6 py-4 text-gray-400 text-xs font-black uppercase tracking-widest hover:text-teal-600 transition">My Documents</button>
            </div>

            <form action="../action/update-profile.php" method="POST" enctype="multipart/form-data" class="p-12 space-y-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="md:col-span-1 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-2">First Name</label>
                        <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none font-bold text-[#06201d] focus:ring-2 focus:ring-teal-500/20">
                    </div>
                    <div class="md:col-span-1 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-2">Middle Initial</label>
                        <input type="text" name="middle_initial" maxlength="2" value="<?php echo htmlspecialchars($user['middle_initial'] ?? ''); ?>" class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none font-bold text-[#06201d]">
                    </div>
                    <div class="md:col-span-1 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-2">Last Name</label>
                        <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none font-bold text-[#06201d]">
                    </div>
                    <div class="md:col-span-1 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-2">Extension (e.g. Jr.)</label>
                        <input type="text" name="extension" value="<?php echo htmlspecialchars($user['extension'] ?? ''); ?>" class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none font-bold text-[#06201d]">
                    </div>
                </div>

                <div class="space-y-6 pt-6 border-t border-gray-50">
                    <div class="flex justify-between items-end">
                        <div class="space-y-1">
                            <h3 class="text-xl font-black text-[#06201d] flex items-center gap-3">
                                <i class="fa-solid fa-shield-halved text-teal-600"></i>
                                Professional Document Vault
                            </h3>
                            <p class="text-gray-400 text-xs font-medium max-w-2xl">Manage your credentials. Uploaded files are securely stored and ready for applications.</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                        <div class="p-6 rounded-[2rem] border-2 border-dashed border-gray-100 bg-gray-50/50 flex items-center justify-between hover:border-teal-200 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center text-teal-600">
                                    <i class="fa-solid fa-file-pdf text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-[11px] font-black uppercase tracking-widest text-[#06201d]">Resume / CV</p>
                                    <p class="text-[10px] text-gray-400 font-bold truncate max-w-[150px]">
                                        <?php echo $user['resume_path'] ? $user['resume_path'] : 'No file uploaded'; ?>
                                    </p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <?php if($user['resume_path']): ?>
                                    <a href="../../uploads/documents/<?php echo $user['resume_path']; ?>" target="_blank" class="px-4 py-2 bg-white text-teal-600 text-[9px] font-black uppercase tracking-widest rounded-lg border border-teal-100 hover:bg-teal-50 transition">View</a>
                                <?php endif; ?>
                                <input type="file" name="resume" class="hidden" id="resume_upload">
                                <label for="resume_upload" class="px-4 py-2 bg-[#06201d] text-white text-[9px] font-black uppercase tracking-widest rounded-lg hover:bg-teal-700 cursor-pointer transition">Update</label>
                            </div>
                        </div>

                        <div class="p-6 rounded-[2rem] border-2 border-dashed border-gray-100 bg-gray-50/50 flex items-center justify-between hover:border-teal-200 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center text-teal-600">
                                    <i class="fa-solid fa-fingerprint text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-[11px] font-black uppercase tracking-widest text-[#06201d]">NBI Clearance</p>
                                    <p class="text-[10px] text-gray-400 font-bold">
                                        <?php echo $user['nbi_clearance_path'] ? 'File Verified' : 'Required for most roles'; ?>
                                    </p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <?php if($user['nbi_clearance_path']): ?>
                                    <a href="../../uploads/documents/<?php echo $user['nbi_clearance_path']; ?>" target="_blank" class="px-4 py-2 bg-white text-teal-600 text-[9px] font-black uppercase tracking-widest rounded-lg border border-teal-100 hover:bg-teal-50 transition">View</a>
                                <?php endif; ?>
                                <input type="file" name="nbi_clearance" class="hidden" id="nbi_upload">
                                <label for="nbi_upload" class="px-4 py-2 bg-[#06201d] text-white text-[9px] font-black uppercase tracking-widest rounded-lg hover:bg-teal-700 cursor-pointer transition">Update</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t border-gray-50 flex justify-end">
                    <button type="submit" class="px-10 py-5 bg-[#06201d] text-white font-black uppercase tracking-[0.2em] text-xs rounded-2xl shadow-xl shadow-teal-900/20 hover:bg-teal-700 transition-all active:scale-95">
                        Save Profile Changes
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-12 bg-white rounded-[2.5rem] p-10 border border-teal-100 shadow-sm">
            <h4 class="text-xs font-black text-teal-600 uppercase tracking-[0.2em] mb-6 block italic">User Application Preparation Checklist</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="space-y-3">
                    <p class="text-[11px] font-black text-[#06201d] uppercase tracking-widest">📄 Core Documents</p>
                    <ul class="text-[10px] text-gray-500 space-y-2 font-bold">
                        <li>• Resume / CV (Summary of skills)</li>
                        <li>• Cover Letter (Why you fit)</li>
                    </ul>
                </div>
                <div class="space-y-3">
                    <p class="text-[11px] font-black text-[#06201d] uppercase tracking-widest">🪪 Identity & Legal</p>
                    <ul class="text-[10px] text-gray-500 space-y-2 font-bold">
                        <li>• NBI / Police Clearance</li>
                        <li>• Barangay Clearance</li>
                        <li>• Gov-Issued ID (Passport/DL)</li>
                    </ul>
                </div>
                <div class="space-y-3">
                    <p class="text-[11px] font-black text-[#06201d] uppercase tracking-widest">🎓 Professional</p>
                    <ul class="text-[10px] text-gray-500 space-y-2 font-bold">
                        <li>• Transcript of Records</li>
                        <li>• Certificate of Employment</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function toggleEditMode() {
    const inputs = document.querySelectorAll('input:not([type="file"])');
    inputs[0].focus();
    // Smooth scroll to the form
    window.scrollTo({
        top: document.querySelector('form').offsetTop - 100,
        behavior: 'smooth'
    });
}
</script>

<?php include '../../includes/footer.php'; ?>