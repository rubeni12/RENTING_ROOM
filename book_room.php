<?php
header("Access-Control-Allow-Origin: *"); // For development only
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once 'config.php'; // config.php iwe na $conn = new mysqli(...);

// Pokea inputs
$email = $_POST['email'] ?? '';
$roomName = $_POST['room_name'] ?? '';
$bookingDate = $_POST['booking_date'] ?? '';
$roomPrice = $_POST['room_price'] ?? '';
$roomDescription = $_POST['room_description'] ?? '';
$roomLocation = $_POST['room_location'] ?? '';

// Validations
if (empty($email) || empty($roomName) || empty($bookingDate)) {
    echo json_encode(["success" => false, "message" => "Missing required fields."]);
    exit;
}

// Check kama tayari imebookiwa
$check = $conn->prepare("SELECT * FROM bookings WHERE room_name = ? AND booking_date = ?");
$check->bind_param("ss", $roomName, $bookingDate);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Room already booked for this date."]);
    exit;
}

// Handle image upload (badala ya URL)
$imagePath = '';
if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] == 0) {
    $targetDir = "uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $imageName = time() . "_" . basename($_FILES["room_image"]["name"]);
    $imagePath = $targetDir . $imageName;

    if (!move_uploaded_file($_FILES["room_image"]["tmp_name"], $imagePath)) {
        echo json_encode(["success" => false, "message" => "Failed to upload image."]);
        exit;
    }
} else {
    echo json_encode(["success" => false, "message" => "Image file is required."]);
    exit;
}

// Insert booking
$stmt = $conn->prepare("INSERT INTO bookings (email, room_name, booking_date, room_price, room_description, room_location, room_image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $email, $roomName, $bookingDate, $roomPrice, $roomDescription, $roomLocation, $imagePath);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Room booked successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Booking failed."]);
}

?>
