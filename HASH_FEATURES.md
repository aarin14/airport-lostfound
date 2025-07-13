# Hash-Based Features Documentation

## Overview

The Airport Lost & Found system now includes advanced hash-based functionality for improved search performance, data integrity verification, and secure item identification.

## Hash Functions Implemented

### 1. Hash-Based Search Indexing

**File:** `backend/hash_util.php`

#### Functions:
- `hash_description($description)` - Creates SHA-256 hash from item descriptions
- `hash_item_search($item)` - Creates comprehensive search hash from multiple fields
- `create_search_hash_map($items)` - Builds hash map for O(1) lookup
- `hash_search($items, $searchTerm, $hashMap)` - Fast search using hash indexing

#### Benefits:
- **O(1) lookup time** for exact matches
- **Faster search performance** compared to linear search
- **Indexed search terms** for quick retrieval

### 2. Data Integrity Verification

**File:** `backend/hash_util.php`

#### Functions:
- `hash_item_integrity($item)` - Creates integrity hash from critical fields
- `verify_item_integrity($item, $storedHash)` - Verifies data hasn't been tampered with
- `create_item_id($item)` - Generates unique item identifier

#### Benefits:
- **Data tampering detection** - Identifies if item data has been modified
- **Unique item identification** - Each item gets a unique hash-based ID
- **Audit trail** - Track changes to item data

### 3. File Integrity

**File:** `backend/hash_util.php`

#### Functions:
- `hash_file_integrity($filePath)` - Creates SHA-256 hash of uploaded files

#### Benefits:
- **File integrity verification** - Ensures uploaded images haven't been corrupted
- **Secure file handling** - Hash-based file validation

## New Files Added

### 1. Enhanced Hash Utilities
- **File:** `backend/hash_util.php`
- **Purpose:** Comprehensive hash functions for search, integrity, and identification

### 2. Hash-Based Search API
- **File:** `backend/hash_search.php`
- **Purpose:** REST API for hash-based item searching
- **Features:** Fast search with hash indexing, multiple filter options

### 3. Data Integrity Checker
- **File:** `backend/verify_integrity.php`
- **Purpose:** Web interface to verify integrity of all items
- **Features:** Visual integrity reports, statistics, detailed verification results

### 4. Hash Search Interface
- **File:** `hash_search_interface.php`
- **Purpose:** User-friendly interface for hash-based searching
- **Features:** AJAX search, real-time results, hash information display

## Updated Files

### 1. Item Saving (`backend/save_lost.php`)
- **Added:** Hash generation for new items
- **Features:** 
  - Item ID generation
  - Integrity hash creation
  - Search hash indexing
  - File integrity hashing

### 2. Dashboard (`index.php`)
- **Added:** Hash-based features section
- **Features:** Links to hash search and integrity verification

## How to Use

### 1. Hash-Based Search
1. Navigate to the dashboard
2. Click "Try Hash Search" in the Advanced Hash-Based Features section
3. Enter search terms in the interface
4. View results with hash information

### 2. Data Integrity Verification
1. Navigate to the dashboard
2. Click "Check Integrity" in the Advanced Hash-Based Features section
3. View integrity statistics and detailed results
4. Identify any items with integrity issues

### 3. API Usage
```bash
# Search items using hash-based API
GET /backend/hash_search.php?query=wallet&category=Electronics

# Response includes hash information
{
  "results": [...],
  "total_count": 5,
  "search_performed": true,
  "hash_based_search": true
}
```

## Technical Details

### Hash Algorithms Used
- **SHA-256** for all cryptographic hashing
- **Bcrypt** for password hashing (existing)

### Performance Benefits
- **Search Speed:** O(1) lookup for indexed terms vs O(n) linear search
- **Memory Usage:** Efficient hash map indexing
- **Scalability:** Better performance with large datasets

### Security Features
- **Data Integrity:** Tamper detection using hash verification
- **File Security:** Uploaded file integrity checking
- **Unique Identification:** Hash-based item IDs

## Configuration

### Hash Map Caching (Recommended for Production)
For better performance in production, consider caching the hash map:

```php
// Cache hash map in Redis/Memcached
$hashMap = cache_get('search_hash_map');
if (!$hashMap) {
    $hashMap = create_search_hash_map($items);
    cache_set('search_hash_map', $hashMap, 3600); // Cache for 1 hour
}
```

### Database Integration
The hash system is designed to work with JSON files but can be easily adapted for database storage:

```php
// Example: Store hashes in database
$sql = "INSERT INTO items (data, item_id, integrity_hash, search_hash) 
        VALUES (?, ?, ?, ?)";
```

## Future Enhancements

1. **Hash Map Persistence** - Store hash maps in database for faster loading
2. **Incremental Updates** - Update hash maps when items are added/modified
3. **Hash Collision Detection** - Handle potential hash collisions
4. **Compressed Hashes** - Use shorter hash prefixes for storage efficiency
5. **Hash-based Analytics** - Track search patterns using hash analysis

## Troubleshooting

### Common Issues

1. **Missing Hash Functions**
   - Ensure `backend/hash_util.php` is included in files that use hash functions

2. **Hash Verification Failures**
   - Check if item data has been modified outside the system
   - Verify file permissions for data files

3. **Search Performance Issues**
   - Consider implementing hash map caching
   - Monitor memory usage with large datasets

### Debug Information
Hash information is included in search results for debugging:
- `item_id` - Unique item identifier
- `search_hash` - Search indexing hash
- `integrity_hash` - Data integrity verification hash 