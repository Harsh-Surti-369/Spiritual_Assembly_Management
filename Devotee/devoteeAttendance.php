<?php
session_start();
include('../php/dbConnect.php');

// Get the selected period from the URL
$period = isset($_GET['period']) ? $_GET['period'] : 'lifetime';
$devotee_id = $_SESSION['devotee_id'];

// Define variables to store attendance data
$totalSabhas = 0;
$presentCount = 0;
$attendancePercentage = 0;

// Calculate start and end dates for the last month
$startDate = date('Y-m-d', strtotime('-30 days')); // 30 days before today
$endDate = date('Y-m-d'); // Today's date

// Fetch attendance data for the selected period
$sql = "SELECT COUNT(*) AS total_sabhas, SUM(CASE WHEN a.attendance_status = 'Present' THEN 1 ELSE 0 END) AS present_count
            FROM tbl_attendance a
            JOIN tbl_sabha s ON a.sabha_id = s.sabha_id
            WHERE a.devotee_id = ? AND s.date BETWEEN ? AND ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $devotee_id, $startDate, $endDate);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $totalSabhas = $row['total_sabhas'];
        $presentCount = $row['present_count'];
        if ($totalSabhas > 0) {
            $attendancePercentage = round(($presentCount / $totalSabhas) * 100, 2);
        } else {
            $attendancePercentage = 100; // Set 100% if no sabhas in the given period
        }
    }
} else {
    // Log or display the error
    error_log("Error executing SQL query: " . $stmt->error);
}

// Prepare chart data for Pie Chart
$dataPointsPie = array(
    array("label" => "Present", "y" => $attendancePercentage),
    array("label" => "Absent", "y" => 100 - $attendancePercentage)
);

// Prepare chart data for Column Chart
$dataPointsColumn = array();
$currentDate = new DateTime();
for ($i = 0; $i < 12; $i++) {
    $currentDate->modify("-1 month");
    $month = $currentDate->format('m');
    $year = $currentDate->format('Y');

    // Query to get the total number of sabhas attended by the devotee in the current month
    $attendanceQuery = "SELECT COUNT(*) AS total_sabhas
                        FROM tbl_attendance a
                        JOIN tbl_sabha s ON a.sabha_id = s.sabha_id
                        WHERE a.devotee_id = ? 
                        AND MONTH(s.date) = ? 
                        AND YEAR(s.date) = ?";
    $attendanceStmt = $conn->prepare($attendanceQuery);
    $attendanceStmt->bind_param("iii", $devotee_id, $month, $year);
    $attendanceStmt->execute();
    $attendanceResult = $attendanceStmt->get_result();

    if ($attendanceRow = $attendanceResult->fetch_assoc()) {
        $totalSabhas = $attendanceRow['total_sabhas'];
        // Push month and corresponding total sabhas to the data points array
        array_push($dataPointsColumn, array("label" => DateTime::createFromFormat('!m', $month)->format('F'), "y" => $totalSabhas));
    } else {
        // If no data found for the month, push 0 sabhas attended
        array_push($dataPointsColumn, array("label" => DateTime::createFromFormat('!m', $month)->format('F'), "y" => 0));
    }
}

$dataPoints = array();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body>
    <?php include('header.php'); ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <!-- <h1>Pie Chart</h1> -->
                <div id="chartContainerPie" style="height:0; width:0;"></div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-12">
                <!-- Column Chart Component -->
                <h1>Column Chart</h1>
                <div id="chartContainerColumn" style="height: 370px; width: 100%;"></div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-12">
                <!-- List Component -->
                <!-- <h1>Sabhas wise data</h1> -->
                <div id="sabhaDetailsComponent">
                    <?php
                    // Fetch sabha details and attendance status for the selected period
                    $sabhaDetailsQuery = "SELECT s.title, s.date, s.speaker, COALESCE(a.attendance_status, 'Absent') AS attendance_status, a.description
                        FROM tbl_sabha s
                        LEFT JOIN tbl_attendance a ON s.sabha_id = a.sabha_id AND a.devotee_id = ?
                        WHERE s.center_id = ? AND s.date BETWEEN ? AND ?
                        ORDER BY s.date DESC";

                    $sabhaDetailsStmt = $conn->prepare($sabhaDetailsQuery);
                    $sabhaDetailsStmt->bind_param("iiss", $devotee_id, $_SESSION['center_id'], $startDate, $endDate);
                    $sabhaDetailsStmt->execute();
                    $sabhaDetailsResult = $sabhaDetailsStmt->get_result();

                    if ($sabhaDetailsResult->num_rows > 0) {
                        $totalSabhas = $sabhaDetailsResult->num_rows;
                        echo "<p><strong>Total Sabhas: $totalSabhas</strong></p>";

                        while ($row = $sabhaDetailsResult->fetch_assoc()) {
                            $sabhaTitle = $row['title'];
                            $sabhaDate = date('Y-m-d', strtotime($row['date']));
                            $sabhaSpeaker = $row['speaker'];
                            $attendanceStatus = $row['attendance_status'];
                            $description = $row['description'];

                            // Output sabha details in HTML format
                            echo "<div class='sabha-details'>
                                <h3 class='title'>$sabhaTitle</h3>
                                <p><strong>Date:</strong> $sabhaDate</p>
                                <p><strong>Speaker:</strong> $sabhaSpeaker</p>
                                <p class='status'><strong>Attendance Status:</strong> $attendanceStatus</p>
                                <p><strong>Description:</strong> $description</p>
                            </div>";
                        }
                    } else {
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="../css/Devotee/devoteeAttendance.css">
    <script>
        // JavaScript code for rendering the charts
        $(document).ready(function() {
            // Pie Chart
            var chartPie = new CanvasJS.Chart("chartContainerPie", {
                animationEnabled: true,
                exportEnabled: true,
                title: {
                    text: "Attendance Percentage (Pie Chart)"
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
                    dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
                }]
            });
            chartPie.render();

            // Column Chart
            var chartColumn = new CanvasJS.Chart("chartContainerColumn", {
                animationEnabled: true,
                exportEnabled: true,
                title: {
                    text: "Attendance Percentage (Column Chart)"
                },
                subtitles: [{
                    text: "Column Chart"
                }],
                axisX: {
                    title: "Months"
                },
                axisY: {
                    title: "Attendance Count"
                },
                data: [{
                    type: "column",
                    dataPoints: <?php echo json_encode($dataPointsColumn, JSON_NUMERIC_CHECK); ?>
                }]
            });
            chartColumn.render();
        });
    </script>
</body>

</html>