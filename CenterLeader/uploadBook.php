<?php
session_start();
include('../php/dbConnect.php');

$alertMessage = '';
$alertType = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bookTitle = $_POST['book_title'];
    $bookAuthor = $_POST['book_author'];
    $bookDescription = $_POST['book_description'];
    $center_id = $_SESSION['center_id'];

    if (!empty($bookTitle) && !empty($bookAuthor) && !empty($bookDescription)) {
        if (isset($_FILES['book_file'])) {
            $file = $_FILES['book_file'];

            $fileName = $file['name'];
            $fileTmpName = $file['tmp_name'];
            $fileSize = $file['size'];
            $fileError = $file['error'];

            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $allowedExtensions = ['pdf'];

            if (in_array($fileExt, $allowedExtensions)) {
                if ($fileError === 0) {
                    $uploadDir = '../uploads/';
                    $newFileName = uniqid('', true) . '.' . $fileExt;
                    $uploadPath = $uploadDir . $newFileName;

                    if (move_uploaded_file($fileTmpName, $uploadPath)) {
                        $sql = "INSERT INTO tbl_books (center_id, title, author, description, file_path) 
                                VALUES ('$center_id', '$bookTitle', '$bookAuthor', '$bookDescription', '$uploadPath')";

                        if ($conn->query($sql) === TRUE) {
                            $alertMessage = 'Book uploaded successfully.';
                            $alertType = 'success';
                        } else {
                            $alertMessage = 'Error: Unable to upload book. Please try again later.';
                            $alertType = 'danger';
                        }
                    } else {
                        $alertMessage = 'Error: There was a problem uploading your file. Please try again.';
                        $alertType = 'danger';
                    }
                } else {
                    $alertMessage = 'Error: ' . $file['error'];
                    $alertType = 'danger';
                }
            } else {
                $alertMessage = 'Error: Only PDF files are allowed.';
                $alertType = 'danger';
            }
        } else {
            $alertMessage = 'Error: No file uploaded.';
            $alertType = 'danger';
        }
    } else {
        $alertMessage = 'Error: Please fill in all the required fields.';
        $alertType = 'danger';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Upload</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" defer></script>
    <link rel="stylesheet" href="../css/CenterLeader/header.css">
    <style>
        .container {
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

        .form-control {
            border-color: #0C2D57;
        }

        .form-control:focus {
            border-color: #FC6736;
            box-shadow: 0 0 0 0.2rem rgba(252, 103, 54, 0.25);
        }

        .btn-primary {
            background-color: #0C2D57;
            color: #EFECEC;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0a2548;
        }

        .alert {
            border-radius: 5px;
            color: #0C2D57;
            background-color: #FFB0B0;
        }
    </style>
</head>

<body>
    <?php include('header.php'); ?>


    <div class="container mt-3" stye="padding-top:50px;">
        <h1 class="text-center mb-4">Book Upload</h1>
        <?php if (!empty($alertMessage)) : ?>
            <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
                <?php echo $alertMessage; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <form id="uploadBook" action="uploadBook.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="bookFileInput" class="form-label">Select Book</label>
                <input class="form-control" type="file" id="bookFileInput" name="book_file" required accept=".pdf">
            </div>
            <div class="mb-3">
                <label for="bookTitleInput" class="form-label">Book Title</label>
                <input class="form-control" type="text" id="bookTitleInput" name="book_title" required>
            </div>
            <div class="mb-3">
                <label for="bookAuthorInput" class="form-label">Author</label>
                <input class="form-control" type="text" id="bookAuthorInput" name="book_author" required>
            </div>
            <div class="mb-3">
                <label for="bookDescriptionInput" class="form-label">Description</label>
                <textarea class="form-control" id="bookDescriptionInput" name="book_description" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
        <div id="media-container"></div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jY3834LJq9CTBWzWIlchQAe/Kg" crossorigin="anonymous"></script>
</body>

</html>