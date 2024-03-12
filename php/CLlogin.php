<?php
session_start();
include('dbConnect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT leader_id, email, password FROM tbl_leader WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row['password'];

        if (password_verify($password, $hashedPassword)) {
            // Fetch the center_id associated with the leader_id
            $leader_id = $row['leader_id'];
            $sql_fetch_center_id = "SELECT center_id FROM tbl_leader WHERE leader_id = '$leader_id'";
            $result_center_id = $conn->query($sql_fetch_center_id);

            if ($result_center_id->num_rows > 0) {
                $row_center_id = $result_center_id->fetch_assoc();
                $center_id = $row_center_id['center_id'];
                $_SESSION['leader_id'] = $leader_id;
                $_SESSION['center_id'] = $center_id;

                $sql_check_details = "SELECT name,address,DOB,m_no,gender FROM tbl_leader WHERE leader_id = '$leader_id'";
                $result_check_details = $conn->query($sql_check_details);

                if ($result_check_details->num_rows > 0) {
                    // Details already inserted, redirect to another page
                    header("Location: ../centerleader/managedevotees.php");
                    exit();
                } else {
                    header("Location: ../CenterLeader/leaderDetail.html");
                    exit();
                }
            } else {
                echo "Failed to fetch center ID.";
            }
        } else {
            echo "Invalid email or password";
        }
    } else {
        echo "Invalid email or password";
    }
}
