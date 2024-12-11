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

    public function getUserInfo($user_id){
        if(!isset($user_id)){
            http_response_code(400);
            echo json_encode(['message' => ' bad request']);
            exit;
        }
        $data = $this->userModel->getUserInfoById($user_id);
        if($data){
            http_response_code(200);
            echo  json_encode( $data );

        }else{
            http_response_code(500);
            echo json_encode(['message' => ' Internal Server Error']);
        }
    }
    public function signUp(){
        $data = json_decode(file_get_contents("php://input"),true);
        if(!isset($data['email'])||!isset($data['user_name'])||!isset($data['password'])||!isset($data['name'])||!isset($data['last_name'])){
            http_response_code(400);
            echo json_encode(['message' => ' bad request']);
            exit;
        }
        elseif($this->userModel->existUserName($data['user_name'])){
            http_response_code(409);
            echo json_encode(['message' => ' username exist ']);
            exit;
        }
        elseif($this->userModel->existEmail($data['email'])){
            http_response_code(409);
            echo json_encode(['message' => ' email exist ']);
            exit;
        }
        elseif($this->userModel->addUser($data)){
            http_response_code(201);
            echo json_encode(['message' => ' user added sucessfuly :)']);
            exit;
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
            echo json_encode(['message' => " username doesn't exist "]);
            exit;
        }  

        $res =$this->userModel->login($data['user_name'],$data['password']);
        if($res['isValid']){
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
                    'token' =>  $accessToken,
                    'user_name'=>$data['user_name'],
                    'user_id'=>$res['user_id']
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
            echo json_encode(['message' => ' invalid password ']);
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

   


    public function updateUserSettings($user_id){
        $data = json_decode(file_get_contents("php://input"),true);
        $fieldsToUpdate= [];
        if(!isset($user_id)){
            http_response_code(400);
            echo json_encode(['message' => 'bad request ,product_id not provided']);
            exit;
        }
        if(!$this->userModel->getUserInfoById($user_id)){
            http_response_code(404);
            echo json_encode(['message' => ' trying to update non existing user']);
            exit;
        }

        if (isset($data['name'])) {
            $fieldsToUpdate['name'] = $data['name'];
          
        }
        if (isset($data['last_name'])) {
            $fieldsToUpdate['last_name'] = $data['last_name'];
        }
       
        if (isset($data['user_name'])) {

            if($this->userModel->existUserName($data['user_name'])){
                http_response_code(409);
                echo json_encode(['message' => ' userName exist ']);
                exit;
            }

            $fieldsToUpdate['user_name'] = $data['user_name'];
        }
        if(isset($data['email'])){
            if($this->userModel->existEmail($data['email'])){
                http_response_code(409);
                echo json_encode(['message' => ' email exist ']);
                exit;
            }
            $fieldsToUpdate['email'] = $data['email'];
        
        }

        if(count($fieldsToUpdate)<1){
            http_response_code(400);
            echo json_encode(['message' => 'bad requessst empty request ']);
            exit;
        }
        elseif($this->userModel->updateUser($user_id,$fieldsToUpdate)){
            http_response_code(200);
            echo json_encode(['message' => ' user info was updated sucessfuly :)']);
            
        }else{
            http_response_code(500);
            echo json_encode(['message' => ' Internal Server Error ']);
        }
    }

    public function verifyUser(){

        $data = json_decode(file_get_contents("php://input"),true);
        if(!isset($data['user_name'])||!isset($data['password'])){
            http_response_code(400);
            echo json_encode(['message' => ' bad request']);
            exit;
        }
        if(!$this->userModel->existUserName($data['user_name'])){
            http_response_code(404);
            echo json_encode(['message' => " username doesn't exist "]);
            exit;
        }  
        elseif($this->userModel->login($data['user_name'],$data['password'])){
            http_response_code(200);
            echo json_encode([
                'message' => 'user loged verified sucessfuly :)',
              
            ]);
        }else{
            http_response_code(403);
            echo json_encode(['message' => 'wrong password ']);
        }

    }



    public function updatePassword(){
        $data = json_decode(file_get_contents("php://input"),true);
        if(!isset($data['user_name'])||!isset($data['new_password'])){
            http_response_code(400);
            echo json_encode(['message' => 'bad request']);
            exit;
        }
        if(!$this->userModel->existUserName($data['user_name'])){
            http_response_code(404);
            echo json_encode(['message' => " username doesn't exist "]);
            exit;
        }  
        elseif($this->userModel->changePass($data['user_name'],$data['new_password'])){
            http_response_code(200);
            echo json_encode([
                'message' => 'user pass updated  sucessfuly :)',
              
            ]);
        }else{
            http_response_code(500);
            echo json_encode(['message' => ' Internal Server Error ']);
        }
    }
    public function addAddBillingAdress(){
        $data = json_decode(file_get_contents("php://input"),true);
        if (!isset($data['user_name']) ||!isset($data['address_user']) ||!isset($data['address_zip_code']) ||!isset($data['address_region']) ||!isset($data['address_country'])) {
            http_response_code(400);
            echo json_encode(['message' => print_r($data)]);
            echo json_encode(['message' => 'Bad Request']);
            return;
        }
        else if($this->userModel->createBillingAdress($data)){
            http_response_code(201); // Created
            echo json_encode([
                'message' => 'billing address added  sucessfuly :)',
            ]);

        }else{
            http_response_code(500);
            echo json_encode(['message' => ' Internal Server Error ']);
        }
    }
 
}