<?php
session_start();
include('../php/dbConnect.php');

// Retrieve the video ID from the URL parameter
if (!isset($_GET['id'])) {
    // Redirect if video ID is not provided
    header("Location: vpravachan.php"); // Replace 'vpravachan.php' with the URL of your main page
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
    $description = $row['description'];
    $upload_date = $row['upload_date'];

    // Check if the download request is made
    if (isset($_GET['download']) && $_GET['download'] === 'true') {
        // Set the appropriate headers for file download
        header('Content-Description: File Transfer');
        header('Content-Type: video/mp4');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));

        // Read the file and output it to the browser
        readfile($file_path);
        exit;
    }

    // Add more details if needed
} else {
    // Handle if video with the provided ID is not found
    echo "Video not found";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Player</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #EFECEC;
            color: #0C2D57;
        }

        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            /* 16:9 aspect ratio */
            height: 0;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .video-details {
            border: 1px solid #EFECEC;
            background-color: whitesmoke;
            border-radius: 5px;
            padding: 20px;
            margin: 10px;
        }

        @media (max-width: 767px) {
            .video-container {
                padding-bottom: 75%;
                /* 4:3 aspect ratio for smaller screens */
            }
        }

        .video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .video-info {
            margin-top: 20px;
        }

        .video-title {
            font-size: 24px;
            font-weight: bold;
        }

        @media (max-width: 767px) {
            .video-title {
                font-size: 20px;
            }
        }

        .video-meta {
            margin-bottom: 10px;
            color: #666;
        }

        .video-meta span {
            margin-right: 10px;
        }

        @media (max-width: 767px) {
            .video-meta span {
                font-size: 14px;
            }
        }

        .video-actions .btn-outline-primary {
            color: #0C2D57;
            border-color: #0C2D57;
            margin-right: 10px;
        }

        @media (max-width: 767px) {
            .video-actions .btn {
                margin-right: 0;
                margin-bottom: 10px;
            }
        }

        .video-actions .btn-outline-primary:hover {
            background-color: #0C2D57;
            color: #EFECEC;
        }

        .video-description {
            font-size: 18px;
            line-height: 1.5;
        }

        @media (max-width: 767px) {
            .video-description {
                font-size: 14px;
            }
        }

        .d-none {
            display: none;
        }
    </style>
</head>

<body>
    <div class="container my-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="video-container">
                    <video class="video" controls>
                        <source src="<?php echo $file_path; ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
                <div class="video-info">
                    <h2 class="video-title mx-3" style="display:inline;"><?php echo  $title; ?></h2>
                    <button class="btn btn-sm btn-outline-secondary show-details-btn" id="showDetailsBtn">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="video-details d-none">
                        <div class="video-meta">
                            <span class="video-date"><?php echo $upload_date; ?></span>
                        </div>
                        <div class="video-actions">
                            <a href="?id=<?php echo $video_id; ?>&download=true" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download"></i> Download
                            </a> <button class="btn btn-sm btn-outline-primary"><i class="fas fa-bookmark"></i> Save</button>
                        </div>
                        <p class="video-description m-3"><?php echo  $description; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const showDetailsBtn = document.getElementById('showDetailsBtn');
            const videoDetails = document.querySelector('.video-details');

            showDetailsBtn.addEventListener('click', function() {
                videoDetails.classList.toggle('d-none');
                const icon = this.querySelector('i');
                icon.classList.toggle('fa-chevron-down');
                icon.classList.toggle('fa-chevron-up');
            });
        });
    </script>
</body>

</html>