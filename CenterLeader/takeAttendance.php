<?php
session_start();
include('../php/dbConnect.php');

// Check if leader is logged in
if (!isset($_SESSION['leader_id'])) {
    header("Location: login.php");
    exit;
}

$messages = array(); // Array to store all messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the sabha ID from the URL
    if (isset($_GET['sabha_id'])) {
        $sabha_id = $_GET['sabha_id'];
        $sabha_summary = $_POST['sabhaSummary'];

        foreach ($_POST as $key => $value) {
            if (strpos($key, 'devotee_') !== false) {
                $devotee_id = substr($key, strlen('devotee_'));

                // Determine the attendance status
                $attendance_status = ($value == 'present') ? 1 : 0;

                // Check if attendance record already exists for the sabha and devotee
                $sql_check_attendance = "SELECT * FROM tbl_attendance WHERE sabha_id = '$sabha_id' AND devotee_id = '$devotee_id'";
                $result_check_attendance = $conn->query($sql_check_attendance);

                if ($result_check_attendance->num_rows == 0) {
                    // If no attendance record exists, insert a new one
                    $sql_insert_attendance = "INSERT INTO tbl_attendance (sabha_id, devotee_id, attendance_status, description) VALUES ('$sabha_id', '$devotee_id', '$attendance_status', '$sabha_summary')";
                    if ($conn->query($sql_insert_attendance) === TRUE) {
                        $messages[] = "Attendance record for devotee ID $devotee_id added successfully."; // Add success message to array
                    } else {
                        $messages[] = "Error adding attendance record for devotee ID $devotee_id: " . $conn->error; // Add error message to array
                    }
                } else {
                    // If an attendance record already exists, update it
                    $sql_update_attendance = "UPDATE tbl_attendance SET attendance_status = '$attendance_status', description = '$sabha_summary' WHERE sabha_id = '$sabha_id' AND devotee_id = '$devotee_id'";
                    if ($conn->query($sql_update_attendance) === TRUE) {
                        $messages[] = "Attendance record for devotee ID $devotee_id updated successfully."; // Add success message to array
                    } else {
                        $messages[] = "Error updating attendance record for devotee ID $devotee_id: " . $conn->error; // Add error message to array
                    }
                }
            }
        }

        // Update the sabha summary in the respective table (replace 'tbl_attendance' with the correct table name)
        $sql_update_summary = "UPDATE tbl_attendance SET description = '$sabha_summary' WHERE sabha_id = '$sabha_id'";
        // Execute the SQL query for updating sabha summary
        if ($conn->query($sql_update_summary) === TRUE) {
            $messages[] = "Sabha summary updated successfully."; // Add success message to array
        } else {
            $messages[] = "Error updating sabha summary: " . $conn->error; // Add error message to array
        }
    } else {
        // Handle the case when sabha ID is missing in the URL
        $messages[] = "Error: Sabha ID is missing in the URL."; // Add error message to array
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" defer></script>
    <link rel="stylesheet" href="../css/CenterLeader/takeAttendance.css">
    <link rel="stylesheet" href="../css/CenterLeader/header.css">
    <style>
        .devotee-box {
            border: 1px solid #0C2D57;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <header style="margin-bottom: 150px;">
        <?php include('header.php'); ?>
    </header>
    <div class="container" style="padding-top:50px;">
        <h2 class="text-center mb-4">Take Attendance</h2>
        <form id="attendanceForm" action="takeAttendance.php?sabha_id=<?php echo $_GET['sabha_id']; ?>" method="post">
            <div class="form-group">
                <label>Devotees</label>
                <div class="devotee-list" id="devoteeList">
                    <?php
                    // PHP code to fetch devotees and display the form
                    $center_id = $_SESSION['center_id'];
                    $sql = "SELECT * FROM tbl_devotee WHERE center_id = '$center_id'";
                    $result = $conn->query($sql);

                    if ($result !== false && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $devotee_name = $row['name'];
                            $devotee_id = $row['devotee_id'];
                    ?>
                            <div class="devotee-box" id="devotee_<?php echo $devotee_id; ?>" onclick="toggleAttendance(this)">
                                <?php echo $devotee_name; ?>
                                <input type="hidden" name="devotee_<?php echo $devotee_id; ?>" id="attendance_<?php echo $devotee_id; ?>" value="present">
                            </div>
                    <?php
                        }
                    } else {
                        echo "<p>No devotees found.</p>";
                    }
                    ?>
                </div>
            </div>
            <div class="form-group">
                <label for="sabhaSummary">Sabha Summary</label>
                <textarea class="form-control" id="sabhaSummary" name="sabhaSummary" rows="3" placeholder="Write a summary of the sabha"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Attendance</button>
        </form>
    </div>

    <!-- Display Bootstrap toasts for messages -->
    <?php foreach ($messages as $message) { ?>
        <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true">
            <div class="toast-header">
                <strong class="me-auto">Message</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <?php echo $message; ?>
            </div>
        </div>
    <?php } ?>

    <script>
        function toggleAttendance(element) {
            if (element.style.backgroundColor === 'green') {
                element.style.backgroundColor = 'red';
                // Change hidden input value to 'absent'
                document.getElementById('attendance_' + element.id.split('_')[1]).value = 'absent';
            } else {
                element.style.backgroundColor = 'green';
                // Change hidden input value to 'present'
                document.getElementById('attendance_' + element.id.split('_')[1]).value = 'present';
            }
        }
    </script>
</body>

</html>