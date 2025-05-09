<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'hostel';


$conn = mysqli_connect($host, $username, $password);
if (!$conn) {
    $response = ['success' => false, 'message' => 'Failed to connect to database: ' . mysqli_connect_error()];
    echo json_encode($response);
    exit;
}


$db_create_query = "CREATE DATABASE IF NOT EXISTS $dbname";
if (!mysqli_query($conn, $db_create_query)) {
    $response = ['success' => false, 'message' => 'Failed to create database: ' . mysqli_error($conn)];
    echo json_encode($response);
    mysqli_close($conn);
    exit;
}


mysqli_select_db($conn, $dbname);


$table_create_query = "CREATE TABLE IF NOT EXISTS users (
  id INT(255) AUTO_INCREMENT PRIMARY KEY,
  fullname VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  contact VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
)";


if (!mysqli_query($conn, $table_create_query)) {
    $response = ['success' => false, 'message' => 'Failed to create table: ' . mysqli_error($conn)];
    echo json_encode($response);
    mysqli_close($conn);
    exit;
}

$booking = "CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    booking_date DATE NOT NULL,    
    user_id INT(255) NOT NULL,
    FOREIGN KEY user_id REFERENCES users(id)
)";

if (!mysqli_query($conn, $booking)) {
    $response = ['success' => false, 'message' => 'Failed to create table: ' . mysqli_error($conn)];
    echo json_encode($response);
    mysqli_close($conn);
    exit;
}



$room = "CREATE TABLE IF NOT EXISTS room (
room_id INT(255) AUT0_INCREMENT PRIMARY KEY,
room_image_url VARCHAR(255) NOT NULL,
room_number INT(255) NOT NULL UNIQUE,
room_description TEXT NOT NULL,
location VARCHAR(255) NOT NULL,
room_cost DECIMAL(255,2) NOT NULL,
)";

if (!mysqli_query($conn, $room)) {
    $response = ['success' => false, 'message' => 'Failed to create table: ' . mysqli_error($conn)];
    echo json_encode($response);
    mysqli_close($conn);
    exit;
}

?>