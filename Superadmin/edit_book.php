<?php
session_start();
include('../php/dbConnect.php');

// Check if book ID is provided in the URL
if (!isset($_GET['book_id'])) {
    // Redirect to book management page if ID is not provided
    header("Location: book
    .php");
    exit();
}

// Retrieve book details based on the provided ID
$bookId = $_GET['book_id'];

// Fetch book details function
function fetchBookById($bookId)
{
    global $conn;
    $query = "SELECT * FROM tbl_books WHERE book_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $bookId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $book = mysqli_fetch_assoc($result);
    return $book;
}

// Fetch book details
$book = fetchBookById($bookId);

// Check if book exists
if (!$book) {
    // Redirect to book management page if book not found
    header("Location: book.php");
    exit();
}

// Initialize variables for toast messages
$successMessage = $errorMessage = "";

// Check if form is submitted for book update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process form data
    // Retrieve and sanitize form inputs
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    // Additional processing for center_id can be added if needed

    // Update book details in the database
    $query = "UPDATE tbl_books SET title = ?, description = ? WHERE book_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssi", $title, $description, $bookId);
    $success = mysqli_stmt_execute($stmt);

    if ($success) {
        $successMessage = "Book updated successfully.";
    } else {
        $errorMessage = "Error updating book. Please try again.";
    }

    // Set session variable for success message
    $_SESSION['edit_book_success_message'] = $successMessage;

    // Set session variable for error message
    $_SESSION['edit_book_error_message'] = $errorMessage;

    // Redirect to book management page after form submission
    header("Location: book.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Roboto&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #EFECEC;
            font-family: 'Roboto', sans-serif;
        }

        h1 {
            font-family: 'Montserrat', sans-serif;
            color: #0C2D57;
        }

        .btn-custom {
            background-color: #0C2D57;
            color: #EFECEC;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #EFECEC;
            color: #0C2D57;
        }

        .form-control:focus {
            border-color: #0C2D57;
            box-shadow: 0 0 0 0.25rem rgba(12, 45, 87, 0.25);
        }
    </style>
</head>

<body>
    <?php include('header.php'); ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Edit Book</h1>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?book_id=' . $bookId; ?>" method="post">
                    <div class="mb-3">
                        <label for="titleInput" class="form-label">Title</label>
                        <input type="text" class="form-control" id="titleInput" name="title" value="<?php echo $book['title']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="descriptionInput" class="form-label">Description</label>
                        <textarea class="form-control" id="descriptionInput" name="description" required><?php echo $book['description']; ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-custom">
                        <i class="fas fa-save"></i> Update
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>