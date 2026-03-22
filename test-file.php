<?php
$path = __DIR__ . '/public/storage/media/seed/bg.jpg';
echo "Checking path: $path\n";
if (file_exists($path)) {
    echo "FILE EXISTS!\n";
    echo "Size: " . filesize($path) . " bytes\n";
} else {
    echo "FILE DOES NOT EXIST!\n";
    // Check if storage link itself exists
    $link = __DIR__ . '/public/storage';
    if (is_link($link)) {
        echo "Link exists and points to: " . readlink($link) . "\n";
    } elseif (is_dir($link)) {
        echo "Link is a directory (junction?)\n";
    } else {
        echo "Link DOES NOT EXIST!\n";
    }
}
