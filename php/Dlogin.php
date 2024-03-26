<?php
session_start();

require('dbConnect.php');

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM tbl_devotee WHERE email = '$email'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 1) {

    $row = mysqli_fetch_assoc($result);
    if (password_verify($password, $row['password'])) {

        $_SESSION['devotee_id'] = $row['devotee_id'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['login_success'] = "Login successful";
        header("Location: ../devotee/index.php");
    } else {
        $_SESSION['login_error'] = "Invalid email or password";
        header("Location: ../devotee/login.html");
        exit();
    }
} else {

    $_SESSION['login_error'] = "Invalid email or password";
    header("Location: ../devotee/login.html");
    exit();
}

mysqli_close($conn);
