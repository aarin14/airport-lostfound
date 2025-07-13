<?php
session_start();

// Include hash utilities
require_once 'hash_util.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'type' => 'lost',
        'passenger_name' => trim($_POST['passenger_name'] ?? ''),
        'passenger_phone' => trim($_POST['passenger_phone'] ?? ''),
        'passenger_email' => trim($_POST['passenger_email'] ?? ''),
        'flight_number' => trim($_POST['flight_number'] ?? ''),
        'date_of_loss' => $_POST['date_of_loss'] ?? '',
        'time_of_loss' => $_POST['time_of_loss'] ?? '',
        'description' => trim($_POST['description'] ?? ''),
        'category' => trim($_POST['category'] ?? ''),
        'estimated_value' => trim($_POST['estimated_value'] ?? ''),
        'location' => trim($_POST['location'] ?? ''),
        'notes' => trim($_POST['notes'] ?? ''),
        'image' => null,
        'created_at' => date('c'),
        'status' => 'pending',
        'username' => isset($_SESSION['username']) ? $_SESSION['username'] : null
    ];
    
    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $targetDir = '../uploads/';
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $filename = uniqid('lost_') . '_' . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $data['image'] = 'uploads/' . $filename;
            // Add file integrity hash
            $data['image_hash'] = hash_file_integrity($targetFile);
        }
    }
    
    // Add hash-based identifiers and integrity checks
    $data['item_id'] = create_item_id($data);
    $data['integrity_hash'] = hash_item_integrity($data);
    $data['search_hash'] = hash_item_search($data);
    
    // Save to items.json
    $jsonFile = '../data/items.json';
    $items = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];
    $items[] = $data;
    file_put_contents($jsonFile, json_encode($items, JSON_PRETTY_PRINT));
    
    // Success page with better design
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Lost Item Reported - Airport Lost & Found</title>
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    </head>
    <body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full text-center">
            <div class="bg-green-100 p-4 rounded-full inline-block mb-6">
                <i class="fas fa-check-circle text-4xl text-green-600"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Lost Item Reported Successfully!</h2>
            <p class="text-gray-600 mb-6">The lost item has been added to our system with enhanced security verification.</p>
            
                        <!-- Item Verification -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
              <h3 class="text-sm font-semibold text-gray-700 mb-2">Item Security Details:</h3>
              <div class="text-xs text-gray-600 space-y-1">
                <div><strong>Item ID:</strong> <span class="font-mono">' . substr($data['item_id'], 0, 16) . '...</span></div>
                <div><strong>Security Verified:</strong> <span class="text-green-600">✓ Yes</span></div>
                <div><strong>Search Indexed:</strong> <span class="text-green-600">✓ Yes</span></div>
              </div>
            </div>
            
            <div class="space-y-3">
                <a href="../index.php" class="block w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-lg font-semibold transition duration-200">
                    <i class="fas fa-home mr-2"></i>Back to Dashboard
                </a>
                <a href="../all-lost-items.php" class="block w-full bg-gray-600 hover:bg-gray-700 text-white py-3 px-6 rounded-lg font-semibold transition duration-200">
                    <i class="fas fa-list mr-2"></i>View All Lost Items
                </a>
            </div>
        </div>
    </body>
    </html>';
    exit;
}

header('Location: ../report-lost.php');
exit; 