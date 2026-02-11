<?php
// Simple PHP login handler
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Example credentials (replace with your database verification)
    $valid_email = "user@example.com";
    $valid_password = "password123";

    if ($email === $valid_email && $password === $valid_password) {
        echo "Login successful! Welcome, " . htmlspecialchars($email);
    } else {
        echo "Invalid email or password!";
    }
}
?>
