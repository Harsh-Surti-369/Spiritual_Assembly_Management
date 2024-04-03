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

// Initialize an empty array to store data points
$dataPoints = array();

// Fetch data from the result set
// Fetch data from the result set
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Add center name and devotee count to data points
        $dataPoints[] = array("label" => $row["center_name"], "y" => (int)$row["devotee_count"]);
    }

    // Convert data points array to JSON format
    $dataJSON = json_encode($dataPoints, JSON_NUMERIC_CHECK);

    // Check if JSON encoding was successful
    if ($dataJSON === false) {
        echo "Error encoding data points to JSON.";
        exit; // Stop further execution
    }
} else {
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        .bg-primary {
            background-color: #0C2D57 !important;
        }

        .bg-secondary {
            background-color: #EFECEC !important;
        }

        .text-primary {
            color: #0C2D57 !important;
        }

        .text-secondary {
            color: #EFECEC !important;
        }

        .btn-primary {
            background-color: #0C2D57 !important;
            border-color: #0C2D57 !important;
        }

        .btn-primary:hover {
            background-color: #0C2D57 !important;
            border-color: #0C2D57 !important;
        }

        .btn-secondary {
            background-color: #EFECEC !important;
            border-color: #EFECEC !important;
            color: #0C2D57 !important;
        }

        .btn-secondary:hover {
            background-color: #EFECEC !important;
            border-color: #EFECEC !important;
            color: #0C2D57 !important;
        }
    </style>
</head>

<body>
    <?php include('header.php'); ?>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="row m-2">
                <div class="col-md-3 mb-4">
                    <div class="card custom-box">
                        <div class="card-body">
                            <h5 class="card-title">Total Centers</h5>
                            <p class="card-text display-4"><?php echo $centerCount; ?></p>
                            <a class="" href="centers.php">View All</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card custom-box">
                        <div class="card-body">
                            <h5 class="card-title">Total Devotees</h5>
                            <p class="card-text display-4"><?php echo $devoteeCount; ?></p>
                            <a class="" href="devotees.php">View All</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-3" id="chartContainer" style="height: 370px; width: 100%;"></div>

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header bg-secondary">
                    <h5 class="card-title text-primary">Center Attendance</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="text-primary">Center Name</th>
                                <th class="text-primary">Location</th>
                                <th class="text-primary">Total Attendees</th>
                                <th class="text-primary">Expected Attendance</th>
                                <th class="text-primary">Attendance %</th>
                                <th class="text-primary">% Change</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $centerAttendanceQuery = "SELECT
    c.center_name,c.location,
    COUNT(a.attendance_id) AS total_attendees,
    COUNT(s.sabha_id) AS expected_attendance,
    ROUND((COUNT(a.attendance_id) / COUNT(s.sabha_id)) * 100, 2) AS attendance_percentage,
    CONCAT(ROUND((COUNT(a.attendance_id) / COUNT(s.sabha_id)) * 100, 2), '%') AS attendance_percentage_formatted
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
                                echo '<tr><td colspan="7">No attendance data available</td></tr>';
                            }
                            ?>

                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-secondary">
                    <h5 class="card-title text-primary">Devotee Attendance</h5>
                    <div class="d-flex justify-content-end">
                        <select class="form-select form-select-sm">
                            <option value="">Select Devotee</option>
                            <option value="devotee1">Alice Johnson</option>
                            <option value="devotee2">Bob Williams</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="devoteeAttendanceChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-secondary">
                    <h5 class="card-title text-primary">Overall Attendance</h5>
                </div>
                <div class="card-body">
                    <canvas id="overallAttendanceChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            // Function to load devotees based on selected center
            function loadDevotees(centerId) {
                $.ajax({
                    url: 'get_devotees.php', // Change this to the actual file name that fetches devotees based on center
                    type: 'GET',
                    data: {
                        center_id: centerId
                    },
                    success: function(response) {
                        $('#devoteeTable tbody').html(response);
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            }

            // Event listener for center filter change
            $('#centerFilter').change(function() {
                var centerId = $(this).val();
                loadDevotees(centerId);
            });

            // Initial load of devotees
            loadDevotees('');
        });

        window.onload = function() {
            // Step 4: Front-end Integration
            // Include the CanvasJS library and render the pie chart
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
        }
    </script>
</body>

</html>