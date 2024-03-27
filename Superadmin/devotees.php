<?php
session_start();
include('../php/dbConnect.php');

if (!isset($_SESSION['super_admin_email'])) {
    // Redirect to the login page
    header("Location: login.php");
    exit; // Stop further execution
}

// Function to fetch devotee data
function fetchDevoteeData()
{
    global $conn;

    $devoteeData = array();

    // Query to fetch devotee data
    $query = "SELECT d.devotee_id, d.name, d.email, d.mobile_number, c.center_name
              FROM tbl_devotee d
              LEFT JOIN tbl_center c ON d.center_id = c.center_id";

    $result = mysqli_query($conn, $query);

    // Check if query executed successfully
    if ($result) {
        // Fetch data from the result set
        while ($row = mysqli_fetch_assoc($result)) {
            // Add each row to the array
            $devoteeData[] = $row;
        }
    } else {
        // Handle query error
        echo "Error fetching devotee data: " . mysqli_error($conn);
    }

    return $devoteeData;
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['devotee_id'])) {
    $devotee_Id = $_GET['devotee_id'];

    // Disable foreign key checks
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");

    // Execute DELETE query to delete the center from the database
    $query = "DELETE FROM tbl_devotee WHERE devotee_id = $devotee_Id";
    if (mysqli_query($conn, $query)) {
        // Center deleted successfully, display success message
        echo '<script>alert("devotee deleted successfully.");</script>';
    } else {
    }

    // Enable foreign key checks
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");
}

// Call the function to fetch devotee data
$devotees = fetchDevoteeData();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devotees</title>
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

    <div class="container mt-4">
        <h1 class="mb-4">Devotees Management</h1>

        <!-- Devotees List Table -->
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile Number</th>
                    <th>Center</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($devotees as $devotee) : ?>
                    <tr>
                        <td><?php echo $devotee['name']; ?></td>
                        <td><?php echo $devotee['email']; ?></td>
                        <td><?php echo $devotee['mobile_number']; ?></td>
                        <td><?php echo $devotee['center_name']; ?></td>
                        <td>
                            <a href="edit_devotee.php?id=<?php echo $devotee['devotee_id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                            <button class="btn btn-danger btn-sm" onclick="openConfirmDeleteModal(<?php echo $devotee['devotee_id']; ?>)">Delete</button>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this Devotee"s account?
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
        function openConfirmDeleteModal(devotee_Id) {
            $('#confirmDeleteModal').modal('show');

            // Set the centerId to a data attribute of the delete button inside the modal
            $('#confirmDeleteBtn').data('devotee_id', devotee_Id);
        }

        // When the delete button inside the modal is clicked, trigger the delete action
        $('#confirmDeleteBtn').click(function() {
            var devotee_Id = $(this).data('devotee_id');
            // Redirect to this page with action=delete and center ID in URL
            window.location.href = 'devotees.php?action=delete&devotee_id=' + devotee_Id;
        });
    </script>
</body>

</html>