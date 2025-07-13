<?php
/**
 * Hash-Based Data Integrity Verification
 * Verifies the integrity of all items in the system
 */

session_start();
require_once 'hash_util.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: ../login.html');
    exit;
}

// Load all items
$jsonFile = '../data/items.json';
$items = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];

$verificationResults = [];
$totalItems = count($items);
$validItems = 0;
$invalidItems = 0;
$missingHashes = 0;

foreach ($items as $index => $item) {
    $result = [
        'index' => $index,
        'description' => $item['description'] ?? 'No description',
        'passenger_name' => $item['passenger_name'] ?? 'Unknown',
        'status' => 'unknown'
    ];
    
    // Check if item has integrity hash
    if (!isset($item['integrity_hash'])) {
        $result['status'] = 'missing_hash';
        $result['message'] = 'Item missing integrity hash';
        $missingHashes++;
    } else {
        // Verify integrity
        $currentHash = hash_item_integrity($item);
        if (hash_equals($currentHash, $item['integrity_hash'])) {
            $result['status'] = 'valid';
            $result['message'] = 'Item integrity verified';
            $validItems++;
        } else {
            $result['status'] = 'invalid';
            $result['message'] = 'Item integrity check failed';
            $result['stored_hash'] = substr($item['integrity_hash'], 0, 16) . '...';
            $result['current_hash'] = substr($currentHash, 0, 16) . '...';
            $invalidItems++;
        }
    }
    
    $verificationResults[] = $result;
}

// Calculate statistics
$integrityPercentage = $totalItems > 0 ? round(($validItems / $totalItems) * 100, 2) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Health Check - Airport Lost & Found</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <!-- Header -->
    <header class="bg-gradient-to-r from-blue-900 to-indigo-900 text-white shadow-lg">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="../index.php" class="text-blue-200 hover:text-white transition duration-200">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <div class="bg-white bg-opacity-20 p-3 rounded-full">
                        <i class="fas fa-shield-alt text-2xl"></i>
                    </div>
                    <div>
                                    <h1 class="text-2xl font-bold">System Health Check</h1>
            <p class="text-blue-200 text-sm">Verify data integrity and system status</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-blue-200">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Items</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $totalItems; ?></p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-boxes text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
                        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-gray-600 text-sm font-medium">Healthy Items</p>
                  <p class="text-3xl font-bold text-gray-800"><?php echo $validItems; ?></p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                  <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
              </div>
            </div>
            
                        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-gray-600 text-sm font-medium">Issues Found</p>
                  <p class="text-3xl font-bold text-gray-800"><?php echo $invalidItems; ?></p>
                </div>
                <div class="bg-red-100 p-3 rounded-full">
                  <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
              </div>
            </div>
            
                        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-gray-600 text-sm font-medium">System Health</p>
                  <p class="text-3xl font-bold text-gray-800"><?php echo $integrityPercentage; ?>%</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                  <i class="fas fa-heartbeat text-yellow-600 text-xl"></i>
                </div>
              </div>
            </div>
        </div>

        <!-- Verification Results -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6 text-white">
                          <h2 class="text-2xl font-bold">System Health Report</h2>
          <p class="text-blue-100 mt-1">Detailed health check results for all lost & found items</p>
            </div>
            
            <div class="p-6">
                <?php if (empty($verificationResults)): ?>
                    <div class="text-center py-12">
                        <div class="bg-gray-100 rounded-full p-6 inline-block mb-4">
                            <i class="fas fa-inbox text-4xl text-gray-400"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">No items to verify</h3>
                        <p class="text-gray-600">There are no items in the system to verify.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Passenger</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Health Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($verificationResults as $result): ?>
                                    <tr class="hover:bg-gray-50 transition duration-200">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($result['description']); ?></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900"><?php echo htmlspecialchars($result['passenger_name']); ?></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php if ($result['status'] === 'valid'): ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-check mr-1"></i>Valid
                                                </span>
                                            <?php elseif ($result['status'] === 'invalid'): ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <i class="fas fa-times mr-1"></i>Invalid
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i>Missing Hash
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                                                <td class="px-6 py-4">
                          <div class="text-sm text-gray-600"><?php echo htmlspecialchars($result['message']); ?></div>
                          <?php if (isset($result['stored_hash'])): ?>
                            <div class="text-xs text-gray-500 mt-1">
                              <strong>Issue:</strong> Data verification mismatch detected
                            </div>
                          <?php endif; ?>
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

    <!-- Footer -->
    <footer class="bg-gray-800 text-gray-300 py-6 mt-12">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; 2024 Airport Lost & Found System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html> 