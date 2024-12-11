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

    public function removeProduct (){
        $data = json_decode(file_get_contents("php://input"),true);
        if(!isset($data["product_id"])){
            http_response_code(400);
            echo json_encode(['message' => 'bad request ,product_id not provided']);
            //exit; // arret l'excution du code 
        }

        elseif(!$this->productModel->getProductById($data["product_id"])){
            http_response_code(404);
            echo json_encode(['message' => ' trying to delete non existing product']);
        }
        elseif($this->productModel->removeProductById($data["product_id"])){
            http_response_code(200);
            echo json_encode(['message' => ' product was sucessfuly removed :)']);
        }
        else{
            http_response_code(500);
            echo json_encode(['message' => ' Internal Server Error']);
        }


    }
    public function getProductByCategory($categ){

        if(!isset($categ)){
            http_response_code(400);
            echo json_encode(['message' => 'bad request ,product_id monquant']);
            exit; // arret l'excution du code 
        }
        $product = $this->productModel->getProductByCategory($categ);
        if($product){
            http_response_code(200);
            echo json_encode($product);
        }else{
            http_response_code(404);
            echo json_encode(['message' => ' product Not Found ']);
            exit;
        }

    }

    public function getProductById($id){

        if(!isset($id)){
            http_response_code(400);
            echo json_encode(['message' => 'bad request ,product_id monquant']);
            exit; // arret l'excution du code 
        }
        $product = $this->productModel->getProductById($id);
        if($product){
            http_response_code(200);
            echo  json_encode($product);
        }else{
            http_response_code(404);
            echo json_encode(['message' => ' product Not Found ']);
        }

    }

    public function updateProduct($id){
        $data = json_decode(file_get_contents("php://input"),true);
        $fieldsToUpdate= [];
        if(!isset($id)){
            http_response_code(400);
            echo json_encode(['message' => 'bad request ,product_id not provided']);
            exit;
        }
        if(!$this->productModel->getProductById($id)){
            http_response_code(404);
            echo json_encode(['message' => ' trying to update non existing product']);
            exit;
        }

        if (isset($data['product_name'])) {
            $fieldsToUpdate['product_name'] = $data['product_name'];
          
        }
        if (isset($data['stock'])) {
            $fieldsToUpdate['stock'] = $data['stock'];
        }
        if (isset($data['price'])) {
            $fieldsToUpdate['price'] = $data['price'];
        }
        

        if(empty($fieldsToUpdate)){
            http_response_code(400);
            echo json_encode(['message' => 'bad request empty request ']);
            exit;
        }
        elseif($this->productModel->updateProduct($id,$fieldsToUpdate)){
            http_response_code(200);
            echo json_encode(['message' => ' product was updated sucessfuly :)']);
        }else{
            http_response_code(500);
            echo json_encode(['message' => ' product Not Found ']);
        }
    }

 
}