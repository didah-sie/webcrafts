<?php
// api/register.php
require_once '../config.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];
    $password = $_POST['password'];
    $role = $_POST['role'] ?? 'customer';
    
    // Check if user exists
    $check_query = "SELECT * FROM users WHERE email = :email OR username = :username";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(':email', $email);
    $check_stmt->bindParam(':username', $username);
    $check_stmt->execute();
    
    if ($check_stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Email or username already exists']);
        exit;
    }
    
    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user
    $query = "INSERT INTO users (username, email, password_hash, full_name, role) 
              VALUES (:username, :email, :password_hash, :full_name, :role)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password_hash', $password_hash);
    $stmt->bindParam(':full_name', $full_name);
    $stmt->bindParam(':role', $role);
    
    if ($stmt->execute()) {
        $user_id = $db->lastInsertId();
        
        // If artist, create artist profile
        if ($role === 'artist') {
            $artist_query = "INSERT INTO artists (user_id) VALUES (:user_id)";
            $artist_stmt = $db->prepare($artist_query);
            $artist_stmt->bindParam(':user_id', $user_id);
            $artist_stmt->execute();
        }
        
        echo json_encode(['success' => true, 'message' => 'Registration successful']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Registration failed']);
    }
}
?>