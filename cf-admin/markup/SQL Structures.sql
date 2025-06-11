CREATE DATABASE cc_content3C25;
USE cc_content3C25;

CREATE TABLE User_Configs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    key_name VARCHAR(255) NOT NULL,
    value VARCHAR(255),
    value_type VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


CREATE TABLE Products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type ENUM('physical', 'digital') NOT NULL,
    price DECIMAL(10, 2) NOT NULL CHECK (price >= 0),
    description TEXT,
    pre_requirements TEXT,
    reviews TEXT,
    category_id INT,
    rating FLOAT CHECK (rating BETWEEN 0 AND 5),
    vendor VARCHAR(255),
    affiliates INT,
    comment_id INT,
    meta JSON,
    image_id JSON,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE NewsAndEvents (
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

CREATE TABLE People (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    position VARCHAR(255),
    email VARCHAR(255),
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

CREATE TABLE Blogs (
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

CREATE TABLE Inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    email VARCHAR(255),
    contact_no VARCHAR(20),
    affiliates INT,
    source_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_collected BOOLEAN DEFAULT FALSE
);

CREATE TABLE Newsletter (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    email VARCHAR(255),
    push_notification BOOLEAN DEFAULT FALSE,
    source_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_collected BOOLEAN DEFAULT FALSE
);

CREATE TABLE FAQs (
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

CREATE TABLE Affiliations (
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

CREATE TABLE Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    role VARCHAR(100),
    2fa_enabled BOOLEAN DEFAULT FALSE,
    2fa_secret VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE Showcase (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    description TEXT,
    img VARCHAR(255),
    link VARCHAR(255),
    affiliates INT,
    meta JSON,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE Comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    comment TEXT NOT NULL,
    author VARCHAR(255) NOT NULL,
    parent_comment_id INT,
    is_active BOOLEAN DEFAULT TRUE,
    is_offensive BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE Chatbot (
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

CREATE TABLE Configurations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    Feature VARCHAR(255),
    is_enabled BOOLEAN DEFAULT TRUE,
    trial_end DATE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE MediaLibrary (
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

ALTER TABLE Products
ADD CONSTRAINT fk_products_affiliates FOREIGN KEY (affiliates) REFERENCES Affiliations(id),
ADD CONSTRAINT fk_products_comments FOREIGN KEY (comment_id) REFERENCES Comments(id);

ALTER TABLE NewsAndEvents
ADD CONSTRAINT fk_news_affiliates FOREIGN KEY (affiliates) REFERENCES Affiliations(id),
ADD CONSTRAINT fk_news_comments FOREIGN KEY (comment_id) REFERENCES Comments(id);

ALTER TABLE People
ADD CONSTRAINT fk_people_affiliates FOREIGN KEY (affiliates) REFERENCES Affiliations(id),
ADD CONSTRAINT fk_people_comments FOREIGN KEY (comment_id) REFERENCES Comments(id);

ALTER TABLE Blogs
ADD CONSTRAINT fk_blogs_author FOREIGN KEY (author_id) REFERENCES People(id),
ADD CONSTRAINT fk_blogs_affiliates FOREIGN KEY (affiliates) REFERENCES Affiliations(id),
ADD CONSTRAINT fk_blogs_comments FOREIGN KEY (comment_id) REFERENCES Comments(id);

ALTER TABLE FAQs
ADD CONSTRAINT fk_faqs_author FOREIGN KEY (author_id) REFERENCES People(id),
ADD CONSTRAINT fk_faqs_affiliates FOREIGN KEY (affiliates) REFERENCES Affiliations(id),
ADD CONSTRAINT fk_faqs_comments FOREIGN KEY (comment_id) REFERENCES Comments(id);

ALTER TABLE Showcase
ADD CONSTRAINT fk_showcase_affiliates FOREIGN KEY (affiliates) REFERENCES Affiliations(id);

ALTER TABLE Comments
ADD CONSTRAINT fk_comments_parent FOREIGN KEY (parent_comment_id) REFERENCES Comments(id) ON DELETE CASCADE;
