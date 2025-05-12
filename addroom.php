<?php
require_once 'config.php';


// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data from the request
    $roomNumber = $_POST['room_number'] ?? '';
    $capacity = $_POST['capacity'] ?? '';
    $roomCost = $_POST['room_cost'] ?? '';
    $roomDescription = $_POST['room_description'] ?? '';
    $location = $_POST['location'] ?? '';

    // Handle image upload
    $uploadDir = 'uploads/'; // Directory to store uploaded images (must be writable)
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            echo json_encode(['error' => 'Failed to create upload directory. Check server permissions.']);
            $conn->close();
            exit();
        }
    }
    $roomImageUrl = '';
    if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] === UPLOAD_ERR_OK) {
        $tempFile = $_FILES['room_image']['tmp_name'];
        $fileName = basename($_FILES['room_image']['name']);
        $uniqueFileName = uniqid() . '_' . $fileName;
        $destination = $uploadDir . $uniqueFileName;

        if (move_uploaded_file($tempFile, $destination)) {
            $roomImageUrl = $destination; // Store the path to the uploaded image
        } else {
            echo json_encode(['error' => 'Failed to upload image. Check server permissions.']);
            $conn->close();
            exit();
        }
    }

    // Validate required fields
    if (empty($roomNumber) || empty($capacity) || empty($roomCost)) {
        echo json_encode(['error' => 'Room number, capacity, and cost are required.']);
        $conn->close();
        exit();
    }

    // Prepare and execute the SQL query
    $sql = "INSERT INTO rooms (room_number, capacity, room_cost, room_description, location, room_image_url) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siisss", $roomNumber, $capacity, $roomCost, $roomDescription, $location, $roomImageUrl);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Room added successfully']);
    } else {
        echo json_encode(['error' => 'Error adding room: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid request method']);
}

$conn->close();

?>