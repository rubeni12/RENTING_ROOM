<?php
// Set CORS headers
header("Access-Control-Allow-Origin: http://localhost:52966"); // Badilisha kama URL yako ya front-end inabadilika
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
header("Cache-Control: no-cache, must-revalidate, max-age=0"); // Ensure no cache

// Handle OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit; // If it's a CORS pre-flight request, simply exit here
}

// Include database config
include 'config.php';

// Ensure table 'messages' exists, if not create it
$table_query = "CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if (!$conn->query($table_query)) {
    echo json_encode(['success' => false, 'message' => 'Failed to create messages table: ' . $conn->error]);
    exit;
}

// Handle GET request to fetch messages
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT username, message, created_at FROM messages ORDER BY id DESC";
    $result = $conn->query($sql);

    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }

    echo json_encode($messages);
    exit;
}

// Handle POST request to insert new message
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // For JSON input, decode the JSON body
    $input_data = json_decode(file_get_contents('php://input'), true);
    
    $username = $input_data['username'] ?? '';
    $message = $input_data['message'] ?? '';

    if (empty($username) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Username and message required.']);
        exit;
    }

    // Insert new message into database
    $stmt = $conn->prepare("INSERT INTO messages (username, message) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $message);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Message sent successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send message.']);
    }

    exit;
}
?>
