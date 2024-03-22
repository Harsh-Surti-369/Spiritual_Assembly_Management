<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Devotees</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/CenterLeader/managedevotees.css">
</head>

<body>
    <div class="container">
        <h2 class="text-center mb-4">Manage Devotees</h2>
        <?php
        session_start();
        include('../php/dbConnect.php');
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
                    echo "<td><a href='updateDevotee.php?devotee_id=" . $devotee_row["devotee_id"] . "'class='btn btn-primary'>Update</a>
                            <a href='#' class='btn btn-danger btn-delete' data-devotee-id='" . $devotee_row["devotee_id"] . "'>Delete</a></td>
                            <a href='attendanceDashboard.php?devotee_id=" . $devotee_row["devotee_id"] . "' class='btn btn-success'>Attendance</a>
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
    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Function to show confirmation modal
            function showConfirmation(devoteeId) {
                $('#confirmationModal').modal('show');
                // Set href of delete button in modal to include devotee ID
                var deleteUrl = 'manageDevotees.php?action=delete&devotee_id=' + devoteeId;
                $('#confirmDeleteButton').attr('href', deleteUrl);
            }

            // When delete button is clicked
            $('.btn-delete').click(function(event) {
                event.preventDefault(); // Prevent default link behavior
                var devoteeId = $(this).data('devotee-id');
                showConfirmation(devoteeId);
            });
        });
    </script>
</body>

</html>