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

// Fetch the number of centers
$centersQuery = "SELECT COUNT(*) AS total_centers FROM tbl_center";
$centersResult = mysqli_query($conn, $centersQuery);
$totalCenters = mysqli_fetch_assoc($centersResult)['total_centers'];

// Fetch the number of devotees
$devoteesQuery = "SELECT COUNT(*) AS total_devotees FROM tbl_devotee";
$devoteesResult = mysqli_query($conn, $devoteesQuery);
$totalDevotees = mysqli_fetch_assoc($devoteesResult)['total_devotees'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbpjJcH3zYLnsK1znEMxkYvz9yX7Kt8rrG+2oVzxsRt3HUcqzS8k9/XmW" crossorigin="anonymous">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/styles.css">
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
                        <a class="nav-link" href="#">Centers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Devotees</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Content</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Settings</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="row m-2">
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Centers</h5>
                    <p class="card-text display-4"><?php echo $centerCount; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Devotees</h5>
                    <p class="card-text display-4"><?php echo $devoteeCount; ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-4">
    <div class="row">
        <!-- Total Centers and Total Devotees cards here -->
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Centers List</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Center Name</th>
                                <th>Location</th>
                                <th>Leader Name</th>
                                <th>Devotees</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $centersQuery = "SELECT c.center_name, c.location, l.name AS leader_name, COUNT(d.center_id) AS total_devotees
                                            FROM tbl_center c
                                            LEFT JOIN tbl_leader l ON c.leader_id = l.leader_id
                                            LEFT JOIN tbl_devotee d ON c.center_id = d.center_id
                                            GROUP BY c.center_id";
                            $centersResult = mysqli_query($conn, $centersQuery);
                            while ($row = mysqli_fetch_assoc($centersResult)) {
                                echo "<tr>";
                                echo "<td>" . $row['center_name'] . "</td>";
                                echo "<td>" . $row['location'] . "</td>";
                                echo "<td>" . $row['leader_name'] . "</td>";
                                echo "<td>" . $row['total_devotees'] . "</td>";
                                echo "<td>
                                        <a href='#' class='btn btn-sm btn-primary'>View</a>
                                        <a href='#' class='btn btn-sm btn-secondary'>Edit</a>
                                        <a href='#' class='btn btn-sm btn-danger'>Delete</a>
                                      </td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Devotees List</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Devotee Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Center</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $devoteesQuery = "SELECT name, email, mobile_number, c.center_name
                                            FROM tbl_devotee d
                                            LEFT JOIN tbl_center c ON d.center_id = c.center_id";
                            $devoteesResult = mysqli_query($conn, $devoteesQuery);
                            while ($row = mysqli_fetch_assoc($devoteesResult)) {
                                echo "<tr>";
                                echo "<td>" . $row['name'] . "</td>";
                                echo "<td>" . $row['email'] . "</td>";
                                echo "<td>" . $row['mobile_number'] . "</td>";
                                echo "<td>" . $row['center_name'] . "</td>";
                                echo "<td>
                                        <a href='#' class='btn btn-sm btn-primary'>View</a>
                                        <a href='#' class='btn btn-sm btn-secondary'>Edit</a>
                                        <a href='#' class='btn btn-sm btn-danger'>Delete</a>
                                      </td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
    <!-- HTML for displaying devotees -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Devotees List</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Devotee Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Center</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Check if there are devotees
                        if (mysqli_num_rows($devoteesResult) > 0) {
                            // Loop through each devotee
                            while ($row = mysqli_fetch_assoc($devoteesResult)) {
                                echo "<tr>";
                                echo "<td>" . $row['devotee_name'] . "</td>";
                                echo "<td>" . $row['email'] . "</td>";
                                echo "<td>" . $row['phone'] . "</td>";
                                echo "<td>" . $row['center'] . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No devotees found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
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
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>

</html>