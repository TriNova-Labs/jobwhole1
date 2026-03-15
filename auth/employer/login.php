<?php 
// 2 levels deep to reach includes
include '../../includes/header.php'; 
?>

<main class="bg-gray-50 min-h-screen pt-32 pb-20">
    <div class="max-w-xl mx-auto px-6">
        <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
            
            <div class="bg-[#06201d] p-10 text-white text-center">
                <h1 class="text-3xl font-bold">Partner Login</h1>
                <p class="text-teal-200/70 mt-2">Access your employer dashboard and manage listings.</p>
            </div>

            <form action="login-action.php" method="POST" class="p-10 space-y-6">
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm font-medium">
                        <i class="fa-solid fa-circle-exclamation mr-2"></i>
                        Invalid email or password. Please try again.
                    </div>
                <?php endif; ?>

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
                        Sign In to Dashboard
                    </button>
                </div>

                <div class="text-center space-y-3 pt-4">
                    <p class="text-sm text-gray-500">
                        Need to hire? 
                        <a href="signup.php" class="text-teal-600 font-bold hover:underline">Create a partner account</a>
                    </p>
                    <p class="text-xs text-gray-400">
                        Looking for a job instead? 
                        <a href="../login.php" class="hover:text-gray-600 underline">Candidate Login</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>