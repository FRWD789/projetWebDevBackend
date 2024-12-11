<?php

require_once 'config/database.php';  
require_once 'models/product.php';   // Product model
require_once 'controllers/productController.php'; // Product controller
require_once 'controllers/userController.php'; // Product controller
require_once 'models/User.php';
require_once 'controllers/orderController.php'; // Product controller
require_once 'models/Order.php';
require_once 'middleware/jwtVerify.php';
require_once 'controllers/refrechTokenController.php';

header('Content-Type: application/json; charset=utf-8');


$productController = new ProductController();
$userController = new userController();
$orderController = new orderController();

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

$uriSegments = explode('/', trim($requestUri, '/'));
$endpoint = isset($_GET) && !empty($_GET) 
? explode('?', $uriSegments[1])[0] 
: $uriSegments[1]; 
$uriSegments2 = isset($uriSegments[2])?  $uriSegments[2] : null;

if ($endpoint === 'products' && !isset($_GET)) {

   

     switch($requestMethod){
         case "GET":
            $productController->getProducts();
             break;
         case "POST":
             $productController->addProduct();
         break;
        case "DELETE":
            $productController->removeProduct();
        break;
         default:
         http_response_code(405);  // Method Not Allowed
         echo json_encode(['message' => 'Method Not Allowed']);
     }

    
 } 



 elseif($endpoint === 'products'&& (isset($_GET))){
    
   
        if(isset($_GET['p_id'])){
            switch($requestMethod){
                case "GET":
                    $productController->getProductById($_GET['p_id']);
                    break;
               case "PUT":
                    $productController->updateProduct($_GET['p_id']);
               break;
               default:
               http_response_code(405);  // Method Not Allowed
                echo json_encode(['message' => 'Method Not Allowed']);

        }}elseif(isset($_GET['p_categ'])){
            switch($requestMethod){
                case "GET":
                    $productController->getProductByCategory($_GET['p_categ']);
                    break;
               default:
               http_response_code(405);  // Method Not Allowed
                echo json_encode(['message' => 'Method Not Allowed']);
        }}else{
            http_response_code(404);  // Method Not Allowed
            echo json_encode(['message' => 'Method Not Allowed']);
        }
      
         
    }
  elseif($endpoint==='sign-up'){
     switch($requestMethod){
         case "POST":
             $userController->signUp();
             break;
         case "PUT":
             $productController->updateProduct($_GET['p_id']);
        break;
         default:
         http_response_code(405);  // Method Not Allowed
        echo json_encode(['message' => 'Method Not Allowed']);
     }
 }elseif($endpoint==='sign-in'){
     switch($requestMethod){
        case "POST":
             $userController->signIn();
             break;
        default:
        http_response_code(405);  // Method Not Allowed
        echo json_encode(['message' => 'Method Not Allowed']);
    }
 }
 elseif($endpoint === 'refreshToken'){
    switch($requestMethod){
        case "GET":
             validateRefreshToken();
            break;
        default:
         http_response_code(405);  // Method Not Allowed
         echo json_encode(['message' => 'Method Not Allowed']);
    }
}
 elseif($endpoint === 'logout'){
     switch($requestMethod){
        case "POST":
           $userController->logout();
            break;
         http_response_code(405);  // Method Not Allowed
         echo json_encode(['message' => 'Method Not Allowed']);
     }
}elseif($endpoint === 'account' && isset($uriSegments2)){
    switch($uriSegments2){
        case'auth':
            switch($requestMethod){
                case'POST':
                $userController->verifyUser();
                break;
                case 'PATCH':
                    $userController->updatePassword();
                    break;
                default:
                http_response_code(405);  // Method Not Allowed
                echo json_encode(['message' => 'Method Not Allowed']);
            }
        break;
        case 'billing':
            switch($requestMethod){
                case'POST':
                   
                    $userController->addAddBillingAdress();
                    break;
                default:
                http_response_code(405);  // Method Not Allowed
                echo json_encode(['message' => 'Method Not Allowed']);
            }
        break;
        default:
        http_response_code(404);  
        echo json_encode(['message' => 'Endpoint Not Found']);
   
    }
            
            

}
elseif($endpoint === 'account' && isset($_GET['u_id'])){

       switch($requestMethod){
             case "GET":
               $userController->getUserInfo($_GET['u_id']);
               break;
             case "PUT":
                 $userController->updateUserSettings($_GET['u_id']);
                 break;
             default:
             http_response_code(405);  // Method Not Allowed
             echo json_encode(['message' => 'Method Not Allowed']);

        }
    
 }elseif($endpoint === 'cart'){
     switch($requestMethod){

         case "POST":
            $orderController->addItemToshoppingCart();
             break;
        case "DELETE":
             $orderController->removeItemToshoppingCart();
            break;
       default:
         http_response_code(405);  // Method Not Allowed
         echo json_encode(['message' => 'Method Not Allowed']);

     }
 }

else {

    http_response_code(404);  
    echo json_encode(['message' => 'Endpoint Not Found']);
}