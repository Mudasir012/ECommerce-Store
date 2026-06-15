CREATE DATABASE IF NOT EXISTS luxe_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE luxe_store;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user','admin') DEFAULT 'user',
    address TEXT,
    city VARCHAR(100),
    postal_code VARCHAR(20),
    country VARCHAR(100) DEFAULT 'US',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email)
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(120) NOT NULL UNIQUE,
    description TEXT,
    image VARCHAR(255),
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_sort (sort_order)
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    gender ENUM('men','women','kids','unisex') NOT NULL DEFAULT 'men',
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(280) NOT NULL UNIQUE,
    description TEXT,
    details TEXT,
    price DECIMAL(10,2) NOT NULL,
    compare_price DECIMAL(10,2) DEFAULT NULL,
    image VARCHAR(255),
    image_alt VARCHAR(255),
    stock INT DEFAULT 0,
    featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    INDEX idx_slug (slug),
    INDEX idx_category (category_id),
    INDEX idx_featured (featured),
    INDEX idx_price (price),
    FULLTEXT idx_search (name, description)
);

CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    session_id VARCHAR(64) NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_session (session_id),
    INDEX idx_user (user_id),
    UNIQUE KEY uk_cart_item (session_id, user_id, product_id)
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pending','confirmed','shipped','delivered','cancelled') DEFAULT 'pending',
    shipping_name VARCHAR(100) NOT NULL,
    shipping_address TEXT NOT NULL,
    shipping_city VARCHAR(100) NOT NULL,
    shipping_postal VARCHAR(20) NOT NULL,
    shipping_country VARCHAR(100) DEFAULT 'US',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    INDEX idx_order (order_id)
);

INSERT INTO categories (name, slug, description, sort_order) VALUES
('Ready-to-Wear', 'ready-to-wear', 'Tailored pieces for the modern wardrobe', 1),
('Outerwear', 'outerwear', 'Structured coats and lightweight layers', 2),
('Footwear', 'footwear', 'Handcrafted leather and woven silhouettes', 3),
('Accessories', 'accessories', 'Bags, belts, and finishing details', 4);

-- Admin user is created by running: php setup.php

INSERT INTO products (category_id, gender, name, slug, description, price, image, stock, featured) VALUES
(1, 'men', 'Merino Wool Crew', 'merino-wool-crew', 'Australian merino wool knit. Ribbed cuffs and hem. Relaxed silhouette.', 395.00, 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=800&q=80', 25, 1),
(1, 'men', 'Linen Relaxed Shirt', 'linen-relaxed-shirt', 'Portuguese linen. Mother-of-pearl buttons. Unconstructed collar.', 285.00, 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=800&q=80', 30, 1),
(1, 'men', 'Cashmere Turtleneck', 'cashmere-turtleneck', 'Inner Mongolian cashmere. Rib-knit collar and cuffs. True to size.', 695.00, 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=800&q=80', 15, 1),
(2, 'men', 'Wool Peacoat', 'wool-peacoat', 'Italian virgin wool. Double-breasted. Horn buttons. Half-lined.', 1295.00, 'https://images.unsplash.com/photo-1539533018447-63fcce2678e3?w=800&q=80', 10, 1),
(2, 'men', 'Trench Coat', 'trench-coat', 'Water-repellent cotton gabardine. Removable belt. Raglan sleeves.', 1595.00, 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=800&q=80', 8, 1),
(3, 'men', 'Leather Derbys', 'leather-derbys', 'Full-grain calf leather. Goodyear welted. Blake stitch construction.', 895.00, 'https://images.unsplash.com/photo-1614252369475-531eba835eb1?w=800&q=80', 20, 1),
(3, 'men', 'Suede Chukka Boots', 'suede-chukka-boots', 'Spanish suede. Crepe sole. Unlined for comfort.', 750.00, 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=800&q=80', 12, 0),
(4, 'men', 'Grained Leather Belt', 'grained-leather-belt', 'Italian vegetable-tanned leather. Brushed brass buckle. 35mm width.', 195.00, 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=800&q=80', 40, 0),
(4, 'men', 'Wool Cashmere Scarf', 'wool-cashmere-scarf', 'Blend of lambswool and cashmere. Fringed ends. 180 x 30cm.', 245.00, 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?w=800&q=80', 35, 0),
(1, 'men', 'Silk Pajama Shirt', 'silk-pajama-shirt', 'Mulberry silk. Shell buttons. Flat front, side vents.', 450.00, 'https://images.unsplash.com/photo-1564257631407-4deb1f99d992?w=800&q=80', 18, 0),
(3, 'men', 'Canvas Espadrilles', 'canvas-espadrilles', 'Organic cotton canvas. Jute sole. Hand-crafted in Spain.', 185.00, 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=800&q=80', 50, 0),
(4, 'men', 'Leather Tote', 'leather-tote', 'Full-grain buffalo leather. Two internal slip pockets. Cotton lining.', 1250.00, 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=800&q=80', 8, 0);
