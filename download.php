<?php

$baseDir = realpath(__DIR__ . '/repository');

$file = $_GET['file'] ?? '';

$target = realpath($baseDir . '/' . $file);

if (!$target || strpos($target, $baseDir) !== 0) {
    die('Invalid file');
}

if (!file_exists($target)) {
    die('File not found');
}

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($target) . '"');
header('Content-Length: ' . filesize($target));

readfile($target);
exit;
