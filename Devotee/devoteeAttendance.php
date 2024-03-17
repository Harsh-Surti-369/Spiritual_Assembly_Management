<?php
session_start();
include('../php/dbConnect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the selected period from the form
    $period = $_POST['period'];
    $devote_id = $_SESSION['devotee_id']; // Assuming devotee ID is stored in session

    // Define variables to store attendance data
    $totalSabhas = 0;
    $presentCount = 0;

    // Get the start date for the selected period
    switch ($period) {
        case 'last_month':
            $startDate = date('Y-m-d', strtotime('first day of last month'));
            break;
        case 'last_year':
            $startDate = date('Y-m-d', strtotime('first day of last year'));
            break;
        case 'lifetime':
        default:
            // Fetch the joining date of the devotee from the database
            $joinDateQuery = "SELECT joining_date FROM tbl_devotee WHERE devotee_id = ?";
            $joinDateStmt = $conn->prepare($joinDateQuery);
            $joinDateStmt->bind_param("i", $devote_id);
            $joinDateStmt->execute();
            $joinDateResult = $joinDateStmt->get_result();
            if ($joinDateRow = $joinDateResult->fetch_assoc()) {
                $startDate = $joinDateRow['joining_date'];
            } else {
                // Default to empty string if joining date not found
                $startDate = "";
            }
            break;
    }

    // Fetch attendance data from the database
    $sql = "SELECT COUNT(*) AS total_sabhas, 
                   SUM(CASE WHEN attendance_status = 'Present' THEN 1 ELSE 0 END) AS present_count
            FROM tbl_attendance 
            WHERE devotee_id = ? AND sabha_id IN (
                SELECT sabha_id FROM tbl_sabha WHERE date >= ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $devote_id, $startDate);
    $stmt->execute();
    $result = $stmt->get_result();

    // Retrieve the attendance data
    if ($row = $result->fetch_assoc()) {
        $totalSabhas = $row['total_sabhas'];
        $presentCount = $row['present_count'];
    }

    // Calculate the attendance percentage
    $attendancePercentage = ($totalSabhas > 0) ? round(($presentCount / $totalSabhas) * 100, 2) : 0;

    // Prepare chart data
    $dataPoints = array(
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        /* Custom CSS for styling */
        #attendanceComponent {
            width: 30vw;
            height: 30vw;
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
        <form id="attendanceForm" action="devoteeAttendance.php" method="post">
            <div class="form-group">
                <label for="timePeriod">Select Time Period:</label>
                <select class="form-control" id="timePeriod" name="period" onchange="this.form.submit()">
                    <option value="last_month">Last Month</option>
                    <option value="last_year">Last Year</option>
                    <option value="lifetime">Lifetime</option>
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