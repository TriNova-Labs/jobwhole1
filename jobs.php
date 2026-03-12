<?php
// 1. Data Setup
$jobs = [
    [
        "title" => "Frontend Developer",
        "company" => "TechBridge Solutions",
        "location" => "Makati City, Philippines",
        "type" => "Hybrid",
        "schedule" => "full time",
        "tags" => ["PWD-Friendly", "Fresh Graduate"],
        "salary" => "PHP 35,000 – 55,000",
        "date" => "Mar 7, 2026",
        "icon" => "fa-code"
    ],
    [
        "title" => "Registered Nurse",
        "company" => "GreenCare Hospital",
        "location" => "Quezon City, Philippines",
        "type" => "On-site",
        "schedule" => "full time",
        "tags" => ["PWD-Friendly"],
        "salary" => "PHP 25,000 – 38,000",
        "date" => "Mar 7, 2026",
        "icon" => "fa-user-nurse"
    ],
    [
        "title" => "Online English Tutor",
        "company" => "EduForward Academy",
        "location" => "Remote - Philippines",
        "type" => "Remote",
        "schedule" => "part time",
        "tags" => ["Remote", "PWD-Friendly", "Fresh Graduate"],
        "salary" => "PHP 15,000 – 25,000",
        "date" => "Mar 7, 2026",
        "icon" => "fa-chalkboard-user"
    ],
];

// 2. Include the Header (This handles <html>, <head>, and <nav>)
include 'includes/header.php'; 
?>

<main class="bg-gray-50 min-h-screen pt-32 pb-20">
    <header class="max-w-6xl mx-auto px-6">
        <h1 class="text-3xl font-bold text-gray-900">Find Your Next Role</h1>
        <p class="text-gray-500 mt-2">Discover inclusive opportunities from verified employers</p>
        
        <div class="mt-8 flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-4 text-gray-400"></i>
                <input type="text" placeholder="Search by title, company, or keyword..." class="w-full pl-12 pr-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 shadow-sm">
            </div>
            <div class="md:w-1/4 relative">
                <i class="fa-solid fa-location-dot absolute left-4 top-4 text-gray-400"></i>
                <input type="text" placeholder="Location" class="w-full pl-10 pr-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 shadow-sm">
            </div>
            <button class="px-6 py-3 bg-white border rounded-lg flex items-center justify-center gap-2 hover:bg-gray-50 transition shadow-sm">
                <i class="fa-solid fa-sliders"></i> Filters
            </button>
        </div>
        <p class="mt-6 text-sm text-gray-500"><?php echo count($jobs); ?> jobs found</p>
    </header>

    <div class="max-w-6xl mx-auto p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($jobs as $job): ?>
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm hover:shadow-lg transition-all group relative">
                <i class="fa-regular fa-bookmark absolute top-6 right-6 text-gray-300 cursor-pointer hover:text-teal-600 transition"></i>
                
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-teal-50 rounded-lg flex items-center justify-center text-teal-600 text-xl group-hover:bg-teal-600 group-hover:text-white transition-colors">
                        <i class="fa-solid <?php echo $job['icon'] ?? 'fa-building'; ?>"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 group-hover:text-teal-700 transition-colors"><?php echo $job['title']; ?></h3>
                        <p class="text-sm text-gray-500"><?php echo $job['company']; ?></p>
                    </div>
                </div>

                <div class="mt-4 space-y-2 text-sm text-gray-500">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-location-dot w-4"></i> <?php echo $job['location']; ?>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-briefcase w-4"></i> <?php echo $job['type']; ?>
                        <span class="mx-1">•</span>
                        <i class="fa-regular fa-clock w-4"></i> <?php echo $job['schedule']; ?>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <?php foreach ($job['tags'] as $tag): ?>
                        <?php 
                            // Dynamic color logic based on tag name
                            $tagStyle = "bg-gray-100 text-gray-600";
                            if($tag == "PWD-Friendly") $tagStyle = "bg-teal-50 text-teal-600 border border-teal-100";
                            if($tag == "Fresh Graduate") $tagStyle = "bg-purple-50 text-purple-600 border border-purple-100";
                            if($tag == "Remote") $tagStyle = "bg-blue-50 text-blue-600 border border-blue-100";
                        ?>
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider <?php echo $tagStyle; ?>">
                            <?php echo $tag; ?>
                        </span>
                    <?php endforeach; ?>
                </div>

                <div class="mt-6 pt-4 border-t flex justify-between items-center">
                    <span class="font-bold text-teal-700 text-sm"><?php echo $job['salary']; ?></span>
                    <span class="text-[10px] text-gray-400 font-medium uppercase"><?php echo $job['date']; ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>