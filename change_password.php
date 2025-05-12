<?php

include 'config.php';

// Check if the necessary POST parameters are present
if (isset($_POST['user_id'], $_POST['current_password'], $_POST['new_password'])) {
    $userId = $_POST['user_id'];
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];

    // Check if the database connection is successful
    if ($conn === false) {
        echo json_encode(array('success' => false, 'message' => 'Database connection failed'));
        exit;
    }

    // Use prepared statements to prevent SQL injection
    $stmt = mysqli_prepare($conn, "SELECT password FROM users WHERE id = ?"); 
    if ($stmt === false) {
        echo json_encode(array('success' => false, 'message' => 'Error preparing query: ' . mysqli_error($conn)));
        exit;
    }
    
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $hashedPasswordFromDB);
    mysqli_stmt_fetch($stmt);
    
    // Check if a result was returned (User exists)
    if ($hashedPasswordFromDB === null) {
        echo json_encode(array('success' => false, 'message' => 'User not found.'));
        mysqli_stmt_close($stmt);
        exit;
    }

    mysqli_stmt_close($stmt);

    // Verify the current password
    if (password_verify($currentPassword, $hashedPasswordFromDB)) {
        // Hash the new password
        $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the password in the database
        $stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
        if ($stmt === false) {
            echo json_encode(array('success' => false, 'message' => 'Error preparing update query: ' . mysqli_error($conn)));
            exit;
        }

        mysqli_stmt_bind_param($stmt, "si", $hashedNewPassword, $userId);

        // Execute the update query
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(array('success' => true, 'message' => "Password changed successfully"));
        } else {
            echo json_encode(array('success' => false, 'message' => "Error updating password: " . mysqli_error($conn)));
        }

        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(array('success' => false, 'message' => "Incorrect current password"));
    }
} else {
    echo json_encode(array('success' => false, 'message' => "Missing parameters: user_id, current_password, or new_password"));
}

?>
