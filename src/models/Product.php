<?php 
class Product {
    private $conn;
    const GET_ALL_PRODUCTS = "SELECT * FROM product";
    const ADD_PRODUCT = "INSERT INTO `product` (product_name, product_detail_id, stock, price, category_id, brand_id, product_img) VALUES (:product_name,:product_detail_id,:stock,:price,:category_id,:brand_id,:product_img)";
    const ADD_PRODUCT_DETAIL = "INSERT INTO `product_detail` (product_description, product_max_freq, product_min_freq, product_impedance) VALUES (:product_description,:product_max_freq,:product_min_freq,:product_impedance)";
    const GET_BRAND_ID = "SELECT brand_id FROM brands WHERE brand_name = :brandName";
    const GET_CATEGORY_ID = "SELECT categorie_id FROM category WHERE categorie_name = :categorieName";

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
        }
        return $result;
    }

    public function addProduct(array $data){
        // Log the incoming data
        error_log(print_r($data, true)); 

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
        }
    }
}
?>
