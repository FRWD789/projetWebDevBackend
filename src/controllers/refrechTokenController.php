<?php 
require_once __DIR__ . '/../../vendor/autoload.php';
use \Firebase\JWT\JWT;
use Firebase\JWT\Key;
header('Content-Type: application/json; charset=utf-8');



function validateRefreshToken(){

    if (!isset($_COOKIE['jwt'])) {
        http_response_code(400);
        echo json_encode(['message' => 'No refresh token found.']);
        exit;
    }

    $token = $_COOKIE['jwt'];
    try {
        $key ='f1546477e47cfa83badd4335f36136e277ee5826b6c2d23b7f04946f44525a52';

        $refKey ='5287a384586fa87c6bd3f7093f56ba0b7e31e1ad1b0c772d9cdffceeaf25b5f6';
        $decoded = JWT::decode($token,new Key($refKey,'HS256'));

        if ($decoded->exp < time()) {
            http_response_code(401);
            echo json_encode(['message' => 'Refresh token has expired. Please log in again.']);
            exit;
        }
    
        $accessPayload = [
            'user_name' => $decoded->user_name,
            'iat' => time(),
            'exp' => time() + 3600 // New access token expiration time (1 hour)
        ];
        $accessToken = JWT::encode($accessPayload,  $key,'HS256');

        http_response_code(200);
        echo json_encode([
            'message' => 'Access token refreshed successfully.',
            'access_token' => $accessToken
        ]);
    }catch(Exception $error){
            http_response_code(401);
            echo json_encode(['message' => 'Token is expired please login again']);
            exit;
        }
}