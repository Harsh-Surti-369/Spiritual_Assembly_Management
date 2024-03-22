<?php
session_start();
include('../php/dbConnect.php');
$leader_id = $_SESSION['leader_id'];

if (isset($_GET['devotee_id'])) {
    $devotee_id = $_GET['devotee_id'];
    $devotee_query = "SELECT * FROM tbl_devotee WHERE devotee_id = $devotee_id";
    $devotee_result = $conn->query($devotee_query);

    if ($devotee_result->num_rows == 1) {
        $devotee_row = $devotee_result->fetch_assoc();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Collect form data
            $devotee_id = $_POST['devotee_id'];
            $name = $_POST['name'];
            $email = $_POST['email'];
            $mobile_number = $_POST['mobile_number'];
            $dob = $_POST['dob'];
            $gender = $_POST['gender'];

            // Update query
            $update_query = "UPDATE tbl_devotee SET name='$name', email='$email', mobile_number='$mobile_number', dob='$dob', gender='$gender' WHERE devotee_id=$devotee_id";

            if ($conn->query($update_query) === TRUE) {
                echo "<div class='alert alert-success' role='alert'>Devotee details updated successfully</div>";
            } else {
                echo "Error updating devotee details: " . $conn->error;
            }
        }
    } else {
        echo "Devotee not found.";
    }
} else {
    echo "Devotee ID not provided.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Devotee</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/CenterLeader/updatedevotee.css">
    <link rel="stylesheet" href="../css/CenterLeader/updatedevotee.css">
</head>

<body>
    <?php include('header.php'); ?>
    <div class="container">
        <h2 class="my-4">Update Devotee</h2>
        <form action="updateDevotee.php?devotee_id=<?php echo $devotee_id; ?>" method="post">
            <input type="hidden" name="devotee_id" value="<?php echo $devotee_id; ?>">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $devotee_row['name']; ?>">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $devotee_row['email']; ?>">
            </div>
            <div class="form-group">
                <label for="mobile_number">Mobile Number:</label>
                <input type="text" class="form-control" id="mobile_number" name="mobile_number" value="<?php echo $devotee_row['mobile_number']; ?>">
            </div>
            <div class="form-group">
                <label for="dob">Date of Birth:</label>
                <input type="date" class="form-control" id="dob" name="dob" value="<?php echo $devotee_row['dob']; ?>">
            </div>
            <div class="form-group">
                <label for="gender">Gender:</label>
                <select class="form-control" id="gender" name="gender">
                    <option value="Male" <?php if ($devotee_row['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if ($devotee_row['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                    <option value="Other" <?php if ($devotee_row['gender'] == 'Other') echo 'selected'; ?>>Other</option>
                </select>
            </div>
            <button type="submit" class="btn">Update Devotee</button>
        </form>
    </div>
</body>

</html>