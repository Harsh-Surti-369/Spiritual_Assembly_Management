<?php
session_start();
include('../php/dbConnect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the sabha ID from the URL
    if (isset($_GET['sabha_id'])) {
        $sabha_id = $_GET['sabha_id'];
        $sabha_summary = $_POST['sabhaSummary'];

        foreach ($_POST as $key => $value) {
            // Check if the key represents a devotee's attendance
            if (strpos($key, 'devotee_') !== false) {
                // Extract the devotee ID from the key
                $devotee_id = substr($key, strlen('devotee_'));

                // Determine the attendance status
                $attendance_status = ($value == 'present') ? 1 : 0; // Assuming 1 represents present and 0 represents absent

                // Check if attendance record already exists for the sabha and devotee
                $sql_check_attendance = "SELECT * FROM tbl_attendance WHERE sabha_id = '$sabha_id' AND devotee_id = '$devotee_id'";
                $result_check_attendance = $conn->query($sql_check_attendance);

                if ($result_check_attendance->num_rows == 0) {
                    // If no attendance record exists, insert a new one
                    $sql_insert_attendance = "INSERT INTO tbl_attendance (sabha_id, devotee_id, attendance_status, description) VALUES ('$sabha_id', '$devotee_id', '$attendance_status', '$sabha_summary')";
                    if ($conn->query($sql_insert_attendance) === TRUE) {
                        echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                                <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
                                </div>";
                    } else {
                        echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
                        </div>";
                    }
                } else {
                    // If an attendance record already exists, update it
                    $sql_update_attendance = "UPDATE tbl_attendance SET attendance_status = '$attendance_status', description = '$sabha_summary' WHERE sabha_id = '$sabha_id' AND devotee_id = '$devotee_id'";
                    if ($conn->query($sql_update_attendance) === TRUE) {
                        echo "Attendance for devotee ID $devotee_id updated successfully.";
                    } else {
                        echo "Error updating attendance for devotee ID $devotee_id: " . $conn->error;
                    }
                }
            }
        }

        // Update the sabha summary in the respective table (replace 'tbl_attendance' with the correct table name)
        $sql_update_summary = "UPDATE tbl_attendance SET description = '$sabha_summary' WHERE sabha_id = '$sabha_id'";
        // Execute the SQL query for updating sabha summary
        if ($conn->query($sql_update_summary) === TRUE) {
            echo "Sabha summary updated successfully.";
        } else {
            echo "Error updating sabha summary: " . $conn->error;
        }
    } else {
        // Handle the case when sabha ID is missing in the URL
        echo "Error: Sabha ID is missing in the URL.";
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
    <?php include('header.php'); ?>
    <div class="container" stye="padding-top:50px;">
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