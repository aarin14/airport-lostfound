<?php
header('Content-Type: application/json');
$jsonFile = '../data/items.json';
$items = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];

// Filters
$query = strtolower(trim($_GET['query'] ?? ''));
$date = $_GET['date'] ?? '';
$terminal = strtolower(trim($_GET['terminal'] ?? ''));
$category = strtolower(trim($_GET['category'] ?? ''));

$filtered = array_filter($items, function($item) use ($query, $date, $terminal, $category) {
    $match = true;
    if ($query) {
        $desc = strtolower($item['description'] ?? '');
        $match = $match && strpos($desc, $query) !== false;
    }
    if ($date) {
        $itemDate = $item['date_of_loss'] ?? $item['date_found'] ?? '';
        $match = $match && ($itemDate === $date);
    }
    if ($terminal) {
        $loc = strtolower($item['location'] ?? '');
        $match = $match && strpos($loc, $terminal) !== false;
    }
    if ($category) {
        $desc = strtolower($item['description'] ?? '');
        $match = $match && strpos($desc, $category) !== false;
    }
    return $match;
});
echo json_encode(array_values($filtered)); 