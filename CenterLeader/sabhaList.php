<?php session_start();; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/CenterLeader/header.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" defer></script>
    <title>Sabha List</title>
    <style>
        body {
            background-color: #EFECEC;
            color: #0C2D57;
        }

        .sabha-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
        }

        .sabha-card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .sabha-card-header {
            background-color: #0C2D57;
            color: #EFECEC;
            font-weight: bold;
            padding: 1rem;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .sabha-card-body {
            padding: 1.5rem;
        }

        .btn-take-attendance {
            background-color: #FC6736;
            color: #EFECEC;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-take-attendance:hover {
            background-color: #e05b2d;
        }

        .text-success {
            color: #0C2D57;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <?php include('header.php'); ?>
    <div class="container" stye="padding-top:50px;">
        <h2 class="text-center mb-4">Sabha List</h2>
        <div class="row">
            <?php include('../php/dbConnect.php');
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
                    $sql_attendance = "SELECT * FROM tbl_attendance WHERE sabha_id = '$sabha_id'";
                    $result_attendance = $conn->query($sql_attendance);
                    $attendance_taken = ($result_attendance && $result_attendance->num_rows > 0) ? true : false;
            ?>
                    <div class="col-md-4 mb-4">
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
                                if (!$attendance_taken) {
                                    // Attendance not taken, display link to take attendance
                                    echo "<a href='takeAttendance.php?sabha_id=" . $sabha_id . "' class='btn btn-take-attendance'>Take Attendance</a>";
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

    <script>
        
    </script>
</body>

</html>