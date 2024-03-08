<?php
// Start the session
session_start();

// Check if the leader ID is set in the session
if(isset($_SESSION['leader_id'])) {
    // Retrieve the leader ID from the session
    $leader_id = $_SESSION['leader_id'];

    // Check if the form has been submitted
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $name = $_POST['name'];
        $mobile = $_POST['mobile'];
        $dob = $_POST['dob'];
        $gender = $_POST['gender'];

        // Your database connection code here
        include('dbConnect.php'); // Assuming you have a file with your database connection code

        // Prepare and execute SQL statement to update tbl_leader
        $sql = "UPDATE tbl_leader SET name=?, m_no=?, dob=?, gender=? WHERE leader_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $mobile, $dob, $gender, $leader_id);
        $stmt->execute();

        // Check if the update was successful
        if($stmt->affected_rows > 0) {
            // Update successful
            echo "Leader details updated successfully.";
        } else {
            // Update failed
            echo "Error updating leader details: " . $conn->error;
        }

        // Close statement and database connection
        $stmt->close();
        $conn->close();
    }
} else {
    // Redirect the user if leader ID is not set in the session
    header("Location: login.php"); // Adjust the URL as needed
    exit();
}
?>
