<?php
session_start();
include('../php/dbConnect.php');

if (!isset($_SESSION['super_admin_email'])) {
    // Redirect to the login page
    header("Location: login.php");
    exit; // Stop further execution
}

$centerCountQuery = "SELECT COUNT(*) AS center_count FROM tbl_center";
$centerCountResult = mysqli_query($conn, $centerCountQuery);
$centerCount = mysqli_fetch_assoc($centerCountResult)['center_count'];

// Fetch number of devotees
$devoteeCountQuery = "SELECT COUNT(*) AS devotee_count FROM tbl_devotee";
$devoteeCountResult = mysqli_query($conn, $devoteeCountQuery);
$devoteeCount = mysqli_fetch_assoc($devoteeCountResult)['devotee_count'];

// Step 1: Data Retrieval
// Connect to the database and retrieve data for all centers
$sql = "SELECT c.center_name, COUNT(d.devotee_id) AS devotee_count
        FROM tbl_center c
        LEFT JOIN tbl_devotee d ON c.center_id = d.center_id
        GROUP BY c.center_id";
$result = $conn->query($sql);

// Initialize an empty array to store data points for the pie chart
$dataPoints = array();

// Fetch data from the result set for the pie chart
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dataPoints[] = array("label" => $row["center_name"], "y" => (int)$row["devotee_count"]);
    }
    $dataJSON = json_encode($dataPoints, JSON_NUMERIC_CHECK);

    if ($dataJSON === false) {
        echo "Error encoding data points to JSON.";
        exit; // Stop further execution
    }
}

