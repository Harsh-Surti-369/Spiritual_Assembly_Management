<?php
session_start();
include('../php/dbConnect.php');

// Check if the leader is not logged in, redirect to the login page
if (!isset($_SESSION['leader_id']) || !isset($_SESSION['center_id'])) {
    header("Location: login.php"); // Redirect to the login page
    exit(); // Stop further execution
}

$query = "SELECT * FROM tbl_content ORDER BY upload_date DESC";
$result = mysqli_query($conn, $query);

// Initialize arrays to store content items
$bhajanAudio = [];
$pravachanAudio = [];
$bhajanVideo = [];
$pravachanVideo = [];

if (mysqli_num_rows($result) > 0) {
    // Loop through each row in the result set
    while ($row = mysqli_fetch_assoc($result)) {
        // Check the category of the content item
        switch ($row['category']) {
            case 'bhajan':
                // Check if the content is audio or video
                if (strpos($row['file_path'], '.mp3') !== false || strpos($row['file_path'], '.aac') !== false || strpos($row['file_path'], '.flac') !== false) {
                    $bhajanAudio[] = $row;
                } else {
                    $bhajanVideo[] = $row;
                }
                break;
            case 'pravachan':
                // Check if the content is audio or video
                if (strpos($row['file_path'], '.mp3') !== false || strpos($row['file_path'], '.aac') !== false || strpos($row['file_path'], '.flac') !== false) {
                    $pravachanAudio[] = $row;
                } else {
                    $pravachanVideo[] = $row;
                }
                break;
            default:
                // Handle other categories if needed
                break;
        }
    }
}
// Function to delete content from the database
function deleteContent($id, $conn)
{
    $sql = "DELETE FROM tbl_content WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    if (deleteContent($id, $conn)) {
        echo json_encode(array("status" => "success"));
    } else {
        echo json_encode(array("status" => "error"));
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploaded Content</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/CenterLeader/header.css">
    <style>
        .toast {
            position: fixed;
            color: #0C2D57;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1050;
            width: 400px;
            /* Adjust the width as needed */
            max-width: calc(100% - 20px);
        }

        .content-section {
            background-color: #EFECEC;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .content-item {
            background-color: #ffffff;
            border-radius: 5px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .content-item h5 {
            color: #FC6736;
            margin-bottom: 0.5rem;
        }

        .content-item audio,
        .content-item video {
            width: 100%;
        }

        .video-link {
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <header style="margin-bottom: 150px;">
        <?php include('header.php'); ?>
    </header>

    <div class="container">
        <h1 class="text-center mb-4">Manage Bhajan&Pravachan</h1>

        <!-- Link to upload page -->
        <div class="text-end mb-4">
            <a href="uploadContent.php" class="btn btn-primary">Upload New</a>
        </div>

        <!-- Audio container -->
        <section class="content-section">
            <h2 class="mb-4">Bhajan & Pravachan Audio</h2>
            <div class="row">
                <div class="col-md-6">
                    <h3>Bhajan Audio</h3>
                    <?php foreach ($bhajanAudio as $audio) : ?>
                        <div class="content-item">
                            <h5><?= $audio['title'] ?></h5>
                            <audio controls>
                                <source src="<?= $audio['file_path'] ?>" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio>
                            <div class="mt-2">
                                <a href="editContent.php?section=bhajanAudio&id=<?= $audio['id'] ?>" class="btn btn-primary me-2"><i class="fas fa-edit"></i> Edit</a>
                                <button type="button" class="btn btn-danger" onclick="confirmDelete(<?= $audio['id'] ?>)"><i class="fas fa-trash"></i> Delete</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="col-md-6">
                    <h3>Pravachan Audio</h3>
                    <?php foreach ($pravachanAudio as $audio) : ?>
                        <div class="content-item">
                            <h5><?= $audio['title'] ?></h5>
                            <audio controls>
                                <source src="<?= $audio['file_path'] ?>" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio>
                            <div class="mt-2">
                                <a href="editContent.php?section=pravachanAudio&id=<?= $audio['id'] ?>" class="btn btn-primary me-2"><i class="fas fa-edit"></i> Edit</a>
                                <button type="button" class="btn btn-danger" onclick="confirmDelete(<?= $audio['id'] ?>)"><i class="fas fa-trash"></i> Delete</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Video containers -->
        <section class="content-section">
            <h2 class="mb-4">Bhajan & Pravachan Video</h2>
            <div class="row">
                <div class="col-md-6">
                    <h3>Bhajan Video</h3>
                    <?php foreach ($bhajanVideo as $video) : ?>
                        <div class="content-item">
                            <h5><?= $video['title'] ?></h5>
                            <video controls>
                                <source src="<?= $video['file_path'] ?>" type="video/mp4">
                                Your browser does not support the video element.
                            </video>
                            <div class="video-link mt-2">
                                <a href="viewVideo.php?id=<?= $video['id'] ?>" class="btn btn-primary me-2"><i class="fas fa-play"></i> View</a>
                                <a href="editContent.php?section=bhajanVideo&id=<?= $video['id'] ?>" class="btn btn-primary me-2"><i class="fas fa-edit"></i> Edit</a>
                                <button type="button" class="btn btn-danger" onclick="confirmDelete(<?= $video['id'] ?>)"><i class="fas fa-trash"></i> Delete</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="col-md-6">
                    <h3>Pravachan Video</h3>
                    <?php foreach ($pravachanVideo as $video) : ?>
                        <div class="content-item">
                            <h5><?= $video['title'] ?></h5>
                            <video controls>
                                <source src="<?= $video['file_path'] ?>" type="video/mp4">
                                Your browser does not support the video element.
                            </video>
                            <div class="video-link mt-2">
                                <a href="viewVideo.php?id=<?= $video['id'] ?>" class="btn btn-primary me-2"><i class="fas fa-play"></i> View</a>
                                <a href="editContent.php?section=pravachanVideo&id=<?= $video['id'] ?>" class="btn btn-primary me-2"><i class="fas fa-edit"></i> Edit</a>
                                <button type="button" class="btn btn-danger" onclick="confirmDelete(<?= $video['id'] ?>)"><i class="fas fa-trash"></i> Delete</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete(id) {
            var confirmationToast = `
                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
                    <div class="toast-header">
                        <strong class="me-auto">Confirm Deletion</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        Are you sure you want to delete this content?
                        <button type="button" class="btn btn-danger btn-sm ms-2" onclick="deleteContent(${id})">Yes</button>
                    </div>
                </div>
            `;

            document.body.insertAdjacentHTML('beforeend', confirmationToast);

            var toastEl = document.querySelector('.toast');
            var toast = new bootstrap.Toast(toastEl);
            toast.show();
        }

        function deleteContent(id) {
            // Send AJAX request to delete content
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.status === "success") {
                            // Show success message toast
                            var successToast = `
                                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="5000">
                                    <div class="toast-header">
                                        <strong class="me-auto">Success</strong>
                                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                    </div>
                                    <div class="toast-body">
                                        Content deleted successfully!
                                    </div>
                                </div>
                            `;
                            document.body.insertAdjacentHTML('beforeend', successToast);
                            var successToastEl = document.querySelector('.toast:last-child');
                            var successToastInstance = new bootstrap.Toast(successToastEl);
                            successToastInstance.show();

                            // Reload the page after 5 seconds
                            setTimeout(function() {
                                location.reload();
                            }, 5000);
                        } else {
                            // Show error message toast
                            var errorToast = `
                                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="5000">
                                    <div class="toast-header">
                                        <strong class="me-auto">Error</strong>
                                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                    </div>
                                    <div class="toast-body">
                                        Error deleting content. Please try again.
                                    </div>
                                </div>
                            `;
                            document.body.insertAdjacentHTML('beforeend', errorToast);
                            var errorToastEl = document.querySelector('.toast:last-child');
                            var errorToastInstance = new bootstrap.Toast(errorToastEl);
                            errorToastInstance.show();
                        }
                    } else {
                        // Show error message toast
                        var errorToast = `
                            <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="5000">
                                <div class="toast-header">
                                    <strong class="me-auto">Error</strong>
                                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                </div>
                                <div class="toast-body">
                                    Error deleting content. Please try again.
                                </div>
                            </div>
                        `;
                        document.body.insertAdjacentHTML('beforeend', errorToast);
                        var errorToastEl = document.querySelector('.toast:last-child');
                        var errorToastInstance = new bootstrap.Toast(errorToastEl);
                        errorToastInstance.show();
                    }
                }
            };
            xhr.open("POST", "content.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.send("delete_id=" + id);
        }
    </script>

</body>

</html>