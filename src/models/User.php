<?php 


class User{
    private $conn;
    const ADD_USER = 'INSERT INTO `client` (user_name,email,password,last_name,name) VALUES(:user_name,:email,:password,:last_name,:name)';
    const EXIST_USER_NAME = "SELECT user_name FROM client WHERE user_name=:user_name ";
    const GET_USER_INFO = "SELECT user_name,user_id,password FROM client WHERE user_name=:user_name ";
    const GET_USER_INFO_BY_ID = "SELECT user_name,name,last_name,email FROM client WHERE user_id=:user_id ";
    const UPDATE_USER_EMAIL = "UPDATE `client` SET email= :email WHERE user_name = :user_name";
    const EXIST_EMAIL =  "SELECT email FROM client WHERE email=:email ";
    const ADD_BILING_ADDRESS = "INSERT INTO  `address` (address_user,address_zip_code,address_region,address_country)VALUES(:address_user,:address_zip_code,:address_region,:address_country) ";
    public function __construct(PDO $bd)
    {
        $this->conn = $bd;
        
    }
    public function existEmail(string $email){
        
        try {
            $stmt = $this->conn->prepare(self::EXIST_EMAIL);
            $stmt->execute( [
                ":email"=>$email
            ]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ;
        } catch (PDOException $error) {
            http_response_code(500);
            echo json_encode(['message' => 'Internal Server Error: ' . $error->getMessage()]);
            exit;
        }


    }

    public function getUserInfoById(string $user_id){
        
        try {
            $stmt = $this->conn->prepare(self::GET_USER_INFO_BY_ID );
            $stmt->execute( [
                ":user_id"=>$user_id,
         
            ]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ;
        } catch (PDOException $error) {
            http_response_code(500);
            echo json_encode(['message' => 'Internal Server Error: ' . $error->getMessage()]);
            exit;
        }


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
                ":password"=>password_hash($userInfo['password'], PASSWORD_BCRYPT,['cost' => 15]),// recomended settings
                ":email"=>$userInfo['email'],
                ":last_name"=>$userInfo['last_name'],
                ":name"=>$userInfo['name']
            ]);
            return $stmt;
        } catch (PDOException $error) {
            http_response_code(500);
            echo json_encode(['message' => 'Internal Server Error: ' . $error->getMessage()]);
            exit;
        }

    }


    public function login(string $userName,string $password):array{
        try {
            $stmt = $this->conn->prepare(self::GET_USER_INFO );
            $stmt->execute( [
                ":user_name"=>$userName
            ]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result && password_verify($password, $result['password'])) {
                return ['isValid' => true, 'user_id' => $result['user_id']];
            }
            return ['isValid' => false, 'user_id' => null];

        } catch (PDOException $error) {
            http_response_code(500);
            echo json_encode(['message' => 'Internal Server Error: ' . $error->getMessage()]);
            exit;
        }

    }


    public function updateEmail(string $newEmail,string $userName,string $password){
        if($this->login($userName,$password)){
            try{
                $stmt = $this->conn->prepare(self::UPDATE_USER_EMAIL );
                return $stmt->execute( [
                    ":user_name"=>$userName,
                    ":email"=>$newEmail
                ]);
            }catch(PDOException $error){
                http_response_code(500);
                echo json_encode(['message' => 'Internal Server Error: ' . $error->getMessage()]);
                exit;
            }
        }

        
    }


    public function updateUser($user_id,array $newData){
        $setClause = [];
        $values = [];
        foreach($newData as $key => $value){
            
            $param = "$key =:$key";
            array_push($setClause,$param);
            $values[":$key"] = $value;

        }
        $setStaments = implode(',',$setClause);

        $query = "UPDATE client SET  $setStaments WHERE user_id=:user_id";
        $values["user_id"] = $user_id;
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute( $values);
            return $stmt;
        } catch (PDOException $error) {
            http_response_code(500);
            echo json_encode(['message' => 'Internal Server Error: ' . $error->getMessage()]);
            exit;
        }
       
    }


    public function createBillingAdress(array $data){
        try {

            $stmt = $this->conn->prepare(self::ADD_BILING_ADDRESS);
            $stmt->execute([
                ':address_user' => $data['address_user'],
                ':address_zip_code' => $data['address_zip_code'],
                ':address_region' => $data['address_region'],
                ':address_country' => $data['address_country']
            ]);
           
            $lastId = $this->conn->lastInsertId();
            $stmt = $this->conn->prepare("UPDATE client SET address_user_id = :id WHERE user_name = :user_name");
            $result = $stmt->execute([
                ':id' =>  $lastId,
                ':user_name' => $data['user_name']
               
            ]);
            return   $result;


            
        } catch (PDOException $error) {
            http_response_code(500);
            echo json_encode(['message' => 'Internal Server Error: ' . $error->getMessage()]);
            exit;
        }
    
    }

    public function changePass($user_name,$new_password){

        $query = "UPDATE client SET  password=:new_password WHERE user_name=:user_name";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute( [
                ':user_name'=>$user_name,
                ':new_password'=>password_hash($new_password, PASSWORD_BCRYPT,['cost' => 15])
            ]);
            return $stmt;
        } catch (PDOException $error) {
            http_response_code(500);
            echo json_encode(['message' => 'Internal Server Error: ' . $error->getMessage()]);
            exit;
        }

    }















    















}