<?php
require_once 'config.php';
// // --- Configuration ---
// $db_host = 'localhost';      // Your database host (e.g., localhost or IP address)
// $db_user = 'root';           // Your database username
// $db_pass = '';               // Your database password
// $db_name = 'your_database_name'; // Replace with your database name

// // --- Set Content-Type Header for JSON response and enable CORS ---
// header('Content-Type: application/json');
// header('Access-Control-Allow-Origin: *'); // For development, allow requests from any origin.
//                                           // For production, restrict to your Flutter app's domain/origin.
// header('Access-Control-Allow-Methods: GET'); // Allow only GET requests
// header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Allow specific headers

// // --- Check if it's an OPTIONS request (pre-flight for CORS) ---
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// --- Connect to MySQL database ---
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check connection
if (!$conn) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit();
}

// --- Get user_id from the request URL query parameter (e.g., ?user_id=user123) ---
$requested_user_id = null;

if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
    $requested_user_id = $_GET['user_id'];
}

if (!$requested_user_id) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'User ID is required.']);
    mysqli_close($conn);
    exit();
}

// --- Sanitize input to prevent SQL injection using prepared statements ---
$sql = "SELECT id, user_id, name, email, contact FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt === false) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'SQL prepare failed: ' . mysqli_error($conn)]);
    mysqli_close($conn);
    exit();
}

// Bind the parameter (the user_id) to the prepared statement
mysqli_stmt_bind_param($stmt, "s", $requested_user_id); // "s" denotes string type

// Execute the prepared statement
mysqli_stmt_execute($stmt);

// Get the result set
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        // User found, return user data
        http_response_code(200); // OK
        echo json_encode([
            'id' => $user['id'],
            'user_id' => $user['user_id'], // Include if needed by Flutter
            'name' => $user['name'],
            'email' => $user['email'],
            'contact' => $user['contact']
        ]);
    } else {
        // User not found
        http_response_code(404); // Not Found
        echo json_encode(['error' => 'User not found.']);
    }
    mysqli_free_result($result); // Free result set
} else {
    // Query execution failed
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Query execution failed: ' . mysqli_error($conn)]);
}

// --- Close statement and connection ---
mysqli_stmt_close($stmt);
mysqli_close($conn);

?>