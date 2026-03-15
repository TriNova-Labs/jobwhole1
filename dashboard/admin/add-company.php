<?php 
  include '../../includes/db.php'; 
  include '../../includes/header.php'; 

  $message = "";

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $name = $_POST['name'];
      $industry = $_POST['industry'];
      $location = $_POST['location'];
      $description = $_POST['description'];
      $is_verified = isset($_POST['is_verified']) ? 1 : 0;
      
      /**
       * FIX FOR ERROR #1452: 
       * We must provide a valid 'employer_id' that exists in your 'users' table.
       * For now, we are using '1' (Assuming this is your Admin/First User ID).
       */
      $employer_id = 1; 

      try {
          $sql = "INSERT INTO companies (name, industry, location, description, is_verified, employer_id) 
                  VALUES (?, ?, ?, ?, ?, ?)";
          $stmt = $pdo->prepare($sql);
          $stmt->execute([$name, $industry, $location, $description, $is_verified, $employer_id]);
          
          $message = "Company added successfully!";
      } catch (PDOException $e) {
          // This will help us catch if User ID 1 doesn't exist yet
          $message = "Error: " . $e->getMessage();
      }
  }
?>

<main class="bg-gray-50 min-h-screen pt-24 pb-12 px-8 md:px-24">
    <div class="max-w-2xl mx-auto">
        <a href="manage-companies.php" class="text-teal-600 hover:text-teal-800 text-sm font-bold mb-6 inline-block">← Back to List</a>
        
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Add New Partner</h1>
            <p class="text-gray-500 text-sm mb-8">Enter the details of the ethical employer joining HIRAYA.</p>

            <?php if($message): ?>
                <div class="mb-6 p-4 rounded-lg <?php echo (strpos($message, 'Error') !== false) ? 'bg-red-50 text-red-600' : 'bg-teal-50 text-teal-600'; ?> font-bold text-sm">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Company Name</label>
                    <input type="text" name="name" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Industry</label>
                        <input type="text" name="industry" placeholder="e.g. Technology" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-teal-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Location</label>
                        <input type="text" name="location" placeholder="e.g. Manila" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-teal-500 outline-none transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Short Description</label>
                    <textarea name="description" rows="4" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-teal-500 outline-none transition"></textarea>
                </div>

                <div class="flex items-center gap-3 bg-gray-50 p-4 rounded-xl">
                    <input type="checkbox" name="is_verified" id="is_verified" class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                    <label for="is_verified" class="text-sm font-semibold text-gray-700">Mark as Verified Partner</label>
                </div>

                <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-4 rounded-xl transition shadow-lg shadow-teal-900/10">
                    Save Company Profile
                </button>
            </form>
        </div>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>