<?php
include('dbConnect.php');
session_start();


$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT password FROM super_admin WHERE email = '$email'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $hashedPassword = $row['password'];

    // Verify the provided password against the hashed password
    if (password_verify($password, $hashedPassword)) {
        // Password is correct, super admin authenticated successfully
        // Store email in session
        $_SESSION['super_admin_email'] = $email;
        echo "Login successful";
    } else {
        // Password is incorrect
        echo "Invalid email or password";
    }
} else {
    // Email not found in the database
    echo "Invalid email or password";
}

$conn->close();
