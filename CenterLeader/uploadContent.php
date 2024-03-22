<?php
session_start();
include('../php/dbConnect.php');

function displaySuccessMessage($message)
{
    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
            $message
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
          </div>";
}

function displayErrorMessage($message)
{
    echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
            $message
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
          </div>";
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
        displayErrorMessage("Invalid file format. Please upload an MP3, AAC, FLAC, or MP4 file.");
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
            displaySuccessMessage("File uploaded and data inserted into the database successfully!");
        } else {
            displayErrorMessage("Error: " . $stmt->error);
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        displayErrorMessage("Sorry, there was an error uploading your file.");
    }
} else {
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bhajan & Pravachan Upload</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/CenterLeader/header.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            /* Set a default font */
            background-image: url("/Spiritual_Assembly_Management/images/bACK\ gROUND\ 01.jpg");
        }

        .container {
            max-width: 800px;
            /* Adjust container width as needed */
        }

        .form-label {
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 15px;
            /* Add space between form groups */
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        select {
            width: 100%;
            /* Make the select box fill the available space */
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            /* Add rounded corners */
            font-size: inherit;
            /* Inherit font size from parent element */
        }

        .btn.btn-primary {
            /* Assuming your button classes are "btn" and "btn-primary" */
            background-color: #0C2D57;
            /* Adjust background color as needed */
            color: #fff;
            /* Adjust text color as needed */
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            display: block;
            /* Make the button display as a block element */
            margin: 15px auto;
            /* Center the button horizontally */
            width: 100px;
        }
    </style>
</head>

<body>
    <?php include('header.php'); ?>
    <div class="container mt-3">
        <h1 class="text-center">Bhajan & Pravachan </h1>

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
                <label for="category">Category:</label>
                <select id="category" name="category">
                    <option value="">Select Category</option>
                    <option value="bhajan">Bhajan</option>
                    <option value="pravachan">Pravachan</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
        <div id="media-container"></div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jY3834LJq9CTBWzWIlchQAe/Kg" crossorigin="anonymous"></script>

</body>

</html>