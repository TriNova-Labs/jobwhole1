<?php 
// We are now 2 levels deep, so we need ../../ to reach includes
include '../../includes/header.php'; 
?>

<main class="bg-gray-50 min-h-screen pt-32 pb-20">
    <div class="max-w-xl mx-auto px-6">
        <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
            
            <div class="bg-[#06201d] p-10 text-white text-center">
                <h1 class="text-3xl font-bold">Employer Partners</h1>
                <p class="text-teal-200/70 mt-2">Create your account and start hiring ethically.</p>
            </div>

            <form action="signup-action.php" method="POST" class="p-10 space-y-6">
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm font-medium">
                        <i class="fa-solid fa-triangle-exclamation mr-2"></i>
                        Registration failed. Please try a different email.
                    </div>
                <?php endif; ?>

                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-widest text-gray-400">Company Name</label>
                    <input type="text" name="company_name" required placeholder="e.g. TriNova Labs" 
                           class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:border-teal-500 outline-none transition-all">
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-widest text-gray-400">Full Name</label>
                    <input type="text" name="full_name" required placeholder="Juan Dela Cruz" 
                           class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:border-teal-500 outline-none transition-all">
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-widest text-gray-400">Business Email</label>
                    <input type="email" name="email" required placeholder="hr@company.com" 
                           class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:border-teal-500 outline-none transition-all">
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-widest text-gray-400">Password</label>
                    <input type="password" name="password" required placeholder="••••••••" 
                           class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:border-teal-500 outline-none transition-all">
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full py-4 bg-teal-600 text-white font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-teal-900/20 hover:bg-teal-700 hover:-translate-y-1 transition-all">
                        Create Employer Account
                    </button>
                </div>

                <p class="text-center text-sm text-gray-500 pt-4">
                    Already have a partner account? 
                    <a href="login.php" class="text-teal-600 font-bold hover:underline">Login here</a>
                </p>
            </form>
        </div>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>