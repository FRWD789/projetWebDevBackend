<?php 
 header('Content-Type: application/json; charset=utf-8');

class ProductController {
    private $productModel;
    public function __construct() {
        $database = new Database();
        $db = $database->getConnexion();
        $this->productModel = new Product($db);
    }
    public function getProducts() {
        $products = $this->productModel->getAllProduct();
        if($products){
            http_response_code(200);
            echo  json_encode($products);
        }else{
            http_response_code(500);
            echo json_encode(['message' => ' Internal Server Error']);
        }
        
        // Return products as JSON
       
    }
    public function addProduct() {
        $data = json_decode(file_get_contents("php://input"),true);
       // product_name, product_detail_id, stock, price, category_id, brand_id, product_img
       //$product_description,$product_max_freq,$product_min_freq,$product_impedance

    
        if(!isset($data['product_description'])||!isset($data['product_max_freq'])||!isset($data['product_min_freq']) ||!isset($data['product_impedance'])||!isset($data['product_name'])||!isset($data['stock']) ||!isset($data['category']) ||!isset($data['price'])||!isset($data['brand'])||!isset($data['product_img'])){
            http_response_code(400);
            echo json_encode(['message' => ' bad request']);
            exit;
        }
        
        if($this->productModel->addProduct($data)){
            http_response_code(201);
            echo json_encode(['message' => ' product added sucessfuly :)']);
        }
        else{
            http_response_code(500);
            echo json_encode(['message' => ' Internal Server Error']);
        }
        

       
    }

 
}