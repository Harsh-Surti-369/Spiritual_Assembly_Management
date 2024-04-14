<?php
session_start();

// Check if the leader is not logged in, redirect to the login page
if (!isset($_SESSION['leader_id']) || !isset($_SESSION['center_id'])) {
    header("Location: login.php"); // Redirect to the login page
    exit(); // Stop further execution
}
// Include your database connection
include('../php/dbConnect.php');

// Fetch leader information from tbl_leader
$leader_id = $_SESSION['leader_id'];
$query_leader = "SELECT * FROM tbl_leader WHERE leader_id = ?";
$stmt_leader = mysqli_prepare($conn, $query_leader);
mysqli_stmt_bind_param($stmt_leader, "i", $leader_id);
mysqli_stmt_execute($stmt_leader);
$result_leader = mysqli_stmt_get_result($stmt_leader);
$leader_info = mysqli_fetch_assoc($result_leader);

// Fetch center information from tbl_center
$center_id = $leader_info['center_id']; // Get center_id from leader info
$query_center = "SELECT * FROM tbl_center WHERE center_id = ?";
$stmt_center = mysqli_prepare($conn, $query_center);
mysqli_stmt_bind_param($stmt_center, "i", $center_id);
mysqli_stmt_execute($stmt_center);
$result_center = mysqli_stmt_get_result($stmt_center);
$center_info = mysqli_fetch_assoc($result_center);


//sabha info
$sabha_sql = "SELECT * FROM tbl_sabha 
        WHERE center_id = $center_id 
        AND date >= CURDATE() 
        ORDER BY date ASC 
        LIMIT 3";
$sabha_result = mysqli_query($conn, $sabha_sql);

// Check if query executed successfully
if ($sabha_result) {
    // Fetch all rows from the sabha_result set into an associative array
    $sabha_records = mysqli_fetch_all($sabha_result, MYSQLI_ASSOC);
} else {
    // Query failed
    $sabha_records = array(); // Initialize as empty array
}

// Query to fetch the latest joined devotees for the current center
$devotee_sql = "SELECT * FROM tbl_devotee
        WHERE center_id = $center_id
        ORDER BY joining_date DESC
        LIMIT 5";

// Execute the query
$devotee_result = mysqli_query($conn, $devotee_sql);

// Check if query executed successfully
if ($devotee_result) {
    // Fetch all rows from the devotee_result set into an associative array
    $devotee_records = mysqli_fetch_all($devotee_result, MYSQLI_ASSOC);
} else {
    // Query failed
    $devotee_records = array(); // Initialize as empty array
} // Fetch one record for each type
$content = [];

// Define the categories and corresponding file extensions
$categories = ['bhajan audio', 'pravachan audio', 'bhajan video', 'pravachan video'];
$extensions = ['mp3', 'mp3', 'mp4', 'mp4'];

// Prepare and execute SQL query using prepared statements
$sql = "SELECT * FROM tbl_content WHERE category = ? AND (file_path LIKE ? OR file_path LIKE ?) ORDER BY upload_date DESC LIMIT 1";
$stmt = $conn->prepare($sql);

foreach ($categories as $index => $category) {
    $extension = '%' . $extensions[$index];
    $mp3_extension = $extension . '.mp3';
    $mp4_extension = $extension . '.mp4';
    $stmt->bind_param("sss", $category, $mp3_extension, $mp4_extension);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $content[$category] = $row;
    }
}

