<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Attendance</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/CenterLeader/takeAttendance.css">
    <style>
        .devotee-box {
            background-color: #6abf69;
            /* Green color */
            padding: 10px;
            margin-bottom: 5px;
            cursor: pointer;
        }

        .devotee-box.absent {
            background-color: #ff6347;
            /* Red color */
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="text-center mb-4">Take Attendance</h2>
        <form id="attendanceForm" action="submitAttendance.php" method="post">
            <div class="form-group">
                <label>Devotees</label>
                <div class="devotee-list" id="devoteeList">
                    <!-- Devotees will be dynamically added here -->
                </div>
            </div>
            <div class="form-group">
                <label for="sabhaSummary">Sabha Summary</label>
                <textarea class="form-control" id="sabhaSummary" name="sabhaSummary" rows="3" placeholder="Write a summary of the sabha"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Attendance</button>
        </form>
    </div>
    <!-- <script src="../js/CenterLeader/takeAttendance.js"></script> -->
    <script>
        // Add event listener to toggle background color
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.devotee-box').forEach(function(devoteeBox) {
                devoteeBox.addEventListener('click', function() {
                    this.classList.toggle('absent');
                });
            });
        });
    </script>
</body>

</html>
<?php
// Process the attendance submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sabhaSummary = $_POST['sabhaSummary'];

    // Retrieve devotee attendance data
    $devoteeAttendance = [];
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'devotee_') === 0) {
            $devoteeId = substr($key, 8); // Extract devotee ID
            $attendanceStatus = ($value === 'present') ? 'present' : 'absent';
            $devoteeAttendance[$devoteeId] = $attendanceStatus;
        }
    }

    // Process devotee attendance data and update database accordingly
    // Your database update code goes here...

    // Provide feedback to the user
    echo "Attendance submitted successfully.";
} else {
    echo "Invalid request method.";
}
?>