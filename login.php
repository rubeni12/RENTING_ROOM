<?php
include 'config.php';

// Get the email and password from the POST request
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
  // Verify the password (use password_verify() if you're using hashed passwords)
  if (password_verify($password, $row["password"])) {
    $response = array('success' => true, 'message' => 'Login successful!');
  } else {
    $response = array('success' => false, 'message' => 'Invalid password!');
  }
} else {
  $response = array('success' => false, 'message' => 'User not found!');
}

// Send the JSON response back to the client
header('Content-Type: application/json');
echo json_encode($response);

$stmt->close();
$conn->close();
?>