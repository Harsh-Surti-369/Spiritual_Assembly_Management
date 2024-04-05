<?php
session_start();
include('../php/dbConnect.php');
// Check if leader is logged in
if (!isset($_SESSION['leader_id'])) {
    header("Location: login.php");
    exit;
}

function displayToast($type, $message)
{
    echo '<div class="toast align-items-center text-white bg-' . $type . ' border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ' . $message . '
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uploadDir = "../uploads/";

    // Get the file details
    $fileName = basename($_FILES["file"]["name"]);
    $targetFilePath = $uploadDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    // Validate file type
    $allowedExtensions = array("mp3", "aac", "flac", "mp4");
    if (!in_array($fileType, $allowedExtensions)) {
        displayToast("danger", "Invalid file format. Please upload an MP3, AAC, FLAC, or MP4 file.");
        exit();
    }

    // Move the uploaded file to the specified directory
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
        // Retrieve other form data
        $title = $_POST["title"];
        $description = $_POST["description"];
        $speaker = $_POST["speaker"];
        $category = $_POST["category"];
        $center_id = $_SESSION['center_id'];

        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO tbl_content (file_path, title, description, speaker, upload_date, category, center_id)
                               VALUES (?, ?, ?, ?, NOW(), ?, ?)");

        // Bind parameters to the prepared statement
        $stmt->bind_param("sssssi", $targetFilePath, $title, $description, $speaker, $category, $center_id);
        // Execute the prepared statement
        if ($stmt->execute()) {
            displayToast("success", "File uploaded and data inserted into the database successfully!");
            echo "<script>$('#uploadForm')[0].reset();</script>"; // Clear the form
        } else {
            displayToast("danger", "Error: " . $stmt->error);
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        displayToast("danger", "Sorry, there was an error uploading your file.");
    }
}
// Fetch uploaded content from the database
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

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bhajan & Pravachan Upload</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/CenterLeader/header.css">
    <style>
        body {
            background-image: url("../images/bgCanada.png");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .container {
            margin-top: 150px;
            max-width: 1200px;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-label {
            font-weight: bold;
            color: #0C2D57;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-control {
            border-color: #0C2D57;
        }

        .form-control:focus {
            border-color: #FC6736;
            box-shadow: 0 0 0 0.2rem rgba(252, 103, 54, 0.25);
        }

        select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #0C2D57;
            border-radius: 4px;
            font-size: inherit;
            color: #0C2D57;
        }

        .btn.btn-primary {
            background-color: #0C2D57;
            color: #EFECEC;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            display: block;
            margin: 1.5rem auto 0;
            width: auto;
            transition: background-color 0.3s ease;
        }

        .btn.btn-primary:hover {
            background-color: #0a2548;
        }

        .content-section {
            background-color: #EFECEC;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .content-section h2 {
            color: #0C2D57;
            margin-bottom: 1.5rem;
        }

        .content-item {
            background-color: #ffffff;
            border-radius: 5px;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
        }

        .content-item h5 {
            color: #FC6736;
            margin-bottom: 0.5rem;
        }

        .content-item p {
            color: #0C2D57;
            margin-bottom: 0.5rem;
        }

        .content-item audio,
        .content-item video {
            width: 100%;
        }
    </style>
</head>

<body>
    <header style="margin-bottom: 150px;">
        <?php include('header.php'); ?>
    </header>
    <div class="container mt-3" stye="padding-top:50px;">
        <h1 class="text-center mb-4">Bhajan & Pravachan</h1>

        <section class="content-section mb-5">
            <h2 class="mb-4"><i class="fas fa-upload"></i> Upload Content</h2>
            <div class="row">
                <div class="col-md-12">
                    <form id="uploadForm" action="uploadContent.php" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="fileInput" class="form-label">Select File</label>
                            <input class="form-control" type="file" id="fileInput" name="file" required accept=".mp3,.aac,.flac,.mp4">
                        </div>
                        <div class="mb-3">
                            <label for="titleInput" class="form-label">Title</label>
                            <input class="form-control" type="text" id="titleInput" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="descriptionInput" class="form-label">Description</label>
                            <textarea class="form-control" id="descriptionInput" name="description" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="speakerInput" class="form-label">Speaker (Optional)</label>
                            <input class="form-control" type="text" id="speakerInput" name="speaker">
                        </div>
                        <div class="form-group">
                            <label for="category" class="form-label">Category:</label>
                            <select id="category" name="category" class="form-control">
                                <option value="">Select Category</option>
                                <option value="bhajan">Bhajan</option>
                                <option value="pravachan">Pravachan</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Upload</button>
                    </form>
                </div>
            </div>
        </section>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script>

        </script>

</body>

</html>