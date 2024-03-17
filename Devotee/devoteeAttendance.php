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



// Get the start and end dates for the selected period
switch ($period) {
    case 'last_month':
        $currentDate = new DateTime();
        $currentDate->modify('last month');
        $lastMonthSameDate = $currentDate->format('Y-m-d');
        $startDate = $lastMonthSameDate;
        // $ = date('Y-m-d', strtotime('-1 month')); // 30 days before today's date
        $endDate = date('Y-m-d'); // Today's date
        break;
    case 'last_year':
        $startDate = date('Y-m-d', strtotime('-1 year')); // 365 days before today's date
        $endDate = date('Y-m-d'); // Today's date
        break;
    default:
        // Fetch the joining date of the devotee from the database
        $joinDateQuery = "SELECT joining_date FROM tbl_devotee WHERE devotee_id = ?";
        $joinDateStmt = $conn->prepare($joinDateQuery);
        $joinDateStmt->bind_param("i", $devotee_id);
        $joinDateStmt->execute();
        $joinDateResult = $joinDateStmt->get_result();
        if ($joinDateRow = $joinDateResult->fetch_assoc()) {
            $startDate = $joinDateRow['joining_date'];
            $endDate = date('Y-m-d'); // Today's date
        } else {
            // Default to empty string if joining date not found
            $startDate = "";
            $endDate = date('Y-m-d'); // Today's date
        }
        break;
}

$sql = "SELECT COUNT(*) AS total_sabhas, SUM(CASE WHEN attendance_status = 'Present' THEN 1 ELSE 0 END) AS present_count,
            (SELECT COUNT(*) FROM tbl_sabha WHERE date BETWEEN ? AND ?) AS total_sabhas_in_period
            FROM tbl_attendance
            WHERE devotee_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $startDate, $endDate, $devotee_id);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    // Retrieve the attendance data
    if ($row = $result->fetch_assoc()) {
        $totalSabhas = $row['total_sabhas'];
        $presentCount = $row['present_count'];
        $totalSabhasInPeriod = $row['total_sabhas_in_period'];
        // print_r($row);
        // Calculate the attendance percentage
        if ($totalSabhasInPeriod > 0) {
            $attendancePercentage = round(($presentCount / $totalSabhas) * 100, 2);
        } else {
            $attendancePercentage = 100; // Set 100% if no sabhas in the given period
        }
    }
} else {
    // Log or display the error
    error_log("Error executing SQL query: " . $stmt->error);
}
// Retrieve the attendance data
if ($row = $result->fetch_assoc()) {
    $totalSabhas = $row['total_sabhas'];
    $presentCount = $row['present_count'];
}


// Prepare chart data
$dataPoints = array(
    array("label" => "Present", "y" => $attendancePercentage),
    array("label" => "Absent", "y" => 100 - $attendancePercentage)
);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        /* Custom CSS for styling */
        #attendanceComponent {
            width: 60vw;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #f8f9fa;
            padding: 20px;
            border-right: 1px solid #dee2e6;
        }

        #chartContainer {
            height: 70%;
        }

        #attendanceForm {
            margin-top: 20px;
        }

        h1 {
            margin-bottom: 20px;
            font-size: 24px;
        }

        #chartContainer .canvasjs-chart-canvas {
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <div id="attendanceComponent">
        <h1>Devotee Attendance</h1>
        <div id="chartContainer"></div>
        <form id="attendanceForm" method="get">
            <div class="form-group">
                <label for="timePeriod">Select Time Period:</label>
                <select class="form-control" id="timePeriod" name="period" onchange="this.form.action = 'devoteeAttendance.php?period=' + this.value; this.form.submit();">
                    <option value="last_month" <?php echo ($period == 'last_month') ? 'selected' : ''; ?>>Last Month</option>
                    <option value="last_year" <?php echo ($period == 'last_year') ? 'selected' : ''; ?>>Last Year</option>
                    <option value="lifetime" <?php echo ($period == 'lifetime') ? 'selected' : ''; ?>>Lifetime</option>
                </select>
            </div>
        </form>
    </div>

    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js" integrity="sha512-igl8WEUuas9k5dtnhKqyyld6TzzRjvMqLC79jkgT3z02FvJyHAuUtyemm/P/jYSne1xwFI06ezQxEwweaiV7VA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
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
                    dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
                }]
            });
            chart.render();
        });
    </script>
</body>

</html>