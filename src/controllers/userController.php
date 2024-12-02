<?php 
require_once __DIR__ . '/../../vendor/autoload.php';
use \Firebase\JWT\JWT;
header('Content-Type: application/json; charset=utf-8');

class userController {
    private $userModel;
    public function __construct() {
        $database = new Database();
        $db = $database->getConnexion();
        $this->userModel = new User($db);
    }


    public function signUp(){
        $data = json_decode(file_get_contents("php://input"),true);
        if(!isset($data['email'])||!isset($data['user_name'])||!isset($data['password'])){
            http_response_code(400);
            echo json_encode(['message' => ' bad request']);
            exit;
        }
        if($this->userModel->existUserName($data['user_name'])){
            http_response_code(409);
            echo json_encode(['message' => ' username exist ']);
            exit;
        }
        
        elseif($this->userModel->addUser($data)){
            http_response_code(201);
            echo json_encode(['message' => ' user added sucessfuly :)']);
        } else{
            http_response_code(500);
            echo json_encode(['message' => ' Internal Server Error']);
        }
    }
    
    public function signIn(){
        $data = json_decode(file_get_contents("php://input"),true);
        if(!isset($data['user_name'])||!isset($data['password'])){
            http_response_code(400);
            echo json_encode(['message' => ' bad request']);
            exit;
        }
        if(!$this->userModel->existUserName($data['user_name'])){
            http_response_code(404);
            echo json_encode(['message' => " username already doesn't exist "]);
            exit;
        }    if(!$this->userModel->existUserName($data['user_name'])){
            http_response_code(404);
            echo json_encode(['message' => " username already doesn't exist "]);
            exit;
        }
        
        elseif($this->userModel->login($data['user_name'],$data['password'])){
            $accessPayload = [
                'user_name' => $data['user_name'],
                'iat' => time(), // Issued at time
                'exp' => time()+ 5*60  // Expiry time (1 hour)
            ];
            $refreshPayload = [
                'user_name' => $data['user_name'],
                'iat' => time(),
                'exp' => time() + 1209600 // Refresh token expiration time (2 weeks)
            ];
    
            $key ='f1546477e47cfa83badd4335f36136e277ee5826b6c2d23b7f04946f44525a52';
            $refKey ='5287a384586fa87c6bd3f7093f56ba0b7e31e1ad1b0c772d9cdffceeaf25b5f6';
            
            try {
                $refreshToken = JWT::encode($refreshPayload, $refKey,'HS256');
                $accessToken = JWT::encode($accessPayload, $key,'HS256');
                setcookie('jwt', $refreshToken, time() + 86400, '/','', true, true); // Secure, HttpOnly cookie
                http_response_code(200);
                echo json_encode([
                    'message' => ' user loged sucessfuly :)',
                    'token' =>  $accessToken
                ]);
                   $refreshPayload = [
            'user_name' => $data['user_name'],
            'iat' => time(),
            'exp' => time() + 1209600 // Refresh token expiration time (2 weeks)
        ];

            } catch (\Throwable $error) {
                error_log('JWT Encoding Error: ' . $error->getMessage()); // cli error
                http_response_code(500);
                echo json_encode(['message' => 'Internal Server Error: ' . $error->getMessage()]);
                exit;
            }
           
           
        } else{
            http_response_code(403);
            echo json_encode(['message' => ' invalid password or username']);
        }
    }
    public function logout() {
        if(!isset($_COOKIE['jwt'])){
            http_response_code(200);
            echo json_encode(['message' => 'User logged out successfully.']);
            exit;
        }
        setcookie('jwt', '', time() - 10**100, '/', '', true, true); // Expire the cookie
        http_response_code(200);
        echo json_encode(['message' => 'User logged out successfully.']);
    }

    public function updateEmail(){
        $data = json_decode(file_get_contents("php://input"),true);


        if(!isset($data['newEmail'])||!isset($data['user_name'])||!isset($data['password'])){
            http_response_code(400);
            echo json_encode(['message' => ' bad request']);
            exit;
        }
        if(!$this->userModel->existUserName($data['user_name'])){
            http_response_code(404);
            echo json_encode(['message' => " username doesn't exist "]);
            exit;
        }
        elseif($this->userModel->updateEmail($data["newEmail"],$data['user_name'],$data['password'])){
            http_response_code(200);
            echo json_encode(['message' => "user email is updated successfuly"]);
            exit;
        }
        else{
            http_response_code(500);
            echo json_encode(['message' => ' Internal Server Error']);
        }
    }
    
 
}