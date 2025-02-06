-- Create Database
CREATE DATABASE IF NOT EXISTS print;

-- Use the Database
USE print;

-- Create Users Table
CREATE TABLE users (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE shop_owners (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    shopname VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample data into the shop_owners table with specified passwords
INSERT INTO shop_owners (shopname, password) VALUES 
('Kattappana', 'password1'),
('Nedumkandam', 'password2'),
('Kumily', 'password3'),
('Thodupuzha', 'password4'),
('Adimali', 'password5');


CREATE TABLE shop_kattappana (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    print_type VARCHAR(100) NOT NULL,
    color_option VARCHAR(50) NOT NULL,
    paper_size VARCHAR(50) NOT NULL,
    paper_material VARCHAR(100) NOT NULL,
    additional_services VARCHAR(255),
    quantity INT(11) NOT NULL,
    delivery_address TEXT NOT NULL,
    number_of_pages INT(11) NOT NULL,
    total_cost DECIMAL(10, 2) NOT NULL,
    order_status VARCHAR(50) NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE shop_nedumkandam (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    print_type VARCHAR(100) NOT NULL,
    color_option VARCHAR(50) NOT NULL,
    paper_size VARCHAR(50) NOT NULL,
    paper_material VARCHAR(100) NOT NULL,
    additional_services VARCHAR(255),
    quantity INT(11) NOT NULL,
    delivery_address TEXT NOT NULL,
    number_of_pages INT(11) NOT NULL,
    total_cost DECIMAL(10, 2) NOT NULL,
    order_status VARCHAR(50) NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE shop_kumily (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    print_type VARCHAR(100) NOT NULL,
    color_option VARCHAR(50) NOT NULL,
    paper_size VARCHAR(50) NOT NULL,
    paper_material VARCHAR(100) NOT NULL,
    additional_services VARCHAR(255),
    quantity INT(11) NOT NULL,
    delivery_address TEXT NOT NULL,
    number_of_pages INT(11) NOT NULL,
    total_cost DECIMAL(10, 2) NOT NULL,
    order_status VARCHAR(50) NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE shop_thodupuzha (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    print_type VARCHAR(100) NOT NULL,
    color_option VARCHAR(50) NOT NULL,
    paper_size VARCHAR(50) NOT NULL,
    paper_material VARCHAR(100) NOT NULL,
    additional_services VARCHAR(255),
    quantity INT(11) NOT NULL,
    delivery_address TEXT NOT NULL,
    number_of_pages INT(11) NOT NULL,
    total_cost DECIMAL(10, 2) NOT NULL,
    order_status VARCHAR(50) NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE shop_adimali (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    print_type VARCHAR(100) NOT NULL,
    color_option VARCHAR(50) NOT NULL,
    paper_size VARCHAR(50) NOT NULL,
    paper_material VARCHAR(100) NOT NULL,
    additional_services VARCHAR(255),
    quantity INT(11) NOT NULL,
    delivery_address TEXT NOT NULL,
    number_of_pages INT(11) NOT NULL,
    total_cost DECIMAL(10, 2) NOT NULL,
    order_status VARCHAR(50) NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE complaints (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Example insertion of a sample complaint
INSERT INTO complaints (first_name, last_name, email, message) VALUES 
('John', 'Doe', 'john.doe@example.com', 'Sample complaint message.');

