<?php 
  // 1. Database Connection
  include 'includes/db.php'; 

  $css_path = "assets/css/bg.css"; 

  // 2. Fetch Real Categories from Database
  try {
      $stmt = $pdo->query("SELECT * FROM categories");
      $categories = $stmt->fetchAll();
  } catch (PDOException $e) {
      // Fallback to empty array if table is empty
      $categories = [];
  }

  // 3. Stats (Usually these are dynamic counts)
  // For now, we can keep these as an array or count them from tables later
  $stats = [
    ['label' => 'Active Jobs', 'count' => '2,500+', 'icon' => '💼'],
    ['label' => 'Verified Companies', 'count' => '350+', 'icon' => '🏢'],
    ['label' => 'Job Seekers', 'count' => '15,000+', 'icon' => '👥'],
    ['label' => 'Successful Hires', 'count' => '4,200+', 'icon' => '🏆'],
  ];
?>

<?php include 'includes/header.php'; ?>

<main>
    <section class="hero-gradient min-h-screen flex flex-col justify-center px-8 md:px-24 pt-32">
        <div class="max-w-3xl">
            <div class="inline-block bg-teal-900/50 border border-teal-700 text-teal-300 text-xs px-3 py-1 rounded-full mb-6">
                ● Supporting UN SDG 8 — Decent Work for All
            </div>
            <h1 class="text-5xl md:text-7xl font-bold text-white mb-6">
                Where Inclusive<br>Careers <span class="text-teal-400">Begin</span>
            </h1>
            <p class="text-gray-300 text-lg mb-8 max-w-xl">
                Discover opportunities with ethical employers who champion accessibility, diversity, and meaningful work for everyone.
            </p>

            <form action="jobs.php" method="GET" class="flex flex-col md:flex-row gap-4 bg-white/10 p-2 rounded-xl backdrop-blur-sm border border-white/20">
                <input type="text" name="search" placeholder="Job title, keyword, or company" class="bg-transparent text-white px-4 py-3 flex-grow outline-none border-r border-white/10 placeholder-gray-400">
                <input type="text" name="location" placeholder="Location" class="bg-transparent text-white px-4 py-3 flex-grow outline-none placeholder-gray-400">
                <button type="submit" class="bg-teal-500 hover:bg-teal-400 text-black font-bold px-8 py-3 rounded-lg flex items-center justify-center gap-2 transition">
                    Search <span>→</span>
                </button>
            </form>
            
            <div class="flex flex-wrap gap-2 mt-4">
                <?php foreach(['PWD-Friendly', 'Fresh Graduate', 'Remote', 'Internship'] as $tag): ?>
                    <a href="jobs.php?tag=<?php echo $tag; ?>" class="bg-white/5 border border-white/10 text-white text-xs px-3 py-1 rounded hover:bg-white/20 cursor-pointer transition"><?php echo $tag; ?></a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-16 translate-y-12">
            <?php foreach($stats as $stat): ?>
                <div class="bg-white p-6 rounded-xl shadow-xl hover:transform hover:-translate-y-1 transition duration-300">
                    <div class="text-2xl mb-2"><?php echo $stat['icon']; ?></div>
                    <div class="text-2xl font-bold text-gray-800"><?php echo $stat['count']; ?></div>
                    <div class="text-xs text-gray-500 uppercase tracking-wider"><?php echo $stat['label']; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="py-32 px-8 md:px-24 bg-gray-50">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Explore Opportunities</h2>
            <p class="text-gray-600">Find the right career path across various categories tailored to your needs and interests.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php foreach($categories as $cat): ?>
                <a href="jobs.php?category=<?php echo $cat['id']; ?>" class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm hover:shadow-lg transition-all group cursor-pointer">
                    <div class="<?php echo $cat['color_class']; ?> w-12 h-12 rounded-lg flex items-center justify-center text-white text-xl mb-4 group-hover:scale-110 transition-transform">
                        <?php echo $cat['icon']; ?>
                    </div>
                    <h3 class="font-bold text-gray-800"><?php echo $cat['name']; ?></h3>
                    <p class="text-sm text-gray-500">View Positions</p>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>