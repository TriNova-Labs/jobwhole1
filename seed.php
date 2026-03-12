<?php
include 'includes/db.php';

// 1. Insert Categories
$categories = [
    ['Remote Jobs', '🌐', 'bg-blue-500'],
    ['Internships', '🎓', 'bg-purple-500'],
    ['PWD-Friendly', '♿', 'bg-teal-500']
];

$stmt = $pdo->prepare("INSERT INTO categories (name, icon, color_class) VALUES (?, ?, ?)");
foreach ($categories as $cat) {
    $stmt->execute($cat);
}

// 2. Insert a Sample Company (Assumes a user with ID 1 exists)
// Note: You must register a user first or manually add one to the 'users' table
echo "Categories seeded successfully!";
?>