<?php
session_start();
include('../php/dbConnect.php');

// Get the selected period from the URL
$period = isset($_GET['period']) ? $_GET['period'] : 'lifetime';
$devotee_id = $_GET['devotee_id'];

// Define variables to store attendance data
$totalSabhas = 0;
$presentCount = 0;
$attendancePercentage = 0;

// Get the start and end dates for the selected period
switch ($period) {
    case 'last_month':
        $currentDate = new DateTime();
        $currentDate->modify('last month');
        $startDate = $currentDate->format('Y-m-01'); // First day of last month
        $endDate = date('Y-m-t', strtotime($startDate)); // Last day of last month
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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" defer></script>
    <link rel="stylesheet" href="../css/Devotee/devoteeAttendance.css">
    <link rel="stylesheet" href="../css/centerleader/header.css">
</head>

<body>
    <?php include('header.php'); ?>
    <div id="attendanceComponent" stye="padding-top:50px;">
        <h1>Devotee Attendance</h1>
        <div id="chartContainer"></div>
        <form id="attendanceForm" method="get">
            <div class="form-group">
                <label for="timePeriod">Select Time Period:</label>
                <select class="form-control" id="timePeriod" name="period" onchange="this.form.action = 'attendanceDashboard.php?devotee_id=<?php echo $devotee_id; ?>&period=' + this.value; this.form.submit();">
                    <option value="last_month" <?php echo ($period == 'last_month') ? 'selected' : ''; ?>>Last Month</option>
                    <option value="last_year" <?php echo ($period == 'last_year') ? 'selected' : ''; ?>>Last Year</option>
                    <option value="lifetime" <?php echo ($period == 'lifetime') ? 'selected' : ''; ?>>Lifetime</option>
                </select>


                <div id="sabhaDetailsComponent">
                    <!-- Right component with sabha details -->
                    <h1>All sabhas</h1>
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

                            echo "<div class='sabha-details'>
                <h3 class='title'>$sabhaTitle</h3>
                <p><strong>Date:</strong> $sabhaDate</p>
                <p><strong>Speaker:</strong> $sabhaSpeaker</p>
                <p class='status'><strong>Attendance Status:</strong> $attendanceStatus</p>
                <p><strong>Description:</strong> $description</p>
              </div>";
                        }
                    } else {
                        echo "<p>No sabhas found for the selected period.</p>";
                    }
                    ?>

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