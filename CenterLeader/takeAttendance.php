<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Attendance</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/CenterLeader/takeAttendance.css">
    <style>
        .devotee-box {
            border: 1px solid #0C2D57;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="text-center mb-4">Take Attendance</h2>
        <form>
            <div class="form-group">
                <label>Devotees</label>
                <div class="devotee-list" id="devoteeList">
                    <?php
                    session_start();
                    include('../php/dbConnect.php');

                    // Get center ID from session
                    $center_id = $_SESSION['center_id'];

                    // Query to fetch devotees of the center
                    $sql = "SELECT * FROM tbl_devotee WHERE center_id = $center_id";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        // Output each devotee's name in a box
                        while ($row = $result->fetch_assoc()) {
                            $devotee_name = $row['name'];
                    ?>
                            <div class="devotee-box"><?php echo $devotee_name; ?></div>
                    <?php
                        }
                    } else {
                        echo "<p>No devotees found.</p>";
                    }
                    ?>
                </div>
            </div>
            <div class="form-group">
                <label for="sabhaSummary">Sabha Summary</label>
                <textarea class="form-control" id="sabhaSummary" rows="3" placeholder="Write a summary of the sabha"></textarea>
            </div>

            <button type="submit" class="btn">Submit Attendance</button>
        </form>
    </div>
    <!-- <script src="../js/CenterLeader/takeAttendance.js"></script> -->
</body>

</html>