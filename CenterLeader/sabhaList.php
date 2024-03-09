<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sabha List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #EFECEC;
        }

        .container {
            background-color: #FFFFFF;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .sabha-card {
            border: none;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .sabha-card-header {
            background-color: #FC6736;
            color: #FFFFFF;
            border-radius: 10px 10px 0 0;
            padding: 10px;
            font-weight: bold;
            text-align: center;
        }

        .sabha-card-body {
            padding: 20px;
            color: #0C2D57;
        }

        .btn-take-attendance {
            background-color: #0C2D57;
            border: none;
            color: #FFFFFF;
        }

        .btn-take-attendance:hover {
            background-color: #FC6736;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="text-center mb-4">Sabha List</h2>
        <div class="row">
            <?php
            session_start();
            include('../php/dbConnect.php');

            $center_id = $_SESSION['center_id'];

            $sql = "SELECT * FROM tbl_sabha WHERE center_id = $center_id";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $sabha_id = $row['sabha_id'];
                    $title = $row['title'];
                    $description = $row['description'];
                    $speaker = $row['speaker'];
                    $date = $row['date'];
                    $timing_from = $row['timing_from'];
                    $timing_to = $row['timing_to'];
                    $location = $row['location'];

                    // Check if attendance is already taken for this sabha
                    $sql_attendance = "SELECT * FROM tbl_attendance WHERE sabha_id = $sabha_id";
                    $result_attendance = $conn->query($sql_attendance);
                    $attendance_taken = ($result_attendance && $result_attendance->num_rows > 0) ? true : false;
            ?>
                    <div class="col-md-4">
                        <div class="card sabha-card">
                            <div class="card-header sabha-card-header">
                                <?php echo $title; ?>
                            </div>
                            <div class="card-body sabha-card-body">
                                <p><strong>Description:</strong> <?php echo $description; ?></p>
                                <p><strong>Speaker:</strong> <?php echo $speaker; ?></p>
                                <p><strong>Date:</strong> <?php echo $date; ?></p>
                                <p><strong>Timing:</strong> <?php echo $timing_from . ' - ' . $timing_to; ?></p>
                                <p><strong>Location:</strong> <?php echo $location; ?></p>
                                <?php
                                // Check if attendance has been taken for this sabha
                                $sql_attendance = "SELECT * FROM tbl_attendance WHERE sabha_id = $sabha_id";
                                $result_attendance = $conn->query($sql_attendance);
                                $attendance_taken = ($result_attendance && $result_attendance->num_rows > 0) ? true : false;

                                if (!$attendance_taken) {
                                    // Attendance not taken, display link to take attendance
                                    echo "<a href='takeAttendance.php?sabha_id=<?php echo $sabha_id; ?>' class='btn btn-take-attendance'>Take Attendance</a>";
                                } else {
                                    // Attendance already taken, display message
                                    echo "<p class='text-success'>Attendance already taken</p>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<p>No sabhas found.</p>";
            }
            ?>
        </div>
    </div>
</body>

</html>