<footer class="mt-auto bg-[#06201d] text-gray-400 py-16 px-8 md:px-24 border-t border-gray-800">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
            <div class="space-y-4">
                <div class="flex items-center gap-2 text-white">
                    <span class="bg-teal-600 px-2.5 py-1 rounded shadow-lg shadow-teal-900/20 font-bold">H</span>
                    <span class="font-bold tracking-[0.2em] uppercase text-sm">Hiraya</span>
                </div>
                <p class="text-sm leading-relaxed pr-4 text-gray-400/80">
                    Bridging the gap between opportunity and accessibility. An inclusive career platform for everyone, aligned with UN SDG 8.
                </p>
                <div class="flex gap-4 pt-2">
                    <a href="#" class="w-8 h-8 rounded-full border border-gray-700 flex items-center justify-center hover:bg-teal-600 hover:text-white transition-all"><i class="fa-brands fa-facebook-f text-xs"></i></a>
                    <a href="#" class="w-8 h-8 rounded-full border border-gray-700 flex items-center justify-center hover:bg-teal-600 hover:text-white transition-all"><i class="fa-brands fa-linkedin-in text-xs"></i></a>
                    <a href="#" class="w-8 h-8 rounded-full border border-gray-700 flex items-center justify-center hover:bg-teal-600 hover:text-white transition-all"><i class="fa-brands fa-x-twitter text-xs"></i></a>
                </div>
            </div>

            <div>
                <h4 class="text-white text-xs font-bold uppercase tracking-widest mb-6">Platform</h4>
                <ul class="text-sm space-y-3">
                    <li><a href="<?php echo $base_path; ?>dashboard/user/jobs.php" class="hover:text-teal-400 flex items-center gap-2 transition-colors">Find Jobs</a></li>
                    <li><a href="<?php echo $base_path; ?>dashboard/user/companies.php" class="hover:text-teal-400 flex items-center gap-2 transition-colors">Partner Companies</a></li>
                    <li><a href="<?php echo $base_path; ?>dashboard/user/resources.php" class="hover:text-teal-400 flex items-center gap-2 transition-colors">Resources</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white text-xs font-bold uppercase tracking-widest mb-6">For Employers</h4>
                <ul class="text-sm space-y-3">
                    <li><a href="#" class="hover:text-teal-400 flex items-center gap-2 transition-colors">Ethical Hiring</a></li>
                    <li><a href="#" class="hover:text-teal-400 flex items-center gap-2 transition-colors">Post an Opening</a></li>
                    <li><a href="#" class="hover:text-teal-400 flex items-center gap-2 transition-colors">Pricing</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white text-xs font-bold uppercase tracking-widest mb-6">Mission</h4>
                <div class="bg-white/5 p-4 rounded-xl border border-white/5">
                    <p class="text-xs italic mb-3">Empowering decent work and economic growth for all.</p>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-amber-600 rounded flex items-center justify-center text-white font-bold text-[10px]">8</div>
                        <span class="text-[10px] font-bold text-gray-300 uppercase tracking-tighter">SDG 8.3 ALIGNED</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-16 pt-8 border-t border-gray-800 flex flex-col md:flex-row justify-between items-center gap-4 text-[10px] font-medium tracking-wide uppercase">
            <p>&copy; <?php echo date("Y"); ?> Hiraya Platform. Built for Inclusion.</p>
            <div class="flex gap-6">
                <a href="#" class="hover:text-white transition-colors">Privacy</a>
                <a href="#" class="hover:text-white transition-colors">Terms</a>
                <a href="#" class="hover:text-white transition-colors">Help</a>
            </div>
        </div>
    </div>
</footer>

</body>
</html>