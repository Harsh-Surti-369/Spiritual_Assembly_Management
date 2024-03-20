<?php
// session_start();
// include('../php/dbConnect.php');

// // Check if the user is logged in
// if (!isset($_SESSION['devotee_id'])) {
//     echo "User not logged in.";
//     exit();
// }

// // Get the content ID and type from the POST request
// if (!isset($_POST['id']) || !isset($_POST['type'])) {
//     echo "Content ID or type not provided.";
//     exit();
// }

// $devotee_id = $_SESSION['devotee_id'];
// $content_id = $_POST['id'];
// $content_type = $_POST['type'];

// // Insert the content ID and type into the favorites table
// $query = "INSERT INTO favorites (devotee_id, id, type) VALUES (?, ?, ?)";
// $stmt = $conn->prepare($query);
// $stmt->bind_param("iis", $devotee_id, $content_id, $content_type);

// if ($stmt->execute()) {
//     echo "Added to favorites successfully.";
// } else {
//     echo "Error adding to favorites: " . $conn->error;
// }

// $stmt->close();
// $conn->close();
