<?php 
require_once __DIR__ . '/../../vendor/autoload.php';
use \Firebase\JWT\JWT;
use Firebase\JWT\Key;
header('Content-Type: application/json; charset=utf-8');


 function verifyToken() {

    $headers = getallheaders();
    if (!isset($headers['Authorization'])){
        http_response_code(400);
        echo json_encode(['message'=>'Authorization header missing.']);
        exit;
    } 

    $token = explode(' ',$headers['Authorization'])[1];

    if($token){


        try {
            $key ='f1546477e47cfa83badd4335f36136e277ee5826b6c2d23b7f04946f44525a52';
            $decoded = JWT::decode($token,new Key($key , 'HS256'));
            return $decoded;
        } catch (\Throwable $th) {
            http_response_code(401);
            echo json_encode(['message' => 'Access denied. Invalid token.']);
            exit;
        }

       
    }else {
        http_response_code(401);
        echo json_encode(['message' => 'No token provided.']);
        exit;
    }


   
}