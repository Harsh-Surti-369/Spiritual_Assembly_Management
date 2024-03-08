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

    if (password_verify($password, $hashedPassword)) {
        $_SESSION['super_admin_email'] = $email;
        header('Location:../superadmin/createcenter.html ');
    } else {
        echo "Invalid email or password";
    }
} else {
    echo "Invalid email or password";
}

$conn->close();
