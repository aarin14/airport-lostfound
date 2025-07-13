<?php
session_start();
if (!isset($_SESSION['username'])) {
  header('Location: login.html');
  exit;
}

// Load all items
$itemsFile = __DIR__ . '/data/items.json';
$items = file_exists($itemsFile) ? json_decode(file_get_contents($itemsFile), true) : [];

// Filter only lost items with pending status
$lostItems = array_filter($items, function($item) {
    return $item['type'] === 'lost' && ($item['status'] ?? 'pending') === 'pending';
});

// Handle search
$searchTerm = trim($_GET['search'] ?? '');
if (!empty($searchTerm)) {
    $lostItems = array_filter($lostItems, function($item) use ($searchTerm) {
        $searchLower = strtolower($searchTerm);
        return strpos(strtolower($item['description']), $searchLower) !== false ||
               strpos(strtolower($item['location']), $searchLower) !== false ||
               strpos(strtolower($item['passenger_name']), $searchLower) !== false ||
               strpos(strtolower($item['date_of_loss']), $searchLower) !== false ||
               strpos(strtolower($item['category']), $searchLower) !== false;
    });
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pending Lost Items - Airport Lost & Found</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
  <!-- Header -->
  <header class="bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg">
    <div class="container mx-auto px-6 py-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <a href="index.php" class="text-red-200 hover:text-white transition duration-200">
            <i class="fas fa-arrow-left text-xl"></i>
          </a>
          <div class="bg-white bg-opacity-20 p-3 rounded-full">
            <i class="fas fa-search text-2xl"></i>
          </div>
          <div>
            <h1 class="text-2xl font-bold">Pending Lost Items</h1>
            <p class="text-red-200 text-sm">Items still being searched</p>
          </div>
        </div>
        <div class="flex items-center space-x-4">
          <a href="all-found-items.php" class="text-red-200 hover:text-white transition duration-200">
            <i class="fas fa-check-circle mr-2"></i>View Found Items
          </a>
          <a href="backend/logout.php" class="bg-red-800 hover:bg-red-900 text-white px-4 py-2 rounded-lg transition duration-200">
            <i class="fas fa-sign-out-alt mr-2"></i>Logout
          </a>
        </div>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="container mx-auto py-8 px-6">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
      <!-- Success/Error Messages -->
      <?php if (isset($_GET['success']) && $_GET['success'] === 'found'): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4">
          <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span>Item has been marked as found successfully!</span>
          </div>
        </div>
      <?php endif; ?>
      
      <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid'): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
          <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <span>Error: Could not mark item as found. Please try again.</span>
          </div>
        </div>
      <?php endif; ?>

      <!-- Header Section -->
      <div class="bg-gradient-to-r from-red-600 to-red-700 p-6 text-white">
        <div class="flex justify-between items-center">
          <div>
            <h2 class="text-2xl font-bold">Pending Lost Items</h2>
            <p class="text-red-100 mt-1"><?php echo count($lostItems); ?> items currently being searched</p>
          </div>
          <a href="report-lost.php" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-6 py-3 rounded-lg font-semibold transition duration-200 flex items-center space-x-2">
            <i class="fas fa-plus"></i>
            <span>Add New Item</span>
          </a>
        </div>
      </div>

      <!-- Search Bar -->
      <div class="p-6 border-b border-gray-200">
        <form method="GET" class="flex gap-3">
          <div class="flex-1 relative">
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            <input type="text" 
                   name="search" 
                   value="<?php echo htmlspecialchars($searchTerm); ?>"
                   placeholder="Search by description, passenger name, location, category..." 
                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition duration-200">
          </div>
          <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-semibold transition duration-200 flex items-center space-x-2">
            <i class="fas fa-search"></i>
            <span>Search</span>
          </button>
          <?php if (!empty($searchTerm)): ?>
            <a href="all-lost-items.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold transition duration-200 flex items-center space-x-2">
              <i class="fas fa-times"></i>
              <span>Clear</span>
            </a>
          <?php endif; ?>
        </form>
      </div>

      <!-- Content -->
      <div class="p-6">
        <?php if (empty($lostItems)): ?>
          <div class="text-center py-12">
            <?php if (!empty($searchTerm)): ?>
              <div class="bg-gray-100 rounded-full p-6 inline-block mb-4">
                <i class="fas fa-search text-4xl text-gray-400"></i>
              </div>
              <h3 class="text-xl font-semibold text-gray-800 mb-2">No items found</h3>
              <p class="text-gray-600 mb-4">No pending lost items match your search for "<?php echo htmlspecialchars($searchTerm); ?>"</p>
              <a href="all-lost-items.php" class="text-red-600 hover:underline">View all pending items</a>
            <?php else: ?>
              <div class="bg-green-100 rounded-full p-6 inline-block mb-4">
                <i class="fas fa-check-circle text-4xl text-green-600"></i>
              </div>
              <h3 class="text-xl font-semibold text-gray-800 mb-2">No pending items</h3>
              <p class="text-gray-600 mb-4">All reported items have been found or there are no lost items yet.</p>
              <div class="space-x-4">
                <a href="report-lost.php" class="text-red-600 hover:underline">Report a new lost item</a>
                <a href="all-found-items.php" class="text-green-600 hover:underline">View Found Items</a>
              </div>
            <?php endif; ?>
          </div>
        <?php else: ?>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Details</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Passenger Info</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location & Date</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category & Value</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($lostItems as $item): ?>
                  <tr class="hover:bg-gray-50 transition duration-200">
                    <td class="px-6 py-4">
                      <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($item['description']); ?></div>
                      <?php if (!empty($item['notes'])): ?>
                        <div class="text-sm text-gray-500 mt-1">
                          <i class="fas fa-sticky-note mr-1"></i>
                          <?php echo htmlspecialchars(substr($item['notes'], 0, 50)) . (strlen($item['notes']) > 50 ? '...' : ''); ?>
                        </div>
                      <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                      <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($item['passenger_name']); ?></div>
                      <div class="text-sm text-gray-500"><?php echo htmlspecialchars($item['passenger_phone']); ?></div>
                      <?php if (!empty($item['passenger_email'])): ?>
                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($item['passenger_email']); ?></div>
                      <?php endif; ?>
                      <?php if (!empty($item['flight_number'])): ?>
                        <div class="text-xs text-blue-600 bg-blue-100 px-2 py-1 rounded mt-1 inline-block">
                          <i class="fas fa-plane mr-1"></i><?php echo htmlspecialchars($item['flight_number']); ?>
                        </div>
                      <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                      <div class="text-sm text-gray-900"><?php echo htmlspecialchars($item['location']); ?></div>
                      <div class="text-sm text-gray-500"><?php echo htmlspecialchars($item['date_of_loss']); ?></div>
                      <?php if (!empty($item['time_of_loss'])): ?>
                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($item['time_of_loss']); ?></div>
                      <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                      <?php if (!empty($item['category'])): ?>
                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($item['category']); ?></div>
                      <?php endif; ?>
                      <?php if (!empty($item['estimated_value'])): ?>
                        <div class="text-sm text-green-600 font-medium"><?php echo htmlspecialchars($item['estimated_value']); ?></div>
                      <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                      <?php if (!empty($item['image'])): ?>
                        <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                             alt="Item image" 
                             class="h-12 w-12 object-cover rounded-lg cursor-pointer shadow-sm"
                             onclick="openImageModal('<?php echo htmlspecialchars($item['image']); ?>')">
                      <?php else: ?>
                        <div class="h-12 w-12 bg-gray-100 rounded-lg flex items-center justify-center">
                          <i class="fas fa-image text-gray-400"></i>
                        </div>
                      <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                      <form action="backend/mark_found.php" method="POST" style="display:inline">
                        <input type="hidden" name="item_index" value="<?php echo array_search($item, $items); ?>">
                        <button type="submit" 
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200 flex items-center space-x-2"
                                onclick="return confirm('Mark this item as found?')">
                          <i class="fas fa-check"></i>
                          <span>Mark Found</span>
                        </button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </main>

  <!-- Image Modal -->
  <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white p-6 rounded-xl max-w-2xl max-h-2xl">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Item Image</h3>
        <button onclick="closeImageModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
      </div>
      <img id="modalImage" src="" alt="Full size image" class="max-w-full max-h-96 object-contain rounded-lg">
    </div>
  </div>

  <!-- Footer -->
  <footer class="bg-gray-800 text-gray-300 py-6 mt-12">
    <div class="container mx-auto px-6 text-center">
      <p>&copy; 2024 Airport Lost & Found System. All rights reserved.</p>
    </div>
  </footer>

  <script>
    function openImageModal(imageSrc) {
      document.getElementById('modalImage').src = imageSrc;
      document.getElementById('imageModal').classList.remove('hidden');
      document.getElementById('imageModal').classList.add('flex');
    }

    function closeImageModal() {
      document.getElementById('imageModal').classList.add('hidden');
      document.getElementById('imageModal').classList.remove('flex');
    }

    // Close modal when clicking outside
    document.getElementById('imageModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeImageModal();
      }
    });
  </script>
</body>
</html> 