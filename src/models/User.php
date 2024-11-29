<?php 


class User{
    private $conn;
    const ADD_USER = 'INSERT INTO `client` (user_name,email,password) VALUES(:user_name,:email,:password)';
    const EXIST_USER_NAME = "SELECT user_name FROM client WHERE user_name=:user_name ";
    const GET_USER_INFO = "SELECT user_name,user_id,password FROM client WHERE user_name=:user_name ";
    public function __construct(PDO $bd)
    {
        $this->conn = $bd;
        
    }

    public function existUserName(string $userName){
        
        try {
            $stmt = $this->conn->prepare(self::EXIST_USER_NAME );
            $stmt->execute( [
                ":user_name"=>$userName
            ]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ;
        } catch (PDOException $error) {
            http_response_code(500);
            echo json_encode(['message' => 'Internal Server Error: ' . $error->getMessage()]);
            exit;
        }


    }

    public function addUser($userInfo){
        
  
        try {
            $stmt = $this->conn->prepare(self::ADD_USER);
            $stmt->execute( [
                ":user_name"=>$userInfo['user_name'],
                ":password"=>password_hash($userInfo['password'], PASSWORD_BCRYPT,['cost' => 15]),
                ":email"=>$userInfo['email']
            ]);
            return $stmt;
        } catch (PDOException $error) {
            http_response_code(500);
            echo json_encode(['message' => 'Internal Server Error: ' . $error->getMessage()]);
            exit;
        }

    }


    public function login(string $userName,string $password):bool{
        try {
            $stmt = $this->conn->prepare(self::GET_USER_INFO );
            $stmt->execute( [
                ":user_name"=>$userName
            ]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return  password_verify($password,$result["password"]);

        } catch (PDOException $error) {
            http_response_code(500);
            echo json_encode(['message' => 'Internal Server Error: ' . $error->getMessage()]);
            exit;
        }

    }















    















}