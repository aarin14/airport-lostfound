<?php
session_start();
if (!isset($_SESSION['username'])) {
  header('Location: login.html');
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Airport Lost & Found - Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
  <!-- Header -->
  <header class="bg-gradient-to-r from-blue-900 to-indigo-900 text-white shadow-lg">
    <div class="container mx-auto px-6 py-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <div class="bg-white bg-opacity-20 p-3 rounded-full">
            <i class="fas fa-plane-departure text-2xl"></i>
          </div>
          <div>
            <h1 class="text-2xl font-bold">Airport Lost & Found</h1>
            <p class="text-blue-200 text-sm">Professional Item Management System</p>
          </div>
        </div>
        <div class="flex items-center space-x-6">
          <div class="text-right">
            <p class="text-blue-200 text-sm">Welcome back,</p>
            <p class="font-semibold"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
          </div>
          <a href="backend/logout.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center space-x-2">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
          </a>
        </div>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="container mx-auto px-6 py-8">
    <!-- Welcome Section -->
    <div class="text-center mb-12">
      <h2 class="text-4xl font-bold text-gray-800 mb-4">Lost & Found Dashboard</h2>
      <p class="text-lg text-gray-600 max-w-2xl mx-auto">
        Manage lost and found items efficiently. Report new lost items, track pending cases, and monitor recovered items.
      </p>
    </div>

    <!-- Quick Stats -->
    <?php
    $itemsFile = __DIR__ . '/data/items.json';
    $items = file_exists($itemsFile) ? json_decode(file_get_contents($itemsFile), true) : [];
    $pendingItems = array_filter($items, function($item) {
        return $item['type'] === 'lost' && ($item['status'] ?? 'pending') === 'pending';
    });
    $foundItems = array_filter($items, function($item) {
        return $item['type'] === 'lost' && ($item['status'] ?? 'pending') === 'found';
    });
    ?>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
      <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm font-medium">Total Items</p>
            <p class="text-3xl font-bold text-gray-800"><?php echo count($items); ?></p>
          </div>
          <div class="bg-blue-100 p-3 rounded-full">
            <i class="fas fa-boxes text-blue-600 text-xl"></i>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm font-medium">Pending Lost</p>
            <p class="text-3xl font-bold text-gray-800"><?php echo count($pendingItems); ?></p>
          </div>
          <div class="bg-yellow-100 p-3 rounded-full">
            <i class="fas fa-search text-yellow-600 text-xl"></i>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm font-medium">Items Found</p>
            <p class="text-3xl font-bold text-gray-800"><?php echo count($foundItems); ?></p>
          </div>
          <div class="bg-green-100 p-3 rounded-full">
            <i class="fas fa-check-circle text-green-600 text-xl"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Action Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Report Lost Item -->
      <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6 text-white">
          <div class="flex items-center space-x-4">
            <div class="bg-white bg-opacity-20 p-3 rounded-full">
              <i class="fas fa-plus text-2xl"></i>
            </div>
            <div>
              <h3 class="text-xl font-bold">Report Lost Item</h3>
              <p class="text-blue-100">Add a new lost item to the system</p>
            </div>
          </div>
        </div>
        <div class="p-6">
          <p class="text-gray-600 mb-4">
            When a passenger reports a lost item, use this form to record all the details including contact information and item description.
          </p>
          <a href="report-lost.php" class="inline-flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition duration-200">
            <i class="fas fa-arrow-right"></i>
            <span>Report Lost Item</span>
          </a>
        </div>
      </div>

      <!-- View Pending Lost Items -->
      <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300">
        <div class="bg-gradient-to-r from-red-600 to-red-700 p-6 text-white">
          <div class="flex items-center space-x-4">
            <div class="bg-white bg-opacity-20 p-3 rounded-full">
              <i class="fas fa-list text-2xl"></i>
            </div>
            <div>
              <h3 class="text-xl font-bold">Pending Lost Items</h3>
              <p class="text-red-100">View and manage items still being searched</p>
            </div>
          </div>
        </div>
        <div class="p-6">
          <p class="text-gray-600 mb-4">
            View all items that are still lost. Search through items, mark them as found when recovered, and manage the search process.
          </p>
          <a href="all-lost-items.php" class="inline-flex items-center space-x-2 bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-semibold transition duration-200">
            <i class="fas fa-arrow-right"></i>
            <span>View Pending Items</span>
          </a>
        </div>
      </div>

      <!-- View Found Items -->
      <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300 lg:col-span-2">
        <div class="bg-gradient-to-r from-green-600 to-green-700 p-6 text-white">
          <div class="flex items-center space-x-4">
            <div class="bg-white bg-opacity-20 p-3 rounded-full">
              <i class="fas fa-check-double text-2xl"></i>
            </div>
            <div>
              <h3 class="text-xl font-bold">Found Items</h3>
              <p class="text-green-100">Track all items that have been recovered</p>
            </div>
          </div>
        </div>
        <div class="p-6">
          <p class="text-gray-600 mb-4">
            View all items that have been found and recovered. This includes complete information about when items were found and by whom.
          </p>
          <a href="all-found-items.php" class="inline-flex items-center space-x-2 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition duration-200">
            <i class="fas fa-arrow-right"></i>
            <span>View Found Items</span>
          </a>
        </div>
      </div>
    </div>


    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-gray-800 text-gray-300 py-6 mt-12">
    <div class="container mx-auto px-6 text-center">
      <p>&copy; 2024 Airport Lost & Found System. All rights reserved.</p>
      <p class="text-sm mt-2">Professional item management for airport operations</p>
    </div>
  </footer>
</body>
</html> 