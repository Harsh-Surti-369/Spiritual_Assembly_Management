<?php
// Include database connection
include('dbConnect.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query to check if the email exists in tbl_leader
    $sql = "SELECT leader_id, email, password FROM tbl_leader WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Email exists, now verify password
        $row = $result->fetch_assoc();
        $hashedPassword = $row['password'];

        if (password_verify($password, $hashedPassword)) {
            // Password is correct, set session variable and redirect
            session_start();
            $_SESSION['leader_id'] = $row['leader_id'];
            header("Location: profile.php"); // Redirect to center leader's profile page
            exit();
        } else {
            // Password is incorrect
            echo "Invalid email or password";
        }
    } else {
        // Email not found in database
        echo "Invalid email or password";
    }
}
