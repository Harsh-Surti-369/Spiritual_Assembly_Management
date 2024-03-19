<?php
session_start();
include('../php/dbConnect.php');

// Check if the user is logged in
if (!isset($_SESSION['devotee_id'])) {
    echo "User not logged in.";
    exit();
}

// Get the content ID from the POST request
if (!isset($_POST['id'])) {
    echo "Content ID not provided.";
    exit();
}

$devotee_id = $_SESSION['devotee_id'];
$content_id = $_POST['id'];

// Insert the content ID into the favourites table
$query = "INSERT INTO favorites (devotee_id, id) VALUES (?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $devotee_id, $content_id);

if ($stmt->execute()) {
    echo "Added to favourites successfully.";
} else {
    echo "Error adding to favourites: " . $conn->error;
}

$stmt->close();
$conn->close();
