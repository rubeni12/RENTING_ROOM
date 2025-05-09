<?php

require_once 'config.php';


$email = isset($_POST['email']) ? $_POST['email'] : '';
$roomName = isset($_POST['room_name']) ? $_POST['room_name'] : '';
$bookingDate = isset($_POST['booking_date']) ? $_POST['booking_date'] : '';
$roomPrice = isset($_POST['room_price']) ? $_POST['room_price'] : '';
$roomDescription = isset($_POST['room_description']) ? $_POST['room_description'] : ''; // Get description
$roomLocation = isset($_POST['room_location']) ? $_POST['room_location'] : '';       // Get location
$roomImageUrl = isset($_POST['room_image_url']) ? $_POST['room_image_url'] : '';     // Get image URL

// Validate data (basic validation)
if (empty($email) || empty($roomName) || empty($bookingDate)) {
  die(json_encode(array("success" => false, "message" => "Missing required fields.")));
}

// Sanitize the data (important to prevent SQL injection)
$email = mysqli_real_escape_string($conn, $email);
$roomName = mysqli_real_escape_string($conn, $roomName);
$bookingDate = mysqli_real_escape_string($conn, $bookingDate);
$roomPrice = mysqli_real_escape_string($conn, $roomPrice);
$roomDescription = mysqli_real_escape_string($conn, $roomDescription); // Sanitize
$roomLocation = mysqli_real_escape_string($conn, $roomLocation);     // Sanitize
$roomImageUrl = mysqli_real_escape_string($conn, $roomImageUrl);       // Sanitize


// Check if the room is already booked for the given date
$checkSql = "SELECT * FROM bookings WHERE room_name = '$roomName' AND booking_date = '$bookingDate'"; // Changed to room_name
$checkResult = mysqli_query($conn, $checkSql);

if (mysqli_num_rows($checkResult) > 0) {
    die(json_encode(array("success" => false, "message" => "Room is already booked for this date.")));
}
// Start a transaction
mysqli_begin_transaction($conn);

// SQL query to insert booking data into the bookings table
$sql = "INSERT INTO bookings (email, room_name, booking_date, room_price, room_description, room_location, room_image_url) VALUES ('$email', '$roomName', '$bookingDate', '$roomPrice', '$roomDescription', '$roomLocation', '$roomImageUrl')"; // Include all room details

if (mysqli_query($conn, $sql) === TRUE) {
  // If the booking is successful, commit the transaction
  mysqli_commit($conn);
  echo json_encode(array("success" => true, "message" => "Room booked successfully."));
} else {
  // If there's an error, roll back the transaction
  mysqli_rollback($conn);
  echo json_encode(array("success" => false, "message" => "Error booking room: " . mysqli_error($conn)));
}

mysqli_close($conn);
?>
