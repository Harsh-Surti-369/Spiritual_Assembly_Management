<?php

// mysqli_close($conn);
session_start();
// Include the database connection file
require('dbConnect.php');

// Get the form data
$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];
$dob = $_POST['dob'];
$gender = $_POST['gender'];
$mobileNumber = $_POST['mobileNumber'];
$centerId = $_POST['spiritualCenter'];

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Prepare the SQL statement to insert the data into the database
$sql = "INSERT INTO tbl_devotee (name, email, password, dob, gender, mobile_number, center_id, joining_date) VALUES ('$name', '$email', '$hashedPassword', '$dob', '$gender', '$mobileNumber', '$centerId', NOW())";

if (mysqli_query($conn, $sql)) {
    // Redirect to the login page with success message
    $_SESSION['signup_success'] = "You have successfully signed up!";
    header("Location: ../devotee/login.html");
} else {
    // Redirect to the signup page with error message
    $_SESSION['signup_error'] = "Error: " . $sql . "<br>" . mysqli_error($conn);
    header("Location: ../devotee/signup.php");
}

// Close the database connection
mysqli_close($conn);
