<?php
require_once 'connectdb.php';

$usersTable = "CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(80) NOT NULL,
    last_name VARCHAR(80) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(160) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$enrollmentsTable = "CREATE TABLE IF NOT EXISTS enrollments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL UNIQUE,
    plan ENUM('basic','premium','vip') NOT NULL,
    goal ENUM('weight_loss','muscle_gain','endurance') NOT NULL,
    preferred_times VARCHAR(100) NOT NULL,
    health_notes TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

if (mysqli_query($conn, $usersTable)) {
    echo "Users table ready.<br>";
} else {
    echo "Error creating users table: " . mysqli_error($conn) . "<br>";
}

if (mysqli_query($conn, $enrollmentsTable)) {
    echo "Enrollments table ready.<br>";
} else {
    echo "Error creating enrollments table: " . mysqli_error($conn) . "<br>";
}
?>
