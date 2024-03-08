<?php
session_start();
// Include the database connection file
require('dbConnect.php');

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM tbl_devotee WHERE email = '$email'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 1) {
    // User found, verify password
    $row = mysqli_fetch_assoc($result);
    if (password_verify($password, $row['password'])) {
        // Password is correct, set session variables and redirect
        $_SESSION['devotee_id'] = $row['devotee_id'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['login_success'] = "Login successful";
        echo "Login success!!!";
    } else {
        // Password is incorrect
        $_SESSION['login_error'] = "Invalid email or password";
        header("Location: ../devotee/login.html");
        exit();
    }
} else {
    // User not found
    $_SESSION['login_error'] = "Invalid email or password";
    header("Location: ../devotee/login.html");
    exit();
}

mysqli_close($conn);
