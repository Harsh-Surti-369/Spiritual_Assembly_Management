<?php
session_start();

require('dbConnect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['title'], $_POST['description'], $_POST['speaker'], $_POST['sabhaType'], $_POST['timingFrom'], $_POST['timingTo'], $_POST['location'], $_POST['date'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $speaker = $_POST['speaker'];
        $sabhaType = $_POST['sabhaType'];
        $timingFrom = $_POST['timingFrom'];
        $timingTo = $_POST['timingTo'];
        $location = $_POST['location'];
        $date = $_POST['date'];
        $center_id = $_SESSION['center_id']; // Retrieve center ID from session

        // Prepare and execute SQL query to insert Sabha information
        $sql_insert_sabha = "INSERT INTO tbl_sabha (title, description, speaker, sabha_type, timing_from, timing_to, location, date, center_id) 
                             VALUES ('$title', '$description', '$speaker', '$sabhaType', '$timingFrom', '$timingTo', '$location', '$date', '$center_id')";

        if ($conn->query($sql_insert_sabha) === TRUE) {
            echo "New Sabha created successfully";
        } else {
            echo "Error creating Sabha: " . $conn->error;
        }
    } else {
        echo "All required fields are not provided.";
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
