<?php session_start();
// Check if leader is logged in
if (!isset($_SESSION['leader_id'])) {
    header("Location: login.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Center Leader -Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" defer></script>
    <link rel="stylesheet" href="../css/CenterLeader/managedevotees.css">
    <link rel="stylesheet" href="../css/CenterLeader/header.css">
</head>

<body>
    <header style="margin-bottom: 150px;">
        <?php include('header.php');
        ?>
    </header>
    <div class="container-fluid mt-1" stye="padding-top:40px">
        <h2 class="text-center mb-2">Manage Devotees</h2>
        <?php
        include('../php/dbConnect.php');
        function disableForeignKeyChecks($conn)
        {
            $sql = "SET FOREIGN_KEY_CHECKS = 0";
            mysqli_query($conn, $sql);
        }

        // Function to enable foreign key checks
        function enableForeignKeyChecks($conn)
        {
            $sql = "SET FOREIGN_KEY_CHECKS = 1";
            mysqli_query($conn, $sql);
        }

        if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['devotee_id'])) {
            // Perform deletion
            $devotee_id = $_GET['devotee_id'];

            disableForeignKeyChecks($conn);
            $delete_query = "DELETE FROM tbl_devotee WHERE devotee_id = $devotee_id";
            $result = mysqli_query($conn, $delete_query);
            enableForeignKeyChecks($conn);

            // Check if deletion was successful
            if ($result) {
                // Set session variable upon successful deletion
                $_SESSION['delete_success'] = true;
            } else {
                // Set session variable upon deletion failure
                $_SESSION['delete_success'] = false;
            }

            // Redirect back to the same page after deletion
            header("Location: dashboard.php");
            exit();
        }

        $leader_id = $_SESSION['leader_id'];

        $center_query = "SELECT center_id FROM tbl_leader WHERE leader_id = $leader_id";
        $center_result = $conn->query($center_query);

        if ($center_result->num_rows > 0) {
            $center_row = $center_result->fetch_assoc();
            $center_id = $center_row['center_id'];

            $devotee_query = "SELECT * FROM tbl_devotee WHERE center_id = $center_id";
            $devotee_result = $conn->query($devotee_query);
            $devotee_row = [];

            if ($devotee_result->num_rows > 0) {
                echo "<div class='table-responsive'>";
                echo "<table class='table table-bordered table-devotees'><thead class='table-header'><tr><th>Name</th><th>Email</th><th>Phone</th><th>DOB</th><th>Gender</th><th>Joining Date</th><th>Actions</th></tr></thead><tbody>";
                while ($devotee_row = $devotee_result->fetch_assoc()) {
                    echo "<tr><td>" . $devotee_row["name"] . "</td><td>" . $devotee_row["email"] . "</td><td>" . $devotee_row["mobile_number"] . "</td><td>" . $devotee_row["dob"] . "</td><td>" . $devotee_row["gender"] . "</td><td>" . $devotee_row["joining_date"] . "</td>";
                    echo "<td><a href='updateDevotee.php?devotee_id=" . $devotee_row["devotee_id"] . "' class='btn bn-sm btn-primary'>Update</a>
                    <a href='?action=delete&devotee_id=" . $devotee_row["devotee_id"] . "' class='btn btn-sm btn-danger btn-delete'>Delete</a>
                    <a href='attendanceDashboard.php?devotee_id=" . $devotee_row["devotee_id"] . "' class='btn btn-sm btn-primary'>Attendance</a></td>
                    </tr>";
                }
                echo "</tbody></table>";
                echo "</div>";
            } else {
                echo "<p class='no-devotees'>No devotees found for this center leader.</p>";
            }
        } else {
            echo "<p class='no-devotees'>Center ID not found for this center leader.</p>";
        }
        ?>


        <!-- Modal for delete confirmation -->
        <div id="confirmationModal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Deletion</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this devotee?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <a href="#" id="confirmDeleteButton" class="btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
        <script>
            $(document).ready(function() {
                // Function to show confirmation modal
                function showConfirmation(devoteeId) {
                    $('#confirmationModal').modal('show');
                    // Set href of delete button in modal to include devotee ID
                    var deleteUrl = 'dashboard.php?action=delete&devotee_id=' + devoteeId;
                    $('#confirmDeleteButton').attr('href', deleteUrl);
                }

                // When delete button is clicked
                $('.btn-delete').click(function(event) {
                    event.preventDefault(); // Prevent default link behavior
                    var devoteeId = $(this).data('devotee-id');
                    showConfirmation(devoteeId);
                });

                // Show success toast if session variable is set
                <?php if (isset($_SESSION['delete_success'])) : ?>
                    <?php if ($_SESSION['delete_success']) : ?>
                        showToast('Devotee deleted successfully', 'success');
                    <?php else : ?>
                        showToast('Failed to delete devotee', 'error');
                    <?php endif; ?>
                    <?php unset($_SESSION['delete_success']); ?> // Unset session variable after displaying toast
                <?php endif; ?>

                // Function to show Bootstrap toast
                function showToast(message, type) {
                    var toastClass = 'bg-success';
                    if (type === 'error') {
                        toastClass = 'bg-danger';
                    }
                    var toast = `<div class="toast align-items-center text-white ${toastClass}" role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="d-flex">
                                <div class="toast-body">
                                    ${message}
                                </div>
                                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                        </div>`;
                    $('.toast-container').append(toast);
                    $('.toast').toast('show');
                }
            });
        </script>
</body>

</html>