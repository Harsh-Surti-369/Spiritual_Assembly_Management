<?php

include('dbConnect.php');

// Retrieve form data
$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT password FROM tbl_devotee WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 1) {
    $stmt->bind_result($hashedPassword);
    $stmt->fetch();

    if (password_verify($password, $hashedPassword)) {
        // Password is correct
        echo "Login successful";
        // You can redirect the user to another page or perform other actions here
    } else {
        // Password is incorrect
        echo "Invalid email or password";
    }
} else {
    // Email not found in the database
    echo "Invalid email or password";
}

// Close statement and connection
$stmt->close();
$conn->close();
