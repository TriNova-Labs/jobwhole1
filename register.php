<?php include 'includes/header.php'; ?>

<main class="min-h-screen bg-gray-50 pt-32 pb-20 px-6">
    <div class="max-w-2xl mx-auto">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-gray-900">Join Hiraya</h1>
            <p class="text-gray-500 mt-2">Create an employer account to start hiring inclusively.</p>
        </div>

        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
            <form action="auth/register_process.php" method="POST" class="space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">First Name</label>
                        <input type="text" name="first_name" required 
                               class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-teal-500 focus:outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Last Name</label>
                        <input type="text" name="last_name" required 
                               class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-teal-500 focus:outline-none transition">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                    <input type="email" name="email" required 
                           class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-teal-500 focus:outline-none transition"
                           placeholder="name@company.com">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" required 
                           class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-teal-500 focus:outline-none transition">
                </div>

                <hr class="border-gray-100">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Company Name</label>
                    <input type="text" name="company_name" required 
                           class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-teal-500 focus:outline-none transition"
                           placeholder="e.g., TechBridge Solutions">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Office Location</label>
                    <input type="text" name="location" required 
                           class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-teal-500 focus:outline-none transition"
                           placeholder="City, Philippines">
                </div>

                <div class="pt-4">
                    <button type="submit" 
                            class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-4 rounded-xl transition shadow-lg shadow-teal-100 flex items-center justify-center gap-2">
                        Create Employer Account <i class="fa-solid fa-arrow-right text-sm"></i>
                    </button>
                </div>
            </form>

            <p class="text-center text-sm text-gray-500 mt-8">
                Already have an account? <a href="login.php" class="text-teal-600 font-bold hover:underline">Sign in here</a>
            </p>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>