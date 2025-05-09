<?php

include 'config.php';

if (isset($_POST['user_id'], $_POST['current_password'], $_POST['new_password'])) {
    $userId = $_POST['user_id'];
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];

    // Use prepared statements to prevent SQL injection
    $stmt = mysqli_prepare($conn, "SELECT password FROM users WHERE id = ?"); // Assuming your table is named 'users' and password field is 'password'
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $hashedPasswordFromDB);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Verify the current password
    if (password_verify($currentPassword, $hashedPasswordFromDB)) {
        // Hash the new password
        $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the password in the database
        $stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $hashedNewPassword, $userId);

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
mysqli_close($conn);