if (isset($_POST['center_id'])) {
    // Sanitize input to prevent SQL injection
    $selectedCenterId = mysqli_real_escape_string($conn, $_POST['center_id']);

    // Construct SQL query based on selected center
    $s = "SELECT 
                DATE_FORMAT(s.date, '%Y-%m') AS month_year,
                c.center_name,
                COUNT(a.attendance_id) AS total_attendees,
                COUNT(d.devotee_id) AS expected_attendance,
                CONCAT(ROUND((COUNT(a.attendance_id) / COUNT(d.devotee_id)) * 100, 2), '%') AS attendance_percentage
            FROM 
                tbl_sabha s
            JOIN 
                tbl_center c ON s.center_id = c.center_id
            LEFT JOIN 
                tbl_attendance a ON s.sabha_id = a.sabha_id
            LEFT JOIN 
                tbl_devotee d ON s.center_id = d.center_id";

    if ($selectedCenterId != 'all') {
        $s .= " WHERE c.center_id = '$selectedCenterId'";
    }

    $s .= " GROUP BY 
                month_year, c.center_id
            ORDER BY 
                month_year DESC";

    $result = $conn->query($s);

    // Check for errors
    if ($result === false) {
        // Handle error
        echo json_encode(['error' => 'Database query error: ' . $conn->error]);
        exit;
    }

    // Initialize arrays to hold data for the column chart
    $months = array();
    $centerChartData = array();

    // Fetch data and organize it by months and centers for the column chart
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $monthYear = $row['month_year'];
            $centerName = $row['center_name'];
            $attendancePercentage = round(($row['total_attendees'] / $row['expected_attendance']) * 100, 2);

            if (!isset($centerChartData[$centerName])) {
                $centerChartData[$centerName] = array();
            }

            $centerChartData[$centerName][$monthYear] = $attendancePercentage;

            if (!in_array($monthYear, $months)) {
                $months[] = $monthYear;
            }
        }

        // Send the JSON response
        echo json_encode(['months' => $months, 'centerChartData' => $centerChartData]);
        exit;
    } else {
        // No data found for the selected center
        echo json_encode(['error' => 'No data available for the selected center']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="shortcut icon" href="../images/Logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        .custom-box {
            background-color: #EFECEC;
            color: #0C2D57;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .custom-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .custom-box a {
            color: #FC6736;
            text-decoration: none;
        }

        .custom-box a:hover {
            color: #0C2D57;
        }

        .card-text {
            font-weight: bold;
            font-family: 'Trebuchet MS';
        }

        .bg-primary {
            background-color: #0C2D57 !important;
        }

        .text-primary {
            color: #0C2D57 !important;
        }

        .btn-primary {
            background-color: #FC6736;
            border-color: #FC6736;
        }

        .btn-primary:hover {
            background-color: #d95526;
            border-color: #d95526;
        }

        .custom-table th {
            background-color: #FFB0B0;
            color: #0C2D57;
        }
    </style>
</head>

<body>
    <header style="margin-bottom: 150px;">
        <?php include('header.php'); ?>
    </header>
    <main class="container mb-5">
        <!-- Cards for total devotees and centers -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="custom-box p-4">
                    <h5 class="card-title mb-3">Total Centers</h5>
                    <p class="card-text display-4 mb-3 text-center"><?php echo $centerCount; ?></p>
                    <a href="centers.php" class="text-center">View All</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="custom-box p-4">
                    <h5 class="card-title mb-3">Total Devotees</h5>
                    <p class="card-text display-4 mb-3 text-center"><?php echo $devoteeCount; ?></p>
                    <a href="devotees.php" class="text-center">View All</a>
                </div>
            </div>
        </div>

        <!-- Center-wise distribution of devotees -->
        <div class="mb-4">
            <div id="chartContainer" style="height: 400px; width: 100%;"></div>
        </div>

        <!-- Form for selecting center -->
        <form id="centerForm" method="post" action="" class="mb-4">
            <div class="mb-3">
                <label for="center" class="form-label text-primary">Select Center:</label>
                <select id="center" name="center" class="form-select">
                    <option value="all">All Centers</option>
                    <?php
                    // Fetch centers from the database and populate the select options
                    $centerQuery = "SELECT center_id, center_name FROM tbl_center";
                    $centerResult = mysqli_query($conn, $centerQuery);

                    if ($centerResult && mysqli_num_rows($centerResult) > 0) {
                        while ($row = mysqli_fetch_assoc($centerResult)) {
                            $centerId = $row['center_id'];
                            $centerName = $row['center_name'];
                            echo "<option value='$centerId'>$centerName</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        <!-- Chart for attendance percentage in months -->
        <div id="centerChartContainer" style="height: 600px; width: 100%; margin-bottom:5rem;"></div>

        <!-- Center attendance table -->
        <div class="card mt-5">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title">Center Attendance</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped custom-table">
                    <thead>
                        <tr>
                            <th>Center Name</th>
                            <th>Location</th>
                            <th>Total Attendees</th>
                            <th>Expected Attendance</th>
                            <th>Attendance %</th>
                            <th>% Change</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $centerAttendanceQuery = "SELECT
                        c.center_name,
                        c.location,
                        COUNT(CASE WHEN a.attendance_status = 'present' THEN a.attendance_id END) AS total_attendees,
                        COUNT(s.sabha_id) AS expected_attendance,
                        ROUND((COUNT(CASE WHEN a.attendance_status = 'present' THEN a.attendance_id END) / COUNT(s.sabha_id)) * 100, 2) AS attendance_percentage,
                        CONCAT(ROUND((COUNT(CASE WHEN a.attendance_status = 'present' THEN a.attendance_id END) / COUNT(s.sabha_id)) * 100, 2), '%') AS attendance_percentage_formatted
                    FROM
                        tbl_center c
                    LEFT JOIN
                        tbl_sabha s ON c.center_id = s.center_id
                    LEFT JOIN
                        tbl_attendance a ON s.sabha_id = a.sabha_id
                    GROUP BY
                        c.center_id;
                    
                        ";

                        $centerAttendanceResult = $conn->query($centerAttendanceQuery);

                        if ($centerAttendanceResult->num_rows > 0) {
                            // Loop through each center's attendance data
                            while ($row = $centerAttendanceResult->fetch_assoc()) {
                                $centerName = $row['center_name'];
                                $location = $row['location'];
                                $totalAttendees = $row['total_attendees'];
                                $expectedAttendance = $row['expected_attendance'];
                                $attendancePercentage = $row['attendance_percentage_formatted'];

                                // Calculate percentage change (assuming we have previous data to compare)
                                $previousAttendancePercentage = 80; // Example previous attendance percentage
                                $attendancePercentageValue = floatval(str_replace('%', '', $attendancePercentage));
                                $percentageChange = $attendancePercentageValue - $previousAttendancePercentage;
                                $percentageChangeClass = $percentageChange >= 0 ? 'text-success' : 'text-danger';
                        ?>
                                <tr>
                                    <td><?php echo $centerName; ?></td>
                                    <td><?php echo $location; ?></td>
                                    <td><?php echo $totalAttendees; ?></td>
                                    <td><?php echo $expectedAttendance; ?></td>
                                    <td><?php echo $attendancePercentage; ?></td>
                                    <td class="<?php echo $percentageChangeClass; ?>"><i class="bi bi-arrow-<?php echo $percentageChange >= 0 ? 'up' : 'down'; ?>"></i> <?php echo abs($percentageChange); ?>%</td>
                                </tr>
                        <?php
                            }
                        } else {
                            // No data found
                            echo
                            '<tr><td colspan="7">No attendance data available</td></tr>';
                        }
                        ?>

                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>

    <script>
        $(document).ready(function() {

            $('#centerForm').submit(function(e) {
                e.preventDefault(); // Prevent default form submission
                var centerId = $('#center').val(); // Get selected center ID

                // Ajax request to fetch attendance data for the selected center
                $.ajax({
                    url: 'index.php', // Replace with the actual file name to fetch data
                    type: 'POST',
                    data: {
                        center_id: centerId
                    },
                    success: function(response) {
                        var data = JSON.parse(response); // Parse the JSON response
                        renderColumnChart(data); // Render the column chart with fetched data
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });

                // Function to render the column chart
                function renderColumnChart(data) {
                    var months = data.months;
                    var centerChartData = data.centerChartData;

                    var dataPoints = [];
                    for (var center in centerChartData) {
                        var centerData = centerChartData[center];
                        var centerDataPoints = [];
                        for (var i = 0; i < months.length; i++) {
                            var month = months[i];
                            var value = centerData[month] !== undefined ? centerData[month] : 0;
                            centerDataPoints.push({
                                x: new Date(month + "-01"),
                                y: value
                            }); // Use x instead of label
                        }
                        dataPoints.push({
                            type: "column",
                            showInLegend: true,
                            name: center,
                            dataPoints: centerDataPoints
                        });
                    }

                    var chart = new CanvasJS.Chart("centerChartContainer", {
                        animationEnabled: true,
                        exportEnabled: true,
                        theme: "light1",
                        title: {
                            text: "Center-wise Attendance Percentage"
                        },
                        axisY: {
                            includeZero: true,
                            title: "Attendance Percentage"
                        },
                        data: dataPoints
                    });
                    chart.render();
                }

            });

            // Initial rendering of the pie chart
            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                exportEnabled: true,
                title: {
                    text: "Distribution of Registered Devotees by Center"
                },
                data: [{
                    type: "pie",
                    showInLegend: true,
                    legendText: "{label}",
                    indexLabelFontSize: 16,
                    indexLabel: "{label} - #percent%",
                    dataPoints: <?php echo $dataJSON; ?>
                }]
            });
            chart.render();
        });
    </script>
</body>

</html>