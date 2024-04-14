<?php
session_start();
include('../php/dbConnect.php');

// Check if Devotee is logged in
if (!isset($_SESSION['devotee_id'])) {
    header("Location: login.php");
    exit;
}

$devotee_id = $_SESSION['devotee_id'];

// Retrieve Devotee information from the database
$sql = "SELECT * FROM tbl_Devotee WHERE devotee_id = '$devotee_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $Devotee = $result->fetch_assoc();

    // Handle form submission for updating Devotee details
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $name = $_POST["name"];
        $email = $_POST["email"];
        $mobile_number = $_POST["mobile_number"];
        $address = $_POST["address"];
        $dob = $_POST["dob"];
        $gender = $_POST["gender"];

        // Update Devotee details in the database
        $updateQuery = "UPDATE tbl_Devotee SET name = '$name', email = '$email', mobile_number = '$mobile_number', dob = '$dob', gender = '$gender' WHERE devotee_id = '$devotee_id'";
        if ($conn->query($updateQuery) === TRUE) {
            $success_message = "Devotee details updated successfully.";
        } else {
            $error_message = "Error updating Devotee details. Please try again.";
        }
    }
} else {
    $error_message = "Devotee not found.";
    exit;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devotee Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/CenterDevotee/header.css">
    <style>
        nav {
            margin-bottom: 50px;
            margin-top: 0;
        }

        body {
            background-color: #EFECEC;
        }

        .container {
            padding-top: 50px;
            max-width: 600px;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #0C2D57;
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            color: #0C2D57;
        }

        .form-control {
            border-color: #0C2D57;
        }

        .btn-primary {
            background-color: #0C2D57;
            border-color: #0C2D57;
        }

        .success {
            color: #0C2D57;
            text-align: center;
            margin-bottom: 15px;
        }

        .error {
            color: #FFB0B0;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <header>
        <?php include('header.php'); ?>
    </header>
    <div class="container my-5" style="margin-top:50px !important;">
        <h1>Devotee Profile</h1>
        <?php
        // Display success or error message if set
        if (isset($success_message)) {
            echo '<p class="success">' . $success_message . '</p>';
        } elseif (isset($error_message)) {
            echo '<p class="error">' . $error_message . '</p>';
        }
        ?>
        <form method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Name:</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $Devotee['name']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $Devotee['email']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="mobile_number" class="form-label">Mobile Number:</label>
                <input type="text" class="form-control" id="mobile_number" name="mobile_number" value="<?php echo $Devotee['mobile_number']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="dob" class="form-label">Date of Birth:</label>
                <input type="date" class="form-control" id="dob" name="dob" value="<?php echo $Devotee['dob']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="gender" class="form-label">Gender:</label>
                <select class="form-select" id="gender" name="gender" required>
                    <option value="Male" <?php if ($Devotee['gender'] === 'Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if ($Devotee['gender'] === 'Female') echo 'selected'; ?>>Female</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>