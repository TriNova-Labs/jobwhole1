<?php 
  // 1. Database Connection (If you want to pull resources from a table later)
  include '../../includes/db.php'; 

  // 2. Include Header
  include '../../includes/header.php'; 

  // Static Resource Data (You can move this to a 'resources' table later)
  $resources = [
    [
        'title' => 'PWD Workplace Rights',
        'desc' => 'A comprehensive guide to labor laws and inclusivity standards.',
        'tag' => 'Legal',
        'icon' => 'fa-scale-balanced',
        'color' => 'bg-amber-500'
    ],
    [
        'title' => 'Resume Building for Fresh Grads',
        'desc' => 'How to stand out in the ethical job market without prior experience.',
        'tag' => 'Career',
        'icon' => 'fa-file-invoice',
        'color' => 'bg-blue-500'
    ],
    [
        'title' => 'Remote Work Ergonomics',
        'desc' => 'Setting up an accessible and healthy home office environment.',
        'tag' => 'Wellness',
        'icon' => 'fa-laptop-house',
        'color' => 'bg-teal-500'
    ],
    [
        'title' => 'Inclusive Interview Prep',
        'desc' => 'Tips on discussing accessibility needs with potential employers.',
        'tag' => 'Guide',
        'icon' => 'fa-comments',
        'color' => 'bg-purple-500'
    ]
  ];
?>

<main class="bg-gray-50 min-h-screen pt-32 pb-20">
    <header class="max-w-6xl mx-auto px-6 mb-12">
        <h1 class="text-3xl font-bold text-gray-900">Learning Resources</h1>
        <p class="text-gray-500 mt-2">Tools and guides to help you navigate your career with confidence.</p>
    </header>

    <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-8">
        <?php foreach ($resources as $res): ?>
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-all group flex items-start gap-6 cursor-pointer">
                <div class="<?php echo $res['color']; ?> w-16 h-16 rounded-2xl flex items-center justify-center text-white text-2xl shrink-0 group-hover:rotate-6 transition-transform">
                    <i class="fa-solid <?php echo $res['icon']; ?>"></i>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-teal-600 bg-teal-50 px-2 py-1 rounded mb-2 inline-block">
                        <?php echo $res['tag']; ?>
                    </span>
                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-teal-700 transition-colors">
                        <?php echo $res['title']; ?>
                    </h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        <?php echo $res['desc']; ?>
                    </p>
                    <div class="mt-4 flex items-center gap-2 text-sm font-bold text-teal-600">
                        Read Article <i class="fa-solid fa-arrow-right text-xs"></i>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>