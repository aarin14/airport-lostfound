<?php
/**
 * Hash Utility Functions for Airport Lost & Found System
 * Provides hash-based search indexing and data integrity features
 */

/**
 * Create a hash from item description for search indexing
 */
function hash_description($description) {
    $words = preg_split('/\W+/', strtolower($description));
    $words = array_filter($words);
    sort($words);
    $keywords = implode(' ', $words);
    return hash('sha256', $keywords);
}

/**
 * Create a hash from multiple item fields for comprehensive search indexing
 */
function hash_item_search($item) {
    $searchableFields = [
        'description' => $item['description'] ?? '',
        'passenger_name' => $item['passenger_name'] ?? '',
        'location' => $item['location'] ?? '',
        'category' => $item['category'] ?? '',
        'flight_number' => $item['flight_number'] ?? '',
        'notes' => $item['notes'] ?? ''
    ];
    
    $searchText = implode(' ', array_filter($searchableFields));
    return hash('sha256', strtolower(trim($searchText)));
}

/**
 * Create a hash for data integrity verification
 */
function hash_item_integrity($item) {
    // Create a hash of all critical fields for integrity checking
    $integrityFields = [
        'type' => $item['type'] ?? '',
        'passenger_name' => $item['passenger_name'] ?? '',
        'passenger_phone' => $item['passenger_phone'] ?? '',
        'description' => $item['description'] ?? '',
        'location' => $item['location'] ?? '',
        'date_of_loss' => $item['date_of_loss'] ?? '',
        'created_at' => $item['created_at'] ?? '',
        'status' => $item['status'] ?? ''
    ];
    
    $integrityString = json_encode($integrityFields, JSON_UNESCAPED_SLASHES);
    return hash('sha256', $integrityString);
}

/**
 * Create a hash map for fast item lookup by search terms
 */
function create_search_hash_map($items) {
    $hashMap = [];
    
    foreach ($items as $index => $item) {
        // Create hash from description
        $descHash = hash_description($item['description'] ?? '');
        $hashMap[$descHash][] = $index;
        
        // Create hash from comprehensive search fields
        $searchHash = hash_item_search($item);
        $hashMap[$searchHash][] = $index;
        
        // Create individual field hashes for specific searches
        $fields = ['passenger_name', 'location', 'category', 'flight_number'];
        foreach ($fields as $field) {
            if (!empty($item[$field])) {
                $fieldHash = hash('sha256', strtolower(trim($item[$field])));
                $hashMap[$fieldHash][] = $index;
            }
        }
    }
    
    return $hashMap;
}

/**
 * Fast search using hash map
 */
function hash_search($items, $searchTerm, $hashMap = null) {
    if (empty($searchTerm)) {
        return $items;
    }
    
    $searchLower = strtolower(trim($searchTerm));
    $searchHash = hash('sha256', $searchLower);
    
    // If hash map is provided, use it for fast lookup
    if ($hashMap && isset($hashMap[$searchHash])) {
        $results = [];
        foreach ($hashMap[$searchHash] as $index) {
            if (isset($items[$index])) {
                $results[] = $items[$index];
            }
        }
        return $results;
    }
    
    // Fallback to traditional search
    return array_filter($items, function($item) use ($searchLower) {
        return strpos(strtolower($item['description'] ?? ''), $searchLower) !== false ||
               strpos(strtolower($item['passenger_name'] ?? ''), $searchLower) !== false ||
               strpos(strtolower($item['location'] ?? ''), $searchLower) !== false ||
               strpos(strtolower($item['category'] ?? ''), $searchLower) !== false ||
               strpos(strtolower($item['flight_number'] ?? ''), $searchLower) !== false;
    });
}

/**
 * Generate hash for file upload integrity
 */
function hash_file_integrity($filePath) {
    if (!file_exists($filePath)) {
        return null;
    }
    return hash_file('sha256', $filePath);
}

/**
 * Create a unique item identifier hash
 */
function create_item_id($item) {
    $uniqueFields = [
        'passenger_name' => $item['passenger_name'] ?? '',
        'passenger_phone' => $item['passenger_phone'] ?? '',
        'description' => $item['description'] ?? '',
        'date_of_loss' => $item['date_of_loss'] ?? '',
        'created_at' => $item['created_at'] ?? ''
    ];
    
    $uniqueString = json_encode($uniqueFields, JSON_UNESCAPED_SLASHES);
    return hash('sha256', $uniqueString);
}

/**
 * Verify item data integrity
 */
function verify_item_integrity($item, $storedHash) {
    $currentHash = hash_item_integrity($item);
    return hash_equals($currentHash, $storedHash);
} 