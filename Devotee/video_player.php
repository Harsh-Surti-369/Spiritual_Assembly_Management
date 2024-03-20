<?php
session_start();
include('../php/dbConnect.php');

// Retrieve the video ID from the URL parameter
if (!isset($_GET['id'])) {
    // Redirect if video ID is not provided
    header("Location: vpravachan.php"); // Replace 'main_page.php' with the URL of your main page
    exit();
}

$video_id = $_GET['id'];

// Retrieve video details from the database using the provided ID
$query = "SELECT * FROM tbl_content WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $video_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $title = $row['title'];
    $file_path = $row['file_path'];
    // Add more details if needed
} else {
    // Handle if video with the provided ID is not found
    echo "Video not found";
    exit();
}

// Display video player here using the retrieved details
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Player</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        /* Custom styles */
        body {
            background-color: #0C2D57;
            color: #EFECEC;
        }

        .video-container {
            position: relative;
            width: 70%;
            margin: auto;
            margin-top: 20px;
        }

        video {
            width: 100%;
            border-radius: 5px;
        }

        .video-details {
            margin-top: 20px;
            padding: 20px;
            background-color: #FC6736;
            border-radius: 5px;
        }

        .video-title {
            color: #FFB0B0;
        }

        .video-description {
            color: #EFECEC;
        }
    </style>
</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <!-- Video Player Section -->
            <div class="col-md-8">
                <video class="video-player" controls>
                    <source src="<?php echo $file_path; ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>

            <!-- Video Details Section -->
            <div class="col-md-4">
                <h2><?php echo $title; ?></h2>
                <?php if (isset($description)) : ?>
                    <p><?php echo $description; ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>