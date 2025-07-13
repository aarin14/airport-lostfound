<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: ../login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemIndex = $_POST['item_index'] ?? null;
    
    if ($itemIndex !== null) {
        $jsonFile = '../data/items.json';
        $items = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];
        
        if (isset($items[$itemIndex])) {
            // Mark the item as found
            $items[$itemIndex]['status'] = 'found';
            $items[$itemIndex]['found_date'] = date('c');
            $items[$itemIndex]['found_by'] = $_SESSION['username'];
            
            // Save back to file
            file_put_contents($jsonFile, json_encode($items, JSON_PRETTY_PRINT));
            
            // Redirect back with success message
            header('Location: ../all-lost-items.php?success=found');
            exit;
        }
    }
}

// If something went wrong, redirect back
header('Location: ../all-lost-items.php?error=invalid');
exit; 