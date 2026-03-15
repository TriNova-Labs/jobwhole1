<?php 
  // Database connection
  include '../../includes/db.php'; 
  
  // Fetching companies - Sorted by verification first
  $stmt = $pdo->query("SELECT * FROM companies ORDER BY is_verified DESC, name ASC");
  $companies = $stmt->fetchAll();

  // Fetch unique industries for the dropdown to avoid duplicates
  $industryStmt = $pdo->query("SELECT DISTINCT industry FROM companies WHERE industry IS NOT NULL AND industry != '' ORDER BY industry ASC");
  $industries = $industryStmt->fetchAll();

  // Include the Header (handles <html>, <head>, and <nav>)
  include '../../includes/header.php'; 
?>

<link rel="stylesheet" href="../../assets/css/companies.css">

<main class="bg-gray-50 min-h-screen pt-32 pb-20">
    <div class="max-w-6xl mx-auto px-6">
        <header>
            <h1 class="text-3xl font-bold text-gray-900">Partner Companies</h1>
            <p class="text-gray-500 mt-2">Connect with verified employers committed to inclusive hiring</p>
            
            <div class="mt-8 flex flex-col md:flex-row gap-4">
                <div class="flex-1 relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-4 text-gray-400"></i>
                    <input type="text" id="companySearch" placeholder="Search companies..." class="w-full pl-12 pr-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 shadow-sm bg-white">
                </div>
                
                <div class="md:w-1/4 relative">
                    <i class="fa-solid fa-filter absolute left-4 top-4 text-gray-400"></i>
                    <select id="industryFilter" class="w-full pl-10 pr-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 shadow-sm bg-white text-gray-500 appearance-none cursor-pointer">
                        <option value="">All Industries</option>
                        <?php foreach ($industries as $ind): ?>
                            <option value="<?php echo htmlspecialchars($ind['industry']); ?>">
                                <?php echo htmlspecialchars($ind['industry']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button id="verifiedToggle" class="px-6 py-3 bg-white border rounded-lg flex items-center justify-center gap-2 hover:bg-gray-50 transition shadow-sm font-semibold">
                    <i class="fa-solid fa-circle-check text-teal-600"></i> Verified Only
                </button>
            </div>
            
            <p class="mt-6 text-sm text-gray-500">
                <span id="companyCount"><?php echo count($companies); ?></span> companies found
            </p>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mt-10">
            <?php foreach ($companies as $company): ?>
                <a href="company-details.php?id=<?php echo $company['id']; ?>" class="group block company-card" 
                   data-name="<?php echo strtolower(htmlspecialchars($company['name'])); ?>"
                   data-industry="<?php echo strtolower(htmlspecialchars($company['industry'] ?? '')); ?>"
                   data-verified="<?php echo $company['is_verified'] ? 'true' : 'false'; ?>">
                    
                    <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm transition-all duration-300 hover:shadow-xl hover:-translate-y-2 hover:border-teal-500/30 relative overflow-hidden h-full">
                        
                        <?php if($company['is_verified']): ?>
                            <div class="absolute top-6 right-6 text-teal-500 bg-teal-50 w-8 h-8 rounded-full flex items-center justify-center shadow-sm">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>
                        <?php endif; ?>

                        <div class="flex flex-col h-full">
                            <div class="flex items-start gap-4 mb-6">
                                <div class="w-14 h-14 bg-teal-50 rounded-2xl flex items-center justify-center text-teal-600 text-2xl font-bold border border-teal-100 group-hover:bg-teal-600 group-hover:text-white transition-colors">
                                    <?php echo substr(htmlspecialchars($company['name']), 0, 1); ?>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 leading-tight"><?php echo htmlspecialchars($company['name']); ?></h3>
                                    <p class="text-gray-400 text-xs font-medium"><?php echo htmlspecialchars($company['industry'] ?? 'Corporate Partner'); ?></p>
                                </div>
                            </div>

                            <div class="space-y-3 mb-8">
                                <div class="flex items-center gap-3 text-gray-500 text-xs">
                                    <i class="fa-solid fa-location-dot w-4 text-gray-300"></i>
                                    <span><?php echo htmlspecialchars($company['location'] ?? 'Bataan, Philippines'); ?></span>
                                </div>
                                <div class="flex items-center gap-3 text-gray-500 text-xs">
                                    <i class="fa-solid fa-users w-4 text-gray-300"></i>
                                    <span><?php echo htmlspecialchars($company['size'] ?? '51-200'); ?> employees</span>
                                </div>
                            </div>

                            <div class="flex gap-2 mt-auto pt-6 border-t border-gray-50">
                                <span class="px-3 py-1 bg-purple-50 text-purple-600 text-[9px] font-black uppercase tracking-wider rounded-lg">Inclusive</span>
                                <span class="px-3 py-1 bg-orange-50 text-orange-600 text-[9px] font-black uppercase tracking-wider rounded-lg">Ethical</span>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<script src="../../assets/js/companies.js"></script>

<?php include '../../includes/footer.php'; ?>