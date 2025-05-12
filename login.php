<?php
include 'config.php';

// Check if POST data is set
if (isset($_POST['email']) && isset($_POST['password'])) {
  $email = $_POST['email'];
  $password = $_POST['password'];

  // Use prepared statements to prevent SQL injection
  $sql = "SELECT * FROM users WHERE email = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $email);  // "s" indicates a string parameter
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
        // Verify the password (use password_verify() for hashed passwords)
        if (password_verify($password, $row["password"])) {
            // Login successful
            http_response_code(200); // OK
            $response = array(
                'success' => true,
                'message' => 'Login successful!',
                // 'user_id' => $row['user_id'] // <-- Send the unique user_id back to Flutter
                // You can also send the internal 'id' if your profile API uses that:
                // 'id' => $row['id']
            );
        } else {
            // Invalid password
            http_response_code(401); // Unauthorized
            $response = array('success' => false, 'message' => 'Invalid password!');
        }
  } else {
    $response = array('success' => false, 'message' => 'User not found!');
  }

  // Ensure response is properly formatted as JSON
  header('Content-Type: application/json');
  echo json_encode($response);
} else {
  // Handle case where email or password is missing in POST
  $response = array('success' => false, 'message' => 'Email and password are required.');
  header('Content-Type: application/json');
  echo json_encode($response);
}

?>
