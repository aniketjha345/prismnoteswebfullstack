CREATE DATABASE file_management_system;

USE file_management_system;

-- Table for admin users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Insert a default admin user (password: admin123)
INSERT INTO users (username, password) VALUES ('admin', MD5('admin123'));

-- Table for categories
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- Table for files
CREATE TABLE files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);
