<?php
$host = "localhost";
$username = "root";
$password = "";     
$dbname = "library_db";

// Connect to MySQL server
$conn = new mysqli($host, $username, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");

// Select the database
$conn->select_db($dbname);

// Create books table
$conn->query("
CREATE TABLE IF NOT EXISTS books (
    book_id INT AUTO_INCREMENT PRIMARY KEY,
    book_name VARCHAR(255),
    book_category VARCHAR(255) UNIQUE,
    book_author VARCHAR(255),
    book_description TEXT,
    book_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Create users table
$conn->query("
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) UNIQUE,
    name VARCHAR(255),
    email VARCHAR(255),
    password VARCHAR(255)
)");

// Create request table
$conn->query("
CREATE TABLE IF NOT EXISTS request (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50),
    book_id INT,
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (student_id) REFERENCES users(student_id),
    FOREIGN KEY (book_id) REFERENCES books(book_id)
)");

// Create favorites table
$conn->query("
CREATE TABLE IF NOT EXISTS favorites (
    favorite_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50),
    book_id INT,
    FOREIGN KEY (student_id) REFERENCES users(student_id),
    FOREIGN KEY (book_id) REFERENCES books(book_id)
)");

// Create messages table
$conn->query("
CREATE TABLE IF NOT EXISTS messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50),
    message_content TEXT,
    message_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(student_id)
)");

// Create non_returned_books table
$conn->query("
CREATE TABLE IF NOT EXISTS non_returned_books (
    nrb_id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT,
    student_id VARCHAR(50),
    date_student_received_book DATE,
    return_date DATE,
    days_left INT,
    overdued_book BOOLEAN,
    FOREIGN KEY (book_id) REFERENCES books(book_id),
    FOREIGN KEY (student_id) REFERENCES users(student_id)
)");

// Create issued_books table
$conn->query("
CREATE TABLE IF NOT EXISTS issued_books (
    issued_id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT,
    student_id VARCHAR(50),
    date_student_received_book DATE,
    book_returned_date DATE,
    FOREIGN KEY (book_id) REFERENCES books(book_id),
    FOREIGN KEY (student_id) REFERENCES users(student_id)
)");

// Create count_items table
$conn->query("
CREATE TABLE IF NOT EXISTS count_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    total_books INT DEFAULT 0,
    total_categories INT DEFAULT 0,
    total_members INT DEFAULT 0,
    total_messages INT DEFAULT 0,
    pending_requests INT DEFAULT 0,
    overdue_books INT DEFAULT 0,
    total_reports INT DEFAULT 0,
    total_non_returned_books INT DEFAULT 0,
    total_issued_books INT DEFAULT 0
)");

echo "Database and tables created successfully.";
$conn->close();
?>
