<?php
session_start();
include('../php/dbConnect.php');

$period = isset($_GET['period']) ? $_GET['period'] : 'lifetime';
$devotee_id = $_GET['devotee_id'];

$totalSabhas = 0;
$presentCount = 0;
$attendancePercentage = 0;

switch ($period) {
    case 'last_month':
        $currentDate = new DateTime();
        $currentDate->modify('last month');
        $lastMonthSameDate = $currentDate->format('Y-m-d');
        $startDate = $lastMonthSameDate;
        $endDate = date('Y-m-d'); // Today's date
        break;
    case 'last_year':
        $startDate = date('Y-m-d', strtotime('-1 year'));
        $endDate = date('Y-m-d');
        break;
    default:
        $joinDateQuery = "SELECT joining_date FROM tbl_devotee WHERE devotee_id = ?";
        $joinDateStmt = $conn->prepare($joinDateQuery);
        $joinDateStmt->bind_param("i", $devotee_id);
        $joinDateStmt->execute();
        $joinDateResult = $joinDateStmt->get_result();
        if ($joinDateRow = $joinDateResult->fetch_assoc()) {
            $startDate = $joinDateRow['joining_date'];
            $endDate = date('Y-m-d'); // Today's date
        } else {
            $startDate = "";
            $endDate = date('Y-m-d'); // Today's date
        }
        break;
}

$sabhaDetailsQuery = "SELECT s.title, s.date, s.speaker, COALESCE(a.attendance_status, 'Absent') AS attendance_status, a.description
                      FROM tbl_sabha s
                      LEFT JOIN tbl_attendance a ON s.sabha_id = a.sabha_id AND a.devotee_id = ?
                      WHERE s.center_id = ? AND s.date BETWEEN ? AND ?
                      ORDER BY s.date DESC";

$sabhaDetailsStmt = $conn->prepare($sabhaDetailsQuery);
$sabhaDetailsStmt->bind_param("iiss", $devotee_id, $_SESSION['center_id'], $startDate, $endDate);
$sabhaDetailsStmt->execute();
$sabhaDetailsResult = $sabhaDetailsStmt->get_result();

$datapoints = array(
    array("label" => "Present", "y" => 0),
    array("label" => "Absent", "y" => 100)
);

if ($totalSabhas > 0) {
    $datapoints = array(
        array("label" => "Present", "y" => $attendancePercentage),
        array("label" => "Absent", "y" => 100 - $attendancePercentage)
    );
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/Devotee/devoteeAttendance.css">
    <link rel="stylesheet" href="../css/centerleader/header.css">
</head>

<body>
    <header style="margin-bottom: 150px;">
        <?php include('header.php'); ?>
    </header>
    <div id="attendanceComponent">
        <h1>Devotee Attendance</h1>
        <div class="toast" id="messageToast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Message</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastBody">
            </div>
        </div>
        <div id="chartContainer"></div>
        <form id="attendanceForm" method="get" action="attendanceDashboard.php">
            <input type="hidden" name="devotee_id" value="<?php // echo $devotee_id; 
                                                            ?>">
            <div class="form-group">
                <label for="timePeriod">Select Time Period:</label>
                <select class="form-control" id="timePeriod" name="period" onchange="this.form.submit();">
                    <option value="last_month" <?php // echo ($period == 'last_month') ? 'selected' : ''; 
                                                ?>>Last Month</option>
                    <option value="last_year" <?php //  echo ($period == 'last_year') ? 'selected' : ''; 
                                                ?>>Last Year</option>
                    <option value="lifetime" <?php // echo ($period == 'lifetime') ? 'selected' : ''; 
                                                ?>>Lifetime</option>
                </select>
            </div>
        </form>

        <div id="sabhaDetailsComponent">
            <h1>All sabhas</h1>
            <?php

            if ($sabhaDetailsResult->num_rows > 0) {
                $totalSabhas = $sabhaDetailsResult->num_rows;
                echo "<p><strong>Total Sabhas: $totalSabhas</strong></p>";

                while ($row = $sabhaDetailsResult->fetch_assoc()) {
                    $sabhaTitle = $row['title'];
                    $sabhaDate = date('Y-m-d', strtotime($row['date']));
                    $sabhaSpeaker = $row['speaker'];
                    $attendanceStatus = $row['attendance_status'];
                    $description = $row['description'];

                    echo "<div class='sabha-details'>
                            <h3 class='title'>$sabhaTitle</h3>
                            <p><strong>Date:</strong> $sabhaDate</p>
                            <p><strong>Speaker:</strong> $sabhaSpeaker</p>
                            <p class='status'><strong>Attendance Status:</strong> $attendanceStatus</p>
                            <p><strong>Description:</strong> $description</p>
                          </div>";
                }
            } else {
                echo "<div class='alert alert-warning' role='alert'>
                        No sabhas found for the selected period.
                      </div>";
            }
            ?>
        </div>
    </div>


    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js" integrity="sha512-igl8WEUuas9k5dtnhKqyyld6TzzRjvMqLC79jkgT3z02FvJyHAuUtyemm/P/jYSne1xwFI06ezQxEwweaiV7VA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        function showToast(message) {
            var toastBody = document.getElementById('toastBody');
            toastBody.innerText = message;
            var toast = new bootstrap.Toast(document.getElementById('messageToast'));
            toast.show();
        }

        $(document).ready(function() {
            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                exportEnabled: true,
                title: {
                    text: "Attendance Percentage"
                },
                subtitles: [{
                    text: "Pie Chart"
                }],
                data: [{
                    type: "pie",
                    showInLegend: true,
                    legendText: "{label}",
                    indexLabelFontSize: 16,
                    indexLabel: "{label} - #percent%",
                    yValueFormatString: "#0.#",
                    dataPoints: <?php // echo json_encode($datapoints, JSON_NUMERIC_CHECK); 
                                ?>
                }]
            });
            chart.render();
        });
    </script>
</body>

</html>