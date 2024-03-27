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
    <nav class="navbar navbar-expand-md navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="centers.php">Centers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="devotees.php">Devotees</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="content.php">Content</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">Profile</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Total Centers and Total Devotees cards here -->
            <div class="row m-2">
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Centers</h5>
                            <p class="card-text display-4"><?php echo $centerCount; ?></p>
                            <a class="" href="centers.php">View All</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card">
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

<!-- <div class="row">      <div class="col-md-12 mb-4">
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
                                <th class="text-primary">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Center A</td>
                                <td>City X</td>
                                <td>500</td>
                                <td>600</td>
                                <td>83.3%</td>
                                <td class="text-success"><i class="bi bi-arrow-up"></i> 12%</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-primary">View Details</a>
                                    <a href="#" class="btn btn-sm btn-secondary">Edit</a>
                                </td>
                            </tr>
                            <!-- Add more rows as needed -->
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
                            <!-- Add more options as needed -->
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