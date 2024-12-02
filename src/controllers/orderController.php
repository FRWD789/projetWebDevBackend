<?php

header('Content-Type: application/json; charset=utf-8');

class orderController {
    private $orderModel;
    public function __construct() {
        $database = new Database();
        $db = $database->getConnexion();
        $this->orderModel = new Order($db);
    }




    public function addItemToshoppingCart(){
        $data = json_decode(file_get_contents("php://input"),true);
        if(!isset($data["user_id"])||!isset($data["product_id"]) || !isset($data["quantity"])||!isset($data["price"]))
        {
            http_response_code(400);
            echo json_encode(['message' => ' bad request']);
            exit;
        }
        if($this->orderModel->addItemToCart($data))
        {
            http_response_code(201);
            echo json_encode(['message' => ' orderitem added sucessfuly :)']);

        }else{
            http_response_code(500);
            echo json_encode(['message' => ' Internal Server Error']);
        }
    }
    public function removeItemToshoppingCart(){
        $data = json_decode(file_get_contents("php://input"),true);
        if(!isset($data["user_id"])|| !isset($data["product_id"])){
            http_response_code(400);
            echo json_encode(['message' => ' bad request']);
            exit;
        }
        if($this->orderModel->deleteFromCart($data["user_id"],$data["product_id"])){
            http_response_code(200);
            echo json_encode(['message' => ' order item removed sucessfuly :)']);

        }
        else{
            http_response_code(500);
            echo json_encode(['message' => ' Internal Server Error']);
        }
    }
 















}