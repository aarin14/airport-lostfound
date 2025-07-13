<?php
function hash_description($description) {
    $words = preg_split('/\W+/', strtolower($description));
    $words = array_filter($words);
    sort($words);
    $keywords = implode(' ', $words);
    return hash('sha256', $keywords);
} 