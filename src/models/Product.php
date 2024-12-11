<?php 
class Product {
    private $conn;
    const GET_ALL_PRODUCTS = "SELECT * FROM product";
    
    const ADD_PRODUCT = "INSERT INTO `product` (product_name, product_detail_id, stock, price, category_id, brand_id, product_img) VALUES (:product_name,:product_detail_id,:stock,:price,:category_id,:brand_id,:product_img)";
    const ADD_PRODUCT_DETAIL = "INSERT INTO `product_detail` (product_description, product_max_freq, product_min_freq, product_impedance) VALUES (:product_description,:product_max_freq,:product_min_freq,:product_impedance)";
    const GET_BRAND_ID = "SELECT brand_id FROM brands WHERE brand_name = :brandName";
    const GET_CATEGORY_ID = "SELECT categorie_id FROM category WHERE categorie_name = :categorieName";
    const DELETE_PRODUCT_ID = "DELETE FROM `product` WHERE product_id=:id";
    const UPDATE_STOCK_PRICE = "UPDATE product SET price=:prix ,stock = :stock WHERE product_id=:id";
    const GET_PRODUCT_BY_Category = "SELECT 
                                            p.product_id,
                                            p.product_name, 
                                            p.price,
                                            p.product_img,
                                            b.brand_name, 
                                            c.categorie_name
                                            FROM 
                                            product p
                                            JOIN 
                                            brands b ON p.brand_id = b.brand_id
                                            JOIN 
                                            category c ON p.category_id = c.categorie_id
                                            WHERE 
                                            p.category_id = :category_id; ";
        const GET_PRODUCT_BY_ID = "SELECT 
                                        p.product_id,
                                        p.product_name,
                                        p.price,
                                        p.product_img,
                                        b.brand_name,
                                        c.categorie_name,
                                        pd.*
                                    FROM 
                                        product p
                                    JOIN 
                                        brands b ON p.brand_id = b.brand_id
                                    JOIN 
                                        category c ON p.category_id = c.categorie_id
                                    JOIN 
                                        product_detail pd ON p.product_detail_id = pd.product_detail_id 
                                    WHERE 
                                        p.product_id = :id;";
    
    
    
   
    
    
    
    
    
    
    private function getCategoryById($category){
        try {
            $stmt = $this->conn->prepare(self::GET_CATEGORY_ID);
            $stmt->execute(['categorieName' => $category]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['categorie_id'] : null;
        } catch (PDOException $error) {
            http_response_code(500);
            echo json_encode(['message' => 'Internal Server Error: ' . $error->getMessage()]);
            exit;
        }
    }
     
    public function getProductbyCategory($category){

        $gategId = $this->getCategoryById($category);
        if(!isset($gategId)){
            http_response_code(404);
            echo json_encode(['message' => 'Not found' ]);
            exit;
        }
        try {
            $stmt = $this->conn->prepare(self::GET_PRODUCT_BY_Category);
            $stmt->execute(['category_id' => $gategId]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result ;
        } catch (PDOException $error) {
            http_response_code(500);
            echo json_encode(['message' => 'Internal Server Error: ' . $error->getMessage()]);
            exit;
        }
    }
    

    public function addProductDetail($product_description, $product_max_freq, $product_min_freq, $product_impedance){
        try {
            $stmt = $this->conn->prepare(self::ADD_PRODUCT_DETAIL);
            $stmt->execute([
                ':product_description' => $product_description,
                ':product_max_freq' => $product_max_freq,
                ':product_min_freq' => $product_min_freq,
                ':product_impedance' => $product_impedance,
            ]);
            return $this->conn->lastInsertId();
        } catch (PDOException $error) {
            http_response_code(500);
            echo json_encode(['message' => 'Internal Server Error: ' . $error->getMessage()]);
            exit;
        }
    }

    private function getBrandById($brandName){
        try {
            $stmt = $this->conn->prepare(self::GET_BRAND_ID);
            $stmt->execute([':brandName' => $brandName]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['brand_id'] : null;
        } catch (PDOException $error) {
            http_response_code(500);
            echo json_encode(['message' => 'Internal Server Error: ' . $error->getMessage()]);
            exit;
        }
    }

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function getAllProduct(){
        $result = null;
        try {
            $stmt = $this->conn->prepare(self::GET_ALL_PRODUCTS);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            http_response_code(500);
            echo json_encode(['message' => 'Internal Server Error: ' . $error->getMessage()]);
            exit;
        }
        return $result;
    }

    public function addProduct(array $data){
 
       

        $brand_id = $this->getBrandById($data['brand']);
        $category_id = $this->getCategoryById($data['category']);
        $product_detail_id = $this->addProductDetail(
            $data['product_description'],
            $data['product_max_freq'],
            $data['product_min_freq'],
            $data['product_impedance']
        );

        try {
            $stmt = $this->conn->prepare(self::ADD_PRODUCT);
            $stmt->execute([
                ':product_name' => $data['product_name'],
                ':product_detail_id' => $product_detail_id,
                ':stock' => $data['stock'],
                ':price' => $data['price'],
                ':category_id' => $category_id,
                ':brand_id' => $brand_id,
                ':product_img' => $data['product_img']
            ]);
            return $stmt;
        } catch (PDOException $error) {
            http_response_code(500);
            echo json_encode(['message' => 'Internal Server Error: ' . $error->getMessage()]);
            exit;
        }
    }


    public function removeProductById($id){
        try {
            $stmt = $this->conn->prepare(self::DELETE_PRODUCT_ID);
            $stmt->execute([
               ':id'=>$id
            ]);
            return $stmt;
        } catch (PDOException $error) {
            http_response_code(500);
            echo json_encode(['message' => 'Internal Server Error: ' . $error->getMessage()]);
            exit;
        }
    }

    public function getProductById($id){
        $result = null;
        try {
            $stmt = $this->conn->prepare(self::GET_PRODUCT_BY_ID);
            $stmt->execute([
               ':id'=>$id
            ]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            http_response_code(500);
            echo json_encode(['message' => 'Internal Server Error: ' . $error->getMessage()]);
            exit;
        }
        return  $result;
    }

    public function updateProduct($id,array $newData){

        $setClause = [];
        $values = [];
        foreach($newData as $key => $value){
            
            $param = "$key =:$key";
            array_push($setClause,$param);
            $values[":$key"] = $value;

        }
        $setStaments = implode(',',$setClause);

        $query = "UPDATE product SET  $setStaments WHERE product_id=:product_id";
        $values["product_id"] = $id;
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
}
?>
