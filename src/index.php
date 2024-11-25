<?php
// Include required files (adjust paths as necessary)
require_once 'config/database.php';  // Database configuration
require_once 'models/product.php';   // Product model
require_once 'controllers/productController.php'; // Product controller

// Set headers for JSON output
header('Content-Type: application/json; charset=utf-8');

// Initialize a new instance of the ProductController
$productController = new ProductController();

// Simple routing based on the request method and path
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

// Parse the request URI to find the endpoint and extract parameters (if any)
$uriSegments = explode('/', trim($requestUri, '/'));
$endpoint = $uriSegments[1]; // Get the first segment, e.g., "products"
// Check the request method and call the appropriate method in the controller
if ($endpoint === 'products') {
    switch($requestMethod){
        case "GET":
            $productController->getProducts();
            break;
        case "POST":
            $productController->addProduct();
        break;
        case "green":
        echo "Your favorite color is green!";
        break;
        default:
        http_response_code(405);  // Method Not Allowed
        echo json_encode(['message' => 'Method Not Allowed']);
    }
    
} else {
    // Handle unsupported endpoints
    http_response_code(404);  // Not Found
    echo json_encode(['message' => 'Endpoint Not Found']);
}