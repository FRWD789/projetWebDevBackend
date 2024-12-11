<?php

$requestUri = $_SERVER['REQUEST_URI'];
$uriSegments = explode('/', trim($requestUri, '/'));

if ($requestUri === '/' || $requestUri === '/home') {
    require_once 'inc/home.php';
} elseif ($uriSegments [0] === 'api') {
    require_once '../src/index.php';
} else {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(404);
    echo "404 - Page Not Found";
}