<?php
// Serve seniorproject.html if no path is provided
$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

if ($uri === '/' || $uri === '') {
    require 'SeniorProject.html';
    return;
}

// Otherwise, let PHP handle the request normally
$path = __DIR__ . $uri;
if (file_exists($path)) {
    return false;
}

require 'index.php'; // fallback
