<?php
// api/cart.php
session_start();
require_once '../config.php';

class CartAPI {
    private $conn;
    
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    public function addToCart($user_id, $product_id, $quantity = 1) {
        // Check if product exists in cart
        $check_query = "SELECT * FROM cart WHERE user_id = :user_id AND product_id = :product_id";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bindParam(':user_id', $user_id);
        $check_stmt->bindParam(':product_id', $product_id);
        $check_stmt->execute();
        
        if($check_stmt->rowCount() > 0) {
            // Update quantity
            $query = "UPDATE cart SET quantity = quantity + :quantity 
                     WHERE user_id = :user_id AND product_id = :product_id";
        } else {
            // Insert new item
            $query = "INSERT INTO cart (user_id, product_id, quantity) 
                     VALUES (:user_id, :product_id, :quantity)";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':quantity', $quantity);
        
        if($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Added to cart']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add to cart']);
        }
    }

    public function getCart($user_id) {
        $query = "SELECT c.*, p.name, p.price, p.image_url, p.stock_quantity
                 FROM cart c
                 JOIN products p ON c.product_id = p.product_id
                 WHERE c.user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $total = 0;
        foreach($cart_items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        echo json_encode([
            'success' => true, 
            'data' => $cart_items,
            'total' => $total,
            'count' => count($cart_items)
        ]);
    }
}
?>