<?php
session_start();
include('../php/dbConnect.php');

// Check if content ID is provided in the URL
if (!isset($_GET['id'])) {
    // Redirect to index.php if ID is not provided
    header("Location: index.php");
    exit();
}

// Retrieve content details based on the provided ID
$contentId = $_GET['id'];

// Fetch content details function
function fetchContentById($contentId)
{
    global $conn;
    $query = "SELECT * FROM tbl_content WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $contentId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $content = mysqli_fetch_assoc($result);
    return $content;
}

// Fetch content details
$content = fetchContentById($contentId);

// Check if content exists
if (!$content) {
    // Redirect to index.php if content not found
    header("Location: index.php");
    exit();
}

// Initialize variables for toast messages
$successMessage = $errorMessage = "";

// Check if form is submitted for content update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process form data
    // Retrieve and sanitize form inputs
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $speaker = mysqli_real_escape_string($conn, $_POST['speaker']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);

    // Check if file is uploaded
    if ($_FILES['file']['size'] > 0) {
        // File upload configuration
        $uploadDir = "../uploads/";
        $fileName = basename($_FILES["file"]["name"]);
        $targetFilePath = $uploadDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // Allow certain file formats
        $allowTypes = array('mp3', 'aac', 'flac', 'mp4');

        if (in_array($fileType, $allowTypes)) {
            // Upload file to server
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
                // Update content details in the database
                $query = "UPDATE tbl_content SET title = ?, description = ?, speaker = ?, category = ?, file_path = ? WHERE id = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "sssssi", $title, $description, $speaker, $category, $targetFilePath, $contentId);
                $success = mysqli_stmt_execute($stmt);

                if ($success) {
                    $successMessage = "Content updated successfully.";
                } else {
                    $errorMessage = "Error updating content. Please try again.";
                }
            } else {
                $errorMessage = "Sorry, there was an error uploading your file.";
            }
        } else {
            $errorMessage = 'File format is not supported. Please upload a valid file format.';
        }
    } else {
        // Update content details in the database without changing the file
        $query = "UPDATE tbl_content SET title = ?, description = ?, speaker = ?, category = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssi", $title, $description, $speaker, $category, $contentId);
        $success = mysqli_stmt_execute($stmt);

        if ($success) {
            $successMessage = "Content updated successfully.";
        } else {
            $errorMessage = "Error updating content. Please try again.";
        }
    }

    // Set session variable for success message
    $_SESSION['edit_success_message'] = $successMessage;

    // Set session variable for error message
    $_SESSION['edit_error_message'] = $errorMessage;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Content</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/CenterLeader/header.css">
    <style>
        body {
            background-image: url("../images/bgCanada.png");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .container {
            margin-top: 150px;
            max-width: 800px;
            background-color: #EFECEC;
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
    </style>
</head>

<body>
    <header style="margin-bottom: 150px;">
        <?php include('header.php'); ?>
    </header>
    <div class="container mt-3" stye="padding-top:50px;">
        <h1 class="text-center mb-4">Edit Content</h1>
        <?php if (!empty($errorMessage)) : ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($successMessage)) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo $successMessage; ?>
            </div>
        <?php endif; ?>
        <form id="uploadForm" action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="fileInput" class="form-label">Select File</label>
                <input class="form-control" type="file" id="fileInput" name="file" accept=".mp3,.aac,.flac,.mp4">
            </div>
            <div class="mb-3">
                <label for="titleInput" class="form-label">Title</label>
                <input class="form-control" type="text" id="titleInput" name="title" value="<?php echo $content['title']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="descriptionInput" class="form-label">Description</label>
                <textarea class="form-control" id="descriptionInput" name="description" required><?php echo $content['description']; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="speakerInput" class="form-label">Speaker (Optional)</label>
                <input class="form-control" type="text" id="speakerInput" name="speaker" value="<?php echo $content['speaker']; ?>">
            </div>
            <div class="form-group">
                <label for="category" class="form-label">Category:</label>
                <select id="category" name="category" class="form-control">
                    <option value="bhajan" <?php if ($content['category'] == 'bhajan') echo 'selected'; ?>>Bhajan</option>
                    <option value="pravachan" <?php if ($content['category'] == 'pravachan') echo 'selected'; ?>>Pravachan</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
        <div id="media-container"></div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        // JavaScript code for redirecting after 2 seconds
        $(document).ready(function() {
            // Check if there's a success or error message
            if ($('.alert-success').length > 0 || $('.alert-danger').length > 0) {
                // Wait for 2 seconds before redirecting
                setTimeout(function() {
                    window.location.href = 'content.php';
                }, 4000);
            }
        });
    </script>
</body>

</html>