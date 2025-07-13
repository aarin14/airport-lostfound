<?php
/**
 * Hash-Based Search System for Airport Lost & Found
 * Provides fast search functionality using hash indexing
 */

require_once 'hash_util.php';

header('Content-Type: application/json');

// Load items
$jsonFile = '../data/items.json';
$items = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];

// Get search parameters
$query = strtolower(trim($_GET['query'] ?? ''));
$date = $_GET['date'] ?? '';
$terminal = strtolower(trim($_GET['terminal'] ?? ''));
$category = strtolower(trim($_GET['category'] ?? ''));
$status = $_GET['status'] ?? '';

// Create hash map for fast search (cache this in production)
$hashMap = create_search_hash_map($items);

// Perform hash-based search
$filtered = hash_search($items, $query, $hashMap);

// Apply additional filters
if ($date || $terminal || $category || $status) {
    $filtered = array_filter($filtered, function($item) use ($date, $terminal, $category, $status) {
        $match = true;
        
        if ($date) {
            $itemDate = $item['date_of_loss'] ?? $item['date_found'] ?? '';
            $match = $match && ($itemDate === $date);
        }
        
        if ($terminal) {
            $loc = strtolower($item['location'] ?? '');
            $match = $match && strpos($loc, $terminal) !== false;
        }
        
        if ($category) {
            $itemCategory = strtolower($item['category'] ?? '');
            $match = $match && strpos($itemCategory, $category) !== false;
        }
        
        if ($status) {
            $itemStatus = $item['status'] ?? 'pending';
            $match = $match && ($itemStatus === $status);
        }
        
        return $match;
    });
}

// Add hash information to results for debugging/verification
$results = [];
foreach ($filtered as $item) {
    $itemWithHash = $item;
    $itemWithHash['search_hash'] = hash_item_search($item);
    $itemWithHash['integrity_hash'] = hash_item_integrity($item);
    $itemWithHash['item_id'] = create_item_id($item);
    $results[] = $itemWithHash;
}

echo json_encode([
    'results' => $results,
    'total_count' => count($results),
    'search_performed' => !empty($query),
    'hash_based_search' => true
]); 