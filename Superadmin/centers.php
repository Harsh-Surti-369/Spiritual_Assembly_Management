<?php
session_start();
include('../php/dbConnect.php');

if (!isset($_SESSION['super_admin_email'])) {
    // Redirect to the login page
    header("Location: login.php");
    exit; // Stop further execution
}

function fetchCenterData()
{
    global $conn;

    $centerData = array();

    // Query to fetch center data
    $query = "SELECT c.center_id, c.center_name, c.location, c.starting_date, l.name AS leader_name
              FROM tbl_center c
              LEFT JOIN tbl_leader l ON c.leader_id = l.leader_id";

    $result = mysqli_query($conn, $query);

    // Check if query executed successfully
    if ($result) {
        // Fetch data from the result set
        while ($row = mysqli_fetch_assoc($result)) {
            // Add each row to the array
            $centerData[] = $row;
        }
    } else {
        // Handle query error
        echo "Error fetching center data: " . mysqli_error($conn);
    }
    return $centerData;
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    // Check if center ID is provided in the URL
    $centerId = $_GET['id'];

    // Disable foreign key checks
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");

    // Execute DELETE query to delete the center from the database
    $query = "DELETE FROM tbl_center WHERE center_id = $centerId";
    if (mysqli_query($conn, $query)) {
        // Center deleted successfully, display success message
        echo '<script>alert("Center deleted successfully.");</script>';
    } else {
        // Error occurred while deleting center, display error message
        echo '<script>alert("Error deleting center. Please try again.");</script>';
    }

    // Enable foreign key checks
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");
}

// Call the function to fetch center data
$centers = fetchCenterData();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centers</title>
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

        body {
            background-color: #EFECEC;
            color: #0C2D57;
        }

        .card {
            border-color: #FC6736;
        }

        .btn-primary {
            background-color: #FC6736;
            border-color: #FC6736;
        }

        .btn-primary:hover {
            background-color: #FFB0B0;
            border-color: #FFB0B0;
        }
    </style>
</head>

<body> <?php include('header.php'); ?>

    <div class="container mt-4">
        <h1 class="mb-4">Centers Management</h1>

        <!-- Create New Center Card -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Create a New Center</h5>
                <p class="card-text">Click below to create a new center.</p>
                <a href="CreateCenter.php" class="btn btn-primary">Create Center</a>
            </div>
        </div>

        <!-- Centers List Table -->
        <h2 class="mb-3">Centers List</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Center Name</th>
                    <th>Location</th>
                    <th>Starting Date</th>
                    <th>Leader Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($centers as $center) : ?>
                    <tr>
                        <td><?php echo $center['center_name']; ?></td>
                        <td><?php echo $center['location']; ?></td>
                        <td><?php echo $center['starting_date']; ?></td>
                        <td><?php echo $center['leader_name']; ?></td>
                        <td>

                            <a href="edit_center.php?id=<?php echo $center['center_id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                            <button class="btn btn-danger btn-sm" onclick="openConfirmDeleteModal(<?php echo $center['center_id']; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this center?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha2/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to open the delete confirmation modal
        function openConfirmDeleteModal(centerId) {
            $('#confirmDeleteModal').modal('show');

            // Set the centerId to a data attribute of the delete button inside the modal
            $('#confirmDeleteBtn').data('center-id', centerId);
        }

        // When the delete button inside the modal is clicked, trigger the delete action
        $('#confirmDeleteBtn').click(function() {
            var centerId = $(this).data('center-id');
            // Redirect to this page with action=delete and center ID in URL
            window.location.href = 'centers.php?action=delete&id=' + centerId;
        });
    </script>

</body>

</html>