CREATE DATABASE re_revamps;
USE re_revamps;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS products, news_and_events, blogs, faqs, categories, user_configs, people, affiliations, users, comments, chatbot, configurations, media_library, inquiries, newsletter;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    parent_category_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE user_configs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    key_name VARCHAR(255) NOT NULL,
    value VARCHAR(255),
    value_type VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type ENUM('physical', 'digital') NOT NULL,
    price DECIMAL(10, 2) NOT NULL CHECK (price >= 0),
    description TEXT,
    pre_requirements TEXT,
    reviews TEXT,
    category_id INT,
    rating FLOAT CHECK (rating >= 0 AND rating <= 5),
    vendor VARCHAR(255),
    affiliates INT,
    comment_id INT,
    meta JSON,
    image_id JSON,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE news_and_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    date DATE,
    content TEXT,
    people TEXT,
    location VARCHAR(255),
    type VARCHAR(100),
    category VARCHAR(100),
    affiliates INT,
    comment_id INT,
    meta JSON,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE people (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    position VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    bio TEXT,
    profile_picture VARCHAR(255),
    dob DATE,
    weblinks TEXT,
    role VARCHAR(100),
    affiliates INT,
    comment_id INT,
    meta JSON,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    is_hidden BOOLEAN DEFAULT FALSE
);

CREATE TABLE blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    snippet TEXT,
    content TEXT,
    author_id INT,
    pub_date DATE,
    category VARCHAR(100),
    affiliates INT,
    comment_id INT,
    meta JSON,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    email VARCHAR(255) UNIQUE,
    contact_no VARCHAR(20),
    affiliates INT,
    source_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_collected BOOLEAN DEFAULT FALSE
);

CREATE TABLE newsletter (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    email VARCHAR(255) UNIQUE,
    push_notification BOOLEAN DEFAULT FALSE,
    source_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_collected BOOLEAN DEFAULT FALSE
);

CREATE TABLE faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    answer TEXT,
    category VARCHAR(100),
    content TEXT,
    is_helpful BOOLEAN DEFAULT FALSE,
    author_id INT,
    affiliates INT,
    comment_id INT,
    meta JSON,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE affiliations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    type ENUM('partner', 'sponsor', 'other') NOT NULL,
    link VARCHAR(255),
    description TEXT,
    logo_img VARCHAR(255),
    content TEXT,
    meta JSON,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    role VARCHAR(100),
    2fa_enabled BOOLEAN DEFAULT FALSE,
    2fa_secret VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    comment TEXT NOT NULL,
    author VARCHAR(255) NOT NULL,
    parent_comment_id INT,
    is_active BOOLEAN DEFAULT TRUE,
    is_offensive BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE chatbot (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT NOT NULL,
    user VARCHAR(255),
    started_at DATETIME,
    expire_date DATETIME,
    contact VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    purge_date DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE configurations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    Feature VARCHAR(255),
    is_enabled BOOLEAN DEFAULT TRUE,
    trial_end DATE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE media_library (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50),
    src VARCHAR(255),
    alt VARCHAR(255),
    srcset VARCHAR(255),
    name VARCHAR(255),
    size BIGINT,
    is_active BOOLEAN DEFAULT TRUE,
    low_res VARCHAR(255),
    high_res VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert placeholders for each table
INSERT INTO categories (name, description, is_active) VALUES ('Default Category', 'Placeholder category', TRUE);
INSERT INTO user_configs (key_name, value, value_type) VALUES ('default_config', 'value', 'string');
INSERT INTO products (name, type, price, category_id, is_active) VALUES ('Sample Product', 'digital', 0.00, 1, TRUE);
INSERT INTO news_and_events (title, date, content, is_active) VALUES ('Sample Event', '2025-01-01', 'Sample content', TRUE);
INSERT INTO people (first_name, last_name, email, is_active) VALUES ('John', 'Doe', 'john.doe@example.com', TRUE);
INSERT INTO blogs (title, snippet, content, author_id, category, is_active) VALUES ('Sample Blog', 'Snippet', 'Content', 1, 'Default Category', TRUE);
INSERT INTO inquiries (first_name, last_name, email) VALUES ('Jane', 'Doe', 'jane.doe@example.com');
INSERT INTO newsletter (first_name, last_name, email) VALUES ('Subscriber', 'One', 'subscriber@example.com');
INSERT INTO faqs (question, answer, category, is_active) VALUES ('Sample Question', 'Sample Answer', 'Default Category', TRUE);
INSERT INTO affiliations (title, type) VALUES ('Sample Affiliation', 'partner');
INSERT INTO users (username, password_hash) VALUES ('admin', 'hashed_password');
INSERT INTO comments (comment, author) VALUES ('Sample Comment', 'Admin');
INSERT INTO chatbot (content, user) VALUES ('Sample Chatbot Content', 'User');
INSERT INTO configurations (Feature, is_enabled) VALUES ('Sample Feature', TRUE);
INSERT INTO media_library (type, src, name) VALUES ('image', 'sample.jpg', 'Sample Image');
