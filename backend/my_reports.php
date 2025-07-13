<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['username'])) {
    echo json_encode(['lost' => [], 'found' => []]);
    exit;
}
$username = $_SESSION['username'];
$jsonFile = __DIR__ . '/../data/items.json';
$items = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];
$lost = [];
$found = [];
foreach ($items as $item) {
    if (($item['username'] ?? null) === $username) {
        if ($item['type'] === 'lost') {
            $lost[] = $item;
        } elseif ($item['type'] === 'found') {
            $found[] = $item;
        }
    }
}
echo json_encode(['lost' => $lost, 'found' => $found]); 