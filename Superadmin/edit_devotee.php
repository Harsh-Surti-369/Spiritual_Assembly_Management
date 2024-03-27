<?php
session_start();
include('../php/dbConnect.php');

if (!isset($_SESSION['super_admin_email'])) {
    // Redirect to the login page
    header("Location: login.php");
    exit; // Stop further execution
}

// Check if devotee ID is provided in the URL
if (!isset($_GET['id'])) {
    header("Location: devotees.php"); // Redirect back to devotees list if ID is not provided
    exit;
}

$devoteeId = $_GET['id'];

// Fetch devotee details from the database based on the ID
$query = "SELECT * FROM tbl_devotee WHERE devotee_id = $devoteeId";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "Devotee not found";
    exit;
}

$devotee = mysqli_fetch_assoc($result);

// Handle form submission for updating devotee details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST["name"];
    $email = $_POST["email"];
    $mobileNumber = $_POST["mobile_number"];
    $centerId = $_POST["center_id"];

    // Update devotee details in the database
    $updateQuery = "UPDATE tbl_devotee SET name = '$name', email = '$email', mobile_number = '$mobileNumber', center_id = $centerId WHERE devotee_id = $devoteeId";
    if (mysqli_query($conn, $updateQuery)) {
        echo '<script>alert("Devotee details updated successfully.");</script>';
        // Redirect back to devotees list page
        header("Location: devotees.php");
        exit;
    } else {
        echo '<script>alert("Error updating devotee details. Please try again.");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>Edit Devotee</title>
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
        <h1 class="mb-4">Edit Devotee</h1>
        <form method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $devotee['name']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $devotee['email']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="mobile_number" class="form-label">Mobile Number</label>
                <input type="text" class="form-control" id="mobile_number" name="mobile_number" value="<?php echo $devotee['mobile_number']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="center_id" class="form-label">Center</label>
                <select class="form-select" id="center_id" name="center_id" required>
                    <?php
                    // Fetch center options from the database
                    $centersQuery = "SELECT * FROM tbl_center";
                    $centersResult = mysqli_query($conn, $centersQuery);
                    while ($row = mysqli_fetch_assoc($centersResult)) {
                        echo '<option value="' . $row['center_id'] . '"';
                        if ($row['center_id'] == $devotee['center_id']) {
                            echo ' selected';
                        }
                        echo '>' . $row['center_name'] . '</option>';
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha2/js/bootstrap.bundle.min.js"></script>

</body>

</html>