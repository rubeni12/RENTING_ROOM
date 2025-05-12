<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once 'config.php'; // hakikisha $conn ipo

$sql = "SELECT room_name, room_price, room_description, room_location, room_image_url FROM bookings ORDER BY id DESC";
$result = $conn->query($sql);

$rooms = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rooms[] = [
            'name' => $row['room_name'],
            'price' => $row['room_price'],
            'description' => $row['room_description'],
            'location' => $row['room_location'],
            'imageUrl' => $row['room_image_url']
        ];
    }

    echo json_encode(['success' => true, 'rooms' => $rooms]);
} else {
    echo json_encode(['success' => false, 'message' => 'No rooms found.']);
}

?>
