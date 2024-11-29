<?php 
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
            echo json_encode(['message' => ' username already exist ']);
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
        }
        
        elseif($this->userModel->login($data['user_name'],$data['password'])){
            http_response_code(200);
            echo json_encode(['message' => ' user loged sucessfuly :)']);
        } else{
            http_response_code(403);
            echo json_encode(['message' => ' invalid password or username']);
        }
    }
    
 
}