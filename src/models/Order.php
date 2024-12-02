<?php 



class Order {
    private $conn ;

    const CREATE_ORDER = "INSERT INTO orders (user_id,total) VALUES (:user_id,null) ";
    const GET_ACTIVE_ORDER = "SELECT * FROM orders WHERE user_id = :user_id AND total IS NULL";
    const ADD_ORDER_ITEM = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)";
    const DELETE_ORDER_ITEM = "DELETE FROM order_items WHERE order_id = :order_id  AND product_id = :product_id";


    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function getActiveOrderId($userId){
       
        try{
            $stmt = $this->conn->prepare(self::GET_ACTIVE_ORDER);
            $stmt->execute([
                ":user_id"=>$userId,
               
            ]);
            
            $order = $stmt->fetch();
      
            if ($order) {
                return $order['order_id'];
            } else {
                $stmt = $this->conn->prepare(self::CREATE_ORDER);
                $stmt->execute([
                    ":user_id"=>$userId,
                   
                ]);
                return $this->conn->lastInsertId();
            }
        }catch(PDOException $error){
            http_response_code(500);
            echo json_encode(['Internal Server Error11: ' . $error->getMessage()]);
            exit;
        }
      

    }


    public function addItemToCart(array $data) {
        $orderId = $this->getActiveOrderId($data["user_id"]);
        json_encode($orderId );
        try {
            $stmt = $this->conn->prepare("SELECT * FROM order_items WHERE order_id =:order_id AND product_id = :product_id");
            $stmt->execute([
                ":order_id"=>$orderId, 
                ":product_id" =>$data['product_id']
            ]);
            $orderItem = $stmt->fetch();
        
            if ($orderItem) {
                // Update quantity if the product already exists
                $stmt = $this->conn->prepare("UPDATE order_items SET quantity = quantity + 1 WHERE order_id = :order_id AND product_id = :product_id");
                return $stmt->execute([
                    ":order_id"=>$orderId, 
                    ":product_id" =>$data['product_id']
                ]);
            } else {
                // Insert new item if it doesn't exist
                $stmt = $this->conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:order_id,:product_id,:quantity,:price)");
                return $stmt->execute([
                    ":order_id"=>$orderId, 
                    ":product_id" =>$data['product_id'],
                    ":quantity"=>$data['quantity'],
                    ":price"=>$data['price']
                ]);
            }
            
        } catch(PDOException $error){
            http_response_code(500);
            echo json_encode(['Internal Server Error: ' . $error->getMessage()]);
            exit;
        }

    }
    function deleteFromCart($userId, $productId) {



        try {
            $orderId = $this->getActiveOrderId($userId);
            $stmt = $this->conn->prepare(self::DELETE_ORDER_ITEM);
            return $stmt->execute([
                    ":order_id"=>$orderId,
                    ":product_id"=> $productId
                ]);
        } catch(PDOException $error){
            http_response_code(500);
            echo json_encode(['Internal Server Error: ' . $error->getMessage()]);
            exit;
        }
    
     
    }



}