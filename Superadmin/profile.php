<?php
session_start();
include('../php/dbConnect.php');

if (!isset($_SESSION['super_admin_email'])) {
    // Redirect to the login page
    header("Location: login.php");
    exit; // Stop further execution
}

$adminEmail = $_SESSION['super_admin_email'];

// Fetch admin details from the database based on the email
$query = "SELECT * FROM super_admin WHERE email = '$adminEmail'";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "Admin not found";
    exit;
}

$admin = mysqli_fetch_assoc($result);

// Handle form submission for updating admin details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $newEmail = $_POST["email"];
    $newPassword = $_POST["password"];

    // Update admin details in the database
    $updateQuery = "UPDATE super_admin SET email = '$newEmail', password = '$newPassword' WHERE email = '$adminEmail'";
    if (mysqli_query($conn, $updateQuery)) {
        // Update session variable if email is changed
        if ($newEmail !== $adminEmail) {
            $_SESSION['super_admin_email'] = $newEmail;
        }
        echo '<script>alert("Admin details updated successfully.");</script>';
        // Refresh the page to reflect changes
        header("Refresh:0");
    } else {
        echo '<script>alert("Error updating admin details. Please try again.");</script>';
    }
}

if (isset($_POST['logout'])) {
    // Destroy the session and redirect to login page
    session_destroy();
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
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

        .container {
            background-color: #EFECEC;
            padding: 100px;
        }
    </style>
</head>

<body>
    <?php include('header.php'); ?>

    <div class="container mt-4">
        <h1 class="mb-4">Admin Profile</h1>
        <form method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $admin['email']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" value="<?php echo $admin['password']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>

        <form action="profile.php" method="post">
            <button name="logout" type="submit" class="btn btn-danger mt-3">Logout</button>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>