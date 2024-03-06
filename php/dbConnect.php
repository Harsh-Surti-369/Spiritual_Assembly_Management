<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "prabodham_weekly_assembly";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
