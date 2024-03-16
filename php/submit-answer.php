<?php
session_start();
include('../php/dbConnect.php');

// Initialize alert message variables
$alertType = "";
$alertMessage = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $answer = $_POST['answer'];
    $qid = $_POST['qid'];
    $answerTime = date("Y-m-d H:i:s"); // Get current date and time

    // Prepare SQL statement to update answer in the database
    $sql = "UPDATE tbl_qna SET Answer = ?, Answer_Time = ? WHERE QID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $answer, $answerTime, $qid);

    // Execute SQL statement
    if ($stmt->execute()) {
        $alertType = "success";
        $alertMessage = "Answer submitted successfully.";
    } else {
        $alertType = "danger";
        $alertMessage = "Error: " . $conn->error;
    }

    // Close the prepared statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Answer</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        .container {
            height: 100vh;
            width: 100vw;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Alert message -->
        <?php if (!empty($alertMessage)) : ?>
            <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
                <?php echo $alertMessage; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <!-- Rest of the page content -->
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
</body>

</html>