// Close the prepared statement
$stmt->close();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Center Leader Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" defer></script>
    <link rel="stylesheet" href="../css/CenterLeader/managedevotees.css">
    <link rel="stylesheet" href="../css/CenterLeader/header.css">
    <style>
        :root {
            --primary-color: #0C2D57;
            --secondary-color: #FC6736;
            --tertiary-color: #FFB0B0;
            --light-color: #EFECEC;
        }

        body {
            font-family: Arial, sans-serif;
            color: var(--primary-color);
            background-color: var(--light-color);
        }

        .header {
            background-color: var(--primary-color);
            color: var(--light-color);
            padding: 1rem;
        }

        .section-heading {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }

        .event-card {
            border-color: var(--tertiary-color);
            background-color: var(--light-color);
        }

        .event-date {
            color: var(--secondary-color);
        }

        .devotee-item {
            border-color: var(--tertiary-color);
            background-color: var(--light-color);
        }

        .footer {
            background-color: var(--primary-color);
            color: var(--light-color);
            padding: 1rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
    </style>
</head>

<body>
    <header style="margin-bottom: 150px;">
        <?php include('header.php'); ?>
    </header>

    <main class="container my-5">

        <section class="mb-5">
            <h2 class="section-heading text-center mb-4">Center Information</h2>
            <div class="center-details text-center mb-4">
                <p><strong>Center Name:</strong> <?php echo $center_info['center_name']; ?></p>
                <p><strong>Location:</strong> <?php echo $center_info['location']; ?></p>
                <p><strong>Email:</strong> <?php echo $leader_info['email']; ?></p>
                <p><strong>Phone:</strong> <?php echo $leader_info['m_no']; ?></p>
            </div>
        </section>
        <hr style="height: 5px;">
        <section class="mb-5">
            <h2 class="section-heading text-center mb-4">Upcoming Events</h2>
            <div id="event-list" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php
                // Check if sabha records exist
                if (isset($sabha_records) && !empty($sabha_records)) {
                    foreach ($sabha_records as $sabha) {
                ?>
                        <div class="col">
                            <div class="card h-100 event-card">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $sabha['title']; ?></h5>
                                    <p class="card-text"><?php echo $sabha['sabha_type']; ?></p>
                                    <p class="card-text event-date"><?php echo date('M d, Y', strtotime($sabha['date'])); ?></p>
                                    <p class="card-text"><?php echo date('h:i A', strtotime($sabha['timing_from'])) . ' - ' . date('h:i A', strtotime($sabha['timing_to'])); ?></p>
                                    <p class="card-text"><?php echo $sabha['location']; ?></p>
                                </div>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    // If no sabha records found
                    echo "<p>No upcoming sabha events.</p>";
                }
                ?>
            </div>

            <!-- Link to create new sabha -->
            <div class="text-center mt-4">
                <a href="createsabha.php" class="btn btn-primary">Create New Sabha</a>
            </div>
        </section>
        <hr>
        <section class="mb-5">
            <h2 class="section-heading text-center mb-4">Latest Joined Devotees</h2>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($devotee_records as $devotee) : ?>
                    <div class="col">
                        <div class="card mb-3 devotee-item">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $devotee['name']; ?></h5>
                                <p class="card-text"><strong>Joining Date:</strong> <?php echo $devotee['joining_date']; ?></p>
                                <p class="card-text"><strong>Email:</strong> <?php echo $devotee['email']; ?></p>
                                <p class="card-text"><strong>Mobile Number:</strong> <?php echo $devotee['mobile_number']; ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Link to manage devotees -->
            <div class="text-center mt-4">
                <a href="dashboard.php" class="btn btn-primary">Manage Devotees</a>
            </div>
        </section>

        <!-- <section class="mb-5">
            <h2 class="section-heading text-center mb-4">Uploaded Content</h2>
            <div class="row">
                <div class="col-md-12 mb-4">
                    <h3>Audio</h3>
                    <ul id="audio-list" class="list-unstyled">
                        <?php if (isset($content['bhajan audio'])) : ?>
                            <li class="mb-3">
                                <strong>Title:</strong> <?php echo $content['bhajan audio']['title']; ?><br>
                                <audio controls>
                                    <source src="<?php echo $content['bhajan audio']['file_path']; ?>" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                            </li>
                        <?php endif; ?>

                        <?php if (isset($content['pravachan audio'])) : ?>
                            <li class="mb-3">
                                <strong>Title:</strong> <?php echo $content['pravachan audio']['title']; ?><br>
                                <audio controls>
                                    <source src="<?php echo $content['pravachan audio']['file_path']; ?>" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-md-12">
                    <h3>Videos</h3>
                    <ul id="video-list" class="list-unstyled row row-cols-1 row-cols-md-2 g-4">
                        <?php if (isset($content['bhajan video'])) : ?>
                            <li class="col">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $content['bhajan video']['title']; ?></h5>
                                        <video width="100%" controls>
                                            <source src="<?php echo $content['bhajan video']['file_path']; ?>" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    </div>
                                </div>
                            </li>
                        <?php endif; ?>

                        <?php if (isset($content['pravachan video'])) : ?>
                            <li class="col">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $content['pravachan video']['title']; ?></h5>
                                        <video width="100%" controls>
                                            <source src="<?php echo $content['pravachan video']['file_path']; ?>" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    </div>
                                </div>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="Content.php" class="btn btn-primary">Manage all Bhajan/Pravachan</a>
            </div>
        </section> -->
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

</body>

</html>