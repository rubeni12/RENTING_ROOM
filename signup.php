
<?php
require 'config.php';


$fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$contact = mysqli_real_escape_string($conn, $_POST['contact']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

if (empty($fullname) || empty($email) || empty($contact) || empty($password)) {
    $response = ['success' => false, 'message' => 'All fields are required.'];
    echo json_encode($response);
    mysqli_close($conn);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response = ['success' => false, 'message' => 'Invalid email format.'];
    echo json_encode($response);
    mysqli_close($conn);
    exit;
}
if (strlen($contact) < 10) {
    $response = ['success' => false, 'message' => 'Invalid contact number.'];
    echo json_encode($response);
    mysqli_close($conn);
    exit;
}
if (strlen($password) < 6) {
    $response = ['success' => false, 'message' => 'Password must be at least 6 characters.'];
    echo json_encode($response);
    mysqli_close($conn);
    exit;
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$check_email_query = "SELECT * FROM users WHERE email = '$email'";
$check_email_result = mysqli_query($conn, $check_email_query);

if (mysqli_num_rows($check_email_result) > 0) {
    $response = ['success' => false, 'message' => 'Email already exists.'];
    echo json_encode($response);
    mysqli_close($conn);
    exit;
}


$insert_query = "INSERT INTO users (fullname, email, contact, password) VALUES ('$fullname', '$email', '$contact', '$hashed_password')";

if (mysqli_query($conn, $insert_query)) {
    $response = ['success' => true, 'message' => 'User registered successfully.'];
    echo json_encode($response);
} else {
    $response = ['success' => false, 'message' => 'Failed to register user: ' . mysqli_error($conn)];
    echo json_encode($response);
}
mysqli_close($conn);

?>
