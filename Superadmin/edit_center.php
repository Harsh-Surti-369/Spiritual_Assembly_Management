<?php
session_start();
include('../php/dbConnect.php');

if (!isset($_SESSION['super_admin_email'])) {
    // Redirect to the login page
    header("Location: login.php");
    exit; // Stop further execution
}

// Check if center ID is provided in the URL
if (!isset($_GET['id'])) {
    // Redirect to the centers management page if ID is not provided
    header("Location: centers.php");
    exit;
}

// Get the center ID from the URL
$centerId = $_GET['id'];

// Fetch center details based on the provided center ID
$query = "SELECT * FROM tbl_center WHERE center_id = $centerId";
$result = mysqli_query($conn, $query);

// Check if the query executed successfully and if a center with the provided ID exists
if ($result && mysqli_num_rows($result) > 0) {
    $center = mysqli_fetch_assoc($result);
} else {
    // Redirect to the centers management page if center ID is invalid or not found
    header("Location: centers.php");
    exit;
}

// Handle form submission for updating center details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $centerName = $_POST['center_name'];
    $location = $_POST['location'];
    $startingDate = $_POST['starting_date'];
    $leaderId = $_POST['leader_id'];

    // Update center details in the database
    $updateQuery = "UPDATE tbl_center SET center_name = '$centerName', location = '$location', starting_date = '$startingDate', leader_id = $leaderId WHERE center_id = $centerId";
    if (mysqli_query($conn, $updateQuery)) {
        // Redirect to the centers management page after successful update
        header("Location: centers.php");
        exit;
    } else {
        // Handle update error
        $updateError = "Error updating center: " . mysqli_error($conn);
    }
}

// Fetch leaders for populating the dropdown list
$leadersQuery = "SELECT leader_id, name FROM tbl_leader";
$leadersResult = mysqli_query($conn, $leadersQuery);
$leaders = mysqli_fetch_all($leadersResult, MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Center</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <style>
        /* Custom styles here */
    </style>
</head>

<body>
<?php include('header.php'); ?>

    <div class="container mt-4">
        <h1>Edit Center</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="center_name" class="form-label">Center Name</label>
                <input type="text" class="form-control" id="center_name" name="center_name" value="<?php echo $center['center_name']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" class="form-control" id="location" name="location" value="<?php echo $center['location']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="starting_date" class="form-label">Starting Date</label>
                <input type="date" class="form-control" id="starting_date" name="starting_date" value="<?php echo $center['starting_date']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="leader_id" class="form-label">Leader</label>
                <select class="form-select" id="leader_id" name="leader_id" required>
                    <option value="">Select Leader</option>
                    <?php foreach ($leaders as $leader) : ?>
                        <option value="<?php echo $leader['leader_id']; ?>" <?php echo ($leader['leader_id'] == $center['leader_id']) ? 'selected' : ''; ?>><?php echo $leader['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Center</button>
            <a href="centers.php" class="btn btn-secondary">Cancel</a>
            <?php if (isset($updateError)) : ?>
                <div class="alert alert-danger mt-3" role="alert">
                    <?php echo $updateError; ?>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <!-- Latest Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha2/js/bootstrap.bundle.min.js"></script>

</body>

</html>