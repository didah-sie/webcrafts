<?php
// api/products.php
require_once '../config.php';

class ProductAPI {
    private $conn;
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    // Get all products
    public function getProducts() {
        $query = "SELECT p.*, c.category_name, u.username as artist_name, 
                         a.bio as artist_bio, u.full_name
                  FROM products p
                  JOIN categories c ON p.category_id = c.category_id
                  JOIN artists a ON p.artist_id = a.artist_id
                  JOIN users u ON a.user_id = u.user_id
                  WHERE p.is_available = TRUE
                  ORDER BY p.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get images for each product
        foreach($products as &$product) {
            $img_query = "SELECT image_url, is_primary FROM product_images 
                         WHERE product_id = :product_id ORDER BY sort_order";
            $img_stmt = $this->conn->prepare($img_query);
            $img_stmt->bindParam(':product_id', $product['product_id']);
            $img_stmt->execute();
            $product['images'] = $img_stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        echo json_encode(['success' => true, 'data' => $products]);
    }

    // Get single product
    public function getProduct($id) {
        $query = "SELECT p.*, c.category_name, u.username as artist_name, a.bio as artist_bio
                  FROM products p
                  JOIN categories c ON p.category_id = c.category_id
                  JOIN artists a ON p.artist_id = a.artist_id
                  JOIN users u ON a.user_id = u.user_id
                  WHERE p.product_id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($product) {
            // Get images
            $img_query = "SELECT image_url, is_primary FROM product_images 
                         WHERE product_id = :product_id ORDER BY sort_order";
            $img_stmt = $this->conn->prepare($img_query);
            $img_stmt->bindParam(':product_id', $product['product_id']);
            $img_stmt->execute();
            $product['images'] = $img_stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get reviews
            $review_query = "SELECT r.*, u.username FROM reviews r
                           JOIN users u ON r.user_id = u.user_id
                           WHERE r.product_id = :product_id
                           ORDER BY r.created_at DESC";
            $review_stmt = $this->conn->prepare($review_query);
            $review_stmt->bindParam(':product_id', $product['product_id']);
            $review_stmt->execute();
            $product['reviews'] = $review_stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        echo json_encode(['success' => true, 'data' => $product]);
    }
}

$api = new ProductAPI();
$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        if(isset($_GET['id'])) {
            $api->getProduct($_GET['id']);
        } else {
            $api->getProducts();
        }
        break;
}
?>