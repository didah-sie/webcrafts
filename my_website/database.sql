-- database.sql
CREATE DATABASE IF NOT EXISTS handmade_market;
USE handmade_market;

-- 1. Users table
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    avatar VARCHAR(255),
    role ENUM('customer', 'artist', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. Artists table (separate from users for 3NF)
CREATE TABLE artists (
    artist_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE NOT NULL,
    bio TEXT,
    location VARCHAR(100),
    website VARCHAR(255),
    instagram VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- 3. Categories table
CREATE TABLE categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    icon_class VARCHAR(50)
);

-- 4. Products table
CREATE TABLE products (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    artist_id INT NOT NULL,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock_quantity INT DEFAULT 0,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_featured BOOLEAN DEFAULT FALSE,
    is_available BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (artist_id) REFERENCES artists(artist_id),
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

-- 5. Product Images table (multiple images per product)
CREATE TABLE product_images (
    image_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

-- 6. Cart table
CREATE TABLE cart (
    cart_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (user_id, product_id)
);

-- 7. Orders table
CREATE TABLE orders (
    order_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    shipping_address TEXT,
    payment_method VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- 8. Order Items table
CREATE TABLE order_items (
    order_item_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price_at_time DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- 9. Reviews table
CREATE TABLE reviews (
    review_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    UNIQUE KEY unique_review (user_id, product_id)
);

-- Insert sample categories
INSERT INTO categories (category_name, description, icon_class) VALUES
('Paintings', 'Original handcrafted paintings', 'fa-paint-brush'),
('Pottery', 'Ceramic and clay artworks', 'fa-pagelines'),
('Woodwork', 'Hand-carved wooden crafts', 'fa-tree'),
('Jewelry', 'Handmade jewelry and accessories', 'fa-diamond'),
('Textiles', 'Fabric and fiber arts', 'fa-cut'),
('Sculptures', '3D art sculptures', 'fa-cube');

-- Insert sample user (password: 'password123')
INSERT INTO users (username, email, password_hash, full_name, role) VALUES
('artisan_john', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Smith', 'artist');

-- Insert sample artist
INSERT INTO artists (user_id, bio, location) VALUES
(1, 'Professional artist specializing in contemporary paintings', 'New York, USA');

-- Insert sample products
INSERT INTO products (artist_id, category_id, name, description, price, stock_quantity, image_url, is_featured) VALUES
(1, 1, 'Abstract Dreams', 'Vibrant abstract painting on canvas', 299.99, 5, 'https://images.unsplash.com/photo-1541961017774-22349e4a1a2b', TRUE),
(1, 2, 'Ceramic Vase Set', 'Hand-thrown ceramic vases', 149.99, 3, 'https://images.unsplash.com/photo-1578749559196-482c531f2b7b', TRUE),
(1, 3, 'Wooden Bowl', 'Hand-carved mahogany bowl', 89.99, 8, 'https://images.unsplash.com/photo-1591474200742-8e512e6f98f8', FALSE);