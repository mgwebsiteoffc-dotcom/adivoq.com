<?php

$target = __DIR__ . '/storage/app/public';
$link = __DIR__ . '/public/storage';

if (symlink($target, $link)) {
    echo "Storage link created successfully.";
} else {
    echo "Failed to create storage link.";
}